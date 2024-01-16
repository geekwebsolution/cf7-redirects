<?php
/**
 * Utility functions for the plugin
 *
 * @author Geek Web Solution
 */
class CF7RGK_util {
	public function __construct() {
		$this->plugin_url       = plugin_dir_url( __FILE__ );
		$this->plugin_path      = plugin_dir_path( __FILE__ );
		$this->version          = '1.2.8';
		$this->CF7RGK_setup();
	}

    static function CF7RGK_setup() {
        // Come here when the plugin is run
		add_action( 'wp_footer', array('CF7RGK_util','redirect_cf7'));
		
		if (is_admin()) {
            // Set up admin actions
			add_action( 'wpcf7_after_save', array( 'cf7rgk_redirect_admin', 'wpcf7r_save_redirect_url' ) );
			add_action( 'wpcf7_editor_panels', array( 'cf7rgk_redirect_admin', 'redirect_add_tab' ) );
			add_action( 'admin_enqueue_scripts', array('CF7RGK_util','cf7rgk_enqueue_admin_scripts'));
		} else {
            // Set up front end actions
			add_action( 'wpcf7_mail_sent', array( 'CF7RGK_util', 'cf7rgk_after_mail_sent_call' ) );
		}
    }

	/**
	 * internal script for redirection
	 */
	static function redirect_cf7() { ?>
		<script type="text/javascript">
		function setCookie(name, value, days) {
			var expires = "";
			if (days) {
				var date = new Date();
				date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
				expires = "; expires=" + date.toUTCString();
			}
			document.cookie = name + "=" + (value || "") + expires + "; path=/";
		}
		function getCookie(cname) {
			let name = cname + "=";
			let decodedCookie = decodeURIComponent(document.cookie);
			let ca = decodedCookie.split(';');
			for (let i = 0; i < ca.length; i++) {
				let c = ca[i];
				while (c.charAt(0) == ' ') {
					c = c.substring(1);
				}
				if (c.indexOf(name) == 0) {
					return c.substring(name.length, c.length);
				}
			}
			return "";
		}
		function eraseCookie(name) {
			setCookie(name, "", -1);
		}

		document.addEventListener( 'wpcf7mailsent', function( event ) {
			var cf7rgk_json = getCookie("cf7rgk_options");
    		var cf7rgk_opt = JSON.parse(cf7rgk_json);
			
			console.log(cf7rgk_opt);
			if (cf7rgk_opt != '') {
				if(cf7rgk_opt.url != '') {
					if (cf7rgk_opt.open_new == "on") {
						window.open(cf7rgk_opt.url, '_blank');
					} else {
						window.open(cf7rgk_opt.url, '_self');
					}
				}
			}

			eraseCookie("cf7rgk_options");
		});

		</script>
		<?php
	}

	/**
	 * Enqueue admin scripts
	 */
	static function cf7rgk_enqueue_admin_scripts( $hook ) {
		if($hook == 'toplevel_page_wpcf7') {
			// Enqueue admin-end styles
			wp_enqueue_style("cf7rgk_front_select2", plugins_url('/css/select2.min.css', __FILE__), false, CF7RGK_BUILD);
			wp_enqueue_style('cf7rgk_redirect_front_script_admin', plugins_url('css/styles-admin.css', __FILE__), array('cf7rgk_front_select2'), CF7RGK_BUILD);

			// Enqueue admin-end scripts
			wp_enqueue_script('cf7rgk_select2', plugins_url('/js/select2.min.js', __FILE__), false, CF7RGK_BUILD);
			wp_enqueue_script('cf7rgk_redirect_front_style_admin', plugins_url('js/scripts-admin.js', __FILE__), array('jquery', 'cf7rgk_select2'), CF7RGK_BUILD);
			wp_localize_script('cf7rgk_redirect_front_style_admin', 'cf7rgkobj', array('ajaxurl' => admin_url('admin-ajax.php')));
		}	
	}

	/**
	 * Form data response
	 */
	static function fields_response($form_id, $inputs, $specific_fields = array()) {
		$new_fields = array();
		if(!$specific_fields)	$specific_fields = array();

		$cf7cw_option = get_option( 'cf7cw_connect_wh_' . $form_id , $default = false );
		$string = wp_unslash($cf7cw_option['cf7cw_message_body']);

		$ContactForm = WPCF7_ContactForm::get_instance($form_id);
		$form_fields = $ContactForm->scan_form_tags();
		
		if(isset($inputs) && !empty($inputs)){
			foreach ($inputs as $key => $value) {				
				if(isset($specific_fields) && !empty($specific_fields)) {
					if(!in_array($key,$specific_fields)) {
						continue;
					}
				}
				
				if(is_array($value)) $value = implode(", ",$value);
				$new_fields[$key] = $value;
			}
		}
		return $new_fields;
	}

	/**
	 * After mail sent - Contact form 7
	 */
	static function cf7rgk_after_mail_sent_call( $contactform ) {
		$cf7_json = [];
		$form_id = $contactform->id();
		$redirection_option = get_option( 'cf7rgk_redirect_url_'.$form_id , $default = false );
		
		$url = $open_tab = "";
		if( strlen(trim($redirection_option['cf7rgk_redirect_after_sent_script'])) == 0 && empty(trim($redirection_option['cf7rgk_redirect_after_sent_script'])) ) {
			$inputs = array();
			$submission = WPCF7_Submission::get_instance();
			$inputs = $submission->get_posted_data();

			$cf7rgk_url 							= $redirection_option['cf7rgk_url'];
			$open_new_tab 							= $redirection_option['cf7rgk_redirect_open_new_tab'];
			$http_build_query 						= $redirection_option['cf7rgk_redirect_http_build_query'];
			$query_selectively_fields 				= $redirection_option['selectively_fields'];
			$http_build_query_selectively_fields 	= $redirection_option['http_build_query_selectively_fields'];

			if($open_new_tab=="on")	$open_tab = "on";

			if($cf7rgk_url == "url_page_post"){
				$cf7rgk_redirect_page	= $redirection_option['cf7rgk_redirect_page'];
				$cf7rgk_redirect_post	= $redirection_option['cf7rgk_redirect_post'];
				if($cf7rgk_redirect_page > 0){
					$url= get_permalink( $cf7rgk_redirect_page );
				}else{
					$url= get_permalink( $cf7rgk_redirect_post );
				}				
			}
			else{
				$cf7rgk_redirect_external=$redirection_option['cf7rgk_redirect_external'];
				$url=  $cf7rgk_redirect_external;
			}

			if($http_build_query=="on") {
				$parameter = CF7RGK_util::fields_response($form_id, $inputs);
				$para	= http_build_query($parameter);
				$url	= $url . "?" . $para;
			}

			if($query_selectively_fields=="on") {
				$specific_fields	= explode(",",$http_build_query_selectively_fields);
				$parameter = CF7RGK_util::fields_response($form_id, $inputs, $specific_fields);

				if($http_build_query=="on"){
					$url = $url."?".http_build_query($parameter);
				}else{
					$url = $url."?".http_build_query($parameter);
				}
			}

			$cf7_json['url']	= $url;
			$cf7_json['script'] = "off";
			$cf7_json['open_new'] = $open_tab;
		}else{
				
			$cf7_json['url']=$url;
			$cf7_json['open_new']=$open_tab;
			$cf7_json['script']=str_replace('"',"$`",str_replace("'","$~",wp_unslash($redirection_option['cf7rgk_redirect_after_sent_script'])));
		}

		$cookie_name = "cf7rgk_options";
        setcookie($cookie_name, json_encode($cf7_json), time() + (86400 * 30), "/");
	}

} // end class 