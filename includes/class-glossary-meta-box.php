<?php

class Glossary_Meta_Box
{
    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'add_glossary_entry_meta_boxes'));
        add_action('save_post', array($this, 'save_glossary_entry_meta_box'));
    }

    public function add_glossary_entry_meta_boxes()
    {
        add_meta_box(
            'glossary_entry_meta_box',
            'Glossary Entry Details',
            array($this, 'render_glossary_entry_meta_box'),
            'glossary_entries',
            'normal',
            'high'
        );
    }

    public function render_glossary_entry_meta_box($post)
    {
        $terms = get_post_meta($post->ID, '_glossary_entry_related_terms', true);

        wp_nonce_field('save_glossary_entry_meta_box', 'glossary_entry_meta_box_nonce');

        echo '<label for="glossary_entry_related_terms">Related terms ';
        echo '<input type="text" id="glossary_entry_related_terms" name="glossary_entry_related_terms" value="' . esc_attr($terms) . '" size="50" />';
        echo '<div><i>(separated by comma)</i></div>';
    }

    public function save_glossary_entry_meta_box($post_id)
    {
        if (!isset($_POST['glossary_entry_meta_box_nonce']) || !wp_verify_nonce($_POST['glossary_entry_meta_box_nonce'], 'save_glossary_entry_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['glossary_entry_related_terms'])) {
            update_post_meta($post_id, '_glossary_entry_related_terms', sanitize_text_field($_POST['glossary_entry_related_terms']));
        }
    }
}
