<?php
if (!defined('ABSPATH')) exit;
function spike_defaults(){
  return [
    'diagram_url'=>'',
    'recipients'=>'eric@kowalewski.com',
    'thankyou'=>'https://proactivehealthmanager.com/thank-you-for-your-intake-form/',
    'recaptcha_site'=>'','recaptcha_secret'=>'','recaptcha_score'=>'0.5',
    'pdf_on'=>'yes','patient_copy'=>'yes','ga_on'=>'no'
  ];
}
function spike_menu(){ add_menu_page('Spike','Spike','manage_options','spike','spike_settings_page','dashicons-forms',25); }
add_action('admin_menu','spike_menu');
function spike_admin_init(){
  register_setting('spike','spike_opts',function($v){ $d=spike_defaults(); return array_merge($d,$v); });
  add_settings_section('spike_main','General',function(){},'spike');
  foreach([
    'diagram_url'=>'Pain diagram image URL (optional)',
    'recipients'=>'Admin email recipients (comma-separated)',
    'thankyou'=>'Thank-you URL',
    'recaptcha_site'=>'reCAPTCHA v3 Site Key',
    'recaptcha_secret'=>'reCAPTCHA v3 Secret',
    'recaptcha_score'=>'reCAPTCHA min score (0–1)',
    'pdf_on'=>'Attach PDF to emails (yes/no)',
    'patient_copy'=>'Show “Email me a copy” checkbox (yes/no)'
  ] as $k=>$label){
    add_settings_field('spike_'.$k,$label,'spike_field','spike','spike_main',['k'=>$k]);
  }
}
add_action('admin_init','spike_admin_init');
function spike_field($a){
  $o=get_option('spike_opts',spike_defaults()); $k=$a['k']; $v=$o[$k]??''; 
  echo '<input type="text" style="width:420px" name="spike_opts['.esc_attr($k).']" value="'.esc_attr($v).'">';
}
function spike_settings_page(){
  echo '<div class="wrap"><h1>Spike Settings</h1><form method="post" action="options.php">';
  settings_fields('spike'); do_settings_sections('spike'); submit_button();
  echo '<p>Set reCAPTCHA keys after creating them for <em>proactivehealthmanager.com</em>.</p></form></div>';
}
