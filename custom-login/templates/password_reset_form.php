<?php
    $forgot_password_email = "";
    if(isset($_GET["login"]) && $_GET["login"] != ""){
        $forgot_password_email = $_GET["login"];
    }
    
?>
<div id="password-reset-form" class="widecolumn">
    <?php if ( $attributes['show_title'] ) : ?>
        <h3><?php _e( 'Enter Your New Password', 'custom-login' ); ?></h3>
    <?php endif; ?>
 
    <form name="resetpassform" id="resetpassform" action="" method="post" autocomplete="off">
        <p>
            <input hidden type="email" value="<?php echo $forgot_password_email; ?>" name="forgotemail" id="forgotemail">
        </p>
        <p>
            <label for="pass1"><?php _e( 'New password', 'custom-login' ) ?></label>
            <input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" />
            <span style="color: red;" class="err err-pass"></span>
        </p>
        <p>
            <label for="pass2"><?php _e( 'Repeat new password', 'custom-login' ) ?></label>
            <input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" />
            <span style="color: red;" class="err err-c-pass"></span>
        </p>
         
        <p class="description"><?php echo wp_get_password_hint(); ?></p>
         
        <p class="resetpass-submit">
            <input type="submit" name="submit" id="resetpass-button"
                   class="button" value="<?php _e( 'Reset Password', 'custom-login' ); ?>" />
        </p>
    </form>
</div>