# loan-simulator
Un simulateur de prêt complet avec calculs d'intérêts simples, composés, mensualités et comparaisons pour wordpress
#Guide d'installation - Plugin Simulateur de Prêt WordPress
## Instructions d'installation
1. Création des fichiers
Créez un nouveau dossier nommé loan-simulator dans le répertoire /wp-content/plugins/ de votre WordPress.
### 2. Fichiers requis
Dans le dossier loan-simulator, créez les fichiers suivants :
A. loan-simulator.php (fichier principal)
Copiez le code PHP complet fourni dans l'artifact précédent.
B. loan-simulator.css (sera créé automatiquement)
Le CSS sera généré automatiquement lors de l'activation du plugin.
C. loan-simulator.js (sera créé automatiquement)
Le JavaScript sera généré automatiquement lors de l'activation du plugin.
### 3. Structure finale
```
		wp-content/
		└── plugins/
	    	└── loan-simulator/
		        ├── loan-simulator.php
		        ├── loan-simulator.css (créé automatiquement)
		        └── loan-simulator.js (créé automatiquement)
```

### 4. Activation du plugin
1. Connectez-vous à votre administration WordPress
2. Allez dans Extensions > Extensions installées
3. Recherchez "Simulateur de Prêt Avancé"
4. Cliquez sur Activer
### 5. Utilisation
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

#### Validation NONCE : Toutes les requêtes AJAX sont protégées
#### Sanitisation des données : Tous les inputs sont nettoyés
#### Validation côté serveur : Double vérification des données
#### Protection contre l'accès direct : Empêche l'exécution directe des fichiers

## Responsive
Le plugin est entièrement responsive et s'adapte à tous les écrans :

Desktop
Tablette
Mobile

## Fonctionnalités
4 modes de calcul :

### Intérêts Simples

Capital initial
Taux d'intérêt annuel
Durée en années


### Intérêts Composés

Capital initial
Taux d'intérêt annuel
Durée en années
Fréquence de capitalisation


### Mensualités de Prêt

Montant du prêt
Taux d'intérêt annuel
Durée en années
Assurance mensuelle (optionnel)


### Comparaison de Prêts

Comparaison de 2 prêts différents
Calcul automatique du meilleur choix



## Affichage des résultats

Cartes résumé : Affichage des valeurs clés
Tableaux d'échéancier : Détail mois par mois (limité à 60 entrées pour les performances)
Formatage monétaire : Format français avec symbole €

## Dépannage
#### Plugin non visible après activation

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
### Pour personnaliser l'apparence, vous pouvez :

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

# Plugin WordPress - Simulateur de Prêt Avancé

## 🔧 Documentation Développeur

### Architecture du Plugin

```
LoanSimulatorPlugin/
├── Méthodes principales
│   ├── __construct()          // Initialisation
│   ├── init()                 // Chargement des traductions
│   ├── enqueue_scripts()      // Chargement CSS/JS
│   ├── render_simulator()     // Rendu du shortcode
│   └── ajax_calculate()       // Traitement AJAX
├── Méthodes de calcul
│   ├── calculate_simple_interest()
│   ├── calculate_compound_interest()
│   ├── calculate_monthly_payment()
│   ├── compare_loans()
│   └── calculate_mensualite()
└── Hooks WordPress
    ├── Actions
    └── Filtres
```

### Hooks et Filtres disponibles

#### Actions
```php
// Avant le rendu du simulateur
do_action('loan_simulator_before_render', $atts);

// Après le rendu du simulateur
do_action('loan_simulator_after_render', $atts);

// Avant le calcul AJAX
do_action('loan_simulator_before_calculate', $type, $data);

// Après le calcul AJAX
do_action('loan_simulator_after_calculate', $type, $data, $result);
```

#### Filtres
```php
// Modifier les attributs par défaut du shortcode
apply_filters('loan_simulator_default_atts', $default_atts);

// Modifier les données avant calcul
apply_filters('loan_simulator_sanitize_data', $data, $type);

// Modifier les résultats avant envoi
apply_filters('loan_simulator_calculate_result', $result, $type, $data);

// Modifier le HTML de rendu
apply_filters('loan_simulator_html_output', $html, $atts);
```

### API des méthodes de calcul

#### 1. Intérêts Simples
```php
$data = array(
    'capital' => 10000,  // Capital initial en €
    'taux' => 3.5,       // Taux annuel en %
    'duree' => 5         // Durée en années
);

$result = $this->calculate_simple_interest($data);
/*
Retourne:
array(
    'capital_initial' => float,
    'interet_total' => float,
    'capital_final' => float,
    'interet_mensuel' => float,
    'echeancier' => array(),
    'duree_totale' => int
)
*/
```

#### 2. Intérêts Composés
```php
$data = array(
    'capital' => 10000,   // Capital initial en €
    'taux' => 3.5,        // Taux annuel en %
    'duree' => 5,         // Durée en années
    'frequence' => 12     // Fréquence de capitalisation
);

$result = $this->calculate_compound_interest($data);
/*
Retourne:
array(
    'capital_initial' => float,
    'interet_total' => float,
    'capital_final' => float,
    'rendement' => float,
    'echeancier' => array(),
    'duree_totale' => int
)
*/
```

#### 3. Mensualités
```php
$data = array(
    'montant' => 200000,  // Montant du prêt en €
    'taux' => 2.5,        // Taux annuel en %
    'duree' => 20,        // Durée en années
    'assurance' => 50     // Assurance mensuelle (optionnel)
);

$result = $this->calculate_monthly_payment($data);
/*
Retourne:
array(
    'mensualite' => float,
    'mensualite_totale' => float,
    'cout_total' => float,
    'interet_total' => float,
    'echeancier' => array(),
    'duree_totale' => int
)
*/
```

#### 4. Comparaison
```php
$data = array(
    'montant_a' => 200000, 'taux_a' => 2.5, 'duree_a' => 20,
    'montant_b' => 200000, 'taux_b' => 3.0, 'duree_b' => 25
);

$result = $this->compare_loans($data);
/*
Retourne:
array(
    'pret_a' => array(...),
    'pret_b' => array(...),
    'economie' => float,
    'meilleur_pret' => string,
    'difference_mensualite' => float
)
*/
```

### Sécurité

#### Validation des données
```php
// Toutes les données sont sanitisées via floatval()
foreach ($_POST as $key => $value) {
    if ($key !== 'action' && $key !== 'nonce' && $key !== 'type') {
        $data[$key] = floatval($value);
    }
}
```

#### Protection NONCE
```php
// Vérification côté serveur
if (!wp_verify_nonce($_POST['nonce'], 'loan_simulator_nonce')) {
    wp_die('Erreur de sécurité');
}

// Génération côté client
wp_nonce_field('loan_simulator_nonce', 'loan_simulator_nonce');
```

### Extension du plugin

#### Ajouter un nouveau type de calcul

1. **Étendre la méthode AJAX**
```php
add_action('loan_simulator_before_calculate', function($type, $data) {
    if ($type === 'mon_nouveau_calcul') {
        // Logique personnalisée
    }
});
```

2. **Ajouter un onglet personnalisé**
```php
add_filter('loan_simulator_html_output', function($html, $atts) {
    // Ajouter votre onglet personnalisé
    return $html;
}, 10, 2);
```

#### Personnaliser les styles
```php
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'loan-simulator-custom', 
        get_template_directory_uri() . '/css/loan-simulator-custom.css',
        array('loan-simulator-css'),
        '1.0.0'
    );
});
```

### Base de données

Le plugin n'utilise pas de tables personnalisées mais peut être étendu :

```php
// Hook d'activation pour créer des tables
register_activation_hook(__FILE__, function() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'loan_calculations';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) DEFAULT NULL,
        calculation_type varchar(50) NOT NULL,
        calculation_data text NOT NULL,
        calculation_result text NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
});
```

### Tests unitaires

#### Structure de test recommandée
```php
class LoanSimulatorTest extends WP_UnitTestCase {
    
    private $plugin;
    
    public function setUp() {
        parent::setUp();
        $this->plugin = new LoanSimulatorPlugin();
    }
    
    public function test_simple_interest_calculation() {
        $data = array(
            'capital' => 10000,
            'taux' => 5,
            'duree' => 2
        );
        
        $result = $this->plugin->calculate_simple_interest($data);
        
        $this->assertEquals(11000, $result['capital_final']);
    }
    
    // Autres tests...
}
```

### Performance

#### Optimisations implémentées
- Limitation de l'affichage des échéanciers à 60 entrées
- Mise en cache côté client des résultats
- Validation côté client avant envoi AJAX
- Sanitisation optimisée des données

#### Métriques recommandées
- Temps de calcul : < 100ms
- Taille des réponses AJAX : < 50KB
- Temps de rendu initial : < 200ms

### Internationalisation

```php
// Chaînes traduisibles
__('Simulateur de Prêt', 'loan-simulator');
_e('Calculez vos mensualités', 'loan-simulator');
_n('mois', 'mois', $count, 'loan-simulator');
```

#### Fichiers de traduction
```
/languages/
├── loan-simulator.pot
├── loan-simulator-fr_FR.po
├── loan-simulator-fr_FR.mo
├── loan-simulator-en_US.po
└── loan-simulator-en_US.mo
```

### Débogage

#### Mode debug
```php
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_log('Loan Simulator Debug: ' . print_r($data, true));
}
```

#### Logs personnalisés
```php
add_action('loan_simulator_error', function($error, $context) {
    error_log(sprintf(
        'Loan Simulator Error: %s | Context: %s',
        $error,
        json_encode($context)
    ));
});
```


