<?php
if (!defined('ABSPATH')) exit;
add_action('admin_post_nopriv_spike_submit','spike_handle_submit');
add_action('admin_post_spike_submit','spike_handle_submit');

function spike_handle_submit(){
  if(empty($_POST['spike_nonce'])||!wp_verify_nonce($_POST['spike_nonce'],'spike_submit')) wp_die('Bad nonce');
  if(!spike_recaptcha_verify($_POST['g-recaptcha-response']??'')) wp_die('reCAPTCHA failed');
  $req=['first_name','last_name','address1','city','state','zip','primary_phone','email'];
  foreach($req as $k){ if(empty($_POST[$k])) wp_die('Missing: '.$k); }
  if(!preg_match('/^\d{3}-\d{3}-\d{4}$/', $_POST['primary_phone'])) wp_die('Phone must be xxx-xxx-xxxx');
  if(!is_email($_POST['email'])) wp_die('Invalid email');

  $title=sanitize_text_field($_POST['last_name'].', '.$_POST['first_name'].' - '.current_time('Y-m-d H:i'));
  $pid=wp_insert_post(['post_type'=>'spike_intake','post_status'=>'publish','post_title'=>$title]);
  foreach($_POST as $k=>$v){ if(in_array($k,['spike_nonce','action','g-recaptcha-response'])) continue; update_post_meta($pid,$k,is_string($v)?wp_unslash($v):wp_json_encode($v)); }

  $pdfPath=spike_generate_pdf($pid);
  $o=get_option('spike_opts',[]); $to=get_option('admin_email'); if(!empty($o['recipients'])) $to.=','.$o['recipients'];
  $headers=['Content-Type: text/plain; charset=UTF-8']; $attachments=[];
  if(($o['pdf_on']??'yes')==='yes' && $pdfPath) $attachments[]=$pdfPath;
  wp_mail($to,'New PHI Intake',"New Spike Intake.\nEdit: ".admin_url('post.php?post='.$pid.'&action=edit'),$headers,$attachments);
  if(!empty($_POST['patient_copy']) && is_email($_POST['email']) && $pdfPath) wp_mail($_POST['email'],'Your PHI Intake Copy','Attached is your intake PDF.',$headers,[$pdfPath]);

  $thanks=!empty($o['thankyou'])?$o['thankyou']:home_url('/thank-you-for-your-intake-form/');
  wp_safe_redirect($thanks); exit;
}
