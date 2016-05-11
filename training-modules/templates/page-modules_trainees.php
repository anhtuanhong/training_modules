<?/*
/*
*/
/*
 *  Single Modules Page. 
 * 
 * 
 */ 
?>

<html>
<head>
<?php wp_head(); ?>
</head>
<body class="modules">
<form id="modAdmin_login" class="mod_loginForm" action="login" method="post">
	<img class="mod_logo" src="<? echo get_option('mod_logo');?>" />
    <h2>Module Administration Login</h2>
    <label for="username">Username</label><br />
    <input id="username" type="text" name="username"><br />
    <label for="password">Password</label><br />
    <input id="password" type="password" name="password"><br />
    <input class="submit_button" type="submit" value="Login" name="submit"><br />
    <p class="status"></p>
    <a class="close" href="">(close)</a>
    <?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
</form>
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

<div class="mod_wrapper">

	<div class="admin_bar" align="right">
<?php if ( is_user_logged_in() ) { ?>
		<a class="login_button button" href="<?php echo wp_logout_url( get_bloginfo('url') . '/' . get_option('trMod_page_name') ); ?>">Logout</a>
	</div><!--admin_bar-->
	<div class="admin_wrapper">
		<div id="mod_moduleDisplay" style="width: 95%; display: none;">
			<div class="switchView">VIEW TRAINEES</div>
			<? if(get_option('mod_logo') != ''){?><img class="mod_logo" src="<? echo get_option('mod_logo');?>" /><? }?>
			<h2>Modules Page</h2>
			<? 	$user_ID = get_current_user_id();?>
			<div id="mod_list" rel="<? echo $user_ID;?>" class="mod_list_view">
			</div><!--mod_list-->
		</div><!--mod_moduleDisplay-->
		<div id="mod_classDisplay" class="show" style="width: 95%;">
			<div class="switchView">VIEW MODULES</div>
			<? if(get_option('mod_logo') != ''){?><img class="mod_logo" src="<? echo get_option('mod_logo');?>" /><? }?>
			<h2>Module Trainees Page</h2>
			<div class="mod_admin_search">
				<h2>Search</h2>
				<div id="modAdmin_search" class="button" style="100px">Search</div>

				<label>First Name:</label><input id='mod_firstName' type="text" name="firstname" required/><label>Last Name:</label><input id='mod_lastName' type="text" name="lastname" required/><br />
				<label>Email</label><input id='mod_email' type="text" name="email" required/>
				<? $location_list = mod_get_location_list();?>
				<label>Location</label><select name="mod_location" id="mod_location"  realname="Location" class="validate[required]" required="required">
		            <option selected="selected" value="all" bs='bs'>All</option>
		            <? //var_dump($locations_data);
		              foreach($location_list as $location){
		                $bs_string = (strpos($location[0], 'bs') !== false) ? 'bs="bs"' : ''; 
		                $bs_sep = (strpos($location[0], 'bs') !== false) ? '' : '-';
		                $location_output = (strpos($location[0], 'bs') !== false) ? "bs" : $location[2].$bs_sep.$location[1]; ?>
		                <option <? echo $bs_string;?> value='<? echo $location_output;?>'><? echo $location[2];?><? echo $bs_sep;?><? echo $location[1];?></option>
		              <? }
		          	?>
		        </select>
			</div><!--mod_admin_search-->
			<div class="mod_list_view">
				<table id="mod_list_table">
				<tr id="0" class="trMod-element-header">
			    	<? /*<th class="userID">ID</th>*/?>
			    	<th class="firstName">First Name</th>
			    	<th class="lastName">Last Name</th>
			    	<th class="email">Email</th>
			    	<th class="location">Location</th>
			    	<th class="lastLogin">Last Login</th>
			    	<th class="numberCompleted">Courses Completed</th>
			    	<th class="lastComplete">Last Course Completed</th>
			    	<th class="dateLastComplete">Date Completed</th>
			    	<th class="expando"></th>
			    </tr>
			    <? //}?>
			    <tr class="trMod-element" id="trMod-element-placeholder" style="display:none;">  
			        <? /* <td id="userID" class="userID"></td>*/?>
			    	<td id="firstName" class="firstName"></td>
			    	<td id="lastName" class="lastName"></td>
			    	<td id="email" class="email"></td>
			    	<td id="location" class="location"></td>
			    	<td id="lastLogin" class="lastLogin"></td>
			    	<td id="numberCompleted" class="numberCompleted"></td>
			    	<td id="lastComplete" class="lastComplete"></td>
			    	<td id="dateLastComplete" class="dateLastComplete"></td>
			        <td class="expando"><div class='button trMod_expando' trainee=''>Expand</div></td> 
			    </tr> 
			    <tr class="trMod-element-expando" id="trMod-element-expando-placeholder" style="display:none;">
			    	<td class="mod_list_courses_holder" colspan='7'>
			    	</td>
			    	<td colspan='2'><div class="button trMod_expando_close">Close</div></td>
			    	<td class="mod_list_course_notify" colspan='4'>
			    		<label>Location</label><select name="mod_location" id="mod_location"  realname="Location" class="validate[required]" required="required">
			    		<option selected="selected" value="" bs='bs'>Please Select</option>
		                <? //var_dump($locations_data);
		              
		                  foreach($location_list as $location){
		                    $bs_string = (strpos($location[0], 'bs') !== false) ? 'bs="bs"' : ''; 
		                    $bs_sep = (strpos($location[0], 'bs') !== false) ? '' : '-';
		                    $location_output = (strpos($location[0], 'bs') !== false) ? "bs" : $location[0]; ?>
		                    <option <? echo $bs_string;?> value='<? echo $location_output;?>'><? echo $location[2];?><? echo $bs_sep;?><? echo $location[1];?></option>
		                <? }?>
		                </select>
			    	</td>
			    </tr>
			    </table>
			    <div id="modAdmin_msg"></div><!--modAdmin_msg-->
			</div><!--mod_list_view-->
		</div><!--mod_classDisplay-->
	</div><!--admin_wrapper-->
<?php } else { ?>
		    <a class="login_button" id="show_login" href="">Login</a>
		</div><!--admin_bar-->
		<?php } ?>

</div><!--mod_wrapper-->

<?php endwhile; // end of the loop.?>

<?php wp_footer();?>
<script type="text/javascript">
//$.noConflict();
jQuery(document).ready(function($) {
	adminDisplayModules( '#mod_list' );
    $('body').fadeIn();

    $('.switchView').click(function(){
    	switchView();
    });

    function checkHash(){
    	console.log(window.location.hash);
    	if(window.location.hash && window.location.hash == '#module'){
    		switchView();
    	}
    }

    checkHash();

    function switchView(){
    	if( $('#mod_classDisplay').hasClass('show') ){
    		$('#mod_classDisplay').hide().removeClass('show');
    		$('#mod_moduleDisplay').fadeIn().addClass('show');
    		window.location.hash = 'module';
    	}else{
    		$('#mod_moduleDisplay').hide().removeClass('show');
    		$('#mod_classDisplay').fadeIn().addClass('show');
    		window.location.hash = '';
    	}
    }
});
</script>
</body>
</html>

