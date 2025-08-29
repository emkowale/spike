<?php
if (!defined('ABSPATH')) exit;
function spike_private_dir(){
  $d=WP_CONTENT_DIR.'/uploads/spike-private'; if(!is_dir($d)) wp_mkdir_p($d);
  $ht="$d/.htaccess"; if(!file_exists($ht)) file_put_contents($ht,"Require all denied\n"); return $d;
}
function spike_generate_pdf($post_id){
  $o=get_option('spike_opts',[]); if(($o['pdf_on']??'yes')!=='yes') return '';
  $p=get_post($post_id); if(!$p) return ''; $m=get_post_meta($post_id); $g=function($k)use($m){return $m[$k][0]??'';};
  $logo='https://proactivehealthmanager.com/wp-content/uploads/2024/03/web-logo-square.jpg';
  $html='<html><head><meta charset="utf-8"><style>body{font-family:Arial,sans-serif;font-size:12px} .hdr{display:flex;gap:10px;align-items:center} .hdr img{height:60px} table{width:100%;border-collapse:collapse} td,th{border:1px solid #ccc;padding:6px}</style></head><body>';
  $html.='<div class="hdr"><img src="'.$logo.'"><h2>PHI Intake</h2></div><table><tbody>';
  foreach([
    'first_name'=>'First Name','last_name'=>'Last Name','middle_initial'=>'M.I.','dob'=>'DOB',
    'address1'=>'Address','address2'=>'Address 2','city'=>'City','state'=>'State','zip'=>'Zip',
    'primary_phone'=>'Phone','email'=>'Email','emergency_name'=>'Emergency Name','emergency_relationship'=>'Emergency Relationship','emergency_phone'=>'Emergency Phone',
    'primary_insurance'=>'Primary Insurance','policy_number'=>'Policy #','subscriber_name'=>'Subscriber Name','subscriber_dob'=>'Subscriber DOB',
    'pain_points'=>'Pain Points (JSON)','witness_name'=>'Witness','signer_print'=>'Printed Name','sign_date'=>'Date'
  ] as $k=>$label){ $v=$g($k); $html.='<tr><th>'.$label.'</th><td>'.esc_html($v).'</td></tr>'; }
  if($g('signature')) $html.='</tbody></table><p><b>Signature:</b><br><img src="'.$g('signature').'" style="height:80px;border:1px solid #ccc"></p>';
  else $html.='</tbody></table>';
  $html.='</body></html>';
  if(!class_exists('\\Dompdf\\Dompdf')){ file_put_contents(spike_private_dir()."/intake-$post_id.html",$html); return ''; }
  $file=spike_private_dir()."/intake-$post_id.pdf"; try{
    $dompdf=new \Dompdf\Dompdf(['isRemoteEnabled'=>true]); $dompdf->loadHtml($html,'UTF-8'); $dompdf->setPaper('A4','portrait'); $dompdf->render(); file_put_contents($file,$dompdf->output()); return $file;
  }catch(\Throwable $e){ return ''; }
}
