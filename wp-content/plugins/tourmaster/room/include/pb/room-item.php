<?php
	/*	
	*	Goodlayers Tour Item
	*/
	
	if( !class_exists('tourmaster_room_item') ){
		class tourmaster_room_item{
			
			var $settings = '';
			
			// init the variable
			function __construct( $settings = array() ){
				
				$this->settings = wp_parse_args($settings, array(
					'category' => '', 
					'tag' => '', 
					'num-fetch' => '9', 
					'layout' => 'fitrows',
					'thumbnail-size' => 'full', 
					'orderby' => 'date', 
					'order' => 'desc',
					'room-style' => 'grid', 
					'has-column' => 'enable',
					'no-space' => 'disable',
					'excerpt' => 'specify-number', 
					'excerpt-number' => 55, 
					'column-size' => 60,
					/*
					'filterer' => 'none',
					'filterer-align' => 'center',
					*/
					'pagination' => 'none'
				));
				
			}
			
			// get the content of the tour item
			function get_content(){
				
				if( function_exists('gdlr_core_set_container_multiplier') && !empty($this->settings['column-size']) ){
					gdlr_core_set_container_multiplier(intval($this->settings['column-size']) / 60, false);
				}

				$ret = '';
				if( !empty($this->settings['query']) ){
					$query = $this->settings['query'];
				}else{
					$query = $this->get_room_query();
				}

				// carousel style
				if( $this->settings['layout'] == 'carousel' ){
					$slides = array();
					$column_no = 60 / intval($this->settings['column-size']);

					$flex_atts = array(
						'carousel' => true,
						'margin' => empty($this->settings['carousel-item-margin'])? '': $this->settings['carousel-item-margin'],
						'overflow' => empty($this->settings['carousel-overflow'])? '': $this->settings['carousel-overflow'],
						'column' => $column_no,
						'start-at' => empty($this->settings['carousel-start-at'])? '': $this->settings['carousel-start-at'],
						'move' => empty($this->settings['carousel-scrolling-item-amount'])? '': $this->settings['carousel-scrolling-item-amount'],
						'navigation' => empty($this->settings['carousel-navigation'])? 'navigation': $this->settings['carousel-navigation'],
						'navigation-on-hover' => empty($this->settings['carousel-navigation-show-on-hover'])? 'disable': $this->settings['carousel-navigation-show-on-hover'],
						'navigation-align' => empty($this->settings['carousel-navigation-align'])? '': $this->settings['carousel-navigation-align'],
						'navigation-size' => empty($this->settings['carousel-navigation-size'])? '': $this->settings['carousel-navigation-size'],
						'navigation-icon-color' => empty($this->settings['carousel-navigation-icon-color'])? '': $this->settings['carousel-navigation-icon-color'],
						'navigation-icon-background' => empty($this->settings['carousel-navigation-icon-bg'])? '': $this->settings['carousel-navigation-icon-bg'],
						'navigation-icon-padding' => empty($this->settings['carousel-navigation-icon-padding'])? '': $this->settings['carousel-navigation-icon-padding'],
						'navigation-icon-radius' => empty($this->settings['carousel-navigation-icon-radius'])? '': $this->settings['carousel-navigation-icon-radius'],
						'navigation-margin' => empty($this->settings['carousel-navigation-margin'])? '': $this->settings['carousel-navigation-margin'],
						'navigation-side-margin' => empty($this->settings['carousel-navigation-side-margin'])? '': $this->settings['carousel-navigation-side-margin'],
						'navigation-icon-margin' => empty($this->settings['carousel-navigation-icon-margin'])? '': $this->settings['carousel-navigation-icon-margin'],
						'navigation-left-icon' => empty($this->settings['carousel-navigation-left-icon'])? '': $this->settings['carousel-navigation-left-icon'],
						'navigation-right-icon' => empty($this->settings['carousel-navigation-right-icon'])? '': $this->settings['carousel-navigation-right-icon'],
						'bullet-style' => empty($this->settings['carousel-bullet-style'])? '': $this->settings['carousel-bullet-style'],
						'controls-top-margin' => empty($this->settings['carousel-bullet-top-margin'])? '': $this->settings['carousel-bullet-top-margin'],
						'nav-parent' => 'tourmaster-room-item',
						'disable-autoslide' => (empty($this->settings['carousel-autoslide']) || $this->settings['carousel-autoslide'] == 'enable')? '': true,
						'mglr' => (($this->settings['no-space'] == 'yes')? false: true),
					);

					if( in_array($flex_atts['navigation'], array('navigation', 'both')) && empty($this->settings['title']) && empty($this->settings['caption']) ){
						$flex_atts['vcenter-nav'] = true;
						$flex_atts['additional-class'] = 'tourmaster-nav-style-rect';
					}else if( $flex_atts['navigation'] == 'navigation-outer' && empty($flex_atts['navigation-left-icon']) && empty($flex_atts['navigation-right-icon']) ){
						$flex_atts['navigation-old'] = true;
					}

					$room_style = new tourmaster_room_style();

					tourmaster_setup_admin_postdata();
					while($query->have_posts()){ $query->the_post();
						$slides[] = $room_style->get_content( $this->settings );
					} // while
					wp_reset_postdata();
					tourmaster_reset_admin_postdata();
					
					$ret .= tourmaster_get_flexslider($slides, $flex_atts);

				// fitrows style
				}else{

					// room item
					tourmaster_setup_admin_postdata();
					$ret .= '<div class="tourmaster-room-item-holder gdlr-core-js-2 clearfix" data-layout="' . $this->settings['layout'] . '" >';
					$ret .= $this->get_room_grid_content($query);
					$ret .= '</div>';
					wp_reset_postdata();
					tourmaster_reset_admin_postdata();

					// pagination
					if( $this->settings['pagination'] != 'none' ){
						$extra_class = ($this->settings['no-space'] == 'yes')? '': 'tourmaster-item-pdlr';

						if( $this->settings['pagination'] == 'page' ){
							$ret .= tourmaster_get_pagination($query->max_num_pages, $this->settings, $extra_class);
						}else if( $this->settings['pagination'] ){
							$paged = empty($query->query['paged'])? 2: intval($query->query['paged']) + 1;
							
							$ajax_settings =  $this->settings;
							unset($ajax_settings['query']);
							$ret .= tourmaster_get_ajax_load_more('room', $ajax_settings, $paged, $query->max_num_pages, 'tourmaster-room-item-holder', $extra_class);
						}
					}
				}

				if( function_exists('gdlr_core_set_container_multiplier') ){
					gdlr_core_set_container_multiplier(1, false);
				}

				return $ret;
			}

			// get content of non carousel tour item
			function get_room_grid_content( $query ){

				$ret = '';
				$column_sum = 0;
				$room_style = new tourmaster_room_style();
				while($query->have_posts()){ $query->the_post();

					$args = $this->settings;

					$additional_class = '';
					if( $this->settings['has-column'] == 'enable' ){
						$additional_class  = ($this->settings['no-space'] == 'enable')? '': ' tourmaster-item-pdlr';
						$additional_class .= in_array($this->settings['room-style'], array('thumbnail'))? ' tourmaster-item-mgb': '';
						if( !empty($this->settings['column-size']) ){
							$additional_class .= ' tourmaster-column-' . $this->settings['column-size'];
						}
						if( $column_sum == 0 || $column_sum + intval($this->settings['column-size']) > 60 ){
							$column_sum = intval($this->settings['column-size']);
							$additional_class .= ' tourmaster-column-first';
						}else{
							$column_sum += intval($this->settings['column-size']);
						}
					}else{
						$additional_class .= ' tourmaster-item-pdlr';
					}

					$ret .= '<div class="gdlr-core-item-list ' . esc_attr($additional_class) . '" >';
					$ret .= $room_style->get_content( $args );
					$ret .= '</div>';

				} // while
				
				return $ret;
			}
			
			// query the post
			function get_room_query(){
				
				$args = array( 'post_type' => 'room', 'post_status' => 'publish', 'suppress_filters' => false );

				if( empty($this->settings['exclude-self']) || $this->settings['exclude-self'] == 'enable' ){
					$post_id = get_the_ID();
					if( get_post_type() == 'room' && !empty($post_id) ){
						$args['post__not_in'] = array($post_id);
					}
				}

				// apply search variable
				if( !empty($this->settings['s']) ){
					$args['s'] = $this->settings['s'];
				} 

				if( !empty($this->settings['meta_query']) ){
					$args['meta_query'] = $this->settings['meta_query'];
				}else{
					$args['meta_query'] = array();

					// hide unavailable room
					if( !empty($this->settings['hide-not-avail']) && $this->settings['hide-not-avail'] == 'enable' ){
						$args['meta_query'][] = array(
							'key' => 'tourmaster-room-date-avail',
							'compare' => 'EXISTS'
						);
					}
				}
				
				// category - tag selection
				if( !empty($this->settings['tax_query']) ){
					$args['tax_query'] = $this->settings['tax_query'];
				}else{
					$args['tax_query'] = array(
						'relation' => empty($this->settings['relation'])? 'OR': $this->settings['relation']
					);
					
					if( !empty($this->settings['category']) ){
						if( !is_array($this->settings['category']) ){
							$this->settings['category'] = array_map('trim', explode(',', $this->settings['category']));
						}
						array_push($args['tax_query'], array('terms'=>$this->settings['category'], 'taxonomy'=>'room_category', 'field'=>'slug'));
					}
					if( !empty($this->settings['tag']) ){
						if( !is_array($this->settings['tag']) ){
							$this->settings['tag'] = array_map('trim', explode(',', $this->settings['tag']));
						}
						array_push($args['tax_query'], array('terms'=>$this->settings['tag'], 'taxonomy'=>'room_tag', 'field'=>'slug'));
					}

					$tax_fields = array(
						'room_location' => esc_html__('Room Location', 'tourmaster')
					) + tourmaster_get_custom_tax_list('room');
					foreach( $tax_fields as $tax_field => $tax_title ){
						if( !empty($this->settings[$tax_field]) ){
							if( !is_array($this->settings[$tax_field]) ){
								$this->settings[$tax_field] = array_map('trim', explode(',', $this->settings[$tax_field]));
							}
							$args['tax_query'][] = array(
								array('terms'=>$this->settings[$tax_field], 'taxonomy'=>$tax_field, 'field'=>'slug')
							);
						}
					}
				}
				
				// pagination
				if( empty($this->settings['paged']) ){
					if( empty($this->settings['pagination']) || $this->settings['pagination'] != 'none' ){
						$args['paged'] = (get_query_var('paged'))? get_query_var('paged') : get_query_var('page');
					}
					$args['paged'] = empty($args['paged'])? 1: $args['paged'];
				}else{
					$args['paged'] = $this->settings['paged'];
				}
				$this->settings['paged'] = $args['paged'];
				
				// variable
				$args['posts_per_page'] = empty($this->settings['num-fetch'])? 9: $this->settings['num-fetch'];
				$args['order'] = empty($this->settings['order'])? 'desc': $this->settings['order'];

				if( empty($this->settings['orderby']) ){
					$args['orderby'] = 'date';
				}else if( in_array($this->settings['orderby'], array('date', 'title')) ){
					$args['orderby'] = $this->settings['orderby'];
				}else if( $this->settings['orderby'] == 'rating' ){
					$args['meta_key'] = 'tourmaster-room-rating-score';
					$args['meta_type'] = 'NUMERIC';
					$args['orderby'] = 'meta_value_num';
				}else{
					$args['orderby'] = $this->settings['orderby'];
				}

				return new WP_Query( $args );
			}
			
		} // tourmaster_tour_item
	} // class_exists