<?php

class Glossary_Post_Type {
    public function __construct() {
        add_action('init', array($this, 'create_glossary_post_type'));
    }

    public function create_glossary_post_type() {
        $labels = array(
            'name'               => _x('Glossary entries', 'post type general name'),
            'singular_name'      => _x('Glossary entry', 'post type singular name'),
            'menu_name'          => _x('Glossary entries', 'admin menu'),
            'name_admin_bar'     => _x('Glossary entry', 'add new on admin bar'),
            'add_new'            => _x('Add New', 'glossary entry'),
            'add_new_item'       => __('Add New glossary entry'),
            'new_item'           => __('New glossary entry'),
            'edit_item'          => __('Edit glossary entry'),
            'view_item'          => __('View glossary entry'),
            'all_items'          => __('All glossary entries'),
            'search_items'       => __('Search glossary entries'),
            'parent_item_colon'  => __('Parent glossary entries:'),
            'not_found'          => __('No glossary entries found.'),
            'not_found_in_trash' => __('No glossary entries found in Trash.')
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'glossary'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            // 'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
            'supports'           => array('title', 'editor', 'excerpt')
        );

        register_post_type('glossary_entries', $args);
    }
}
