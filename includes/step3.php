<?php
if (!defined('ABSPATH')) exit;

function spike_render_step3(){ ?>
  <section data-step="3">
    <div class="field">
      <label>Informed Consent</label>
      <div class="consent-box"><?php echo spike_consent_html(); ?></div>
    </div>
    <div class="grid">
      <div class="field">
        <label>Signature (use finger)</label>
        <canvas id="spike_sig" width="600" height="180"></canvas>
        <input type="hidden" name="signature" id="spike_sig_data">
        <p><button class="btn small secondary" type="button" id="spike_sig_clear">Clear</button></p>
      </div>
      <div class="field"><?php echo spike_input('signer_print','Printed Name','text',true); ?></div>
      <div class="field"><?php echo spike_input('sign_date','Date','date',true); ?></div>
      <div class="field"><?php echo spike_input('witness_name','Witness (optional)','text',false); ?></div>
      <div class="field">
        <label><input type="checkbox" name="patient_copy" value="1"> Email me a copy of my intake (may include sensitive info).</label>
      </div>
    </div>
    <div class="actions">
      <button type="button" class="btn secondary" data-prev>Back</button>
      <button type="submit" class="btn">Submit</button>
    </div>
  </section>
<?php }
