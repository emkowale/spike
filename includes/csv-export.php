<?php
if (!defined('ABSPATH')) exit;
add_action('admin_menu',function(){ add_submenu_page('edit.php?post_type=spike_intake','CSV Export','CSV Export','manage_options','spike_csv','spike_csv_page'); });
function spike_csv_page(){
  if(!current_user_can('manage_options')) return;
  if(isset($_POST['spike_csv']) && check_admin_referer('spike_csv')){ spike_output_csv(); exit; }
  echo '<div class="wrap"><h1>Spike CSV Export</h1><form method="post">'; wp_nonce_field('spike_csv'); submit_button('Download CSV','primary','spike_csv'); echo '</form></div>';
}
function spike_output_csv(){
  header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename=spike-intakes-'.date('Ymd').'.csv'); $out=fopen('php://output','w');
  fputcsv($out,['ID','Date','First','Last','Email','Phone','Address','City','State','Zip','PainPoints','PrintedName','SignDate']);
  $q=new WP_Query(['post_type'=>'spike_intake','posts_per_page'=>-1,'orderby'=>'date','order'=>'DESC']);
  while($q->have_posts()){ $q->the_post(); $g=function($k){return get_post_meta(get_the_ID(),$k,true);};
    fputcsv($out,[get_the_ID(),get_the_date('Y-m-d H:i'),$g('first_name'),$g('last_name'),$g('email'),$g('primary_phone'),trim($g('address1').' '.$g('address2')),$g('city'),$g('state'),$g('zip'),$g('pain_points'),$g('signer_print'),$g('sign_date')]); }
  wp_reset_postdata(); fclose($out);
}
