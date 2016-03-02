<?php
/*
Plugin Name: Training Modules
Plugin URI: http://www.anhtuanhong.me
Description: Plugin that takes jpgs from Powerpoint and creates online slideshow w/ audio capacity.
Version: 1.0.0
Author: Anhtuan Hong
Author URI: http://www.anhtuanhong.me
License: GPL
Copyright: Anhtuan Hong
*/

define( 'trMod_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
//define( 'trMod_INCLUDES_URL', get_home_path() . '/wp-includes/' );
	
date_default_timezone_set('America/New_York');

include_once(ABSPATH . 'wp-includes/pluggable.php');

global $trMod_db_version;
$trMod_db_version = '1.0';

//require_once( trMod_INCLUDES_URL . 'pluggable.php');

/****************** Training Module Settings Page (Start) *******/
register_activation_hook( __FILE__, 'trMod_run_when_plugin_activated' );
//register_activation_hook( __FILE__, 'trMod_install_data' );
register_activation_hook(__FILE__, 'trMod_create_instructor_page' );


register_deactivation_hook( __FILE__, 'trMod_remove_instructor_page' );

//add_action( 'init', 'create_loc_post_types');
//add_action('admin_menu', 'register_locations_submenu_page');

add_action('init', 'trMod_admin_init_post_type');
add_action('init', 'trMod_init_styles');
add_action('admin_menu', 'trMod_admin_generate_settings_page');


//Create Admin Page for Querying Students
function trMod_create_instructor_page()
{
    global $wpdb;

    $the_page_title = 'Module Trainees';
    $the_page_name = 'mod-admin';

    // the menu entry...
    delete_option("trMod_page_title");
    add_option("trMod_page_title", $the_page_title, '', 'yes');
    // the slug...
    delete_option("trMod_page_name");
    add_option("trMod_page_name", $the_page_name, '', 'yes');
    // the id...
    delete_option("trMod_page_id");
    add_option("trMod_page_id", '0', '', 'yes');

    $the_page = get_page_by_title( $the_page_title );

    if ( ! $the_page ) {

        // Create post object
        $_p = array();
        $_p['post_title'] = $the_page_title;
        $_p['post_name'] = $the_page_name;
        $_p['post_content'] = "This text may be overridden by the plugin. You shouldn't edit it.";
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); // the default 'Uncatrgorised'

        // Insert the post into the database
        $the_page_id = wp_insert_post( $_p );

    }
    else {
        // the plugin may have been previously active and the page may just be trashed...

        $the_page_id = $the_page->ID;

        //make sure the page is not trashed...
        $the_page->post_status = 'publish';
        $the_page_id = wp_update_post( $the_page );

    }

    delete_option( 'trMod_page_id' );
    add_option( 'trMod_page_id', $the_page_id );

    

}

function trMod_remove_instructor_page() 
{

    global $wpdb;

    $the_page_title = get_option( "trMod_page_title" );
    $the_page_name = get_option( "trMod_page_name" );

    //  the id of our page...
    $the_page_id = get_option( 'trMod_page_id' );
    if( $the_page_id ) {

        wp_delete_post( $the_page_id ); // this will trash, not delete

    }

    delete_option("trMod_page_title");
    delete_option("trMod_page_name");
    delete_option("trMod_page_id");

}

function trMod_run_when_plugin_activated()
{

	global $wpdb;
	global $trMod_db_version;

	$table_name = $wpdb->prefix . 'trMod_Users';
	$table_session = $wpdb->prefix . 'trMod_Sessions';
	$table_courses = $wpdb->prefix . 'trMod_Courses';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		firstName varchar(255),
		lastName varchar(255),
		email varchar(255),
		location varchar(255),
		createdDate DATETIME DEFAULT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	$sql_session = "CREATE TABLE $table_session (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		user_id mediumint(9),
		sessionDate DATETIME DEFAULT NULL,
		sessionType varchar(255),
		UNIQUE KEY id (id)
	) $chareset_collate;";

	$sql_courses = "CREATE TABLE $table_courses (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		user_id mediumint(9),
		course_id mediumint(9),
		courseStart DATETIME DEFAULT NULL,
		courseEnd DATETIME DEFAULT NULL,
		notifyLocation varchar(255),
		UNIQUE KEY id (id)
	) $chareset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	dbDelta( $sql_session);
	dbDelta( $sql_courses);

	add_option( 'trMod_db_version', $trMod_db_version );

}


function trMod_admin_init_post_type() {
	register_post_type( 'modules',
		array(
			'labels' => array(
				'name' => _x( 'Modules', 'modules'),
				'singular_name' => _x( 'Modules', 'modules'),
				'add_new' => _x( 'Add New', 'modules' ),
        		'add_new_item' => _x( 'Add New Modules', 'modules' ),
        		'edit_item' => _x( 'Edit Modules', 'modules' ),
        		'new_item' => _x( 'New Modules', 'modules' ),
       			'view_item' => _x( 'View Modules', 'modules' ),
        		'search_items' => _x( 'Search Modules', 'modules' ),
        		'not_found' => _x( 'No Module found', 'modules' ),
        		'not_found_in_trash' => _x( 'No Module found in Trash', 'modules' ),
        		'parent_item_colon' => _x( 'Parent Module:', 'modules' ),
        		'menu_name' => _x( 'Modules', 'Modules' ),
			),
			'public' => true,
			'hierarchical' => true,
			'capability_type'=>'post',
			'show_ui' => true,
			'show_in_menu' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'has_archive' => true,
			//'rewrite' => array( 'slug' => 'modules', 'with_front' => false),
			
			'supports' => array( 'title', 'editor','excerpt', 'custom-fields', 'page-attributes', 'thumbnail'),
			//'taxonomies' => array('category'),
		));
}/** end create_loc_post_types **/

//Add Settings Page
function trMod_admin_generate_settings_page()  //Create Settings page
{
	add_submenu_page( 'edit.php?post_type=modules', 'Modules Settings', 'Modules Settings', 'edit_posts', 'modules-settings', 'trMod_global_setup' );
	//add_action('admin_init', 'hca_locations_store');
	//add_menu_page('Module Settings', 'Settings', 'manage_options', 'module-settings');
	$trMod_global_setup = add_submenu_page('trMod_setup', 'Modules', 'Global Settings', 'manage_options', 'module-settings', 'trMod_global_setup');
	add_action('admin_print_styles-' .$trMod_global_setup, 'trMod_admin_output_admin_css');
}

function trMod_global_setup()
{
	include_once('admin/training-module-settings.php');
}

//ADD Meta Box for Single_Module Posts
add_action( 'add_meta_boxes', 'add_module_metaboxes');
add_action('save_post', 'wpt_save_module_meta', 1, 2); // save the custom fields
	
function add_module_metaboxes(){
	add_meta_box('hca_module_meta', 'Module Information', 'hca_module_meta', 'modules', 'normal','high');
}

function hca_module_meta(){
	global $post;

	echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';

	$module_meta = get_post_meta($post->ID, 'module_meta', true); ?>
	<style>
		.table { background-color: #EEE; padding: 5px; border: 1px solid #CCC;}
		.sortable { background-color: #EEE; padding: 10px 5px 5px 20px; border: 1px solid #CCC;}
		.sortable li { vertical-align: top;}

		.sortable li label { width: 100px; font-weight:bold; display: inline-block}
		.sortable li input { width: 70%;}
		.sortable li.number label { width: 300px;}
		.sortable li.number input { width: 100px;}
		.expander {cursor:pointer; border-radius:5px; border:1px solid #666; background-color:#ccc; width:95px; padding:2px 8px 2px 8px;}
		.breakdown div {display:inline-block; width:50px; border-right:1px solid #ccc; padding:5px 10px 5px 10px;}
		.breakdown div:last-child { border-right:none;}
		/*h2 { border-top:1px solid #ccc;}*/
	</style>
	<form method="POST" action="">
		<div id='test_cat'>
			<ul class='sortable'>
				<li><label>Module Type: </label><select id="mod_type" name='module_meta[type]'>
				<? $mod_types = array('video' => 'Video', 'slideshow' => 'Slideshow');
					foreach($mod_types as $key => $value){
						if($key == $module_meta[type])
						{
							$selector = 'selected';
						}else{
							$selector = '';
						}?>
						<option value="<? echo $key;?>" <? echo $selector;?>><? echo $value;?></option>
					<? }?>
				</select>
				</li>
				<? if(isset( $module_meta[type] ) && $module_meta[type] == 'slideshow')
					{
						$display_vid = 'style="display: none;"';
					}else{
						$display_vid = '';
					}?>
				<li id="mod_video_option" <? echo $display_vid;?>><label>Video Url:</label><input type='text' name='module_meta[video_link]' value='<? if(isset($module_meta[video_link])){ echo $module_meta[video_link];}?>' /></li>	
				<li class="number"><label>Minimum Time Requirement in Minutes:<br />(Empty for no time requirement)</label><input type='number' name='module_meta[mod_minimum]' value='<? if(isset($module_meta[mod_minimum])){ echo $module_meta[mod_minimum];}?>' /></li>	
			</ul>
		</div>
	</form>
	<script type='text/javascript'>
		(function($) 
		{
			$(document).ready(function(){
				$('#mod_type').on('change', function(){
					var type = $(this).val();
					if(type == 'video')
					{
						$('#mod_video_option').fadeIn();
					}else{
						$('#mod_video_option').hide();
					}
				});
			});
		})( jQuery );
	</script>
	<?	
}

// Save the Metabox Data
function wpt_save_module_meta($post_id, $post) {
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !wp_verify_nonce( $_POST['eventmeta_noncename'], plugin_basename(__FILE__) )) {
    	return $post->ID;
    }

    // Is the user allowed to edit the post or page?
    if ( !current_user_can( 'edit_post', $post->ID ))
        return $post->ID;

    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.

    $module_meta = $_POST['module_meta'];

    //$pr_meta['state'] = str_replace(' ', '', $pr_meta['state']);
 
    if(get_post_meta($post->ID, 'module_meta', false)){
    	update_post_meta($post->ID, 'module_meta', $module_meta);
    }else{
    	add_post_meta($post->ID, 'module_meta', $module_meta);
    }

}

function orderGalleryByTitle( $gallery){
	$return = array();
	foreach($gallery as $image){
		$return[$image->post_title] = $image->ID;
	}	
	ksort($return);

	return $return;
}

function get_modules($cat){
	$args = array(
		'posts_per_page'   => -1,
		'offset'           => 0,
		'category'         => '',
		'category_name'    => $cat,
		'orderby'          => '',
		'order'            => 'ASC',
		'include'          => '',
		'exclude'          => '',
		'meta_key'         => '',
		'meta_value'       => '',
		'post_type'        => 'modules',
		'post_mime_type'   => '',
		'post_parent'      => '',
		'author'	   => '',
		'post_status'      => 'publish',
		'suppress_filters' => true 
	);
	$posts_array = get_posts( $args );

	return $posts_array;
}

//Enque Scripts to the front end
function trMod_scripts_method() 
{

    wp_enqueue_script('jquery');
    wp_enqueue_script('plugin_functions', trMod_PLUGIN_URL .'js/mod_functions.js', array('jquery' , 'reveal'), null, true);
    wp_enqueue_script('reveal', trMod_PLUGIN_URL .'js/reveal.js', array('jquery'), null, true);
    

    if( is_post_type_archive( 'modules' ) ){
		wp_enqueue_script('mod_archive', trMod_PLUGIN_URL .'js/mod_archive.js', array('jquery'), null, true);
	}
    //wp_enqueue_script('plugin_functions', trMod_PLUGIN_URL .'js/functions.js', array('reveal'), null, true);
    wp_localize_script( 
        'plugin_functions', 
        'myAjax', 
        array(
            'url'   => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( "mod_passwordNonce" ),
        )
    );
}
add_action( 'wp_enqueue_scripts', 'trMod_scripts_method', 20 );

function trMod_init_styles() 
{
	//wp_register_style( 'trMod_admin_style', trMod_PLUGIN_URL . 'css/training-modules-admin.css' );
	wp_register_style( 'trMod_style' , trMod_PLUGIN_URL . 'css/training-modules-style.css' );
	//wp_register_style( 'reveal_style', trMod_PLUGIN_URL . 'css/reveal.css' );
	//wp_enqueue_style( 'trMod_admin_style' );
}	

function ajax_login_init(){

    wp_register_script('ajax-login-script', trMod_PLUGIN_URL . 'js/ajax-login-script.js', array('jquery') ); 
    wp_enqueue_script('ajax-login-script');

    wp_localize_script( 'ajax-login-script', 'ajax_login_object', array( 
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'redirecturl' => get_bloginfo('url') . '/' . get_option('trMod_page_name') ,
        'loadingmessage' => __('Sending user info, please wait...')
    ));

}

//Execute the action only if the user isn't logged in
if (!is_user_logged_in()) {
   add_action('init', 'ajax_login_init');
}


add_filter( 'single_template', 'get_custom_post_type_template' );
add_filter( 'template_include', 'modules_templates');
add_filter( 'template_include', 'modules_trainees_templates');
add_filter( 'show_admin_bar', 'hide_admin_bar' );

//Load Template in Plugin Folder
function get_custom_post_type_template($single_template) {
     global $post;

     if ($post->post_type == 'modules') {
     	wp_enqueue_style( 'trMod_style' );
     	wp_enqueue_style(  'child-style', trMod_PLUGIN_URL . 'css/reveal.css', false);
          //$single_template = dirname( __FILE__ ) . '/templates/single-modules.php';
     	$theme_files = array( 'single-modules.php', 'training-modules/single-modules.php');
		$exists_in_theme = locate_template($theme_files, false);
		if( $exists_in_theme ){
			return $exists_in_theme;
		}else{
			return plugin_dir_path(__FILE__) . '/templates/single-modules.php';
		}
     }
     //echo $single_template;
     return $single_template;
}

function modules_templates( $template ){
	if( is_post_type_archive( 'modules' ) ){
		wp_enqueue_style( 'trMod_style' );
		$theme_files = array( 'archive-modules.php', 'training-modules/archive-modules.php');
		$exists_in_theme = locate_template($theme_files, false);
		if( $exists_in_theme ){
			return $exists_in_theme;
		}else{
			return plugin_dir_path(__FILE__) . '/templates/archive-modules.php';
		}
	}

	return $template;
}


function modules_trainees_templates( $template ){
	if( is_page( 'mod-admin' ) ){
			wp_enqueue_style( 'trMod_style' );
		$theme_files = array( 'page-modules_trainees.php', 'training-modules/page-modules_trainees.php');
		$exists_in_theme = locate_template($theme_files, false);
		if( $exists_in_theme ){
			return $exists_in_theme;
		}else{
			return plugin_dir_path(__FILE__) . '/templates/page-modules_trainees.php';
		}
	}
	return $template;
}

function hide_admin_bar() {
	if ( is_page_template( 'page-modules_trainees.php' ) ) {
		return false;
	}
}

//API FUNCTIONS

// Same handler function...
add_action( 'wp_ajax_nopriv_mod_checkPassword', 'modCheckPassword' );
add_action( 'wp_ajax_nopriv_mod_updateSession', 'modUpdateSession');
add_action( 'wp_ajax_nopriv_mod_logoutSession', 'modLogOutSession');
add_action( 'wp_ajax_mod_checkPassword', 'modCheckPassword' );
add_action( 'wp_ajax_mod_updateSession', 'modUpdateSession');
add_action( 'wp_ajax_mod_logoutSession', 'modLogOutSession');


function modUpdateSession()
{
	$id = $_POST['mod_id'];
	modAddSession($id, 'log_in');
	echo 'success';

	wp_die();
}

function modCheckPassword()
{
	check_ajax_referer( 'mod_passwordNonce', 'nonce' );
	if( true ){

		$mod_firstName = $_POST['firstName'];
		$mod_lastName = $_POST['lastName'];
		$mod_email = $_POST['email'];
		$modPass = get_option('mod_password');
		$modFormPass = $_POST['mod_password'];
		$mod_location = $_POST['location'];
		
		if($modPass == $modFormPass){

			$returner = modCheckUser( $mod_firstName, $mod_lastName, $mod_email, $mod_location );
			echo $returner;
		}else
		{
			echo 'Incorrect Password. Please Try Again.';
		}
	}
	wp_die();
}

function modCheckUser ( $mod_firstName, $mod_lastName, $mod_email, $mod_location )
{
	global $wpdb;
	$trMod_name = $wpdb->prefix . 'trMod_Users';
	
	//CHECK IF ENTRY EXISTS/GET USER ID
	$sql = "SELECT * FROM $trMod_name WHERE firstName = '$mod_firstName' AND lastName = '$mod_lastName' AND email = '$mod_email' AND location = '$mod_location'";

	$results = $wpdb->get_row($sql);
	//var_dump($results);
	if ( null !== $results ) 
	{
	  	$id = $results->id;
	  	
	  	$returner = modAddSession( $id, 'log_in' );
		if( $returner )
		{
			return $id;
		}else
		{
			return 'Failed to Update. Please Try Again.';
		}
	} else 
	{
	  	$results = $wpdb->insert( 
			$trMod_name, 
			array( 
				'id' => '',
				'firstName' => $mod_firstName, 
				'lastName' => $mod_lastName, 
				'email' => $mod_email,
				'location' => $mod_location,
				'createdDate' => date("Y-m-d H:i:s")
			)
		);

		if($results !== false)
		{
			$id = $wpdb->insert_id;
			$returner = modAddSession( $id, 'log_in');
			if( $returner )
			{
				return $id;
			}else
			{
				return 'Failed to Update. Please Try Again.';
			}
		}else{
			return 'Failed to Add. Please Try Again.';
		}

	}
	wp_die();
}

function modLogOutSession($id){

	if($id == ''){
		$id = $_POST['mod_id'];
	}

	modAddSession($id, 'log_out');
	echo 'success';

	wp_die();
}

function modAddSession($id, $type)
{
	global $wpdb;
	$trMod_session = $wpdb->prefix . 'trMod_Sessions';
	
	$results = $wpdb->insert(
		$trMod_session,
		array(
			'id' => '',
			'user_id' => $id,
			'sessionDate' => date("Y-m-d H:i:s"),
			'sessionType' => $type
		)
	);

	if($results !== false)
	{
		return true;
	}else{
		return false;
	}

	return false;
	wp_die();

}

//General Functions for distribution list from HCA Corporate Page.

function mod_get_data($url) {

  $ch = curl_init();
  $userAgent ="Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.204 Safari/534.16";
  $timeout = 5;
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  $data = curl_exec($ch);
  curl_close($ch);

  return $data;
}

function mod_get_state_list(){

	$url = 'http://homecareassistance.com/api?method=match_list';
	$locations_data = mod_get_data($url);
	$xml = simplexml_load_string($locations_data);
	$location_list = array();

	foreach($xml->location as $location_city){
	    foreach($location_city->location as $location){
	    	$state = (string) $location->state;
	      //$state = $location->state;
	      //$namer = $location->name;
	      //$postal = $location->postal;
		    if( $state != '' && !in_array( $state, $location_list )){
		    	$location_list[] = $state;
		    } 
	    }
	}

	return $location_list;
}

function mod_get_country_list($country){
	$url = 'http://homecareassistance.com/api?method=country&postcountry='.$country;
	$locations_data = mod_get_data($url);
	$xml = simplexml_load_string($locations_data);
	$location_list = array();
	
	foreach($xml->location as $location_state){
		$location_list[] = $location_state->loc_state;
	}

	return $location_list;
}

function get_active_states(){
	$active_array = array();
	$active_loc = get_option('mod_active_loc');
	$active_array = explode(';', $active_loc);

	return $active_array;
}

function mod_get_location_list(){

  $url = 'http://homecareassistance.com/api?method=match_list';
  $locations_data = mod_get_data($url);
  $xml = simplexml_load_string($locations_data);
  $location_list = array();
  $active_list = array();
  $active_list = get_active_states();

  foreach($xml->location as $location_city){
    foreach($location_city->location as $location){

      $state_checker = (string) $location->state;
      
      if( !empty( $active_list ) ){
      	if( $state_checker == ''){
      		if($location->name == '-------Country: Australia-------' ){
      			$aus = mod_get_country_list('aus');
      			if( array_intersect( $aus, $active_list ) ){
      				$location_list[] = array('bs1', '&nbsp;', '');
      				$state = $location->state;
      				$namer = $location->name;
      				$postal = $location->postal;
      				$location_list[] = array($postal, $namer, $state);
      			}
      		}elseif( $location->name == '-------Country: Canada-------'){
      			$canada = mod_get_country_list('can');
      			if( array_intersect( $canada, $active_list ) ){
      				$location_list[] = array('bs1', '&nbsp;', '');
      				$state = $location->state;
      				$namer = $location->name;
      				$postal = $location->postal;
      				$location_list[] = array($postal, $namer, $state);
      			}

      		}elseif( $location->name == '-------Country: United States-------'){
      			$usa = mod_get_country_list('usa');
      			if( array_intersect( $usa, $active_list ) ){
      				$location_list[] = array('bs1', '&nbsp;', '');
      				$state = $location->state;
      				$namer = $location->name;
      				$postal = $location->postal;
      				$location_list[] = array($postal, $namer, $state);
      			}
      		}
      	}

      	if( in_array( $state_checker, $active_list ) && $state_checker != '' ){
      		$state = $location->state;
      		$namer = $location->name;
      		$postal = $location->postal;
      		$location_list[] = array($postal, $namer, $state);
      	}

      }else{
      	$state = $location->state;
      	$namer = $location->name;
      	$postal = $location->postal;
      	$location_list[] = array($postal, $namer, $state);
      }
    }
  }

  return $location_list;
}

function mod_get_distr_list($notifyLocation){
  	$location_list = mod_get_location_list();
  	//$categories = get_the_category($tester);
  	//$location = $categories[0]->location;
  	$postal = '';

    foreach($location_list as $location){
      $needle = $location[2].'-'.$location[1];

      if($notifyLocation == $needle){
        $postal = $location[0];
      }
    }

    if($postal != ''){
      $location_meta = mod_get_email_job($postal);
      $email_array = $location_meta['email_job'];
    }else{
      $email_array = 'ahong@homecareassistance.com';
    }

  return $email_array;
}

function mod_get_email_job($postal){
  $postal_coder = (strpos($postal,' ') !== false) ? str_replace(' ', '%20', $postal) : $postal;
  $url = 'http://homecareassistance.com/api/?method=jobs&postal='.$postal_coder;

  $locations_data = mod_get_data($url);
  $xml = simplexml_load_string($locations_data);
  $location_list = array();

  foreach($xml->location as $location){
    //$location_list['post_code'] = $location->post_code;
    $location_list['email_job'] = $location->email_job;
    //$location_list['job_url'] = $location->job_url;
    //$location_list['auto_response'] = $location->auto_response;
    //$location_list['contact'] = $location->contact;
    //$location_list['email_general'] = $location->email_general;
  }

  return $location_list;
}

//Courses API

add_action( 'wp_ajax_nopriv_mod_checkCourse', 'checkCourseSession');
add_action( 'wp_ajax_nopriv_mod_completeCourse', 'completeCourseSession');
add_action( 'wp_ajax_mod_checkCourse', 'checkCourseSession');
add_action( 'wp_ajax_mod_completeCourse', 'completeCourseSession');

function checkCourseSession(){ //Checks for exisiting or adds a new record
	global $wpdb;
	$trMod_courses = $wpdb->prefix . 'trMod_Courses';

	$user_id = $_POST['user_id'];
	$course_id = $_POST['course_id'];

	/*$sql = "SELECT * FROM $trMod_courses WHERE user_id = '$user_id' AND course_id = '$course_id'";

	$results = $wpdb->get_row($sql);
	//var_dump($results);
	if ( null !== $results ) 
	{
	  	$id = $results->id;
		
		ADD LOCATION

		$returner = updateCourseSession( $id, 'updateStart', NULL );
		
		if($returner !== false)
		{
			echo $id;
		}else
		{
			echo'Failed to Add Course. Please Try Again.';
		}

	} else 
	{*/
		$returner = $wpdb->insert(
			$trMod_courses,
			array(
				'id' => '',
				'user_id' => $user_id,
				'course_id' => $course_id,
				'courseStart' => date("Y-m-d H:i:s")
			)
		);

		if($returner !== false)
		{
			$id = $wpdb->insert_id;
			
			if( $returner )
			{
				echo $id;
			}else
			{
				echo 'Failed to Add Course. Please Try Again.';
			}
		}else
		{
			echo 'Failed to Add Course. Please Try Again.';
		}
	//}

	//echo 'Failed to Add Course. Please Try Again.';

	wp_die();
}

function completeCourseSession()
{

	check_ajax_referer( 'mod_passwordNonce', 'nonce' );
	if(true){
		$id = $_POST['id'];
		$location = $_POST['mod_location'];
		$results = updateCourseSession( $id, 'updateEnd', $location );

		if($results !== false)
		{
			//echo $id;
			$returner = notifyCourseComplete( $id, $location );
			if( $returner )
			{
				echo $id;
			}else
			{
				echo 'Failed to Notify Office. Please Try Again.';
			}
		}else{
			echo 'Failed to Close Cousre. Please Try Again.';
		}
	}

	die();
	
}

function mod_set_html_content_type() {
    return 'text/html';
}

function notifyCourseComplete( $id, $location ){
	//Get Information
	global $wpdb;
	$trMod_courses = $wpdb->prefix . 'trMod_Courses';
	$trMod_name = $wpdb->prefix . 'trMod_Users';

	$sql = "SELECT * FROM $trMod_courses WHERE id = '$id'";

	$results = $wpdb->get_row($sql);

	if( null !== $results )
	{
		$user = $results->user_id;
		$sql = "SELECT * FROM $trMod_name WHERE id = '$user'";

		$returner = $wpdb->get_row($sql);

		if( null !== $returner ){

			add_filter( 'wp_mail_content_type', 'mod_set_html_content_type' );

		    //wp_mail('ahong@homecareassistance.com','here', 'test');

		    $email_to = mod_get_distr_list($results->notifyLocation);

		    //$Sendto = "ahong@homecareassistance.com, ".$email_to;
		    $Sendto = "ahong@homecareassistance.com";
		    $date = date('l jS \of F Y h:i:s A');
		    //$Bcc = "test2@test.com";

		    //if($email2 == ''){ //Spammer check

		    $string = '<b>Date:</b>'.$date.' EST<br />
		    <b>Caregiver First Name:</b> '.strtoupper( $returner->firstName ).'<br />
		    <b>Caregiver Last Name:</b> '.strtoupper( $returner->lastName ).'<br />
		    <b>Caregiver Email:</b> '.$returner->email.PHP_EOL.'<br />
		    <b>Caregiver Main Location:</b> '.$returner->location.'<br /><br />
		    <b>Course Completed:</b> '.getCourseName($results->course_id).'<br />
		    <b>Course Completed At:</b> '.$results->courseEnd.' EST<br />
		    <b>Course Completed Location Notified:</b> '.$results->notifyLocation.'<br /><br />
		    <b>Sent To:</b> '.$email_to;

		    $headers .= 'From: HomeCareAssistance.com <ahong@homecareassistance.com>' . "\r\n";
		        
		    //$subject = $returner->firstName.' '.$returner->lastName.' has completed the course '.getCourseName( $results->course_id );

		    wp_mail( $Sendto , 'New Caregiver Course Completion', $string, $headers);
		    

		    //Reset HTML Email Format
		    remove_filter( 'wp_mail_content_type', 'mod_set_html_content_type' );

		    return true;

		}else
		{
			return false;
		}
	}else
	{
		return false;
	}

	wp_die();
}

function updateCourseSession( $id, $action, $location )
{
	global $wpdb;
	$trMod_courses = $wpdb->prefix . 'trMod_Courses';

	$int_Id = intval($id);

	if($action == 'updateStart')
	{
		$results = $wpdb->update( 
			$trMod_courses, 
			array( 
				'courseStart' => date("Y-m-d H:i:s"),
			), 
			array( 'id' => $int_Id )
		);
	}elseif($action == 'updateEnd')
	{
		$results = $wpdb->update( 
			$trMod_courses, 
			array( 
				'courseEnd' => date("Y-m-d H:i:s"),
				'notifyLocation' => $location
			), 
			array( 'id' => $int_Id )
		);
	}

	if($results !== false)
	{
		return true;
	}else{
		return false;
	}

	wp_die();
}

//Administrator API
add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );
add_action( 'wp_ajax_ajaxlogin', 'ajax_login' );
add_action( 'wp_ajax_queryUser', 'adminQuery' );
add_action( 'wp_ajax_queryCourses', 'queryCourses' );

function ajax_login(){

    // First check the nonce, if it fails the function will break
    check_ajax_referer( 'ajax-login-nonce', 'security' );

    // Nonce is checked, get the POST data and sign user on
    $info = array();
    $info['user_login'] = $_POST['username'];
    $info['user_password'] = $_POST['password'];
    $info['remember'] = true;

    $user_signon = wp_signon( $info, false );
    if ( is_wp_error($user_signon) ){
        echo json_encode(array('loggedin'=>false, 'message'=>__('Wrong username or password.')));
    } else {
        echo json_encode(array('loggedin'=>true, 'message'=>__('Login successful, redirecting...')));
    }

    die();
}

function queryCourses(){
	global $wpdb;
	$trMod_courses = $wpdb->prefix . 'trMod_Courses';

	if( intval( $_POST['trainee'] ) ){
		$course_completed = getCourses($_POST['trainee']);
		if( count($course_completed) > 0 ){

			$list = array();
			
			foreach($course_completed as $course){
				$duration = ( $course->courseEnd - $course->courseStart ) / 60000;
		        $list[] = array(
		            'id'   => $course->course_id,
		            'courseName' => getCourseName($course->course_id),
		            'courseComplete' => $course->courseEnd,
		            'courseLocation' => $course->notifyLocation
	        	);
			}

			if(!empty($list)){
	    		header("Content-type: application/json");
	    		echo json_encode( $list );
	    	}else{
	    		echo 'No Courses Found';
	    	}
		}else{
			echo 'No Courses Found';
		}
	}else{	
		echo 'No Courses Found';
	}

	die();
}

function adminQuery(){
	
	global $wpdb;
	$trMod_name = $wpdb->prefix . 'trMod_Users';
	
	//check_ajax_referer( 'mod_passwordNonce', 'nonce' );

	$firstName= $_POST['firstName'];
	$lastName= $_POST['lastName'];
	$email= $_POST['email'];
	$location = $_POST['location'];

	if( $firstName == '' && $lastName == '' && $email == '' && $location == 'all' ){

		$sql = "SELECT * FROM $trMod_name";
		
	}else{	
		if($firstName == ''){
			$firstNamer = '';
		}else{
			$firstNamer = " firstName LIKE '%{$firstName}%' ";
		}

		if($lastName == ''){
			$lastName = '';
		}else{
			if($firstName != ''){
				$lastNamer = " AND lastName LIKE '%{$lastName}%' ";
			}else{
				$lastNamer = " lastName LIKE '%{$lastName}%' ";
			}
		}

		if($email == ''){
			$emaler = '';
		}else{
			if( $firstName != '' || $lastName != ''){
				$emailer = " AND email LIKE '%{$email}%' ";
			}else{
				$emailer = " email LIKE '%{$email}%' ";
			}
		}

		if($location == '' || $location == 'all'){
			$location == '';
		}else{
			if( $firstName != '' || $lastName != '' || $email != '' ){
				$locator = " AND location LIKE '%{$location}%' ";
			}else{
				$locator = " location LIKE '%{$location}%' ";
			}
		}

		$sql = "SELECT * FROM $trMod_name WHERE ".$firstNamer.$lastNamer.$emailer.$locator;
	}

	$results = $wpdb->get_results($sql);

	if ( null !== $results ) 
	{
	  	
	  	$list = array();
    	foreach ( $results as $post ) {
    		$last_login = getLastLogin($post->id);
    		$course_completed = getCourses($post->id);
    		$last_course = $course_completed[0];
	        $list[] = array(
	            'id'   => $post->id,
	            'firstName' => strtoupper( $post->firstName ),
	            'lastName' => strtoupper( $post->lastName ),
	            'email' => $post->email,
	            'user_location' => $post->location,
	            'sessionDate' => $last_login,
	            'coursesCompleted' => count($course_completed),
	            'lastCourse' => getCourseName($last_course->course_id),
	            'lastCourseDate' => $last_course->courseEnd
	        );
    	}

    	if(!empty($list)){
    		header("Content-type: application/json");
    		echo json_encode( $list );
    	}else{
    		echo 'No user found';
    	}
        
	}else{
		echo 'No user found';
	}

	die();
}

function getCourseName($course_id){
	$course = get_post($course_id);
	
	return $course->post_title;
}

function getLastLogin($id){
	global $wpdb;
	$trMod_session = $wpdb->prefix . 'trMod_Sessions';

	$sql = "SELECT * FROM $trMod_session WHERE user_id = '$id' AND sessionType = 'log_in' ORDER BY id DESC LIMIT 1";

	$results = $wpdb->get_results($sql);
	
	foreach($results as $result){
		$sessionDate = $result->sessionDate;
	}

	return $sessionDate;

	wp_die();
}

function getCourses($id){
	global $wpdb;
	$trMod_courses = $wpdb->prefix . 'trMod_Courses';

	$sql = "SELECT * FROM $trMod_courses WHERE user_id = '$id' AND courseEnd IS NOT NULL ORDER BY id DESC";

	$results = $wpdb->get_results($sql);

	if ( null !== $results ) 
	{
	  	return $results;
	}else{
		return NULL;
	}

	wp_die();
}

?>