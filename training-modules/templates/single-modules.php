<?/*
/*
*/
/*
 *  Single Modules Page. 
 * 
 * 
 */ 
?>
<? //CHECK FOR SESSION COOKIE ?>
<html>
<head>
<?php wp_head(); ?>
</head>

<body id="post_<? echo $post->post_ID;?>" class="single-modules modules">

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

<? $module_meta = get_post_meta($post->ID, 'module_meta', true);?>
<div class="mod_wrapper" style="display:none;">
		<!--Check for Session-->
		<!--slideshow-->
		<div class="reveal">
			<div class="header_logo">
				<a href="<? echo get_bloginfo('url');?>/modules">Back</a>	
			</div>
			<!-- Any section element inside of this container is displayed as a slide -->
			<div class="slides">
				<section class='intro'>
					<div class='mod_intro_holder' align="center">
						<h2><? the_title();?></h2>
						<? $mod_intro_text = get_option('mod_intro');?>
						<p><? if( $mod_intro_text != '' )
						{
							echo stripslashes( get_option('mod_intro') );
						}?></p>
						<div id="mod_start_error"></div>
						<div id="mod_classStart" class="button" module="<? echo $post->ID;?>">Start</div>
					</div><!--mod_intro_holder-->
				</section><!--mod_intro-->
				<? 

				//SLIDESHOW
				if($module_meta['type'] == 'slideshow')
				{
					$gallery = get_attached_media( 'image' );
					$images = orderGalleryByTitle( $gallery);
					//var_dump($images);
					if(is_array($images)){
						foreach($images as $key => $value){
							$image_url = wp_get_attachment_image_src($value, 'full');?>
							<section>
								<img src="<? echo $image_url[0];?>" />
							</section>
						<? }
					}
				}

				//VIDEO
				if($module_meta['type'] == 'video')
				{
					$video = $module_meta['video_link'];
				?>
					<section>
						<video width="640" height="480" src="<? echo $video;?>" controls>
				            Your browser does not support the video tag.
				        </video>
					</section>
				<? }?>

				<section class='mod_class_outro' data-state='mod_outro' duration="<? echo $module_meta['mod_minimum'];?>">
					<div class='mod_intro_holder' align="center">
						<div id="mod_outro_error_msg"></div>
						<div id="mod_classEndForm" style="display:none;">
							<h2><? the_title();?></h2>
							<? $location_list = mod_get_location_list();?>
							<? $mod_outro_text = get_option('mod_outro');?>
							<p><? if( $mod_outro_text != '' )
							{
								echo stripslashes( get_option('mod_outro') );
							}?></p>
							<select name="mod_location" id="mod_location"  realname="Location" class="validate[required]" required="required">
					            <option selected="selected" value="" bs='bs'>Location to Notify</option>
					            <? //var_dump($locations_data);
					              foreach($location_list as $location){
					                $bs_string = (strpos($location[0], 'bs') !== false) ? 'bs="bs"' : ''; 
					                $bs_sep = (strpos($location[0], 'bs') !== false) ? '' : '-';
					                $location_output = (strpos($location[0], 'bs') !== false) ? "bs" : $location[2].$bs_sep.$location[1]; ?>
					                <option <? echo $bs_string;?> value='<? echo $location_output;?>'><? echo $location[2];?><? echo $bs_sep;?><? echo $location[1];?></option>
					              <? }
					          	?>
					        </select>
					        <div id="mod_outro_error"></div>
							<div id="mod_classEnd" class="button">Submit Completion</div>
						</div><!--mod_classEndForm-->
					</div><!--mod_intro_holder-->
				</section><!--mod_outro-->
			</div><!--slides-->
		</div><!--reveal-->
</div><!--mod_wrapper-->

<?php endwhile; // end of the loop.?>
<?php wp_footer();?>
<script type="text/javascript">
//$.noConflict();
//(function($) 
//{
jQuery(document).ready(function() {

	Reveal.initialize({
		controls: true,
		progress: true,
		history: true,
		center: true,
		transition: 'slide', // none/fade/slide/convex/concave/zoom
		// Optional reveal.js plugins
		/*dependencies: [
			{ src: 'lib/js/classList.js', condition: function() { return !document.body.classList; } },
			{ src: 'plugin/markdown/marked.js', condition: function() { return !!document.querySelector( '[data-markdown]' ); } },
			{ src: 'plugin/markdown/markdown.js', condition: function() { return !!document.querySelector( '[data-markdown]' ); } },
			{ src: 'plugin/highlight/highlight.js', async: true, callback: function() { hljs.initHighlightingOnLoad(); } },
			{ src: 'plugin/zoom-js/zoom.js', async: true },
			{ src: 'plugin/notes/notes.js', async: true }
		]*/
	})
	jQuery('body.modules').fadeIn();
	jQuery('.controls').hide();

	Reveal.addEventListener( 'mod_outro', function() {
		//console.log( jQuery('.mod_outro').attr( 'duration' ) );
		var courseDuration = jQuery('.mod_class_outro').attr( 'duration' );
		console.log( courseDuration );
		checkCourseDuration( courseDuration );
	});	

	if( !checkModSession() )
	{
		window.location.href = '<? echo get_bloginfo(url);?>/modules';
	} else
	{
		jQuery('.mod_wrapper').fadeIn();
	}

	jQuery('#mod_classStart').click(function(){
		jQuery('#mod_start_error').html('Loading...').fadeIn();
		jQuery('#mod_classStart').hide();
		var course_id = jQuery(this).attr('module');
		console.log( course_id );
		startCourse( course_id );

		//if(!startCourse( course_id ) ){

		//}else{
			//alert('Failed to End Course. Please Try Again.');
		//}
	});

	jQuery('#mod_classEnd').click(function(){
		jQuery('#mod_outro_error').html('Loading...');
		jQuery('#mod_classEnd').hide();
		var url = '<? echo get_bloginfo(url);?>/modules';
		endCourse(url);
	});

});

//})( jQuery );
</script>
</body>
</html>
<? //ELSE REDIRECT BACK TO LOGIN PAGE ?>
