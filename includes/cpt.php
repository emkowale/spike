<?php
if (!defined('ABSPATH')) exit;

function spike_register_cpt(){
  register_post_type('spike_intake', [
    'labels' => [
      'name'          => 'Spike Intakes',
      'singular_name' => 'Spike Intake',
      'menu_name'     => 'Intakes',
      'add_new'       => 'Add New',
      'add_new_item'  => 'Add New Intake',
      'edit_item'     => 'Edit Intake',
      'view_item'     => 'View Intake',
      'search_items'  => 'Search Intakes',
    ],
    'public'              => false,
    'show_ui'             => true,
    // Nest under the Spike top-level menu (created in includes/admin.php)
    'show_in_menu'        => 'spike',
    'show_in_admin_bar'   => false,
    'show_in_nav_menus'   => false,
    'has_archive'         => false,
    'exclude_from_search' => true,
    'publicly_queryable'  => false,
    'capability_type'     => 'post',
    'map_meta_cap'        => true,
    'supports'            => ['title'],
  ]);
}
add_action('init','spike_register_cpt');
