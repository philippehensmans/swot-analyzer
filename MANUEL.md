# Manuel d'utilisation - Application SWOT/TOWS Multi-utilisateurs

## Table des matieres

1. [Presentation](#presentation)
2. [Installation](#installation)
3. [Guide Administrateur](#guide-administrateur)
4. [Guide Formateur](#guide-formateur)
5. [Guide Participant](#guide-participant)
6. [FAQ et Depannage](#faq-et-depannage)

---

## Presentation

Cette application permet de realiser des analyses SWOT (Forces, Faiblesses, Opportunites, Menaces) et TOWS (strategies croisees) dans un contexte de formation collective.

### Fonctionnalites principales

- **Sessions de formation** : Chaque formation dispose de son propre espace avec un code unique
- **Multi-utilisateurs** : Chaque participant a sa propre analyse, sauvegardee sur le serveur
- **Temps reel** : Le formateur peut suivre l'avancement des participants en direct
- **Projection** : Affichage plein ecran pour projection et discussion collective
- **Comparaison** : Possibilite de comparer plusieurs analyses cote a cote
- **Export** : Export PDF et copie dans le presse-papier

---

## Installation

### Prerequis

- Serveur web avec PHP 7.4 ou superieur
- Extension PDO SQLite activee
- Droits d'ecriture sur le dossier `data/`

### Etapes d'installation

1. **Copier les fichiers** sur votre serveur web

2. **Verifier les permissions** :
   ```bash
   chmod 755 data/
   chmod 644 data/*.db
   ```

3. **Tester l'installation** en accedant a `admin_sessions.php`

La base de donnees SQLite sera creee automatiquement lors du premier acces.

---

## Guide Administrateur

### Creer une session de formation

1. Accedez a **admin_sessions.php**

2. Remplissez le formulaire :
   - **Nom de la session** : Ex: "Formation SWOT - Janvier 2025"
   - **Mot de passe formateur** (optionnel) : Pour proteger l'acces a l'interface formateur
   - **Description** (optionnel) : Notes sur la formation

3. Cliquez sur **"Creer la session"**

4. Un **code unique** est genere (ex: `XY7K2P`)
   - Notez ce code, il sera distribue aux participants

### Gerer les sessions existantes

Dans la liste des sessions, vous pouvez :

| Action | Description |
|--------|-------------|
| **Desactiver** | Empeche les nouveaux participants de rejoindre |
| **Activer** | Reactive une session desactivee |
| **Supprimer** | Supprime la session ET toutes les donnees associees |

### Informations affichees

- **Participants** : Nombre de personnes inscrites
- **Soumis** : Nombre d'analyses soumises
- **Statut** : Active ou Inactive
- **Date de creation**

---

## Guide Formateur

### Se connecter a l'interface formateur

1. Accedez a **formateur.php**

2. Entrez :
   - Le **code de session** (ex: `XY7K2P`)
   - Le **mot de passe** (si defini lors de la creation)

3. Cliquez sur **"Acceder a la session"**

### Tableau de bord

Le tableau de bord affiche :

```
+------------------+------------------+------------------+------------------+
| Total            | Soumis           | En cours         | Non commences    |
| participants     | (vert)           | (orange)         | (rouge)          |
+------------------+------------------+------------------+------------------+
```

### Liste des participants

La liste affiche pour chaque participant :
- Nom et prenom
- Organisation
- Statut (Soumis / En cours / Non commence)
- Date de derniere modification
- Bouton "Voir" pour afficher l'analyse

### Visualiser une analyse

1. Cliquez sur le bouton **"Voir"** a cote du participant

2. L'analyse s'affiche dans une fenetre modale avec :
   - Analyse SWOT complete
   - Analyse TOWS (si remplie)

3. Options disponibles :
   - **Plein ecran** : Pour projection sur grand ecran
   - **Imprimer** : Impression de l'analyse
   - **Fermer** : Retour a la liste

### Comparer plusieurs analyses

1. Cochez les cases a gauche des participants a comparer (minimum 2)

2. Cliquez sur **"Comparer la selection"**

3. Les analyses s'affichent les unes en dessous des autres pour faciliter la comparaison

### Conseils pour l'animation

- **Pendant l'exercice** : Gardez l'onglet "Liste des participants" ouvert pour suivre l'avancement
- **Pour la restitution** : Utilisez le mode "Plein ecran" pour projeter les analyses
- **Pour le debat** : Utilisez la fonction "Comparer" pour mettre en evidence les differences

---

## Guide Participant

### Se connecter

1. Accedez a la page d'accueil **index.php**

2. Remplissez le formulaire :
   - **Code de session** : Fourni par le formateur (ex: `XY7K2P`)
   - **Prenom** : Votre prenom
   - **Nom** : Votre nom
   - **Organisation** (optionnel) : Votre structure

3. Cliquez sur **"Commencer l'analyse"**

### Remplir l'analyse SWOT

L'ecran SWOT presente 4 quadrants :

| Quadrant | Description | Exemples |
|----------|-------------|----------|
| **Forces** (vert) | Atouts internes | Equipe engagee, expertise, reseau |
| **Faiblesses** (rouge) | Points faibles internes | Ressources limitees, manque de visibilite |
| **Opportunites** (bleu) | Facteurs externes favorables | Nouveaux financements, partenariats |
| **Menaces** (orange) | Risques externes | Reduction subventions, concurrence |

Pour chaque quadrant :
1. Tapez votre element dans le champ de saisie
2. Appuyez sur **Entree** ou cliquez sur **"Ajouter"**
3. L'element apparait dans la liste
4. Pour supprimer : cliquez sur le **X** a droite de l'element

### Sauvegarde automatique

- Toutes vos modifications sont **sauvegardees automatiquement**
- Un indicateur en bas a droite confirme la sauvegarde
- Vous pouvez fermer votre navigateur et reprendre plus tard

### Passer a l'analyse TOWS

1. Une fois le SWOT complete, cliquez sur **"Passer a l'analyse TOWS"**

2. L'ecran TOWS presente 4 strategies :

| Strategie | Croisement | Question cle |
|-----------|------------|--------------|
| **SO** | Forces + Opportunites | Comment utiliser nos forces pour saisir les opportunites ? |
| **WO** | Faiblesses + Opportunites | Comment surmonter nos faiblesses pour saisir les opportunites ? |
| **ST** | Forces + Menaces | Comment utiliser nos forces pour contrer les menaces ? |
| **WT** | Faiblesses + Menaces | Comment minimiser nos faiblesses face aux menaces ? |

3. Vous pouvez utiliser le bouton **"Suggestions automatiques"** pour avoir des idees

### Soumettre l'analyse

1. Une fois termine, cliquez sur **"Soumettre mon analyse"**

2. Votre statut passe de "Brouillon" a "Soumis"

3. **Important** : Vous pouvez toujours modifier votre analyse apres soumission

### Exporter votre travail

Cliquez sur **"Exporter l'analyse"** pour :
- Imprimer
- Telecharger en PDF
- Copier le texte

### Se deconnecter

Cliquez sur **"Deconnexion"** en haut a droite pour quitter l'application.

---

## FAQ et Depannage

### Questions frequentes

**Q: J'ai ferme mon navigateur, ai-je perdu mon travail ?**
> Non, tout est sauvegarde automatiquement sur le serveur. Reconnectez-vous avec les memes identifiants.

**Q: Puis-je modifier mon analyse apres l'avoir soumise ?**
> Oui, la soumission indique simplement au formateur que vous avez termine, mais vous pouvez continuer a modifier.

**Q: Le code de session ne fonctionne pas**
> Verifiez que vous avez bien saisi le code (attention aux majuscules). La session est peut-etre desactivee.

**Q: Je ne vois pas le bouton "Voir" pour un participant**
> Ce bouton n'apparait que si le participant a commence a remplir son analyse.

### Problemes techniques

**Erreur "Non authentifie"**
> Votre session a expire. Retournez a la page d'accueil et reconnectez-vous.

**Les donnees ne se sauvegardent pas**
> Verifiez votre connexion internet. L'indicateur en bas a droite doit afficher "Sauvegarde OK".

**Page blanche ou erreur PHP**
> Verifiez que l'extension PDO SQLite est activee sur le serveur.
> Verifiez les droits d'ecriture sur le dossier `data/`.

---

## Architecture technique

```
swot-analyzer/
├── index.php              # Page d'identification participant
├── swot_app.php           # Application SWOT/TOWS
├── formateur.php          # Interface formateur
├── admin_sessions.php     # Gestion des sessions
├── logout.php             # Deconnexion
├── config/
│   └── database.php       # Configuration base de donnees
├── api/
│   ├── save.php           # Sauvegarde analyse
│   ├── load.php           # Chargement analyse
│   ├── submit.php         # Soumission analyse
│   ├── get_participants.php
│   └── get_analysis.php
└── data/
    └── swot_analyzer.db   # Base SQLite (creee automatiquement)
```

### Base de donnees

3 tables principales :
- **sessions** : Informations sur les sessions de formation
- **participants** : Liste des participants par session
- **analyses** : Donnees SWOT/TOWS de chaque participant

---

## Support

Pour toute question ou probleme, contactez l'administrateur de la plateforme.
