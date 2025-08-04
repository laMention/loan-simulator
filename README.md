# loan-simulator
Un simulateur de prêt complet avec calculs d'intérêts simples, composés, mensualités et comparaisons pour wordpress
#Guide d'installation - Plugin Simulateur de Prêt WordPress
## Instructions d'installation
1. Création des fichiers
Créez un nouveau dossier nommé loan-simulator dans le répertoire /wp-content/plugins/ de votre WordPress.
### 2. Fichiers requis
Dans le dossier loan-simulator, créez les fichiers suivants :
#### A. loan-simulator.php (fichier principal)
Copiez le code PHP complet fourni dans l'artifact précédent.
		B. loan-simulator.css (sera créé automatiquement)
			Le CSS sera généré automatiquement lors de l'activation du plugin.
		C. loan-simulator.js (sera créé automatiquement)
			Le JavaScript sera généré automatiquement lors de l'activation du plugin.
	3. Structure finale
		```
		wp-content/
		└── plugins/
	    	└── loan-simulator/
		        ├── loan-simulator.php
		        ├── loan-simulator.css (créé automatiquement)
		        └── loan-simulator.js (créé automatiquement)
		```
	4. Activation du plugin
		1. Connectez-vous à votre administration WordPress
		2. Allez dans Extensions > Extensions installées
		3. Recherchez "Simulateur de Prêt Avancé"
		4. Cliquez sur Activer
	5. Utilisation
		Pour afficher le simulateur sur une page ou un article, utilisez le shortcode :
		```[loan_simulator]```
		Vous pouvez également personnaliser la classe CSS :
		```[loan_simulator class="mon-simulateur-custom"]```

## Couleurs utilisées

			Orange principal : #cd7e02
			Noir : #000
			Blanc : #fff
			Orange dégradé : #ff9500
## Fonctionnalités de sécurité

		Validation NONCE : Toutes les requêtes AJAX sont protégées
		Sanitisation des données : Tous les inputs sont nettoyés
		Validation côté serveur : Double vérification des données
		Protection contre l'accès direct : Empêche l'exécution directe des fichiers

## Responsive
		Le plugin est entièrement responsive et s'adapte à tous les écrans :

		Desktop
		Tablette
		Mobile

## Fonctionnalités
		4 modes de calcul :

		Intérêts Simples

		Capital initial
		Taux d'intérêt annuel
		Durée en années


		Intérêts Composés

		Capital initial
		Taux d'intérêt annuel
		Durée en années
		Fréquence de capitalisation


		Mensualités de Prêt

		Montant du prêt
		Taux d'intérêt annuel
		Durée en années
		Assurance mensuelle (optionnel)


		Comparaison de Prêts

		Comparaison de 2 prêts différents
		Calcul automatique du meilleur choix



## Affichage des résultats

		Cartes résumé : Affichage des valeurs clés
		Tableaux d'échéancier : Détail mois par mois (limité à 60 entrées pour les performances)
		Formatage monétaire : Format français avec symbole €

## Dépannage
		Plugin non visible après activation

		Vérifiez que tous les fichiers sont dans le bon répertoire
		Assurez-vous que les permissions des fichiers sont correctes (644 pour les fichiers, 755 pour les dossiers)

## Erreurs JavaScript

		Vérifiez la console développeur de votre navigateur
		Assurez-vous que jQuery est chargé sur votre site

## Styles non appliqués

		Vérifiez que le fichier CSS a été créé automatiquement
		Testez avec un thème par défaut pour éliminer les conflits

## Problèmes AJAX

		Vérifiez que les URL AJAX sont correctes
		Testez avec les outils de développement du navigateur

## Personnalisation
		Pour personnaliser l'apparence, vous pouvez :

		Modifier les couleurs dans le fichier CSS généré
		Ajouter du CSS custom dans votre thème
		Utiliser les classes CSS existantes comme points d'ancrage

		```
			/* Modifier la couleur principale */
			.loan-simulator-container .ls-header h1 {
			    color: #votre-couleur !important;
			}

			/* Modifier les boutons */
			.loan-simulator-container .ls-calculate-btn {
			    background: linear-gradient(135deg, #votre-couleur1, #votre-couleur2) !important;
			}
		```
## Support
	Pour toute question ou problème :

	Vérifiez les logs d'erreur WordPress
	Testez avec un thème par défaut
	Désactivez temporairement les autres plugins pour identifier les conflits

## Mise à jour
	Pour mettre à jour le plugin :

	Sauvegardez vos fichiers actuels
	Remplacez le contenu des fichiers par les nouvelles versions
	Réactivez le plugin si nécessaire


