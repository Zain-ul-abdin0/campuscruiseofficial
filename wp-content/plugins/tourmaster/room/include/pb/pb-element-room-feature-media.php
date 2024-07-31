<?php
	/*	
	*	Goodlayers Item For Page Builder
	*/

	add_action('plugins_loaded', 'tourmaster_add_pb_element_room_feature_media');
	if( !function_exists('tourmaster_add_pb_element_room_feature_media') ){
		function tourmaster_add_pb_element_room_feature_media(){

			if( class_exists('gdlr_core_page_builder_element') ){
				gdlr_core_page_builder_element::add_element('room_feature_media', 'tourmaster_pb_element_room_feature_media'); 
			}
			
		}
	}
	
	if( !class_exists('tourmaster_pb_element_room_feature_media') ){
		class tourmaster_pb_element_room_feature_media{
			
			// get the element settings
			static function get_settings(){
				return array(
					'icon' => 'fa-header',
					'title' => esc_html__('Room (Single) Feature Media', 'tourmaster')
				);
			}
			
			// return the element options
			static function get_options(){
				return apply_filters('tourmaster_room_search_item_options', array(		
					'general' => array(
						'title' => esc_html__('General', 'tourmaster'),
						'options' => array(
							'padding-bottom' => array(
								'title' => esc_html__('Padding Bottom ( Item )', 'tourmaster'),
								'type' => 'text',
								'data-input-type' => 'pixel',
								'default' => '30px'
							),
						)
					),
				));
			}

			// get the preview for page builder
			static function get_preview( $settings = array() ){
				$content  = self::get_content($settings, true);
				return $content;
			}			

			// get the content from settings
			static function get_content( $settings = array(), $preview = false ){
				
				// default variable
				$settings = empty($settings)? array(): $settings;

				if( !$preview ){
					$room_option = tourmaster_get_post_meta(get_the_ID(), 'tourmaster-room-option');
					$feature_media = tourmaster_get_room_feature_media($room_option);
					if( empty($feature_media) ) return '';
				} 

				$ret  = '<div class="tourmaster-room-feature-media-item tourmaster-item-mglr tourmaster-item-pdb clearfix" ';
				if( !empty($settings['padding-bottom']) && $settings['padding-bottom'] != '30px' ){
					$ret .= tourmaster_esc_style(array('padding-bottom'=>$settings['padding-bottom']));
				}
				if( !empty($settings['id']) ){
					$ret .= ' id="' . esc_attr($settings['id']) . '" ';
				}
				$ret .= ' >';

				if( $preview ){
					$ret .= '<div class="gdlr-core-external-plugin-message">' . esc_html__('This item display inner feature media for single room. You can set the feature media at room settings area.', 'tourmaster') . '</div>';
				}else{
					$ret .= $feature_media;
				}

				$ret .= '</div>'; // tourmaster-room-title-item
				
				return $ret;
			}		

		} // tourmaster_pb_element_tour
	} // class_exists	

	if( !function_exists('tourmaster_get_room_feature_media') ){
		function tourmaster_get_room_feature_media($room_option){
			$ret = '';

			if( !empty($room_option['inner-feature-image']) && $room_option['inner-feature-image'] == 'gallery' ){
				if( !empty($room_option['inner-image-lb-gallery'][0]['id']) ){
					$thumbnail_size = empty($room_option['inner-feature-image-size'])? 'full': $room_option['inner-feature-image-size']; 
					$ret .= '<div class="tourmaster-room-single-feature-thumbnail tourmaster-item-mglr tourmaster-media-image" >';
					$ret .= tourmaster_get_image($room_option['inner-image-lb-gallery'][0]['id'], $thumbnail_size);
	
					$lb_group = 'tourmaster-single-header-gallery';
					$count = 0;
	
					$ret .= '<div class="tourmaster-single-header-gallery-wrap" >';
					foreach($room_option['inner-image-lb-gallery'] as $slider){ $count++;
						$lightbox_atts = array(
							'url' => tourmaster_get_image_url($slider['id']), 
							'group' => $lb_group
						);
	
						if( $count == 1 ){
							$lightbox_atts['class'] = 'tourmaster-single-header-gallery-button';
							$ret .= '<a ' . tourmaster_get_lightbox_atts($lightbox_atts) . ' >';
							$ret .= '<i class="gdw-icon-imagesmode" ></i>';
							$ret .= '</a>';
						}else{
							$ret .= '<a ' . tourmaster_get_lightbox_atts($lightbox_atts) . ' ></a>';
						}
						
					}
	
					if( !empty($room_option['inner-image-lb-video-url']) ){
						$ret .= '<a ' . tourmaster_get_lightbox_atts(array(
							'class' => 'tourmaster-single-header-gallery-button',
							'type' => 'video', 
							'url' => $room_option['inner-image-lb-video-url']
						)) . ' >';
						$ret .= '<i class="gdw-icon-play_arrow" ></i>';
						$ret .= '</a>';
					}
	
					$ret .= '</div>';
					$ret .= '</div>';
				}
			}

			return $ret;
		}
	}