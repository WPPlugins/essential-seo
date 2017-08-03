<?php
/**
 * SEO settings functionality. Hooks into Settings API and set up fields for setting publish url and webmaster tools etc.
 *
 *
 * @package    EssentialSEO
 * @subpackage Inc
 * @author     James Geiger <james@seamlessthemes.com>
 * @author     Liam Bailey <info@smallcoders.com>
 * @copyright  Copyright (c) 2008 - 2013, James Geiger and Liam Bailey
 * @link       http://seamlessthemes.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
global $essential_settings;
$essential_settings = (get_option('essential-seo-settings')) ? get_option('essential-seo-settings') : essential_seo_get_settings();
//print_r($options);

//Add theme options menu under Settings heading
add_action('admin_menu','addmenu_essential_settings');
function addmenu_essential_settings() {
    add_options_page('Essential SEO','Essential SEO Settings','manage_options','essential_seo_settings','renderpage_settings');
}

//Set default options
function essential_seo_get_settings() {
	$settings = array(
		'google-plus-publisher' => '',
		'verifications' => array(
                    'google_webmaster_tools' => '',
                    'pinterest' => '',
                    'bing' => ''
                )

	);
	return $settings;
}

//add_action('admin_init','setup_essential_settings');

function setup_essential_settings() {
	//Normally this would be added as an activation hook on a plugin, in which case we wouldn't need is_admin() check
	if (is_admin()) {
	$defaults = wswp_getter_setupoptions_themeopts();
     $essential_seo_settings = get_option( 'wswp_themeopts' );
     if ( $wswp_themeopts['restore-defaults'] == true || !is_array($essential_seo_settings)) {
          $essential_seo_settings = $defaults;
          update_option( 'essential-seo-settings', $wswp_themeopts );
     }
     
	}
}
//end initialising defaults

/*Add settings sections and fields to new option group essential-seo-settings*/
add_action('admin_init','register_essential_settings');

function register_essential_settings() {
    $full_settings = essential_seo_get_settings();
	register_setting('essential-seo-settings','essential-seo-settings','validate_settings');
	add_settings_section('section-one', 'Essential SEO Options', 'addsection_settings','essential_seo_settings');
	//Google Plus Publisher
	add_settings_field('settingsfield_gpluspub','Google Plus Publisher URL (G+ profile url)','addsettingsfield_gpluspub','essential_seo_settings','section-one');
	//Verifications
        foreach($full_settings['verifications'] as $key => $value) {
            add_settings_field('settingsfield_verify_'.$key,ucwords(str_replace("_"," ",$key)) . ' verification meta','addsettingsfield_verify_' . $key,'essential_seo_settings','section-one');
        }
}

/*Codes to display settings fields */

function addsection_settings()
{
	?><p>Please setup Essential SEO plugin by entering information in the fields below.</p><?php
}

function addsettingsfield_gpluspub() {
	global $essential_settings;
	echo "<input type='text' size='100' id='settingsfield_gpluspub' name='essential-seo-settings[google-plus-publisher]' value='" . $essential_settings['google-plus-publisher'] . "' />";
}

function addsettingsfield_verify_google_webmaster_tools() {
	global $essential_settings;
	if (isset($_SESSION['errors']['verifications']['google_webmaster_tools'])) {
            echo $_SESSION['errors']['verifications']['google_webmaster_tools'];
        }
	echo "<input type='text' size='100' id='settingsfield_verify_gwt' name='essential-seo-settings[verifications][google_webmaster_tools]' value='" . $essential_settings['verifications']['google_webmaster_tools'] . "' />";
}

function addsettingsfield_verify_pinterest() {
	global $essential_settings;
	if (isset($_SESSION['errors']['verifications']['pinterest'])) {
            echo $_SESSION['errors']['verifications']['pinterest'];
        }
	echo "<input type='text' size='100' id='settingsfield_verify_pinterest' name='essential-seo-settings[verifications][pinterest]' value='" . $essential_settings['verifications']['pinterest'] . "' />";
}

function addsettingsfield_verify_bing() {
	global $essential_settings;
	if (isset($_SESSION['errors']['verifications']['bing'])) {
            echo $_SESSION['errors']['verifications']['bing'];
        }
	echo "<input type='text' size='100' id='settingsfield_verify_bing' name='essential-seo-settings[verifications][bing]' value='" . $essential_settings['verifications']['bing'] . "' />";
}

function validate_settings($input) {
    session_start();
    $output['google-plus-publisher'] = esc_url(strip_tags($input['google-plus-publisher']));
	foreach($input['verifications'] as $key => $verify_meta) {
            if (strstr($verify_meta,"<meta")) {
                $output['verifications'][$key] = $verify_meta;
                
            }
            else {
                $_SESSION['errors']['verifications'][$key] = "Please enter meta tags";
            }
        }
        return apply_filters('validate_settings',$output,$input);
}

/*Function to render options page */
function renderpage_settings() {
?>
	<div class="wrap">
		<div class="icon32" id="icon-themes"></div>
		<h2>Essential SEO Settings</h2>
		<form action="options.php" method="post">
		<?php settings_fields('essential-seo-settings'); ?>
		<?php do_settings_sections('essential_seo_settings'); ?>
		<p class="submit">
			<input name="essential-seo-settings[submit]" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
		</p>
		</form>
	</div>
<?php
}