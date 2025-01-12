# Cahier des Charges : Site de Réservation de Traversées en Radeau

## 1. Introduction

Ce cahier des charges a pour objectif de définir les besoins fonctionnels et techniques pour le développement d’un site web permettant la réservation de traversées en radeau entre la France et l’Angleterre. Le projet sera réalisé en Symfony.

## 2. Objectifs du Projet

Le site doit :

- Permettre aux utilisateurs de consulter les traversées disponibles.
- Offrir un système de réservation en ligne.
- Gérer les informations relatives aux radeaux, traversées, clients, et réservations.
- Fournir une interface d’administration pour gérer les données.

## 3. Fonctionnalités

### 3.1 Fonctionnalités Utilisateurs

- **Inscription et connexion des utilisateurs**
- Consultation des traversées disponibles
- Réservation de traversées
- Consultation de l’historique des réservations

### 3.2 Fonctionnalités Administrateurs

- Gestion des radeaux (ajout, modification, suppression)
- Gestion des traversées (ajout, modification, suppression)
- Gestion des réservations
- Consultation des statistiques d’utilisation du site

## 4. Architecture et Modélisation des Données

Le projet comprendra au moins 10 entités avec les relations suivantes :

### 4.1 Entités

1. **User** : représente les utilisateurs du site (clients ou administrateurs).
2. **Raft** : représente les radeaux utilisés pour les traversées.
3. **Crossing** : représente les traversées programmées.
4. **Reservation** : représente les réservations effectuées par les utilisateurs.
5. **Route** : représente les itinéraires entre la France et l’Angleterre.
6. **Review** : représente les avis laissés par les utilisateurs.
7. **AdminSettings** : représente les paramètres administratifs du site.
8. **Port** : représente les ports de départ et d’arrivée des traversées.
9. **Schedule** : représente les horaires spécifiques associés aux traversées.
10. **WeatherCondition** : représente les conditions météorologiques associées à une traversée.
11. **SiteMessage** : représente les bandeaux texte administrable affichés sur le site.

### 4.2 Relations entre les Entités

- **ManyToMany** :

  - User <-> Route : un utilisateur peut préférer plusieurs itinéraires, et un itinéraire peut être préféré par plusieurs utilisateurs.
  - Crossing <-> Raft : une traversée peut inclure plusieurs radeaux, et un radeau peut être utilisé pour plusieurs traversées.

- **OneToMany** :

  - User -> Reservation : un utilisateur peut effectuer plusieurs réservations.
  - Crossing -> Reservation : une traversée peut avoir plusieurs réservations.
  - Route -> Crossing : un itinéraire peut inclure plusieurs traversées.
  - User -> Review : un utilisateur peut laisser plusieurs avis.
  - Port -> Crossing : un port peut être associé à plusieurs traversées (départ ou arrivée).
  - Crossing -> Schedule : une traversée peut avoir plusieurs horaires associés.
  - Crossing -> WeatherCondition : une traversée peut être affectée par plusieurs conditions météorologiques.
  - Raft -> CrewMember : un radeau peut avoir plusieurs membres d’équipage affectés.

## 5. Pages du Site

1. Page d’accueil
2. Page d’inscription/connexion
3. Page de mot de passe oublié
4. Page de réinitialisation du mot de passe
5. Page de consultation des traversées
6. Page de détail d’une traversée
7. Page de réservation (vérifier le code envoyé par mail)
8. Page de confirmation de réservation
9. Page de profil utilisateur
10. Page de l’historique des réservations

## 6. Exigences Techniques

- **Framework** : Symfony (dernière version stable).
- **Base de données** : MySQL ou PostgreSQL.
- **Sécurité** :
  - Authentification et autorisation basées sur les rôles (utilisateur/admin).
  - Validation des entrées utilisateur.
- **Responsive Design** : le site doit être optimisé pour les mobiles, tablettes et desktops.
- **API** : prévoir une API REST pour certaines fonctionnalités (exemple : consultation des traversées).

## 7. Contraintes et Délais

- **Délai de livraison** : 4 mois.
- **Langue** : le site doit être disponible en anglais et en français.

## 8. Livraison Attendue

- Code source hébergé sur un dépôt Git.
- Documentation technique (structure du projet, modèle de données, guide d’installation).
- Site fonctionnel déployé sur un environnement de production.

---

Ce cahier des charges pourra être modifié en fonction des retours du client ou des parties prenantes.


--------------------

command perso -> brouillé les données (confirmé l'exportation des données et mettre un bandeau site en maintenance)
asynchrone -> envoi de mail
temps reel -> nb de place restante / commentaires en temps réel
