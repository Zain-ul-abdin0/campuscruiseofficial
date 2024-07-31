<?php
	/*	
	*	Utility function for uses
	*/
	if( !function_exists('tourmaster_room_advance_review_text') ){
		function tourmaster_room_advance_review_text( $score ){
			if( $score < 1 ){
				return esc_html__('Terrible', 'tourmaster');
			}else if( $score < 2 ){
				return esc_html__('Poor', 'tourmaster');
			}else if( $score < 3 ){
				return esc_html__('Fair', 'tourmaster');
			}else if( $score < 3.5 ){
				return esc_html__('Okay', 'tourmaster');
			}else if( $score < 4 ){
				return esc_html__('Good', 'tourmaster');
			}else if( $score < 4.5 ){
				return esc_html__('Very Good', 'tourmaster');
			}
			
			return esc_html__('Superb', 'tourmaster');
		}
	}

	// advance review form
	if( !function_exists('tourmaster_room_review_form_rating') ){
		function tourmaster_room_review_form_rating( $settings, $rating_value ){

			echo '<div class="tourmaster-review-form-item clearfix ';
			echo empty($settings['wrapper_class'])? '': $settings['wrapper_class'];
			echo '" >';
			echo '<div class="tourmaster-head" >' . $settings['label'] . '</div>';
			echo '<div class="tourmaster-review-form-rating clearfix" >';
			for( $i = 1; $i <= 10; $i++ ){
				if( $i % 2 == 0 ){
					echo '<span class="tourmaster-rating-select" data-rating-score="' . esc_attr($i) . '" ></span>';
				}else{
					echo '<i class="tourmaster-rating-select ';
					if( $rating_value == $i ){
						echo 'fa fa-star-half-empty';
					}else if( $rating_value > $i ){
						echo 'fa fa-star';
					}else{
						echo 'fa fa-star-o';
					}
					echo '" data-rating-score="' . esc_attr($i) . '" ></i>';
				}
			}

			echo '<input type="hidden" name="' . esc_attr($settings['slug']) . '" value="' . esc_attr($rating_value) . '" />';
			echo '</div>';
			echo '</div>';
		}
	}
	if( !function_exists('tourmaster_room_advance_review_fields') ){
		function tourmaster_room_advance_review_fields(){
			return array(
				'cleanliness' => esc_html__('Cleanliness', 'tourmaster'),
				'comfort' => esc_html__('Comfort', 'tourmaster'),
				'service' => esc_html__('Staff & Service', 'tourmaster'),
				'location' => esc_html__('Location', 'tourmaster'),
				'facilities' => esc_html__('Property Condition & Facilities', 'tourmaster'),
				'value' => esc_html__('Value For Money', 'tourmaster'),
			);
		}
	}
	if( !function_exists('tourmaster_room_get_advance_review_form') ){
		function tourmaster_room_get_advance_review_form( $result, $is_admin = false, $value = array() ){

			ob_start();
?>

<?php if( $is_admin ){ ?>
<div class="tourmaster-review-form tourmaster-form-field tourmaster-with-border" >
	<?php if( $is_admin === true ){ ?>
		<div class="tourmaster-review-form-name" >
			<span class="tourmaster-head" ><?php echo esc_html__('Reviewer Name', 'tourmaster'); ?></span>
			<input type="text" name="review-name" />
		</div>
		<div class="tourmaster-review-form-email" >	
			<span class="tourmaster-head" ><?php echo esc_html__('Reviewer Email (For Gravatar Profile Picture)', 'tourmaster'); ?></span>
			<input type="text" name="review-email" />
		</div>
	<?php } ?>
<?php }else{ ?> 
<form class="tourmaster-advance-review-form tourmaster-form-field tourmaster-with-border" method="POST" >
<?php } ?>
	<?php	
		$review_details = empty($value['review_detail'])? array(): json_decode($value['review_detail'], true);
		$review_fields = tourmaster_room_advance_review_fields();
		foreach($review_fields as $review_slug => $review_label){
			if( empty($review_details) ){
				$rating_value = empty($value['rating'])? 10: intval($value['rating']);
			}else{
				$rating_value = isset($review_details[$review_slug])? intval($review_details[$review_slug]): 10;
			}
			
			tourmaster_room_review_form_rating(array(
				'label' => $review_label,
				'slug' => $review_slug,
				'wrapper_class' => 'tourmaster-column-30'
			), $rating_value);
		}
	?>
	<div class="clear"></div>
	<div class="tourmaster-review-form-description" >
		<div class="tourmaster-head" ><?php echo esc_html__('What do you say about this stay? *', 'tourmaster'); ?></div>
		<textarea name="description" ><?php echo empty($value['description'])? '': esc_textarea($value['description']); ?></textarea>
	</div>
<?php if( $is_admin ){ ?>	
	<div class="tourmaster-review-form-date" >	
		<span class="tourmaster-head" ><?php echo esc_html__('Published Date', 'tourmaster'); ?></span>
		<input type="text" class="tourmaster-html-option-datepicker" name="review-published-date" value="<?php
			if( !empty($value['published-date']) ){
				echo esc_attr(date('Y-m-d', strtotime($value['published-date'])));
			}
		?>" />
		<input type="hidden" name="room_id" value="<?php 
			$room_id = get_the_ID();
			if( empty($room_id) && !empty($value['room_id']) ){
				$room_id = $value['room_id'];
			} 
			echo esc_attr($room_id); 
		?>" />
		<?php if( $is_admin === true ){ ?>
			<input type="hidden" name="review_action" value="tourmaster_admin_add_review" />
		<?php }else{ ?>
			<input type="hidden" name="review_action" value="tourmaster_admin_edit_review" />
			<input type="hidden" name="review_id" value="<?php echo esc_attr($value['review-id']); ?>" />
		<?php } ?>
	</div>
	<input class="tourmaster-button tourmaster-submit-review" data-ajax-url="<?php echo esc_attr(TOURMASTER_AJAX_URL); ?>" type="button" value="<?php echo esc_html__('Submit Review', 'tourmaster'); ?>" />
</div>
<?php }else{ ?>
	<input type="hidden" name="review_id" value="<?php echo esc_attr($result->id); ?>" />
	<input type="hidden" name="order_id" value="<?php echo esc_attr($result->order_id); ?>" />
	<input class="tourmaster-button" type="submit" value="<?php echo esc_html__('Submit Review', 'tourmaster'); ?>" />
</form>
<?php }
			$ret = ob_get_contents();
			ob_end_clean();

			return $ret;
		}
	}

	// review form
	if( !function_exists('tourmaster_room_get_review_form') ){
		function tourmaster_room_get_review_form( $result, $is_admin = false, $value = array() ){

			$advance_review = tourmaster_get_option('room_general', 'enable-advance-review-submission', 'disable');
			if( $advance_review == 'enable' ){
				return tourmaster_room_get_advance_review_form($result, $is_admin, $value);
			}

			ob_start();
?>

<?php if( $is_admin ){ ?>
<div class="tourmaster-review-form tourmaster-form-field tourmaster-with-border" >
	<?php if( $is_admin === true ){ ?>
		<div class="tourmaster-review-form-name" >
			<span class="tourmaster-head" ><?php echo esc_html__('Reviewer Name', 'tourmaster'); ?></span>
			<input type="text" name="review-name" />
		</div>
		<div class="tourmaster-review-form-email" >	
			<span class="tourmaster-head" ><?php echo esc_html__('Reviewer Email (For Gravatar Profile Picture)', 'tourmaster'); ?></span>
			<input type="text" name="review-email" />
		</div>
	<?php } ?>
<?php }else{ ?> 
<form class="tourmaster-review-form tourmaster-form-field tourmaster-with-border" method="POST" >
<?php } ?>
	<div class="tourmaster-review-form-description" >
		<div class="tourmaster-head" ><?php echo esc_html__('What do you say about this room? *', 'tourmaster'); ?></div>
		<textarea name="description" ><?php echo empty($value['description'])? '': esc_textarea($value['description']); ?></textarea>
	</div>
	<div class="tourmaster-review-form-rating-wrap" >
		<div class="tourmaster-head" ><?php echo esc_html__('Rate this room *', 'tourmaster'); ?></div>
		<div class="tourmaster-review-form-rating clearfix" >
		<?php
			$rating_value = empty($value['rating'])? 10: intval($value['rating']);

			for( $i = 1; $i <= 10; $i++ ){
				if( $i % 2 == 0 ){
					echo '<span class="tourmaster-rating-select" data-rating-score="' . esc_attr($i) . '" ></span>';
				}else{
					echo '<i class="tourmaster-rating-select ';
					if( $rating_value == $i ){
						echo 'fa fa-star-half-empty';
					}else if( $rating_value > $i ){
						echo 'fa fa-star';
					}else{
						echo 'fa fa-star-o';
					}
					echo '" data-rating-score="' . esc_attr($i) . '" ></i>';
				}
			}

			echo '<input type="hidden" name="rating" value="' . esc_attr($rating_value) . '" />';
		?>
		</div>
	</div>
<?php if( $is_admin ){ ?>	
	<div class="tourmaster-review-form-date" >	
		<span class="tourmaster-head" ><?php echo esc_html__('Published Date', 'tourmaster'); ?></span>
		<input type="text" class="tourmaster-html-option-datepicker" name="review-published-date" value="<?php
			if( !empty($value['published-date']) ){
				echo esc_attr(date('Y-m-d', strtotime($value['published-date'])));
			}
		?>" />
		<input type="hidden" name="room_id" value="<?php 
			$room_id = get_the_ID();
			if( empty($room_id) && !empty($value['room_id']) ){
				$room_id = $value['room_id'];
			} 
			echo esc_attr($room_id); 
		?>" />
		<?php if( $is_admin === true ){ ?>
			<input type="hidden" name="review_action" value="tourmaster_admin_add_review" />
		<?php }else{ ?>
			<input type="hidden" name="review_action" value="tourmaster_admin_edit_review" />
			<input type="hidden" name="review_id" value="<?php echo esc_attr($value['review-id']); ?>" />
		<?php } ?>
	</div>
	<input class="tourmaster-button tourmaster-submit-review" data-ajax-url="<?php echo esc_attr(TOURMASTER_AJAX_URL); ?>" type="button" value="<?php echo esc_html__('Submit Review', 'tourmaster'); ?>" />
</div>
<?php }else{ ?>
	<input type="hidden" name="review_id" value="<?php echo esc_attr($result->id); ?>" />
	<input type="hidden" name="order_id" value="<?php echo esc_attr($result->order_id); ?>" />
	<input class="tourmaster-button" type="submit" value="<?php echo esc_html__('Submit Review', 'tourmaster'); ?>" />
</form>
<?php }
			$ret = ob_get_contents();
			ob_end_clean();

			return $ret;
		}
	}

	////////////////////////////// admin review ////////////////////////////
	
	if( !function_exists('tourmaster_room_admin_add_review') ){
		function tourmaster_room_admin_add_review( $data = array() ){

			if( !empty($data['review-name']) && !empty($data['review-email']) && !empty($data['room_id']) && 
				!empty($data['review-published-date']) && !empty($data['description']) ){

				if( is_email($data['review-email']) ){
					global $wpdb;

					$rating_score = empty($data['rating'])? 0: $data['rating'];
					$review_details = array();
					$review_fields = tourmaster_room_advance_review_fields();
					foreach($review_fields as $review_slug => $review_label){
						if( !empty($data[$review_slug]) ){
							$review_details[$review_slug] = $data[$review_slug];
							$rating_score += $data[$review_slug];
						}
					}
					if( !empty($review_details) ){
						$rating_score = $rating_score / sizeof($review_details);
					}

					$wpdb->insert("{$wpdb->prefix}tourmaster_room_review" ,array(
						'review_room_id' => $data['room_id'],
						'reviewer_name' => $data['review-name'],
						'reviewer_email' => $data['review-email'],
						'review_date' => $data['review-published-date'],
						'review_description' => $data['description'],
						'review_score' => $rating_score,
						'review_detail' => json_encode($review_details)
					), array(
						'%d', '%s', '%s', '%s', '%s', '%d', '%s'
					));

					tourmaster_room_update_review_score($data['room_id']);

					$ret = json_encode(array(
						'status' => 'success',
						'message' => esc_html__('A review is successfully added.', 'tourmaster')
					));
				}else{
					$ret = json_encode(array(
						'status' => 'failed',
						'message' => esc_html__('Invalid Email, please try again.', 'tourmaster')
					));
				}
			}else{
				$ret = json_encode(array(
					'status' => 'failed',
					'message' => esc_html__('Please fill all required fields.', 'tourmaster')
				));
			}

			return $ret;
		}
	}

	// review content
	if( !function_exists('tourmaster_room_get_edit_admin_review_item') ){
		function tourmaster_room_get_edit_admin_review_item( $review_id ){

			global $wpdb;
			$sql  = "SELECT * FROM {$wpdb->prefix}tourmaster_room_review ";
			$sql .= $wpdb->prepare("WHERE review_id = %d ", $review_id);
			$result = $wpdb->get_row($sql);

			$value = array(
				'room_id' => $result->review_room_id,
				'review-id' => $result->review_id,
				'description' => $result->review_description,
				'review_detail' => $result->review_detail,
				'rating' => $result->review_score,
				'published-date' => $result->review_date,
			);

			return array(
				'status' => 'success',
				'content' => tourmaster_admin_lightbox_content(array(
					'title' => esc_html__('Edit Review', 'tourmaster'),
					'content' => '<div class="tourmaster-html-option-admin-review" >' . tourmaster_room_get_review_form(null, 'edit', $value) . '</div>'
				))
			);
			
		}
	}
			
	if( !function_exists('tourmaster_room_admin_edit_review') ){
		function tourmaster_room_admin_edit_review( $post_data ){
			if( !empty($post_data['review-published-date']) && !empty($post_data['description']) ){
				global $wpdb;

				$rating_score = empty($post_data['rating'])? 0: $post_data['rating'];
				$review_details = array();
				$review_fields = tourmaster_room_advance_review_fields();
				foreach($review_fields as $review_slug => $review_label){
					if( !empty($post_data[$review_slug]) ){
						$review_details[$review_slug] = $post_data[$review_slug];
						$rating_score += $post_data[$review_slug];
					}
				}
				if( !empty($review_details) ){
					$rating_score = $rating_score / sizeof($review_details);
				}

				$updated = $wpdb->update("{$wpdb->prefix}tourmaster_room_review", array(
					'review_score' => $rating_score,
					'review_description' => $post_data['description'],
					'review_date' => $post_data['review-published-date'] . ' 00:00:00',
					'review_detail' => json_encode($review_details)
				), array('review_id' => $post_data['review_id']), array('%d', '%s', '%s', '%s'), array('%d'));

				tourmaster_room_update_review_score($data['room_id']);
				
				if( $updated !== false ){
					$ret = array(
						'status' => 'success',
					);
				}else{
					$ret = array(
						'status' => 'failed',
						'message' => esc_html__('Cannot update review data, please refresh the page and try this again.', 'tourmaster')
					);
				}
			}else{
				$ret = array(
					'status' => 'failed',
					'message' => esc_html__('Please fill all required fields.', 'tourmaster')
				);
			}

			return $ret;
		}
	}

	// review content
	if( !function_exists('tourmaster_room_get_admin_review_item') ){
		function tourmaster_room_get_admin_review_item( $room_id, $paged = 1 ){

			global $wpdb;

			$review_num_fetch = apply_filters('tourmaster_review_num_fetch', 5);

			$sql  = "SELECT * FROM {$wpdb->prefix}tourmaster_room_review ";
			$sql .= $wpdb->prepare("WHERE review_room_id = %d ", $room_id);
			$sql .= "AND order_id IS NULL ";
			$sql .= tourmaster_get_sql_page_part($paged, $review_num_fetch);
			$results = $wpdb->get_results($sql);

			if( !empty($results) ){

				$sql  = "SELECT COUNT(*) FROM {$wpdb->prefix}tourmaster_room_review ";
				$sql .= $wpdb->prepare("WHERE review_room_id = %d ", $room_id);
				$sql .= "AND order_id IS NULL ";
				$max_num_page = $wpdb->get_var($sql) / $review_num_fetch;

				$ret = array(
					'status' => 'success', 
					'content' => tourmaster_room_get_review_content_list($results, true) .
						tourmaster_room_get_review_content_pagination($max_num_page, $paged)
				);
			}else{
				$ret = array(
					'status' => 'failed',
					'message' => esc_html__('No result found, please refresh the page to try again.', 'tourmaster')
				);
			}

			return $ret;

		}
	}

	// review content
	if( !function_exists('tourmaster_room_remove_review_data') ){
		function tourmaster_room_remove_review_data( $review_id, $room_id ){
			
			global $wpdb;

			$wpdb->delete("{$wpdb->prefix}tourmaster_room_review", array('review_id' => $review_id), array('%d'));

			tourmaster_room_update_review_score($room_id);
		}
	}

	// review content
	if( !function_exists('tourmaster_room_get_review_content_list') ){
		function tourmaster_room_get_review_content_list( $query, $editable = false ){
			
			$ret  = '';
			foreach( $query as $result ){
				
				$user_id = '';
				$avatar = '';
				if( !empty($result->user_id) ){
					$user_id = $result->user_id;
					$avatar = get_the_author_meta('tourmaster-user-avatar', $user_id);
				}else if( !empty($result->reviewer_email) ){
					$user_id = $result->reviewer_email;
				}

				$reviewer_name = '';
				if( !empty($result->user_id) ){
					$reviewer_name = tourmaster_get_user_meta($result->user_id);
				}else if( !empty($result->reviewer_name) ){
					$reviewer_name = $result->reviewer_name;
				}

				$ret .= '<div class="tourmaster-single-review-content-item clearfix" >';
				$ret .= '<div class="tourmaster-single-review-user clearfix" >';
				if( !empty($user_id) ){
					$ret .= '<div class="tourmaster-single-review-avatar tourmaster-media-image" >';
					if( !empty($avatar['thumbnail']) ){
						$ret .= '<img src="' . esc_url($avatar['thumbnail']) . '" alt="profile-image" />';
					}else if( !empty($avatar['file_url']) ){
						$ret .= '<img src="' . esc_url($avatar['file_url']) . '" alt="profile-image" />';
					}else{
						$ret .= get_avatar($user_id, 90);
					}
					$ret .= '</div>'; 
				}
				$ret .= '<h4 class="tourmaster-single-review-user-name" >' . $reviewer_name . '</h4>';
				$ret .= '</div>'; // tourmaster-single-review-user

				$ret .= '<div class="tourmaster-single-review-detail" >';
				if( !empty($result->review_description) ){
					$ret .= '<div class="tourmaster-single-review-detail-description" >' . tourmaster_content_filter($result->review_description) . '</div>';
				}
				$ret .= '<div class="tourmaster-single-review-detail-rating" >' . tourmaster_get_rating($result->review_score) . '</div>';
				$ret .= '<div class="tourmaster-single-review-detail-date" >' . tourmaster_date_format($result->review_date) . '</div>';
				
				if( $editable ){
					$ret .= '<div class="tourmaster-single-review-editable" >';
					$ret .= '<div class="tourmaster-single-review-edit" data-id="' . esc_attr($result->review_id) . '" ><i class="fa fa-edit" ></i>' . esc_html__('Edit', 'tourmaster') . '</div>';
					$ret .= '<div class="tourmaster-single-review-remove" data-id="' . esc_attr($result->review_id) . '" ><i class="fa fa-remove" ></i>' . esc_html__('Remove', 'tourmaster') . '</div>';
					$ret .= '</div>';
				}
				$ret .= '</div>'; // tourmaster-single-review-detail
				$ret .= '</div>'; // tourmaster-single-review-content-item
			}

			return $ret;
		} // tourmaster_get_review_content_list
	}
	if( !function_exists('tourmaster_room_get_review_content_pagination') ){
		function tourmaster_room_get_review_content_pagination( $max_num_page, $current_page = 1 ){

			$ret = '';
			if( !empty($max_num_page) && $max_num_page > 1 ){
				$max_num_page = ceil($max_num_page);

				$ret .= '<div class="tourmaster-review-content-pagination" >';
				if( $current_page > 1 ){
					$ret .= '<span data-paged="' . esc_attr($current_page-1) . '" ><i class="fa fa-angle-left" ></i></span>';
				}
				for( $i = 1; $i <= $max_num_page; $i++ ){
					if( $i == $current_page ){
						$ret .= '<span class="tourmaster-active" >' . $i . '</span>';
					}else if( $i == 1 || $i == $max_num_page ){
						$ret .= '<span data-paged="' . esc_attr($i) . '" >' . $i . '</span>';
					}else if( $i >= $current_page - 1 && $i <= $current_page + 1 ){
						if( $current_page > 1 + 2 && $i == $current_page - 1 && 1 <= $current_page - 1 ){
							$ret .= '<span class="dots">...</span>';
						}
						$ret .= '<span data-paged="' . esc_attr($i) . '" >' . $i . '</span>';
						if( $current_page < $max_num_page - 2 && $i == $current_page + 1 && $max_num_page > $current_page + 1 ){
							$ret .= '<span class="dots">...</span>';
						}
					}
				}
				if( $current_page < $max_num_page ){
					$ret .= '<span data-paged="' . esc_attr($current_page+1) . '" ><i class="fa fa-angle-right" ></i></span>';
				}
				$ret .= '</div>';
			}

			return $ret;
		} // tourmaster_get_review_content_pagination
	}

	////////////////////////////// customer review ////////////////////////////

	// ajax review list
	add_action('wp_ajax_get_single_room_review', 'tourmaster_get_single_room_review');
	add_action('wp_ajax_nopriv_get_single_room_review', 'tourmaster_get_single_room_review');
	if( !function_exists('tourmaster_get_single_room_review') ){
		function tourmaster_get_single_room_review(){

			// sort_by
			// filter_by
			if( !empty($_POST['room_id']) ){

				$review_num_fetch = apply_filters('tourmaster_review_num_fetch', 5);
				$paged = (empty($_POST['paged'])? '1': $_POST['paged']);

				global $wpdb;
				$sql  = "SELECT * from {$wpdb->prefix}tourmaster_room_review ";
				$sql .= $wpdb->prepare("WHERE review_room_id = %d ", $_POST['room_id']);
				if( empty($_POST['sort_by']) || $_POST['sort_by'] == 'date' ){
					$sql .= "ORDER BY review_date DESC ";
				}else if( $_POST['sort_by'] == 'rating' ){
					$sql .= "ORDER BY review_score DESC ";
				}
				$sql .= tourmaster_get_sql_page_part($paged, $review_num_fetch);
				$results = $wpdb->get_results($sql);

				$sql  = "SELECT COUNT(*) from {$wpdb->prefix}tourmaster_room_review ";
				$sql .= $wpdb->prepare("WHERE review_room_id = %d ", $_POST['room_id']);
				$max_num_page = intval($wpdb->get_var($sql)) / $review_num_fetch;

				die(json_encode(array(
					'content' => tourmaster_room_get_review_content_list($results) .
					tourmaster_room_get_review_content_pagination($max_num_page, $paged)
				)));
			}

			die(json_encode(array()));

		} // tourmaster_get_single_tour_review
	}

	if( !function_exists('tourmaster_room_get_submitted_advance_review') ){
		function tourmaster_room_get_submitted_advance_review( $result ){
			ob_start();

			$review_details = json_decode($result->review_detail, true);
			$review_fields = tourmaster_room_advance_review_fields();
?>
<div class="tourmaster-review-form" >
	<?php 
		if( !empty($review_details) ){ 
			foreach($review_fields as $review_slug => $review_label){
				echo '<div class="tourmaster-review-form-rating-wrap tourmaster-column-30" >';
				echo '<div class="tourmaster-head" >' . $review_label . '</div>';
				echo '<div class="tourmaster-review-form-rating clearfix" >';
				echo tourmaster_get_rating($review_details[$review_slug]);
				echo '</div>';
				echo '</div>';
			}
		} 

		echo '<div class="clear"></div>';
	?>
	<?php if( !empty($result->review_description) ){ ?>
		<div class="tourmaster-review-form-description" >
			<div class="tourmaster-head" ><?php echo esc_html__('What do you say about this tour? *', 'tourmaster'); ?></div>
			<div class="tourmaster-tail"><?php echo tourmaster_content_filter($result->review_description); ?></div>
		</div>
	<?php } ?>	
</div>
<?php			
			$ret = ob_get_contents();
			ob_end_clean();

			return $ret;
		}
	}
	if( !function_exists('tourmaster_room_get_submitted_review') ){
		function tourmaster_room_get_submitted_review( $result ){
			if( !empty($result->review_detail) && $result->review_detail != '[]' ){
				return tourmaster_room_get_submitted_advance_review($result);
			}

			ob_start();
?>
<div class="tourmaster-review-form" >
	<?php if( !empty($result->review_description) ){ ?>
		<div class="tourmaster-review-form-description" >
			<div class="tourmaster-head" ><?php echo esc_html__('What do you say about this tour? *', 'tourmaster'); ?></div>
			<div class="tourmaster-tail"><?php echo tourmaster_content_filter($result->review_description); ?></div>
		</div>
	<?php } ?>
	<?php if( !empty($result->review_score) ){ ?>
		<div class="tourmaster-review-form-rating-wrap" >
			<div class="tourmaster-head" ><?php echo esc_html__('Rate this tour *', 'tourmaster'); ?></div>
			<div class="tourmaster-review-form-rating clearfix" >	
			<?php
				$score = intval($result->review_score);
				echo tourmaster_get_rating($score);
			?>
			</div>
		</div>
	<?php } ?>
</div>
<?php			
			$ret = ob_get_contents();
			ob_end_clean();

			return $ret;
		}
	} // tourmaster_get_submitted_review
	if( !function_exists('tourmaster_room_update_review_score') ){
		function tourmaster_room_update_review_score( $room_id ){

			global $wpdb;

			$sql  = "SELECT * from {$wpdb->prefix}tourmaster_room_review ";
			$sql .= $wpdb->prepare("WHERE review_room_id = %d ", $room_id);
			$results = $wpdb->get_results($sql);

			$review_details = array();
			$review_fields = tourmaster_room_advance_review_fields();
			foreach($review_fields as $review_slug => $review_label){
				$review_details[$review_slug] = 0;
			}

		    $review_score = 0;
		    $review_number = 0;
		    foreach( $results as $result ){
		    	if( $result->review_score != '' ){
		    		$review_score += $result->review_score;
		    		
					if( empty($result->review_detail) || $result->review_detail == '[]' ){
						foreach($review_fields as $review_slug => $review_label){
							$review_details[$review_slug] += $result->review_score;
						}
					}else{
						$details = json_decode($result->review_detail, true);
						foreach($review_fields as $review_slug => $review_label){
							$review_details[$review_slug] += isset($details[$review_slug])? $details[$review_slug]: $result->review_score;
						}
					}

					$review_number++;
		    	}
		    }

		    update_post_meta($room_id, 'tourmaster-room-rating', array(
		    	'score' => $review_score,
		    	'reviewer' => $review_number,
				'detail' => $review_details
		    ));

		    if( $review_number > 0 ){
		    	update_post_meta($room_id, 'tourmaster-room-rating-score', $review_score / $review_number);
		    }else{
		    	delete_post_meta($room_id, 'tourmaster-room-rating-score');
		    }

			// check location
			tourmaster_update_site_review();
			tourmaster_update_location_review($room_id);

		} // tourmaster_update_review_score
	}

	function tourmaster_update_location_review($room_id){
		global $wpdb;

		$terms = get_the_terms($room_id, 'room_location');
		$review_fields = tourmaster_room_advance_review_fields();

		if( !empty($terms) ){
			// foreach term
			foreach($terms as $term){
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
				$results = $wpdb->get_results($sql);

				$review_details = array();
				foreach($review_fields as $review_slug => $review_label){
					$review_details[$review_slug] = 0;
				}
				
				$review_score = 0;
		    	$review_number = 0;
				$processed_order = array();
				foreach( $results as $result ){

					// skip process with same order
					if( !empty($result->order_id) ){
						if( empty($processed_order[$result->order_id]) ){
							$processed_order[$result->order_id] = 1; 
						}else{
							continue;
						}
					}
					
					// add to score
					if( $result->review_score != '' ){
						$review_score += $result->review_score;
						
						if( empty($result->review_detail) || $result->review_detail == '[]' ){
							foreach($review_fields as $review_slug => $review_label){
								$review_details[$review_slug] += $result->review_score;
							}
						}else{
							$details = json_decode($result->review_detail, true);
							foreach($review_fields as $review_slug => $review_label){
								$review_details[$review_slug] += isset($details[$review_slug])? $details[$review_slug]: $result->review_score;
							}
						}

						$review_number++;
					}
				}

				// location rating
				update_term_meta($term->term_id, 'location-rating', array(
					'score' => $review_score,
					'reviewer' => $review_number,
					'detail' => $review_details
				));
			}
			
			wp_reset_query();
		}
	}
	if( !function_exists('tourmaster_update_site_review') ){
		function tourmaster_update_site_review(){
			global $wpdb;

			$review_fields = tourmaster_room_advance_review_fields();

			// get review for this location
			$sql  = "SELECT * from {$wpdb->prefix}tourmaster_room_review ";
			$results = $wpdb->get_results($sql);

			$review_details = array();
			foreach($review_fields as $review_slug => $review_label){
				$review_details[$review_slug] = 0;
			}
			
			$review_score = 0;
			$review_number = 0;
			$processed_order = array();
			foreach( $results as $result ){

				// skip process with same order
				if( !empty($result->order_id) ){
					if( empty($processed_order[$result->order_id]) ){
						$processed_order[$result->order_id] = 1; 
					}else{
						continue;
					}
				}
				
				// add to score
				if( $result->review_score != '' ){
					$review_score += $result->review_score;
					
					if( empty($result->review_detail) || $result->review_detail == '[]' ){
						foreach($review_fields as $review_slug => $review_label){
							$review_details[$review_slug] += $result->review_score;
						}
					}else{
						$details = json_decode($result->review_detail, true);
						foreach($review_fields as $review_slug => $review_label){
							$review_details[$review_slug] += isset($details[$review_slug])? $details[$review_slug]: $result->review_score;
						}
					}

					$review_number++;
				}
			}

			// location rating
			update_option('tourmaster-room-rating', array(
				'score' => $review_score,
				'reviewer' => $review_number,
				'detail' => $review_details
			));
		}
	}

	// advance review display
	if( !function_exists('tourmaster_review_content_item') ){
		function tourmaster_review_content_item($results, $max_num_page, $advance_review, $echo = true){

			if( !$echo ){
				ob_start();
			}

			echo '<div class="tourmaster-single-review ';
			echo ( $advance_review == 'enable' )? 'tourmaster-advance-review-style': '';
			echo '" id="tourmaster-room-single-review" >';

			echo '<div class="tourmaster-single-review-head clearfix" >';
			echo '<div class="tourmaster-single-review-head-info" >';
			$room_style = new tourmaster_room_style();
			echo $room_style->get_rating(array());

			echo '<div class="tourmaster-single-review-filter" id="tourmaster-single-review-filter" >';
			echo '<div class="tourmaster-single-review-sort-by" >';
			echo '<span class="tourmaster-head" >' . esc_html__('Sort By:', 'tourmaster') . '</span>';
			echo '<span class="tourmaster-sort-by-field" data-sort-by="rating" >' . esc_html__('Rating', 'tourmaster') . '</span>';
			echo '<span class="tourmaster-sort-by-field tourmaster-active" data-sort-by="date" >' . esc_html__('Date', 'tourmaster') . '</span>';
			echo '</div>'; // tourmaster-single-review-sort-by
			echo '</div>'; // tourmaster-single-review-filter
			echo '</div>'; // tourmaster-single-review-head-info
			echo '</div>'; // tourmaster-single-review-head

			echo '<div class="tourmaster-single-review-content" id="tourmaster-single-review-content" ';
			echo 'data-room-id="' . esc_attr(get_the_ID()) . '" ';
			echo 'data-ajax-url="' . esc_attr(TOURMASTER_AJAX_URL) . '" >';
			echo tourmaster_room_get_review_content_list($results);

			echo tourmaster_room_get_review_content_pagination($max_num_page);
			echo '</div>'; // tourmaster-single-review-content
			echo '</div>'; // tourmaster-single-review

			if( !$echo ){
				$ret = ob_get_contents();
				ob_end_clean();
				return $ret;
			}

		}
	}
	if( !function_exists('tourmaster_advance_review_sidebar') ){
		function tourmaster_advance_review_sidebar($room_rating = array(), $echo = true){
			$review_fields = tourmaster_room_advance_review_fields();

			if( !$echo ){
				ob_start();
			}

			if( !empty($room_rating['score']) && !empty($room_rating['reviewer']) ){
				$rating_score = intval($room_rating['score']) / 2 / intval($room_rating['reviewer']);
				$decimal_digit = (floor($rating_score) == $rating_score)? 0: 1;
				echo '<div class="tourmaster-advance-review-sidebar clearfix" >';
				echo '<div class="tourmaster-advance-review-head" >';
				echo '<span class="tourmaster-head" >' . number_format($rating_score, $decimal_digit) . '/5</span>';
				echo '<span class="tourmaster-tail" >' . sprintf(esc_html__('%d reviews', 'tourmaster'), $room_rating['reviewer']) . '</span>';
				echo '</div>';
				foreach( $review_fields as $review_slug => $review_label ){
					$rating = empty($room_rating['detail'][$review_slug])? $room_rating['score']: $room_rating['detail'][$review_slug];
					$rating = number_format(intval($rating) / 2 / intval($room_rating['reviewer']), 1);

					echo '<div class="tourmaster-advance-review-detail" >';
					echo '<div class="tourmaster-head" >' . $review_label . '</div>';
					echo '<div class="tourmaster-tail" >' . $rating . '</div>';
					echo '<div class="tourmaster-progress" ><span ' . tourmaster_esc_style(array(
						'width' => (floatval($rating) * 20) . '%'
					)) . ' ></span></div>';
					echo '</div>';
				}
				echo '</div>';
			}

			if( !$echo ){
				$ret = ob_get_contents();
				ob_end_clean();
				return $ret;
			}
		}
	}