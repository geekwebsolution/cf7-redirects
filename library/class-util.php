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
		add_action( 'wp_ajax_cf7rgk_ajax_redirect_cf7', array("CF7RGK_util", "cf7rgk_ajax_redirect_cf7"));
		add_action( 'wp_ajax_nopriv_cf7rgk_ajax_redirect_cf7', array("CF7RGK_util", "cf7rgk_ajax_redirect_cf7"));
		
		if (is_admin()) {
            // Set up admin actions
			add_action( 'wpcf7_after_save', array( 'cf7rgk_redirect_admin', 'wpcf7r_save_redirect_url' ) );
			add_action( 'wpcf7_editor_panels', array( 'cf7rgk_redirect_admin', 'redirect_add_tab' ) );
			add_action('admin_enqueue_scripts', array('CF7RGK_util','cf7rgk_enqueue_admin_scripts'));
			
		} else {
            // Set up front end actions
            add_action('wp_enqueue_scripts', array('CF7RGK_util','cf7rgk_enqueue_scripts'));
			
			
            
		 }
    }
	static function redirect_cf7() {
	
	?>
	<script type="text/javascript">
	function setCookie(cname, cvalue, exdays) {
			var d = new Date();
			d.setTime(d.getTime() + (exdays*24*60*60*1000));
			var expires = "expires="+ d.toUTCString();
			document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	}
	
	function getCookie(cname) {
		var name = cname + "=";
		var decodedCookie = decodeURIComponent(document.cookie);
		var ca = decodedCookie.split(';');
		for(var i = 0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}
		return "";
	}
	document.addEventListener( 'wpcf7mailsent', function( event ) {
		
		jQuery.ajax({
				type : "post",
				dataType : "json",
				url : myAjax.ajaxurl, 
				data : {action: "cf7rgk_ajax_redirect_cf7", form_id : event.detail.contactFormId, inputs: event.detail.inputs},
				success: function(option) {
				var obj =  JSON.parse(JSON.stringify(option));
				
				var url=obj.url;
				console.log(url);
				if(obj.script=='off'){	
				if(url!="" && url != null)
					{
						if(obj.open_new=='on'){
								window.open(url);
								
						}
						else{
							location = url;
						}
					}
				}
				else{
					after_sent_script = obj.script ;
					var script=after_sent_script.replace(/\$~/g,"'").replace(/\$`/g,'"')
					eval( script );
					
				}	
			}
			})
		
	}, false );
	</script>
	<?php
	}
	
	static function cf7rgk_enqueue_scripts() {
		wp_enqueue_script('cf7rgk_redirect_front_script', plugins_url('/js/script.js', __FILE__), array('jquery'), CF7RGK_BUILD);
		wp_enqueue_style('cf7rgk_redirect_front_style', plugins_url('css/style.css', __FILE__), false, CF7RGK_BUILD);
	     wp_enqueue_script( "my_voter_script",  plugins_url('/js/cf7rgk_script_ajax.js', __FILE__), array('jquery'), CF7RGK_BUILD );
		wp_enqueue_script('custom'); //Name of the script. Should be unique.here is 'custom'
		wp_localize_script('my_voter_script', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ))); 
	}

	static function cf7rgk_enqueue_admin_scripts() {
		// Enqueue admin-end styles
		wp_enqueue_style("cf7rgk_front_select2", plugins_url('/css/select2.min.css', __FILE__), false, CF7RGK_BUILD);
		wp_enqueue_style('cf7rgk_redirect_front_script_admin', plugins_url('css/styles-admin.css', __FILE__), array('cf7rgk_front_select2'), CF7RGK_BUILD);
	
		// Enqueue admin-end scripts
		wp_enqueue_script('cf7rgk_select2', plugins_url('/js/select2.min.js', __FILE__), false, CF7RGK_BUILD);
		wp_enqueue_script('cf7rgk_redirect_front_style_admin', plugins_url('js/scripts-admin.js', __FILE__), array('jquery', 'cf7rgk_select2'), CF7RGK_BUILD);
	
		// Localize the admin-end script
		wp_localize_script('cf7rgk_redirect_front_style_admin', 'cf7rgkobj', array('ajaxurl' => admin_url('admin-ajax.php')));
	}

	static function cf7rgk_ajax_redirect_cf7() {
		if(isset($_POST['form_id']) && isset($_POST['inputs'])){
			$form_id=sanitize_text_field($_POST['form_id']);	
			$inputs =$_POST['inputs'];
			
			$redirection_option=get_option( 'cf7rgk_redirect_url_'.$form_id , $default = false );
			$url="";
			$open_tab="";
			if( strlen(trim($redirection_option['cf7rgk_redirect_after_sent_script']))==0 && empty(trim($redirection_option['cf7rgk_redirect_after_sent_script']))){
				$cf7rgk_url=$redirection_option['cf7rgk_url'];
				$parameter=array();
				$open_new_tab=$redirection_option['cf7rgk_redirect_open_new_tab'];
				$http_build_query=$redirection_option['cf7rgk_redirect_http_build_query'];
				$query_selectively_fields=$redirection_option['selectively_fields'];
				$http_build_query_selectively_fields=$redirection_option['http_build_query_selectively_fields'];
				$cf7_json=array();
				if($cf7rgk_url=="url_page_post"){
					$cf7rgk_redirect_page=$redirection_option['cf7rgk_redirect_page'];
					$cf7rgk_redirect_post=$redirection_option['cf7rgk_redirect_post'];
					if($cf7rgk_redirect_page>0){
						$url= get_permalink( $cf7rgk_redirect_page );
					}
					else{
						$url= get_permalink( $cf7rgk_redirect_post );
					}
					
				}
				else{
					$cf7rgk_redirect_external=$redirection_option['cf7rgk_redirect_external'];
					$url=  $cf7rgk_redirect_external;
				}
				if($open_new_tab=="on"){
					$open_tab="on";
				}
				if($http_build_query=="on"){
					 foreach($inputs as $key=>$val){
						$parameter[$val['name']]=sanitize_text_field($val['value']);
					}
					$para=http_build_query($parameter);
					$url=$url."?".$para;
				}
				if($query_selectively_fields=="on"){
					$field=explode(",",$http_build_query_selectively_fields);
					foreach($inputs as $key=>$val){
						foreach($field as $field_val){
							if($field_val==$val['name']){
								$parameter[$val['name']]=sanitize_text_field($val['value']);
							} 
						}	
						
					}
					if($http_build_query=="on"){
						$url=$url."?".http_build_query($parameter);
					}
					else{
						$url=$url."?".http_build_query($parameter);
					}			
				}
				$cf7_json['url']=$url;
				$cf7_json['open_new']=$open_tab;
				$cf7_json['script']="off";
				echo  json_encode($cf7_json);	
				die;
			}
			else{
				
				$cf7_json['url']=$url;
				$cf7_json['open_new']=$open_tab;
				$cf7_json['script']=str_replace('"',"$`",str_replace("'","$~",wp_unslash($redirection_option['cf7rgk_redirect_after_sent_script'])));
				echo  json_encode($cf7_json);	
				die;
			}
		}
		else{
			$cf7_json['url']="";
				$cf7_json['open_new']="";
				$cf7_json['script']="";
				echo  json_encode($cf7_json);
			die;				
		}
	}

	} // end class 
	 
	 