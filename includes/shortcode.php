<?php
if (!defined('ABSPATH')) exit;

function spike_assets(){
  wp_enqueue_style('spike-css', SPIKE_URL.'assets/css/frontend.css', [], SPIKE_VER);
  wp_enqueue_script('spike-js',  SPIKE_URL.'assets/js/frontend.js',  [], SPIKE_VER, true);
  $o = get_option('spike_opts', []);
  wp_localize_script('spike-js','SPIKE',['siteKey'=>$o['recaptcha_site'] ?? '','maxPts'=>10]);
}
add_action('wp_enqueue_scripts','spike_assets');

function spike_shortcode($atts = []){
  $o = get_option('spike_opts', []);
  $atts = shortcode_atts(['diagram' => $o['diagram_url'] ?? ''], $atts, 'spike_intake');
  $diagram = $atts['diagram'] ?: 'https://via.placeholder.com/600x800?text=Pain+Diagram';

  ob_start(); ?>
  <div class="spike-wrap" data-spike>
    <div class="spike-steps">
      <div class="spike-step is-active" data-tab="1">1) Patient Info</div>
      <div class="spike-step" data-tab="2">2) Pain Diagram</div>
      <div class="spike-step" data-tab="3">3) Consent & Sign</div>
    </div>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
      <input type="hidden" name="action" value="spike_submit"><?php wp_nonce_field('spike_submit','spike_nonce'); ?>
      <input type="hidden" name="g-recaptcha-response" id="spike_token">

      <!-- Step 1 -->
      <section data-step="1">
        <div class="grid">
          <?php
          $fields1=[
            ['first_name','First Name',1,'text'],['last_name','Last Name',1,'text'],
            ['middle_initial','M.I.',0,'text'],['dob','DOB',0,'date'],
            ['address1','Address',1,'text'],['address2','Address Line 2',0,'text'],
            ['city','City',1,'text']
          ];
          foreach($fields1 as $f){
            echo '<div class="field"><label for="'.$f[0].'">'.$f[1].($f[2]?' <span>*</span>':'').'</label><input type="'.$f[3].'" id="'.$f[0].'" name="'.$f[0].'" '.($f[2]?'required':'').'></div>';
          }
          // State dropdown after City (defaults to MI)
          echo spike_state_select('MI');

          $fields2=[
            ['zip','Zip',1,'text'],['primary_phone','Primary Phone',1,'tel'],['email','Email',1,'email'],
            ['emergency_name','Emergency Contact Name',0,'text'],['emergency_relationship','Emergency Relationship',0,'text'],
            ['emergency_phone','Emergency Phone',0,'tel'],['primary_insurance','Primary Insurance',0,'text'],
            ['policy_number','Policy #',0,'text'],['subscriber_name','Subscriber Name',0,'text'],['subscriber_dob','Subscriber DOB',0,'date']
          ];
          foreach($fields2 as $f){
            echo '<div class="field"><label for="'.$f[0].'">'.$f[1].($f[2]?' <span>*</span>':'').'</label><input type="'.$f[3].'" id="'.$f[0].'" name="'.$f[0].'" '.($f[2]?'required':'').'></div>';
          }
          ?>
        </div>
        <div class="actions">
          <button type="button" class="btn secondary" data-prev disabled>Back</button>
          <button type="button" class="btn" data-next>Next</button>
        </div>
      </section>

      <!-- Step 2 -->
      <section data-step="2">
        <div class="field">
          <label>Pain Diagram (tap up to 10 spots). Use Clear to redo.</label>
          <div class="diagram">
            <div class="toggle">
              <button class="btn small secondary" data-clear type="button">Clear</button>
            </div>
            <div class="diagram-box">
              <img id="spike-diagram" src="<?php echo esc_url($diagram); ?>" alt="Pain diagram">
              <canvas id="spike-layer"></canvas>
            </div>
            <input type="hidden" name="pain_points" id="spike_points" value="[]">
          </div>
        </div>
        <div class="actions">
          <button type="button" class="btn secondary" data-prev>Back</button>
          <button type="button" class="btn" data-next>Next</button>
        </div>
      </section>

      <!-- Step 3 -->
      <section data-step="3">
        <div class="field">
          <label>Informed Consent</label>
          <div class="consent-box">
            <p>Please read this entire document before signing. See clinic for full Notice of Privacy Practices.</p>
          </div>
        </div>

        <div class="grid">
          <div class="field">
            <label>Signature (use finger)</label>
            <canvas id="spike_sig" width="600" height="180"></canvas>
            <input type="hidden" name="signature" id="spike_sig_data">
            <p><button class="btn small secondary" type="button" id="spike_sig_clear">Clear</button></p>
          </div>
          <div class="field">
            <label for="signer_print">Printed Name</label>
            <input type="text" id="signer_print" name="signer_print" required>
          </div>
          <div class="field">
            <label for="sign_date">Date</label>
            <input type="date" id="sign_date" name="sign_date" required>
          </div>
          <div class="field">
            <label for="witness_name">Witness (optional)</label>
            <input type="text" id="witness_name" name="witness_name">
          </div>
          <div class="field">
            <label><input type="checkbox" name="patient_copy" value="1"> Email me a copy of my intake (may include sensitive info).</label>
          </div>
        </div>

        <div class="actions">
          <button type="button" class="btn secondary" data-prev>Back</button>
          <button type="submit" class="btn">Submit</button>
        </div>
      </section>
    </form>
  </div>
  <?php return ob_get_clean();
}
add_shortcode('spike_intake','spike_shortcode');
