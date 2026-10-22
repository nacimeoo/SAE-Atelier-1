const API_BASE_URL = 'http://localhost:7080'; 

document.addEventListener('DOMContentLoaded', () => {
    chargerSpectacles();
    chargerStyles();
    afficherCompteur();

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

function getPanierId() {
    let panierId = localStorage.getItem('panierId');
    if (!panierId) {
        panierId = crypto.randomUUID();
        localStorage.setItem('panierId', panierId);
    }
    return panierId;
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
    const response = await fetch(`${API_BASE_URL}/panier/${getPanierId()}/lignes`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
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

    if (response.status === 422) {
        const data = await response.json();
        return { success: false, message: data.message };
    }
    
    return { success: false, message: "Erreur lors de l'ajout au panier" };
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
