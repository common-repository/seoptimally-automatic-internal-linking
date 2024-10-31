<?php
/*
Plugin Name: Seoptimally - Automatic Internal Linking
Description: This plugin integrates the Seoptimally JavaScript snippet into all WordPress pages, enhancing SEO by automatically creating internal links based on your content. Internal links enhance SEO, leading to better rankings and improved user experience.
Version: 1.0.0
Author: Seoptimally Team
Author URI: https://seoptimally.com
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Prevent direct file access
if (!defined('ABSPATH')) {
  exit;
}

// Register settings
function seoptimally_register_settings() {
  register_setting('seoptimally_options_group', 'seoptimally_website_id', 'sanitize_text_field');
}

add_action('admin_init', 'seoptimally_register_settings');

// Create menu item for settings page
function seoptimally_register_options_page() {
    add_options_page('Seoptimally Settings', 'Seoptimally', 'manage_options', 'seoptimally', 'seoptimally_options_page');
}

add_action('admin_menu', 'seoptimally_register_options_page');

// Options page content
function seoptimally_options_page() {
  ?>
  <div class="wrap">
    <h2>Seoptimally Settings</h2>
    <div id="seoptimally-instructions" style="background-color: #f9f9f9; border: 1px solid #ddd; padding: 10px 20px; margin-top: 15px; border-radius: 5px;">
      <h3>Getting Started with Seoptimally</h3>
      <p style="font-size: 14px;">Follow these steps to configure your Seoptimally plugin:</p>
      <ol>
        <li><strong>Create an Account:</strong> Visit <a href="https://seoptimally.com" target="_blank" rel="noopener noreferrer">Seoptimally's website</a> and create an account if you haven't already.</li>
        <li><strong>Add Your Site:</strong> In your Seoptimally account, add your WordPress site as a new website.</li>
        <li><strong>Find Website ID:</strong> On your Seoptimally website integration page, locate your Website ID for the WordPress site.</li>
        <li><strong>Enter Website ID:</strong> Copy and paste the Website ID into the field below.</li>
      </ol>
    </div>
    <form method="post" action="options.php" style="margin-top: 20px;">
      <?php settings_fields('seoptimally_options_group'); ?>
      <table class="form-table">
        <tr valign="top">
          <th scope="row"><label for="seoptimally_website_id">Seoptimally Website ID:</label></th>
          <td><input type="text" id="seoptimally_website_id" name="seoptimally_website_id" value="<?php echo esc_attr(get_option('seoptimally_website_id')); ?>" class="regular-text" /></td>
        </tr>
      </table>
      <?php submit_button(); ?>
    </form>
  </div>
  <?php
}

// Enqueue the Seoptimally script
function seoptimally_enqueue_script() {
  $website_id = trim(get_option('seoptimally_website_id'));

  if (!empty($website_id)) {
    // Register a script handle to attach inline JS, set version and load in footer
    wp_register_script('seoptimally-script', false, array(), '1.0.0', true);

    // Add inline script with dynamic website ID and URL
    wp_add_inline_script(
      'seoptimally-script',
      "var url = document.location.origin + document.location.pathname;
      var websiteId = '" . esc_js($website_id) . "';
      var seoptimallyScript = document.createElement('script');
      seoptimallyScript.src = 'https://seoptimally.com/e.js?website=' + websiteId + '&url=' + url;
      seoptimallyScript.async = 1;
      document.getElementsByTagName('head')[0].appendChild(seoptimallyScript);"
    );

    // Enqueue the script to ensure inline JS is included
    wp_enqueue_script('seoptimally-script');
  }
}


add_action('wp_enqueue_scripts', 'seoptimally_enqueue_script');

// Show admin notice if Website ID is missing
function seoptimally_website_id_notice() {
  ?>
  <div class="notice notice-error">
    <p><strong>Seoptimally:</strong> Your plugin requires a Seoptimally Website ID to function properly. Please <a href="admin.php?page=seoptimally">configure it in the Seoptimally settings</a>.</p>
  </div>
  <?php
}

// Show the admin notice if Website ID is missing
if (!get_option('seoptimally_website_id')) {
  add_action('admin_notices', 'seoptimally_website_id_notice');
}

// Add a link to the plugin action links
function seoptimally_plugin_action_links($links) {
  $settings_link = '<a href="admin.php?page=seoptimally">Settings</a>';
  array_unshift($links, $settings_link);
  return $links;
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'seoptimally_plugin_action_links');
