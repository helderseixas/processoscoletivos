<?php
/**
 * Plugin Name:       Personalize Login
 * Description:       A plugin that replaces the WordPress login flow with a custom page.
 * Version:           1.0.0
 * Author:            Jarkko Laine
 * License:           GPL-2.0+
 * Text Domain:       personalize-login
 */
 
class Personalize_Login_Plugin {
	
    /**
     * Initializes the plugin.
     *
     * To keep the initialization fast, only add filter and action
     * hooks in the constructor.
     */
    public function __construct() {
		//Login form
		add_shortcode( 'custom-login-form', array( $this, 'render_login_form' ) );     
		add_action( 'login_form_login', array( $this, 'redirect_to_custom_login' ) );		

		//Login admin form
		add_shortcode( 'custom-login-admin-form', array( $this, 'render_login_admin_form' ) );
		add_filter( 'login_redirect', array( $this, 'redirect_after_login' ), 10, 3 );
		
		//Logout
		add_action( 'wp_logout', array( $this, 'redirect_after_logout' ) );		
		
		//Certificado social
		add_shortcode( 'certificado-social-form', array( $this, 'render_certificado_social_form' ) );    
		add_action('init', array( $this, 'authenticate_certificado_social')); 
    }

	/**
	 * Plugin activation hook.
	 *
	 * Creates all WordPress pages needed by the plugin.
	 */
	public static function plugin_activated() {
		// Information needed for creating the plugin's pages
		$page_definitions = array(
		    'member-login' => array(
		        'title' => __( 'Acessar Sistema', 'personalize-login' ),
		        'content' => '[custom-login-form]'
		    ),
		    'admin-login' => array(
		        'title' => __( 'Acessar Sistema - Administradores', 'personalize-login' ),
		        'content' => '[custom-login-admin-form]'
		    ),
			'certificado-social' => array(
		        'title' => __( 'Certificado Social', 'personalize-login' ),
		        'content' => '[certificado-social-form]'
		    ),
		);
		 
	    foreach ( $page_definitions as $slug => $page ) {
	        // Check that the page doesn't exist already
	        $query = new WP_Query( 'pagename=' . $slug );
	        if ( ! $query->have_posts() ) {
	            // Add the page using the data from the array above
	            wp_insert_post(
	                array(
	                    'post_content'   => $page['content'],
	                    'post_name'      => $slug,
	                    'post_title'     => $page['title'],
	                    'post_status'    => 'publish',
	                    'post_type'      => 'page',
	                    'ping_status'    => 'closed',
	                    'comment_status' => 'closed',
	                )
	            );
	        }
	    }
	}

	/**
	 * A shortcode for rendering the login form.
	 *
	 * @param  array   $attributes  Shortcode attributes.
	 * @param  string  $content     The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 */
	public function render_login_form( $attributes, $content = null ) {
	    // Parse shortcode attributes
	    $default_attributes = array( 'show_title' => false );
	    $attributes = shortcode_atts( $default_attributes, $attributes );
	    $show_title = $attributes['show_title'];
	 
	    if ( is_user_logged_in() ) {
	        return __( 'Você já está logado.', 'personalize-login' );
	    }
	    
		// Check if the user just registered
		$attributes['registered'] = isset( $_REQUEST['registered'] );
	     
	    // Pass the redirect parameter to the WordPress login functionality: by default,
	    // don't specify a redirect, but if a valid redirect URL has been passed as
	    // request parameter, use it.
	    $attributes['redirect'] = '';
	    if ( isset( $_REQUEST['redirect_to'] ) ) {
	        $attributes['redirect'] = wp_validate_redirect( $_REQUEST['redirect_to'], $attributes['redirect'] );
	    }
	
		// Error messages
		$errors = array();
		if ( isset( $_REQUEST['login'] ) ) {
		    $error_codes = explode( ',', $_REQUEST['login'] );
		 
		    foreach ( $error_codes as $code ) {
		        $errors []= $this->get_error_message( $code );
		    }
		}
		$attributes['errors'] = $errors;
		
		// Check if user just logged out
		$attributes['logged_out'] = isset( $_REQUEST['logged_out'] ) && $_REQUEST['logged_out'] == true;
			     
	    // Render the login form using an external template
	    return $this->get_template_html( 'login_form', $attributes );
	}
	
	/**
	 * A shortcode for rendering the login admin form.
	 *
	 * @param  array   $attributes  Shortcode attributes.
	 * @param  string  $content     The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 */
	public function render_login_admin_form( $attributes, $content = null ) {
	    // Parse shortcode attributes
	    $default_attributes = array( 'show_title' => false );
	    $attributes = shortcode_atts( $default_attributes, $attributes );
	    $show_title = $attributes['show_title'];
	 
	    if ( is_user_logged_in() ) {
	        return __( 'Você já está logado.', 'personalize-login' );
	    }
	    
		// Check if the user just registered
		$attributes['registered'] = isset( $_REQUEST['registered'] );
	     
	    // Pass the redirect parameter to the WordPress login functionality: by default,
	    // don't specify a redirect, but if a valid redirect URL has been passed as
	    // request parameter, use it.
	    $attributes['redirect'] = '';
	    if ( isset( $_REQUEST['redirect_to'] ) ) {
	        $attributes['redirect'] = wp_validate_redirect( $_REQUEST['redirect_to'], $attributes['redirect'] );
	    }
	
		// Error messages
		$errors = array();
		if ( isset( $_REQUEST['login'] ) ) {
		    $error_codes = explode( ',', $_REQUEST['login'] );
		 
		    foreach ( $error_codes as $code ) {
		        $errors []= $this->get_error_message( $code );
		    }
		}
		$attributes['errors'] = $errors;
		
		// Check if user just logged out
		$attributes['logged_out'] = isset( $_REQUEST['logged_out'] ) && $_REQUEST['logged_out'] == true;
			     
	    // Render the login form using an external template
	    return $this->get_template_html( 'login_admin_form', $attributes );
	}	
	
	function authenticate_certificado_social() {				
		$page_request = explode("/", $_SERVER['REQUEST_URI'])[2];
		if($page_request == "certificado-social"){		
			$token = $_REQUEST['access_token'];
	    
			$authorization = "Authorization: Bearer ".$token;
						
			$ch = curl_init("http://localhost:8080/certificadora-social-oauth2/endpoints/seguranca/usuario/logado");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json' , $authorization ));
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");		
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);		
			curl_close($ch);
			$json = json_decode($result);
			
			if($json->usuario->login != null){			
				$name = $json->usuario->nome;			    	    
				$login = $json->usuario->login;
				$email = $json->usuario->email;	   
			 
				if ( !username_exists( $login ) ) {						
					$result = $this->register_user_by_oauth($login, $email, $name );
				}	
				
				$user = get_user_by( 'login', $login );	
				wp_clear_auth_cookie();	
				wp_set_current_user($user->ID, $login);
				wp_set_auth_cookie($user->ID);			
			}
		}		
	}
	
	public function render_certificado_social_form( $attributes, $content = null ) {	    		
		if ( is_user_logged_in() ) {
			return __( 'Logado com sucesso.', 'personalize-login' );
		}else{
			return __( 'Erro!', 'personalize-login' );
		}							
	}	
	
	private function register_user_by_oauth($login, $email, $name) {
	    // Generate the password 
	    $password = wp_generate_password(12, false );
	 
	    $user_data = array(
	        'user_login'    => $login,
	        'user_email'    => $email,
	        'user_pass'     => $password,
	        'first_name'    => $name,
	        'nickname'      => $name,
	    );
	 
	    $user_id = wp_insert_user( $user_data );	    
	 
	    return $user_id;
	}	

	/**
	 * Renders the contents of the given template to a string and returns it.
	 *
	 * @param string $template_name The name of the template to render (without .php)
	 * @param array  $attributes    The PHP variables for the template
	 *
	 * @return string               The contents of the template.
	 */
	private function get_template_html( $template_name, $attributes = null ) {
	    if ( ! $attributes ) {
	        $attributes = array();
	    }
	 
	    ob_start();
	 
	    do_action( 'personalize_login_before_' . $template_name );
	 
	    require( 'templates/' . $template_name . '.php');
	 
	    do_action( 'personalize_login_after_' . $template_name );
	 
	    $html = ob_get_contents();
	    ob_end_clean();
	 
	    return $html;
	}
		
	/**
	 * Redirect the user to the custom login page instead of wp-login.php.
	 */
	function redirect_to_custom_login() {
	    if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
	        $redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : null;
	     
	        if ( is_user_logged_in() ) {
	            $this->redirect_logged_in_user( $redirect_to );
	            exit;
	        }
	 
	        // The rest are redirected to the login page
	        $login_url = home_url( 'member-login' );
	        if ( ! empty( $redirect_to ) ) {
	            $login_url = add_query_arg( 'redirect_to', $redirect_to, $login_url );
	        }
	 
	        wp_redirect( $login_url );
	        exit;
	    }
	}
	
	/**
	 * Redirects the user to the correct page depending on whether he / she
	 * is an admin or not.
	 *
	 * @param string $redirect_to   An optional redirect_to URL for admin users
	 */
	private function redirect_logged_in_user( $redirect_to = null ) {
	    $user = wp_get_current_user();
	    if ( user_can( $user, 'manage_options' ) ) {
	        if ( $redirect_to ) {
	            wp_safe_redirect( $redirect_to );
	        } else {
	            wp_redirect( admin_url() );
	        }
	    } else {
	        wp_redirect( home_url( 'member-account' ) );
	    }
	}	     	
		
	/**
	 * Finds and returns a matching error message for the given error code.
	 *
	 * @param string $error_code    The error code to look up.
	 *
	 * @return string               An error message.
	 */
	private function get_error_message( $error_code ) {
	    switch ( $error_code ) {
	        case 'empty_username':
	            return __( 'Informe seu nome de usuário ou email.', 'personalize-login' );
	 
	        case 'empty_password':
	            return __( 'Informe sua senha.', 'personalize-login' );
	 
	        case 'invalid_username':
	            return __(
	                "Usuario não encontrado. Você já possui um cadastro?",
	                'personalize-login'
	            );	 
	        case 'incorrect_password':
	            $err = __(
	                "Senha incorreta. <a href='%s'>Você esqueceu</a>?",
	                'personalize-login'
	            );
	            return sprintf( $err, wp_lostpassword_url() );			 
	 
	        default:
	            break;
	    }
	     
	    return __( 'Um erro desconhecido ocorreu. Por favor, tente novamente mais tarde.', 'personalize-login' );
	}
	
	/**
	 * Redirect to custom login page after the user has been logged out.
	 */
	public function redirect_after_logout() {
	    $redirect_url = home_url( 'member-login?logged_out=true' );
	    wp_safe_redirect( $redirect_url );
	    exit;
	}
	
	/**
	 * Returns the URL to which the user should be redirected after the (successful) login.
	 *
	 * @param string           $redirect_to           The redirect destination URL.
	 * @param string           $requested_redirect_to The requested redirect destination URL passed as a parameter.
	 * @param WP_User|WP_Error $user                  WP_User object if login was successful, WP_Error object otherwise.
	 *
	 * @return string Redirect URL
	 */
	public function redirect_after_login( $redirect_to, $requested_redirect_to, $user ) {
	    $redirect_url = home_url();
	 
	    if ( ! isset( $user->ID ) ) {
	        return $redirect_url;
	    }
	 
	    if ( user_can( $user, 'manage_options' ) ) {
	        // Use the redirect_to parameter if one is set, otherwise redirect to admin dashboard.
	        if ( $requested_redirect_to == '' ) {
	            $redirect_url = admin_url();
	        } else {
	            $redirect_url = $requested_redirect_to;
	        }
	    } else {
	        // Non-admin users always go to their account page after login
	        $redirect_url = home_url( 'member-account' );
	    }
	 
	    return wp_validate_redirect( $redirect_url, home_url() );
	}
					
}
 
// Initialize the plugin
$personalize_login_pages_plugin = new Personalize_Login_Plugin();

// Create the custom pages at plugin activation
register_activation_hook( __FILE__, array( 'Personalize_Login_Plugin', 'plugin_activated' ) );
