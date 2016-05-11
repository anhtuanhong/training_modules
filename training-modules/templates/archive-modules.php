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

<div class="mod_wrapper">
	<div id="mod_loginForm" class="mod_loginForm" style="display:none;">
		<img class="mod_logo" src="<? echo get_option('mod_logo');?>" />
		<h2>Please Log In</h2>
		<div class="mod_error_msg"></div>
		<div class="mod_error_tips"></div>
		<input id='mod_firstName' type="text" name="firstname"  class="field" placeholder="First Name" size="25" required/>
		<input id='mod_lastName' type="text" name="lastname" class="field" placeholder="Last Name" size="25" required/>
		<input id='mod_email' type="text" name="email" class="field" placeholder="Email" size="25" required/>
		<? $location_list = mod_get_location_list();?>
		<select name="mod_location" id="mod_location"  realname="Location" class="validate[required]" required="required">
            <option selected="selected" value="" bs='bs'>Your HCA Office</option>
            <? var_dump($location_list);
              foreach($location_list as $location){
                $bs_string = (strpos($location[0], 'bs') !== false) ? 'bs="bs"' : ''; 
                $bs_sep = (strpos($location[0], 'bs') !== false) ? '' : '-';
                $location_output = (strpos($location[0], 'bs') !== false) ? "bs" : $location[2].$bs_sep.$location[1]; ?>
                <option <? echo $bs_string;?> value='<? echo $location_output;?>'><? echo $location[2];?><? echo $bs_sep;?><? echo $location[1];?></option>
              <? }
          	?>
        </select>
		<input id='mod_password' type="password" name="password" class="field" placeholder="Password" size="25" required/>
		<div id="mod_submit_login" class="button">Login</div>
	</div><!--mod_loginForm-->

<?php //if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

	<div id="mod_classDisplay" style="display:none;">
		<div id="mod_logout">Log Out</div>
		<? if(get_option('mod_logo') != ''){?><img class="mod_logo" src="<? echo get_option('mod_logo');?>" /><? }?>
		<? if(get_option('mod_title') != ''){?><h2><? echo stripslashes( get_option('mod_title') );?></h2><? }?>
		<? if(get_option('mod_desc') != ''){?><p style="width:90%; margin: 15px auto; clear: left;"><? echo stripslashes( get_option('mod_desc') );?></hp><? }?>
		<div class="mod_list_view">
			<? //ADD Module Class Here ?>
			<? /*$modules = get_modules('');
			foreach($modules as $module){
				$permalink = get_post_permalink($module->ID);
				$module_meta = get_post_meta($module->ID, 'module_meta', true);?>
				<div class="mod_entry">
				<a href="<? echo $permalink;?>" alt="<? echo $module->post_title;?>"><?php echo get_the_post_thumbnail( $page->ID, 'thumbnail' ); ?></a>
				<a href="<? echo $permalink;?>" alt="<? echo $module->post_title;?>"><h3><? echo $module->post_title;?></h3></a>
				<? if(!empty($module_meta)){?>
					<div class="mod_meta"><? echo strtoupper($module_meta['type']);?> | <? echo $module_meta['mod_minimum'];?> min.</div>
				<? }?>
				<p><? echo $module->post_content;?></p>
				
				</div><!--mod_entry-->
			
			<? } */?>
		</div><!--mod_list_view-->
		<div style="clear:both; margin-bottom: 10px;"></div>
	</div><!--mod_classDisplay-->	

<?php //endwhile; // end of the loop.?>

</div><!--mod_wrapper-->

<?php wp_footer();?>
</body>
</html>

