<?php

class Person_WCREST {
	
	public $fields = array(
		'OfficeStaffId',
		'_person_id',
		'_crest_id',
		'_mls_id',
		'_rfg_office_staff_id',
		'_status',
		'_office_id',
		'_position_type',
		'_position',
		'_active_since',
		'_show',
		'_first_name',
		'_middle_name',
		'_last_name',
		'_display_name',
		'_familiar_name',
		'_description',
		'_primary_email',
		'_primary_phone',
		'_office_location',
		'_phone_additional',
		'_description_html',
		//'_primary_photo_url',
		//'_remote_photo_url',
		'_primary_image_title',
		'_last_updated_crest',
	);
	
	public $settings = array();
	
	public $crest_settings = array();
	
	public $manual_settings = array();
	
	public function set_person_from_crest( $crest_xml ){
		
		//var_dump( $crest_xml );
		
		$this->settings['OfficeStaffId'] = $this->get_value_from_xml_unique( 'OfficeStaffId', $crest_xml );
		$this->settings['_person_id'] = $this->get_value_from_xml_unique( 'PersonID', $crest_xml );
		$this->settings['_crest_id'] = $this->get_value_from_xml_unique( 'PersonID', $crest_xml );
		$this->settings['_mls_id'] = $this->get_value_form_xml_multiple( 'PersonMLSId', $crest_xml );
		$this->settings['_rfg_office_staff_id'] = $this->get_value_from_xml_unique( 'RFGOfficeStaffID', $crest_xml );
		$this->settings['_status'] = $this->get_value_from_xml_unique( 'Status', $crest_xml );
		$this->settings['_office_id'] = $this->get_value_from_xml_unique( 'OfficeId', $crest_xml );
		$this->settings['_position_type'] = $this->get_value_from_xml_unique( 'PositionType', $crest_xml );
		$this->settings['_position'] = $this->get_value_from_xml_unique( 'PositionName', $crest_xml );
		$this->settings['_active_since'] = $this->get_value_from_xml_unique( 'ActiveSince', $crest_xml );
		$this->settings['_show'] = $this->get_value_from_xml_unique( 'IsShowOnInternet', $crest_xml );
		$this->settings['_first_name'] = $this->get_value_from_xml_unique( 'FirstName', $crest_xml );
		$this->settings['_middle_name'] = $this->get_value_from_xml_unique( 'MiddleName', $crest_xml );
		$this->settings['_last_name'] = $this->get_value_from_xml_unique( 'LastName', $crest_xml );
		$this->settings['_display_name'] = $this->get_value_from_xml_unique( 'DisplayName', $crest_xml );
		$this->settings['_familiar_name'] = $this->get_value_from_xml_unique( 'FamiliarName', $crest_xml );
		$this->settings['_description'] = $this->get_value_from_xml_nested( 'ProfileDescriptions', 'Description', $crest_xml );
		$this->settings['_description_html'] = $this->get_value_from_xml_nested( 'ProfileDescriptions', 'DescriptionInRichText', $crest_xml );
		$this->settings['_primary_email'] = $this->get_value_from_xml_nested( 'DefaultEmail', 'EmailAddress', $crest_xml );
		$this->settings['_primary_phone'] = $this->get_value_from_xml_unique( 'DefaultPhoneNumber', $crest_xml );
		$this->settings['_office_location'] = $this->get_value_from_xml_unique( 'DefaultAddress', $crest_xml );
		$this->settings['_phone_additional'] = $this->get_value_from_xml_nested( 'AdditionalPhoneNumbers','Number', $crest_xml );
		$this->settings['_primary_photo_url'] = $this->get_value_from_xml_nested( 'MediaItems','URL', $crest_xml );
		$this->settings['_remote_photo_url'] = '';
		$this->settings['_primary_image_title'] = $this->get_value_from_xml_nested( 'MediaItems','Title', $crest_xml );
		$this->settings['_last_updated_crest'] = $this->get_value_from_xml_unique( 'LastUpdateDate', $crest_xml );
		$this->settings['_post_id'] = '';
		
		return true;
		
	} // end set_person_from_crest
	
	
	public function set_person_from_wp( $post_id ){
		
		$this->settings[ '_post_id' ] = $post_id;
		
		$this->settings[ '_last_checked' ] = get_post_meta( $post_id, '_last_checked', true );
		
		foreach( $this->fields as $key ){
			
			$this->crest_settings[ $key ] = get_post_meta( $post_id, $key, true );
			
			$this->manual_settings[ $key ] = get_post_meta( $post_id, $key . '_manual', true );
			
			if ( ! empty( $this->manual_settings[ $key ] ) ){
				
				$this->settings[ $key ] = $this->manual_settings[ $key ];
				
			} else {
				
				$this->settings[ $key ] = $this->crest_settings[ $key ];
				
			} // End if
			
		} // End foreach
		
	} // End set_person_from_wp
	
	
	public function check_status( $person_id ){
		
		$exists = $this->check_person_exists( $person_id );
		
		if ( $exists ){
			
			$post_id = $exists; 
			
			$description = get_post_meta( $post_id, '_description', true );
		
			$post_object = get_post( $post_id );
			
			if ( ! empty( $description ) && empty( $post_object->post_content ) ){
				
				$people_post = array(
					  'ID'           => $post_id,
					  'post_content' => $description,
				  );
			
				  wp_update_post( $people_post );
				
			} // End if
		
			if ( 'Active' != $this->settings['_status'] || empty( $this->settings['_status'] ) ){
				
				wp_trash_post( $exists );
				
				return array(
					'status' 	=> true,
					'msg' 		=> 'Person Removed',
					'response' => $exists,
				);
				
			} else {
				
				return array(
					'status' 	=> true,
					'msg' 		=> 'Person Active',
					'response' => $exists,
				);
				
			}// End if
		
		} else {
			
			return array(
				'status' 	=> true,
				'msg' 		=> 'Person Error',
				'response' => $person_id,
			);
			
		} // End if
		
	} // End check_status
	
	
	public function create_person( $force_update = false ){
		
		//var_dump( $this->settings );
		
		$date = date("Y-m-dTH:i:s");
		
		$exists = $this->check_person_exists( $this->settings['_person_id'] );
		
		if ( ! $exists  ) {
			
			$content = htmlspecialchars_decode ( $this->settings['_description'] );
			
			//var_dump( $this->settings['_primary_photo_url'] );
			
			//$image_url = $this->upload_image( $this->settings['_primary_photo_url'] );
			
			//var_dump( $image_url );
			
			if (  ! empty( $this->settings['_status'] ) && 'Active' == $this->settings['_status'] ){
			
				$post = array(
					'post_content' => $content,
					'post_title' => $this->settings['_last_name'] . ', ' . $this->settings['_first_name'],
					'post_status' => 'publish',
					'post_type' => 'people',
					'meta_input' => $this->settings,
				);

				$post_id = wp_insert_post( $post );

				$image_url = $this->upload_image_new( $this->settings['_primary_photo_url'], $post_id );

				if ( $image_url ) {

					update_post_meta( $post_id, '_primary_photo_url', $image_url );

					update_post_meta( $post_id, '_remote_photo_url', $image_url );

				} // End if

				update_post_meta( $post_id, '_last_checked', $date );

				$positions = explode( ',', $this->settings['_position'] );

				if ( is_array( $positions ) ){

					foreach( $positions as $position ){

						$term = get_term_by( 'name', $position, 'people_position', ARRAY_A );

						if ( ! $term ) {

							$term = wp_insert_term( $position, 'people_position' );

						} // end if

						wp_set_object_terms( $post_id, $term['term_id'], 'people_position' );

					} // end foreach

				} // end if	

				return array(
					'status' 	=> true,
					'msg' 		=> 'Person created',
					'response' => $post_id,
				);
				
			} else {
				
				return array(
					'status' 	=> true,
					'msg' 		=> 'Person no status',
					'response' => $post_id,
				);
				
			}
			
		} else {
			
			update_post_meta( $exists, '_last_checked', $date );
			
			$update_text = 'Last Name:' . $this->settings['_last_name'] . $this->update_person( $exists, $force_update );
			
			return array(
				  'status' 	=> false,
				  'msg' 		=> 'Person already exists, ' . $update_text,
				  'response' => $exists,
			  );
			
			/*$post_id = $exists;
			
			$image = get_post_meta( $post_id, '_primary_photo_url', true );
			
			//var_dump( $image );
			
			if ( false === strpos( $image, 'uploads' ) ){
				
				$image_url = $this->upload_image( $image );
				
				if ( $image_url ){
					
					update_post_meta( $post_id, '_primary_photo_url', $image_url );
					
					return array(
					  'status' 	=> false,
					  'msg' 		=> 'Person already exists, Image added',
					  'response' => $exists,
				  );
					
				} else {
					
					return array(
					  'status' 	=> false,
					  'msg' 		=> 'Person already exists, Image upload failed',
					  'response' => $exists,
				  );
					
				} // End if
				
			} else {
			
				  return array(
					  'status' 	=> false,
					  'msg' 		=> 'Person already exists',
					  'response' => $exists,
				  );
			} // End if*/
			
		} // end if
		
	} // end create_person
	
	
	protected function update_person( $post_id, $force_update = false ){
		
		$updated = '';
		
		$status = get_post_meta( $post_id, '_status', true );
		
		if ( ( 'Active' !== $status ) || empty( $status ) ){
			
			wp_trash_post( $post_id );
			
			$updated .= 'Removed, ';
			
		} // End if
		
		foreach( $this->fields as $field ){
			
			$meta = get_post_meta( $post_id, $field, true );
			
			if ( ( $meta != $this->settings[ $field ] ) || $force_update ){
				
				update_post_meta( $post_id, $field, $this->settings[ $field ] );
				
				if ( '_description' === $field ){
					
					$people_post = array(
						'ID'           => $post_id,
						'post_content' => $this->settings[ $field ],
					);
				
					wp_update_post( $people_post );
					
				} // End If
				
				$updated .= $field . ', ';
				
			} // End if
			
		} // End Foreach
		
		if ( isset( $_GET['debug-description'] ) ){
			
			$people_post = array(
				'ID'           => $post_id,
				'post_content' => $this->settings[ $field ],
			);

			wp_update_post( $people_post );
			
			$updated .= 'forced - description, ';
			
		} // End if
		
		$description = get_post_meta( $post_id, '_description', true );
		
		$remote_source = get_post_meta( $post_id, '_remote_photo_url', true );
		
		$existing_image_url = get_post_meta( $post_id, '_primary_photo_url', true );
		
		if ( ! empty( $this->settings['_primary_photo_url'] ) ){
			
			if ( ( $this->settings['_primary_photo_url'] !== $remote_source ) || ! has_post_thumbnail( $post_id ) ){
				
				//$image_url = $this->upload_image( $this->settings['_primary_photo_url'], $post_id );
				
				$image_url = $this->upload_image_new( $this->settings['_primary_photo_url'], $post_id );

				update_post_meta( $post_id, '_primary_photo_url', $image_url );

				update_post_meta( $post_id, '_remote_photo_url', $this->settings['_primary_photo_url'] );

				$updated .= 'image_updated, ';
				
			} // End if
			
		}// End if
		
		/*if ( ! has_post_thumbnail( $post_id ) && ! empty( $existing_image_url ) ){
			
			if ( strpos( $existing_image_url, 'uploads') !== false ){
				
				$image_id = $this->get_image_url_id( $existing_image_url );
				
				set_post_thumbnail( $post_id, $image_id );
				
				$updated .= "Featured Image Set {$image_id}, ";
				
			} // End if
			
		} // End if*/
		 
		return $updated;
		
	} // End update_person
	
	protected function upload_image_new( $image_url, $post_id = false ){
		
		if ( !function_exists('media_handle_upload') ) {
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			require_once(ABSPATH . "wp-admin" . '/includes/file.php');
			require_once(ABSPATH . "wp-admin" . '/includes/media.php');
		}

		$url = $image_url;
		
		$tmp = download_url( $url );
		
		if( is_wp_error( $tmp ) ){
			// download failed, handle error
		}
		
		$desc = "Agent Profile Image";
		
		$file_array = array();

		// Set variables for storage
		// fix file filename for query strings
		preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $url, $matches);
		$file_array['name'] = 'person-' . $this->settings['_person_id'] . '-updated.jpg';;
		$file_array['tmp_name'] = $tmp;

		// If error storing temporarily, unlink
		if ( is_wp_error( $tmp ) ) {
			@unlink($file_array['tmp_name']);
			$file_array['tmp_name'] = '';
		}

		// do the validation and storage stuff
		$id = media_handle_sideload( $file_array, $post_id, $desc );

		// If error storing permanently, unlink
		if ( is_wp_error($id) ) {
			@unlink($file_array['tmp_name']);
			return '';
		}
		
		set_post_thumbnail( $post_id, $id );

		$src = wp_get_attachment_url( $id );
		
		return $src;
		
	}
	
	
	protected function upload_image( $image_url, $post_id = false ){
		
		$upload_directory = wp_upload_dir();
		
		$response = wp_remote_get( $image_url );
		
		$image = wp_remote_retrieve_body( $response );
		
		$file_name = 'person-' . $this->settings['_person_id'] . '-new.jpg';
		
		$file_path = $upload_directory['path'] . '/' . $file_name;
		
		$file_url = $upload_directory['url'] . '/' . $file_name;
		
		file_put_contents ( $file_path, $image );
		
		$wp_filetype = wp_check_filetype(basename( $file_name ), null );

		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => $file_name,
			'post_content' => '',
			'post_status' => 'inherit'
		);
		
		$attach_id = wp_insert_attachment( $attachment, $file_path );
		
		if ( $post_id ){
			
			set_post_thumbnail( $post_id, $attach_id );
			
		} // End if
		
		
		return $file_url;
		
		/*
		
		//var_dump( $image_url );
		
		// Gives us access to the download_url() and wp_handle_sideload() functions
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		
		$timeout_seconds = 5;
		
		// Download file to temp dir
		$temp_file = download_url( $image_url, $timeout_seconds );
		
		var_dump( $temp_file );
		
		if ( !is_wp_error( $temp_file ) ) {
		
			// Array based on $_FILE as seen in PHP file uploads
			$file = array(
				'name'     => basename( $image_url ), // ex: wp-header-logo.png
				'type'     => 'image/jpg',
				'tmp_name' => $temp_file,
				'error'    => 0,
				'size'     => filesize( $temp_file ),
			);
		
			$overrides = array(
				// Tells WordPress to not look for the POST form
				// fields that would normally be present as
				// we downloaded the file from a remote server, so there
				// will be no form fields
				// Default is true
				'test_form' => false,
		
				// Setting this to false lets WordPress allow empty files, not recommended
				// Default is true
				'test_size' => true,
			);
		
			// Move the temporary file into the uploads directory
			$results = wp_handle_sideload( $file, $overrides );
			
			var_dump( $results );
		
			if ( !empty( $results['error'] ) ) {
				// Insert any error handling here
			} else {
		
				$filename  = $results['file']; // Full path to the file
				$local_url = $results['url'];  // URL to the file in the uploads dir
				$type      = $results['type']; // MIME type of the file
				
				return $local_url;
		
				// Perform any actions here based in the above results
			}
		
		}
		
		return false;*/
		
	} // End upload_image
	
	
	public function append_office( $post_id, $office_id ){
		
		$offices = get_post_meta( $post_id, '_offices', true );
		
		$offices = explode( ',', $offices );
		
		if ( ! in_array( $office_id, $offices ) ){
			
			$offices[] = $office_id;
		
			$offices = implode( ',', $offices );
		
			update_post_meta( $post_id, '_offices', $offices );
			
		} // end if
		
	} // end append_office
	
	
	public function check_person_exists( $crest_id ){
		
		$args = array(
			'post_type' => 'people',
			'posts_per_page' => 1,
			'post_status' => 'publish',
			'meta_key' => '_person_id',
			'meta_value' => $crest_id,
		);
		
		$the_query = new WP_Query( $args );

		// The Loop
		if ( $the_query->have_posts() ) {
			
			$the_query->the_post();
			
			$post_id = $the_query->post->ID;
			
			//var_dump( $the_query->post );
			
			wp_reset_postdata();
			
			return $post_id;
			
		} else {
			
			return false;
			
		} // end if
		
	} // end check_person_exists
	
	
	private function get_value_from_xml_unique( $key, $xml ){ //'OfficeStaffId'
		
		$regex = '/<.*?:' . $key . '>(.*?)<\/.*?:' . $key . '>/';
		
		preg_match( $regex, $xml, $matches, PREG_OFFSET_CAPTURE);
		
		if ( is_array( $matches ) && ! empty( $matches[1][0] ) ){
			
			return $matches[1][0];
			
		} else {
			
			return '';
			
		}
		
	} // end get_value_from_xml_unique
	
	
	private function get_value_form_xml_multiple( $key, $xml ){
		
		$value_array = array();
		
		$regex = '/<.*?:' . $key . '>(.*?)<\/.*?:' . $key . '>/';
		
		preg_match_all( $regex, $xml, $matches, PREG_OFFSET_CAPTURE);
		
		if ( ! empty( $matches[1] ) && is_array( $matches[1] ) ) {
		
			foreach( $matches[1] as $index => $match ){
				
				if ( ! empty( $match[0] ) ){
					
					$value_array[] = $match[0];
					
				} // End if

			} // End foreach
			
		} // End if
			
		return implode(',', $value_array );
		
	} // End get_value_form_xml_multiple
	
	
	private function get_value_from_xml_nested( $parent_key, $key, $xml ){ //'OfficeStaffId'
		
		$regex = '/<.*?' . $parent_key . '.*?:' . $key . '>(.*?)<.*?:' . $key . '>/s';
		
		preg_match( $regex, $xml, $matches, PREG_OFFSET_CAPTURE);
		
		if ( is_array( $matches ) && ! empty( $matches[1][0] ) ){
			
			return $matches[1][0];
			
		} else {
			
			return '';
			
		}
		
	} // end get_value_from_xml_unique
	
}