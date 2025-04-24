<?php

class Glossary_Tooltip
{
    private $tooltip_ids = array();

    /**
     * Registers the filter to add tooltips to the content
     *
     * This class uses the `the_content` filter to add tooltips to the content.
     * In the constructor, we register this filter to run when the content is
     * rendered.
     */
    public function __construct()
    {
        if (!is_admin()) {
            add_filter('the_content', array($this, 'add_tooltips_to_content'));
            add_action('wp_footer', array($this, 'add_tooltip_tags_to_footer'));

            if (!function_exists('enqueue_tippy_scripts')) {
                add_action('wp_enqueue_scripts', array($this, 'enqueue_tippy_scripts'));
            }
        }
    }

    public function enqueue_tippy_scripts()
    {
        // Enqueue the CSS and JS files
        wp_enqueue_style('tooltip-css', plugin_dir_url(__FILE__) . '../assets/css/glossary.css');

        $theme = get_option('glossary_theme', 'light-border');
        $animation = get_option('glossary_animation', 'shift-away-subtle');
        // Themes
        wp_enqueue_style('tooltip-theme', plugin_dir_url(__FILE__) . '../assets/css/tippy.js/themes/' . $theme . '.min.css');
        // wp_enqueue_style('tooltip-theme-light-border', plugin_dir_url(__FILE__) . '../assets/css/tippy.js/themes/light-border.min.css');
        // wp_enqueue_style('tooltip-theme-dark', plugin_dir_url(__FILE__) . '../assets/css/tippy.js/themes/dark.min.css');

        // Animations
        wp_enqueue_style('tooltip-animation', plugin_dir_url(__FILE__) . '../assets/css/tippy.js/animations/' . $animation . '.min.css');
        // wp_enqueue_style('tooltip-animation-scale', plugin_dir_url(__FILE__) . '../assets/css/tippy.js/animations/scale.min.css');
        // wp_enqueue_style('tooltip-animation-scale-subtle', plugin_dir_url(__FILE__) . '../assets/css/tippy.js/animations/scale-subtle.min.css');
        // wp_enqueue_style('tooltip-animation-shift-away-subtle', plugin_dir_url(__FILE__) . '../assets/css/tippy.js/animations/shift-away-subtle.min.css');

        // JavaScript
        wp_enqueue_script('tooltip-js', plugin_dir_url(__FILE__) . '../assets/js/glossary-tooltip.js', array('jquery'), null, true);
    }

    public function add_tooltips_to_content($content)
    {
        // Retrieve all glossary entries
        $glossary_entries = $this->get_glossary_entries();

        // Search content for terms and add tooltips
        foreach ($glossary_entries as $entry) {
            $title = esc_html($entry->post_title);
            $related_terms = get_post_meta($entry->ID, '_glossary_entry_related_terms', true);
            $related_terms = explode(',', $related_terms);

            // Collect tooltip ID and add tooltips for the main term
            if (!in_array($entry->ID, $this->tooltip_ids)) {
                $this->tooltip_ids[] = $entry->ID;
            }
            $content = $this->add_tooltip($content, $title, $entry->ID);

            // Collect tooltip IDs and add tooltips for related terms
            foreach ($related_terms as $term) {
                $term = trim($term);
                if (!empty($term)) {
                    if (!in_array($entry->ID, $this->tooltip_ids)) {
                        $this->tooltip_ids[] = $entry->ID;
                    }
                    $content = $this->add_tooltip($content, $term, $entry->ID);
                }
            }
        }

        return $content;
    }

    private function get_glossary_entries()
    {
        $args = array(
            'post_type' => 'glossary_entries',
            'posts_per_page' => -1,
        );

        $entries = get_posts($args);
        return $entries;
    }

    private function add_tooltip($content, $term, $id)
    {
        $theme = get_option('glossary_theme', 'light-border');
        $animation = get_option('glossary_animation', 'scale-subtle');
        $trigger = get_option('glossary_trigger', 'mouseover');
        // Create the HTML tooltip
        $tooltip_html = '<span class="gt-tooltip-parent"><span data-template-id="' . $id . '" data-tooltip-trigger="' . $trigger . '" data-tooltip-theme="' . $theme . '" data-tooltip-animation="' . $animation . '" class="gt-tooltip-trigger" tabindex="0">' . '$0' . '*</span></span>';

        // Flag for case sensitivity
        $case_sensitive = false;
        $pattern_modifier = $case_sensitive ? '' : 'i';

        // This pattern searches for the term not in HTML attributes
        $pattern = '/(?<![\w\-])' . preg_quote($term, '/') . '(?![\w\-])(?![^<]*>)/' . $pattern_modifier;

        // Replace the term with the tooltip HTML
        $content = preg_replace_callback($pattern, function ($matches) use ($tooltip_html) {
            return str_replace('$0', $matches[0], $tooltip_html);
        }, $content);

        return $content;
    }

    public function add_tooltip_tags_to_footer()
    {
        $truncate = get_option('gt_truncate', 250);
        $show_link = get_option('gt_show_link', true);

        foreach ($this->tooltip_ids as $id) {
            $post = get_post($id);
            if ($post) {
                $title = esc_html($post->post_title);
                $excerpt = substr($post->post_content, 0, $truncate);
                if (strlen($post->post_content) > $truncate) {
                    $excerpt .= '...';
                }


                // Add the template tag at the end of the body
                echo '<template data-tooltip-id="' . $id . '" class="tooltip-template">';
                echo '<div class="gt-tooltip-title">' . $title . '</div>';
                echo '<div class="gt-tooltip-excerpt">' . $excerpt . '</div>';
                if ($show_link) {
                    echo '<div class="gt-tooltip-link"><a href="' . get_permalink($post->ID) . '">Read more</a></div>';
                };
                echo '</template>';
            }
        }
    }
}
