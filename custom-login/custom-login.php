<?php
/**
 * Plugin Name:       Custom Login
 * Description:       A plugin that replaces the WordPress login flow with a custom page.
 * Version:           1.0.0
 * Author:            Custom Plugin
 * License:           GPL-2.0+
 * Text Domain:       custom-login
 */
 
class Custom_Login_Plugin {
 
    /**
     * Initializes the plugin.
     *
     * To keep the initialization fast, only add filter and action
     * hooks in the constructor.
     */
    public function __construct() {

        /** For enqueue style file and script file */
        add_action('wp_enqueue_scripts', array( $this,  'enqueue_block_editor_scripts' ));

        /** For add extra field in admin site in user profile page */
        add_action( 'show_user_profile', array( $this,  'extra_user_profile_fields') );
        add_action( 'edit_user_profile', array( $this, 'extra_user_profile_fields') );
        /** For save extra field for from user profile page */
        add_action( 'personal_options_update', array( $this, 'save_extra_user_profile_fields') );
        add_action( 'edit_user_profile_update', array( $this, 'save_extra_user_profile_fields') );
        /** Create Shortcode For Login Page */
        add_shortcode( 'custom-login-form', array( $this, 'redirect_login_form' ) );
        /** redirect custom login page instead of wordpress login page */
        add_action( 'login_form_login', array( $this, 'redirect_to_custom_login' ) );
        /** check user authenticate user is valid or not for custom login page */
        add_filter( 'authenticate', array( $this, 'authenticate_user' ), 101, 3 );
        /** For redirect after user logout */
        add_action( 'wp_logout', array( $this, 'redirect_after_logout' ) );
        /** Create shortcode for register form */
        add_shortcode( 'custom-register-form', array( $this, 'redirect_register_form' ) );
        /** Ajax function for login page */
        add_action('wp_ajax_custom_login', array( $this, 'custom_login' )); 
        add_action('wp_ajax_nopriv_custom_login', array( $this, 'custom_login')); 
        /** Ajax function for register page */
        add_action('wp_ajax_custom_register', array( $this, 'custom_register' )); 
        add_action('wp_ajax_nopriv_custom_register', array( $this, 'custom_register'));
        /** Ajax function for forgot password page */
        add_action('wp_ajax_custom_forgot_password', array( $this, 'custom_forgot_password' )); 
        add_action('wp_ajax_nopriv_custom_forgot_password', array( $this, 'custom_forgot_password')); 
        /** Ajax function for reset password page */
        add_action('wp_ajax_custom_reset_password', array( $this, 'custom_reset_password' )); 
        add_action('wp_ajax_nopriv_custom_reset_password', array( $this, 'custom_reset_password')); 
        /** Create shortcode for forgot password */
        add_shortcode( 'custom-password-lost-form', array( $this, 'redirect_password_forgot_form' ) );
        /** Create shortcode for reset password */
        add_shortcode( 'custom-password-reset-form', array( $this, 'redirect_password_reset_form' ) );
        
        ob_start();
    }

    public function enqueue_block_editor_scripts() {
		if ( empty( $GLOBALS['post'] ) ) {
			return;
		}

        wp_enqueue_script('jquery-plugin',plugins_url( 'js/jquery.min.js', __FILE__ ), array( ), '1.4', true );
		wp_enqueue_script('custom-plugin', plugins_url( 'js/custom.js', __FILE__ ), array( ), '1.4', true );
        wp_localize_script( 'custom-plugin', 'custom',
            array( 
                'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php',
            )
        );
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
                'title' => __( 'Sign In', 'custom-login' ),
                'content' => '[custom-login-form]'
            ),
            'member-register' => array(
                'title' => __( 'Register', 'custom-login' ),
                'content' => '[custom-register-form]'
            ),
            'member-password-lost' => array(
                'title' => __( 'Forgot Your Password?', 'custom-login' ),
                'content' => '[custom-password-lost-form]'
            ),
            'member-password-reset' => array(
                'title' => __( 'Reset New Password', 'custom-login' ),
                'content' => '[custom-password-reset-form]'
            )
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
    public function redirect_login_form( $attributes, $content = null ) {
        // Parse shortcode attributes
        $default_attributes = array( 'show_title' => false );
        $attributes = shortcode_atts( $default_attributes, $attributes );
        $show_title = $attributes['show_title'];
    
        if ( is_user_logged_in() ) {
            wp_redirect(site_url("/"));
            // return __( 'You are already signed in.', 'custom-login' );
        }else{}
        
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
        
        // Render the login form using an external template
        return $this->get_template_html( 'login_form', $attributes );
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
     * Redirect the user after authentication if there were any errors.
     *
     * @param Wp_User|Wp_Error  $user       The signed in user, or the errors that have occurred during login.
     * @param string            $username   The user name used to log in.
     * @param string            $password   The password used to log in.
     *
     * @return Wp_User|Wp_Error The logged in user, or error information if there were errors.
     */
    function authenticate_user( $user, $username, $password ) {
        session_start();
        // Check if the earlier authenticate filter (most likely, 
        // the default WordPress authentication) functions have found errors
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            if ( is_wp_error( $user ) ) {
                $error_codes = join( ',', $user->get_error_codes() );
    
                $login_url = home_url( 'member-login' );
                
                $login_url = add_query_arg( 'login', $error_codes, $login_url );
                $_SESSION["user_status"] =  "active";
                $_SESSION["redirect_url"] = $login_url;
                wp_redirect( $login_url );
                exit;
            }
        }
        // check user is activate or deactivate
        $user_status = get_user_meta( $user->ID, 'user-status', true  );
        if( $user_status  == "active"){
            wp_redirect($redirect_url);
            return $user;
        }else{
            $redirect_url = site_url( '/member-login' );
            $_SESSION["user_status"] =  "deactivate";
            $_SESSION["redirect_url"] =  $redirect_url;
            wp_redirect($redirect_url);
        }
    }

    /**
     * Redirect to custom login page after the user has been logged out.
     */
    public function redirect_after_logout() {
        session_start();
        $redirect_url = home_url( 'member-login?logged_out=true' );
        unset($_SESSION['user_status']);
        wp_safe_redirect( $redirect_url );
        exit;
    }

     /**
     * A shortcode for rendering the new user registration form.
     *
     * @param  array   $attributes  Shortcode attributes.
     * @param  string  $content     The text content for shortcode. Not used.
     *
     * @return string  The shortcode output
     */
    public function redirect_register_form( $attributes, $content = null ) {
        // Parse shortcode attributes
        $default_attributes = array( 'show_title' => false );
        $attributes = shortcode_atts( $default_attributes, $attributes );
        
        // Retrieve possible errors from request parameters
        $attributes['errors'] = array();
        if ( isset( $_REQUEST['register-errors'] ) ) {
            $error_codes = explode( ',', $_REQUEST['register-errors'] );
        
            foreach ( $error_codes as $error_code ) {
                $attributes['errors'] []= $this->get_error_message( $error_code );
            }
        }
        if ( is_user_logged_in() ) {
            wp_redirect(site_url("/"));
            // return __( 'You are already signed in.', 'custom-login' );
        } elseif ( ! get_option( 'users_can_register' ) ) {
            return __( 'Registering new users is currently not allowed.', 'custom-login' );
        } else {
            return $this->get_template_html( 'register_form', $attributes );
        }
    }

    public function extra_user_profile_fields( $user ) { ?>
        <h3><?php _e("Extra profile information", "blank"); ?></h3>

        <table class="form-table">
            <tr>
                <th><label for="middle-name"><?php _e("Middle Name"); ?></label></th>
                <td>
                    <input type="text" name="middle-name" id="middle-name" value="<?php echo esc_attr( get_the_author_meta( 'middle-name', $user->ID ) ); ?>" class="regular-text" /><br />
                    <span class="description"><?php _e("Please enter your middle name."); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="phone-number"><?php _e("Phone Number"); ?></label></th>
                <td>
                    <input type="text" name="phone-number" id="phone-number" value="<?php echo esc_attr( get_the_author_meta( 'phone-number', $user->ID ) ); ?>" class="regular-text" /><br />
                    <span class="description"><?php _e("Please enter your phone number."); ?></span>
                </td>
            </tr>	
            <tr>
                <th><label for="address"><?php _e("Address"); ?></label></th>
                <td>
                <textarea type="text" name="address" id="address" class="regular-text" cols="30" rows="5"><?php echo esc_attr( get_the_author_meta( 'address', $user->ID ) ); ?></textarea><br />
                    <span class="description"><?php _e("Please enter your address."); ?></span>
                </td>
            </tr>
            <tr>
                <th>
                        <label for="user-status"><?php _e("User Status"); ?></label>
                </th>
                <td>
                    <?php
                        $user_status = get_the_author_meta( 'user-status', $user->ID );
                        $active = "";
                        $deactive = "";
                        if($user_status == "active"){
                            $active = "checked";
                            $deactive = ""; 
                        }
                        if($user_status == "deactive"){
                            $deactive = "checked"; 
                            $active = "";
                        }
                    ?>
                    <input type="radio" name="user-status" <?php echo $active; ?> value="active"> Active
                    <input type="radio" name="user-status" <?php echo $deactive; ?> value="deactive"> Deactive
                </td>
            </tr>
        </table>
    <?php }


    public function save_extra_user_profile_fields( $user_id ) {

        if ( !current_user_can( 'edit_user', $user_id ) ) { 
            return false; 
        }

            update_user_meta( $user_id, 'address', $_POST['address'] );
            update_user_meta( $user_id, 'middle-name', $_POST['middle-name'] );
            update_user_meta( $user_id, 'phone-number', $_POST['phone-number'] );
            update_user_meta( $user_id, 'user-status', $_POST['user-status'] );
    }

    /**
     * A shortcode for rendering the form used to initiate the password reset.
     *
     * @param  array   $attributes  Shortcode attributes.
     * @param  string  $content     The text content for shortcode. Not used.
     *
     * @return string  The shortcode output
     */
    public function redirect_password_forgot_form( $attributes, $content = null ) {
        // Parse shortcode attributes
        $default_attributes = array( 'show_title' => false );
        $attributes = shortcode_atts( $default_attributes, $attributes );
        // Retrieve possible errors from request parameters
        $attributes['errors'] = array();
        if ( isset( $_REQUEST['errors'] ) ) {
            $error_codes = explode( ',', $_REQUEST['errors'] );
        
            foreach ( $error_codes as $error_code ) {
                $attributes['errors'] []= $this->get_error_message( $error_code );
            }
        }
        if ( is_user_logged_in() ) {
            return __( 'You are already signed in.', 'custom-login' );
        } else {
            return $this->get_template_html( 'password_lost_form', $attributes );
        }
    }

    /**
     * A shortcode for rendering the form used to reset a user's password.
     *
     * @param  array   $attributes  Shortcode attributes.
     * @param  string  $content     The text content for shortcode. Not used.
     *
     * @return string  The shortcode output
     */
    public function redirect_password_reset_form( $attributes, $content = null ) {
        // Parse shortcode attributes
        $default_attributes = array( 'show_title' => false );
        $attributes = shortcode_atts( $default_attributes, $attributes );
    
        if ( is_user_logged_in() ) {
            return __( 'You are already signed in.', 'custom-login' );
        } else {

                return $this->get_template_html( 'password_reset_form', $attributes );
        }
    }
    /**
     * Ajax function for login page
     */
    function custom_login(){
            session_start();

            unset($_SESSION["register_message"]);
            unset($_SESSION["err_email"]);
            unset($_SESSION["reset_pass_message"]);
            unset($_SESSION["user_status"]);

        	$email =  sanitize_text_field($_POST["email"]);
        	$password =  sanitize_text_field($_POST["password"]);

        	$user = get_user_by( 'email', $email );
        	$id = $user->ID;
            $hash = $user->data->user_pass;
            
            $username = $user->data->user_login;

            
            // check password and wordpress password same or not 
            $password_check = wp_check_password( $password, $hash, $id );

            $check_authenticate = wp_authenticate( $username, $password );
            $response = array();
            $response["username"] =  $username;
            $response["password"] =  $password;

            $response['in_valid_email'] = "";
        	if($user){
                $user_check = wp_authenticate_email_password( $user, $email, $password );
                if($user_check) {
                    $display_name = $user_check->display_name;
                    $user_login = $user_check->user_login;
                    $creds = array();
                    $creds['user_login'] = $user_check->user_login;
                    $creds['user_password'] = $password;
                    $creds['remember'] = false;
                    
                    $user_status = get_user_meta( $user->ID, 'user-status', true  );

                    if($user_status  == "active"){
                        $user_signon = wp_signon($creds);
                        $response["user_sign"] =  $user_signon;
                        if ( is_wp_error( $user_signon ) ) {
                            
                            $response["error"] =  $user_signon->get_error_message();
                        }else{
                            $response["redirect_url"] =  site_url("/");
                        }
                    }else{
                        $response["redirect_url"] =  site_url("/member-login");
                    }
                       
                } else {
                    // $response["redirect_url"] = $_SESSION["redirect_url"];
                    $response["error"] =  "Invalid login credentials.";
                }
        	}else{
                $response['in_valid_email'] = "Your email id not register.";
                $response["redirect_url"] =  site_url("/");
            }
            $response["session"] = $_SESSION;
        	echo json_encode($response);
        	wp_die(); 
    }
    /**
     * Ajax function for register page
     */
    function custom_register(){
        session_start();
        $result_register = array();
        $first_name = sanitize_text_field( $_POST['first_name'] );
        $middle_name = sanitize_text_field( $_POST['middle_name'] );
        $last_name = sanitize_text_field( $_POST['last_name'] );
        $email = $_POST['email'];
        $phone_number = sanitize_text_field( $_POST['phone_number'] );
        $address = sanitize_text_field( $_POST['address'] );
        $password = sanitize_text_field( $_POST['password'] );
        $confirm_password = sanitize_text_field( $_POST['confirm_password'] );
        $user_status = sanitize_text_field( $_POST['deactive'] );

        $pattern = '/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{8,20}$/';

        $user_data = array(
            'user_login'    => $email,
            'user_email'    => $email,
            'user_pass'     => $password,
            'first_name'    => $first_name,
            'last_name'     => $last_name,
            'nickname'      => $first_name,
            'role'         => 'subscriber',
        );
        
        $user = get_user_by( 'email', $email );

        if($user == false){
            if(!empty($first_name) && !empty($middle_name) && !empty($last_name) && !empty($email) && !empty($phone_number) && !empty($address) && !empty($password)){
                if($password == $confirm_password){
                    if(preg_match($pattern, $password)){
                        $user_id = wp_insert_user( $user_data );
                        update_user_meta( $user_id, 'address', $address );
                        update_user_meta( $user_id, 'middle-name', $middle_name );
                        update_user_meta( $user_id, 'phone-number', $phone_number );
                        update_user_meta( $user_id, 'user-status', $user_status );
                        wp_new_user_notification( $user_id, $password );
            
                        $result_register['register_message'] = "Your account is successfully register.";
                        $result_register['register_success'] = "success";
                        $result_register['register_redirect'] = site_url("member-login");
                        $_SESSION[" register_message"] = "Your account is successfully register.";
                        unset($_SESSION[" register_error"]);
                    }else{
                        $result_register['register_error'] = "error";
                        $result_register['register_message'] = "Please enter one capital latter, one alphabet, one number password between 8 to 20 character long password.";
                    }
                }
            }
        }
        else{
            $result_register['register_message'] = "This email id is already register.";
            $_SESSION["register_message"] = "This email id is already register.";
            $result_register['register_error'] = "error";
            $result_register['register_redirect'] = site_url("member-register");
            unset($_SESSION[" register_message"]);
        }
        
        echo json_encode($result_register);
        wp_die();

    }
    /**
     * Ajax function for forgot password page
     */
    function custom_forgot_password(){
        session_start();
        
        $_SESSION["forgot_password_email"] = "";
        unset($_SESSION["register_message"]);
        $user_login =  $_POST["user_login"];
        $user = get_user_by( 'email', $user_login );
        
        $output_forgotpassword = array();
        if($user != false){
            $output_forgotpassword["redirect_url"] = site_url("/member-login?rese_password=true");

            $username = $user->data->user_login;
            $key = get_password_reset_key( $user );

            $reset_link = site_url(). '/member-password-reset/?action=rp&key=' . $key . '&login=' . $username;

            $message = '<h2>' . __('Proceed to reset password : ', 'my_slug') . '</h2><br />' .
            __('Reset your password link : ', 'my_slug') . 
            '<a href="'. esc_url( $reset_link ) . '" title="' . __('Reset your password link : ', 'my_slug') .'" >' . 
            esc_url( $reset_link ) . '</a>';

            wp_mail( $user_login, "Reset Password Link", stripslashes( $message ), "Content-Type: text/html; charset=UTF-8" );
            
            $output_forgotpassword["message"] = "success";
        }else{
            $output_forgotpassword["redirect_url"] = site_url("/member-register");
            $_SESSION["err_email"] = "This email id is not register.";
            $output_forgotpassword["message"] = "error";
        }

        echo json_encode($output_forgotpassword);
        wp_die();

    }
    /**
     * Ajax function for reset password page
     */
    function custom_reset_password(){
        session_start();
        
        $reset_message = array();
        unset($_SESSION["register_message"]);
        unset($_SESSION["err_email"]);
        $forgotemail =  $_POST["forgotemail"];
        $user = get_user_by( 'email', $forgotemail );
        $user_id = $user->ID;
        $pass1 =  $_POST["pass1"];
        $pass2 =  $_POST["pass2"];

        // Regex for password
        $pattern = '/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{8,20}$/';

        $reset_message["success"] = "";
        $reset_message["error"] = "";
        $reset_message["redirect_url"] = "";

        if($pass1 == $pass2){
            if(preg_match($pattern, $pass1)){
                reset_password( $user, $pass1 );

                $_SESSION["reset_pass_message"] = "Your password was reset successfully.";
                $reset_message["success"] = "success";
                $reset_message["redirect_url"] = site_url("/member-login");
            }else{
                $reset_message["error"] = "error";
                $reset_message["error_message"] = "Please enter one capital latter, one alphabet, one number password between 8 to 20 character long password.";
            }
            
        }else{
            $reset_message["error"] = "error";
            $reset_message["redirect_url"] = site_url("/member-password-reset");
        }
        
        echo json_encode($reset_message);
        wp_die();

    }
}
 
/** Initialize the plugin */
$custom_login_pages_plugin = new Custom_Login_Plugin();

// Create the custom pages at plugin activation
register_activation_hook( __FILE__, array( 'Custom_Login_Plugin', 'plugin_activated' ) );