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
        $entries = [];

        $output = '';

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

            if (!isset($entries[$first_letter])) {
                $entries[$first_letter] = array();
            }
            $entries[$first_letter][] = array(
                'ID' => $post->ID,
                'post_title' => $post->post_title,
                'permalink' => get_permalink($post->ID)
            );
        }

        ksort($entries);

        foreach ($entries as $letter => $posts) {
            $output .= '<div id="' . strtolower($letter) . '">';
            $output .= '<h2>' . $letter . '</h2>';
            $output .= '<ul>';
            foreach ($posts as $post) {
                $output .= '<li><a href="' . get_permalink($post['ID']) . '">' . $post['post_title'] . '</a></li>';
            }
            $output .= '</ul>';
            $output .= '</div>';
        }

        // WP_Query zurücksetzen
        wp_reset_postdata();

        return $output;
    }
}
