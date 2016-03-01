jQuery(document).ready(function($) {
    
    $.noConflict();
    $('body').fadeIn()
    checkUsage();

    //jQuery('#mod_logout').click(function(){
    $('body').on('click', '#mod_logout', function(){
        logoutSession();
    });

    //jQuery('#mod_submit_login').click(function(){
    $('body').on('click', '#mod_submit_login', function(){
        $('.mod_error_msg').html();
        $('#mod_submit').hide();
        var mod_password = $('#mod_password').val();
        console.log(mod_password);
        checkPassword( mod_password );
    });

    function checkUsage(){
        if( !checkModSession() )
        {
            jQuery('#mod_classDisplay').hide();
            jQuery('.mod_wrapper').fadeIn('fast').find('#mod_loginForm').fadeIn('slow');
        } else
        {
            jQuery('#mod_loginForm').hide();
            jQuery('.mod_wrapper').fadeIn().find('#mod_classDisplay').fadeIn('slow');
        }
    }

});