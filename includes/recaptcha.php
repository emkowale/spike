<?php
if (!defined('ABSPATH')) exit;
function spike_recaptcha_verify($token){
  $o=get_option('spike_opts',[]); $secret=trim($o['recaptcha_secret']??'');
  if(!$secret) return true; // allow initial testing before keys are set
  if(!$token) return false;
  $r=wp_remote_post('https://www.google.com/recaptcha/api/siteverify',['timeout'=>8,'body'=>['secret'=>$secret,'response'=>$token]]);
  if(is_wp_error($r)) return false;
  $j=json_decode(wp_remote_retrieve_body($r),true);
  $score=floatval($j['score']??0); $min=floatval($o['recaptcha_score']??0.5);
  return !empty($j['success']) && $score>=$min;
}
