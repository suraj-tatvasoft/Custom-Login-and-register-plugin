$(function(){
	$("#phone-number").keypress(function (e){
		if( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)){
			return false;
		}
	}); 
	
	// Email Validation
	function isEmail(email) {
		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if(!regex.test(email)) {
			return false;
		}else{
			return true;
		}
	}
	// Login form validation and ajax for login functionality
	$(".login-form-container .err").hide();
	$('.login-form-container #login-submit').click(function(e){
		var email = $("#user_login").val();
		var password = $("#user_pass").val();

		var button = $(this),
		    data = {
			'action': 'custom_login',
			'email': email,
			'password': password,
			
		};
 
		$.ajax({ 
			url : custom.ajaxurl,
			data : data,
			type : 'post',
			dataType: 'json',
			success : function( response ){

				console.log(response);
				if( email == ""){
					$(".login-form-container .err.err-email").text("Please enter your email.");
					$(".login-form-container .err.err-email").show();
				}else{
					$(".login-form-container .err.err-email").hide();
				}
				if( password == "" ){
					$(".login-form-container .err.err-password").text("Please enter your Password.");
					$(".login-form-container .err.err-password").show();
				}else{
					$(".login-form-container .err.err-password").hide();
				}

				if(isEmail(email) == false){
					$(".login-form-container .err.err-email").text("Please enter valid email.");
					$(".login-form-container .err.err-email").show();
					return false;
				}else if(email && password){

					if(response['in_valid_email'] != ""){
						console.log(response['in_valid_email']);
						$(".login-form-container .err.err-email").text(response['in_valid_email']);
						$(".login-form-container .err.err-email").show();
						return false;
					}else{
						$(".login-form-container .err.err-email").hide();
							
					}

					window.location.replace(response["redirect_url"]);
					return true;
				}

				
			}
		});

		e.preventDefault();
	});

	// Register form validation and ajax for register functionality
	$("#signupform .err").hide();
	$('#signupform .register-button').click(function(e){

		var first_name = $("#first-name").val();
		var middle_name = $("#middle-name").val();
		var last_name = $("#last-name").val();
		var email = $("#email").val();
		var phone_number = $("#phone-number").val();
		var address = $("#address").val();
		var password = $("#password").val();
		var confirm_password = $("#c-password").val();
		var deactive = $("#deactive").val();

		var button = $(this),
		    data = {
			'action': 'custom_register',
			'email': email,
			'first_name': first_name,
			'middle_name': middle_name,
			'last_name': last_name,
			'phone_number': phone_number,
			'address': address,
			'password': password,
			'confirm_password': confirm_password,
			'deactive': deactive
			
		};
 
		$.ajax({ 
			url : custom.ajaxurl,
			data : data,
			type : 'post',
			dataType: 'json',
			success : function( response ){

				console.log(response);
				if(first_name == "" || middle_name == "" || last_name == "" || phone_number == "" || address == "" || password == "" || confirm_password == "" || email == "" ){
					// e.preventDefault();
					if(first_name == ""){
						$("#signupform .err.err-first").text("Please enter your first name.");
						$("#signupform .err.err-first").show();
					}else{
						$("#signupform .err.err-first").hide();
					}
		
					if(middle_name == ""){
						$("#signupform .err.err-middle").text("Please enter your middle name.");
						$("#signupform .err.err-middle").show();
					}else{
						$("#signupform .err.err-middle").hide();
					}
		
					if(last_name == "" ){
						$("#signupform .err.err-last").text("Please enter your last name.");
						$("#signupform .err.err-last").show();
					}else{
						$("#signupform .err.err-last").hide();
					}
		
					if(phone_number == ""){
						$("#signupform .err.err-phone").text("Please enter your phone number.");
						$("#signupform .err.err-phone").show();
					}else{
						$("#signupform .err.err-phone").hide();
					}
		
					if(address == ""){
						$("#signupform .err.err-address").text("Please enter your address.");
						$("#signupform .err.err-address").show();
					}else{
						$("#signupform .err.err-address").hide();
					}
		
					if(password == ""){
						$("#signupform .err.err-password").text("Please enter your password.");
						$("#signupform .err.err-password").show();
					}else{
						$("#signupform .err.err-password").hide();
					}
		
					if(confirm_password == ""){
						$("#signupform .err.err-c-password").text("Please enter your confirm password.");
						$("#signupform .err.err-c-password").show();
					}else{
						$("#signupform .err.err-c-password").hide();
					}
		
					if(email == ""){
						$("#signupform .err.err-email").text("Please enter your email.");
						$("#signupform .err.err-email").show();
					}else{
						$("#signupform .err.err-email").hide();
					}

					return false;
	
				}
				
				if(email){
					if(isEmail(email) == false){
						// e.preventDefault();
						$("#signupform .err.err-email").text("Please enter valid email.");
						$("#signupform .err.err-email").show();

						return false;

					}else{
						$("#signupform .err.err-email").hide();
					}
				}
		
				if(password != confirm_password){
					// e.preventDefault();
					$("#signupform .err.err-c-password").text("Please enter both password same.");
					$("#signupform .err.err-c-password").show();

					return false;
				}
				else{
					$("#signupform .err.err-c-password").hide();
				}

				if( response["register_success"] == "success"){
					window.location.replace(response["register_redirect"]);
					return true;
				}
				else if( response["register_error"] == "error"){
					$("#signupform .err.err-password").text(response["register_message"]);
					$("#signupform .err.err-password").show();
					return false;
				}else{
					$("#signupform .err.err-password").hide();
					return false;
				}
					
			}
		});
		e.preventDefault();
		
	});

	// Forgot password form validation and ajax for forgot password functionality
	$("#password-lost-form .err").hide();
	$('#password-lost-form .lostpassword-button').click(function(e){

		var user_login = $("#user_login").val();

		var button = $(this),
		    data = {
			'action': 'custom_forgot_password',
			'user_login': user_login,
			
		};
 
		$.ajax({ 
			url : custom.ajaxurl,
			data : data,
			type : 'post',
			dataType: 'json',
			success : function( response ){

				if( user_login == ""){
					$("#password-lost-form .err.err-email").text("Please enter your email.");
					$("#password-lost-form .err.err-email").show();
					return false;
				}else if(isEmail(user_login) == false){
					$("#password-lost-form .err.err-email").text("Please enter valid email.");
					$("#password-lost-form .err.err-email").show();
					return false;
				}
				else{
					$("#password-lost-form .err.err-email").hide();
				}

				if(response["message"] == "success"){
					window.location.replace(response["redirect_url"]);
					return true;
				}else if(response["message"] == "error"){
					window.location.replace(response["redirect_url"]);
					return true;
				}else{
					return false;
				}
				
					
			}
		});
		e.preventDefault();
		
	});

	// Reset password form validation and ajax for reset password functionality
	$("#password-reset-form .err").hide();
	$('#password-reset-form #resetpass-button').click(function(e){

		var forgotemail = $("#forgotemail").val();
		var pass1 = $("#pass1").val();
		var pass2 = $("#pass2").val();

		var button = $(this),
		    data = {
			'action': 'custom_reset_password',
			'forgotemail': forgotemail,
			'pass1': pass1,
			'pass2': pass2,
			
		};
 
		$.ajax({ 
			url : custom.ajaxurl,
			data : data,
			type : 'post',
			dataType: 'json',
			success : function( response ){

				if( pass1 == "" || pass2 == ""){
					if(pass1 == ""){
						$("#password-reset-form .err.err-pass").text("Please enter your password.");
						$("#password-reset-form .err.err-pass").show();
					}else{
						$("#password-reset-form .err.err-pass").hide();
					}

					if(pass2 == ""){
						$("#password-reset-form .err.err-c-pass").text("Please enter your confirm password.");
						$("#password-reset-form .err.err-c-pass").show();
					}else{
						$("#password-reset-form .err.err-c-pass").hide();
					}
					
					return false;
				}
				
				if(pass1 != pass2){
					$("#password-reset-form .err.err-c-pass").text("Please enter both password same.");
					$("#password-reset-form .err.err-c-pass").show();
					return false;
				}
				else{

					if(response["error"] == "error"){
						$("#password-reset-form .err.err-pass").text(response["error_message"]);
						$("#password-reset-form .err.err-pass").show();
					}else{
						$("#password-reset-form .err.err-pass").hide();
					}

					$("#password-reset-form .err.err-c-pass").hide();
					
				}

				if(response["success"] == "success"){
					window.location.replace(response["redirect_url"]);
					return true;
				}else{
					return false;
				}
				
					
			}
		});
		e.preventDefault();
		
	});
})
