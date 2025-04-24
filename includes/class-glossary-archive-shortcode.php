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

        $current_letter = null;

        foreach ($posts as $post) {
            $title = $post->post_title;

            // Find the first word that starts with a letter
            if (!preg_match('/^[A-Za-z]/', $title)) {
                $words = explode(' ', $title);
                foreach ($words as $index => $word) {
                    if (preg_match('/^[A-Za-z]/', $word)) {
                        $title = $word;
                        break;
                    }
                }
            }
            
            $first_letter = strtoupper(substr($title, 0, 1));
            echo $first_letter;

            if ($first_letter != $current_letter) {
                if ($current_letter != null) {
                    $output .= '</ul></div>';
                }

                $output .= '<div id="' . strtolower($first_letter) . '">';
                $output .= '<h2>' . $first_letter . '</h2>';
                $output .= '<ul>';

            }

            $current_letter = $first_letter;
            $output .= '<li><a href="' . get_permalink($post->ID) . '">' . $post->post_title . '</a></li>';
            
        }

        $output .= '</div>';
        $output .= '</ul>';

        // WP_Query zurücksetzen
        wp_reset_postdata();

        return $output;
    }
}
