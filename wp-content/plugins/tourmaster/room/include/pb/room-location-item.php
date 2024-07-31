<?php
	/*	
	*	Goodlayers Tour Item
	*/
	
	if( !class_exists('tourmaster_room_location_item') ){
		class tourmaster_room_location_item{
			
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
					'pagination' => 'none'
				));
				
			}
			
			// get the content of the tour item
			function get_content(){
				
				if( function_exists('gdlr_core_set_container_multiplier') && !empty($this->settings['column-size']) ){
					gdlr_core_set_container_multiplier(intval($this->settings['column-size']) / 60, false);
				}

				$ret = '';
				if( !empty($this->settings['locations']) ){
					$locations = array();
					foreach( $this->settings['locations'] as $location ){
						$locations[] = get_term_by('slug', $location, 'room_location');
					}
				}else{
					$locations = get_terms('room_location', array(
						'hide_empty' => false
					));
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

					$location_style = new tourmaster_room_location_style();

					tourmaster_setup_admin_postdata();
					foreach( $locations as $location ){
						$slides[] = $location_style->get_content( $location, $this->settings );
					} // while
					wp_reset_postdata();
					tourmaster_reset_admin_postdata();
					
					$ret .= tourmaster_get_flexslider($slides, $flex_atts);

				// fitrows style
				}else{

					// room item
					tourmaster_setup_admin_postdata();
					$ret .= '<div class="tourmaster-room-location-item-holder gdlr-core-js-2 clearfix" data-layout="' . $this->settings['layout'] . '" >';
					$ret .= $this->get_room_grid_content($locations);
					$ret .= '</div>';
					wp_reset_postdata();
					tourmaster_reset_admin_postdata();
				}

				if( function_exists('gdlr_core_set_container_multiplier') ){
					gdlr_core_set_container_multiplier(1, false);
				}

				return $ret;
			}

			// get content of non carousel tour item
			function get_room_grid_content( $locations ){

				$ret = '';
				$column_sum = 0;
				$location_style = new tourmaster_room_location_style();
				foreach( $locations as $location ){

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
					$ret .= $location_style->get_content( $location, $this->settings );
					$ret .= '</div>';

				} // while
				
				return $ret;
			}
			
		} // tourmaster_tour_item
	} // class_exists