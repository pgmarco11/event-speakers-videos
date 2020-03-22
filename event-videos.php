<?php
/**
 * Plugin Name: Event Videos for Events Calendar
 * Description: Add Videos to your events
 * Version: 1.0.0
 */

/**
 * Do not load this file directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

function event_video_pages() {

	$labels = array(
			'label' 			=> __('Event Videos'),
			'singular_label' 	=> __('Event Video'),
			'public' 			=> true,
			'show_ui' 			=> true,
			'capability_type' 	=> 'post',
			'hierarchical' 		=> false,
			'has_archive' 		=> false,
			'supports' 			=> array('title','excerpt', 'page-attributes','custom-fields'),
			'rewrite' 			=> array('slug' => 'event-videos', 'with_front' => false),
		);

		register_post_type('event-videos', $labels);

		$args = array(
			'hierarchical'			=> true,
			'labels'				=> 'Video Categories',
			'singular_label' 		=> 'Video Category',
			'query_var'				=> true,
			'rewrite'				=> true,
			'slug'	 				=> 'video-category',
			'register_meta_box_cb'	=> 'event_video_add_meta'
		);
		register_taxonomy('video-category', 'event-videos', $args );

}
add_action('init', 'event_video_pages');

function slug_save_video_post_callback( $post_id ) {

    // verify post is not a revision
    if ( ! wp_is_post_revision( $post_id ) ) {

        // unhook this function to prevent infinite looping
        remove_action( 'save_post', 'slug_save_video_post_callback' );

        // update the post slug
        wp_update_post( array(
            'ID' => $post_id,
            'post_name' => '' // do your thing here
        ));

        // re-hook this function
        add_action( 'save_post', 'slug_save_video_post_callback' );

    }
}
add_action( 'save_post', 'slug_save_video_post_callback', 10, 3 );


function change_video_title(){

	$screen = get_current_screen();
    if  ( 'event-videos' == $screen->post_type ) {
        $title = 'Enter The Video Title';
    } else {
    	$title = 'Add title';
    }

    return $title;

}
add_filter('enter_title_here', 'change_video_title');

function event_video_meta(){

	add_meta_box('event-link', 'Event', 'event_video_link', 'event-videos', 'advanced', 'high');
	add_meta_box('event-videos', 'Event Video', 'event_videos', 'event-videos', 'advanced', 'high');
	add_meta_box('video-title', 'Event Videos Title', 'video_title','tribe_events', 'advanced', 'high');

}
add_action('admin_init', 'event_video_meta');

function event_video_link(){

	global $post;

	if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;

	$event_id = get_post_meta($post->ID, 'event_id', true);

?>

<style type="text/css">
	<?php include('css/event-videos.css'); ?>
</style>

<div class="event_page_info">
<?php

    $events = tribe_get_events();
    
     ?>
        <div><label>Select Event:</label><select name="event_id" > 
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

function video_title(){

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

function event_videos(){

	global $post;
	wp_enqueue_media();
	wp_register_script('photo_upload.js', get_template_directory_uri() . '/js/photo_upload.js', true);
	wp_enqueue_script( 'photo_upload.js' );
	wp_register_script('video_upload.js', get_template_directory_uri() . '/js/video_upload.js', true);
	wp_enqueue_script( 'video_upload.js' );

	$image_url = get_post_meta($post->ID, 'image_url', true);
    $image_id = get_post_meta($post->ID, 'image_id', true);

    $about =get_post_meta($post->ID, 'about', true);

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
		<input type="text" name="image_url" class="image_url" value="<?php echo isset($image_url) ? $image_url : ''; ?>" />
		<input type="hidden" name="image_id" class="image_id" value="<?php echo isset($image_id) ? $image_id: ''; ?>" />
		<input class="my_upl_button" type="button" value="Upload File" />
		<input class="my_clear_button" type="button" value="Clear" />
	</div>

	<div class="margin" id="upload_img_preview" style="min-height: 100px;">		

			<?php if(!empty($video_embed_url)): ?>
			<div class="alignleft mb40 p0 linkwrap">
				<iframe class="video-frame" align="left" src="<?php echo esc_url($video_embed_url); ?>" width="320" height="282" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
			</div>
			<?php else: ?>
		    <video width="320" height="240" controls poster="<?php echo esc_url($image_url); ?>" >
		    	<source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
		    	Your browser does not support the video tag.
		    </video>
			<?php endif; ?>

	</div>

<div class="mt10 clear">
<label>Description:</label><br><br>
<?php wp_editor( htmlspecialchars_decode($about), 'metabox_ID', $settings=array('textarea_name'=>'about')); ?>
</div>

</div>

<?php

}

function event_video_save_extras($post_id){

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
		if( isset($_POST['about']) ){
			$data=htmlspecialchars($_POST['about']);
			update_post_meta($post_id, 'about', $data);
		}
		if( isset($_POST['image_url']) ){
			update_post_meta($post_id, "image_url", $_POST["image_url"]);
		}
		if( isset($_POST['image_id']) ){
			update_post_meta($post_id, "image_id", $_POST["image_id"]);
		}
		if( isset($_POST['video_embed_url']) ){
			update_post_meta($post_id, "video_embed_url", $_POST["video_embed_url"]);
		}
		if( isset($_POST['video_url']) ){
			update_post_meta($post_id, "video_url", $_POST["video_url"]);
		}
		if( isset($_POST['video_id']) ){
			update_post_meta($post_id, "video_id", $_POST["video_id"]);
		}
		if( isset($_POST['video_title']) ){
			update_post_meta($post_id, "video_title", $_POST["video_title"]);
		}

	}

}
add_action('save_post', 'event_video_save_extras');


function event_videos_edit_columns($columns){
	$columns = array(
		"cb" 			=> "<input type=\"checkbox\" />",
		"title"			=> "Name",
		"created"		=> 'Created Date',
		"event"			=> "Event Name",
		"category"		=> "Event Category",

		);
	return $columns;
}
add_filter('manage_edit-event-videos_columns', "event_videos_edit_columns");

function event_videos_custom_columns($column){
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
		case "category":
			$category_obj = get_the_terms($post->ID, 'video-category');
			if($category_obj == true){
				$category = join(', ', wp_list_pluck($category_obj, 'name'));
			} else {
				$category = "";
			}			
			echo $category;
			break;
	}
}
add_action("manage_event-videos_posts_custom_column", "event_videos_custom_columns");

function event_videos_sortable_columns( $columns ){

	$columns['created'] = 'created';
	$columns['event'] = 'event';
	$columns['category'] = 'category';
	return $columns;
}	
add_filter('manage_edit-event-videos_sortable_columns', 'event_videos_sortable_columns');

function event_videos_orderby($query){

	if ( ! $query->is_main_query()  )
    return $query;

		$orderbyCreated = $query->get('created');
		$orderbyEvent = $query->get('event');
		$orderbyCategory = $query->get('category');

		if( 'slice' == $orderbyCreated ){
			$query->set('meta_key', 'created');
			$query->set('orderby', 'meta_value_num');
		}
		if ( 'slice' == $orderbyEvent ){
			$query->set('meta_key', 'event');
			$query->set('orderby', 'meta_value_num');
		}
		if ( 'slice' == $orderbyCategory ){
			$query->set('meta_key', 'category');
			$query->set('orderby', 'meta_value_num');
		}

}
add_action('pre_get_posts', 'event_videos_orderby');


?>