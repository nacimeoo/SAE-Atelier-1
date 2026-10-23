const API_BASE_URL = 'http://localhost:7080'; 

document.addEventListener('DOMContentLoaded', () => {
    chargerSpectacles();
    chargerStyles();
    afficherCompteur();
    afficherUtilisateur();
    initAuth();

    const dateFilter = document.getElementById('date-filter');
    const styleFilter = document.getElementById('style-filter');

    document.getElementById('apply-filters').addEventListener('click', () => {
        chargerSpectacles(dateFilter.value, styleFilter.value);
    });

    document.getElementById('reset-filters').addEventListener('click', () => {
        dateFilter.value = '';
        styleFilter.value = '';
        chargerSpectacles();
    });

    document.getElementById('btn-retour').addEventListener('click', () => {
        document.getElementById('spectacles-section').style.display = 'block';
        document.getElementById('soiree-section').style.display = 'none';
    });
});

async function chargerSpectacles(date = '', style = '') {
    try {
        const params = new URLSearchParams();
        if (date) params.set('date', date);
        if (style) params.set('style', style);
        const query = params.toString();

        const response = await fetch(`${API_BASE_URL}/spectacles${query ? '?' + query : ''}`);
        const data = await response.json();
        const container = document.getElementById('spectacles-grid');
        container.innerHTML = '';

        if (data.spectacles.length === 0) {
            container.innerHTML = '<p>Aucun spectacle ne correspond aux filtres.</p>';
            return;
        }

        data.spectacles.forEach(spectacle => {
            const card = document.createElement('div');
            card.className = 'card';
            card.onclick = () => chargerSoiree(spectacle._links.soiree.href);
            
            card.innerHTML = `
                <div class="img-container">
                    ${spectacle.image ? `<img src="${spectacle.image}" alt="${spectacle.titre}">` : ''}
                </div>
                <h3>${spectacle.titre}</h3>
                <p class="date-heure">${spectacle.date} · ${spectacle.heure}</p>
                <span class="badge">${spectacle.style}</span>
            `;
            container.appendChild(card);
        });
    } catch (error) {
        console.error("Erreur Fetch spectacles :", error);
    }
}

async function chargerStyles() {
    try {
        const response = await fetch(`${API_BASE_URL}/styles`);
        const data = await response.json();
        const select = document.getElementById('style-filter');
        
        
        data.styles.forEach(style => {
            const option = document.createElement('option');
            option.value = style;
            option.textContent = style;
            select.appendChild(option);
        });
    } catch (error) {
        console.error("Erreur Fetch styles :", error);
    }
}   

async function chargerSoiree(urlApi) {
    try {
        const response = await fetch(`${API_BASE_URL}${urlApi}`);
        const data = await response.json();
        const soiree = data.soiree;
        console.log("Données de la soirée :", soiree);

        const detailContainer = document.getElementById('soiree-detail');
        
        let spectaclesHtml = soiree.spectacles.map(sp => `
            <div class="spectacle-item">
                <div class="spectacle-img-container">
                    ${sp.image ? `<img src="${sp.image}" alt="${sp.titre}">` : ''}
                </div>
                <div class="spectacle-info">
                    <div class="spectacle-meta">
                        <span class="heure-orange">${sp.heure}</span>
                        <span class="badge">${sp.style}</span>
                    </div>
                    <h4>${sp.titre}</h4>
                    <p class="artistes">${sp.artistes}</p>
                    <p class="description">${sp.description}</p>
                </div>
            </div>
        `).join('');

        let html = `
            <div class="soiree-layout">
                <!-- Colonne de gauche : Détails -->
                <div class="soiree-main">
                    <div class="main-image-container"></div>
                    
                    <span class="badge badge-theme">${soiree.thematique}</span>
                    <h2 class="soiree-title">${soiree.nom}</h2>

                    <div class="info-cards">
                        <div class="info-card">
                            <span class="info-label">Date</span>
                            <span class="info-value">${soiree.date}</span>
                        </div>
                        <div class="info-card">
                            <span class="info-label">Début</span>
                            <span class="info-value">${soiree.heure_debut}</span>
                        </div>
                        <div class="info-card">
                            <span class="info-label">Lieu</span>
                            <span class="info-value">${soiree.lieu.nom}</span>
                        </div>
                        <div class="info-card">
                            <span class="info-label">Places restantes</span>
                            <span class="info-value">${soiree.places_restantes}</span>
                        </div>
                    </div>

                    <h3 class="section-title">Spectacles</h3>
                    <div class="spectacles-list">
                        ${spectaclesHtml}
                    </div>
                </div>

                <!-- Colonne de droite : Panier / Billets -->
                <aside class="soiree-sidebar">
                    <div class="billetterie-card">
                        <h3>Billets</h3>
                        
                        <div class="ticket-row">
                            <div class="ticket-details">
                                <span class="ticket-name">Tarif unique</span>
                                <span class="ticket-price">${soiree.tarif} €</span>
                            </div>
                            <div class="ticket-controls">
                                <button class="btn-qty">-</button>
                                <span class="qty">0</span>
                                <button class="btn-qty">+</button>
                            </div>
                        </div>

                        <div class="total-row">
                            <span>Total</span>
                            <span class="total-price">0,00 €</span>
                        </div>

                        <button class="btn-primary">Ajouter au panier</button>
                        <div id="message-panier" class="message-panier"></div>
                    </div>
                </aside>
            </div>
        `;

        detailContainer.innerHTML = html;
        initBilleterie(soiree);

        document.getElementById('spectacles-section').style.display = 'none';
        document.getElementById('page-header-title').style.display = 'none';
        document.getElementById('soiree-section').style.display = 'block';

    } catch (error) {
        console.error("Erreur Fetch soirée :", error);
    }
}

function updateCompteur(lignes) {
    const total = lignes.reduce((sum, ligne) => sum + ligne.quantite, 0);
    localStorage.setItem('panierTotal', total);
    afficherCompteur();
}

function afficherCompteur() {
    const total = localStorage.getItem('panierTotal') || 0;
    const compteur = document.getElementById('compteur-panier');
    compteur.textContent = total;
}

async function ajouterAuPanier(soireeId, quantite) {
    const token = getToken();
    if (!token) {
        afficherSection('auth');
        return { success: false, message: "Vous devez être connecté pour ajouter au panier." };
    }
    
    try {
        const response = await fetch(`${API_BASE_URL}/panier/lignes`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({
                soiree_id: soireeId,
                type_tarif: 'NORMAL',
                quantite: quantite
            })
        });

        if (response.status === 201) {
            const data = await response.json();
            updateCompteur(data.panier.lignes);
            return { success: true, message: "Billets ajoutés au panier" };
        }

        if (response.status === 401) {
            deconnecter();
            afficherSection('auth');
            return { success: false, message: "Session expiré reconnectez vous" };
        }

        if (response.status === 422) {
            const data = await response.json();
            return { success: false, message: data.message };
        }
    
        return { success: false, message: "Erreur lors de l'ajout au panier" };
    } catch (error){
        console.error("Erreur ajout panier :", error);
        return { success: false, message: "Impossible de joindre le serveur." };
    }
}

function initBilleterie(soiree) {
    const [minusBtn, plusBtn] = document.querySelectorAll('.btn-qty');
    const qty = document.querySelector('.qty');
    const totalPriceSpan = document.querySelector('.total-price');
    const messageDiv = document.getElementById('message-panier');
    let quantite = 0;

    const updateTotal = () => {
        qty.textContent = quantite;
        totalPriceSpan.textContent = (quantite * soiree.tarif).toFixed(2) + " €";
    };

    minusBtn.addEventListener('click', () => {
        if (quantite > 0) {
            quantite--;
            updateTotal();
        }
    });

    plusBtn.addEventListener('click', () => {
        if (quantite < soiree.places_restantes) {
            quantite++;
            updateTotal();
        }
    });

    document.querySelector('.btn-primary').addEventListener('click', async () => {
        if (quantite === 0) {
            messageDiv.textContent = "Veuillez sélectionner au moins un billet.";
            return;
        }
        
        const result = await ajouterAuPanier(soiree.id, quantite);
        messageDiv.textContent = result.message;
        if (result.success) {
            quantite = 0;
            updateTotal();
        }
    });
}

function getToken(){
    return localStorage.getItem('nrv_token');
}

function getUser(){
    try {
        return JSON.parse(localStorage.getItem('nrv_user'));
    }catch(e){
        return null;
    }
}

function enregistrerSession(token, user) {
    localStorage.setItem('nrv_token', token);
    localStorage.setItem('nrv_prenom', user.prenom);
    localStorage.setItem('nrv_role', user.role);
    afficherUtilisateur();
}

function afficherUtilisateur() {
    const prenom = localStorage.getItem('nrv_prenom');
    document.getElementById('lien-connexion').style.display = prenom ? 'none' : 'inline';
    document.getElementById('zone-user').style.display = prenom ? 'inline' : 'none';
    document.getElementById('nom-utilisateur').textContent = prenom || '';
    document.getElementById('lien-billets').style.display = prenom ? 'inline' : 'none';
    document.getElementById('lien-jauges').style.display = localStorage.getItem('nrv_role') === 'GESTIONNAIRE' ? 'inline' : 'none';
}

function afficherSection(nom) {
    document.getElementById('spectacles-section').style.display = nom === 'programme' ? 'block' : 'none';
    document.getElementById('page-header-title').style.display = nom === 'programme' ? 'block' : 'none';
    document.getElementById('soiree-section').style.display = nom === 'soiree' ? 'block' : 'none';
    document.getElementById('auth-section').style.display = nom === 'auth' ? 'block' : 'none';
    document.getElementById('billets-section').style.display = nom === 'billets' ? 'block' : 'none';
    document.getElementById('jauges-section').style.display = nom === 'jauges' ? 'block' : 'none';

}

async function chargerBillets() {
    const container = document.getElementById('billets-liste');
    const btnImprimer = document.getElementById('btn-imprimer');
    const token = getToken();
    if (!token) {
        afficherSection('auth');
        return;
    }

    afficherSection('billets');
    container.innerHTML = '<p>Chargement...</p>';
    btnImprimer.style.display = 'none';

    try {
        const response = await fetch(`${API_BASE_URL}/billets`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });

        if (response.status === 401) {
            deconnecter();
            afficherSection('auth');
            return;
        }
        if (!response.ok) {
            container.innerHTML = '<p>Erreur lors du chargement des billets.</p>';
            return;
        }

        const data = await response.json();
        if (data.billets.length === 0) {
            container.innerHTML = '<p>Vous n\'avez aucun billet pour le moment.</p>';
            return;
        }

        container.innerHTML = data.billets.map(b => `
            <article class="billet">
                <div class="billet-main">
                    <span class="label">Nancy Rock Vibration</span>
                    <h3>${b.libelle}</h3>
                    <p><span class="label">Acheteur</span>${b.acheteur}</p>
                    <p><span class="label">Date et horaire</span>${b.date ? `${b.date} · ${b.heure}` : 'Pass multi-soirées'}</p>
                    ${b.lieu ? `<p><span class="label">Lieu</span>${b.lieu}</p>` : ''}
                </div>
                <div class="billet-side">
                    <span class="label">Tarif</span>
                    <strong>${b.type_tarif}</strong>
                    <span class="label" style="margin-top: 0.75rem;">Référence</span>
                    <span class="billet-ref">${b.reference}</span>
                </div>
            </article>
        `).join('');
        btnImprimer.style.display = 'inline-block';
    } catch (error) {
        console.error("Erreur Fetch billets :", error);
        container.innerHTML = '<p>Impossible de joindre le serveur.</p>';
    }
}

function deconnecter() {
    ['nrv_token', 'nrv_prenom','nrv_role', 'panierTotal'].forEach(cle => localStorage.removeItem(cle));
    afficherUtilisateur();
    afficherCompteur();
}

async function connecter(email, password) {
    const response = await fetch(`${API_BASE_URL}/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password })
    });

    if (response.ok) {
        const data = await response.json();
        enregistrerSession(data.token, data.user);
        return { success: true };
    }
    if (response.status === 401) {
        return { success: false, message: "Email ou mot de passe incorrect." };
    }
    return { success: false, message: "Erreur lors de la connexion." };
}

async function inscrire(nom, prenom, email, password) {
    const response = await fetch(`${API_BASE_URL}/register`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ nom, prenom, email, password })
    });

    if (response.status === 201) {
        return { success: true };
    }
    if (response.status === 409) {
        return { success: false, message: "Cet email est déjà utilisé." };
    }
    if (response.status === 400) {
        return { success: false, message: "Données invalides (mot de passe de 8 caractères minimum)." };
    }
    return { success: false, message: "Erreur lors de l'inscription." };
}


function initAuth() {
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const loginError = document.getElementById('login-error');
    const registerError = document.getElementById('register-error');

    document.getElementById('lien-connexion').addEventListener('click', (e) => {
        e.preventDefault();
        afficherSection('auth');
    });

    document.getElementById('lien-billets').addEventListener('click', (e) => {
        e.preventDefault();
        chargerBillets();
    });

    document.getElementById('btn-retour-billets').addEventListener('click', () => {
        afficherSection('programme');
    });

    document.getElementById('btn-imprimer').addEventListener('click', () => window.print());

    document.getElementById('lien-deconnexion').addEventListener('click', (e) => {
        e.preventDefault();
        deconnecter();
        afficherSection('programme');
    });

    document.getElementById('lien-jauges').addEventListener('click', (e) => {
        e.preventDefault();
        chargerJauges();
    });

    document.getElementById('btn-retour-jauges').addEventListener('click', () => {
        afficherSection('programme');
    });

    document.getElementById('btn-retour').addEventListener('click', () => {
        afficherSection('programme');
    });

    document.getElementById('vers-inscription').addEventListener('click', (e) => {
        e.preventDefault();
        loginForm.style.display = 'none';
        registerForm.style.display = 'flex';
    });

    document.getElementById('vers-connexion').addEventListener('click', (e) => {
        e.preventDefault();
        registerForm.style.display = 'none';
        loginForm.style.display = 'flex';
    });

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        loginError.textContent = '';
        try {
            const result = await connecter(
                document.getElementById('login-email').value,
                document.getElementById('login-password').value
            );
            if (result.success) {
                loginForm.reset();
                afficherSection('programme');
            } else {
                loginError.textContent = result.message;
            }
        } catch (error) {
            loginError.textContent = "Impossible de joindre le serveur.";
        }
    });

    registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        registerError.textContent = '';
        const email = document.getElementById('register-email').value;
        const password = document.getElementById('register-password').value;
        try {
            const result = await inscrire(
                document.getElementById('register-nom').value,
                document.getElementById('register-prenom').value,
                email,
                password
            );
            if (!result.success) {
                registerError.textContent = result.message;
                return;
            }
            const login = await connecter(email, password);
            if (login.success) {
                registerForm.reset();
                afficherSection('programme');
            } else {
                registerError.textContent = login.message;
            }
        } catch (error) {
            registerError.textContent = "Impossible de joindre le serveur.";
        }
    });
}

async function chargerJauges() {
    const container = document.getElementById('jauges-liste');
    const token = getToken();
    if (!token) {
        afficherSection('auth');
        return;
    }

    afficherSection('jauges');
    container.innerHTML = '<p>Chargement...</p>';

    try {
        const response = await fetch(`${API_BASE_URL}/jauges`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });

        if (response.status === 403) {
            container.innerHTML = '<p>Accès réservé aux gestionnaires.</p>';
            return;
        }
        if (!response.ok) {
            container.innerHTML = '<p>Erreur lors du chargement des jauges.</p>';
            return;
        }

        const data = await response.json();
        container.innerHTML = `
            <table class="jauges-table">
                <thead>
                    <tr><th>Soirée</th><th>Date</th><th>Lieu</th><th>Jauge</th><th>Recette</th></tr>
                </thead>
                <tbody>
                    ${data.soirees.map(s => `
                        <tr>
                            <td>${s.nom}</td>
                            <td>${s.date}</td>
                            <td>${s.lieu}</td>
                            <td>
                                <div class="jauge-barre"><div class="jauge-remplie" style="width: ${Math.min(s.taux_remplissage, 100)}%"></div></div>
                                ${s.places_vendues} / ${s.capacite} (${s.taux_remplissage} %)
                            </td>
                            <td>${s.recette.toFixed(2)} €</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>`;
    } catch (error) {
        console.error("Erreur Fetch jauges :", error);
        container.innerHTML = '<p>Impossible de joindre le serveur.</p>';
    }
}

