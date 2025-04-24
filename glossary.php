<?php
/*
Plugin Name: Glossary
Description: Ein Plugin, um ein Glossar zu erstellen und zu verwalten.
Version: 1.0
Author: Ihr Name
*/

// Verhindert den direkten Zugriff auf die Datei
if (!defined('ABSPATH')) {
    exit;
}

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
