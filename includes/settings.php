<?php
if (!defined('ABSPATH')) exit;

/** Settings menu under Spike */
add_action('admin_menu', function(){
  add_submenu_page(
    'edit.php?post_type=spike_intake',
    'Spike Settings',
    'Settings',
    'manage_options',
    'spike_settings',
    'spike_settings_page'
  );
});

/** Register & sanitize options */
add_action('admin_init', function(){
  register_setting('spike_opts_group','spike_opts','spike_sanitize_opts');
});

function spike_sanitize_opts($in){
  $o = is_array($in)?$in:[];
  $out=[];
  $out['thankyou']         = isset($o['thankyou'])?esc_url_raw(trim($o['thankyou'])):'';
  $out['recipients']       = isset($o['recipients'])?sanitize_text_field($o['recipients']):'';
  $out['diagram_url']      = isset($o['diagram_url'])?esc_url_raw(trim($o['diagram_url'])):'';
  $out['recaptcha_site']   = isset($o['recaptcha_site'])?sanitize_text_field(trim($o['recaptcha_site'])):'';
  $out['recaptcha_secret'] = isset($o['recaptcha_secret'])?sanitize_text_field(trim($o['recaptcha_secret'])):'';
  return $out;
}

/** Settings page */
function spike_settings_page(){
  if (!current_user_can('manage_options')) return;
  wp_enqueue_media(); // enable WP media modal
  $o = get_option('spike_opts',[]);
  $v = fn($k)=>esc_attr($o[$k]??'');
  ?>
  <div class="wrap">
    <h1>Spike Settings</h1>
    <form method="post" action="options.php">
      <?php settings_fields('spike_opts_group'); ?>
      <table class="form-table" role="presentation">
        <tr>
          <th scope="row"><label for="spike_thankyou">Thank-You URL</label></th>
          <td><input type="url" id="spike_thankyou" name="spike_opts[thankyou]" class="regular-text"
                 placeholder="https://example.com/thank-you/" value="<?php echo $v('thankyou'); ?>"></td>
        </tr>
        <tr>
          <th scope="row"><label for="spike_recipients">admin email recipients (comma-separated)</label></th>
          <td>
            <input type="text" id="spike_recipients" name="spike_opts[recipients]" class="regular-text"
                   placeholder="name@example.com, other@example.com" value="<?php echo $v('recipients'); ?>">
            <p class="description">Only these addresses receive admin notifications (PDF attached).</p>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="spike_diagram_url">Pain diagram image</label></th>
          <td>
            <input type="url" id="spike_diagram_url" name="spike_opts[diagram_url]" class="regular-text" value="<?php echo $v('diagram_url'); ?>" readonly>
            <p class="description">Select an image from the Media Library. (Required)</p>
            <p>
              <button type="button" class="button" id="spike_pick_diagram">Choose from Media Library</button>
              <button type="button" class="button button-secondary" id="spike_clear_diagram">Remove</button>
            </p>
          </td>
        </tr>
        <tr>
          <th scope="row">reCAPTCHA v3</th>
          <td>
            <label for="spike_site">Site key</label><br>
            <input type="text" id="spike_site" name="spike_opts[recaptcha_site]" class="regular-text" value="<?php echo $v('recaptcha_site'); ?>"><br><br>
            <label for="spike_secret">Secret key</label><br>
            <input type="text" id="spike_secret" name="spike_opts[recaptcha_secret]" class="regular-text" value="<?php echo $v('recaptcha_secret'); ?>">
          </td>
        </tr>
      </table>
      <?php submit_button('Save Settings'); ?>
    </form>
  </div>
  <script>
  jQuery(function($){
    let frame;
    $('#spike_pick_diagram').on('click', function(e){
      e.preventDefault();
      if(!frame){
        frame = wp.media({ title:'Select Pain Diagram', button:{ text:'Use this image' }, library:{ type:'image' }, multiple:false });
        frame.on('select', function(){
          const a = frame.state().get('selection').first().toJSON();
          $('#spike_diagram_url').val(a.url);
        });
      }
      frame.open();
    });
    $('#spike_clear_diagram').on('click', function(e){ e.preventDefault(); $('#spike_diagram_url').val(''); });
  });
  </script>
  <?php
}
