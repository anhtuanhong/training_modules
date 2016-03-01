jQuery(document).ready(function($) {
	   // Show the login dialog box on click
    $('body').on('click', 'a#show_login', function(e){
    	console.log('Login');
        $('body').prepend('<div class="login_overlay"></div>');
        $('form#modAdmin_login').fadeIn(500);
        $('div.login_overlay, form#modAdmin_login a.close').on('click', function(){
            $('div.login_overlay').remove();
            $('form#modAdmin_login').hide();
        });
        e.preventDefault();
    });

    // Perform AJAX login on form submit
    $('body').on('submit', 'form#modAdmin_login', function(e){
        $('form#modAdmin_login p.status').show().text(ajax_login_object.loadingmessage);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_login_object.ajaxurl,
            data: { 
                'action': 'ajaxlogin', //calls wp_ajax_nopriv_ajaxlogin
                'username': $('form#modAdmin_login #username').val(), 
                'password': $('form#modAdmin_login #password').val(), 
                'security': $('form#modAdmin_login #security').val() },
            success: function(data){
                $('form#modAdmin_login p.status').text(data.message);
                if (data.loggedin == true){
                    document.location.href = ajax_login_object.redirecturl;
                }
            }
        });
        e.preventDefault();
    });
    
});