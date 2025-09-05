<?php
if (!defined('ABSPATH')) exit;

add_action('admin_post_nopriv_spike_submit','spike_handle_submit');
add_action('admin_post_spike_submit','spike_handle_submit');

function spike_handle_submit(){
  if (empty($_POST['spike_nonce']) || !wp_verify_nonce($_POST['spike_nonce'],'spike_submit')) wp_die('Bad nonce');
  if (function_exists('spike_recaptcha_verify') && !spike_recaptcha_verify($_POST['g-recaptcha-response'] ?? '')) wp_die('reCAPTCHA failed');

  $need=['first_name','last_name','address1','city','state','zip','primary_phone','email'];
  foreach($need as $k){ if(empty($_POST[$k])) wp_die('Missing: '.$k); }
  if(!preg_match('/^\d{3}-\d{3}-\d{4}$/', $_POST['primary_phone'])) wp_die('Phone must be xxx-xxx-xxxx');
  if(!is_email($_POST['email'])) wp_die('Invalid email');

  $title=sanitize_text_field(($_POST['last_name']??'').', '.($_POST['first_name']??'').' - '.current_time('Y-m-d H:i'));
  $pid=wp_insert_post(['post_type'=>'spike_intake','post_status'=>'publish','post_title'=>$title]);

  foreach($_POST as $k=>$v){
    if(in_array($k,['spike_nonce','action','g-recaptcha-response'],true)) continue;
    update_post_meta($pid,$k,is_string($v)?wp_unslash($v):wp_json_encode($v));
  }

  $pdf = function_exists('spike_generate_pdf') ? spike_generate_pdf($pid) : '';

  // --- mail helpers (scoped filters so DMARC doesn't block external recipients) ---
  $host = parse_url(home_url(), PHP_URL_HOST);
  $from_email = 'no-reply@'.$host;
  $from_name  = 'PHI Chiropractic';
  $apply_from = function() use($from_email,$from_name){
    add_filter('wp_mail_from', fn($e)=>$from_email, 99);
    add_filter('wp_mail_from_name', fn($n)=>$from_name, 99);
  };
  $remove_from = function(){
    remove_filter('wp_mail_from','__return_false',99); // noop guard
    remove_all_filters('wp_mail_from', 99);
    remove_all_filters('wp_mail_from_name', 99);
  };
  $headers=['Content-Type: text/plain; charset=UTF-8'];

  // Patient copy (only if box checked)
  if (!empty($_POST['patient_copy']) && is_email($_POST['email'])){
    $first = sanitize_text_field($_POST['first_name'] ?? '');
    $subject = 'PHI Chiropractic Intake Form';
    $body = "Hello {$first},\n\n     Attached is a pdf of the Intake Form you filled out for PHI Chiropractic.\n\nThank You!,\nThe PHI Team";
    $atts = $pdf ? [$pdf] : [];
    $apply_from(); $ok = wp_mail($_POST['email'], $subject, $body, $headers, $atts); $remove_from();
    if(!$ok && defined('WP_DEBUG') && WP_DEBUG) error_log('Spike: patient email failed for post '.$pid);
  }

  // Admin — ONLY to emails in settings box, attach PDF
  $o = get_option('spike_opts',[]);
  $rcpt_str = trim($o['recipients'] ?? ''); // label shows "admin email recipients (comma-separated)"
  if ($rcpt_str !== ''){
    $tolist = array_filter(array_map('trim', explode(',', $rcpt_str)), 'is_email');
    if ($tolist){
      $first = sanitize_text_field($_POST['first_name'] ?? ''); $last = sanitize_text_field($_POST['last_name'] ?? '');
      $subject = 'PHI Chiropractic Intake Form';
      $body = "Hello,\n\n     Attached is an Intake Form for {$first} {$last}.\n\nThanks!,\nYour amazing website!";
      $atts = $pdf ? [$pdf] : [];
      $apply_from();
      foreach($tolist as $addr){ wp_mail($addr, $subject, $body, $headers, $atts); }
      $remove_from();
    }
  }

  // Redirect to Thank-You (supports cross-domain)
  $thanks = trim($o['thankyou'] ?? ''); if($thanks==='') $thanks = home_url('/thank-you-for-your-intake-form/');
  $code = 303;
  if (preg_match('#^https?://#i',$thanks)) {
    $site = parse_url(home_url(), PHP_URL_HOST); $dest = parse_url($thanks, PHP_URL_HOST);
    if ($dest && $dest !== $site) { wp_redirect(esc_url_raw($thanks), $code); exit; }
  }
  wp_safe_redirect(esc_url_raw($thanks), $code); exit;
}
