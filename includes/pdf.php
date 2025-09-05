<?php
if (!defined('ABSPATH')) exit;
require_once __DIR__.'/consent.php';
require_once __DIR__.'/pdf_view.php';

function spike_private_dir(){
  $d=WP_CONTENT_DIR.'/uploads/spike-private'; if(!is_dir($d)) wp_mkdir_p($d);
  $ht="$d/.htaccess"; if(!file_exists($ht)) file_put_contents($ht,"Require all denied\n"); return $d;
}

/** Build diagram with red X’s server-side (GD). Returns data URI or '' on fail. */
function spike_diagram_overlay($src,$points,$post_id){
  $bin=@file_get_contents($src); if($bin===false) return '';
  if(!function_exists('imagecreatefromstring')) return ''; // GD not available
  $im=@imagecreatefromstring($bin); if(!$im) return '';
  $w=imagesx($im); $h=imagesy($im); $red=imagecolorallocate($im,255,0,0); imagesetthickness($im,4);
  $pts=json_decode($points?:'[]',true); if(!is_array($pts)) $pts=[];
  foreach($pts as $p){
    $x=(int)round(($p['x']??0)*$w); $y=(int)round(($p['y']??0)*$h);
    imageellipse($im,$x,$y,36,36,$red);
    imageline($im,$x-28,$y-28,$x+28,$y+28,$red);
    imageline($im,$x-28,$y+28,$x+28,$y-28,$red);
  }
  $file=spike_private_dir()."/intake-$post_id-diagram.png"; imagepng($im,$file); imagedestroy($im);
  $b=@file_get_contents($file); return $b ? 'data:image/png;base64,'.base64_encode($b) : '';
}

function spike_generate_pdf($post_id){
  $o=get_option('spike_opts',[]); if(($o['pdf_on']??'yes')!=='yes') return '';
  $p=get_post($post_id); if(!$p) return ''; $m=get_post_meta($post_id);
  $g=function($k)use($m){return $m[$k][0]??'';};
  $gj=function($k)use($g){$v=$g($k); $d=json_decode($v,true); return is_array($d)?implode(', ',$d):$v;};

  $V=[
    'logo'=>'https://proactivehealthmanager.com/wp-content/uploads/2024/03/web-logo-square.jpg',
    'diagram'=>($o['diagram_url']??'')?:'https://via.placeholder.com/600x800?text=Pain+Diagram',
    'first'=>$g('first_name'),'last'=>$g('last_name'),'mi'=>$g('middle_initial'),'dob'=>$g('dob'),
    'addr1'=>$g('address1'),'addr2'=>$g('address2'),'city'=>$g('city'),'state'=>$g('state'),'zip'=>$g('zip'),
    'phone1'=>$g('primary_phone'),'phone2'=>$g('secondary_phone'),'email'=>$g('email'),
    'sex'=>$g('sex'),'race'=>$g('race'),'ethnicity'=>$g('ethnicity'),'lang'=>$g('preferred_language'),
    'rel'=>$g('relationship_status'),'heard'=>$g('heard_about'),
    'emp_stat'=>$g('employment_status'),'occ'=>$g('occupation'),'sch'=>$g('employer_school'),
    'emp_phone'=>$g('employer_phone'),'emp_addr'=>$g('employer_address'),'emp_city'=>$g('employer_city'),'emp_zip'=>$g('employer_zip'),
    'emg_name'=>$g('emergency_name'),'emg_rel'=>$g('emergency_relationship'),'emg_phone'=>$g('emergency_phone'),
    'ins1'=>$g('primary_insurance'),'pol1'=>$g('policy_number'),'grp1'=>$g('group_number'),
    'sub1'=>$g('subscriber_name'),'subrel1'=>$g('subscriber_relationship'),'subdob1'=>$g('subscriber_dob'),
    'ins2'=>$g('secondary_insurance'),'pol2'=>$g('secondary_policy_number'),'grp2'=>$g('secondary_group_number'),
    'sub2'=>$g('secondary_subscriber_name'),'subrel2'=>$g('secondary_subscriber_relationship'),'subdob2'=>$g('secondary_subscriber_dob'),
    'acc_now'=>$g('accident_now'),'acc_date'=>$g('accident_date'),'acc_type'=>$g('accident_type'),'acc_rep'=>$g('accident_reported_to'),
    'mh_reason'=>$g('mh_reason'),
    'mh_pain_desc'=>trim(($gj('mh_pain_desc')?:'').(($t=$g('mh_pain_desc_other'))?(' | Other: '.$t):'')),
    'mh_travel'=>$g('mh_travel'),'mh_travel_where'=>$g('mh_travel_where'),
    'mh_duration'=>$g('mh_duration'),'mh_onset'=>$g('mh_onset'),
    'mh_aggravates'=>$g('mh_aggravates'),'mh_helps'=>$g('mh_helps'),
    'mh_freq'=>$g('mh_frequency'),'mh_const'=>$g('mh_constant'),
    'mh_inter'=>trim(($gj('mh_interferes')?:'').(($t=$g('mh_interferes_other'))?(' | Other: '.$t):'')),
    'mh_seen'=>$g('mh_seen_provider'),'mh_seen_who'=>$g('mh_seen_who'),
    'mh_test'=>$g('mh_testing'),'mh_test_when'=>$g('mh_testing_when'),
    'mh_conds'=>trim(($gj('mh_conditions')?:'').(($t=$g('mh_conditions_other'))?(' | Other: '.$t):'')),
    'mh_ex'=>$gj('mh_exercise'),'mh_work'=>$gj('mh_work_activity'),
    'mh_hab'=>implode('; ',array_filter([
      $g('mh_habit_smoking')==='Yes'?'Smoking: '.$g('mh_habit_smoking_amt').' packs/day':'',
      $g('mh_habit_alcohol')==='Yes'?'Alcohol: '.$g('mh_habit_alcohol_amt').' drinks/wk':'',
      $g('mh_habit_caffeine')==='Yes'?'Caffeine: '.$g('mh_habit_caffeine_amt').' cups/day':''
    ])),
    'mh_preg'=>$g('mh_pregnant'),'mh_inj'=>$g('mh_injuries'),'mh_meds'=>$g('mh_meds'),'mh_all'=>$g('mh_allergies'),
    'points'=>$g('pain_points'),'sign'=>$g('signature'),'sign_print'=>$g('signer_print'),
    'sign_date'=>$g('sign_date'),'witness'=>$g('witness_name'),'consent'=>spike_consent_html(),
  ];

  // Bake the overlay; fall back to raw diagram if overlay fails
  $V['diagram_render']=spike_diagram_overlay($V['diagram'],$V['points'],$post_id) ?: $V['diagram'];

  $META=[]; foreach($m as $k=>$arr){ $v=$arr[0]??''; $d=json_decode($v,true); $META[$k]=is_array($d)?implode(', ',$d):$v; }

  $html=spike_pdf_html($V,$META); $base=spike_private_dir()."/intake-$post_id"; file_put_contents("$base.html",$html);

  if(!class_exists('\\Dompdf\\Dompdf')){ $auto=dirname(__DIR__).'/vendor/autoload.php'; if(file_exists($auto)) require_once $auto; }
  if(!class_exists('\\Dompdf\\Dompdf')) return '';
  try{ $dompdf=new \Dompdf\Dompdf(['isRemoteEnabled'=>true]); $dompdf->setPaper('letter','portrait'); $dompdf->loadHtml($html,'UTF-8'); $dompdf->render(); file_put_contents("$base.pdf",$dompdf->output()); return "$base.pdf"; }
  catch(\Throwable $e){ return ''; }
}
