<?php

/**
 * Do not load this file directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

add_shortcode('list-event-speakers', 'pg_esv_list_speakers');

function pg_esv_speakers(){

					$speaker_args = array(
						'post_type' => 'event-speakers',
						'posts_per_page' => -1,
						'orderby' => 'title',
						'order' => 'ASC',
						'post_status' => 'publish',
					);
					$speakers = get_posts($speaker_args);
	
    				foreach($speakers as $speaker ){
						setup_postdata($speaker);
						$speaker_event_id = get_post_meta($speaker->ID, "speaker_event_id", true);

						$imageid = get_post_meta($speaker->ID, "image_speaker_id", true);
					    $jobtitle = get_post_meta($speaker->ID, "title", true);

					    if( !empty($imageid) ):
					            $profile_image = wp_get_attachment_image($imageid, 'profile-image');
					       	else:
					       		$profile_image = '';
					   endif;
						
						if($event_id == $speaker_event_id){	

							return 	'<div class="speaker-grid pull-left">
								    <a href="' . the_permalink($speaker->ID) . '" title="' . get_the_title($speaker->ID) . '">
								    <figure>' . $profile-image .
							         		'<figcaption>
								    			<div class="personal-info">
								    			<h3>' 
								    			.  get_the_title($speaker->ID) . 
								    			'</h3>
								    			</div>
								    			<div class="job-title">
								    			<h3>' . $jobtitle . '</h3>
								    			</div>
								    		</figcaption>
								    		</figure>
								    		</a>
								    </div>';

						}
					}

}


?>