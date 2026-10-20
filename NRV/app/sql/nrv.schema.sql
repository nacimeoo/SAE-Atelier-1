DROP TABLE IF EXISTS billet        CASCADE;
DROP TABLE IF EXISTS commande      CASCADE;
DROP TABLE IF EXISTS ligne_panier  CASCADE;
DROP TABLE IF EXISTS panier        CASCADE;
DROP TABLE IF EXISTS pass_soiree   CASCADE;
DROP TABLE IF EXISTS pass          CASCADE;
DROP TABLE IF EXISTS image         CASCADE;
DROP TABLE IF EXISTS spectacle     CASCADE;
DROP TABLE IF EXISTS soiree        CASCADE;
DROP TABLE IF EXISTS lieu          CASCADE;
DROP TABLE IF EXISTS utilisateur   CASCADE;

DROP TYPE IF EXISTS role;
DROP TYPE IF EXISTS type_tarif;
DROP TYPE IF EXISTS statut_commande;

CREATE TYPE role            AS ENUM ('CLIENT', 'GESTIONNAIRE');
CREATE TYPE type_tarif      AS ENUM ('NORMAL', 'REDUIT');
CREATE TYPE statut_commande AS ENUM ('EN_ATTENTE', 'PAYEE');

CREATE TABLE utilisateur (
    id             UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nom            VARCHAR(100) NOT NULL,
    prenom         VARCHAR(100) NOT NULL,
    email          VARCHAR(255) NOT NULL,
    password_hash  VARCHAR(255) NOT NULL,
    role           role NOT NULL DEFAULT 'CLIENT'
);

CREATE TABLE lieu (
    id         UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nom        VARCHAR(150) NOT NULL,
    adresse    VARCHAR(255) NOT NULL,
    nb_places  INT NOT NULL
);

CREATE TABLE soiree (
    id                 UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nom                VARCHAR(150) NOT NULL,
    thematique         VARCHAR(100) NOT NULL,
    date               DATE NOT NULL,
    heure_debut        TIME NOT NULL,
    tarif_normal       NUMERIC(8,2) NOT NULL,
    tarif_reduit       NUMERIC(8,2) NOT NULL,
    nb_places_vendues  INT NOT NULL DEFAULT 0,
    lieu_id            UUID NOT NULL REFERENCES lieu(id)
);

CREATE TABLE spectacle (
    id           UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    titre        VARCHAR(150) NOT NULL,
    artistes     VARCHAR(255) NOT NULL,
    description  TEXT NOT NULL,
    style        VARCHAR(50) NOT NULL,
    url_video    VARCHAR(255),
    heure        TIME NOT NULL,
    soiree_id    UUID NOT NULL REFERENCES soiree(id)
);

CREATE TABLE image (
    id            UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    url           VARCHAR(255) NOT NULL,
    spectacle_id  UUID NOT NULL REFERENCES spectacle(id)
);

CREATE TABLE pass (
    id       UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    libelle  VARCHAR(150) NOT NULL
);

CREATE TABLE pass_soiree (
    pass_id    UUID NOT NULL REFERENCES pass(id),
    soiree_id  UUID NOT NULL REFERENCES soiree(id),
    PRIMARY KEY (pass_id, soiree_id)
);

CREATE TABLE panier (
    id              UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    utilisateur_id  UUID NOT NULL REFERENCES utilisateur(id)
);

CREATE TABLE ligne_panier (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    panier_id   UUID NOT NULL REFERENCES panier(id),
    soiree_id   UUID REFERENCES soiree(id),
    pass_id     UUID REFERENCES pass(id),
    quantite    INT NOT NULL,
    type_tarif  type_tarif NOT NULL
);

CREATE TABLE commande (
    id              UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    date_commande   TIMESTAMP NOT NULL DEFAULT NOW(),
    montant_total   NUMERIC(8,2) NOT NULL,
    statut          statut_commande NOT NULL DEFAULT 'EN_ATTENTE',
    utilisateur_id  UUID NOT NULL REFERENCES utilisateur(id)
);

CREATE TABLE billet (
    id           UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    reference    VARCHAR(30) NOT NULL,
    token        VARCHAR(64) NOT NULL,
    type_tarif   type_tarif NOT NULL,
    prix         NUMERIC(8,2) NOT NULL,
    commande_id  UUID NOT NULL REFERENCES commande(id),
    soiree_id    UUID NOT NULL REFERENCES soiree(id)
);