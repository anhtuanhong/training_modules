( function( $ ) {

$('body').on('click', '.trMod_expando', function(e){
	$(this).hide();
	var button = $(this);
	var trainee = $(this).attr('trainee');
	closeExpando( trainee );
	var parent = $(this).parent('td').parent('tr').attr('id');
	//alert(trainee);

	var data = {
		'action' : 'queryCourses',
		'nonce' : myAjax.nonce,
		'trainee' : trainee
	}

	$.post(myAjax.url, data, function(response){
		console.log(response);
		//Create Window
		var plus = parent;
		console.log(plus);
		var elementRow = $('#trMod-element-expando-placeholder').clone();
		var elementRowID = "tr#course-"+(trainee);
		var insertRowID = "tr#"+(plus);
		console.log(elementRowID);
		console.log(insertRowID);

		$(elementRow).attr( 'id', 'course-'+trainee );
		
		$(insertRowID).after(elementRow);

		//Add Course Info
		var output = '';
		if( response !== 'No Courses Found'){
			output = "<strong>Completed Courses</strong>"
			output += '<table class="trMod_list_courses">';
			output += '<tr><th>Course Name</th><th>Completed Date</th><th>Location Notified</th></tr>';
			$.each(response, function(i, item){
				output += '<tr>';
				output += '<td class="courseName">' + item.courseName + '</td>';
				output += '<td class="courseComplete">' + item.courseComplete + '</td>';
				if(item.courseLocation !== null ){
					output += '<td class="courseLocation">' + item.courseLocation + '</td>';
				}else{
					output += '<td class="courseLocation"></td>';
				}
				output += '</tr>';
			});
			output += '</table>'

			$(elementRowID).find('td.mod_list_courses_holder').html(output);
			
		}else{
			$(elementRowID).find('td.mod_list_courses_holder').html(response);
		}
		//$(elementRow).attr( 'trainee', trainee );
		$(elementRowID).find('.trMod_expando_close').attr('trainee', trainee);

		$(elementRowID).fadeIn();
		button.fadeIn();
	});
});

$('body').on('click', '.trMod_expando_close', function(e){
	closeExpando( $(this).attr('trainee') );
});

function closeExpando(trainee){
	$('tr#course-' + trainee).hide().remove();
}

$('body').on('click', '#modAdmin_search', function(e){
	$('#modAdmin_search').hide();
	if(clearTable())
	{
		var firstName = $('#mod_firstName').val();
		var lastName = $('#mod_lastName').val();
		var email = $('#mod_email').val();
		var location = $('#mod_location').val();


		console.log( firstName + '-' + lastName + '-' + email);
		console.log(myAjax.url);
		var data = {
			'action' : 'queryUser',
	    	//'dataType' : 'json',
			'nonce' : myAjax.nonce,
			'firstName' : firstName,
			'lastName' : lastName,
			'email' : email,
			'location' : location
		};

		$.post(myAjax.url, data, function(response)
		{

			if( response != 'No user found')
			{	
				console.log(response);
				//var obj = JSON.parse(response);
				//console.log(obj);
				$.each(response, function(i, item){
					console.log(item);
					var plus = i+1;
					var elementRow = $('#trMod-element-placeholder').clone();
					var elementRowID = "tr#"+(plus);
					var insertRowID = "tr#"+(i);
					console.log(elementRowID);
					console.log(insertRowID);

					$(elementRow).attr('id', plus);
					$(insertRowID).after(elementRow);

					//Add Info
					//$(elementRowID).find('.userID').html(item.id);
					$(elementRowID).find('.trMod_expando').attr('trainee', item.id);
					$(elementRowID).find('.firstName').html(item.firstName);
					$(elementRowID).find('.lastName').html(item.lastName);
					$(elementRowID).find('.email').html(item.email);
					$(elementRowID).find('.location').html(item.user_location);
					$(elementRowID).find('.lastLogin').html(item.sessionDate);
					$(elementRowID).find('.numberCompleted').html(item.coursesCompleted);
					$(elementRowID).find('.lastComplete').html(item.lastCourse);
					$(elementRowID).find('.dateLastComplete').html(item.lastCourseDate);

					$(elementRowID).show();

					//Add Event Listener
					//$(elementRowID).find('.update').on('click', updateRow);
				
					//originalRow.find('.rp_btn').css('background', '#ddd');
				});

			}else
			{
				$('#modAdmin_msg').html('No User Found');
			}

			$('#modAdmin_search').fadeIn();
		});	
	}
});


function clearTable(){
	$.each($('.trMod-element-expando'), function(){
		if( $(this).attr('id') != 'trMod-element-expando-placeholder' ){
			$(this).remove();
		}
	});
	$.each($('.trMod-element'), function(){
		if( $(this).attr('id') != 'trMod-element-placeholder'){
			$(this).remove();
		}
	});

	$('#modAdmin_msg').html('');
	return true;
}

} )( jQuery );

function isJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

function updateTips( t ) {
      jQuery('.mod_error_tips')
        .text( t )
        .addClass( "ui-state-highlight" );
      setTimeout(function() {
        jQuery('.mod_error_tips').removeClass( "ui-state-highlight", 1500 );
      }, 500 );
    }

function checkLength( o, n, min, max ) {
    if ( o.attr('value').length > max || o.attr('value').length < min ) {
      o.addClass( "ui-state-error" );
      updateTips( "Length of " + n + " must be between " +
        min + " and " + max + "." );
      return false;
    } else {
      return true;
    }
  }

function checkRegexp( o, regexp, n ) {
	if ( !( regexp.test( o.val() ) ) ) 
	{
	  o.addClass( "ui-state-error" );
	  updateTips( n );
	  return false;
	} else {
	  return true;
	}
}


function checkModSession()
{
	if(localStorage && localStorage.getItem('modSession_startTime') )
	{
			//var raw_mod = ; 

			var modSession_startTime = localStorage.getItem( 'modSession_startTime' );
			if(modSession_startTime === 'undefined') return false;
			var currentTime = new Date();
			var currentTimeMilli = currentTime.getTime();
			var cutoffTime = currentTime - 21600000; //Six hours
			console.log(modSession_startTime);
			console.log(cutoffTime);
			if( parseInt( modSession_startTime ) > cutoffTime)
			{
				var modSession_ID = localStorage.getItem( 'modSession_ID');
				updateSession(modSession_ID);
				return true;
			} else 
			{
				//localStorage.removeItem('modSession');
				return false;
			}

	} else
	{
		//alert('Please Update Your Browser');
		return false;
	}
}

function logoutSession()
{

	if( localStorage && localStorage.getItem('modSession_startTime') ){
		var modSession_ID = localStorage.getItem( 'modSession_ID');

		var data = {
			'action' : 'mod_logoutSession',
			'nonce' : myAjax.nonce,
			'mod_id' : modSession_ID
		};

		jQuery.post(myAjax.url, data, function(response)
		{
			console.log(modSession_ID);
			jQuery('#mod_classDisplay').hide();
			jQuery('.mod_wrapper').fadeIn('fast').find('#mod_loginForm').fadeIn('slow');
			localStorage.setItem( 'modSession_startTime' , '0');
		});	
	}
	return false;
}

function updateSession(id)
{
	var data = {
			'action' : 'mod_updateSession',
			'nonce' : myAjax.nonce,
			'mod_id' : id
		};

		jQuery.post(myAjax.url, data, function(response)
		{
			console.log(response);
		});
}

function checkPassword(mod_password)
{
	
	var mod_firstName = jQuery('#mod_firstName').val();
	var mod_lastName = jQuery('#mod_lastName').val();
	var mod_email = jQuery('#mod_email').val();
	var mod_location = jQuery('#mod_location').val();
	var bValid = true;
	//console.log(mod_location);

    //allFields.removeClass( "ui-state-error" );

    bValid = bValid && checkLength( jQuery( '#mod_firstName' ), "first name", 2, 16 );
    bValid = bValid && checkLength( jQuery( '#mod_lastName' ), "last name", 2, 16 );
    if(jQuery('#mod_email').val() != '' ){
    	 bValid = bValid && checkLength( jQuery( '#mod_email' ) , "email", 6, 80 );
    	 bValid = bValid && checkRegexp( jQuery( '#mod_email' ) , /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "eg. ui@jquery.com" );
    }
    bValid = bValid && checkLength( jQuery('#mod_location') , "location", 3, 100);

    //bValid = bValid && checkLocation(email);
	
	if ( bValid ) {
		var data = {
			'action' : 'mod_checkPassword',
			'nonce' : myAjax.nonce,
			'firstName' : mod_firstName.toLowerCase(),
			'lastName' : mod_lastName.toLowerCase(),
			'email' : mod_email,
			'location' : mod_location,
			'mod_password': mod_password
		};
		jQuery.post(myAjax.url, data, function(response)
		{
			console.log(response);
			if( parseInt(response) )
			{
				if(localStorage){
					//var modSession = {};
					var startTime = new Date();
					var startTimeMilli = startTime.getTime();
					console.log(startTimeMilli);
					localStorage.setItem( 'modSession_startTime' , startTimeMilli.toString() );
					localStorage.setItem( 'modSession_ID' , response);
					localStorage.setItem( 'modSession_firstName' , mod_firstName );
					localStorage.setItem( 'modSession_lastName' , mod_lastName );
					localStorage.setItem( 'modSession_location' , mod_location);
					localStorage.setItem( 'modSession_email' , mod_email );
				}
				//load
				jQuery('#mod_loginForm').hide();
				displayModules( '.mod_list_view' );
				jQuery('#mod_classDisplay').fadeIn('slow');
				jQuery('#mod_password').val('');
				jQuery('#mod_submit').fadeIn();
					//setModSession( mod_firstName, mod_lastName, mod_email );
			}else
			{
				jQuery('#mod_password').val('');
				jQuery('#mod_submit').fadeIn();
				jQuery('.mod_error_msg').html(response);
				
			}
		});
	}else
	{
		jQuery('#mod_password').val('');
		jQuery('#mod_submit').fadeIn();
		jQuery('.mod_error_msg').html('Invalid Entries. Please try again.');
	}
}

function displayModules( element ){
    var location = localStorage.getItem( 'modSession_location');
    console.log( location );
    console.log( element );
    var data = {
        'action' : 'mod_getModules',
        'nonce' : myAjax.nonce,
        'admin' : '',
        'location' : location
    }

    var results = queryModules( element, data );

    console.log(results);
}

function adminDisplayModules( element ){
	var mod_id =jQuery(element).attr('rel');
	console.log(mod_id);
	var data = {
        'action' : 'mod_getModules',
        'nonce' : myAjax.nonce,
        'admin' : mod_id,
        'location' : ''
    }

    var results = queryModules( element, data);
}

function queryModules( element, data){
	jQuery.post(myAjax.url, data, function(response)
    {
    	console.log(response);
        if( response != 'failed')
        {
            
            var jsonResponse = jQuery.parseJSON(response);
            console.log(jsonResponse);
            jQuery.each(jsonResponse, function( i , item){
                var html = '';
                
                html += '<div class="mod_entry">';
                html += '<a href="' + item.permalink + '" alt="' + item.post_title + '">' + item.mod_image + '</a>';
                html += '<a href="' + item.permalink + '" alt="' + item.post_title + '"><h3>' + item.post_title + '</h3></a>';
                var module_meta = item.module_meta;
                console.log(module_meta);
                html += '<div class="mod_meta">' + module_meta.type.toUpperCase() + ' | ' + module_meta.mod_minimum + ' min.</div>';
                html += '<p>' + item.post_content + '</p><div style="clear:both; width: 100%;"></div></div><!--mod_entry-->';
            
                jQuery(element).append(html).fadeIn();
            });

            return true;
        }else
        {   
            jQuery(element).html('No Modules to Show.').fadeIn();
            console.log('mod_getModules Failed.');
            return false;
        }
    });
}

function startCourse(course_id)
{
	if(localStorage && localStorage.getItem('modSession_ID') )
	{
		var user_id = localStorage.getItem('modSession_ID');
		console.log(user_id);
		var data = {
			'action' : 'mod_checkCourse',
			'nonce' : myAjax.nonce,
			'user_id' : user_id,
			'course_id' : course_id
		};

		jQuery.post(myAjax.url, data, function(response)
		{
			console.log(response);

			if( parseInt(response) )
			{
				//if(localStorage){
					//var modSession = {};
				var courseStartTime = new Date();
				var courseStartTimeMilli = courseStartTime.getTime();
				console.log(courseStartTimeMilli);
				localStorage.setItem( 'modSession_courseStartTime' , courseStartTimeMilli.toString() );
				localStorage.setItem( 'modSession_courseID' , course_id);
				localStorage.setItem( 'modSession_courseSessionID' , response);
	
				//load
				Reveal.next();
				jQuery('.controls').fadeIn();
			}else
			{
				//Update Error Box under Start Button
				jQuery('#mod_start_error').html('An Error Occured. Please Try Again').fadeIn();
				jQuery('#mod_classStart').fadeIn();
				console.log(response);
			}
		});
	}else
	{
		logoutSession();
	}
}

function checkCourseDuration(courseDuration)
{
	if(localStorage && localStorage.getItem('modSession_courseSessionID') )
	{
		var courseSessionID = parseInt( localStorage.getItem( 'modSession_courseSessionID' ) );
		var sessionStartTime = parseInt( localStorage.getItem( 'modSession_startTime' ) );
		var courseSessionStartTime = parseInt( localStorage.getItem( 'modSession_courseStartTime' ) );
		var courseDurationMilli = parseInt( courseDuration ) * 60000;
		var courseMinTimeDone = courseDurationMilli + courseSessionStartTime;
		var currentTime = new Date();
		var currentTimeMilli = currentTime.getTime();
		console.log( 'courseMinTimeDone = ' + courseMinTimeDone);
		console.log( 'currentTimeMilli = ' + currentTimeMilli );

		//if( sessionStartTime < courseSessionStartTime && courseSessionStartTime < currentTimeMilli  && courseMinTimeDone < currentTimeMilli )
		

		if( courseMinTimeDone < currentTimeMilli )
		{
			//Display Continue
			jQuery('#mod_outro_error_msg').hide();
			jQuery('#mod_classEndForm').fadeIn();

		}else if( courseMinTimeDone > currentTimeMilli )
		{
			//Required Time is not satisfied.
			jQuery('#mod_outro_error_msg').html('You have not completed the required time for this course. Please go back and finish your training.').fadeIn();
		}
	}
}

function endCourse(url)
{
	if(localStorage && localStorage.getItem('modSession_courseSessionID') )
	{

		//CHECK DURATION OF TIME BEFORE MOVING ON

		var courseSessionID = localStorage.getItem('modSession_courseSessionID');
		console.log(courseSessionID);

		var mod_location = jQuery('#mod_location').val();
		console.log(mod_location);

		var data = {
			'action' : 'mod_completeCourse',
			'nonce' : myAjax.nonce,
			'id' : courseSessionID,
			'mod_location' : ''
		};

		jQuery.post(myAjax.url, data, function(response)
		{
			if( parseInt(response) )
			{	
				//var courseEndTime = new Date();
				//var courseEndTimeMilli = startTime.getTime();
				//console.log(courseStartTimeMilli);
				//localStorage.setItem( 'modSession_courseStartTime' , courseStartTimeMilli.toString() );
				localStorage.removeItem( 'modSession_courseID' );
				localStorage.removeItem( 'modSession_courseSessionID' );
				localStorage.removeItem( 'modSession_courseStartTime' );

				window.location.href = url;
				//jQuery ACTION
			}else
			{
				jQuery('#mod_outro_error').html('An Error Occured. Please Try Again').fadeIn();
				jQuery('#mod_classEnd').fadeIn();
				console.log(response);//Update Error Box Under Close Button
			}
		});	
	}
}



