<?php
/**
 * The template for displaying single room
 */

	get_header();

	while( have_posts() ){ the_post();
		
		$room_option = tourmaster_get_post_meta(get_the_ID(), 'tourmaster-room-option');

		// header
		$enable_title = empty($room_option['enable-page-title'])? tourmaster_get_option('room_general', 'enable-room-title', 'enable'): $room_option['enable-page-title'];
		if( $enable_title == 'enable' ){

			$background_radius = '';
			$top_radius = empty($room_option['title-background-top-radius'])? '': $room_option['title-background-top-radius'];
			$bottom_radius = empty($room_option['title-background-bottom-radius'])? '': $room_option['title-background-bottom-radius'];
			if( empty($top_radius) && empty($bottom_radius) ){
				$top_radius = tourmaster_get_option('room_general', 'room-title-background-top-radius', '');
				$bottom_radius = tourmaster_get_option('room_general', 'room-title-background-bottom-radius', '');
			}
			if( !empty($top_radius) || !empty($bottom_radius) ){
				$background_radius  = empty($top_radius)? '0 0 ': "{$top_radius} {$top_radius} ";
				$background_radius .= empty($bottom_radius)? '0 0': "{$bottom_radius} {$bottom_radius} ";
			}

			echo '<div class="tourmaster-room-single-header-title-wrap" ' . tourmaster_esc_style(array(
				'background-image' => empty($room_option['header-image'])? '': $room_option['header-image'],
				'border-radius' => $background_radius
			)) . ' >';
			echo '<div class="tourmaster-room-single-header-background-overlay" ' . tourmaster_esc_style(array(
				'opacity' => empty($room_option['header-background-overlay-opacity'])? '': $room_option['header-background-overlay-opacity']
			)) . '></div>';
			echo '<div class="tourmaster-container" >';
			echo '<h1 class="tourmaster-item-pdlr" >' . get_the_title() . '</h1>';
			if( !empty($room_option['page-caption']) ){
				echo '<div class="tourmaster-item-pdlr tourmaster-page-caption" >' . $room_option['page-caption'] . '</div>';
			}
			echo '</div>';
			echo '</div>';
		}

		// editor content
		if( empty($room_option['show-wordpress-editor-content']) || $room_option['show-wordpress-editor-content'] == 'enable' ){
			ob_start();
			the_content();
			$content = ob_get_contents();
			ob_end_clean();

			if( !empty($content) ){
				echo '<div class="tourmaster-container" >';
				echo '<div class="tourmaster-page-content tourmaster-item-pdlr" >';
				echo '<div class="tourmaster-single-main-content" >' . $content . '</div>'; // tourmaster-single-main-content
				echo '</div>'; // tourmaster-page-content
				echo '</div>'; // tourmaster-container
			}
		}
		
	} // while

	if( !post_password_required() ){
		do_action('gdlr_core_print_page_builder');
	}

	////////////////////////////////////////////////////////////////////
	// review section
	////////////////////////////////////////////////////////////////////
	$enable_review = tourmaster_get_option('room_general', 'display-single-review', 'enable');
	if( $enable_review == 'enable' && (empty($room_option['enable-review']) || $room_option['enable-review'] == 'enable') ){

		$room_ids = array();
		if( !empty($sitepress) ){
			$trid = $sitepress->get_element_trid(get_the_ID(), 'post_page');
			$translations = $sitepress->get_element_translations($trid,'post_page');
			foreach( $translations as $translation ){
				$room_ids[] = $translation->element_id;
			}
		}else if( function_exists('pll_get_post_translations') ){
			$pll_translations = pll_get_post_translations(get_the_ID());
			foreach( $pll_translations as $translation ){
				$room_ids[] = $translation; 
			}
		}else{
			$room_ids = array(get_the_ID());
		}

		$review_num_fetch = apply_filters('tourmaster_review_num_fetch', 5);

		global $wpdb;
		$sql  = "SELECT * from {$wpdb->prefix}tourmaster_room_review ";
		$sql .= "WHERE review_room_id IN (" . implode(',', $room_ids) . ") ";
		$sql .= "ORDER BY review_date DESC ";
		$sql .= tourmaster_get_sql_page_part(1, $review_num_fetch);
		$results = $wpdb->get_results($sql);
		
		if( !empty($results) ){

			$sql  = "SELECT COUNT(*) from {$wpdb->prefix}tourmaster_room_review ";
			$sql .= $wpdb->prepare("WHERE review_room_id = %d ", get_the_ID());
			$max_num_page = intval($wpdb->get_var($sql)) / $review_num_fetch;

			echo '<div class="tourmaster-single-review-container tourmaster-container" >';
			echo '<div class="tourmaster-single-review-item tourmaster-item-pdlr clearfix" >';

			$advance_review = tourmaster_get_option('room_general', 'display-advance-review', 'disable');
			if( $advance_review == 'enable' ){
				$room_rating = get_post_meta(get_the_ID(), 'tourmaster-room-rating', true);
				tourmaster_advance_review_sidebar($room_rating);
			}
			
			tourmaster_review_content_item($results, $max_num_page, $advance_review);
			
			echo '</div>'; // tourmaster-single-review-item
			echo '</div>'; // tourmaster-single-review-container
		} 
	} 

	tourmaster_room_single_booknow_bar();

	get_footer(); 
?>