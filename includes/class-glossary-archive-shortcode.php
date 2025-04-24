<?php
class Glossary_Archive_Shortcode
{
    public function __construct()
    {
        add_shortcode('gt_glossary_posts', array($this, 'glossary_archive_shortcode'));
    }
    public function glossary_archive_shortcode()
    {
        $args = array(
            'post_type' => 'glossary_entries',
            'posts_per_page' => -1,
            'orderby' => 'name',
            'order' => 'ASC'
        );

        $query = new WP_Query($args);
        $posts = $query->posts;

        $output = '';

        $current_letter = '';

        foreach ($posts as $post) {
            $first_letter = strtoupper(substr($post->post_title, 0, 1));

            if ($first_letter != $current_letter) {
                if ($current_letter != '') {
                    $output .= '</ul>';
                }

                $output .= '<div id="' . strtolower($first_letter) . '">';
                $output .= '<h2>' . $first_letter . '</h2><ul>';

                $current_letter = $first_letter;
            }

            $output .= '<li><a href="' . get_permalink($post->ID) . '">' . $post->post_title . '</a></li>';
            $output .= '</div>';
        }

        $output .= '</ul>';

        // WP_Query zurücksetzen
        wp_reset_postdata();

        return $output;
    }
}
