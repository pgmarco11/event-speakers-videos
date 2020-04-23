<?php

/**
 * Do not load this file directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

function pg_esv_event_video_link(){

		global $post;

		if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;

		$video_event_id = get_post_meta($post->ID, 'video_event_id', true);

	?>

	<style type="text/css">
		<?php include(plugin_dir_path( __FILE__ ) . '../css/event-videos.css'); ?>
	</style>

	<div class="event_page_info">
	<?php

	    $events = tribe_get_events();
	    
	     ?>
	        <div><label>Select Event:</label><select name="video_event_id" > 
	        <option selected="selected" value=""><?php echo esc_attr( __( 'Select event' ) ); ?>
	        </option> 
	        <?php
	            if(!empty($events)):
	                foreach ( $events as $event ) { 
	                	setup_postdata($event);
	                	?>
	                     <option value="<?php echo $event->ID ?>" <?php selected( $video_event_id, $event->ID ); ?> ><?php echo $event->post_title ?></option> 
	                    <?php
	                        }
	            endif;        

	        ?>
	                                                                                                                
	        </select>
	        </div>

	</div>


	<?php

	}

	function pg_esv_video_title(){

		global $post;

		if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;

		$input = get_post_meta($post->ID);	

		if(isset($input["video_title"])){
	        $video_title = $input["video_title"][0];
	    } 

	   ?>

	<div class="event_video_title">
	<?php
	    
	     ?>
	        <div><label>Videos Title Heading:</label>
	        <input name="video_title" type="text" value="<?php echo isset($video_title) ? $video_title : 'Videos'; ?>" />
	    	</div>

	</div>

	<?php

	}

	function pg_esv_event_videos(){

		global $post;
		wp_enqueue_media();
		wp_register_script('photo_upload.js', plugin_dir_path( __FILE__ ) . '/js/photo_upload.js', true);
		wp_enqueue_script( 'photo_upload.js' );
		wp_register_script('video_upload.js', plugin_dir_path( __FILE__ ) . '/js/video_upload.js', true);
		wp_enqueue_script( 'video_upload.js' );

		$image_video_url = get_post_meta($post->ID, 'image_video_url', true);
	    $image_video_id = get_post_meta($post->ID, 'image_video_id', true);

	    $about_video =get_post_meta($post->ID, 'about_video', true);

	    $video_embed_url = get_post_meta($post->ID, 'video_embed_url', true);
	    $video_url = get_post_meta($post->ID, 'video_url', true);
	    $video_id = get_post_meta($post->ID, 'video_id', true);

	?>

	<div class="event_video">

		<div class="margin"><label>Vimeo Embed URL:</label>
			<input type="text" name="video_embed_url" class="video_embed_url" value="<?php echo isset($video_embed_url) ? $video_embed_url : ''; ?>" />
		</div>

		<div class="margin mt10 clear"><label>Video:</label>
			<input type="text" name="video_url" class="video_url" value="<?php echo isset($video_url) ? $video_url : ''; ?>" />
			<input type="hidden" name="video_id" class="video_id" value="<?php echo isset($video_id) ? $video_id: ''; ?>" />
			<input class="upl_button" type="button" value="Upload File" />
			<input class="clear_button" type="button" value="Clear" />
		</div>

		<div class="margin"><label>Video Placeholder:</label>
			<input type="text" name="image_video_url" class="image_video_url" value="<?php echo isset($image_video_url) ? $image_video_url : ''; ?>" />
			<input type="hidden" name="image_video_id" class="image_video_id" value="<?php echo isset($image_video_id) ? $image_video_id: ''; ?>" />
			<input class="my_upl_button" type="button" value="Upload File" />
			<input class="my_clear_button" type="button" value="Clear" />
		</div>

		<div class="margin" id="upload_img_preview" style="min-height: 100px;">		

				<?php if(!empty($video_embed_url)): ?>
				<div class="alignleft mb40 p0 linkwrap">
					<iframe class="video-frame" align="left" src="<?php echo esc_url($video_embed_url); ?>" width="320" height="282" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
				</div>
				<?php else: ?>
			    <video width="320" height="240" controls poster="<?php echo esc_url($image_video_url); ?>" >
			    	<source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
			    	Your browser does not support the video tag.
			    </video>
				<?php endif; ?>

		</div>

	<div class="mt10 clear">
	<label>Description:</label><br><br>
	<?php wp_editor( htmlspecialchars_decode($about_video), 'metabox_ID', $settings=array('textarea_name'=>'about_video')); ?>
	</div>

	</div>

	<?php

	}


?>