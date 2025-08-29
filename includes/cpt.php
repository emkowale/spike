<?php
if (!defined('ABSPATH')) exit;
function spike_register_cpt(){
  register_post_type('spike_intake',[
    'labels'=>['name'=>'Spike Intakes','singular_name'=>'Spike Intake'],
    'public'=>false,'show_ui'=>true,'menu_icon'=>'dashicons-heart','supports'=>['title']
  ]);
}
add_action('init','spike_register_cpt');
