# SPE web-app gestion des règlements et recettes
Version 2.1 - mai 2017

L'objectif de cette web-app est d'apporter dans une vue très simple et fonctionnele un contrôle quotidien sur ses règlements / recettes.

#############################
V2.2
#############################

# Gestion des règlements en attente sur n'importe quelle banque
N'importe quel règlement en attente de positionnement peut être mis sur n'importe quelle calendrier de n'importe quelle banque.

# Corrections graphiques diverses
Un scroll a été ajouté dans les paiements en attentes. Une refonte visuelle légère a été appliquée pour une meilleure lecture des paiements et de leur montant 

#############################
V2.1
#############################

# Gestion des règlements nouvelle fonctionnalité de couleur
Sur la définition d'un règlement est désormais présent une couleur
Cette nouvelle brique va évoluer vers une catégorisation des paiements par couleur.
Nous pourrons bientôt filtrer par couleur

#############################
V2.0
#############################

# Onglet trésorerie
Vue du compte de résultat annuel par compte.

# Gestion catégories comptables
Le logiciel ne vous bride pas à l'utilisation d'un seul compte. Vous pouvez bien entendu en générer autant que voulu.

# Gestion 4 comptes obligatoires pour trésorerie
Définir dans le fichier preload.php les rowid des 4 comptes correspondant : compte CA, Charge 1 et Charge 2 pour vue synthétisée
Puis Compte Exceptionnel pour bloc final du compte de résultat

# Gestion règlement amélioré
Désormais vous pouvez définir :
- Si votre règlement est une provision
- Sa TVA
- Sa date réel de facture (pour compte de résultat)
- Sa catégorie comptable affiliée
- Son mode de paiement

#############################
V1.0
#############################

# Gestion multi-users 
Vous pouvez générer plusieurs utilisateurs avec mot de passe (encrypté).

# Gestion multi-comptes
Le logiciel ne vous bride pas à l'utilisation d'un seul compte. Vous pouvez bien entendu en générer autant que voulu.

# Gestion multi-règlements
L'objectif principal de cette web-app est de pouvoir visualiser les règlements au jour le jour. Une vue quotidienne de vos règlements et de votre balance vous permettra un meilleur pilotage.


#############################

# Installer la web-app
Vous devez disposer d'un serveur apache et d'une base de données.
Pensez à copier le fichier class/default-spdo.class.php et modifier son contenu pour se connecter à votre base de données.
Il vous est fortement recommandé d'importer le script de base nommé spe.sql situé dans le dossier scripts.
Une fois importé, vous pourrez accéder à votre web-app et vous authentifier grâce au premier compte utilisateur instancié. Celui-ci n'est pas supprimable tout comme le premier compte créé.

# Identifiants
Avec l'import de base, vous disposez d'un compte admin qui utilise le password admin


#############################

# Sources
Cette web-app minimaliste open-source est développé à partir du code de l'ERP Dolibarr.
Si vous désirez un ERP complet et gratuit : http://www.dolibarr.fr

# spe
