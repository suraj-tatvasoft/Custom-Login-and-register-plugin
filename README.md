
# Custom login plugin

We have created a custom plugin, this plugin has the functionality of custom login, register, forgot password, and reset password.




## enqueue_block_editor_scripts function

This function is enqueue all css and js file

## plugin_activated function

When this plugin is activated, login, register, forget and reset password pages will be create automatically.

## redirect_login_form function

This function create a custom login form shortcut that will redirect to the login page.

## get_template_html function 

This function incudes all pages.

## authenticate_user function 

This plugin returns whether the user is valid or not when the user goes to login.

## redirect_after_logout function 

This function is redirect login page after logout.

## redirect_register_form function 

This function create a custom register form shortcut that will redirect to the register page.

## extra_user_profile_fields function 

This function is added extra field of user profile page in admin site.

## save_extra_user_profile_fields function

This function is save extra field from admin site in database.

## redirect_password_forgot_form function

This function create a custom forgot password form shortcut that will redirect to the forgot password form page.

## redirect_password_reset_form function

This function create a custom reset password form shortcut that will redirect to the reset password form page.

## custom_login function

This function is call ajax for login validation and login functionality.

## custom_register function

This function is call ajax for register validation and register functionality.

## custom_forgot_password function

This function is call ajax for forgot password validation and forgot password functionality.

## custom_reset_password function

This function is call ajax for reset password validation and reset password functionality.