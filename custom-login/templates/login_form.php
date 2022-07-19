<?php
    session_start();
    $register_message = "";
    $reset_message = "";
    if(isset($_SESSION["register_message"]) && $_SESSION["register_message"] != ""){
        $register_message = $_SESSION["register_message"];
    }

    // if(isset($_SESSION["reset_pass_message"]) && $_SESSION["reset_pass_message"] != ""){
    //     $reset_message = $_SESSION["reset_pass_message"];
    // }
    $reset_message = "";
    if(isset($_GET["rese_password"]) && $_GET["rese_password"] != ""){
        $reset_message = "Your reset password link send your email please check your email.";
    }

    $invalid_pass = "";
    if(isset($_GET["invalid_pass"]) && $_GET["invalid_pass"] != ""){
        $invalid_pass = "Please enter correct password.";
    }

?>
<div class="login-form-container">
    <?php
        if($_SESSION['user_status'] == "deactivate"){
            echo "<p>User Status is deactivate so wait for activate your account.</p><br>";
        }

        if(!empty($register_message)){
            echo "<p style='color: red; '> ".$register_message." </p>";
        }
        if(!empty($reset_message)){
            echo "<p style='color: #000000; '> ".$reset_message." </p>";
        }
        if(!empty($invalid_pass)){
            echo "<p style='color: #000000; '> ".$invalid_pass." </p>";
        }
    ?>
    <form method="post" action="">
        <p class="login-username">
            <label for="user_login"><?php _e( 'Email', 'custom-login' ); ?></label>
            <input type="text" name="log" id="user_login">
            <span style="color: red;" class="err err-email"></span>
        </p>
        <p class="login-password">
            <label for="user_pass"><?php _e( 'Password', 'custom-login' ); ?></label>
            <input type="password" name="pwd" id="user_pass">
            <span style="color: red;" class="err err-password"></span>
        </p>
        <p class="login-submit">
            <input type="submit" name="submit" id="login-submit" value="<?php _e( 'Sign In', 'custom-login' ); ?>">
            
        </p>
    </form>
    <br>
    <a href="<?php echo site_url("/member-register")?>">Register User</a> 
    <a href="<?php echo site_url("/member-password-lost")?>">Forgot Password</a>

</div>