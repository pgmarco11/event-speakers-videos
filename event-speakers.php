<?php
/**
 * Plugin Name: Event Speakers for Events Calendar
 * Description: Add Speakers to your events
 * Version: 1.0.0
 */

/**
 * Do not load this file directly.
 */


if ( ! defined( 'ABSPATH' ) ) {
	die();
}

function event_pages() {

	$labels = array(
			'label' 			=> __('Event Speakers'),
			'singular_label' 	=> __('Event Speaker'),
			'public' 			=> true,
			'show_ui' 			=> true,
			'capability_type' 	=> 'post',
			'hierarchical' 		=> false,
			'has_archive' 		=> false,
			'supports' 			=> array('title','excerpt', 'page-attributes','custom-fields'),
			'rewrite' 			=> array('slug' => 'event-speakers', 'with_front' => false),
		);

	register_post_type('event-speakers', $labels);

}

add_action('init', 'event_pages');

add_action('admin_init', 'event_pages_meta');

function change_title(){

	$screen = get_current_screen();
    if  ( 'event-speakers' == $screen->post_type ) {
        $title = 'Enter The Speakers Name';
    } else {
    	$title = 'Add title';
    }

    return $title;

}
add_filter('enter_title_here', 'change_title');

function slug_save_post_callback( $post_id ) {

    // verify post is not a revision
    if ( ! wp_is_post_revision( $post_id ) ) {

        // unhook this function to prevent infinite looping
        remove_action( 'save_post', 'slug_save_post_callback' );

        // update the post slug
        wp_update_post( array(
            'ID' => $post_id,
            'post_name' => '' // do your thing here
        ));

        // re-hook this function
        add_action( 'save_post', 'slug_save_post_callback' );

    }
}
add_action( 'save_post', 'slug_save_post_callback', 10, 3 );

function event_pages_meta(){

	add_meta_box('event-link', 'Event', 'event_link', 'event-speakers', 'advanced', 'high');
	add_meta_box('event-speakers', 'Event Speaker', 'event_speakers', 'event-speakers', 'advanced', 'high');
	add_meta_box('speaker-title', 'Event Speakers Title', 'speaker_title','tribe_events', 'advanced', 'high');

}

function speaker_title(){

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

function event_link(){

	global $post;

	if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;

	$event_id = get_post_meta($post->ID, 'event_id', true);


?>

<style type="text/css">
	<?php include('css/event-speakers.css'); ?>
</style>

<div class="event_page_info">
<?php

    $events = tribe_get_events();
    
     ?>
        <div><label>Select Event:</label><select name="event_id" onchange="eventChange(event)"> 
        <option selected="selected" value=""><?php echo esc_attr( __( 'Select event' ) ); ?>
        </option> 
        <?php
            if(!empty($events)):
                foreach ( $events as $event ) { 
                	setup_postdata($event);
                	?>
                     <option value="<?php echo $event->ID ?>" <?php selected( $event_id, $event->ID ); ?> ><?php echo $event->post_title ?></option> 
                    <?php
                        }
            endif;        

        ?>
                                                                                                                
        </select>
        </div>

</div>


<?php

}

function event_speakers(){

	global $post;
	wp_enqueue_media();
	wp_register_script('photo_upload.js', get_template_directory_uri() . '/js/photo_upload.js', true);
	wp_enqueue_script( 'photo_upload.js' );

	$image_url = get_post_meta($post->ID, 'image_url', true);
    $image_id = get_post_meta($post->ID, 'image_id', true);
	$company = get_post_meta($post->ID, 'company', true);
    $title = get_post_meta($post->ID, 'title', true);
    $email = get_post_meta($post->ID, 'email', true);
    $about =get_post_meta($post->ID, 'about', true);
	$website = get_post_meta($post->ID, 'website', true);

?>

<div class="event_speaker">
	<div class="margin"><label>Photo:</label>
	<input type="text" name="image_url" class="image_url" value="<?php echo isset($image_url) ? $image_url : ''; ?>" />
	<input type="hidden" name="image_id" class="image_id" value="<?php echo isset($image_id) ? $image_id: ''; ?>" />
	<input class="my_upl_button" type="button" value="Upload File" />
	<input class="my_clear_button" type="button" value="Clear" />
	<div class="margin" id="upload_img_preview" style="min-height: 100px; margin-top: 20px;">
	    <img style="max-width: 300px; width: 100%;" src="<?php echo esc_url($image_url); ?>" alt="Image Preview" />
	</div>
</div>
<div class="margin"><label>Company:</label><input type="text" name="company" value="<?php echo isset($company) ? $company : ''; ?>" /></div>
<div class="margin"><label>Job Title:</label><input type="text" name="title" value="<?php echo isset($title) ? $title : ''; ?>" /></div>
<div class="margin"><label>Email:</label><input type="text" name="email" value="<?php echo isset($email) ? $email : ''; ?>" /></div>
<div class="margin"><label>Website:</label><input type="text" name="website" value="<?php echo isset($website) ? $website : ''; ?>" /></div>

<div class="mt10">
<label>Description:</label><br><br>
<?php wp_editor( htmlspecialchars_decode($about), 'metabox_ID', $settings=array('textarea_name'=>'about')); ?>
</div>

</div>

<?php

}


add_action('save_post', 'event_save_extras');

function event_save_extras($post_id){

	global $post;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
		return;
	} else {

		if( isset($_POST['event_id']) ){
			update_post_meta($post_id, "event_id", $_POST["event_id"]);
		}
		if( isset($_POST['eventID']) ){
			update_post_meta($post_id, "eventID", $_POST["eventID"]);
		}
		if( isset($_POST['eventTitle']) ){
			update_post_meta($post_id, "eventTitle", $_POST["eventTitle"]);
		}
		if( isset($_POST['company']) ){
			update_post_meta($post_id, "company", $_POST["company"]);
		}
		if( isset($_POST['title']) ){
			update_post_meta($post_id, "title", $_POST["title"]);
		}
		if( isset($_POST['email']) ){
			update_post_meta($post_id, "email", $_POST["email"]);
		}
		if( isset($_POST['about']) ){
			$data=htmlspecialchars($_POST['about']);
			update_post_meta($post_id, 'about', $data);
		}
		if( isset($_POST['website']) ){
			update_post_meta($post_id, "website", $_POST["website"]);
		}
		if( isset($_POST['image_url']) ){
			update_post_meta($post_id, "image_url", $_POST["image_url"]);
		}
		if( isset($_POST['image_id']) ){
			update_post_meta($post_id, "image_id", $_POST["image_id"]);
		}
		if( isset($_POST['speaker_title']) ){
			update_post_meta($post_id, "speaker_title", $_POST["speaker_title"]);
		}

	}

}

add_filter('manage_edit-event-speakers_columns', "event_speakers_edit_columns");

function event_speakers_edit_columns($columns){
	$columns = array(
		"cb" 			=> "<input type=\"checkbox\" />",
		"title"			=> "Name",
		"created"		=> 'Created Date',
		"event"			=> "Event Name",
		"company"		=> "Company",
		);
	return $columns;
}

add_action("manage_event-speakers_posts_custom_column", "event_speakers_custom_columns");

function event_speakers_custom_columns($column){
	global $post;
	$output = get_post_custom();

	switch($column)
	{
		case "created":
			$created = get_the_date();
			echo $created;
			break;
		case "event":
			$event = get_the_title($output["event_id"][0]);
			echo $event;
			break;
		case "company":
			$company = $output["company"][0];
			echo $company;
			break;
	}
}

add_filter('manage_edit-event-speakers_sortable_columns', 'event_sortable_columns');

function event_sortable_columns( $columns ){

	$columns['created'] = 'created';
	$columns['event'] = 'event';
	$columns['company'] = 'company';
	return $columns;
}	

add_action('pre_get_posts', 'event_orderby');

function event_orderby($query){

	if(! is_admin())
		return;

		$orderbyCreated = $query->get('created');
		$orderbyEvent = $query->get('event');
		$orderbyCompany = $query->get('company');

		if( 'slice' == $orderbyCreated ){
			$query->set('meta_key', 'created');
			$query->set('orderby', 'meta_value_num');
		} 

		if ( 'slice' == $orderbyEvent ){
			$query->set('meta_key', 'event');
			$query->set('orderby', 'meta_value_num');
		}

		if('slice' == $orderbyCompany){
			$query->set('meta_key', 'company');
			$query->set('orderby', 'meta_value_num');
		}

}


?>