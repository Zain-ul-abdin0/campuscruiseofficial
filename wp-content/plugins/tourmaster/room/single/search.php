<?php
	get_header();

	//$shadow_size = tourmaster_get_option('room_general', 'search-page-tour-frame-shadow-size', '');
	$settings = array(
		'pagination' => 'page',
		'room-style' => tourmaster_get_option('room_general', 'search-room-style', 'side-thumbnail'),
		'with-frame' => tourmaster_get_option('room_general', 'search-room-with-frame', 'disable'),
		'column-size' => tourmaster_get_option('room_general', 'search-room-column-size', '30'),
		'thumbnail-size' => tourmaster_get_option('room_general', 'search-room-thumbnail-size', 'full'),
		'display-price' => tourmaster_get_option('room_general', 'search-room-display-price', 'enable'),
		'enable-price-prefix' => tourmaster_get_option('room_general', 'search-room-enable-price-prefix', 'enable'),
		'enable-price-suffix' => tourmaster_get_option('room_general', 'search-room-enable-price-suffix', 'enable'),
		'price-decimal-digit' => tourmaster_get_option('room_general', 'search-room-price-decimal-digit', 0),
		'display-ribbon' => tourmaster_get_option('room_general', 'search-room-display-ribbon', 'enable'),
		'room-info' => tourmaster_get_option('room_general', 'search-room-info', array()),
		'room-info-location-top' => true,
		'excerpt' => tourmaster_get_option('room_general', 'search-room-excerpt', 'specify-number'),
		'excerpt-number' => tourmaster_get_option('room_general', 'search-room-excerpt-number', '55'),
		'enable-rating' => tourmaster_get_option('room_general', 'search-room-enable-rating', 'enable'),
		'read-more-button' => tourmaster_get_option('room_general', 'search-room-read-more-button', 'text'),
		'custom-pagination' => true,
		'room-title-font-size' => tourmaster_get_option('room_general', 'search-room-title-font-size', ''),
		'room-title-font-weight' => tourmaster_get_option('room_general', 'search-room-title-font-weight', ''),
		'room-title-letter-spacing' => tourmaster_get_option('room_general', 'search-room-title-letter-spacing', ''),
		'room-title-text-transform' => tourmaster_get_option('room_general', 'search-room-title-text-transform', ''),
		'num-fetch' => tourmaster_get_option('room_general', 'search-room-num-fetch', '10'),
	);
	$settings['paged'] = (get_query_var('paged'))? get_query_var('paged') : get_query_var('page');
	$settings['paged'] = empty($settings['paged'])? 1: $settings['paged'];

	// search query
	$args = array(
		'post_status' => 'publish',
		'post_type' => 'room',
		'posts_per_page' => $settings['num-fetch'],
		'paged' => $settings['paged'],
	);

	// category
	$args['tax_query'] = array(
		'relation' => 'AND'
	);

	// taxonomy
	$tax_fields = array(
		'room_category' => esc_html__('Category', 'tourmaster'),
		'room_tag' => esc_html__('Tag', 'tourmaster'),
		'room_location' => esc_html__('Location', 'tourmaster')
	);
	$tax_fields = $tax_fields + tourmaster_get_custom_tax_list('room');
	foreach( $tax_fields as $tax_slug => $tax_name ){
		if( !empty($_GET['tax-' . $tax_slug]) ){
			$args['tax_query'][] = array(
				array('terms'=>$_GET['tax-' . $tax_slug], 'taxonomy'=>$tax_slug, 'field'=>'slug')
			);
		}
	}

	$meta_query = array(
		'relation' => 'AND'
	);

	// guest amount
	if( !empty($_GET['adult']) || !empty($_GET['children']) ){
		$guest_amount = intval($_GET['adult']) + intval($_GET['children']);
	
		if( !empty($guest_amount) ){

			// max guest
			$meta_query[] = array( 'relation' => 'OR',
				array(
					'key'     => 'tourmaster-room-max-guest',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => 'tourmaster-room-max-guest',
					'value'   => $guest_amount,
					'compare' => '>=',
					'type'    => 'NUMERIC'
				)
			);

			// min guest
			$meta_query[] = array( 'relation' => 'OR',
				array(
					'key'     => 'tourmaster-room-min-guest',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => 'tourmaster-room-min-guest',
					'value'   => $guest_amount,
					'compare' => '<=',
					'type'    => 'NUMERIC'
				)
			);
		}
	}

	if( !empty($meta_query) ){
		$args['meta_query'] = $meta_query;
	}

	global $wpdb;
	
	if( !empty($_GET['start_date']) && !empty($_GET['end_date']) ){
		$date_list = tourmaster_split_date($_GET['start_date'], $_GET['end_date']);
		if( !empty($date_list) ){

			// get post id where room is available within start_date to end_date
			$sql  = "(SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'tourmaster-room-date-display' ";
			$sql .= $wpdb->prepare("AND meta_value LIKE %s) ", '%' . implode(',', $date_list) . '%');
			$results = $wpdb->get_results($sql);
			$post_ids = array();
			foreach($results as $result){
				$post_ids[] = $result->post_id;
			}
			
			// subtract post id where min night & max night not match
			if( !empty($post_ids) ){
				$date_amount = sizeof($date_list);
				$sql  = "SELECT room_id as post_id FROM {$wpdb->prefix}tourmaster_room_settings ";
				$sql .= $wpdb->prepare("WHERE date_list LIKE %s", '%' . $_GET['start_date'] . '%');
				$sql .= $wpdb->prepare("AND (min_date > %d OR (max_date <> 0 AND max_date < %d) ) ", $date_amount, $date_amount);
				$results = $wpdb->get_results($sql);
				if( !empty($results) ){
					$post_ids2 = array();
					foreach($results as $result){
						$post_ids2[] = $result->post_id;
					}
					$post_ids = array_diff($post_ids, $post_ids2);
				}
			}

			if( empty($post_ids) ){
				$skip_query = true;
			}else{
				$args['post__in'] = $post_ids;
			}
		}
	}
	
	if( empty($skip_query) ){
		$settings['query'] = new WP_Query($args);
	}

	// start the content
	echo '<div class="tourmaster-template-wrapper" >';
	echo '<div class="tourmaster-container" >';

	// sidebar content
	$sidebar_type = 'none';
	echo '<div class="' . tourmaster_get_sidebar_wrap_class($sidebar_type) . '" >';
	echo '<div class="' . tourmaster_get_sidebar_class(array('sidebar-type'=>$sidebar_type, 'section'=>'center')) . '" >';
	
	
	echo '<div class="tourmaster-page-content" >';
	
	// search filter
	$search_settings = array(
		'style' => 'box',
		'align' => 'vertical',
		'button-style' => tourmaster_get_option('room_general', 'search-filters-button', 'border'),
		'search-filters' => tourmaster_get_option('room_general', 'search-filters', array()),
		'search-filter-content' => tourmaster_get_option('room_general', 'search-filters-content', 'enable'),
	);
	// if( in_array('room_location', $search_settings['search-filters']) ){ 
	// 	$search_settings['enable-room-location'] = 'enable';
	// 	$search_settings['search-filters'] = array_diff($search_settings['search-filters'], array('room_location'));
	// }
	
	echo '<div class="tourmaster-room-search-item-wrap tourmaster-column-15" >';
	echo '<h3 class="tourmaster-item-pdlr" >' . esc_html__('Check Availability', 'tourmaster') . '</h3>';
	echo tourmaster_pb_element_room_search::get_content($search_settings);
	echo '</div>';

	// content
	if( !empty($settings['query']) && $settings['query']->have_posts() ){
		echo '<div class="tourmaster-tour-search-content-wrap tourmaster-column-45" >';
		echo tourmaster_pb_element_room::get_content($settings);
		echo '</div>';
	}else{
		echo '<div class="tourmaster-single-search-not-found-wrap tourmaster-column-45 tourmaster-item-pdlr" >';
		echo '<div class="tourmaster-single-search-not-found-inner" >';
		echo '<div class="tourmaster-single-search-not-found" >';
		echo '<h3 class="tourmaster-single-search-not-found-title" >' . esc_html__('Not Found', 'tourmaster') . '</h3>';
		echo '<div class="tourmaster-single-search-not-found-caption" >' . esc_html__('Nothing matched your search criteria. Please try again with different keywords', 'tourmaster') . '</div>';
		echo '</div>'; // tourmaster-single-search-not-found
		echo '</div>'; // tourmaster-single-search-not-found-inner
		echo '</div>'; // tourmaster-single-search-not-found-wrap
	}

	echo '</div>'; // tourmaster-page-content
	
	echo '</div>'; // tourmaster-get-sidebar-class
	echo '</div>'; // tourmaster-get-sidebar-wrap-class	
	
	echo '</div>'; // tourmaster-container
	echo '</div>'; // tourmaster-template-wrapper

get_footer(); 

?>