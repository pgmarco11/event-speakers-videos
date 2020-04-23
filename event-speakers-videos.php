<?php
/*
 * Plugin Name: Speakers and Videos for Events Calendar
 * Description: Add Speakers and Videos to specfic events
 * Version: 1.1.0
 * Author: Peter Giammarco
 * Author URI: https://www.pgiammarco.com
 * License: GPLv2
*/

/**
 * Do not load this file directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

//register plugin only when events calendar is activated
register_activation_hook( __FILE__, 'pg_esv_install');

function pg_esv_install(){

	if( !is_plugin_active('the-events-calendar/the-events-calendar.php') && current_user_can('activate_plugins')){

		wp_die('Sorry, this plugin requires The Events Calendar Plugin, please install and activate. <br><br><a href="' . admin_url('plugins.php') . '">Return to Plugins</a>');

	}  

}


add_action('wp_enqueue_scripts', 'pg_esv_register_styles');

function pg_esv_register_styles(){

			wp_register_style('event-video-speakers', plugins_url('event-speakers-videos/css/events.css'));
			wp_enqueue_style('event-video-speakers');

}

add_action('init', 'pg_esv_event_pages');

function pg_esv_event_pages() {

			$pg_esv_speaker_args = array(
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

			register_post_type('event-speakers', $pg_esv_speaker_args);

			$pg_esv_videos_args = array(
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

			register_post_type('event-videos', $pg_esv_videos_args);

			$pg_esv_video_args = array(
				'hierarchical'			=> true,
				'labels'				=> 'Video Categories',
				'singular_label' 		=> 'Video Category',
				'query_var'				=> true,
				'rewrite'				=> true,
				'slug'	 				=> 'video-category',
				'register_meta_box_cb'	=> 'event_video_add_meta'
			);
			register_taxonomy('video-category', 'event-videos', $pg_esv_video_args );

			add_image_size('profile-image', 240, 295, array( 'center', 'top' ));

}


if(is_admin()){

	function pg_esv_change_title(){

		$screen = get_current_screen();

	    if  ( 'event-speakers' == $screen->post_type ) {
	        $title = 'Enter The Speakers Name';
	    } elseif ( 'event-videos' == $screen->post_type ) {
	        $title = 'Enter The Video Title';
	    } else {
	    	$title = 'Add title';
	    }

	    return $title;
	}
	add_filter('enter_title_here', 'pg_esv_change_title');

	function pg_esv_event_pages_meta(){

		add_meta_box('speaker-title', 'Event Speakers Title', 'pg_esv_speaker_title','tribe_events', 'normal', 'high');
		add_meta_box('event-link', 'Events', 'pg_esv_event_link', 'event-speakers', 'normal', 'high');
		add_meta_box('event-speakers', 'Event Speakers', 'pg_esv_event_speakers', 'event-speakers', 'normal', 'high');
		
		add_meta_box('event-link', 'Event', 'pg_esv_event_video_link', 'event-videos', 'normal', 'high');
		add_meta_box('event-videos', 'Event Video', 'pg_esv_event_videos', 'event-videos', 'normal', 'high');
		add_meta_box('video-title', 'Event Videos Title', 'pg_esv_video_title','tribe_events', 'normal', 'high');
		
	}
	add_action('admin_init', 'pg_esv_event_pages_meta');

	//meta callback functions for speakers and videos
	include( plugin_dir_path( __FILE__ ) . 'includes/event-speakers.php');	
	include( plugin_dir_path( __FILE__ ) . 'includes/event-videos.php');	

	function pg_esv_slug_save_post_callback( $post_id ) {

	    // verify post is not a revision
	    if ( ! wp_is_post_revision( $post_id ) ) {

	        // unhook this function to prevent infinite looping
	        remove_action( 'save_post', 'pg_esv_slug_save_post_callback' );

	        // update the post slug
	        wp_update_post( array(
	            'ID' => $post_id,
	            'post_name' => '' // do your thing here
	        ));

	        // re-hook this function
	        add_action( 'save_post', 'pg_esv_slug_save_post_callback' );

	    }
	}
	add_action( 'save_post', 'pg_esv_slug_save_post_callback', 10, 3 );

	function pg_esv_event_save_extras($post_id){

		global $post;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
			return;
		} else {

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
			if( isset($_POST['website']) ){
				update_post_meta($post_id, "website", $_POST["website"]);
			}
			if( isset($_POST['speaker_title']) ){
				update_post_meta($post_id, "speaker_title", $_POST["speaker_title"]);
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
			if( isset($_POST['about_video']) ){
				$data=htmlspecialchars($_POST['about_video']);
				update_post_meta($post_id, 'about_video', $data);
			}
			if( isset($_POST['about_speaker']) ){
				$data=htmlspecialchars($_POST['about_speaker']);
				update_post_meta($post_id, 'about_speaker', $data);
			}


			if( isset($_POST['image_speaker_url']) ){
				update_post_meta($post_id, "image_speaker_url", $_POST["image_speaker_url"]);
			}
			if( isset($_POST['image_speaker_id']) ){
				update_post_meta($post_id, "image_speaker_id", $_POST["image_speaker_id"]);
			}
			if( isset($_POST['image_video_url']) ){
				update_post_meta($post_id, "image_video_url", $_POST["image_video_url"]);
			}
			if( isset($_POST['image_video_id']) ){
				update_post_meta($post_id, "image_video_id", $_POST["image_video_id"]);
			}
			if( isset($_POST['speaker_event_id']) ){
				update_post_meta($post_id, "speaker_event_id", $_POST["speaker_event_id"]);
			}
			if( isset($_POST['video_event_id']) ){
				update_post_meta($post_id, "video_event_id", $_POST["video_event_id"]);
			}


		}

	}
	add_action('save_post', 'pg_esv_event_save_extras');

	function pg_esv_event_videos_edit_columns($columns){

	$columns = array(
		"cb" 			=> "<input type=\"checkbox\" />",
		"title"			=> "Name",
		"created"		=> 'Created Date',
		"event"			=> "Event Name",
		"category"		=> "Event Category",

		);


	return $columns;
	}
	add_filter('manage_edit-event-videos_columns', "pg_esv_event_videos_edit_columns");

	function pg_esv_event_videos_custom_columns($column){
		global $post;
		$output = get_post_custom();


		switch($column)
		{
			case "created":
				$created = get_the_date();
				echo $created;
				break;
			case "event":
				$event = get_the_title($output["video_event_id"][0]);
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
	add_action("manage_event-videos_posts_custom_column", "pg_esv_event_videos_custom_columns");

	function pg_esv_event_videos_sortable_columns( $columns ){

		$columns['created'] = 'created';
		$columns['event'] = 'event';
		$columns['category'] = 'category';
		return $columns;
	}	
	add_filter('manage_edit-event-videos_sortable_columns', 'pg_esv_event_videos_sortable_columns');

	function pg_esv_event_videos_orderby($query){

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
	add_action('pre_get_posts', 'pg_esv_event_videos_orderby');

	function pg_esv_event_speakers_edit_columns($columns){
		$columns = array(
			"cb" 			=> "<input type=\"checkbox\" />",
			"title"			=> "Name",
			"created"		=> 'Created Date',
			"event"			=> "Event Name",
			"company"		=> "Company",
			);
		return $columns;
	}
	add_filter('manage_edit-event-speakers_columns', "pg_esv_event_speakers_edit_columns");

	function pg_esv_event_speakers_custom_columns($column){
		global $post;
		$output = get_post_custom();

		switch($column)
		{
			case "created":
				$created = get_the_date();
				echo $created;
				break;
			case "event":
				$event = get_the_title($output["speaker_event_id"][0]);
				echo $event;
				break;
			case "company":
				$company = $output["company"][0];
				echo $company;
				break;
		}
	}
	add_action("manage_event-speakers_posts_custom_column", "pg_esv_event_speakers_custom_columns");

	function pg_esv_event_speakers_sortable_columns( $columns ){

		$columns['created'] = 'created';
		$columns['event'] = 'event';
		$columns['company'] = 'company';
		return $columns;
	}
	add_filter('manage_edit-event-speakers_sortable_columns', 'pg_esv_event_speakers_sortable_columns');	

	function pg_esv_event_speakers_orderby($query){

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
	add_action('pre_get_posts', 'pg_esv_event_speakers_orderby');

}

//shortcodes
include( plugin_dir_path( __FILE__ ) . 'includes/shortcodes.php');	