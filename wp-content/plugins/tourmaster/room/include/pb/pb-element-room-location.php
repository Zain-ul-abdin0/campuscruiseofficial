<?php
	/*	
	*	Goodlayers Item For Page Builder
	*/

	add_action('plugins_loaded', 'tourmaster_add_pb_element_room_location');
	if( !function_exists('tourmaster_add_pb_element_room_location') ){
		function tourmaster_add_pb_element_room_location(){

			if( class_exists('gdlr_core_page_builder_element') ){
				gdlr_core_page_builder_element::add_element('room-location', 'tourmaster_pb_element_room_location'); 
			}
			
		}
	}
	
	if( !class_exists('tourmaster_pb_element_room_location') ){
		class tourmaster_pb_element_room_location{
			
			// get the element settings
			static function get_settings(){
				return array(
					'icon' => 'fa-hotel',
					'title' => esc_html__('Room Location', 'tourmaster')
				);
			}
			
			// return the element options
			static function get_options(){
				return apply_filters('tourmaster_room_location_item_options', array(					
					'general' => array(
						'title' => esc_html__('General', 'tourmaster'),
						'options' => array(
							'locations' => array(
								'title' => esc_html__('Locations', 'tourmaster'),
								'type' => 'multi-combobox',
								'options' => tourmaster_get_term_list('room_location')
							)
						),
					),
					'settings' => array(
						'title' => esc_html('Style', 'tourmaster'),
						'options' => array(
							'column-size' => array(
								'title' => esc_html__('Column Size', 'tourmaster'),
								'type' => 'combobox',
								'options' => array( 60=>1, 30=>2, 20=>3, 15=>4, 12=>5 ),
								'default' => 20
							),
							'thumbnail-size' => array(
								'title' => esc_html__('Thumbnail Size', 'tourmaster'),
								'type' => 'combobox',
								'options' => 'thumbnail-size',
							),
							'thumbnail-link' => array(
								'title' => esc_html__('Thumbnail Link', 'tourmaster'),
								'type' => 'checkbox',
								'default' => 'enable'
							),
							'layout' => array(
								'title' => esc_html__('Layout', 'tourmaster'),
								'type' => 'combobox',
								'options' => array( 
									'fitrows' => esc_html__('Fit Rows', 'tourmaster'),
									'carousel' => esc_html__('Carousel', 'tourmaster'),
									'masonry' => esc_html__('Masonry', 'tourmaster'),
								),
								'default' => 'fitrows',
							),
							'carousel-item-margin' => array(
								'title' => esc_html__('Carousel Item Margin', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'pixel',
								'condition' => array( 'layout' => 'carousel' )
							),
							'carousel-overflow' => array(
								'title' => esc_html__('Carousel Overflow', 'tourmaster'),
								'type' => 'combobox',
								'options' => array(
									'' => esc_html__('Hidden', 'tourmaster'),
									'visible' => esc_html__('Visible', 'tourmaster')
								),
							),
							'carousel-scrolling-item-amount' => array(
								'title' => esc_html__('Carousel Scrolling Item Amount', 'tourmaster'),
								'type' => 'text',
								'default' => '1',
							),
							'carousel-autoslide' => array(
								'title' => esc_html__('Autoslide Carousel', 'tourmaster'),
								'type' => 'checkbox',
								'default' => 'enable',
							),
							'carousel-start-at' => array(
								'title' => esc_html__('Carousel Start At (Number)', 'tourmaster'),
								'type' => 'text',
								'default' => '',
							),
							'carousel-navigation' => array(
								'title' => esc_html__('Carousel Navigation', 'tourmaster'),
								'type' => 'combobox',
								'options' => (function_exists('gdlr_core_get_flexslider_navigation_types')? gdlr_core_get_flexslider_navigation_types(): array()),
								'default' => 'navigation',
							),
							'carousel-navigation-show-on-hover' => array(
								'title' => esc_html__('Carousel Navigation Display On Hover', 'tourmaster'),
								'type' => 'checkbox',
								'default' => 'disable',
								'condition' => array( 'carousel-navigation' => array('navigation-outer', 'navigation-inner') )
							),
							'carousel-navigation-align' => (function_exists('gdlr_core_get_flexslider_navigation_align')? gdlr_core_get_flexslider_navigation_align(): array()),
							'carousel-navigation-left-icon' => (function_exists('gdlr_core_get_flexslider_navigation_left_icon')? gdlr_core_get_flexslider_navigation_left_icon(): array()),
							'carousel-navigation-right-icon' => (function_exists('gdlr_core_get_flexslider_navigation_right_icon')? gdlr_core_get_flexslider_navigation_right_icon(): array()),
							'carousel-navigation-icon-color' => (function_exists('gdlr_core_get_flexslider_navigation_icon_color')? gdlr_core_get_flexslider_navigation_icon_color(): array()),
							'carousel-navigation-icon-hover-color' => (function_exists('gdlr_core_get_flexslider_navigation_icon_hover_color')? gdlr_core_get_flexslider_navigation_icon_hover_color(): array()),
							'carousel-navigation-icon-bg' => (function_exists('gdlr_core_get_flexslider_navigation_icon_background')? gdlr_core_get_flexslider_navigation_icon_background(): array()),
							'carousel-navigation-icon-padding' => (function_exists('gdlr_core_get_flexslider_navigation_icon_padding')? gdlr_core_get_flexslider_navigation_icon_padding(): array()),
							'carousel-navigation-icon-radius' => (function_exists('gdlr_core_get_flexslider_navigation_icon_radius')? gdlr_core_get_flexslider_navigation_icon_radius(): array()),
							'carousel-navigation-size' => (function_exists('gdlr_core_get_flexslider_navigation_icon_size')? gdlr_core_get_flexslider_navigation_icon_size(): array()),
							'carousel-navigation-margin' => (function_exists('gdlr_core_get_flexslider_navigation_margin')? gdlr_core_get_flexslider_navigation_margin(): array()),
							'carousel-navigation-side-margin' => (function_exists('gdlr_core_get_flexslider_navigation_side_margin')? gdlr_core_get_flexslider_navigation_side_margin(): array()),
							'carousel-navigation-icon-margin' => (function_exists('gdlr_core_get_flexslider_navigation_icon_margin')? gdlr_core_get_flexslider_navigation_icon_margin(): array()),
							'carousel-bullet-style' => array(
								'title' => esc_html__('Carousel Bullet Style', 'tourmaster'),
								'type' => 'radioimage',
								'options' => (function_exists('gdlr_core_get_flexslider_bullet_itypes')? gdlr_core_get_flexslider_bullet_itypes(): array()),
								'condition' => array( 'layout' => 'carousel', 'carousel-navigation' => array('bullet','both') ),
								'wrapper-class' => 'gdlr-core-fullsize'
							),
							'carousel-bullet-top-margin' => array(
								'title' => esc_html__('Carousel Bullet Top Margin', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'pixel',
								'condition' => array( 'layout' => 'carousel', 'carousel-navigation' => array('bullet','both') )
							),
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
				
				ob_start();
?><script type="text/javascript" id="tourmaster-preview-room-location-<?php echo esc_attr($id); ?>" >
if( document.readyState == 'complete' ){
	jQuery(document).ready(function(){
		var room_preview = jQuery('#tourmaster-preview-room-location-<?php echo esc_attr($id); ?>').parent();
		room_preview.gdlr_core_lightbox().gdlr_core_flexslider().gdlr_core_isotope();
	});
}else{
	jQuery(window).load(function(){
		setTimeout(function(){
			var room_preview = jQuery('#tourmaster-preview-room-location-<?php echo esc_attr($id); ?>').parent();
			room_preview.gdlr_core_lightbox().gdlr_core_flexslider().gdlr_core_isotope();
		}, 300);
	});
}
</script><?php	
				$content .= ob_get_contents();
				ob_end_clean();
				
				return $content;
			}			
			
			// get the content from settings
			static function get_content( $settings = array(), $preview = false ){

				// default variable
				if( empty($settings) ){
					$settings = array(
						'column-size' => '20'
					);
				}

				$extra_class = '';
				if( $settings['layout'] == 'carousel' ){
					$extra_class .= 'gdlr-core-item-pdlr';
				}

				// start printing item
				$ret  = '<div class="tourmaster-room-location-item gdlr-core-item-pdb clearfix ' . esc_attr($extra_class) . '" ';
				if( !empty($settings['padding-bottom']) && $settings['padding-bottom'] != '30px' ){
					$ret .= tourmaster_esc_style(array('padding-bottom'=>$settings['padding-bottom']));
				}
				if( !empty($settings['id']) ){
					$ret .= ' id="' . esc_attr($settings['id']) . '" ';
				}
				$ret .= ' >';
				
				// pring tour item
				$room_item = new tourmaster_room_location_item($settings);

				$ret .= $room_item->get_content();
				
				$ret .= '</div>'; // tourmaster-room-location-item
				
				return $ret;
			}			
			
		} // tourmaster_pb_element_tour
	} // class_exists	

	add_shortcode('tourmaster_room', 'tourmaster_room_shortcode');
	if( !function_exists('tourmaster_room_shortcode') ){
		function tourmaster_room_shortcode($atts){
			$atts = wp_parse_args($atts, array());
			$atts['column-size'] = empty($atts['column-size'])? 60: 60 / intval($atts['column-size']); 
			$atts['room-info'] = empty($atts['room-info'])? array(): array_map('trim', explode(',', $atts['room-info']));
			
			$ret  = '<div class="tourmaster-room-shortcode clearfix tourmaster-item-rvpdlr" >';
			$ret .= tourmaster_pb_element_room::get_content($atts);
			$ret .= '</div>';

			return $ret;
		}
	}