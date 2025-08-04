<?php
/**
 * Plugin Name: Simulateur de Prêt Avancé
 * Plugin URI: https://example.com
 * Description: Un simulateur de prêt complet avec calculs d'intérêts simples, composés, mensualités et comparaisons. Utilisation: [loan_simulator]
 * Version: 1.0.0
 * Author: Dagou Patrick Elysée Botchi
 * License: GPL v2 or later
 * Text Domain: loan-simulator (simulateur de prêt)
 * Documentation: README.md
 */

// Empêcher l'accès direct
if (!defined('ABSPATH')) {
    exit;
}

class LoanSimulatorPlugin {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_shortcode('loan_simulator', array($this, 'render_simulator'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_calculate_loan', array($this, 'ajax_calculate'));
        add_action('wp_ajax_nopriv_calculate_loan', array($this, 'ajax_calculate'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        load_plugin_textdomain('loan-simulator', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    public function activate() {
        // Code d'activation si nécessaire
    }
    
    public function deactivate() {
        // Code de désactivation si nécessaire
    }
    
    public function enqueue_scripts() {
        wp_enqueue_script('loan-simulator-js', plugin_dir_url(__FILE__) . 'loan-simulator.js', array('jquery'), '1.0.0', true);
        wp_enqueue_style('loan-simulator-css', plugin_dir_url(__FILE__) . 'loan-simulator.css', array(), '1.0.0');
        
        // Localiser le script pour AJAX
        wp_localize_script('loan-simulator-js', 'loanSimulatorAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('loan_simulator_nonce')
        ));
    }
    
    public function render_simulator($atts) {
        $atts = shortcode_atts(array(
            'class' => 'loan-simulator-container'
        ), $atts);
        
        ob_start();
        ?>
        <div class="<?php echo esc_attr($atts['class']); ?>">
            <div class="ls-container">
                <div class="ls-header">
                    <h1>🏦 Simulateur de Prêt</h1>
                    <p>Calculez vos mensualités et visualisez votre échéancier</p>
                </div>

                <div class="ls-tabs">
                    <button class="ls-tab active" data-tab="simple">Intérêts Simples</button>
                    <button class="ls-tab" data-tab="compose">Intérêts Composés</button>
                    <button class="ls-tab" data-tab="mensualite">Mensualités</button>
                    <button class="ls-tab" data-tab="comparaison">Comparaison</button>
                </div>

                <!-- Onglet Intérêts Simples -->
                <div id="ls-simple" class="ls-tab-content active">
                    <h2>Calcul des Intérêts Simples</h2>
                    <form class="ls-form" data-type="simple">
                        <?php wp_nonce_field('loan_simulator_nonce', 'loan_simulator_nonce'); ?>
                        <div class="ls-form-grid">
                            <div class="ls-form-group">
                                <label>Capital initial (€)</label>
                                <input type="number" name="capital" placeholder="10000" step="100" min="0" required>
                            </div>
                            <div class="ls-form-group">
                                <label>Taux d'intérêt annuel (%)</label>
                                <input type="number" name="taux" placeholder="3.5" step="0.1" min="0" max="50" required>
                            </div>
                            <div class="ls-form-group">
                                <label>Durée (années)</label>
                                <input type="number" name="duree" placeholder="5" step="1" min="1" max="50" required>
                            </div>
                        </div>
                        <button type="submit" class="ls-calculate-btn">Calculer les Intérêts Simples</button>
                    </form>
                    <div class="ls-results" id="ls-resultat-simple"></div>
                </div>

                <!-- Onglet Intérêts Composés -->
                <div id="ls-compose" class="ls-tab-content">
                    <h2>Calcul des Intérêts Composés</h2>
                    <form class="ls-form" data-type="compose">
                        <?php wp_nonce_field('loan_simulator_nonce', 'loan_simulator_nonce'); ?>
                        <div class="ls-form-grid">
                            <div class="ls-form-group">
                                <label>Capital initial (€)</label>
                                <input type="number" name="capital" placeholder="10000" step="100" min="0" required>
                            </div>
                            <div class="ls-form-group">
                                <label>Taux d'intérêt annuel (%)</label>
                                <input type="number" name="taux" placeholder="3.5" step="0.1" min="0" max="50" required>
                            </div>
                            <div class="ls-form-group">
                                <label>Durée (années)</label>
                                <input type="number" name="duree" placeholder="5" step="1" min="1" max="50" required>
                            </div>
                            <div class="ls-form-group">
                                <label>Fréquence de capitalisation</label>
                                <select name="frequence">
                                    <option value="1">Annuelle</option>
                                    <option value="2">Semestrielle</option>
                                    <option value="4">Trimestrielle</option>
                                    <option value="12" selected>Mensuelle</option>
                                    <option value="365">Quotidienne</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="ls-calculate-btn">Calculer les Intérêts Composés</button>
                    </form>
                    <div class="ls-results" id="ls-resultat-compose"></div>
                </div>

                <!-- Onglet Mensualités -->
                <div id="ls-mensualite" class="ls-tab-content">
                    <h2>Calcul des Mensualités de Prêt</h2>
                    <form class="ls-form" data-type="mensualite">
                        <?php wp_nonce_field('loan_simulator_nonce', 'loan_simulator_nonce'); ?>
                        <div class="ls-form-grid">
                            <div class="ls-form-group">
                                <label>Montant du prêt (€)</label>
                                <input type="number" name="montant" placeholder="200000" step="1000" min="0" required>
                            </div>
                            <div class="ls-form-group">
                                <label>Taux d'intérêt annuel (%)</label>
                                <input type="number" name="taux" placeholder="2.5" step="0.01" min="0" max="20" required>
                            </div>
                            <div class="ls-form-group">
                                <label>Durée (années)</label>
                                <input type="number" name="duree" placeholder="20" step="1" min="1" max="50" required>
                            </div>
                            <div class="ls-form-group">
                                <label>Assurance mensuelle (€) - optionnel</label>
                                <input type="number" name="assurance" placeholder="50" step="10" min="0">
                            </div>
                        </div>
                        <button type="submit" class="ls-calculate-btn">Calculer les Mensualités</button>
                    </form>
                    <div class="ls-results" id="ls-resultat-mensualite"></div>
                </div>

                <!-- Onglet Comparaison -->
                <div id="ls-comparaison" class="ls-tab-content">
                    <h2>Comparaison de Prêts</h2>
                    <form class="ls-form" data-type="comparaison">
                        <?php wp_nonce_field('loan_simulator_nonce', 'loan_simulator_nonce'); ?>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
                            <div>
                                <h3>Prêt A</h3>
                                <div class="ls-form-group">
                                    <label>Montant (€)</label>
                                    <input type="number" name="montant_a" placeholder="200000" required>
                                </div>
                                <div class="ls-form-group">
                                    <label>Taux (%)</label>
                                    <input type="number" name="taux_a" placeholder="2.5" step="0.01" required>
                                </div>
                                <div class="ls-form-group">
                                    <label>Durée (années)</label>
                                    <input type="number" name="duree_a" placeholder="20" required>
                                </div>
                            </div>
                            <div>
                                <h3>Prêt B</h3>
                                <div class="ls-form-group">
                                    <label>Montant (€)</label>
                                    <input type="number" name="montant_b" placeholder="200000" required>
                                </div>
                                <div class="ls-form-group">
                                    <label>Taux (%)</label>
                                    <input type="number" name="taux_b" placeholder="3.0" step="0.01" required>
                                </div>
                                <div class="ls-form-group">
                                    <label>Durée (années)</label>
                                    <input type="number" name="duree_b" placeholder="25" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="ls-calculate-btn">Comparer les Prêts</button>
                    </form>
                    <div class="ls-results" id="ls-resultat-comparaison"></div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public function ajax_calculate() {
        // Vérification de sécurité
        if (!wp_verify_nonce($_POST['nonce'], 'loan_simulator_nonce')) {
            wp_die('Erreur de sécurité');
        }
        
        $type = sanitize_text_field($_POST['type']);
        $data = array();
        
        // Sanitiser les données d'entrée
        foreach ($_POST as $key => $value) {
            if ($key !== 'action' && $key !== 'nonce' && $key !== 'type') {
                $data[$key] = floatval($value);
            }
        }
        
        $result = array();
        
        switch ($type) {
            case 'simple':
                $result = $this->calculate_simple_interest($data);
                break;
            case 'compose':
                $result = $this->calculate_compound_interest($data);
                break;
            case 'mensualite':
                $result = $this->calculate_monthly_payment($data);
                break;
            case 'comparaison':
                $result = $this->compare_loans($data);
                break;
        }
        
        wp_send_json_success($result);
    }
    
    private function calculate_simple_interest($data) {
        $capital = $data['capital'];
        $taux = $data['taux'] / 100;
        $duree = $data['duree'];
        
        $taux_mensuel = $taux / 12;
        $duree_mois = $duree * 12;
        $interet_mensuel = $capital * $taux_mensuel;
        $interet_total = $interet_mensuel * $duree_mois;
        $capital_final = $capital + $interet_total;
        
        $echeancier = array();
        $capital_accumule = $capital;
        
        for ($mois = 1; $mois <= min($duree_mois, 60); $mois++) {
            $capital_accumule += $interet_mensuel;
            $echeancier[] = array(
                'mois' => $mois,
                'capital_initial' => $capital,
                'interet_mois' => $interet_mensuel,
                'capital_accumule' => $capital_accumule
            );
        }
        
        return array(
            'capital_initial' => $capital,
            'interet_total' => $interet_total,
            'capital_final' => $capital_final,
            'interet_mensuel' => $interet_mensuel,
            'echeancier' => $echeancier,
            'duree_totale' => $duree_mois
        );
    }
    
    private function calculate_compound_interest($data) {
        $capital = $data['capital'];
        $taux = $data['taux'] / 100;
        $duree = $data['duree'];
        $frequence = $data['frequence'];
        
        $capital_final = $capital * pow(1 + $taux / $frequence, $frequence * $duree);
        $interet_total = $capital_final - $capital;
        
        $taux_mensuel = $taux / 12;
        $duree_mois = $duree * 12;
        
        $echeancier = array();
        $capital_accumule = $capital;
        
        for ($mois = 1; $mois <= min($duree_mois, 60); $mois++) {
            $capital_debut = $capital_accumule;
            $interet_mois = $capital_debut * $taux_mensuel;
            $capital_accumule = $capital_debut + $interet_mois;
            
            $echeancier[] = array(
                'mois' => $mois,
                'capital_debut' => $capital_debut,
                'interet_mois' => $interet_mois,
                'capital_fin' => $capital_accumule
            );
        }
        
        return array(
            'capital_initial' => $capital,
            'interet_total' => $interet_total,
            'capital_final' => $capital_final,
            'rendement' => (($capital_final / $capital - 1) * 100),
            'echeancier' => $echeancier,
            'duree_totale' => $duree_mois
        );
    }
    
    private function calculate_monthly_payment($data) {
        $montant = $data['montant'];
        $taux = $data['taux'] / 100;
        $duree = $data['duree'];
        $assurance = isset($data['assurance']) ? $data['assurance'] : 0;
        
        $taux_mensuel = $taux / 12;
        $nombre_mois = $duree * 12;
        
        $mensualite = $montant * ($taux_mensuel * pow(1 + $taux_mensuel, $nombre_mois)) / (pow(1 + $taux_mensuel, $nombre_mois) - 1);
        $mensualite_totale = $mensualite + $assurance;
        $cout_total = $mensualite_totale * $nombre_mois;
        $interet_total = $cout_total - $montant - ($assurance * $nombre_mois);
        
        $echeancier = array();
        $capital_restant = $montant;
        
        for ($mois = 1; $mois <= min($nombre_mois, 60); $mois++) {
            $interet_mois = $capital_restant * $taux_mensuel;
            $capital_rembourse = $mensualite - $interet_mois;
            $capital_restant -= $capital_rembourse;
            
            $echeancier[] = array(
                'mois' => $mois,
                'mensualite' => $mensualite,
                'interet' => $interet_mois,
                'capital' => $capital_rembourse,
                'capital_restant' => max(0, $capital_restant)
            );
        }
        
        return array(
            'mensualite' => $mensualite,
            'mensualite_totale' => $mensualite_totale,
            'cout_total' => $cout_total,
            'interet_total' => $interet_total,
            'echeancier' => $echeancier,
            'duree_totale' => $nombre_mois
        );
    }
    
    private function compare_loans($data) {
        $montant_a = $data['montant_a'];
        $taux_a = $data['taux_a'] / 100;
        $duree_a = $data['duree_a'];
        
        $montant_b = $data['montant_b'];
        $taux_b = $data['taux_b'] / 100;
        $duree_b = $data['duree_b'];
        
        $mensualite_a = $this->calculate_mensualite($montant_a, $taux_a, $duree_a);
        $cout_total_a = $mensualite_a * $duree_a * 12;
        $interet_total_a = $cout_total_a - $montant_a;
        
        $mensualite_b = $this->calculate_mensualite($montant_b, $taux_b, $duree_b);
        $cout_total_b = $mensualite_b * $duree_b * 12;
        $interet_total_b = $cout_total_b - $montant_b;
        
        $economie = abs($cout_total_a - $cout_total_b);
        $meilleur_pret = $cout_total_a < $cout_total_b ? 'A' : 'B';
        
        return array(
            'pret_a' => array(
                'mensualite' => $mensualite_a,
                'cout_total' => $cout_total_a,
                'interet_total' => $interet_total_a
            ),
            'pret_b' => array(
                'mensualite' => $mensualite_b,
                'cout_total' => $cout_total_b,
                'interet_total' => $interet_total_b
            ),
            'economie' => $economie,
            'meilleur_pret' => $meilleur_pret,
            'difference_mensualite' => abs($mensualite_a - $mensualite_b)
        );
    }
    
    private function calculate_mensualite($montant, $taux, $duree) {
        $taux_mensuel = $taux / 12;
        $nombre_mois = $duree * 12;
        return $montant * ($taux_mensuel * pow(1 + $taux_mensuel, $nombre_mois)) / (pow(1 + $taux_mensuel, $nombre_mois) - 1);
    }
}

// Initialiser le plugin
new LoanSimulatorPlugin();

// Créer les fichiers CSS et JS lors de l'activation
register_activation_hook(__FILE__, 'create_loan_simulator_assets');

function create_loan_simulator_assets() {
    $plugin_dir = plugin_dir_path(__FILE__);
    
    // Créer le fichier CSS
    $css_content = '
        /* Simulateur de Prêt - Styles */
        .loan-simulator-container {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .ls-container {
            width: 100%;
        }

        .ls-header {
            background: linear-gradient(135deg, #000, #333);
            color: #fff;
            text-align: center;
            padding: 30px;
        }

        .ls-header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            color: #cd7e02;
        }

        .ls-header p {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .ls-tabs {
            display: flex;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            flex-wrap: wrap;
        }

        .ls-tab {
            flex: 1;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            background: none;
            font-size: 16px;
            font-weight: 500;
            color: #000;
            min-width: 150px;
        }

        .ls-tab.active {
            background: #cd7e02;
            color: #fff;
            border-bottom: 3px solid #cd7e02;
        }

        .ls-tab:hover:not(.active) {
            background: #e9ecef;
        }

        .ls-tab-content {
            display: none;
            padding: 30px;
        }

        .ls-tab-content.active {
            display: block;
        }

        .ls-form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }

        .ls-form-group {
            display: flex;
            flex-direction: column;
        }

        .ls-form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #000;
        }

        .ls-form-group input, 
        .ls-form-group select {
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s ease;
            background: #fff;
            color: #000;
        }

        .ls-form-group input:focus, 
        .ls-form-group select:focus {
            outline: none;
            border-color: #cd7e02;
            box-shadow: 0 0 0 3px rgba(205, 126, 2, 0.1);
        }

        .ls-calculate-btn {
            background: linear-gradient(135deg, #cd7e02, #ff9500);
            color: #fff;
            border: none;
            padding: 15px 30px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.3s ease;
            width: 100%;
            margin-bottom: 20px;
        }

        .ls-calculate-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(205, 126, 2, 0.3);
        }

        .ls-results {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-top: 20px;
        }

        .ls-summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .ls-summary-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border: 2px solid #e9ecef;
        }

        .ls-summary-card h4 {
            color: #cd7e02;
            margin-bottom: 10px;
            font-size: 14px;
            text-transform: uppercase;
        }

        .ls-summary-card .ls-value {
            font-size: 24px;
            font-weight: bold;
            color: #000;
        }

        .ls-echeancier {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .ls-echeancier th {
            background: linear-gradient(135deg, #cd7e02, #ff9500);
            color: #fff;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        .ls-echeancier td {
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
            color: #000;
        }

        .ls-echeancier tr:nth-child(even) {
            background: #f8f9fa;
        }

        .ls-echeancier tr:hover {
            background: #fff3e0;
        }

        .ls-error {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
            padding: 10px;
            background: #ffeaea;
            border: 1px solid #e74c3c;
            border-radius: 5px;
        }

        .ls-loading {
            text-align: center;
            padding: 20px;
            color: #cd7e02;
        }

        .ls-toggle-btn {
            background: #cd7e02;
            color: #fff;
            border: none;
            padding: 10px 20px;
            margin: 5px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .ls-toggle-btn:hover {
            background: #ff9500;
        }

        .ls-toggle-btn.inactive {
            background: #95a5a6;
        }

        @media (max-width: 768px) {
            .ls-tabs {
                flex-direction: column;
            }
            
            .ls-form-grid {
                grid-template-columns: 1fr;
            }
            
            .ls-summary-cards {
                grid-template-columns: 1fr;
            }
            
            .ls-tab {
                min-width: auto;
            }
        }

        @media (max-width: 480px) {
            .loan-simulator-container {
                margin: 10px;
                border-radius: 10px;
            }
            
            .ls-header {
                padding: 20px;
            }
            
            .ls-header h1 {
                font-size: 2em;
            }
            
            .ls-tab-content {
                padding: 20px;
            }
        }
    ';
    
    file_put_contents($plugin_dir . 'loan-simulator.css', $css_content);
    
    // Créer le fichier JavaScript
    $js_content = '
        jQuery(document).ready(function($) {
            // Gestion des onglets
            $(".ls-tab").click(function() {
                var tab = $(this).data("tab");
                
                $(".ls-tab").removeClass("active");
                $(".ls-tab-content").removeClass("active");
                
                $(this).addClass("active");
                $("#ls-" + tab).addClass("active");
            });
            
            // Gestion des formulaires
            $(".ls-form").submit(function(e) {
                e.preventDefault();
                
                var form = $(this);
                var type = form.data("type");
                var resultDiv = $("#ls-resultat-" + type);
                
                // Afficher le chargement
                resultDiv.html("<div class=\"ls-loading\">Calcul en cours...</div>");
                
                // Collecter les données du formulaire
                var formData = {
                    action: "calculate_loan",
                    type: type,
                    nonce: loanSimulatorAjax.nonce
                };
                
                form.find("input, select").each(function() {
                    if ($(this).attr("name") && $(this).attr("name") !== "loan_simulator_nonce") {
                        formData[$(this).attr("name")] = $(this).val();
                    }
                });
                
                // Validation côté client
                var isValid = true;
                form.find("input[required]").each(function() {
                    if (!$(this).val()) {
                        isValid = false;
                        $(this).css("border-color", "#e74c3c");
                    } else {
                        $(this).css("border-color", "#e9ecef");
                    }
                });
                
                if (!isValid) {
                    resultDiv.html("<div class=\"ls-error\">Veuillez remplir tous les champs obligatoires.</div>");
                    return;
                }
                
                // Envoyer la requête AJAX
                $.ajax({
                    url: loanSimulatorAjax.ajax_url,
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            displayResults(type, response.data, resultDiv);
                        } else {
                            resultDiv.html("<div class=\"ls-error\">Erreur lors du calcul.</div>");
                        }
                    },
                    error: function() {
                        resultDiv.html("<div class=\"ls-error\">Erreur de connexion.</div>");
                    }
                });
            });
            
            // Fonction pour afficher les résultats
            function displayResults(type, data, container) {
                var html = "";
                
                switch (type) {
                    case "simple":
                        html = generateSimpleInterestResults(data);
                        break;
                    case "compose":
                        html = generateCompoundInterestResults(data);
                        break;
                    case "mensualite":
                        html = generateMonthlyPaymentResults(data);
                        break;
                    case "comparaison":
                        html = generateComparisonResults(data);
                        break;
                }
                
                container.html(html);
            }
            
            // Fonction pour formater les montants
            function formatMoney(amount) {
                return new Intl.NumberFormat("fr-FR", {
                    style: "currency",
                    currency: "EUR"
                }).format(amount);
            }
            
            // Résultats intérêts simples
            function generateSimpleInterestResults(data) {
                var echeancierRows = "";
                data.echeancier.forEach(function(row) {
                    echeancierRows += "<tr>" +
                        "<td>" + row.mois + "</td>" +
                        "<td>" + formatMoney(row.capital_initial) + "</td>" +
                        "<td>" + formatMoney(row.interet_mois) + "</td>" +
                        "<td>" + formatMoney(row.capital_accumule) + "</td>" +
                        "</tr>";
                });
                
                if (data.duree_totale > 60) {
                    echeancierRows += "<tr><td colspan=\"4\" style=\"text-align: center; font-style: italic;\">... et " + (data.duree_totale - 60) + " autres mois</td></tr>";
                }
                
                return "<div class=\"ls-summary-cards\">" +
                    "<div class=\"ls-summary-card\">" +
                        "<h4>Capital Initial</h4>" +
                        "<div class=\"ls-value\">" + formatMoney(data.capital_initial) + "</div>" +
                    "</div>" +
                    "<div class=\"ls-summary-card\">" +
                        "<h4>Intérêts Totaux</h4>" +
                        "<div class=\"ls-value\">" + formatMoney(data.interet_total) + "</div>" +
                    "</div>" +
                    "<div class=\"ls-summary-card\">" +
                        "<h4>Capital Final</h4>" +
                        "<div class=\"ls-value\">" + formatMoney(data.capital_final) + "</div>" +
                    "</div>" +
                    "<div class=\"ls-summary-card\">" +
                        "<h4>Intérêt Mensuel</h4>" +
                        "<div class=\"ls-value\">" + formatMoney(data.interet_mensuel) + "</div>" +
                    "</div>" +
                "</div>" +
                "<table class=\"ls-echeancier\">" +
                    "<thead><tr>" +
                        "<th>Mois</th>" +
                        "<th>Capital Initial</th>" +
                        "<th>Intérêt du Mois</th>" +
                        "<th>Capital Accumulé</th>" +
                    "</tr></thead>" +
                    "<tbody>" + echeancierRows + "</tbody>" +
                "</table>";
            }
            
            // Résultats intérêts composés
            function generateCompoundInterestResults(data) {
                var echeancierRows = "";
                data.echeancier.forEach(function(row) {
                    echeancierRows += "<tr>" +
                        "<td>Mois " + row.mois + "</td>" +
                        "<td>" + formatMoney(row.capital_debut) + "</td>" +
                        "<td>" + formatMoney(row.interet_mois) + "</td>" +
                        "<td>" + formatMoney(row.capital_fin) + "</td>" +
                        "</tr>";
                });
                
                if (data.duree_totale > 60) {
                    echeancierRows += "<tr><td colspan=\"4\" style=\"text-align: center; font-style: italic;\">... et " + (data.duree_totale - 60) + " autres mois jusqu\'au capital final de " + formatMoney(data.capital_final) + "</td></tr>";
                }
                
                return "<div class=\"ls-summary-cards\">" +
                    "<div class=\"ls-summary-card\">" +
                        "<h4>Capital Initial</h4>" +
                        "<div class=\"ls-value\">" + formatMoney(data.capital_initial) + "</div>" +
                    "</div>" +
                    "<div class=\"ls-summary-card\">" +
                        "<h4>Intérêts Totaux</h4>" +
                        "<div class=\"ls-value\">" + formatMoney(data.interet_total) + "</div>" +
                    "</div>" +
                    "<div class=\"ls-summary-card\">" +
                        "<h4>Capital Final</h4>" +
                        "<div class=\"ls-value\">" + formatMoney(data.capital_final) + "</div>" +
                    "</div>" +
                    "<div class=\"ls-summary-card\">" +
                        "<h4>Rendement (%)</h4>" +
                        "<div class=\"ls-value\">" + data.rendement.toFixed(2) + "%</div>" +
                    "</div>" +
                "</div>" +
                "<table class=\"ls-echeancier\">" +
                    "<thead><tr>" +
                        "<th>Période</th>" +
                        "<th>Capital Début</th>" +
                        "<th>Intérêts Gagnés</th>" +
                        "<th>Capital Fin</th>" +
                    "</tr></thead>" +
                    "<tbody>" + echeancierRows + "</tbody>" +
                "</table>";
            }
            
            // Résultats mensualités
            function generateMonthlyPaymentResults(data) {
                var echeancierRows = "";
                data.echeancier.forEach(function(row) {
                    echeancierRows += "<tr>" +
                        "<td>" + row.mois + "</td>" +
                        "<td>" + formatMoney(row.mensualite) + "</td>" +
                        "<td>" + formatMoney(row.interet) + "</td>" +
                        "<td>" + formatMoney(row.capital) + "</td>" +
                        "<td>" + formatMoney(row.capital_restant) + "</td>" +
                        "</tr>";
                });
                
                if (data.duree_totale > 60) {
                    echeancierRows += "<tr><td colspan=\"5\" style=\"text-align: center; font-style: italic;\">... et " + (data.duree_totale - 60) + " autres mensualités</td></tr>";
                }
                
                return "<div class=\"ls-summary-cards\">" +
                    "<div class=\"ls-summary-card\">" +
                        "<h4>Mensualité (hors assurance)</h4>" +
                        "<div class=\"ls-value\">" + formatMoney(data.mensualite) + "</div>" +
                    "</div>" +
                    "<div class=\"ls-summary-card\">" +
                        "<h4>Mensualité Totale</h4>" +
                        "<div class=\"ls-value\">" + formatMoney(data.mensualite_totale) + "</div>" +
                    "</div>" +
                    "<div class=\"ls-summary-card\">" +
                        "<h4>Coût Total</h4>" +
                        "<div class=\"ls-value\">" + formatMoney(data.cout_total) + "</div>" +
                    "</div>" +
                    "<div class=\"ls-summary-card\">" +
                        "<h4>Intérêts Totaux</h4>" +
                        "<div class=\"ls-value\">" + formatMoney(data.interet_total) + "</div>" +
                    "</div>" +
                "</div>" +
                "<table class=\"ls-echeancier\">" +
                    "<thead><tr>" +
                        "<th>Mois</th>" +
                        "<th>Mensualité</th>" +
                        "<th>Intérêts</th>" +
                        "<th>Capital</th>" +
                        "<th>Capital Restant</th>" +
                    "</tr></thead>" +
                    "<tbody>" + echeancierRows + "</tbody>" +
                "</table>";
            }
            
            // Résultats comparaison
            function generateComparisonResults(data) {
                var winnerA = data.meilleur_pret === "A";
                var winnerB = data.meilleur_pret === "B";
                
                return "<div style=\"display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;\">" +
                    "<div class=\"ls-summary-card\" style=\"border: " + (winnerA ? "3px solid #27ae60" : "2px solid #e9ecef") + ";\">" +
                        "<h3>Prêt A " + (winnerA ? "🏆" : "") + "</h3>" +
                        "<p><strong>Mensualité:</strong> " + formatMoney(data.pret_a.mensualite) + "</p>" +
                        "<p><strong>Coût total:</strong> " + formatMoney(data.pret_a.cout_total) + "</p>" +
                        "<p><strong>Intérêts:</strong> " + formatMoney(data.pret_a.interet_total) + "</p>" +
                    "</div>" +
                    "<div class=\"ls-summary-card\" style=\"border: " + (winnerB ? "3px solid #27ae60" : "2px solid #e9ecef") + ";\">" +
                        "<h3>Prêt B " + (winnerB ? "🏆" : "") + "</h3>" +
                        "<p><strong>Mensualité:</strong> " + formatMoney(data.pret_b.mensualite) + "</p>" +
                        "<p><strong>Coût total:</strong> " + formatMoney(data.pret_b.cout_total) + "</p>" +
                        "<p><strong>Intérêts:</strong> " + formatMoney(data.pret_b.interet_total) + "</p>" +
                    "</div>" +
                "</div>" +
                "<div class=\"ls-summary-card\">" +
                    "<h3>💰 Économie avec le Prêt " + data.meilleur_pret + "</h3>" +
                    "<div class=\"ls-value\" style=\"color: #27ae60; font-size: 32px;\">" + formatMoney(data.economie) + "</div>" +
                    "<p>Différence de mensualité: " + formatMoney(data.difference_mensualite) + "</p>" +
                "</div>";
            }
            
            // Validation des champs numériques
            $("input[type=\"number\"]").on("input", function() {
                if ($(this).val() < 0) {
                    $(this).val(0);
                }
            });
            
            // Gestion des erreurs de formulaire
            $("input[required]").on("blur", function() {
                if (!$(this).val()) {
                    $(this).css("border-color", "#e74c3c");
                } else {
                    $(this).css("border-color", "#e9ecef");
                }
            });
        });
    ';
    
    file_put_contents($plugin_dir . 'loan-simulator.js', $js_content);
    }
?>