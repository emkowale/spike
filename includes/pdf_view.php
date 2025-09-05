<?php
if (!defined('ABSPATH')) exit;

function spike_pdf_html($v,$meta){
  $h='<!doctype html><html><head><meta charset="utf-8"><style>
  *{box-sizing:border-box}body{font-family:Arial,Helvetica,sans-serif;font-size:10px;line-height:1.18;color:#111;margin:18px}
  .hdr{display:flex;align-items:center;gap:8px;margin-bottom:2px}.hdr img{height:44px}.hdr h1{font-size:16px;margin:0}
  .sec{margin-top:6px;page-break-inside:avoid}.ttl{font-weight:700;border-bottom:1px solid #000;padding:2px 0;margin:4px 0 3px}
  table{width:100%;border-collapse:collapse}th,td{border:1px solid #000;padding:3px 4px;vertical-align:top}
  th{width:20%;background:#f5f5f5;text-align:left;font-weight:700}.two td,.two th{width:auto}
  .sig{display:flex;gap:12px;margin-top:8px;align-items:flex-end}.line{border-top:1px solid #000;padding-top:2px;font-size:9px;min-width:180px}
  img.sig{height:60px;border:1px solid #999;padding:2px;background:#fff}.pb{page-break-before:always}
  .pd{max-width:420px;margin:4px auto} .pd img{display:block;width:100%;height:auto}
  .consent{font-size:9.5px;line-height:1.22}
  </style></head><body>';
  $h.='<div class="hdr"><img src="'.$v['logo'].'"><h1>Patient Health Intake</h1></div>';
  $row=function($a,$b=[]){return'<tr><th>'.$a[0].'</th><td>'.htmlspecialchars($a[1]??'').'</td>'.($b?'<th>'.$b[0].'</th><td>'.htmlspecialchars($b[1]??'').'</td>':'<th></th><td></td>').'</tr>';};
  $val=function($k)use($meta){return $meta[$k]??'';};

  // Page 1
  $h.='<div class="sec"><div class="ttl">Patient Information</div><table class="two"><tbody>';
  $h.=$row(['First Name',$v['first']],['Last Name',$v['last']]); $h.=$row(['M.I.',$v['mi']],['DOB',$v['dob']]);
  $h.=$row(['Address',$v['addr1']],['Address 2',$v['addr2']]); $h.=$row(['City',$v['city']],['State',$v['state']]);
  $h.=$row(['Zip',$v['zip']],['Primary Phone',$v['phone1']]); $h.=$row(['Secondary Phone',$v['phone2']],['Email',$v['email']]);
  $h.=$row(['Sex',$v['sex']],['Race',$v['race']]); $h.=$row(['Ethnicity',$v['ethnicity']],['Preferred Language',$v['lang']]);
  $h.=$row(['Relationship Status',$v['rel']],['How did you hear about our office?',$v['heard']]);
  $h.='</tbody></table></div>';
  $h.='<div class="sec"><div class="ttl">Employment Status</div><table class="two"><tbody>';
  $h.=$row(['Employment Status',$v['emp_stat']],['Occupation',$v['occ']]); $h.=$row(['Employer/School',$v['sch']],['Employer Phone',$v['emp_phone']]);
  $h.=$row(['Employer Address',$v['emp_addr']],['Employer City',$v['emp_city']]); $h.=$row(['Employer Zip',$v['emp_zip']]);
  $h.='</tbody></table></div>';
  $h.='<div class="sec"><div class="ttl">Emergency Contact</div><table class="two"><tbody>';
  $h.=$row(['Name',$v['emg_name']],['Relationship',$v['emg_rel']]); $h.=$row(['Phone',$v['emg_phone']]);
  $h.='</tbody></table></div>';
  $h.='<div class="sec"><div class="ttl">Insurance Information</div><table class="two"><tbody>';
  $h.=$row(['Primary Insurance',$v['ins1']],['Policy #',$v['pol1']]); $h.=$row(['Group #',$v['grp1']],['Subscriber Name',$v['sub1']]);
  $h.=$row(['Subscriber Relationship',$v['subrel1']],['Subscriber DOB',$v['subdob1']]); $h.=$row(['Secondary Insurance',$v['ins2']],['Policy # (Secondary)',$v['pol2']]);
  $h.=$row(['Group # (Secondary)',$v['grp2']],['Subscriber Name (Secondary)',$v['sub2']]); $h.=$row(['Subscriber Relationship (Secondary)',$v['subrel2']],['Subscriber DOB (Secondary)',$v['subdob2']]);
  $h.='</tbody></table></div>';

  // Page 2
  $h.='<div class="sec"><div class="ttl">Accident Information</div><table class="two"><tbody>';
  $h.=$row(['Are you here today because of an accident?',$v['acc_now']],['Date of Accident',$v['acc_date']]);
  $h.=$row(['Type of Accident',$v['acc_type']],['Reported To',$v['acc_rep']]); $h.='</tbody></table></div>';
  $h.='<div class="sec"><div class="ttl">Medical History</div><table class="two"><tbody>';
  $h.=$row(['Reason for Visit Today',$v['mh_reason']],['Pain Description',$v['mh_pain_desc']]); $h.=$row(['Pain travels/shoots?',$v['mh_travel']],['If yes, where?',$v['mh_travel_where']]);
  $h.=$row(['How long',$v['mh_duration']],['How did it start',$v['mh_onset']]); $h.=$row(['What aggravates',$v['mh_aggravates']],['What helps',$v['mh_helps']]);
  $h.=$row(['How often',$v['mh_freq']],['Constant or comes/goes',$v['mh_const']]); $h.=$row(['Interferes with',$v['mh_inter']],['Seen other provider?',$v['mh_seen']]);
  $h.=$row(['If yes, who?',$v['mh_seen_who']],['Diagnostic testing',$v['mh_test']]); $h.=$row(['If yes, when?',$v['mh_test_when']],['Conditions (check all)',$v['mh_conds']]);
  $h.=$row(['Exercise',$v['mh_ex']],['Work Activity',$v['mh_work']]); $h.=$row(['Habits',$v['mh_hab']],['Family — Pregnant?',$v['mh_preg']]);
  $h.=$row(['Injuries/Surgeries',$v['mh_inj']],['Medications',$v['mh_meds']]); $h.=$row(['Allergies',$v['mh_all']]);
  $h.='</tbody></table></div>';

  // Pain Diagram with baked overlay (data URI or plain URL fallback)
  $src=$v['diagram_render'] ?: $v['diagram'];
  $h.='<div class="sec"><div class="ttl">Pain Diagram</div><div class="pd"><img src="'.htmlspecialchars($src).'" alt="Pain Diagram"></div></div>';

  // Page 3
  $h.='<div class="pb sec"><div class="ttl">Informed Consent</div><div class="consent">'.$v['consent'].'</div><div class="sig">';
  if(!empty($v['sign'])) $h.='<img class="sig" src="'.$v['sign'].'" alt="Signature">';
  $h.='<div class="line">'.htmlspecialchars($val('signer_print')).'<br>Printed Name</div><div class="line">'.htmlspecialchars($val('sign_date')).'<br>Date</div><div class="line">'.htmlspecialchars($val('witness_name')).'<br>Witness</div></div></div>';

  return $h.'</body></html>';
}
