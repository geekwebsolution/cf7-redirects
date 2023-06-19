<?php
/**
 * Description: Admin functions for the plugin 
 *
 * @author Geek Web Solution
 */
class cf7rgk_redirect_admin {
	static $options;
	
	static function redirect_add_tab( $panels ) {
		$panels['redirect-pane'] = array(
			'title'     => __( 'Redirect Settings', 'cf7rgk-redirect' ),
			'callback'  => array( 'cf7rgk_redirect_admin', 'create_tab_elements' ) ,
		);
		return $panels;
	}
	static function create_tab_elements($post){
		$cf7rgk_url = $cf7rgk_redirect_external = $cf7rgk_redirect_page = $cf7rgk_redirect_post = $cf7rgk_redirect_open_new_tab = $cf7rgk_redirect_http_build_query = $selectively_fields = $http_build_query_selectively_fields = $cf7rgk_redirect_after_sent_script = '';
		$form_id = $post->id();
		 $option=get_option( 'cf7rgk_redirect_url_'.$form_id , $default = false );
		 if(isset($option['cf7rgk_url'])) $cf7rgk_url = $option['cf7rgk_url'];
		 if(isset($option['cf7rgk_redirect_external'])) $cf7rgk_redirect_external = $option['cf7rgk_redirect_external']; 
		 if(isset($option['cf7rgk_redirect_page'])) $cf7rgk_redirect_page = $option['cf7rgk_redirect_page'];
		 if(isset($option['cf7rgk_redirect_post'])) $cf7rgk_redirect_post = $option['cf7rgk_redirect_post'];
		 if(isset($option['cf7rgk_redirect_open_new_tab'])) $cf7rgk_redirect_open_new_tab = $option['cf7rgk_redirect_open_new_tab'];
		 if(isset($option['cf7rgk_redirect_http_build_query'])) $cf7rgk_redirect_http_build_query = $option['cf7rgk_redirect_http_build_query'];
		 if(isset($option['selectively_fields'])) $selectively_fields = $option['selectively_fields'];
		 if(isset($option['http_build_query_selectively_fields'])) $http_build_query_selectively_fields = $option['http_build_query_selectively_fields'];
		 if(isset($option['cf7rgk_redirect_after_sent_script'])) $cf7rgk_redirect_after_sent_script = $option['cf7rgk_redirect_after_sent_script']; ?>
		 <h2>Redirect Settings</h2>
		<div class="cf7rgk_setting">
			<div class="cf7rgk-inner-input">	
				<span>Select a page or post or external URL  to redirect to on successful form submission. </span><br>
				<span class="cf7rgk-fildes cf7rgk-radio">
					<input type="radio" name="cf7rgk_url" id="use_page_or_post" value="url_page_post" <?php if(isset($cf7rgk_url) && !empty($cf7rgk_url)){ if($cf7rgk_url=="url_page_post"){ echo "checked"; } }else{ echo "checked"; } ?>> <label for="use_page_or_post">Use page or post</label>
					<input type="radio" name="cf7rgk_url" id="use_external_url" value="url_external" <?php if(isset($cf7rgk_url) && !empty($cf7rgk_url)){ if($cf7rgk_url=="url_external"){ echo "checked"; } } ?>>  <label for="use_external_url">Use external URL</label>
				</span>
			</div>	
			<div class="cf7rgk-inner-input cf7rgk-external" <?php if(isset($cf7rgk_url) && !empty($cf7rgk_url)){ if($cf7rgk_url=="url_external"){ echo 'style="display:block;"'; } } ?>>
				<span class="cf7rgk-fildes">
					<input type="text"  placeholder="External URL" name="cf7rgk-redirect-external" value="<?php if(isset($cf7rgk_redirect_external) && !empty($cf7rgk_redirect_external)){ if($cf7rgk_redirect_external){ echo $cf7rgk_redirect_external; } } ?>">
				</span>
				<div class="field-notice field-notice-alert field-notice-hidden cf7rgk_redirect_external" style="display:none;">
					<strong>
						Notice!        
					</strong>
					URL Not Valid
				</div>
			</div>	
			<div class="cf7rgk-inner-input cf7rgk-page" <?php if(isset($cf7rgk_url) && !empty($cf7rgk_url)){ if($cf7rgk_url=="url_external"){ echo 'style="display:none;"'; } } ?>>	
				
				<span class="cf7rgk-fildes">
						<?php	
						$pages = get_pages(); 
						?>
						<select name="cf7rgk-redirect-page" id="cf7rgk-redirect-page">
							<option value="0">Choose Page</option>
							<?php 
							if(isset($pages) && !empty($pages)){
								foreach ($pages as $page_data) { ?>
								<option value="<?php  echo $page_data->ID;?>" <?php if(isset($cf7rgk_redirect_page) && !empty($cf7rgk_redirect_page)){ if($cf7rgk_redirect_page==$page_data->ID){ echo 'selected="selected"'; } } ?>><?php echo $page_data->post_title.'(id#'.$page_data->ID.')';?></option>
							<?php }
							} ?>
						 </select>
				</span>	
			</div>
			<div class="cf7rgk-inner-input cf7rgk-post" <?php if(isset($cf7rgk_url) && !empty($cf7rgk_url)){ if($cf7rgk_url=="url_external"){ echo 'style="display:none;"'; } } ?>>		
				
				<span class="cf7rgk-fildes">
					<select name="cf7rgk-redirect-post" id="cf7rgk-redirect-post">
						<option value="0">Choose Post</option>
						<?php
						global $post;
						$args = array( 'numberposts' => -1);
						$posts = get_posts($args);
						foreach( $posts as $post ) : setup_postdata($post); ?>
							<option value="<?php  echo $post->ID; ?>" <?php if(isset($cf7rgk_redirect_post) && !empty($cf7rgk_redirect_post)){ if($cf7rgk_redirect_post==$post->ID){ echo 'selected="selected"'; } } ?>><?php echo the_title().'(id#'.$post->ID.')'; ?></option>
						<?php endforeach; ?>
					</select>
				</span>
			</div>
			<div class="cf7rgk-inner-input cf7rgk-checkbox">
				<input type="checkbox" id="cf7rgk_open_image_in_new_tab" name="cf7rgk-redirect-open-new-tab" <?php if(isset($cf7rgk_redirect_open_new_tab) && !empty($cf7rgk_redirect_open_new_tab)){ if($cf7rgk_redirect_open_new_tab=="on"){ echo "checked"; } } ?>>
				<label for="cf7rgk_open_image_in_new_tab">Open page in a new tab</label>
				<div class="field-notice field-notice-alert field-notice-hidden" style="display:none;">
					<strong>
						Notice!        
					</strong>
					This option might not work as expected, since browsers often block popup windows. This option depends on the browser settings.
				</div>
			</div>
			<div class="cf7rgk-inner-input cf7rgk-checkbox">
				<input type="checkbox" id="cf7rgk_pass_all_field_as_query" class="cf7rgk-redio-check" name="cf7rgk-redirect-http-build-query" <?php if(isset($cf7rgk_redirect_http_build_query) && !empty($cf7rgk_redirect_http_build_query)){ if($cf7rgk_redirect_http_build_query=="on"){ echo "checked"; } } ?>>
				<label for="cf7rgk_pass_all_field_as_query">
					Pass all the fields from the form as URL query parameters      
				</label>
			</div>
			<div class="cf7rgk-inner-input cf7rgk-checkbox">
				<input type="checkbox" id="cf7rgk_pass_specific_field_as_query" class="cf7rgk-redio-check"  name="cf7rgk-redirect-http-build-query-selectively" <?php if(isset($selectively_fields) && !empty($selectively_fields)){ if($selectively_fields=="on"){ echo "checked"; } } ?>>
				<label for="cf7rgk_pass_specific_field_as_query">
					Pass specific fields from the form as URL query parameters      
				</label>
				<?php if(isset($selectively_fields) && !empty($selectively_fields)){ if($selectively_fields=="on"){ ?> <script>jQuery(document).ready(function () { jQuery(".cf7rgk-redirect-http-fields-hidden").show(); });</script> <?php } } ?>
				<span class="cf7rgk-fields cf7rgk-redirect-http-fields-hidden" style="display:none;">	
					<input type="text" id="cf7rgk-redirect-http-build-query-selectively-fields" class="field-hidden" placeholder="Fields to pass, separated by commas" name="cf7rgk-redirect-http_build_query_selectively_fields" value="<?php if(isset($http_build_query_selectively_fields) && !empty($http_build_query_selectively_fields)){ echo $http_build_query_selectively_fields; } ?>" >
				</span>	
			</div>
			<hr>
			<div class="cf7rgk-inner-input">
				<label >Here you can add scripts to run after form sent successfully.</label>
				<div class="cf7rgk-field-message"></div>
				<textarea id="cf7rgk-redirect-after-sent-script" name="cf7rgk-redirect-after_sent_script" rows="8" cols="100"> <?php if(isset($cf7rgk_redirect_after_sent_script) && !empty($cf7rgk_redirect_after_sent_script)){ echo wp_unslash($cf7rgk_redirect_after_sent_script); } ?></textarea>
				<input type="hidden" name="cf7rgk_redirect" value="<?php echo $nonce= wp_create_nonce('cf7rgk_redirect_option_nonce'); ?>">
			</div>
			<div class="field-notice field-notice-alert alert-notice" >
					<strong>
						Note:       
					</strong>
					Do not include <code>&lt;script&gt;</code> tags. If you will add script then Redirection will be stop.
			</div>	
		 </div>
<?php
	}
	
	static function wpcf7r_save_redirect_url($contact_form) {
		
		$cf7rgk_redirect_open_new_tab = $cf7rgk_redirect_http_build_query = $selectively_fields = "";
		$form_id = $contact_form->id();
		if(isset($_POST['cf7rgk_url']))							$cf7rgk_url=sanitize_text_field($_POST['cf7rgk_url']);
		if(isset($_POST['cf7rgk-redirect-external']))			$cf7rgk_redirect_external=sanitize_text_field($_POST['cf7rgk-redirect-external']);
		if(isset($_POST['cf7rgk-redirect-page']))				$cf7rgk_redirect_page=sanitize_text_field($_POST['cf7rgk-redirect-page']);
		if(isset($_POST['cf7rgk-redirect-post']))				$cf7rgk_redirect_post=sanitize_text_field($_POST['cf7rgk-redirect-post']);
		if(isset($_POST['cf7rgk-redirect-open-new-tab'])) 		$cf7rgk_redirect_open_new_tab=sanitize_text_field($_POST['cf7rgk-redirect-open-new-tab']);
		if(isset($_POST['cf7rgk-redirect-http-build-query'])) 	$cf7rgk_redirect_http_build_query=sanitize_text_field($_POST['cf7rgk-redirect-http-build-query']);
		if(isset($_POST['cf7rgk-redirect-http-build-query-selectively'])) 
			$selectively_fields=sanitize_text_field($_POST['cf7rgk-redirect-http-build-query-selectively']);
		if(isset($_POST['cf7rgk-redirect-after_sent_script']))
			$cf7rgk_redirect_after_sent_script=sanitize_text_field($_POST['cf7rgk-redirect-after_sent_script']);
		if(isset($_POST['cf7rgk-redirect-http_build_query_selectively_fields']))
			$http_build_query_selectively_fields=sanitize_text_field($_POST['cf7rgk-redirect-http_build_query_selectively_fields']);
		$cf7rgk['form_id']=$form_id;
		if(isset($cf7rgk_url))									$cf7rgk['cf7rgk_url']=$cf7rgk_url;
		if(isset($cf7rgk_redirect_external))					$cf7rgk['cf7rgk_redirect_external']=$cf7rgk_redirect_external;
		if(isset($cf7rgk_redirect_page))						$cf7rgk['cf7rgk_redirect_page']=$cf7rgk_redirect_page;
		if(isset($cf7rgk_redirect_post))						$cf7rgk['cf7rgk_redirect_post']=$cf7rgk_redirect_post;
		if(isset($cf7rgk_redirect_open_new_tab))				$cf7rgk['cf7rgk_redirect_open_new_tab']=$cf7rgk_redirect_open_new_tab;
		if(isset($cf7rgk_redirect_http_build_query))			$cf7rgk['cf7rgk_redirect_http_build_query']=$cf7rgk_redirect_http_build_query;
		if(isset($selectively_fields))							$cf7rgk['selectively_fields']=$selectively_fields;
		if(isset($cf7rgk_redirect_after_sent_script))			$cf7rgk['cf7rgk_redirect_after_sent_script']=$cf7rgk_redirect_after_sent_script;
		if(isset($http_build_query_selectively_fields))			$cf7rgk['http_build_query_selectively_fields']=$http_build_query_selectively_fields;
		
		$wpnonce_design = (isset($_POST['cf7rgk_redirect'])) ? sanitize_text_field($_POST['cf7rgk_redirect']) : '';
		if(wp_verify_nonce( $wpnonce_design, 'cf7rgk_redirect_option_nonce' ))
		{
			update_option('cf7rgk_redirect_url_'.$form_id ,$cf7rgk,$autoload = null );
			cf7rgk_redirect_admin::cf7rgk_success_option_msg("Save Setting!");
		}
		else
		{
			cf7rgk_redirect_admin::cf7rgk_failure_option_msg('Unable to save data!');
		}
		
	}
	static function  cf7rgk_success_option_msg($msg)
	{
		
		echo ' <div class="notice notice-success vdgk-success-msg is-dismissible"><p>'. $msg . '</p></div>';		
		
	}

	// Error message
	static function  cf7rgk_failure_option_msg($msg)
	{ 
		echo  '<div class="notice notice-error  is-dismissible"><p>' . $msg . '</p></div>';
			
	}
}