<?php
/*
 * Plugin Name: Spike
 * Description: PHI Intake (3-step form with pain diagram, signature, PDF, CSV export, reCAPTCHA v3).
 * Author: Eric Kowalewski
 * Version: 1.0.0
 * Last Updated: 2025-08-29 17:20 EDT
 */
if (!defined('ABSPATH')) exit;
define('SPIKE_VER','1.0.0');
define('SPIKE_DIR', plugin_dir_path(__FILE__));
define('SPIKE_URL', plugin_dir_url(__FILE__));
if (file_exists(SPIKE_DIR.'vendor/autoload.php')) require SPIKE_DIR.'vendor/autoload.php';
require_once SPIKE_DIR.'includes/cpt.php';
require_once SPIKE_DIR.'includes/settings.php';
require_once SPIKE_DIR.'includes/recaptcha.php';
require_once SPIKE_DIR.'includes/shortcode.php';
require_once SPIKE_DIR.'includes/pdf.php';
require_once SPIKE_DIR.'includes/submit.php';
require_once SPIKE_DIR.'includes/csv-export.php';
register_activation_hook(__FILE__,function(){ spike_register_cpt(); flush_rewrite_rules(); });
register_deactivation_hook(__FILE__,function(){ flush_rewrite_rules(); });
