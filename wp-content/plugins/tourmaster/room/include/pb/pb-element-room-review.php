<?php
	/*	
	*	Goodlayers Item For Page Builder
	*/

	add_action('plugins_loaded', 'tourmaster_room_add_pb_element_tour_review');
	if( !function_exists('tourmaster_room_add_pb_element_tour_review') ){
		function tourmaster_room_add_pb_element_tour_review(){

			if( class_exists('gdlr_core_page_builder_element') ){
				gdlr_core_page_builder_element::add_element('room_review', 'tourmaster_pb_element_room_review'); 
			}
			
		}
	}
	
	if( !class_exists('tourmaster_pb_element_room_review') ){
		class tourmaster_pb_element_room_review{
			
			// get the element settings
			static function get_settings(){
				return array(
					'icon' => 'fa-star',
					'title' => esc_html__('Room Review', 'tourmaster')
				);
			}
			
			// return the element options
			static function get_options(){
				return apply_filters('tourmaster_room_review_item_options', array(		
					'general' => array(
						'title' => esc_html__('General', 'tourmaster'),
						'options' => array(
							'style' => array(
								'title' => esc_html__('Style', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'widget' => esc_html__('Widget', 'tourmaster'),
									'advance-review' => esc_html__('Advance Review', 'tourmaster')
								)
							),
							'location' => array(
								'title' => esc_html__('Location', 'tourmaster'),
								'type' => 'combobox',
								'options' => tourmaster_get_term_list('room_location', '', true),
								'condition' => array('style' => 'advance-review')
							),
							'num-display' => array(
								'title' => esc_html__('Num Display', 'tourmaster'),
								'type' => 'text',
								'default' => 5
							),
						)
					),			
					'spacing' => array(
						'title' => esc_html('Spacing', 'tourmaster'),
						'options' => array(
							'padding-bottom' => array(
								'title' => esc_html__('Padding Bottom ( Item )', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'pixel',
								'default' => '30px'
							)
						)
					),
				));
			}

			// get the preview for page builder
			static function get_preview( $settings = array() ){
				$content  = self::get_content($settings);
				return $content;
			}			

			// get the content from settings
			static function get_content( $settings = array() ){
				
				// default variable
				$settings = empty($settings)? array('num-display' => 3): $settings;

				if( empty($settings['style']) || $settings['style'] == 'widget' ){
					$ret = self::get_review_widget($settings);
				}else if( $settings['style'] == 'advance-review' ){
					$ret = self::get_review_advance($settings);
				}
				
				return $ret;
			}		

			static function get_review_advance($settings){

				global $wpdb;

				$review_num_fetch = empty($settings['num-display'])? 5: $settings['num-display'];

				$ret  = '<div class="tourmaster-room-review-item tourmaster-item-pdlr clearfix" ';
				if( !empty($settings['padding-bottom']) && $settings['padding-bottom'] != '30px' ){
					$ret .= tourmaster_esc_style(array('padding-bottom'=>$settings['padding-bottom']));
				}
				if( !empty($settings['id']) ){
					$ret .= ' id="' . esc_attr($settings['id']) . '" ';
				}
				$ret .= 'data-settings="' . esc_attr(json_encode($settings)) . '" ';
				$ret .= ' >';

				if( !empty($settings['location']) ){
					
					$term = get_term_by('slug', $settings['location'], 'room_location');
					$location_review = get_term_meta($term->term_id, 'location-rating', true);
					
					$ret .= '<div class="tourmaster-single-review-item clearfix" >';
					$ret .= tourmaster_advance_review_sidebar($location_review, false);
					
					// get rooms from location
					$query = new WP_Query(array(
						'post_type' => 'room',
						'post_status' => 'publish', 
						'suppress_filters' => false,
						'tax_query' => array(array(
							'terms' => $term->slug, 
							'taxonomy' => 'room_location', 
							'field' => 'slug'
						)),
						'posts_per_page' => '999'
					));

					// get post ids in each term
					$room_ids = array();
					while( $query->have_posts() ){ $query->the_post();
						$room_ids[] = get_the_ID();
					}
					
					// get review for this location
					$sql  = "SELECT * from {$wpdb->prefix}tourmaster_room_review ";
					$sql .= "WHERE review_room_id IN (" . implode(',', $room_ids) . ") ";
					$sql .= "ORDER BY review_date DESC ";
					$sql .= tourmaster_get_sql_page_part(1, $review_num_fetch);
					$results = $wpdb->get_results($sql);
					
					// max num page
					$sql  = "SELECT COUNT(*) from {$wpdb->prefix}tourmaster_room_review ";
					$sql .= "WHERE review_room_id IN (" . implode(',', $room_ids) . ") ";
					$max_num_page = intval($wpdb->get_var($sql)) / $review_num_fetch;
					
					$ret .= tourmaster_review_content_item($results, $max_num_page, 'enable', false);
					$ret .= '</div>';
				}else{
					
					$location_review = get_option('tourmaster-room-rating', array());
					
					$ret .= '<div class="tourmaster-single-review-item clearfix" >';
					$ret .= tourmaster_advance_review_sidebar($location_review, false);
					$sql  = "SELECT * from {$wpdb->prefix}tourmaster_room_review ";
					$sql .= "ORDER BY review_date DESC ";
					$sql .= tourmaster_get_sql_page_part(1, $review_num_fetch);
					$results = $wpdb->get_results($sql);
					
					// max num page
					$sql  = "SELECT COUNT(*) from {$wpdb->prefix}tourmaster_room_review ";
					$max_num_page = intval($wpdb->get_var($sql)) / $review_num_fetch;
					
					$ret .= tourmaster_review_content_item($results, $max_num_page, 'enable', false);
					$ret .= '</div>';
				}

				$ret .= '</div>';
				
				return $ret;
			}

			static function get_review_widget($settings){

				global $wpdb;
				$sql  = "SELECT * from {$wpdb->prefix}tourmaster_room_order as order_table ";
				$sql .= "RIGHT JOIN {$wpdb->prefix}tourmaster_room_review as review_table ";
				$sql .= "ON order_table.id = review_table.order_id ";
				$sql .= "WHERE review_score IS NOT NULL AND (order_status IS NULL OR order_status != 'cancel') ";
				$sql .= "ORDER BY review_date DESC ";
				$sql .= tourmaster_get_sql_page_part(1, $settings['num-display']);
				$results = $wpdb->get_results($sql);

				$ret  = '<div class="tourmaster-tour-review-item tourmaster-item-pdlr clearfix" ';
				if( !empty($settings['padding-bottom']) && $settings['padding-bottom'] != '30px' ){
					$ret .= tourmaster_esc_style(array('padding-bottom'=>$settings['padding-bottom']));
				}
				if( !empty($settings['id']) ){
					$ret .= ' id="' . esc_attr($settings['id']) . '" ';
				}
				$ret .= ' >';
				if( !empty($results) ){
					foreach( $results as $result ){
						if( empty($result->room_id) && !empty($result->review_room_id) ){
							$result->room_id = $result->review_room_id;
						}

						$ret .= '<div class="tourmaster-tour-review-item-list" >';
						$ret .= '<div class="tourmaster-tour-review-item-avatar tourmaster-media-image" >';
						if( !empty($result->user_id) ){
							$ret .= get_avatar($result->user_id, 85);
						}else if( !empty($result->reviewer_email) ){
							$ret .= get_avatar($result->reviewer_email, 85);
						}
						
						$ret .= '</div>'; 
						
						$ret .= '<div class="tourmaster-tour-review-item-content" >';
						$ret .= '<h3 class="tourmaster-tour-review-item-title" >';
						$ret .= '<a href="' . get_permalink($result->room_id) . '" >' . get_the_title($result->room_id) . '</a>';
						$ret .= '</h3>';
						$ret .= '<div class="tourmaster-tour-review-item-rating" >';
						$ret .= tourmaster_get_rating($result->review_score);
						if( !empty($result->user_id) ){
							$ret .= '<span class="tourmaster-tour-review-item-user" >' . tourmaster_get_user_meta($result->user_id) . '</span>';
						}else if( !empty($result->reviewer_name) ){
							$ret .= '<span class="tourmaster-tour-review-item-user" >' . $result->reviewer_name . '</span>';
						}
						$ret .= '</div>';
						$ret .= '</div>';
						$ret .= '</div>';
					}
				}
				$ret .= '</div>';
				return $ret;
			}

		} // tourmaster_pb_element_tour
	} // class_exists	

	add_shortcode('tourmaster_room_review', 'tourmaster_room_review_shortcode');
	if( !function_exists('tourmaster_room_review_shortcode') ){
		function tourmaster_room_review_shortcode($atts){
			$atts = wp_parse_args($atts, array());
			
			$ret  = '<div class="tourmaster-tour-review-shortcode clearfix tourmaster-item-rvpdlr" >';
			$ret .= tourmaster_pb_element_room_review::get_content($atts);
			$ret .= '</div>';

			return $ret;
		}
	}

	// ajax review list
	add_action('wp_ajax_get_room_item_review', 'tourmaster_ajax_get_room_item_review');
	add_action('wp_ajax_nopriv_get_room_item_review', 'tourmaster_ajax_get_room_item_review');
	if( !function_exists('tourmaster_ajax_get_room_item_review') ){
		function tourmaster_ajax_get_room_item_review(){
			
			if( !empty($_POST['settings']) ){
				global $wpdb;
				$settings = $_POST['settings'];
				$review_num_fetch = empty($settings['num-display'])? 5: $settings['num-display'];
				$paged = (empty($_POST['paged'])? '1': $_POST['paged']);

				if( !empty($settings['location']) ){
					
					$term = get_term_by('slug', $settings['location'], 'room_location');
					
					// get rooms from location
					$query = new WP_Query(array(
						'post_type' => 'room',
						'post_status' => 'publish', 
						'suppress_filters' => false,
						'tax_query' => array(array(
							'terms' => $term->slug, 
							'taxonomy' => 'room_location', 
							'field' => 'slug'
						)),
						'posts_per_page' => '999'
					));

					// get post ids in each term
					$room_ids = array();
					while( $query->have_posts() ){ $query->the_post();
						$room_ids[] = get_the_ID();
					}
					
					// get review for this location
					$sql  = "SELECT * from {$wpdb->prefix}tourmaster_room_review ";
					$sql .= "WHERE review_room_id IN (" . implode(',', $room_ids) . ") ";
					if( empty($_POST['sort_by']) || $_POST['sort_by'] == 'date' ){
						$sql .= "ORDER BY review_date DESC ";
					}else if( $_POST['sort_by'] == 'rating' ){
						$sql .= "ORDER BY review_score DESC ";
					}
					$sql .= tourmaster_get_sql_page_part($paged, $review_num_fetch);
					$results = $wpdb->get_results($sql);
					
					// max num page
					$sql  = "SELECT COUNT(*) from {$wpdb->prefix}tourmaster_room_review ";
					$sql .= "WHERE review_room_id IN (" . implode(',', $room_ids) . ") ";
					$max_num_page = intval($wpdb->get_var($sql)) / $review_num_fetch;

					die(json_encode(array(
						'content' => tourmaster_room_get_review_content_list($results) .
						tourmaster_room_get_review_content_pagination($max_num_page, $paged)
					)));
				}else{
					
					$sql  = "SELECT * from {$wpdb->prefix}tourmaster_room_review ";
					if( empty($_POST['sort_by']) || $_POST['sort_by'] == 'date' ){
						$sql .= "ORDER BY review_date DESC ";
					}else if( $_POST['sort_by'] == 'rating' ){
						$sql .= "ORDER BY review_score DESC ";
					}
					$sql .= tourmaster_get_sql_page_part($paged, $review_num_fetch);
					$results = $wpdb->get_results($sql);
					
					// max num page
					$sql  = "SELECT COUNT(*) from {$wpdb->prefix}tourmaster_room_review ";
					$max_num_page = intval($wpdb->get_var($sql)) / $review_num_fetch;
					
					die(json_encode(array(
						'content' => tourmaster_room_get_review_content_list($results) .
						tourmaster_room_get_review_content_pagination($max_num_page, $paged)
					)));
				}
			}

			die(json_encode(array()));

		} // tourmaster_get_single_tour_review
	}