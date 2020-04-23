<?php

/**
 * Do not load this file directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

function pg_esv_speaker_title(){

		global $post;

		if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;

		$input = get_post_meta($post->ID);	

		if(isset($input["speaker_title"])){
	        $speaker_title = $input["speaker_title"][0];
	    } 

	   ?>

	<div class="event_speaker_title">
	<?php
	    
	     ?>
	        <div><label>Speaker Title Heading:</label>
	        <input name="speaker_title" type="text" value="<?php echo isset($speaker_title) ? $speaker_title : 'Speakers'; ?>" />
	    	</div>

	</div>

	<?php

	}

	function pg_esv_event_link(){

		global $post;

		if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;

		$speaker_event_id = get_post_meta($post->ID, 'speaker_event_id', true);


	?>

	<style type="text/css">
		<?php include(plugin_dir_path( __FILE__ ) . '../css/event-speakers.css'); ?>
	</style>

	<div class="event_page_info">
	<?php

	    $events = tribe_get_events();
	    
	     ?>
	        <div><label>Select Event:</label><select name="speaker_event_id" onchange="eventChange(event)"> 
	        <option selected="selected" value=""><?php echo esc_attr( __( 'Select event' ) ); ?>
	        </option> 
	        <?php
	            if(!empty($events)):
	                foreach ( $events as $event ) { 
	                	setup_postdata($event);
	                	?>
	                     <option value="<?php echo $event->ID ?>" <?php selected( $speaker_event_id, $event->ID ); ?> ><?php echo $event->post_title ?></option> 
	                    <?php
	                        }
	            endif;        

	        ?>
	                                                                                                                
	        </select>
	        </div>

	</div>


	<?php

	}

	function pg_esv_event_speakers(){

		global $post;
		wp_enqueue_media();
		wp_register_script('photo_upload.js', plugin_dir_path( __FILE__ ) . '/js/photo_upload.js', true);
		wp_enqueue_script( 'photo_upload.js' );

		$image_speaker_url = get_post_meta($post->ID, 'image_speaker_url', true);
	    $image_speaker_id = get_post_meta($post->ID, '$image_speaker_id', true);
		$company = get_post_meta($post->ID, 'company', true);
	    $title = get_post_meta($post->ID, 'title', true);
	    $email = get_post_meta($post->ID, 'email', true);
	    $about_speaker =get_post_meta($post->ID, 'about_speaker', true);
		$website = get_post_meta($post->ID, 'website', true);

	?>

	<div class="event_speaker">
		<div class="margin"><label>Photo:</label>
		<input type="text" name="image_speaker_url" class="image_speaker_url" value="<?php echo isset($image_speaker_url) ? $image_speaker_url : ''; ?>" />
		<input type="hidden" name="image_speaker_id" class="image_speaker_id" value="<?php echo isset($image_speaker_id) ? $image_speaker_id: ''; ?>" />
		<input class="my_upl_button" type="button" value="Upload File" />
		<input class="my_clear_button" type="button" value="Clear" />
		<div class="margin" id="upload_img_preview" style="min-height: 100px; margin-top: 20px;">
		    <img style="max-width: 300px; width: 100%;" src="<?php echo esc_url($image_speaker_url); ?>" alt="Image Preview" />
		</div>
	</div>
	<div class="margin"><label>Company:</label><input type="text" name="company" value="<?php echo isset($company) ? $company : ''; ?>" /></div>
	<div class="margin"><label>Job Title:</label><input type="text" name="title" value="<?php echo isset($title) ? $title : ''; ?>" /></div>
	<div class="margin"><label>Email:</label><input type="text" name="email" value="<?php echo isset($email) ? $email : ''; ?>" /></div>
	<div class="margin"><label>Website:</label><input type="text" name="website" value="<?php echo isset($website) ? $website : ''; ?>" /></div>

	<div class="mt10">
	<label>Description:</label><br><br>
	<?php wp_editor( htmlspecialchars_decode($about_speaker), 'metabox_ID', $settings=array('textarea_name'=>'about_speaker')); ?>
	</div>

	</div>

	<?php

	}

?>