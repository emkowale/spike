<?php
if (!defined('ABSPATH')) exit;

/** States + select (no blank; default MI) */
function spike_states(){ return [
 'AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California','CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware','FL'=>'Florida','GA'=>'Georgia',
 'HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois','IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana','ME'=>'Maine','MD'=>'Maryland',
 'MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota','MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada','NH'=>'New Hampshire',
 'NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York','NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma','OR'=>'Oregon','PA'=>'Pennsylvania',
 'RI'=>'Rhode Island','SC'=>'South Carolina','SD'=>'South Dakota','TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah','VT'=>'Vermont','VA'=>'Virginia','WA'=>'Washington',
 'WV'=>'West Virginia','WI'=>'Wisconsin','WY'=>'Wyoming'
];}
function spike_state_select($selected='MI'){
  $h='<div class="field"><label for="state">State <span>*</span></label><select id="state" name="state" required>';
  foreach(spike_states() as $k=>$v){ $sel=$k===$selected?' selected':''; $h.='<option value="'.$k.'"'.$sel.'>'.$v.'</option>'; }
  return $h.'</select></div>';
}

/** Dropdown option sets */
function spike_opts_sex(){return ['Male'=>'Male','Female'=>'Female'];}
function spike_opts_race(){return [
 'American Indian or Alaskan Native'=>'American Indian or Alaskan Native','Asian'=>'Asian','Black or African American'=>'Black or African American',
 'White or Caucasian'=>'White or Caucasian','Native Hawaiian or Pacific Islander'=>'Native Hawaiian or Pacific Islander','Decline to Specify'=>'Decline to Specify'
];}
function spike_opts_ethnicity(){return ['Hispanic or Latino'=>'Hispanic or Latino','Not Hispanic or Latino'=>'Not Hispanic or Latino','Decline to Specify'=>'Decline to Specify'];}
function spike_opts_relationship(){return ['Married'=>'Married','Single'=>'Single','Widowed'=>'Widowed','Partnered'=>'Partnered','Other'=>'Other'];}
function spike_opts_employment(){return ['Employed'=>'Employed','Student'=>'Student','Retired'=>'Retired','Other'=>'Other'];}
function spike_opts_accident_type(){return ['Auto'=>'Auto','Work'=>'Work','Home'=>'Home','Other'=>'Other'];}
function spike_opts_accident_reported_to(){return ['Auto Insurance'=>'Auto Insurance','Employer'=>'Employer','Work Comp'=>'Work Comp','Other'=>'Other'];}
function spike_opts_heard_about(){return ['Google Search'=>'Google Search','Friend/Family'=>'Friend/Family','Doctor Referral'=>'Doctor Referral','Social Media'=>'Social Media','Drive By'=>'Drive By','Other'=>'Other'];}
function spike_opts_language(){return ['English'=>'English','Spanish'=>'Spanish','Other'=>'Other'];}

/** Small render helpers */
function spike_input($name,$label,$type='text',$required=false,$attrs=''){
  $req=$required?' required':''; $star=$required?' <span>*</span>':'';
  return '<div class="field"><label for="'.$name.'">'.$label.$star.'</label><input type="'.$type.'" id="'.$name.'" name="'.$name.'"'.$req.' '.$attrs.'></div>';
}
function spike_select($name,$label,$options,$required=false,$attrs='',$selected=null,$includeBlank=true){
  $req=$required?' required':''; $star=$required?' <span>*</span>':'';
  $h='<div class="field"><label for="'.$name.'">'.$label.$star.'</label><select id="'.$name.'" name="'.$name.'"'.$req.' '.$attrs.'>';
  if($includeBlank) $h.='<option value=""></option>';
  foreach($options as $val=>$text){ $sel=($selected!==null && (string)$val===(string)$selected)?' selected':''; $h.='<option value="'.esc_attr($val).'"'.$sel.'>'.esc_html($text).'</option>'; }
  return $h.'</select></div>';
}
