<?php
if (!defined('ABSPATH')) exit;

/** Admin menu: Spike (heart) → Settings */
add_action('admin_menu', function(){
  add_menu_page('Spike','Spike','manage_options','spike','spike_admin_settings_screen','dashicons-heart',25);
  add_submenu_page('spike','Settings','Settings','manage_options','spike','spike_admin_settings_screen');
});

/** Settings screen wrapper */
function spike_admin_settings_screen(){
  if (function_exists('spike_settings_page')) { spike_settings_page(); return; }
  echo '<div class="wrap"><h1>Spike Settings</h1><p>Settings page not found. Ensure a <code>spike_settings_page()</code> exists.</p></div>';
}

/** Columns for Spike Intakes list */
add_filter('manage_spike_intake_posts_columns', function($c){
  return ['cb'=>$c['cb']??'','title'=>'Intake','patient'=>'Patient','email'=>'Email','phone'=>'Phone','pdf'=>'PDF','date'=>$c['date']??'Date'];
});

/** Render column data */
add_action('manage_spike_intake_posts_custom_column', function($col,$post_id){
  $g=function($k)use($post_id){ return get_post_meta($post_id,$k,true); };
  if($col==='patient'){
    $name=trim($g('first_name').' '.$g('last_name')); $dob=$g('dob');
    echo esc_html($name.($dob ? " (DOB: $dob)" : ''));
  } elseif($col==='email'){
    echo esc_html($g('email'));
  } elseif($col==='phone'){
    echo esc_html($g('primary_phone'));
  } elseif($col==='pdf'){
    $url=wp_nonce_url(admin_url('admin-post.php?action=spike_download_pdf&post_id='.$post_id),'spike_pdf_'.$post_id);
    echo '<a class="button" target="_blank" rel="noopener" href="'.esc_url($url).'">View PDF</a>';
  }
},10,2);

/** Inline PDF/HTML stream (no forced download) */
add_action('admin_post_spike_download_pdf', function(){
  $pid=absint($_GET['post_id']??0);
  if(!$pid || !current_user_can('edit_post',$pid) || !wp_verify_nonce($_GET['_wpnonce']??'','spike_pdf_'.$pid)) wp_die('Unauthorized',403);

  if(!function_exists('spike_generate_pdf')) require_once __DIR__.'/pdf.php';
  $built=spike_generate_pdf($pid);
  $base=spike_private_dir()."/intake-$pid";
  $pdf = (is_string($built)&&$built && file_exists($built)) ? $built : (file_exists("$base.pdf") ? "$base.pdf" : '');
  $html= file_exists("$base.html") ? "$base.html" : '';

  if($pdf){
    if(ob_get_length()) @ob_end_clean();
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="intake-'.$pid.'.pdf"'); // inline view
    header('Content-Length: '.filesize($pdf));
    readfile($pdf); exit;
  }
  if($html){
    if(ob_get_length()) @ob_end_clean();
    header('Content-Type: text/html; charset=utf-8');
    header('Content-Disposition: inline; filename="intake-'.$pid.'.html"');
    readfile($html); exit;
  }
  wp_die('PDF not available. Install Dompdf or check file permissions.');
});
