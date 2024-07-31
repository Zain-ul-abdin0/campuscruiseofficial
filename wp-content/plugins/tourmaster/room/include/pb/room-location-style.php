<?php
	/*	
	*	Goodlayers Blog Item Style
	*/

	if( !class_exists('tourmaster_room_location_style') ){
		class tourmaster_room_location_style{

			// get the content of the tour item
			function get_content( $term, $args ){

				$ret = apply_filters('tourmaster_room_location_style_content', '', $term, $args, $this);
				if( !empty($ret) ) return $ret;

				return $this->grid_style( $term, $args ); 
				
			}			

			// get title
			function get_title( $term, $args, $with_link = true ){

				$extra_css = array(
					'font-size' => empty($args['title-font-size'])? '': $args['title-font-size'],
					'font-weight' => empty($args['title-font-weight'])? '': $args['title-font-weight'],
					'letter-spacing' => empty($args['title-letter-spacing'])? '': $args['title-letter-spacing'],
					'text-transform' => empty($args['title-text-transform'])? '': $args['title-text-transform'],
					'margin-bottom' => empty($args['title-bottom-margin'])? '': $args['title-bottom-margin']
				);

				if( $with_link ){
					$ret  = '<h3 class="tourmaster-room-title gdlr-core-skin-title" ' . tourmaster_esc_style($extra_css) . ' >';
					$ret .= '<a href="' . esc_attr(get_term_link($term->term_id)) . '" ' . tourmaster_esc_style(array(
						'color' => empty($args['title-color'])? '': $args['title-color']
					)) . ' >' . $term->name . '</a>';
					$ret .= '</h3>';
				}else{
					$extra_css['color'] = empty($args['title-color'])? '': $args['title-color'];
					$ret  = '<h3 class="tourmaster-room-title gdlr-core-skin-title" ' . tourmaster_esc_style($extra_css) . ' >' . $term->name . '</h3>';
				}

				return $ret;
			}

			// get thumbnail
			function get_thumbnail( $term, $args ){
				
				$ret = '';
				$thumbnail = get_term_meta($term->term_id, 'thumbnail', true);

				if( !empty($thumbnail) ){
					$thumbnail_link = (empty($args['thumbnail-link']) || $args['thumbnail-link'] == 'enable')? true: false;
					$ret .= '<div class="tourmaster-room-location-thumbnail tourmaster-media-image" >';
					$ret .= $thumbnail_link? '<a href="' . esc_attr(get_term_link($term->term_id)) . '" >': '';
					$ret .= tourmaster_get_image($thumbnail, $args['thumbnail-size']);
					$ret .= $thumbnail_link? '</a>': '';

					$ret .= $this->get_ribbon($term, $args);
					$ret .= '</div>';
				}

				return $ret;
			}

			// get ribbon
			function get_ribbon($term, $args = array()){

				if( !empty($args['enable-ribbon']) && $args['enable-ribbon'] == 'disable' ) return '';

				$ret = '';
				$ribbon_text = get_term_meta($term->term_id, 'banner-text', true);
				$ribbon_color = get_term_meta($term->term_id, 'banner-background', true);

				if( !empty($ribbon_text) ){
					$ret  = '<div class="tourmaster-ribbon" ' . tourmaster_esc_style(array(
						'background-color' => $ribbon_color,
					)) .' >' . $ribbon_text . '</div>';
				}

				return $ret;
			}

			// rating
			function get_rating( $term, $args ){

				if( !empty($args['enable-rating']) && $args['enable-rating'] == 'disable' ) return '';

				$ret = '';
				$rating = get_term_meta($term->term_id, 'hotel-stars', true);

				if( !empty($rating) ){
					$ret  = '<div class="tourmaster-room-location-rating" ' . tourmaster_esc_style(array(
						'color' => empty($args['rating-color'])? '': $args['rating-color']
					)) . ' >';
					$ret .= tourmaster_get_rating($rating);
					$ret .= '</div>';
				}	

				return $ret;

			}
			function get_customer_rating( $term, $args ){

				if( !empty($args['enable-customer-rating']) && $args['enable-customer-rating'] == 'disable' ) return '';

				$ret = '';
				$location_rating = get_term_meta($term->term_id, 'location-rating', true);
				if( !empty($location_rating['reviewer']) && !empty($location_rating['score']) ){
					$rating = floatval($location_rating['score']) / intval($location_rating['reviewer']);
					$rating = number_format($rating, 1);
					$reviewer_count = $location_rating['reviewer'];

					$ret  = '<div class="tourmaster-room-location-customer-rating" ' . tourmaster_esc_style(array(
						'color' => empty($args['user-rating-text-color'])? '': $args['user-rating-text-color'],
						'background' => empty($args['user-rating-background-color'])? '': $args['user-rating-background-color']
					)) . ' >';
					$ret .= '<span class="tourmaster-head" >' . sprintf('%s/5', '<span>' . ($rating/2) . '</span>') . '</span>';
					$ret .= '<span class="tourmaster-text" >' . tourmaster_room_advance_review_text($rating/2) . '</span>';
					$ret .= '<span class="tourmaster-tail" >' . sprintf('(%d reviews)', $reviewer_count) . '</span>';
					$ret .= '</div>';
				}
				
				return $ret;

			}

			// location
			function get_location( $term, $args ){

				if( !empty($args['enable-rating']) && $args['enable-rating'] == 'disable' ) return '';

				$ret  = '';
				$location = get_term_meta($term->term_id, 'location', true);

				if( !empty($location) ){
					$ret .= '<div class="tourmaster-room-location-at" ><i class="icon-location-pin" ></i>';
					$ret .=  $location;
					$ret .= '</div>';
				}

				return $ret;

			}

			// price
			function get_price( $term, $args ){

				if( !empty($args['enable-rating']) && $args['enable-rating'] == 'disable' ) return '';

				$ret = '';
				$start_price = get_term_meta($term->term_id, 'start-price', true);
				$discount_price = get_term_meta($term->term_id, 'discount-price', true);

				if( !empty($start_price) ){
					$ret .= '<div class="tourmaster-room-location-price ';
					$ret .= empty($discount_price)? '': 'tourmaster-with-discount ';
					$ret .= '" ' . tourmaster_esc_style(array(
						'color' => empty($args['price-color'])? '': $args['price-color']
					)) . ' >';
					$ret .= '<span class="tourmaster-head" >' . esc_html__('From', 'tourmaster') . '</span>';
					$ret .= '<span class="tourmaster-tail" >' . tourmaster_money_format($start_price) . '</span>';
					
					if( !empty($discount_price) ){
						$ret .= '<span class="tourmaster-discount" >' . tourmaster_money_format($discount_price) . '</span>';
					}
					$ret .= '</div>';
				}

				return $ret;

			}
			
			// tour grid
			function grid_style( $term, $args ){

				$ret  = '<div class="tourmaster-room-location-grid" >';
				$ret .= $this->get_thumbnail($term, $args);
				$ret .= $this->get_rating($term, $args);
				$ret .= $this->get_title($term, $args);
				$ret .= $this->get_location($term, $args);
				$ret .= '<div class="tourmaster-divider" ></div>';
				$ret .= $this->get_price($term, $args);
				$ret .= $this->get_customer_rating($term, $args);
				$ret .= '</div>'; // tourmaster-room-grid
				
				return $ret;
			} 

		} // tourmaster_tour_style
	} // class_exists
	