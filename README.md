# Unity Care Clinic V2 - Système de Gestion Médicale

## 📋 Description du Projet

Unity Care Clinic V2 est une application web de gestion médicale complète permettant la gestion des rendez-vous, consultations et prescriptions médicales. Cette version introduit un système d'authentification robuste avec gestion des rôles utilisateurs (RBAC) et des fonctionnalités de sécurité avancées.

## 🎯 Objectifs Principaux

- Système d'authentification sécurisé avec gestion de sessions PHP
- Contrôle d'accès basé sur les rôles (Admin, Doctor, Patient)
- Gestion complète des rendez-vous médicaux
- Gestion des prescriptions et médicaments
- Protection contre les vulnérabilités web (XSS, CSRF, SQL Injection)
- Dashboard avec statistiques enrichies
- Architecture orientée objet consolidée

## 👥 Rôles Utilisateurs

### Admin
- Gestion complète des départements, médecins et patients
- Gestion du catalogue de médicaments
- Visualisation de tous les rendez-vous avec filtres
- Accès aux statistiques globales

### Doctor (Médecin)
- Consultation de ses propres rendez-vous
- Création et gestion de prescriptions
- Marquage des rendez-vous comme effectués
- Visualisation de la liste des patients (lecture seule)
- Accès aux statistiques limitées

### Patient
- Prise de rendez-vous avec les médecins
- Consultation et annulation de ses rendez-vous
- Visualisation de l'historique des prescriptions reçues

## 🏗️ Architecture

### Hiérarchie des Classes Utilisateurs

```
User (classe abstraite)
├── Admin
├── Doctor
└── Patient
```

### Classes Principales

- **User** : Classe abstraite de base avec email, username et password hashé
- **Appointment** : Gestion des rendez-vous (date, heure, médecin, patient, motif, statut)
- **Medication** : Gestion du catalogue de médicaments
- **Prescription** : Liaison médecin-patient-médicament avec instructions de dosage
- **Department** : Gestion des départements médicaux

## 📊 Matrice des Permissions

| Fonctionnalité | Admin | Doctor | Patient |
|----------------|:-----:|:------:|:-------:|
| Gérer les départements | ✓ | ✗ | ✗ |
| Gérer les médecins | ✓ | ✗ | ✗ |
| Gérer les patients | ✓ | ✓ (lecture) | ✗ |
| Gérer les médicaments | ✓ | ✗ | ✗ |
| Voir tous les rendez-vous | ✓ | ✗ | ✗ |
| Voir ses rendez-vous | ✓ | ✓ | ✓ |
| Créer un rendez-vous | ✓ | ✓ | ✓ |
| Annuler un rendez-vous | ✓ | ✓ (siens) | ✓ (siens) |
| Créer une prescription | ✗ | ✓ | ✗ |
| Voir les prescriptions | ✗ | ✓ (créées) | ✓ (reçues) |
| Voir les statistiques | ✓ | ✓ (limitées) | ✗ |

## 🔐 Sécurité

### Mesures Implémentées

- **Authentification** : Sessions PHP sécurisées avec `$_SESSION`
- **Mots de passe** : Hashage avec `password_hash()` et vérification avec `password_verify()`
- **Protection XSS** : Échappement de toutes les sorties dynamiques
- **Protection SQL Injection** : Requêtes préparées avec PDO
- **Protection CSRF** : Tokens sur tous les formulaires
- **Contrôle d'accès** : Vérification systématique des rôles avant affichage du contenu

## 📝 User Stories

### Authentification
- **US01** : Connexion avec email et mot de passe
- **US02** : Déconnexion sécurisée
- **US03** : Redirection automatique en cas d'accès non autorisé

### Rendez-vous
- **US04** : Prise de rendez-vous par le patient
- **US05** : Consultation des rendez-vous par le médecin
- **US06** : Annulation de rendez-vous
- **US07** : Marquage des rendez-vous comme effectués

### Prescriptions
- **US08** : Création de prescriptions par le médecin
- **US09** : Consultation de l'historique par le patient

### Administration
- **US10** : Gestion du catalogue de médicaments
- **US11** : Visualisation globale des rendez-vous avec filtres

### Sécurité
- **US12** : Protection CSRF et XSS sur tous les formulaires

### Bonus
- **US13** : Affichage des créneaux disponibles en temps réel

## 🚀 Fonctionnalités Bonus

### Système de Réservation Intelligent
- Affichage uniquement des créneaux disponibles du médecin sélectionné
- Horaires par défaut : 09:00-17:00
- Créneaux de 30 minutes
- Mise à jour dynamique via AJAX

### Router/Controller
- Système de routing simple
- Controllers pour regrouper la logique métier
- Séparation claire des responsabilités

## 📈 Statistiques

Le dashboard fournit des statistiques enrichies sur :
- Rendez-vous par statut (programmés, effectués, annulés)
- Distribution des rendez-vous par médecin
- Évolution mensuelle des consultations
- Médicaments les plus prescrits
- Taux de présence aux rendez-vous

## 🛠️ Technologies Utilisées

- **Backend** : PHP (POO)
- **Base de données** : MySQL/MariaDB avec PDO
- **Frontend** : HTML5, CSS3, JavaScript (AJAX)
- **Sécurité** : Sessions PHP, Password Hashing, CSRF Tokens
- **Architecture** : MVC (optionnel), OOP

## 📦 Installation

1. Cloner le repository
2. Configurer la base de données dans le fichier de configuration
3. Importer le schéma SQL fourni
4. Configurer les paramètres de connexion
5. Accéder à l'application via votre serveur web

## 🔑 Comptes de Test

Des comptes de démonstration sont disponibles pour chaque rôle :
- **Admin** : admin@unity-care.com
- **Doctor** : doctor@unity-care.com
- **Patient** : patient@unity-care.com

## 📄 License

Ce projet est développé dans un cadre éducatif.

## 👨‍💻 Contributeurs

Équipe technique Unity Care Clinic

---

**Version** : 2.0  
**Dernière mise à jour** : Décembre 2025