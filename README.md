# 📚 Gestion Bibliothèque — Application Web de Gestion de Bibliothèque

> Application web complète de gestion d'une bibliothèque, développée en **PHP & MySQL** avec une interface HTML/CSS multi-rôles (Administrateur, Bibliothécaire, Stagiaire, Utilisateur).

![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat-square&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat-square&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black)
![License](https://img.shields.io/badge/License-MIT-blue?style=flat-square)

---

## 📋 Table des matières

- [Aperçu](#-aperçu)
- [Problématique](#-problématique)
- [Fonctionnalités](#-fonctionnalités)
- [Architecture & Technologies](#-architecture--technologies)
- [Base de données](#-base-de-données)
- [Structure du projet](#-structure-du-projet)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Utilisation](#-utilisation)
- [Interfaces graphiques](#-interfaces-graphiques)
- [Auteure](#-auteure)

---

## Aperçu

**Gestion Bibliothèque** est une application web complète permettant la gestion centralisée d'une bibliothèque. Elle propose plusieurs espaces selon le rôle connecté : un **espace utilisateur** pour consulter et emprunter des livres, un **espace administrateur** pour piloter l'ensemble des ressources (livres, périodiques, exemplaires, emprunts, amendes, utilisateurs, statistiques), et un **espace stagiaire** offrant un accès en lecture aux données.

La charte graphique adopte un design en **violet et blanc** pour une interface moderne et épurée.

---

## Problématique

La gestion manuelle d'une bibliothèque — suivi des emprunts, gestion des retards, contrôle des exemplaires disponibles, administration des utilisateurs — est une tâche complexe et chronophage. Les méthodes traditionnelles (registres papier, tableaux Excel) manquent de réactivité et sont sources d'erreurs.

**Gestion Bibliothèque** apporte une solution numérique centralisée qui automatise le suivi des emprunts, signale les retards, calcule les amendes, gère les différents types d'utilisateurs, et offre des statistiques en temps réel sur l'état de la bibliothèque.

---

## Fonctionnalités

### Authentification & Gestion des comptes

- **Connexion sécurisée** — Authentification par email et mot de passe avec redirection automatique selon le rôle (Administrateur, Bibliothécaire, Stagiaire, Utilisateur)
- **Inscription** — Formulaire complet avec validations en temps réel :
  - Champs obligatoires : nom, prénom, date de naissance, ville, code postal, téléphone, email, type, mot de passe
  - Vérification d'unicité de l'adresse email
  - Validation du format du numéro de téléphone (chiffres uniquement)
  - Types d'utilisateurs : Occasionnel, Abonné, Abonné Privilégié
  - Mot de passe hashé avec **BCrypt** (`password_hash`)
- **Réinitialisation du mot de passe** — Formulaire de changement de mot de passe sécurisé avec vérification de l'ancien mot de passe
- **Déconnexion** — Destruction de la session et redirection vers la page de connexion

### Espace Utilisateur

- **Tableau de bord** — Menu latéral avec navigation vers les sections Livres et Compte
- **Emprunter un livre** — Liste des livres disponibles avec barre de recherche, possibilité d'emprunter avec vérification des droits et du quota maximum (5 emprunts par défaut)
- **Livres empruntés** — Consultation des livres actuellement empruntés par l'utilisateur, avec bouton de retour
- **Mes informations** — Consultation des données du profil connecté
- **Modifier mes informations** — Mise à jour des informations personnelles synchronisée en base de données

### Espace Administrateur

- **Tableau de bord admin** — Menu latéral complet avec 9 catégories : Livres, Périodiques, Emprunts, Bibliothécaires, Exemplaires, Utilisateurs, Amendes, Statistiques, Notifications

**Gestion des Livres**
- Liste des livres (triable par titre, prix croissant, prix décroissant)
- Ajouter un livre (titre, auteurs, année, éditeur, ISBN, prix) — insertion transactionnelle dans `Doc` + `Livre`
- Modifier un livre
- Supprimer un livre (suppression en cascade via `Doc`)

**Gestion des Périodiques**
- Liste des périodiques (triable par titre, prix croissant, prix décroissant)
- Ajouter, modifier, supprimer un périodique

**Gestion des Exemplaires**
- Liste complète des exemplaires
- Ajouter / modifier / supprimer un exemplaire
- Vues filtrées : exemplaires disponibles (en rayon), empruntés, en maintenance, en retard, en réserve
- Catalogue (nombre d'exemplaires par document)
- Vérification de disponibilité d'un exemplaire
- État d'un exemplaire

**Gestion des Emprunts**
- Liste complète des emprunts
- Modifier un emprunt
- Tri par date d'emprunt
- Nombre d'emprunts par utilisateur
- Emprunts du jour
- Documents les plus empruntés
- Nombre d'emprunts par document

**Gestion des Utilisateurs**
- Liste des utilisateurs avec informations complètes
- Utilisateurs bloqués
- Ajouter / modifier / supprimer un utilisateur
- Consulter les emprunts d'un utilisateur
- Enregistrer un emprunt pour un utilisateur
- Retourner un emprunt

**Gestion des Amendes**
- Enregistrer une amende (liée à un utilisateur et un exemplaire)
- Liste des amendes
- Supprimer une amende

**Statistiques**
- Nombre total de documents, exemplaires, livres, périodiques et utilisateurs

**Notifications**
- Liste des emprunts en retard
- Envoi de message aux utilisateurs en retard

### Espace Stagiaire

- **Tableau de bord stagiaire** — Accès en lecture seule : liste des livres et périodiques (avec tri), consultation des emprunts, liste des utilisateurs et utilisateurs bloqués, emprunts par utilisateur, emprunts du jour, documents les plus empruntés

---

## Architecture & Technologies

| Couche | Technologie | Rôle |
|--------|-------------|------|
| Langage serveur | **PHP** | Logique métier, accès base de données, sessions |
| Interface graphique | **HTML5 / CSS3** | Structure et style des pages, frameset multi-panneaux |
| Interactivité | **JavaScript** | Validation des formulaires côté client, menus déroulants |
| Base de données | **MySQL** | Stockage relationnel (`mylast_biblio`) |
| Connecteur BDD | **MySQLi / PDO** | Requêtes préparées, transactions |
| Serveur local | **XAMPP / WAMP** | Apache + PHP + MySQL |

**Patterns & bonnes pratiques :**
- **PreparedStatement / requêtes préparées** — Protection contre les injections SQL
- **Transactions MySQL** — Insertion atomique lors de l'ajout de livres/périodiques (tables `Doc` + `Livre`/`Perio`)
- **`password_hash` BCrypt** — Hachage sécurisé des mots de passe à l'inscription
- **`session_start()`** — Gestion des sessions utilisateur entre les pages
- **Frameset HTML** — Séparation visuelle barre de navigation / menu / contenu principal
- **Séparation des rôles** — Redirection automatique à la connexion selon le rôle (Admin, Bibliothécaire, Stagiaire, Utilisateur)

---

## Base de données

La base de données `mylast_biblio` (MySQL) est composée de plusieurs tables avec contraintes d'intégrité référentielle.

```
┌──────────────────────────────┐        ┌──────────────────────────────────┐
│            User              │        │           Emprunter               │
│──────────────────────────────│        │──────────────────────────────────│
│ id_user        INT (PK, AI)  │───┐    │ id_emp         INT (PK, AI)      │
│ nom            VARCHAR       │   └───►│ id_user        INT (FK)          │
│ prenom         VARCHAR       │        │ id_ex          INT (FK)    ◄──┐  │
│ ville          VARCHAR       │        │ date_emprunt   DATETIME        │  │
│ codePostal     VARCHAR       │        │ date_retour    DATETIME (NULL) │  │
│ tel            VARCHAR       │        └──────────────────────────────────┘  │
│ email          VARCHAR       │                                               │
│ type           ENUM          │        ┌──────────────────────────────────┐   │
│ dateNaissance  DATE          │        │            Exemp                  │   │
│ inscr          DATETIME      │        │──────────────────────────────────│   │
│ interdit       TINYINT       │        │ id_ex          INT (PK, AI)      │───┘
│ max_pret       INT           │        │ id_doc         INT (FK)    ◄──┐  │
│ password       VARCHAR       │        │ statut         VARCHAR         │  │
└──────────────────────────────┘        └──────────────────────────────────┘  │
                                                                               │
┌──────────────────────────────┐        ┌──────────────────────────────────┐   │
│            Doc               │        │            Amende                 │   │
│──────────────────────────────│───┐    │──────────────────────────────────│   │
│ id_doc         INT (PK, AI)  │   └───►│ id_user        INT (FK)          │   │
│ titre          VARCHAR       │        │ id_ex          INT (FK)          │   │
│ ref            VARCHAR       │        │ montant        DECIMAL           │   │
│ annee          INT           │        │ raison         VARCHAR           │   │
│ editeur        VARCHAR       │        └──────────────────────────────────┘   │
│ cat            ENUM          │                                                │
└──────────────────────────────┘        ┌──────────────────────────────────┐   │
        │                               │            Livre                  │   │
        ├──────────────────────────────►│ id_doc      INT (FK, PK)         │   │
        │                               │ auteurs     VARCHAR              │   │
        │                               │ isbn        VARCHAR              │   │
        │                               │ prixl       DECIMAL              │   │
        │                               └──────────────────────────────────┘   │
        │                                                                       │
        │                               ┌──────────────────────────────────┐   │
        └──────────────────────────────►│            Perio                  │   │
                                        │ id_doc      INT (FK, PK)         │   │
                                        │ issn        VARCHAR              │   │
                                        │ periodicite VARCHAR              │   │
                                        │ prixp       DECIMAL              │   │
                                        └──────────────────────────────────┘   │
                                                                    ▲           │
                                                                    └───────────┘
```

### Description des tables

| Table | Description |
|-------|-------------|
| `User` | Comptes utilisateurs avec rôle, type (Occasionnel/Abonné/Privilégié), statut de blocage et quota d'emprunts |
| `Doc` | Table mère de tous les documents (livres et périodiques) — contient titre, référence, année, éditeur, catégorie |
| `Livre` | Extension de `Doc` pour les livres — auteurs, ISBN, prix |
| `Perio` | Extension de `Doc` pour les périodiques — ISSN, périodicité, prix |
| `Exemp` | Exemplaires physiques de chaque document avec leur statut (rayon, prêt, maintenance, réserve…) |
| `Emprunter` | Liaison User ↔ Exemp, avec dates d'emprunt et de retour (NULL si en cours) |
| `Amende` | Amendes enregistrées par utilisateur et exemplaire, avec montant et raison |
| `biblio` | Table de gestion des comptes bibliothécaires (login, mot de passe) |

---

## Structure du projet

```
gestion bibliothèque/
│
├── — Authentification —
├── login.html                        # Page de connexion (formulaire)
├── login.php                         # Traitement de la connexion + gestion de session
├── inscription.php                   # Formulaire + traitement de l'inscription utilisateur
├── deconnection.php                  # Destruction de session + redirection
├── deconnection.html                 # Page de déconnexion (barre supérieure)
├── reset_password.html               # Formulaire de réinitialisation du mot de passe
├── reset_password_handler.php        # Traitement de la réinitialisation du mot de passe
│
├── — Points d'entrée (Frameset) —
├── admin.php                         # Entrée espace Administrateur (frameset 3 panneaux)
├── admin.html                        # Variante statique de l'entrée admin
├── utilisateur.html                  # Entrée espace Utilisateur
├── stagiaire.php / stagiaire         # Entrée espace Stagiaire
│
├── — Menus latéraux (Dashboard) —
├── dashboardadm.php                  # Menu admin : Livres, Périodiques, Emprunts, Exemplaires, Utilisateurs, Amendes, Stats, Notifs
├── dashboardadm.html                 # Version statique du menu admin
├── dashboard.php                     # Menu utilisateur : Livres, Compte
├── dashboardstag.php                 # Menu stagiaire : accès lecture seule
│
├── — Pages par défaut —
├── defaultadm.php / defaultadm.html  # Page d'accueil du panneau principal admin
├── tous_les_livres.html              # Page d'accueil utilisateur (liste des livres)
│
├── — Gestion des Livres —
├── liste_livres.php                  # Liste de tous les livres
├── ajouter_livre.php                 # Formulaire d'ajout (transaction Doc + Livre)
├── modif_livre.php                   # Modification d'un livre
├── supp_livre.php                    # Suppression d'un livre
├── ordre_par_titre.php               # Liste triée par titre
├── ordre_prix_croissant.php          # Liste triée par prix croissant
├── ordre_prix_decroissant.php        # Liste triée par prix décroissant
│
├── — Gestion des Périodiques —
├── liste_periodiques.php             # Liste de tous les périodiques
├── ajouter_perio.php                 # Formulaire d'ajout de périodique
├── modif_perio.php                   # Modification d'un périodique
├── supp_perio.php                    # Suppression d'un périodique
├── ordre_par_titre_periodiques.php   # Tri périodiques par titre
├── ordre_prix_croissant_periodiques.php   # Tri par prix croissant
├── ordre_prix_decroissant_periodiques.php # Tri par prix décroissant
│
├── — Gestion des Exemplaires —
├── liste_exemplaires.php             # Liste de tous les exemplaires
├── ajout_exemplaires.php             # Ajouter un exemplaire
├── modif_exemplaires.php / modif_exempl.php  # Modifier un exemplaire
├── supp_exemplaires.php              # Supprimer un exemplaire
├── exemplaires_rayon.php             # Exemplaires disponibles (en rayon)
├── exemplaires_pret.php              # Exemplaires actuellement empruntés
├── exemplaires_trav.php              # Exemplaires en maintenance
├── exemplaires_retard.php            # Exemplaires en retard de retour
├── exemplaires_reserve.php           # Exemplaires en réserve
├── nbr_exempl.php                    # Catalogue (nb exemplaires par document)
├── dispo.php                         # Vérification de disponibilité d'un exemplaire
├── etat_exempl.php                   # État d'un exemplaire
│
├── — Gestion des Emprunts —
├── liste_emprunts.php                # Liste complète des emprunts
├── modif_empr.php                    # Modifier un emprunt
├── emprunter_livre.php               # Emprunter un livre (espace utilisateur)
├── emprunter.php / empunter.php      # Traitement de l'emprunt
├── retourner.php                     # Retourner un emprunt (espace utilisateur)
├── livres_empruntes.php              # Livres empruntés par l'utilisateur connecté
├── livres_empruntes_ut.php           # Emprunts d'un utilisateur (admin)
├── livres_emp_adm.php                # Vue admin des livres empruntés
├── ordre_par_date.php                # Emprunts triés par date
├── aujoudr_emp.php                   # Emprunts du jour
├── util_nbr_emp.php                  # Nombre d'emprunts par utilisateur
├── Nbr_emp_doc.php                   # Nombre d'emprunts par document
├── plus_emp.php                      # Documents les plus empruntés
│
├── — Gestion des Utilisateurs —
├── liste_utilisateurs.php            # Liste complète des utilisateurs
├── ajout_ut.php                      # Ajouter un utilisateur (admin)
├── modif_ut.php                      # Modifier les infos d'un utilisateur
├── supprimer_utilisateur.php         # Supprimer un utilisateur
├── util_bloc.php                     # Utilisateurs bloqués
├── empint_utilisat.php               # Emprunts d'un utilisateur (admin)
├── ajouter_empint_uti.php            # Enregistrer un emprunt pour un utilisateur
├── supp_empint_uti.php               # Retourner un emprunt (admin)
├── mes_informations.php              # Profil de l'utilisateur connecté
├── modifier_compte.php               # Modifier son propre profil
├── liste_bibliothecaires.php         # Liste des bibliothécaires
│
├── — Gestion des Amendes —
├── enreg_amende.php                  # Enregistrer une amende
├── liste_amende.php                  # Liste des amendes
├── supp_amende.php                   # Supprimer une amende
│
├── — Statistiques & Notifications —
├── stat.php                          # Statistiques globales de la bibliothèque
├── notif.php                         # Liste des emprunts en retard
├── envoy.php                         # Envoyer un message aux utilisateurs en retard
│
├── — Feuilles de style —
├── styles.css                        # Style principal
├── stylestest.css                    # Style des tableaux et pages de liste
├── stylestest2.css                   # Style alternatif
└── styleinsc.css                     # Style du formulaire d'inscription
```

---

## Installation

### Prérequis

- [XAMPP](https://www.apachefriends.org/) ou [WAMP](https://www.wampserver.com/) (Apache + PHP + MySQL)
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Navigateur web moderne

### Étapes

**1. Cloner ou décompresser le projet**
```bash
git clone https://github.com/votre-repo/gestion-bibliotheque.git
```
Ou décompresser le `.zip` directement dans le dossier `htdocs` (XAMPP) ou `www` (WAMP).

**2. Placer le projet dans le dossier web**
```
C:/xampp/htdocs/gestion_bibliotheque/   (Windows - XAMPP)
/opt/lampp/htdocs/gestion_bibliotheque/ (Linux - XAMPP)
```

**3. Créer la base de données**

Dans phpMyAdmin (`http://localhost/phpmyadmin`) :
- Créer une nouvelle base nommée `mylast_biblio`
- Importer le fichier SQL fourni :
```sql
SOURCE /chemin/vers/mylast_biblio.sql;
```

**4. Configurer la connexion** *(voir section Configuration ci-dessous)*

**5. Lancer l'application**

Démarrer Apache et MySQL depuis le panneau XAMPP, puis ouvrir :
```
http://localhost/gestion_bibliotheque/login.html
```

---

## Configuration

### Connexion à la base de données

La connexion est définie directement dans chaque fichier PHP. Modifier les paramètres suivants :

```php
$host     = 'localhost';
$user     = 'root';
$password = '';              // Votre mot de passe MySQL
$database = 'mylast_biblio';
```

---

## Utilisation

### Créer un compte utilisateur

Depuis la page de connexion, cliquer sur **"S'inscrire"** et renseigner :
- Nom et prénom
- Date de naissance
- Ville et code postal
- Numéro de téléphone (chiffres uniquement)
- Adresse email (unique)
- Type d'utilisateur : Occasionnel / Abonné / Abonné Privilégié
- Mot de passe

### Accès Administrateur / Bibliothécaire

Se connecter avec un compte dont le rôle est `Administrateur` ou `Bibliothécaire` — la redirection vers l'espace correspondant est automatique.

---

### Page de Connexion — `login.html`

<img width="559" height="313" alt="image" src="https://github.com/user-attachments/assets/45bdc262-af07-4ad3-bf1b-c620223249c2" />

---



## Auteure

Projet réalisé dans le cadre d'un projet académique.

- **Filière :** Génie Informatique
- **Année académique :** 2024 – 2025

---

<div align="center">
  <sub>Fait avec ❤️ — Gestion Bibliothèque &copy; 2024/2025</sub>
</div>
