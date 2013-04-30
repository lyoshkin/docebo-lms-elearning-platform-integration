<?php
/*
Plugin Name: Docebo
Description: Integrate your Docebo account and users with a WordPress-powered website 
Plugin URI: http://wordpress.org/plugins/docebo
Version: 1.0
Author: Docebo Srl.
Author URI: http://www.docebo.com
Textdomain: docebo
*/

/* 
Copyright 2013 Docebo Srl. (http://www.docebo.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

function docebo_init() {
	/* --------------------------------------------------- */
	/* Global variables 
	/* --------------------------------------------------- */
		global $dwp_prefix, $dpw_plugin_name, $dwp_options, $current_user;
		
		$dwp_prefix = 'dwp_';
		$dwp_plugin_name = 'Docebo WordPress Plugin';

		// Retrieve global variables (Docebo Plugin settings, current user etc.)
		$dwp_options = get_option('dwp_settings');

		$current_user = wp_get_current_user();


	/* --------------------------------------------------- */
	/* Includes
	/* --------------------------------------------------- */
		include_once( 'includes/docebo-api.php' ); // Load the Docebo API

	/* --------------------------------------------------- */
	/* Localize the plugin
	/* --------------------------------------------------- */
		function dwp_init() {
			load_plugin_textdomain( 'docebo', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
		}
		add_action('plugins_loaded', 'dwp_init');

	/* --------------------------------------------------- */
	/* Load styles and scripts for the front-end...
	/* --------------------------------------------------- */
		function dwp_load_frontend_scripts() {
			wp_enqueue_style('dwp-front', plugin_dir_url( __FILE__ ) . 'includes/css/front.css'); // Front-end stylesheet
		}
		add_action('wp_enqueue_scripts', 'dwp_load_frontend_scripts');

	/* --------------------------------------------------- */
	/* ...and for the admin dashboard
	/* --------------------------------------------------- */
		function dwp_load_admin_scripts() {
			global $pagenow;
			if ($pagenow == 'admin.php') {
				wp_enqueue_script('dwp-admin-script', plugin_dir_url( __FILE__ ) . 'includes/js/dwp-admin.js'); // Admin dashboard javascript
				wp_enqueue_style('dwp-admin-style', plugin_dir_url( __FILE__ ) . 'includes/css/admin.css'); // Admin dashboard stylesheet
			}
		
			wp_enqueue_script('thickbox'); // Thickbox JS (for image upload) 
			wp_enqueue_style('thickbox'); // Thichbox CSS (for image upload)
			wp_enqueue_script('media-upload'); // Media upload script 
			wp_enqueue_style( 'farbtastic' ); // Colorpicker CSS
			wp_enqueue_script( 'farbtastic' ); // Colorpicker JS
			wp_enqueue_script( 'jquery-validate', plugin_dir_url( __FILE__ ) . 'includes/js/jquery.validate.min.js', array( 'jquery' ), '1.11.0' ); // Validation
		}
		add_action ('admin_enqueue_scripts', 'dwp_load_admin_scripts');
	
	
	/* ------------------------------------------------------*/
	/* The display functions for the WP admin pages
	/* ------------------------------------------------------*/

		// 1. Login Box configuration page
		function dwp_admin_page() {
	
			global $dwp_options, $current_user;
			$dwp_login_options = get_option('dwp_login_settings');
		
			ob_start(); ?>

				<div class="wrap docebo">
					<div id="icon-docebo-settings" class="icon32"><br></div>
					<h2><?php _e('Docebo - Login Box Configuration', 'docebo'); ?></h2>
					
					<?php if ($dwp_options['docebo_scenario'] == 'user_scenario2') { ?>
					<p class="docebo-note"><?php _e('<a href="' . site_url() . '/wp-admin/admin.php?page=docebo-signon">Single Sign-on</a> is also available - your users may use that to log in to your LMS.', 'docebo'); ?></p>
					<?php } ?>
					
					<hr/>
					
					<!-- Display settings form -->
					<div id="docebo-settings-panel">
					<p><?php _e('Use this box if you want to allow your users to log in to Docebo LMS directly from your WordPress website BUT not with the single sing-on system.', 'docebo'); ?></p>
										
					<form method="post" action="options.php"> <!-- Form action is set to options.php, so settings will be saved in the _options table -->
			
						<?php settings_fields('dwp_login_settings_group'); ?>
					
						<fieldset>
							<label class="textinput" for="dwp_login_settings[logo]"><?php _e('Login Box logo', 'docebo'); ?></label>
							<input type="text" id="logo_url" name="dwp_login_settings[logo]" value="<?php echo esc_url( $dwp_login_options['logo'] ); ?>" />  
							<input id="upload_logo_button" type="button" class="button" value="<?php _e( 'Upload Logo', 'docebo' ); ?>" /> <em><?php _e('Leave this field empty to use Docebo logo', 'docebo'); ?></em>
						</fieldset>
						<br class="clear">
						
						<fieldset>
							<label class="textinput" for="dwp_login_settings[no_logo]"><?php _e('Do not use logo', 'docebo'); ?></label>
							<input type="checkbox" id="dwp_login_settings[no_logo]"  name="dwp_login_settings[no_logo]" class="textinput" 
								value="yes" <?php if (isset($dwp_login_options['no_logo']) && ($dwp_login_options['no_logo'] == 'yes')) { echo 'checked="checked"'; } ?> /><br/>
						</fieldset>
						<br class="clear">
												
						<fieldset>
							<label class="textinput" for="dwp_login_settings[login_text]"><?php _e('Intro text', 'docebo'); ?></label>
							<textarea id="dwp_login_settings[login_text]"  name="dwp_login_settings[login_text]" class="textinput" rows="4" cols="47"><?php echo $dwp_login_options['login_text']; ?></textarea>
						</fieldset>
						<br class="clear">
			
						<fieldset>
							<label class="textinput" for="dwp_login_settings[login_box_width]"><?php _e('Box width', 'docebo'); ?></label>
							<input type="text" id="dwp_login_settings[login_box_width]" name="dwp_login_settings[login_box_width]" class="textinput" value="<?php echo $dwp_login_options['login_box_width']; ?>" /><span class="field-after">px</span>
						</fieldset>
						<br class="clear">
						
						<fieldset>
							<label class="textinput" for="dwp_login_settings[login_box_height]"><?php _e('Box height', 'docebo'); ?></label>
							<input type="text" id="dwp_login_settings[login_box_height]" name="dwp_login_settings[login_box_height]" class="textinput" value="<?php echo $dwp_login_options['login_box_height']; ?>" /><span class="field-after">px</span>
						</fieldset>
						<br class="clear">
			
						<fieldset>
							<label class="textinput" for="dwp_login_settings[login_button_text]"><?php _e('Login button text', 'docebo'); ?></label>
							<input type="text" id="dwp_login_settings[login_button_text]" name="dwp_login_settings[login_button_text]" class="textinput" value="<?php echo $dwp_login_options['login_button_text']; ?>" />
						</fieldset>
						<br class="clear">
						
						<fieldset>
							<label class="textinput" for="dwp_login_settings[login_button_pos]"><?php _e('Button position', 'docebo'); ?></label>
							<input type="radio" id="dwp_login_settings[login_button_pos]"  name="dwp_login_settings[login_button_pos]" class="textinput" 
								value="left" <?php if (isset($dwp_login_options['login_button_pos']) && $dwp_login_options['login_button_pos'] == 'left') { echo 'checked="checked"'; } ?> /><?php _e('Left', 'docebo');?>
							<input type="radio" id="dwp_login_settings[login_button_pos]"  name="dwp_login_settings[login_button_pos]" class="textinput" 
								value="center" <?php if (isset($dwp_login_options['login_button_pos']) && $dwp_login_options['login_button_pos'] == 'center') { echo 'checked="checked"'; } ?> /><?php _e('Center', 'docebo');?>
							<input type="radio" id="dwp_login_settings[login_button_pos]"  name="dwp_login_settings[login_button_pos]" class="textinput" 
								value="right" <?php if (isset($dwp_login_options['login_button_pos']) && $dwp_login_options['login_button_pos'] == 'right') { echo 'checked="checked"'; } ?> /><?php _e('Right', 'docebo');?><br/>
						</fieldset>
						<br class="clear">
			
						<p></p>
			
						
						<h3><?php _e('Customize your Login box', 'docebo'); ?></h3>
						
						<script type="text/javascript">
						
							jQuery(document).ready(function() {  	
								// Color picker wheels 
								
								jQuery('#dwp_login_settings\\[text_color\\]').hide();
								jQuery('#dwp_login_settings\\[text_color\\]').farbtastic('#text_color');            
								jQuery('#text_color').focus(function() { 
									jQuery('#dwp_login_settings\\[text_color\\]').show();
								}).blur(function() {
									jQuery('#dwp_login_settings\\[text_color\\]').hide();
								});
	
								jQuery('#dwp_login_settings\\[border_color\\]').hide();
								jQuery('#dwp_login_settings\\[border_color\\]').farbtastic('#border_color');            
								jQuery('#border_color').focus(function() { 
									jQuery('#dwp_login_settings\\[border_color\\]').show();
								}).blur(function() {
									jQuery('#dwp_login_settings\\[border_color\\]').hide();
								});
								
								jQuery('#dwp_login_settings\\[bg_color\\]').hide();
								jQuery('#dwp_login_settings\\[bg_color\\]').farbtastic('#bg_color');            
								jQuery('#bg_color').focus(function() { 
									jQuery('#dwp_login_settings\\[bg_color\\]').show();
								}).blur(function() {
									jQuery('#dwp_login_settings\\[bg_color\\]').hide();
								});
									
								jQuery('#dwp_login_settings\\[buttonbg_color\\]').hide();
								jQuery('#dwp_login_settings\\[buttonbg_color\\]').farbtastic('#buttonbg_color');            
								jQuery('#buttonbg_color').focus(function() { 
									jQuery('#dwp_login_settings\\[buttonbg_color\\]').show();
								}).blur(function() {
									jQuery('#dwp_login_settings\\[buttonbg_color\\]').hide();
								});
								
								jQuery('#dwp_login_settings\\[buttonborder_color\\]').hide();
								jQuery('#dwp_login_settings\\[buttonborder_color\\]').farbtastic('#buttonborder_color');            
								jQuery('#buttonborder_color').focus(function() { 
									jQuery('#dwp_login_settings\\[buttonborder_color\\]').show();
								}).blur(function() {
									jQuery('#dwp_login_settings\\[buttonborder_color\\]').hide();
								});
											
								jQuery('#dwp_login_settings\\[buttontext_color\\]').hide();
								jQuery('#dwp_login_settings\\[buttontext_color\\]').farbtastic('#buttontext_color');            
								jQuery('#buttontext_color').focus(function() { 
									jQuery('#dwp_login_settings\\[buttontext_color\\]').show();
								}).blur(function() {
									jQuery('#dwp_login_settings\\[buttontext_color\\]').hide();
								});
								
							});
						</script>
						
						<a href="<?php echo home_url() . '/wp-admin/plugin-editor.php?file=docebo%2Fincludes%2Fcss%2Ffront.css&plugin=docebo%2Fdocebo.php'; ?>"><?php _e('Manually edit CSS file', 'docebo'); ?></a>
						<br class="clear">
						
						<fieldset>
							<label class="textinput" for="dwp_login_settings[text_color]"><?php _e('Text color', 'docebo'); ?></label>
							<input type="text" id="text_color" value="<?php if($dwp_login_options['text_color']) { echo $dwp_login_options['text_color']; } else { echo '#333333'; } ?>" name="dwp_login_settings[text_color]" />
							<div id="dwp_login_settings[text_color]" class="colorpckr"></div>						
						</fieldset>
						<br class="clear">
						
						<fieldset>
							<label class="textinput" for="dwp_login_settings[border_color]"><?php _e('Border color', 'docebo'); ?></label>
							<input type="text" id="border_color" value="<?php if($dwp_login_options['border_color']) { echo $dwp_login_options['border_color']; } else { echo '#333333'; } ?>" name="dwp_login_settings[border_color]" /> <em><?php _e('Leave empty for no border.', 'docebo'); ?></em>
							<div id="dwp_login_settings[border_color]" class="colorpckr"></div>						
						</fieldset>
						<br class="clear">
						
						<fieldset>
							<label class="textinput" for="dwp_login_settings[bg_color]"><?php _e('Background color', 'docebo'); ?></label>
							<input type="text" id="bg_color" value="<?php if($dwp_login_options['bg_color']) { echo $dwp_login_options['bg_color']; } else { echo '#333333'; } ?>" name="dwp_login_settings[bg_color]" />
							<div id="dwp_login_settings[bg_color]" class="colorpckr"></div>						
						</fieldset>
						<br class="clear">
						
						<fieldset>
							<label class="textinput" for="dwp_login_settings[box_shadow]"><?php _e('Use box shadow?', 'docebo'); ?></label>
							<input type="checkbox" id="dwp_login_settings[box_shadow]"  name="dwp_login_settings[box_shadow]" class="textinput" 
								value="yes" <?php if (isset($dwp_login_options['box_shadow']) && $dwp_login_options['box_shadow'] == 'yes') { echo 'checked="checked"'; } ?> /><br/>
						</fieldset>
						<br class="clear">
						
						<fieldset>
							<label class="textinput" for="dwp_login_settings[buttonbg_color]"><?php _e('Button background color', 'docebo'); ?></label>
							<input type="text" id="buttonbg_color" value="<?php if($dwp_login_options['buttonbg_color']) { echo $dwp_login_options['buttonbg_color']; } else { echo '#333333'; } ?>" name="dwp_login_settings[buttonbg_color]" /> 
							<div id="dwp_login_settings[buttonbg_color]" class="colorpckr"></div>						
						</fieldset>
						<br class="clear">
						
						<fieldset>
							<label class="textinput" for="dwp_login_settings[buttonborder_color]"><?php _e('Button border color', 'docebo'); ?></label>
							<input type="text" id="buttonborder_color" value="<?php if($dwp_login_options['buttonborder_color']) { echo $dwp_login_options['buttonborder_color']; } else { echo '#333333'; } ?>" name="dwp_login_settings[buttonborder_color]" /> <em><?php _e('Leave empty for no border.', 'docebo'); ?></em>
							<div id="dwp_login_settings[buttonborder_color]" class="colorpckr"></div>						
						</fieldset>
						<br class="clear">
						
						<fieldset>
							<label class="textinput" for="dwp_login_settings[buttontext_color]"><?php _e('Button text color', 'docebo'); ?></label>
							<input type="text" id="buttontext_color" value="<?php if($dwp_login_options['buttontext_color']) { echo $dwp_login_options['buttontext_color']; } else { echo '#333333'; } ?>" name="dwp_login_settings[buttontext_color]" />
							<div id="dwp_login_settings[buttontext_color]" class="colorpckr"></div>						
						</fieldset>
						<br class="clear">
						
						<br class="clear">	
						
						<input type="submit" id="submit_dlogin_form" class="button-primary" value="<?php _e('Save changes', 'docebo'); ?>" />
					</form>
				</div><!-- end #docebo-settings-panel-->
				
				<!-- Generate a preview -->
				<div id="docebo-preview-panel">
					
					<h3><?php _e('Preview', 'docebo'); ?></h3>
				
					<div id="docebo-login-box" 
						style="background: <?php echo $dwp_login_options['bg_color'];?>; width: <?php echo $dwp_login_options['login_box_width'];?>px; height: <?php echo $dwp_login_options['login_box_height'];?>px; color: <?php echo $dwp_login_options['text_color']; ?>; border: 1px solid <?php echo $dwp_login_options['border_color']; ?>; <?php if (isset($dwp_login_options['box_shadow']) && $dwp_login_options['box_shadow'] == 'yes') { echo 'box-shadow: 0 0 8px rgba(0,0,0,0.20)'; } ?>">
						<div class="login-container">
							<div class="logo-holder">
								<?php
								$logo = (isset($dwp_login_options['logo']) && $dwp_login_options['logo']) ? $dwp_login_options['logo'] : '';
								$no_logo = ((isset($dwp_login_options['no_logo']) && $dwp_login_options['no_logo'] == 'yes') ? true : false);
								if($logo && !$no_logo) { 
									echo '<img src="' . $logo . '" />'; 
								} elseif(!$logo && !$no_logo) { 
									echo '<img src="' . plugin_dir_url( __FILE__ ) . 'includes/images/docebo_logo.png' . '" />';
								} elseif($no_logo) { } ?>
							</div>
							
							<?php if($dwp_login_options['login_text']) { echo '<p>' . $dwp_login_options['login_text'] . '</p>'; } ?>
							
							<form method="post" class="user-login" target="_blank" action="<?php echo admin_url( 'admin.php?page=docebo-sso-login' ) ?>">
								
								<input type="text" name="userid" class="docebo-text" value="" placeholder="<?php _e('Username', 'docebo'); ?>" />				
								<input type="password" name="pwd" class="docebo-text" value="" placeholder="<?php _e('Password', 'docebo'); ?>" />
			
								<input type="hidden" name="docebo_address" value="<?php echo $dwp_options['docebo_address']; ?>" />
								<input type="hidden" name="docebo_secret" value="<?php echo $dwp_options['docebo_secret']; ?>" />
								<input type="hidden" name="site_url" value="<?php echo site_url(); ?>" />
							
								<div class="cloud-button" style="text-align: <?php if(isset($dwp_login_options['login_button_pos'])) echo $dwp_login_options['login_button_pos']; ?>;">
									<input type="submit" name="submit" class="docebo-button" style="background: <?php echo $dwp_login_options['buttonbg_color'];?>; color: <?php echo $dwp_login_options['buttontext_color']; ?>; border: 1px solid <?php echo $dwp_login_options['buttonborder_color']; ?>;" value="<?php echo ($dwp_login_options['login_button_text'] ? $dwp_login_options['login_button_text'] : __('SIGN IN', 'docebo')); ?>" />
								</div>
							</form>
							
						</div>
					</div>
					<p>To insert the login box in your pages use the shortcode [docebo_login] or insert it from the widget area.</p>
				</div><!-- end #docebo-preview-panel -->
					
			</div>
	
			<?php echo ob_get_clean();
		} // end Main admin page with login form


		// 2. General Settings page
		function dwp_settings_page() {
	
			global $dwp_options, $current_user;
	
			ob_start(); ?>
	
			<div class="wrap docebo">
				<div id="icon-docebo-settings" class="icon32"><br></div>
				<h2><?php _e('Docebo - General Settings', 'docebo'); ?></h2>
				<p></p>
				
				<?php settings_errors(); ?>
		
				<form method="post" action="options.php"> <!-- Form action is set to options.php, so settings will be saved in the _options table -->
			
					<!-- This snippet generates the "Settings saved" message -->
					<?php settings_fields('dwp_settings_group'); ?>
			
					<fieldset class="sync-scenarios-wrapper">
						<label class="textinput" for="dwp_settings[docebo_scenario]"><?php _e('Choose your users\' scenario', 'docebo'); ?></label>
						
							<input type="radio" id="dwp_settings[docebo_scenario]"  name="dwp_settings[docebo_scenario]" class="textinput" 
								value="user_scenario1" <?php if ($dwp_options['docebo_scenario'] == 'user_scenario1') { echo 'checked="checked"'; } ?> /> I already have users in Docebo and I want to keep them separated from WordPress users<br/>
							<input type="radio" id="dwp_settings[docebo_scenario]"  name="dwp_settings[docebo_scenario]" class="textinput" 
								value="user_scenario2" <?php if ($dwp_options['docebo_scenario'] == 'user_scenario2') { echo 'checked="checked"'; } ?> /> I already have users in WordPress and I want to create them in Docebo, so they can log in with the same username and password (Single Sign-on System)
						
						
					</fieldset>
					<br class="clear">
			
					<p></p>
					<hr/>
					<p></p>
			
					<fieldset>
						<label class="textinput" for="dwp_settings[docebo_address]"><?php _e('Docebo Cloud Address', 'docebo'); ?></label>
						<input type="text" id="dwp_settings[docebo_address]"  name="dwp_settings[docebo_address]" class="textinput" value="<?php echo $dwp_options['docebo_address']; ?>" />
					</fieldset>
					<br class="clear">
			
					<fieldset>
						<label class="textinput" for="dwp_settings[docebo_key]"><?php _e('Key', 'docebo'); ?></label>
						<input type="text" id="dwp_settings[docebo_key]" name="dwp_settings[docebo_key]" class="textinput" value="<?php echo $dwp_options['docebo_key']; ?>" />
					</fieldset>
					<br class="clear">
			
					<fieldset>
						<label class="textinput" for="dwp_settings[docebo_secret]"><?php _e('Secret Key', 'docebo'); ?></label>
						<input type="text" id="dwp_settings[docebo_secret]" name="dwp_settings[docebo_secret]" class="textinput" value="<?php echo $dwp_options['docebo_secret']; ?>" />
					</fieldset>
					<br class="clear">
					
					<fieldset>
						<label class="textinput" for="dwp_settings[docebo_sso]"><?php _e('SSO', 'docebo'); ?></label>
						<input type="text" id="dwp_settings[docebo_sso]" name="dwp_settings[docebo_sso]" class="textinput" value="<?php echo $dwp_options['docebo_sso']; ?>" />
					</fieldset>
					<br class="clear">
								
					<input type="submit" class="button-primary" value="<?php _e('Save changes', 'docebo'); ?>" />
				</form>
			</div>
	
			<?php echo ob_get_clean();
		} // end General Settings page


		// 3. Single Sign-on page
		function dwp_signon_page() {
			global $dwp_options, $current_user;
			$dwp_sso_options = get_option('dwp_sso_settings');
			
			$button_link = Api::sso(dwp_get_docebo_username());
				
			ob_start(); ?>
	
			<div class="wrap docebo">
				<div id="icon-docebo-settings" class="icon32"><br></div>
				<h2><?php _e('Docebo - Single Sign-on Configuration', 'docebo'); ?></h2>
				
					<?php if ($dwp_options['docebo_scenario'] == 'user_scenario2') { 
					
					//This snippet generates the "Settings saved" message 
					settings_errors(); ?>
					
					<p class="docebo-note"><?php _e('Your WordPress and Docebo LMS credentials are identical - your users can use the Single Sign-on system.', 'docebo'); ?></p>
					
					<p><?php _e('Use this box if you already use a login system into your wordpress website and you want to allow your users to log in to Docebo LMS with the single sing-on system.', 'docebo'); ?></p>
						
					<h3><?php _e('Text link', 'docebo'); ?></h3>
					<p><?php _e('To insert a link that will redirect you to Docebo Cloud use the following shortcode:[docebo_sso_link text="YOUR LINK TEXT HERE"]', 'docebo'); ?></p>
					<p><?php _e('Once you have saved the changes, you can try the link', 'docebo'); ?> <a href="<?php echo $button_link; ?>" target="_blank"><?php _e('here', 'docebo');?></a>.</p>
						
					<hr/><!-- end the full-width upper part -->
					<p></p>
				
				<!-- Display the settings form  -->
				<div id="docebo-settings-panel">
					<h3><?php _e('SSO box', 'docebo'); ?></h3>
					<p><?php _e('You can also display a box with your latest progress and available courses from Docebo Cloud: use the [docebo_sso] shortcode or the Docebo SSO Widget.', 'docebo'); ?></p>
					
					<form method="post" action="options.php"> <!-- Form action is set to options.php, so settings will be saved in the _options table -->
			
						<?php settings_fields('dwp_sso_settings_group'); ?>
			
						<fieldset>
							<label class="textinput" for="dwp_sso_settings[sso_title]"><?php _e('Box title', 'docebo'); ?></label>
							<input type="text" id="dwp_sso_settings[sso_title]"  name="dwp_sso_settings[sso_title]" class="textinput" value="<?php echo $dwp_sso_options['sso_title']; ?>" />
						</fieldset>
						<br class="clear">
						
						<fieldset>
							<label class="textinput" for="dwp_sso_settings[sso_text]"><?php _e('Intro text', 'docebo'); ?></label>
							<textarea id="dwp_sso_settings[sso_text]"  name="dwp_sso_settings[sso_text]" class="textinput" rows="4" cols="47"><?php echo $dwp_sso_options['sso_text']; ?></textarea>
						</fieldset>
						<br class="clear">
			
						<fieldset>
							<label class="textinput" for="dwp_sso_settings[sso_box_width]"><?php _e('Box width', 'docebo'); ?></label>
							<input type="text" id="dwp_sso_settings[sso_box_width]" name="dwp_sso_settings[sso_box_width]" class="textinput" value="<?php echo $dwp_sso_options['sso_box_width']; ?>" /><span class="field-after">px</span>
						</fieldset>
						<br class="clear">
						
						<fieldset>
							<label class="textinput" for="dwp_sso_settings[sso_box_height]"><?php _e('Box height', 'docebo'); ?></label>
							<input type="text" id="dwp_sso_settings[sso_box_height]" name="dwp_sso_settings[sso_box_height]" class="textinput" value="<?php echo $dwp_sso_options['sso_box_height']; ?>" /><span class="field-after">px</span>
						</fieldset>
						<br class="clear">
			
						<fieldset>
							<label class="textinput" for="dwp_sso_settings[sso_button_text]"><?php _e('SSO button text', 'docebo'); ?></label>
							<input type="text" id="dwp_sso_settings[sso_button_text]" name="dwp_sso_settings[sso_button_text]" class="textinput" value="<?php echo $dwp_sso_options['sso_button_text']; ?>" />
						</fieldset>
						<br class="clear">
						
						<fieldset>
							<label class="textinput" for="dwp_sso_settings[sso_button_pos]"><?php _e('Button position', 'docebo'); ?></label>
							<input type="radio" id="dwp_sso_settings[sso_button_pos]"  name="dwp_sso_settings[sso_button_pos]" class="textinput" 
								value="left" <?php if ($dwp_sso_options['sso_button_pos'] == 'left') { echo 'checked="checked"'; } ?> /><?php _e('Left', 'docebo');?>
							<input type="radio" id="dwp_sso_settings[sso_button_pos]"  name="dwp_sso_settings[sso_button_pos]" class="textinput" 
								value="center" <?php if ($dwp_sso_options['sso_button_pos'] == 'center') { echo 'checked="checked"'; } ?> /><?php _e('Center', 'docebo');?>
							<input type="radio" id="dwp_sso_settings[sso_button_pos]"  name="dwp_sso_settings[sso_button_pos]" class="textinput" 
								value="right" <?php if ($dwp_sso_options['sso_button_pos'] == 'right') { echo 'checked="checked"'; } ?> /><?php _e('Right', 'docebo');?><br/>
						</fieldset>
						<br class="clear">
			
						<p></p>
				
						<fieldset>
							<label class="textinput" for="dwp_sso_settings[show_enrolled]"><?php _e('Show courses you are enrolled in', 'docebo'); ?></label>
							<input type="checkbox" id="dwp_sso_settings[show_enrolled]"  name="dwp_sso_settings[show_enrolled]" class="textinput" 
								value="yes" <?php if ($dwp_sso_options['show_enrolled'] == 'yes') { echo 'checked="checked"'; } ?> /><br/>
						</fieldset>
						<br class="clear">
						
						<fieldset>
							<label class="textinput" for="dwp_sso_settings[show_last_attended]"><?php _e('Show last attended course', 'docebo'); ?></label>
							<input type="checkbox" id="dwp_sso_settings[show_last_attended]"  name="dwp_sso_settings[show_last_attended]" class="textinput" 
								value="yes" <?php if ($dwp_sso_options['show_last_attended'] == 'yes') { echo 'checked="checked"'; } ?> /><br/>
						</fieldset>
						<br class="clear">
						
						<h3><?php _e('Customize your SSO box', 'docebo'); ?></h3>
						
						<script type="text/javascript">
							jQuery(document).ready(function() {
								// Color picker wheels
								jQuery('#dwp_sso_settings\\[text_color\\]').hide();
								jQuery('#dwp_sso_settings\\[text_color\\]').farbtastic('#text_color');            
								jQuery('#text_color').focus(function() { 
									jQuery('#dwp_sso_settings\\[text_color\\]').show();
								}).blur(function() {
									jQuery('#dwp_sso_settings\\[text_color\\]').hide();
								});
								
								jQuery('#dwp_sso_settings\\[title_color\\]').hide();
								jQuery('#dwp_sso_settings\\[title_color\\]').farbtastic('#title_color');            
								jQuery('#title_color').focus(function() { 
									jQuery('#dwp_sso_settings\\[title_color\\]').show();
								}).blur(function() {
									jQuery('#dwp_sso_settings\\[title_color\\]').hide();
								});
								
								jQuery('#dwp_sso_settings\\[border_color\\]').hide();
								jQuery('#dwp_sso_settings\\[border_color\\]').farbtastic('#border_color');            
								jQuery('#border_color').focus(function() { 
									jQuery('#dwp_sso_settings\\[border_color\\]').show();
								}).blur(function() {
									jQuery('#dwp_sso_settings\\[border_color\\]').hide();
								});
								
								jQuery('#dwp_sso_settings\\[bg_color\\]').hide();
								jQuery('#dwp_sso_settings\\[bg_color\\]').farbtastic('#bg_color');            
								jQuery('#bg_color').focus(function() { 
									jQuery('#dwp_sso_settings\\[bg_color\\]').show();
								}).blur(function() {
									jQuery('#dwp_sso_settings\\[bg_color\\]').hide();
								});
								
								jQuery('#dwp_sso_settings\\[buttonbg_color\\]').hide();
								jQuery('#dwp_sso_settings\\[buttonbg_color\\]').farbtastic('#buttonbg_color');            
								jQuery('#buttonbg_color').focus(function() { 
									jQuery('#dwp_sso_settings\\[buttonbg_color\\]').show();
								}).blur(function() {
									jQuery('#dwp_sso_settings\\[buttonbg_color\\]').hide();
								});
								
								jQuery('#dwp_sso_settings\\[buttonborder_color\\]').hide();
								jQuery('#dwp_sso_settings\\[buttonborder_color\\]').farbtastic('#buttonborder_color');            
								jQuery('#buttonborder_color').focus(function() { 
									jQuery('#dwp_sso_settings\\[buttonborder_color\\]').show();
								}).blur(function() {
									jQuery('#dwp_sso_settings\\[buttonborder_color\\]').hide();
								});	
								
								jQuery('#dwp_sso_settings\\[buttontext_color\\]').hide();
								jQuery('#dwp_sso_settings\\[buttontext_color\\]').farbtastic('#buttontext_color');            
								jQuery('#buttontext_color').focus(function() { 
									jQuery('#dwp_sso_settings\\[buttontext_color\\]').show();
								}).blur(function() {
									jQuery('#dwp_sso_settings\\[buttontext_color\\]').hide();
								});
	
							}); 	
						</script>
						
						
						<a href="<?php echo home_url() . '/wp-admin/plugin-editor.php?file=docebo%2Fincludes%2Fcss%2Ffront.css&plugin=docebo%2Fdocebo.php'; ?>"><?php _e('Manually edit CSS file', 'docebo'); ?></a>
						<br class="clear">
						
						<fieldset>
							<label class="textinput" for="dwp_sso_settings[text_color]"><?php _e('Text color', 'docebo'); ?></label>
							<input type="text" id="text_color" value="<?php if($dwp_sso_options['text_color']) { echo $dwp_sso_options['text_color']; } else { echo '#333333'; } ?>" name="dwp_sso_settings[text_color]" />
							<div id="dwp_sso_settings[text_color]" class="colorpckr"></div>						
						</fieldset>
						<br class="clear">
						
						<fieldset><label class="textinput" for="dwp_sso_settings[title_color]"><?php _e('Title color', 'docebo'); ?></label>
							<input type="text" id="title_color" value="<?php if($dwp_sso_options['title_color']) { echo $dwp_sso_options['title_color']; } else { echo '#333333'; } ?>" name="dwp_sso_settings[title_color]" />
							<div id="dwp_sso_settings[title_color]" class="colorpckr"></div>						
						</fieldset>
						<br class="clear">
						
						<fieldset>
							<label class="textinput" for="dwp_sso_settings[border_color]"><?php _e('Border color', 'docebo'); ?></label>
							<input type="text" id="border_color" value="<?php if($dwp_sso_options['border_color']) { echo $dwp_sso_options['border_color']; } else { echo '#333333'; } ?>" name="dwp_sso_settings[border_color]" /> <em><?php _e('Leave empty for no border.', 'docebo'); ?></em>
							<div id="dwp_sso_settings[border_color]" class="colorpckr"></div>						
						</fieldset>
						<br class="clear">
						
						<fieldset>
							<label class="textinput" for="dwp_sso_settings[bg_color]"><?php _e('Background color', 'docebo'); ?></label>
							<input type="text" id="bg_color" value="<?php if($dwp_sso_options['bg_color']) { echo $dwp_sso_options['bg_color']; } else { echo '#333333'; } ?>" name="dwp_sso_settings[bg_color]" />
							<div id="dwp_sso_settings[bg_color]" class="colorpckr"></div>						
						</fieldset>
						<br class="clear">
						
						<fieldset>
							<label class="textinput" for="dwp_sso_settings[box_shadow]"><?php _e('Use box shadow?', 'docebo'); ?></label>
							<input type="checkbox" id="dwp_sso_settings[box_shadow]"  name="dwp_sso_settings[box_shadow]" class="textinput" 
								value="yes" <?php if ($dwp_sso_options['box_shadow'] == 'yes') { echo 'checked="checked"'; } ?> /><br/>
						</fieldset>
						<br class="clear">
						
						<fieldset>
							<label class="textinput" for="dwp_sso_settings[buttonbg_color]"><?php _e('Button background color', 'docebo'); ?></label>
							<input type="text" id="buttonbg_color" value="<?php if($dwp_sso_options['buttonbg_color']) { echo $dwp_sso_options['buttonbg_color']; } else { echo '#333333'; } ?>" name="dwp_sso_settings[buttonbg_color]" /> 
							<div id="dwp_sso_settings[buttonbg_color]" class="colorpckr"></div>						
						</fieldset>
						<br class="clear">
						
						<fieldset>
							<label class="textinput" for="dwp_sso_settings[buttonborder_color]"><?php _e('Button border color', 'docebo'); ?></label>
							<input type="text" id="buttonborder_color" value="<?php if($dwp_sso_options['buttonborder_color']) { echo $dwp_sso_options['buttonborder_color']; } else { echo '#333333'; } ?>" name="dwp_sso_settings[buttonborder_color]" /> <em><?php _e('Leave empty for no border.', 'docebo'); ?></em>
							<div id="dwp_sso_settings[buttonborder_color]" class="colorpckr"></div>						
						</fieldset>
						<br class="clear">
						
						<fieldset>
							<label class="textinput" for="dwp_sso_settings[buttontext_color]"><?php _e('Button text color', 'docebo'); ?></label>
							<input type="text" id="buttontext_color" value="<?php if($dwp_sso_options['buttontext_color']) { echo $dwp_sso_options['buttontext_color']; } else { echo '#333333'; } ?>" name="dwp_sso_settings[buttontext_color]" />
							<div id="dwp_sso_settings[buttontext_color]" class="colorpckr"></div>						
						</fieldset>
						<br class="clear">
			
						<input type="submit" class="button-primary" value="<?php _e('Save changes', 'docebo'); ?>" />
					</form>
				</div><!-- end #docebo-settings-panel-->
				
				<!-- Generate a preview -->
				<div id="docebo-preview-panel">
					
					<h3><?php _e('Preview', 'docebo'); ?></h3>
					
					<?php // Let's create the SSO box, first call the API
						$params = array(
							'ext_user' => $current_user->ID,
							'ext_user_type' => 'wordpress',
						);
						$data = Api::call('user/getstat/', $params); 		
						
						$course_block = json_decode($data);
						$button_link = Api::sso(dwp_get_docebo_username()); 
					?>
					<div id="docebo-sso-box" 
						style="background: <?php echo $dwp_sso_options['bg_color'];?>; width: <?php echo $dwp_sso_options['sso_box_width'];?>px; height: <?php echo $dwp_sso_options['sso_box_height'];?>px; color: <?php echo $dwp_sso_options['text_color']; ?>; border: 1px solid <?php echo $dwp_sso_options['border_color']; ?>; <?php if ($dwp_sso_options['box_shadow'] == 'yes') { echo 'box-shadow: 0 0 8px rgba(0,0,0,0.20)'; } ?>">
						<div class="sso-container">
							<h3 style="color: <?php echo $dwp_sso_options['title_color'];?>"><?php echo $dwp_sso_options['sso_title'];?></h3>
							<?php if($dwp_sso_options['sso_text']) { echo '<p>' . $dwp_sso_options['sso_text'] . '</p>'; } ?>
						
							<div class="course-overview">
								<h4 style="color: <?php echo $dwp_sso_options['title_color'];?>"><?php _e('Courses you are enrolled in', 'docebo'); ?></h4>
								<?php 
									echo '<div class="new-courses">New</span><span class="number">' . $course_block->user_courses->new_courses . '</div>';
									echo '<div class="inprogress-courses">In Progress</span><span class="number">' . $course_block->user_courses->in_progress . '</div>';
									echo '<div class="completed-courses">Completed</span><span class="number">' . $course_block->user_courses->completed . '</div>';
								?>
							</div>
							
							<?php if ( !empty($course_block->last_course) ) { ?>
								<div class="last-course">
									<h4 style="color: <?php echo $dwp_sso_options['title_color'];?>"><?php _e('Last attended course', 'docebo'); ?></h4>
									<?php 	echo '<img class="course-thumb" src="' . $course_block->last_course->course_thumbnail . '" />';
											echo '<div class="last-course-meta"><h4 style="color: ' . $dwp_sso_options['title_color'] . '">' . $course_block->last_course->course_title . '</h4>';
											_e('Course Progress:', 'docebo'); 
											echo '<div class="progress"><div class="bar" style="width: ' . $course_block->last_course->course_progress . '%;"></div></div></div><div class="clear"></div>'; ?>
								</div>
							<?php } ?>
						
							<div class="cloud-button" style="text-align: <?php echo $dwp_sso_options['sso_button_pos']; ?>;">
								<a href="<?php echo $button_link; ?>" target="_blank" class="docebo-button" style="background: <?php echo $dwp_sso_options['buttonbg_color'];?>; color: <?php echo $dwp_sso_options['buttontext_color']; ?>; border: 1px solid <?php echo $dwp_sso_options['buttonborder_color']; ?>;"><?php echo ($dwp_sso_options['sso_button_text'] ? $dwp_sso_options['sso_button_text'] : __('SIGN IN', 'docebo')); ?></a>
							</div>
						</div>
					</div>
					<p>To insert the SSO box in your pages use the shortcode [docebo_sso] or insert it from the widget area.</p>
				</div><!-- end #docebo-preview-panel -->
					
				<?php } else { ?>
					<p class="error"><?php _e('Single Sign-on is not enabled - please use the <a href="' . site_url() . '/wp-admin/admin.php?page=docebo-login">Login Box</a> instead.', 'docebo'); ?></p>
				<?php } ?>
			</div>
	
			<?php echo ob_get_clean();
		} // end Single Sign-on page

		
		// 4. My Courses page
		function dwp_courses_page() {
	
			global $current_user;
			
			$dwp_course_options = get_option('dwp_course_settings');
	
			ob_start(); ?>
	
			<div class="wrap docebo">
				<div id="icon-docebo-settings" class="icon32"><br></div>
				<h2><?php _e('Docebo - My Courses', 'docebo'); ?></h2>
				
				<!-- This snippet generates the "Settings saved" message -->
				<?php settings_errors(); ?>
				
				<p><?php _e('This will show to the logged user a list of courses in which the user is enrolled. By clicking on each course, you will be redirected to your Docebo Cloud.', 'docebo'); ?></p>
				<p><?php _e('Use this shortcode [docebo_mycourses] to view the list.', 'docebo'); ?></p>
				
				<script type="text/javascript">
				jQuery(document).ready(function() {
					// My Courses form validation
					jQuery("#mycourses").validate();

					jQuery('#cblock_width').rules('add', {
						required: true,
						digits: true,
						min: 170,
						max: 230
					});
				});
				</script>
				
				
				<form method="post" id="mycourses" action="options.php"> <!-- Form action is set to options.php, so settings will be saved in the _options table -->
			
					<?php settings_fields('dwp_course_settings_group'); ?>
			
					<fieldset>
						<label class="textinput" for="dwp_course_settings[cblock_width]"><?php _e('Course block width', 'docebo'); ?></label>
						<input type="text" id="cblock_width" name="dwp_course_settings[cblock_width]" class="textinput" value="<?php echo $dwp_course_options['cblock_width']; ?>" /><span class="field-after">px</span> <em><?php _e('Min Value: 170px  -  Max value: 230px', 'docebo'); ?></em>
					</fieldset>
					<br class="clear">
					
					<fieldset>
						<label class="textinput" for="dwp_course_settings[cblock_amount]"><?php _e('Number of courses displayed', 'docebo'); ?></label>
						<input type="text" id="cblock_amount" name="dwp_course_settings[cblock_amount]" class="textinput" value="<?php echo $dwp_course_options['cblock_amount']; ?>" /> <em><?php _e('Leave blank if you want to display every course.', 'docebo'); ?></em>
					</fieldset>
					<br class="clear">
			
					<input type="submit" class="button-primary" value="<?php _e('Save changes', 'docebo'); ?>" />
				</form>
				
				<p>&nbsp;</p>
				<h3><?php _e('Preview', 'docebo'); ?></h3>
				<?php 
				
				$params = array(
					'ext_user' => $current_user->ID,
					'ext_user_type' => 'wordpress',
				);
				$data = Api::call('user/userCourses/', $params); 			
				
				$course_block = json_decode($data);
	
				$course_count = 0;
				$num_courses_displayed = (isset($dwp_course_options['cblock_amount']) ? (int)$dwp_course_options['cblock_amount'] : 0);
				$display_all_courses = ($num_courses_displayed==0);
				
				if(is_array($course_block) || is_object($course_block)){
					foreach ($course_block as $courses) {
						if(isset($courses->course_info) && isset($courses->course_info->course_id) && ($course_count < $num_courses_displayed || $display_all_courses) ) {
							echo 	'<div class="cblock-element" style="width:' . $dwp_course_options['cblock_width'] . 'px; height:' . $dwp_course_options['cblock_width'] . 'px; ';
									if ($courses->course_info->courseuser_status == 2) { 
										echo 'border: 4px solid #60c060;';
									} else { 
										echo 'border: 4px solid #0465ac;';
									}
									echo '">';
									if($courses->course_info->courseuser_status == 2){
										?>
										<img class="course-status-icon" src="<?php echo plugin_dir_url( __FILE__ ); ?>includes/images/icon_corner_complete.png" />
										<?php
									}else{
										?>
										<img class="course-status-icon" src="<?php echo plugin_dir_url( __FILE__ ); ?>includes/images/icon_corner_progress.png" />
										<?php
									}
									echo '<a target="_blank" href="' . Api::sso(dwp_get_docebo_username()) . '&id_course=' . $courses->course_info->course_id. '">
											<div style="width:' . $dwp_course_options['cblock_width'] . 'px; height:' . $dwp_course_options['cblock_width'] . 'px; background:url(\''.$courses->course_info->course_thumbnail.'\'); background-size: cover; position: relative; background-position: center;">
												<p class="coursename">' . $courses->course_info->course_name . '</p>
											</div>

										</a>
									</div><!--.cblock-element-->';
							$course_count++;
						}
					}
				}
				
				if($course_count==0){
					_e('No courses available', 'docebo');
				}
				
				echo '<div class="clear"></div>';
				?>
		
			</div>
	
			<?php echo ob_get_clean();
		} // end My Courses page


		// 5. User Sync page
		function dwp_usersync_page() {
			
			global $dwp_options;
			
			$success = ((isset($_GET['users-synced']) && $_GET['users-synced']) ? true : false);
					
			ob_start(); ?>
	
			<div class="wrap docebo">
				<div id="icon-docebo-settings" class="icon32"><br></div>
				<h2><?php _e('Docebo - User Synchronization', 'docebo'); ?></h2>
				
				<?php if ($dwp_options['docebo_scenario'] == 'user_scenario2') { ?>
				
				<?php if( $success == 'true' ) : ?>
						<div id="setting-error-settings_updated" class="updated settings-error below-h2"> 
								<p><strong>
										Your users are now synchronized.
										<?php if($_GET['synced'] || $_GET['failed']){
											echo ' (';
											if($_GET['synced'])
												echo "Copied: ". ((int) $_GET['synced']);
											
											if($_GET['synced'] && $_GET['failed'])
												echo " ";
											
											if($_GET['failed'])
												echo "Failed: ". ((int) $_GET['failed']);
											echo ')';
										} ?>
								</strong></p>
							 </div>
				<?php endif; ?>
				
				<p><?php _e('Users in your WordPress system: ', 'docebo'); 
				$wpuser_count = count_users();
				echo '<strong>' . $wpuser_count['total_users'] . '</strong><br/>'; ?>
						
				<?php _e('Users in your Docebo Cloud: ', 'docebo'); ?>
					<?php $params = array();
					$data = Api::call( 'user/listUsers/', $params ); 
					$users_block = json_decode( $data );
					
					$docebo_users = 0;
					if(isset($users_block->users) && is_array($users_block->users))
						$docebo_users = (int) count($users_block->users);

					echo '<strong>' . $docebo_users . '</strong>'; ?>			
				</p>
				
				<p><?php _e('This will fetch users from Wordpress and add them to Docebo Cloud.', 'docebo'); ?><br/>
				<?php _e('Username and password will be fetched.', 'docebo'); ?><br/>
				<?php _e('Users already in Docebo Cloud will not be added.', 'docebo'); ?><br/>
				<strong><?php _e('After the first sync, every user added in WordPress will be automatically added also in Docebo Cloud.', 'docebo'); ?></strong></p>
				
				<form method="post" class="user-login" action="<?php echo admin_url( 'admin.php?page=docebo-usersync-do'); ?>">
					<input type="hidden" name="action" value="user_sync" />
					<input type="submit" name="submit" class="button-primary" value="<?php _e('Sync users', 'docebo'); ?>" />
				</form>
				<?php } else {
					echo '<p class="error">'.__('User sync is not enabled. Please, see the <a href="' . site_url() . '/wp-admin/admin.php?page=docebo-settings">General Settings</a>.', 'docebo').'</p>';
				} ?>
			</div>
	
			<?php echo ob_get_clean();
		} // end User Sync page
		
		// 6. FAQ & Support page
		function dwp_support_page() {
		
			ob_start(); ?>
	
			<div class="wrap docebo">
				<div id="icon-docebo-settings" class="icon32"><br></div>
				<h2><?php _e('Docebo - FAQ & Support', 'docebo'); ?></h2>
				
				
				<p><?php _e('Please find the most frequently asked questions below. If these don\'t solve your problem, you may contact Docebo Support (blue button in the top right corner of your LMS) or ask
				your question in the <a href="http://wordpress.org/support/plugin/docebo">WordPress support forum</a>.', 'docebo'); ?></p>
				
				<h3><?php _e('Support info', 'docebo'); ?></h3>
				<p><?php _e('If you copy this block of information into your ticket, it helps us answer your questions easier and quicker. Thank you!', 'docebo'); ?><p>
				
				<!-- Generate system info that will help support -->
				<p>
				<strong><?php _e('Website URL: ', 'docebo'); ?></strong><?php echo home_url(); ?><br/>
				<strong><?php _e('WordPress URL: ', 'docebo'); ?></strong><?php echo site_url(); ?><br/>
				<strong><?php _e('Current theme: ', 'docebo'); ?></strong><?php
				if(!function_exists('wp_get_theme')){
					echo get_current_theme();
				}else{
					echo wp_get_theme();
				}
				?><br/>
				<strong><?php _e('Active plugins: ', 'docebo'); ?></strong>
					<?php 	$active_plugins = get_option('active_plugins'); 
							$to_display = '';
							foreach($active_plugins as $key => $value) {
								$plugin_data = get_plugin_data(ABSPATH . 'wp-content/plugins/' . $value);
								$to_display .= $plugin_data['Name'] . ' ' . $plugin_data['Version'] . ', ';
							}
							echo trim($to_display, ' ,'); // Remove the comma after the last element
				?><br/>
				<strong><?php _e('Version of Docebo plugin: ', 'docebo'); ?></strong>
					<?php $plugin_data = get_plugin_data( plugin_dir_path(__FILE__) . '/docebo.php' );
					echo $plugin_data['Version'];
				?><br/>
				<strong><?php _e('WordPress version: ', 'docebo'); ?></strong>
					<?php echo get_bloginfo('version'); ?>				
				</p>
				
			</div>
	
			<?php echo ob_get_clean();
		
		} // end FAQ & Support page
		
		// 7. (no menu item) SSO authentication/login page (redirects to the LMS)
		function dwp_sso_login(){
			dpw_redirect_to_sso();
		}
		
		// 8. (no menu item) User synchronization processing page (redirects after completion)
		function dwp_user_sync_do(){
			dwp_sync_users_and_redirect();
		}
		
	/*--- End of display functions of Docebo admin pages ---*/

	
	/* ------------------------------------------------------*/
	/* Create users in WP and in Docebo
	/* ------------------------------------------------------*/
	function dwp_user_create( $user_id, $plaintext_pass = '' ) {
		
		global $dwp_options;
		
		$user = new WP_User($user_id);
		
		$userdata['userid'] = $user->user_login;
		$userdata['email'] = $user->user_email;
		$userdata['firstname'] = $user->user_firstname;
		$userdata['lastname'] = $user->user_lastname;
		$userdata['password'] = $_POST['pass1'];
		$userdata['ext_user'] = $user->ID;
		$userdata['ext_user_type'] = 'wordpress';

		if($dwp_options['docebo_scenario']=='user_scenario2'){
			// User sync is enabled
			$data = Api::call('user/create/', $userdata);
		}
 		
	}
	add_action ('user_register', 'dwp_user_create');
	
	/* ------------------------------------------------------*/
	/* Update/edit users in WP and in Docebo
	/* ------------------------------------------------------*/
	function dwp_user_edit() {
		
		global $dwp_options;
		
		$userdata = array();
		
		/*$userdata['userid'] = $user->user_login;*/ // Unnecessary, can't be changed in WP
		$userdata['email'] = $_POST['email'];
		$userdata['firstname'] = $_POST['first_name'];
		$userdata['lastname'] = $_POST['last_name'];
		$userdata['password'] = $_POST['pass1'];
		$userdata['ext_user'] = $_POST['user_id'];
		$userdata['ext_user_type'] = 'wordpress';
		$userdata['single_user'] = 'true';

		if($dwp_options['docebo_scenario']=='user_scenario2'){
			// User sync is enabled
			$data = Api::call('user/edit/', $userdata);
		}
 		
	}
	add_action ('edit_user_profile_update', 'dwp_user_edit');
	add_action ('personal_options_update', 'dwp_user_edit');
	add_action ('edit_user_profile_update', 'dwp_user_edit');
	
	
	
	/* ------------------------------------------------------*/
	/* Adding our custom Docebo menu to the WP admin menu
	/* ------------------------------------------------------*/
	function dwp_add_admin_page() {
		add_menu_page('Docebo', 'Docebo', 'manage_options', 'docebo-settings', 'dwp_settings_page', plugin_dir_url( __FILE__) .'includes/images/docebo.png'); // The root page, which leads to the General settings page
		add_submenu_page( 'docebo-settings', __('General Settings', 'docebo'), __('General Settings', 'docebo'), 'manage_options', 'docebo-settings', 'dwp_settings_page' ); // General Settings page
		add_submenu_page( 'docebo-settings', __('Log In', 'docebo'), __('Log In', 'docebo'), 'manage_options', 'docebo-login', 'dwp_admin_page' ); // General Settings page
		add_submenu_page( 'docebo-settings', __('Single Sign-on', 'docebo'), __('Single Sign-on', 'docebo'), 'manage_options', 'docebo-signon', 'dwp_signon_page' ); // Single Sign-on page
		add_submenu_page( 'docebo-settings', __('My Courses', 'docebo'), __('My Courses', 'docebo'), 'manage_options', 'docebo-courses', 'dwp_courses_page' ); // My Courses page
		add_submenu_page( 'docebo-settings', __('User Synchronization', 'docebo'), __('User Synchronization', 'docebo'), 'manage_options', 'docebo-usersync', 'dwp_usersync_page' ); // User Sync page
		add_submenu_page( 'docebo-settings', __('FAQ & Support', 'docebo'), __('FAQ & Support', 'docebo'), 'manage_options', 'docebo-support', 'dwp_support_page' ); // FAQ and Support page
		
		// Core pages that have no UI and Admin menu links, they only do processing
		add_submenu_page( null, __('Authentication', 'docebo'), __('Authentication', 'docebo'), 'read', 'docebo-sso-login', 'dwp_sso_login' ); // SSO login redirection (page accessible by anyone and not visible in menu)
		add_submenu_page( null, __('User Synchronization', 'docebo'), __('User Synchronization', 'docebo'), 'manage_options', 'docebo-usersync-do', 'dwp_user_sync_do' ); // SSO login redirection (page accessible by anyone and not visible in menu)
	
		// Changing the title of the first item - originally the same as the top menu (Docebo) - to "Log In"
		global $submenu;
			if ( isset( $submenu['docebo-login'] ) )
				$submenu['docebo-login'][0][0] = __( 'Log In', 'docebo' );
	
	}
	add_action('admin_menu', 'dwp_add_admin_page');
	
	// We need to hook early, even before the admin page is rendered.
	// Otherwise the redirects don't work.
	function dwp_initial_admin_page_process($value){
		global $pagenow;
		$page = (isset($_REQUEST['page']) ? $_REQUEST['page'] : false);
		if($pagenow=='admin.php'){
			switch($page){
				case 'docebo-sso-login':
					dpw_redirect_to_sso();
					break;
				case 'docebo-usersync-do':
					dwp_sync_users_and_redirect();
					break;
				default:
					break;
			}
		}
	}
	add_action('admin_init', 'dwp_initial_admin_page_process');
	
	// Get SSO link and redirect to it
	function dpw_redirect_to_sso(){
		$sso_login_link = (isset($_POST['userid']) ? Api::sso($_POST['userid']) : false);
		if($sso_login_link){
			exit(wp_redirect($sso_login_link));
		}else{
			echo __('Can not get SSO login link', 'docebo');
		}
	}
	
	function dwp_sync_users_and_redirect(){
		$syncedUsers = 0;
		$failedUsers = 0;

		if(function_exists('get_users')){
			$users = get_users();
		}elseif(function_exists('get_users_of_blog')){
			$users = get_users_of_blog();
		}

		$userdata = array();
		$i = 0;
		foreach ($users as $user) {
			if ($user->ID > 0) {
				$userdata[] = array(
									'ext_user'=>$user->ID,
									'ext_user_type'=>'wordpress',
									'wp_user_id'=>$user->ID,
									'wp_username'=>$user->user_login,
									'userid'=>$user->user_login,
									'email'=>$user->user_email,
									'first_name'=>$user->first_name,
									'last_name'=>$user->last_name,
								);

				$i++;
			}
		}

		if (!empty($userdata)) {
			$sendData = array('users'=>json_encode($userdata));
			$data = Api::call('user/syncWordpressUsers/', $sendData);
			$response = json_decode($data, true);

			if(isset($response['success']) && $response['success']==true){
				// API call successful

				if(isset($response['result']) && is_array($response['result'])){
					foreach($response['result'] as $singleUser){
						if($singleUser['success']==true){
							$syncedUsers++;
						}else{
							$failedUsers++;
						}
					}
				}
			}
		}

		wp_safe_redirect(site_url() . '/wp-admin/admin.php?page=docebo-usersync&users-synced=true&synced='.(int)$syncedUsers.'&failed='.(int)$failedUsers);
		exit;
	}
	
	/* ------------------------------------------------------*/
	/* Adding our custom Docebo menu to the WP admin bar
	/* ------------------------------------------------------*/
	function dwp_render_admin_bar() {
		global $wp_admin_bar;
		$wp_admin_bar->add_menu( array(
				'parent' => false, // If it is root menu: use 'false'; if it is a child: pass the ID of the parent menu
				'id' => 'docebo-login', // link ID
				'title' => __('Docebo'), // link title
				'href' => admin_url( 'admin.php?page=docebo-login'), // URL of page
				'meta' => false // array of any of the following options: array( 'html' => '', 'class' => '', 'onclick' => '', target => '', title => '' );
		));
		$wp_admin_bar->add_menu( array(
				'parent' => 'docebo-login', 
				'id' => 'docebo-login-sub', 
				'title' => __('Log In'), 
				'href' => admin_url( 'admin.php?page=docebo-login'), 
				'meta' => false
		));
		$wp_admin_bar->add_menu( array(
				'parent' => 'docebo-login', 
				'id' => 'docebo-settings-sub', 
				'title' => __('General Settings'), 
				'href' => admin_url( 'admin.php?page=docebo-settings'), 
				'meta' => false 
		));
		$wp_admin_bar->add_menu( array(
				'parent' => 'docebo-login', 
				'id' => 'docebo-signon-sub', 
				'title' => __('Single Sign-on'), 
				'href' => admin_url( 'admin.php?page=docebo-signon'), 
				'meta' => false
		));
		$wp_admin_bar->add_menu( array(
				'parent' => 'docebo-login', 
				'id' => 'docebo-courses-sub', 
				'title' => __('My Courses'), 
				'href' => admin_url( 'admin.php?page=docebo-courses'), 
				'meta' => false
		));
		$wp_admin_bar->add_menu( array(
				'parent' => 'docebo-login', 
				'id' => 'docebo-usersync-sub', 
				'title' => __('User Synchronization'), 
				'href' => admin_url( 'admin.php?page=docebo-usersync'), 
				'meta' => false
		));
		$wp_admin_bar->add_menu( array(
				'parent' => 'docebo-login', 
				'id' => 'docebo-support-sub', 
				'title' => __('FAQ & Support'), 
				'href' => admin_url( 'admin.php?page=docebo-support'), 
				'meta' => false
		));
	}
	if(current_user_can('manage_options')){
		add_action( 'wp_before_admin_bar_render', 'dwp_render_admin_bar' );
	}
	

	/* ------------------------------------------------------*/
	/* Add and save Docebo Plugin settings data
	/* ------------------------------------------------------*/
	function dwp_register_settings() {
		register_setting('dwp_settings_group', 'dwp_settings', 'dwp_preprocess_saved_settings'); // General settings
		register_setting('dwp_course_settings_group', 'dwp_course_settings'); // My Courses settings
		register_setting('dwp_sso_settings_group', 'dwp_sso_settings'); // SSO settings 
		register_setting('dwp_login_settings_group', 'dwp_login_settings'); // Login Box settings 
	}
	add_action('admin_init', 'dwp_register_settings');
	
	/**
	 * Processing of saved Settings fields before actually saving them to database
	 * @param array $settings - associative array of saved settings from POST
	 * @return array - associative array of sanitized settings to be saved
	 */
	function dwp_preprocess_saved_settings($settings){
		
		// Remove unnecessary things from the LMS url
		if(is_array($settings) && $settings['docebo_address']){
			$settings['docebo_address'] = str_ireplace(array(
				'http://', 'http:', 'http//'
				), '', $settings['docebo_address']);
		}
		
		// Trim spaces from beginning and end of settings values
		foreach($settings as $key => &$value){
			$value = trim($value);
		}
		return $settings;
	}
	
} 
// end the docebo_init() function
// Load the plugin after the core WP files are loaded, so it can use the core WP functions 
add_action('init', 'docebo_init');


/* --------------------------------------------------- */
/* Initialize widgets
/* --------------------------------------------------- */
add_action( 'widgets_init', 'docebo_widgets_init' );
function docebo_widgets_init() {		
	register_widget( 'docebo_login_widget' );
	register_widget( 'docebo_sso_widget' );
	register_widget( 'docebo_courses_widget' );
}

// Docebo Login Widget
class docebo_login_widget extends WP_Widget {
 	
	/**
	 * Set the widget defaults
	 */
	private $widget_title = "Docebo Login";
 	
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		
		parent::__construct(
			'docebo_login_widget',		// Base ID
			'Docebo Login Widget',		// Name
			array(
				'classname'		=>	'docebo_login_widget',
				'description'	=>	__('A widget that displays a Docebo Login Box in the sidebar.', 'docebo')
			)
		);
	} // end constructor
	
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		global $dwp_options;
		extract( $args );

		/* Our variables from the widget settings. */
		$this->widget_title = apply_filters('widget_title', $instance['title'] );
		
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $this->widget_title )
			echo $before_title . $this->widget_title . $after_title;

		echo do_shortcode('[docebo_login]');

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}
	
	/**
	 * Create the form for the Widget admin
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */	 
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array(
		'title' => $this->widget_title,
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>


			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'docebo') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		
	<?php
	}
} // end class docebo_login_widget

// Docebo SSO Widget
class docebo_sso_widget extends WP_Widget {
 	
	/**
	 * Set the widget defaults
	 */
	private $widget_title = "Docebo SSO";
 	
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		
		parent::__construct(
			'docebo_sso_widget',		// Base ID
			'Docebo SSO Widget',		// Name
			array(
				'classname'		=>	'docebo_sso_widget',
				'description'	=>	__('A widget that displays a Docebo SSO Box in the sidebar.', 'docebo')
			)
		);
	} // end constructor
	
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		global $dwp_options;
		extract( $args );

		/* Our variables from the widget settings. */
		$this->widget_title = apply_filters('widget_title', $instance['title'] );
		
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $this->widget_title )
			echo $before_title . $this->widget_title . $after_title;

		echo do_shortcode('[docebo_sso]');

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}
	
	/**
	 * Create the form for the Widget admin
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */	 
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array(
		'title' => $this->widget_title,
		'button_text' => $this->button_text,
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>


			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'docebo') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		
		
	<?php
	}
} // end class docebo_sso_widget

// My Courses widget
class docebo_courses_widget extends WP_Widget {
 	
	/**
	 * Set the widget defaults
	 */
	private $widget_title = "My Docebo Courses";
 	
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		
		parent::__construct(
			'docebo_courses_widget',		// Base ID
			'Docebo My Courses Widget',		// Name
			array(
				'classname'		=>	'docebo_courses_widget',
				'description'	=>	__('A widget that displays the Docebo courses you are enrolled in.).', 'docebo')
			)
		);
	} // end constructor
	
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		global $dwp_options;
		extract( $args );

		/* Our variables from the widget settings. */
		$this->widget_title = apply_filters('widget_title', $instance['title'] );
		
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $this->widget_title )
			echo $before_title . $this->widget_title . $after_title;

		/* My Courses Widget  */
			echo do_shortcode('[docebo_mycourses]');
		
		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['button_text'] = strip_tags( $new_instance['button_text'] );

		return $instance;
	}
	
	/**
	 * Create the form for the Widget admin
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */	 
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array(
		'title' => $this->widget_title,
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>


			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'docebo') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		
	<?php
	}
} // end class docebo_courses_widget


/* --------------------------------------------------- */
/*  Shortcodes
/* --------------------------------------------------- */
function shortcode_docebo_mycourses ( $atts, $content = null ) {
	
	global $current_user;
	
	// Get plugin settings
	$dwp_course_options = get_option('dwp_course_settings');
	
	// Docebo API call
	$params = array(
		'ext_user' => $current_user->ID,
		'ext_user_type' => 'wordpress',
	);
	$data = Api::call('user/userCourses/', $params); 			
	$course_block = json_decode($data);
	
	$num_courses_displayed = (isset($dwp_course_options['cblock_amount']) ? (int)$dwp_course_options['cblock_amount'] : 0);
	$display_all_courses = ($num_courses_displayed==0);

	$course_count = 0;
	$output = '';
	if(is_array($course_block) || is_object($course_block)){
		foreach ($course_block as $courses) {
			if(isset($courses->course_info) && isset($courses->course_info->course_id) && ($course_count < $num_courses_displayed || $display_all_courses) ) {
				$output .= '<div class="cblock-element" style="width:' . $dwp_course_options['cblock_width'] . 'px; height:' . $dwp_course_options['cblock_width'] . 'px; position:relative;';
						if ($courses->course_info->courseuser_status == 2) { 
							$output .= 'border: 4px solid #60c060;'; 
						} else { 
							$output .= 'border: 4px solid #0465ac;';
						}
						$output .= '">';

						if($courses->course_info->courseuser_status == 2){
							$output .= '<img class="course-status-icon" src="'.plugin_dir_url( __FILE__ ).'includes/images/icon_corner_complete.png" />';
						}else{
							$output .= '<img class="course-status-icon" src="'.plugin_dir_url( __FILE__ ).'includes/images/icon_corner_progress.png" />';
						}

						$output .= '<a target="_blank" href="' . Api::sso(dwp_get_docebo_username()) . '&id_course=' . $courses->course_info->course_id. '">
										<div style="width:' . $dwp_course_options['cblock_width'] . 'px; height:' . $dwp_course_options['cblock_width'] . 'px; background:url(\''.$courses->course_info->course_thumbnail.'\'); background-size: cover; position: relative; background-position: center;">
											<div class="coursename">
												<div class="coursename-spacer">
													' . $courses->course_info->course_name . '
												</div>
											</div>
										</div>
									</a>
							</div><!--.cblock-element-->';
				$course_count++;
			}
		}
	}
	
	if($course_count==0){
		$output .= '<p>'.__('No courses available', 'docebo').'</p>';
	}
	
	$output .= '<div class="clear"></div>';
	
	return do_shortcode( $output );
}
add_shortcode('docebo_mycourses', 'shortcode_docebo_mycourses'); 


function shortcode_docebo_sso ( $atts, $content = null ) {
	
	// Get plugin settings
	$dwp_sso_options = get_option('dwp_sso_settings');
	
	global $current_user;
	
	// Docebo API call
	$params = array(
		'ext_user' => $current_user->ID,
		'ext_user_type' => 'wordpress',
	);
	$data = Api::call('user/getstat/', $params); 			
	$course_block = json_decode($data);
	$button_link = Api::sso(dwp_get_docebo_username()); 
	
	$output = '<div id="docebo-sso-box" style="background:' . $dwp_sso_options['bg_color'] . '; width: ' . $dwp_sso_options['sso_box_width'] . 'px; height: ' . $dwp_sso_options['sso_box_height'] . 'px; color: ' . $dwp_sso_options['text_color'] . '; border: 1px solid ' . $dwp_sso_options['border_color'] . ';';
	if($dwp_sso_options['box_shadow'] == 'yes') { 
		$output .= ' box-shadow: 0 0 8px rgba(0,0,0,0.20)'; 
	}
	$output .= '"><div class="sso-container">
				<h3 style="color: ' . $dwp_sso_options['title_color'] . '">' . $dwp_sso_options['sso_title'] . '</h3>';
	if($dwp_sso_options['sso_text']) { 
		$output .= '<p>' . $dwp_sso_options['sso_text'] . '</p>'; 
	}
	$output .= '<div class="course-overview">
					<h4 style="color: ' . $dwp_sso_options['title_color'] . '">' . __('Courses you are enrolled in', 'docebo') . '</h4>
					<div class="new-courses">New</span><span class="number">' . $course_block->user_courses->new_courses . '</div>
					<div class="inprogress-courses">In Progress</span><span class="number">' . $course_block->user_courses->in_progress . '</div>
					<div class="completed-courses">Completed</span><span class="number">' . $course_block->user_courses->completed . '</div>
				</div>';
							
	if ( !empty($course_block->last_course) ) {
		$output .= '<div class="last-course">
					<h4 style="color: ' . $dwp_sso_options['title_color'] . '">' . __('Last attended course', 'docebo') . '</h4>
					<img class="course-thumb" src="' . $course_block->last_course->course_thumbnail . '" />
					<div class="last-course-meta"><h4 style="color: ' . $dwp_sso_options['title_color'] . '">' . $course_block->last_course->course_title . '</h4>' . __('Course Progress:', 'docebo') . '
					<div class="progress"><div class="bar" style="width: ' . $course_block->last_course->course_progress . '%;"></div></div></div><div class="clear"></div>
				</div>';
	}
	
	$output .= '<div class="cloud-button" style="text-align: ' . $dwp_sso_options['sso_button_pos'] . '">
					<a href="' . $button_link . '" target="_blank" class="docebo-button" style="background: ' . $dwp_sso_options['buttonbg_color'] . '; color: ' . $dwp_sso_options['buttontext_color'] . '; border: 1px solid ' . $dwp_sso_options['buttonborder_color'] . ';">' . ($dwp_sso_options['sso_button_text'] ? $dwp_sso_options['sso_button_text'] : __('SIGN IN', 'docebo')) . '</a>
				</div>
			</div>
		</div>';
		
	return do_shortcode($output);

}				
add_shortcode('docebo_sso', 'shortcode_docebo_sso'); 


function shortcode_docebo_login ( $atts, $content = null ) {
	
	global $dwp_options;
	
	// Get plugin settings
	$dwp_login_options = get_option('dwp_login_settings');

	$output = '<div id="docebo-login-box" style="background: ' . $dwp_login_options['bg_color'] . '; width: ' . $dwp_login_options['login_box_width'] . 'px; height: ' . $dwp_login_options['login_box_height'] . 'px; color: ' . $dwp_login_options['text_color'] . '; border: 1px solid ' . $dwp_login_options['border_color'] . ';';
					if (isset($dwp_login_options['box_shadow']) && $dwp_login_options['box_shadow'] == 'yes') { 
						$output .= ' box-shadow: 0 0 8px rgba(0,0,0,0.20)'; 
					}
					$output .= '">
						<div class="login-container">
							<div class="logo-holder">';
								$logo = ((isset($dwp_login_options['logo']) && $dwp_login_options['logo']) ? $dwp_login_options['logo'] : false);
								$no_logo = ((isset($dwp_login_options['no_logo']) && $dwp_login_options['no_logo']=='yes') ? true : false);
								if($logo && !$no_logo) { 
									$output .= '<img src="' . $logo . '" />'; 
								} elseif(!$logo && !$no_logo) { 
									$output .= '<img src="' . plugin_dir_url( __FILE__ ) . 'includes/images/docebo_logo.png' . '" />';
								} elseif($no_logo) { 
									// No logo output 
								}
							$output .= '</div>';
							
							if($dwp_login_options['login_text']) { 
								$output .=  '<p>' . $dwp_login_options['login_text'] . '</p>'; 
							}
							
							$output .= '<form method="post" class="user-login" target="_blank" action="' . admin_url( 'admin.php?page=docebo-sso-login' ) . '">
								<input type="text" name="userid" class="docebo-text" value="" placeholder="' . __('Username', 'docebo') . '" />
								<input type="password" name="pwd" class="docebo-text" value="" placeholder="' . __('Password', 'docebo') . '" />
								<input type="hidden" name="docebo_address" value="' . $dwp_options['docebo_address'] . '" />
								<input type="hidden" name="docebo_secret" value="' . $dwp_options['docebo_secret'] . '" />
								<input type="hidden" name="site_url" value="' . site_url() . '" />
							
								<div class="cloud-button" style="text-align: ' . $dwp_login_options['login_button_pos'] . ';">
									<input type="submit" name="submit" class="docebo-button" style="background: ' . $dwp_login_options['buttonbg_color'] . '; color: ' . $dwp_login_options['buttontext_color'] . '; border: 1px solid ' . $dwp_login_options['buttonborder_color'] . ';" value="' . ($dwp_login_options['login_button_text'] ? $dwp_login_options['login_button_text'] : __('SIGN IN', 'docebo')) . '" />
								</div>
							</form>
							
						</div>
					</div>';
					
	return do_shortcode($output);
	
}
add_shortcode('docebo_login', 'shortcode_docebo_login');


function shortcode_docebo_sso_link( $atts, $content = null ) {
	
	global $current_user;
	
	// Shortcode attributes
	extract( shortcode_atts( array (
		'text' => ''
	), $atts ) );
	
	// Get the SSO settings
	$dwp_sso_options = get_option('dwp_sso_settings');
	$button_link = Api::sso(dwp_get_docebo_username()); 
	
	$output = '<a href="' . $button_link . '" target="_blank" class="docebo-button" style="background: ' . $dwp_sso_options['buttonbg_color'] . '; color: ' . $dwp_sso_options['buttontext_color'] . '; border: 1px solid ' . $dwp_sso_options['buttonborder_color'] . ';">' . $text . '</a>';
	
	return do_shortcode($output); 

}
add_shortcode('docebo_sso_link', 'shortcode_docebo_sso_link');

/**
 * Attempts to get the Docebo username that is synced with the currently
 * logged in WordPress user. Cache is implemented as well.
 * On failure, it returns the WordPress user's username.
 * @global WP_User $current_user - Data for the currently logged WordPress user
 * @return mixed - The Docebo username synced with the currently logged in
 * WordPress user, the WordPress user if can't find an associated Docebo user,
 * or FALSE if no user logged in to WordPress.
 */
function dwp_get_docebo_username(){
	global $current_user;
	
	if(!$current_user)
		return false; // User not logged in
	
	// in seconds (deleted on user logout, regardless of expiration time)
	$username_cache_expiration = 60*3;
	
	// Default value in case things go wrong (the WordPress username)
	$docebo_username = $current_user->user_login;
	
	// Check if WP->Docebo username pairs exist in the cache
	$cache = get_transient('docebo_usernames');
	
	// The WordPress->Docebo username cache is empty
	if($cache == false){
		
		// Get the data for the current WordPress user through the LMS API
		$params = array(
			'ext_user' => $current_user->ID,
			'ext_user_type' => 'wordpress',
		);
		$data = Api::call('user/profile/', $params);
		
		if($data){ // API success
			
			// Parse the received user data
			$docebo_user_info = json_decode($data);
			if(isset($docebo_user_info->userid)){
				$docebo_username = $docebo_user_info->userid;
				
				// Cache the WP/Docebo username pair for performance
				$users_to_cache = array(
					$current_user->ID => $docebo_username
				);
				set_transient('docebo_usernames', json_encode($users_to_cache), $username_cache_expiration);
			}
		}
		
	} else { // WordPress/Docebo username pair cache exists
		
		// Get them as an associative array
		$cached_usernames = json_decode($cache, true);
		
		if(isset($cached_usernames[$current_user->ID]) && !empty($cached_usernames[$current_user->ID])){
			// This WordPress user's Docebo username is cached
			$docebo_username = $cached_usernames[$current_user->ID];
		}else{
			// This WordPress user's Docebo username is not cached. Retrieve it
			$params = array(
				'ext_user' => $current_user->ID,
				'ext_user_type' => 'wordpress',
			);
			$data = Api::call('user/profile/', $params);
			if($data){ // API call succeeded
				
				// Parse the received user info
				$docebo_user_info = json_decode($data);
				
				if(isset($docebo_user_info->userid)){ // The Docebo username is set
					$docebo_username = $docebo_user_info->userid;
					
					// Add this username to the Transient cache
					$cached_usernames[$current_user->ID] = $docebo_user_info->userid;
					set_transient('docebo_usernames', json_encode($cached_usernames), $username_cache_expiration);
				}
			}
		}
	}
	
	return $docebo_username;
}

/**
 * Deletes the WP_username->Docebo_username cache on user logout
 * (so new data is fetched next time, in case data in the LMS changed)
 */
function dwp_clear_username_cache(){
	delete_transient('docebo_usernames');
}
add_action('wp_logout', 'dwp_clear_username_cache');
add_action('wp_login', 'dwp_clear_username_cache');