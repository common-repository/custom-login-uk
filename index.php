<?php

/*

Plugin name: Custom login - UK
Description: This is plugin will generate a form with shortcode that will authenticate user by valid postcode and 9 charachter generated username
Author: Md. Sarwar-A-Kawsar
Author URI: https://fiverr.com/sa_kawsar
Version: 1.0
*/

defined('ABSPATH') or die('You cannot access to this page');
function custom_login_uk_activate(){
	$user_name = 'custom_user_uk';
	$password = md5($user_name);
	$user_exists = username_exists( $user_name );
	if(!$user_exists){
		$user_id = wp_create_user($user_name,$password);
		update_option('custom_user_uk',$user_id);
	}
}
register_activation_hook( __FILE__, 'custom_login_uk_activate' );

add_shortcode( 'custom_login_form', 'custom_login_uk_form_callback' );
function custom_login_uk_form_callback(){
	if(isset($_POST['custom_login'])){
		$username = sanitize_text_field( $_POST['username'] );
		$password = sanitize_text_field( $_POST['password'] );
		if(is_numeric($username) && strlen($username)==9 && substr($username,0,1)==1){
			if(custom_login_uk_IsPostcode($password)==1){
				$user_id = get_option( 'custom_user_uk' );
				$user = get_user_by( 'id', $user_id ); 
				if( $user ) {
				    wp_set_current_user( $user_id, $user->user_login );
				    wp_set_auth_cookie( $user_id );
				    do_action( 'wp_login', $user->user_login, $user );
				}
			} else {
				echo esc_html('<script>alert("Invalid postcode")</script>');
			}
		} else {
			echo esc_html('<script>alert("Invalid username")</script>');
		}
	}
	if(!is_user_logged_in()){
		$output = '<form method="post">';
		$output .= '<label style="display:block;">Username</label>';
		$output .= '<input style="padding:8px;" type="text" name="username" placeholder="Username"><br><br>';
		$output .= '<label style="display:block;">Postcode</label>';
		$output .= '<input style="padding:8px;" type="password" name="password" placeholder="Postcode"><br><br>';
		$output .= '<input style="padding:8px;min-width:80px;font-weight:600;" type="submit" name="custom_login" value="login">';
		$output .= '</form>';
	} else {
		$output = 'Successfully logged in.<a href="'.wp_logout_url(get_permalink()).'">Logout</a>';
	}
	echo $output;
	return ob_get_clean();
}

function custom_login_uk_IsPostcode($postcode)
{
    $postcode = strtoupper(str_replace(' ','',$postcode));
    if(preg_match("/(^[A-Z]{1,2}[0-9R][0-9A-Z]?[\s]?[0-9][ABD-HJLNP-UW-Z]{2}$)/i",$postcode) || preg_match("/(^[A-Z]{1,2}[0-9R][0-9A-Z]$)/i",$postcode))
    {    
        return true;
    }
    else
    {
        return false;
    }
}