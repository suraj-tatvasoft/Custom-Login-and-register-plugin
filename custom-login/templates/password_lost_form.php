<?php 
    if ( count( $attributes['errors'] ) > 0 ) : 
        foreach ( $attributes['errors'] as $error ) : ?>
            <p>
                <?php echo $error; ?>
            </p>
<?php 
        endforeach; 
    endif; 
?>
<div id="password-lost-form" class="widecolumn">
    <?php if ( $attributes['show_title'] ) : ?>
        <h3><?php _e( 'Forgot Your Password?', 'custom-login' ); ?></h3>
    <?php endif; ?>

    <p>
        <?php
            _e(
                "Enter your email address and we'll send you a link you can use to pick a new password.",
                'personalize_login'
            );
        ?>
    </p>
 
    <form id="lostpasswordform" action="" method="post">
        <p class="form-row">
            <label for="user_login"><?php _e( 'Email', 'custom-login' ); ?>
            <input type="text" name="user_login" id="user_login">
            <span style="color: red;" class="err err-email"></span>
        </p>
 
        <p class="lostpassword-submit">
            <input type="submit" name="submit" class="lostpassword-button"
                   value="<?php _e( 'Reset Password', 'custom-login' ); ?>"/>
        </p>
    </form>
</div>