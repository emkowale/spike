<?php
if (!defined('ABSPATH')) exit;
require_once __DIR__.'/fields.php';
require_once __DIR__.'/medical.php';
require_once __DIR__.'/consent.php';
require_once __DIR__.'/step1.php';
require_once __DIR__.'/step3.php';

/** Assets */
function spike_assets(){
  wp_enqueue_style('spike-css', SPIKE_URL.'assets/css/frontend.css', [], SPIKE_VER);
  wp_enqueue_script('spike-js',  SPIKE_URL.'assets/js/frontend.js',  [], SPIKE_VER, true);
  $o = get_option('spike_opts', []);
  wp_localize_script('spike-js','SPIKE',[
    'siteKey'=>$o['recaptcha_site']??'',
    'maxPts'=>10,
    'thanks'=>!empty($o['thankyou']) ? esc_url($o['thankyou']) : ''
  ]);
}
add_action('wp_enqueue_scripts','spike_assets');

/** Shortcode */
function spike_shortcode($atts=[]){
  $o=get_option('spike_opts',[]);
  $atts=shortcode_atts(['diagram'=>$o['diagram_url']??''],$atts,'spike_intake');
  $diagram=$atts['diagram']?:'https://via.placeholder.com/600x800?text=Pain+Diagram';
  ob_start(); ?>
  <div class="spike-wrap" data-spike>
    <div class="spike-steps headings">
      <div class="spike-step is-active" data-tab="1"><span>1) Patient Info</span></div>
      <div class="spike-step" data-tab="2"><span>2) Medical History</span></div>
      <div class="spike-step" data-tab="3"><span>3) Consent & Sign</span></div>
    </div>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
      <input type="hidden" name="action" value="spike_submit"><?php wp_nonce_field('spike_submit','spike_nonce'); ?>
      <input type="hidden" name="g-recaptcha-response" id="spike_token">
      <?php spike_render_step1(); ?>
      <?php spike_render_medical_step($diagram); ?>
      <?php spike_render_step3(); ?>
    </form>
  </div>
  <?php return ob_get_clean();
}
add_shortcode('spike_intake','spike_shortcode');
