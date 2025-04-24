<?php
/*
Plugin Name: WP Glossary Tooltip
Description: Ein Plugin, um ein Glossar zu erstellen und zu verwalten.
Plugin URI: https://github.com/rowphant/WP-Glossary-Tooltip
Version: 1.0.6
Requires at least: 6.8
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires PHP: 7.0
Author: Robert Metzner
*/

// Verhindert den direkten Zugriff auf die Datei
if (!defined('ABSPATH')) {
    exit;
}

// Update Checker
require 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/rowphant/WP-Glossary-Tooltip/',
	__FILE__,
	'wp-glossary-tooltip'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');

//Optional: If you're using a private repository, specify the access token like this:
// $myUpdateChecker->setAuthentication('your-token-here');

// Einbinden der Klassen
require_once plugin_dir_path(__FILE__) . 'includes/class-glossary-post-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-glossary-settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-glossary-meta-box.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-glossary-tooltip.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-glossary-archive-shortcode.php';

// Initialisierung der Klassen
function run_glossary_plugin() {
    $post_type = new Glossary_Post_Type();
    $settings = new Glossary_Settings();
    $meta_box = new Glossary_Meta_Box();
    $tooltip = new Glossary_Tooltip();
    $archive_shortcode = new Glossary_Archive_Shortcode();
}

add_action('plugins_loaded', 'run_glossary_plugin');
