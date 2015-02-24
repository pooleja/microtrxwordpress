<?php // add the admin options page - based on http://ottopress.com/2009/wordpress-settings-api-tutorial/

add_action('admin_menu', 'plugin_admin_add_page');

function plugin_admin_add_page() {
  add_options_page('MicroTrx Bitcoin Paywall Options', 'MicroTrx', 'manage_options', 'microtrx_plugin', 'plugin_options_page');
}

function plugin_options_page() {
?>
<div>
    <h2>MicroTrx Bitcoin Paywall Options</h2>
    Use the configuration options below to configure your Bitcoin address for payments and set default values.

    <form action="options.php" method="post">
      <?php settings_fields('microtrx_plugin_options'); ?>
      <?php do_settings_sections('plugin'); ?>

      <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
    </form>
  </div>
<?php
}

// Add the admin settings
add_action('admin_init', 'plugin_admin_init');

function plugin_admin_init(){
  register_setting( 'microtrx_plugin_options', 'microtrx_options', 'plugin_options_validate' );

  add_settings_section('plugin_main', 'Bitcoin Address Settings', 'bitcoin_wallet_section_text', 'plugin');
  add_settings_field('plugin_public_key_string', 'HD Wallet Public Key', 'public_key_setting_string', 'plugin', 'plugin_main');

  add_settings_section('plugin_amount_section', 'Default Charge Settings', 'amount_section_text', 'plugin');
  add_settings_field('plugin_default_charge_string', 'Default Charge', 'default_charge_setting_string', 'plugin', 'plugin_amount_section');

  add_settings_section('plugin_mode_section', 'Mode Settings', 'mode_section_text', 'plugin');
  add_settings_field('plugin_default_mode_radio', 'Use Paywall for all Posts?', 'default_mode_setting_radio', 'plugin', 'plugin_mode_section');
}


// Public Key section
function bitcoin_wallet_section_text() {
  echo '<p>Enter your HD wallet Public Key.  This should be a standard BIP 38 compliant public key.  Trezor, GreenAddress, and Mycelium are
  examples of wallets that support using these keys.</p>';
}

function public_key_setting_string() {
  $options = get_option('microtrx_options');
  echo "<input id='plugin_public_key_string' name='microtrx_options[public_key_string]' size='40' type='text' value='{$options['public_key_string']}' />";
}


// Default amount section
function amount_section_text() {
  echo '<p>Enter the default amount to charge per Post.  This value can be overriden in each specific Post configuration settings.</p>';
}

function default_charge_setting_string() {
  $options = get_option('microtrx_options');
  $val = '0.00001';
  if($options['default_charge_string'])
    $val = $options['default_charge_string'];

  echo "<input id='plugin_default_charge_string' name='microtrx_options[default_charge_string]' size='40' type='text' value='{$val}' />";
}


// Default mode section
function mode_section_text() {
  echo '<p>Enter the mode of operation.  If you would like to turn on the paywall for all Posts site-wide, choose \'Yes\'.  If you would like to have some posts
  with the paywall disabled, then choose \'No\'.  If set to \'No\', the paywall can be enabled/disabled for each Post. </p>';
}

function default_mode_setting_radio() {
  $options = get_option('microtrx_options');

  $yes = '';
  $no = 'checked';

  if($options[default_mode_string] === 'Yes'){
    $yes = 'checked';
    $no = '';
  }

  echo "<input name='microtrx_options[default_mode_string]' type='radio' value='Yes' {$yes}/>Yes <br />";
  echo "<input name='microtrx_options[default_mode_string]' type='radio' value='No' {$no}/>No";
}


// Property Validations - called when user clicks Save
function plugin_options_validate($input) {
  return $input;
}

// Error messages can be set from this API - http://codex.wordpress.org/Function_Reference/add_settings_error
?>
