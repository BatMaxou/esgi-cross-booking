# CrossBooking

## Installation

### Clone

```bash
git clone git@github.com:BatMaxou/esgi-cross-booking.git
```

Retirer le .example des fichiers .env.local et compose.override.yaml pour avoir une config fonctionnelle sans se prendre la tête.

Il faudra tout de même ajouter la configuration de TWILLIO pour la gestion des SMS.
Pour cela il faudra compléter les variables suivantes dans le fichier .env.local :

```env
TWILIO_SID=
TWILIO_TOKEN=
TWILIO_FROM=
```

Si besoin de charger des fixtures, il faudra ajouter un numéro de téléphone dans la variable :
Format : +33*********

```env
TWILIO_TEST_PHONE=
```


### Install

```bash
make install
```

## FIxtures

```bash
make fixtures
```

Compte admin :

- email : adminbg@gmail.com
- mdp : azerty

## Tests

```bash
make tests
```

## Cahier des charges

### 1. Introduction

Ce cahier des charges a pour objectif de définir les besoins fonctionnels et techniques pour le développement d’un site web permettant la réservation de traversées en radeau entre la France et l’Angleterre. Le projet sera entierement réalisé en Symfony.

### 2. Objectifs du Projet

Le site doit :

- Permettre aux utilisateurs de consulter les traversées disponibles.
- Offrir un système de réservation en ligne.
- Gérer les informations relatives aux radeaux, traversées, clients, et réservations.
- Fournir une interface d’administration pour gérer les données.

### 3. Fonctionnalités

#### 3.1 Fonctionnalités Utilisateurs

- Inscription et connexion des utilisateurs
- Consultation des traversées disponibles
- Réservation de traversées
- Modification des informations personnelles
- Consultation de l’historique des réservations
- Création d'équipe
- Gestion des membres de son / ses équipe(s)
- Réservation de son / ses équipe(s)

#### 3.2 Fonctionnalités Administrateurs

- Gestion des **Utilisateurs** (ajout d'admin, modification, suppression)
- Gestion des **Marques d'embarcations / Entreprises** (ajout, modification, suppression)
- Gestion des **Ports** (ajout, modification, suppression)
- Gestion des **Routes** / Itinéraires (ajout, modification, suppression)
- Gestion des **Équipes** (ajout, modification, suppression)
- Gestion des **Embarcations** (ajout, modification, suppression)
- Gestion des **Traversées** (ajout, modification, suppression)
- Gestion des **Avis** (ajout, modification, suppression)
- Gestion des **Réservations Simple** (ajout, modification, suppression)
- Gestion des **Réservations d'équipe** (ajout, modification, suppression)
- Gestion de **Contenu dynamique** (modification)

### 4. Architecture et Modélisation des Données

Le projet comprendra au moins 10 entités avec les relations suivantes :

#### 4.1 Entités

1. **User** : représente les utilisateurs du site (clients ou administrateurs).
2. **Team** : représente les équipes d'utilisateurs.
3. **Raft** : représente les embarcations utilisées pour les traversées.
4. **Company** : représente les marques d'embarcations.
5. **Crossing** : représente les traversées programmées.
6. **SimpleReservation** : représente les réservations effectuées par les utilisateurs.
7. **TeamReservation** : représente les réservations effectuées par les équipes.
8. **Port** : représente les ports de départ et d’arrivée des traversées.
9. **Route** : représente les itinéraires entre 2 ports.
10. **Review** : représente les avis laissés par les utilisateurs.
11. **SiteMessage** : représente les bandeaux texte administrable affichés sur le site.

### 4.2 Héritage

Les 2 types de réservations étenderont de la classe **Reservation** qui contiendra les informations communes.

### 4.3 Relations entre les Entités

- **ManyToMany** :

  - Crossing <-> Raft : une traversée peut inclure plusieurs radeaux, et un radeau peut être utilisé pour plusieurs traversées.
  - User <-> Team : un utilisateur peut être membre de plusieurs équipes, et une équipe peut avoir plusieurs membres.

- **OneToMany** :

  1. Raft -> Company : une marque peut avoir plusieurs embarcations.
  2. Crossing -> Reservation : une traversée peut avoir plusieurs réservations.
  3. Crossing -> Review : une traversée peut avoir plusieurs avis.
  4. Port -> Route : un port peut être le départ ou l'arrivée de plusieurs itinéraires.
  5. Route -> Crossing : un itinéraire peut inclure plusieurs traversées.
  6. Team -> TeamReservation : une équipe peut avoir plusieurs réservations.
  7. User -> SimpleReservation : un utilisateur peut effectuer plusieurs réservations simples.
  8. User -> Review : un utilisateur peut laisser plusieurs avis.
  9. User -> Team : un utilisateur peut être membre de plusieurs équipes.

## 5. Pages du Site

1. Page d’accueil
2. Page d’inscription
2. Page de connexion
3. Page d'oublie de mot de passe
4. Page de réinitialisation du mot de passe
5. Page de confirmation de création de compte
6. Page liste de traversées
7. Page de détail d’une traversée
8. Page de profil utilisateur
9. Page liste des réservations
10. Page de création d'équipe
11. Page liste équipes
12. Page de détail d'une équipe
13. Page de bannissement

## 6. Exigences Techniques

- **Framework** : Symfony.
- **Base de données** : MySQL.
- **Sécurité** :
  - Authentification et autorisation basées sur les rôles (utilisateur/admin/banni).
  - Validation des droits de vision des pages.
- **Responsive Design** : le site doit être optimisé pour les mobiles, tablettes et desktops.
- **API** : prévoir 2 endpoints d'API pour:
  - Lister toutes les traversées
  - Lister les détails d'une traversée
- **Asynchrone** : Les envoies de mail et SMS se feront en asynchrone afin de ne pas bloquer l'utilisateur.
