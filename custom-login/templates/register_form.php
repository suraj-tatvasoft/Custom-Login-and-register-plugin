<?php
    session_start();
    $register_message = "";
    $err_email = "";
    if(isset($_SESSION["register_message"]) && $_SESSION["register_message"] != ""){
        $register_message = $_SESSION["register_message"];
    }

    if(isset($_SESSION["err_email"]) && $_SESSION["err_email"] != ""){
        $err_email = $_SESSION["err_email"];
    }

?>
<div id="register-form" class="widecolumn">
    <?php if ( $attributes['show_title'] ) : ?>
        <h3><?php _e( 'Register', 'custom-login' ); ?></h3>
    <?php endif; 
        if(!empty($register_message)){
            echo "<p style='color: red; '> ".$register_message." </p>";
        }

        if(!empty($err_email)){
            echo "<p style='color: red; '> ".$err_email." </p>";
        }
    ?>
        
    <form id="signupform" action="" method="post">
 
        <p class="form-row">
            <label for="first_name"><?php _e( 'First name', 'custom-login' ); ?><strong>*</strong></label>
            <input type="text" name="user_first_name" id="first-name">
            <span style="color: red;" class="err err-first"></span>
        </p>
        <p class="form-row">
            <label for="middle_name"><?php _e( 'Middle name', 'custom-login' ); ?><strong>*</strong></label>
            <input type="text" name="user_middle_name" id="middle-name">
            <span style="color: red;" class="err err-middle"></span>
        </p>
        <p class="form-row">
            <label for="last_name"><?php _e( 'Last name', 'custom-login' ); ?><strong>*</strong></label>
            <input type="text" name="user_last_name" id="last-name">
            <span style="color: red;" class="err err-last"></span>
        </p>

        <p class="form-row">
            <label for="email"><?php _e( 'Email', 'custom-login' ); ?> <strong>*</strong></label>
            <input type="text" name="user_email" id="email">
            <span style="color: red;" class="err err-email"></span>
        </p>

        <p class="form-row">
            <label for="phone-number"><?php _e( 'Phone Number', 'custom-login' ); ?> <strong>*</strong></label>
            <input type="text" name="user_phone_number" id="phone-number" maxlength="10">
            <span style="color: red;" class="err err-phone"></span>
        </p>

        <p class="form-row">
            <label for="address"><?php _e( 'Address', 'custom-login' ); ?> <strong>*</strong></label>
            <textarea  type="text" name="user_address" id="address" cols="30" rows="5"></textarea>
            <span style="color: red;" class="err err-address"></span>
        </p>

        <p class="form-row">
            <label for="password"><?php _e( 'Password', 'custom-login' ); ?> <strong>*</strong></label>
            <input type="password" name="user_password" id="password">
            <span style="color: red;" class="err err-password"></span>
        </p>

        <p class="form-row">
            <label for="c-password"><?php _e( 'Confirm Password', 'custom-login' ); ?> <strong>*</strong></label>
            <input type="password" name="user_c_password" id="c-password">
            <span style="color: red;" class="err err-c-password"></span>
        </p>

        <p class="form-row">
            <input type="radio"  hidden name="user-status" value="active"> 
            <input type="radio" id="deactive" hidden name="user-status" checked value="deactive"> 

        </p>
        
        <p class="signup-submit">
            <input type="submit" name="submit" class="register-button"
                   value="<?php _e( 'Register', 'custom-login' ); ?>"/>
        </p>
    </form>
</div>