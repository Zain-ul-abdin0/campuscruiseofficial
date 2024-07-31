<?php
	/*	
	*	Goodlayers Item For Page Builder
	*/

	add_action('plugins_loaded', 'tourmaster_add_pb_element_room_location_title');
	if( !function_exists('tourmaster_add_pb_element_room_location_title') ){
		function tourmaster_add_pb_element_room_location_title(){

			if( class_exists('gdlr_core_page_builder_element') ){
				gdlr_core_page_builder_element::add_element('room-location-title', 'tourmaster_pb_element_room_location_title'); 
			}
			
		}
	}
	
	if( !class_exists('tourmaster_pb_element_room_location_title') ){
		class tourmaster_pb_element_room_location_title{
			
			// get the element settings
			static function get_settings(){
				return array(
					'icon' => 'fa-plane',
					'title' => esc_html__('Room Location Title', 'tourmaster')
				);
			}
			
			// return the element options
			static function get_options(){
				return apply_filters('tourmaster_room_location_item_options', array(					
					'general' => array(
						'title' => esc_html__('General', 'tourmaster'),
						'options' => array(
							'location' => array(
								'title' => esc_html__('Location', 'tourmaster'),
								'type' => 'combobox',
								'options' => tourmaster_get_term_list('room_location')
							)
						),
					),
					'typography' => array(
						'title' => esc_html('Typography', 'tourmaster'),
						'options' => array(
							'title-font-size' => array(
								'title' => esc_html__('Title Font Size', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'pixel',
							),
							'title-font-weight' => array(
								'title' => esc_html__('Title Font Weight', 'tourmaster'),
								'type' => 'text',
								'description' => esc_html__('Eg. lighter, bold, normal, 300, 400, 600, 700, 800', 'tourmaster')
							),
							'title-letter-spacing' => array(
								'title' => esc_html__('Title Letter Spacing', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'pixel',
							),
							'title-text-transform' => array(
								'title' => esc_html__('Title Text Transform', 'tourmaster'),
								'type' => 'combobox',
								'data-type' => 'text',
								'options' => array(
									'uppercase' => esc_html__('Uppercase', 'tourmaster'),
									'lowercase' => esc_html__('Lowercase', 'tourmaster'),
									'capitalize' => esc_html__('Capitalize', 'tourmaster'),
									'none' => esc_html__('None', 'tourmaster'),
								),
								'default' => 'none'
							),
						)
					),
					'color' => array(
						'title' => esc_html('Color', 'tourmaster'),
						'options' => array(
							'title-color' => array(
								'title' => esc_html__('Title Color', 'tourmaster'),
								'type' => 'colorpicker'
							),
							'rating-color' => array(
								'title' => esc_html__('Rating Color', 'tourmaster'),
								'type' => 'colorpicker'
							),
							'price-color' => array(
								'title' => esc_html__('Price Color', 'tourmaster'),
								'type' => 'colorpicker'
							),
							'user-rating-background-color' => array(
								'title' => esc_html__('User Rating Background Color', 'tourmaster'),
								'type' => 'colorpicker'
							),
							'user-rating-text-color' => array(
								'title' => esc_html__('User Rating Text Color', 'tourmaster'),
								'type' => 'colorpicker'
							)
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
							),
						)
					)
				));
			}

			// get the preview for page builder
			static function get_preview( $settings = array() ){
				$content  = self::get_content($settings, true);
				$id = mt_rand(0, 9999);
				
				return $content;
			}			
			
			// get the content from settings
			static function get_content( $settings = array(), $preview = false ){

				// default variable
				if( empty($settings) ){
					$settings = array();
				}

				// start printing item
				$ret  = '<div class="tourmaster-room-location-title-item gdlr-core-item-pdlr gdlr-core-item-pdb clearfix" ';
				if( !empty($settings['padding-bottom']) && $settings['padding-bottom'] != '30px' ){
					$ret .= tourmaster_esc_style(array('padding-bottom'=>$settings['padding-bottom']));
				}
				if( !empty($settings['id']) ){
					$ret .= ' id="' . esc_attr($settings['id']) . '" ';
				}
				$ret .= ' >';
				
				if( !empty($settings['location']) ){
					$term = get_term_by('slug', $settings['location'], 'room_location');
					$location_style = new tourmaster_room_location_style();
					$ret .= $location_style->get_rating($term, $settings);
					$ret .= $location_style->get_title($term, $settings, false);
					$ret .= $location_style->get_customer_rating($term, $settings);
					$ret .= $location_style->get_price($term, $settings);
				}else{
					$ret .= '<div class="gdlr-core-external-plugin-message">' . esc_html__('Please select the location you want to display', 'tourmaster') . '</div>';
				}
				
				
				
				$ret .= '</div>'; // tourmaster-room-location-item
				
				return $ret;
			}			
			
		} // tourmaster_pb_element_tour
	} // class_exists	