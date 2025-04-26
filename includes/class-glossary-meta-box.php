<?php

class Glossary_Meta_Box
{
    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'add_glossary_entry_meta_boxes'));
        add_action('save_post', array($this, 'save_glossary_entry_meta_box'));

        add_filter('redirect_post_location', function ($location, $post_id) {
            if (get_transient('glossary_error_' . $post_id)) {
                $location = add_query_arg('glossary_error', 1, $location);
            }
            return $location;
        }, 10, 2);

        add_action('admin_notices', function () {
            if (isset($_GET['glossary_error']) && isset($_GET['post'])) {
                $post_id = intval($_GET['post']);
                $conflicts = get_transient('glossary_error_' . $post_id);
        
                if ($conflicts && is_array($conflicts)) {
                    echo '<div class="notice notice-error"><p>';
                    echo 'Error: The following related terms already exist in other glossary entries:<ul>';
                    foreach ($conflicts as $term => $source_post_id) {
                        $url = get_edit_post_link($source_post_id);
                        $title = get_the_title($source_post_id);
                        echo '<li><strong>' . esc_html($term) . '</strong> in <a href="' . esc_url($url) . '" target="_blank">' . esc_html($title) . '</a></li>';
                    }
                    echo '</ul></p></div>';
                    delete_transient('glossary_error_' . $post_id);
                }
            }
        });
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
        // Verify nonce, autosave, permissions
        if (!isset($_POST['glossary_entry_meta_box_nonce']) || !wp_verify_nonce($_POST['glossary_entry_meta_box_nonce'], 'save_glossary_entry_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (!isset($_POST['glossary_entry_related_terms'])) {
            return;
        }

        $input_terms_raw = sanitize_text_field($_POST['glossary_entry_related_terms']);
        $input_terms = array_map('trim', explode(',', strtolower($input_terms_raw)));

        // Query all other glossary_entries posts
        $args = array(
            'post_type' => 'glossary_entries',
            'post_status' => 'any',
            'posts_per_page' => -1,
            'post__not_in' => array($post_id),
            'fields' => 'ids',
        );
        $glossary_posts = get_posts($args);

        foreach ($glossary_posts as $other_post_id) {
            $other_terms_raw = get_post_meta($other_post_id, '_glossary_entry_related_terms', true);
            $other_terms = array_map('trim', explode(',', strtolower($other_terms_raw)));

            $conflicts = [];

            foreach ($input_terms as $term) {
                if (in_array($term, $other_terms)) {
                    $conflicts[$term] = $other_post_id;
                }
            }

            if (!empty($conflicts)) {
                set_transient('glossary_error_' . $post_id, $conflicts, 30);
                remove_action('save_post', array($this, 'save_glossary_entry_meta_box'));
                wp_redirect(add_query_arg('glossary_error', 1, get_edit_post_link($post_id, 'url')));
                exit;
            }
        }

        // If no duplicates, save the terms
        update_post_meta($post_id, '_glossary_entry_related_terms', $input_terms_raw);
    }
}
