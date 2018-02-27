<?php

class People_Post_Type_WCREST extends Post_Type_WCREST {
	
	public $post_type = 'people';
	public $post_type_args = array(
      'public' => true,
	  'label'  => 'People',
	  'supports' => array( 'title','excerpt','thumbnail','custom-fields' ),
	  'rewrite'            => array( 'slug' => 'our-team' ),
	);
	public $fields = array(
		'OfficeStaffId' 				=> 'text',
		'_person_id' 					=> 'text',
		'_crest_id' 					=> 'text',
		'_mls_id' 						=> 'text',
		'_rfg_office_staff_id' 			=> 'text',
		'_status' 						=> 'text',
		'_office_id' 					=> 'text',
		'_position_type' 				=> 'text',
		'_position' 					=> 'text',
		'_active_since' 				=> 'text',
		'_show' 						=> 'text',
		'_first_name' 					=> 'text',
		'_middle_name' 					=> 'text',
		'_last_name' 					=> 'text',
		'_display_name' 				=> 'text',
		'_familiar_name' 				=> 'text',
		'_description' 					=> 'text',
		'_description_html' 			=> 'html',
		'_primary_email' 				=> 'text',
		'_primary_phone' 				=> 'text',
		'_office_location' 				=> 'text',
		'_phone_additional' 			=> 'text',
		'_primary_photo_url'			=> 'text',
		'_remote_photo_url'				=> 'text',
		'_primary_image_title' 			=> 'text',
		'_youtube_video_url' 			=> 'text',
		'_youtube_video_cover_img' 		=> 'text',
		'_shortcode_active_override' 	=> 'text',
		'_shortcode_closed_override' 	=> 'text',
		'_offices' 						=> 'text',
		'_bio_type'						=> 'text', 
		'_personbio'					=> 'html',
	);
	public $save_fields = array(
		'_shortcode_active_override' 	=> 'text',
		'_shortcode_closed_override' 	=> 'text',
	);
	
	/*protected function the_editor( $post, $settings ){
		
		$crest_fields = array(
			'OfficeStaffId' 			=> 'Office Staff ID',
			'_person_id' 				=> 'Person ID',
			'_crest_id' 				=> 'CREST ID',
			'_rfg_office_staff_id' 		=> 'RFG Office Staff ID',
			'_status' 					=> 'Status',
			'_office_id' 				=> 'Office ID',
			'_position_type' 			=> 'Position Type',
			'_position' 				=> 'Position',
			'_active_since' 			=> 'Active Since',
			'_show' 					=> 'Show on Web',
			'_first_name' 				=> 'First Name',
			'_middle_name' 				=> 'Middle Name',
			'_last_name' 				=> 'Last Name',
			'_display_name' 			=> 'Display Name',
			'_familiar_name' 			=> 'Familiar Name',
			'_description' 				=> 'Description',
			'_description_html' 		=> 'Description html',
			'_primary_email' 			=> 'Email',
			'_primary_phone' 			=> 'Phone',
			'_office_location' 			=> 'Office Location',
			'_phone_additional' 		=> 'Phone Additional',
			'_primary_photo_url'		=> 'Image URL',
			'_remote_photo_url'			=> 'Remote URL',
			'_primary_image_title' 		=> 'Image Title',
		);
		
		$bio_type = ( ! empty( $settings[ '_bio_type' ] ) ) ? $settings[ '_bio_type' ] : 'crest';
		
		include 'parts/editor.php';
		
	} // end the_editor*/
	
	
	public function the_content_filter( $content ){
		
		if ( is_singular( 'people' ) ){
			
			global $post;
			
			$fields = array();
			
			foreach( $this->fields as $key => $type ){
				
				$field = get_post_meta( get_the_ID(), $key . '_manual', true );
				
				if ( empty( $field ) ) {
					
					$field = get_post_meta( get_the_ID(), $key, true );
					
				} // end if
				
				$fields[ $key ] = $field;
				
			} // end foreach
			
			if ( has_post_thumbnail() ){
				
				$thumb_id = get_post_thumbnail_id();
				$thumb_url_array = wp_get_attachment_image_src( $thumb_id, 'large', true);
				$thumb_url = $thumb_url_array[0];
				
				$image = $thumb_url;
				
			} else {
				
				$image = $fields[ '_primary_photo_url' ];
				
			}// end if
			$name = $fields[ '_display_name' ];
			
			$position_low = strtolower( $fields[ '_position' ] );
			
			if ( strpos( $position_low, 'sales associate' ) !== false ){
				
				$position = 'Broker Associate';
				
			} else {
				
				$position = $fields[ '_position' ];
				
			}// end if
			
			$email = $fields[ '_primary_email' ];
			$phone = $fields[ '_primary_phone' ];
			$video = $fields[ '_youtube_video_url' ];
			$agent_id = $fields[ '_crest_id' ];
			
			$video_html = ( ! empty( $video ) ) ? wp_oembed_get( $video ) : '';
			
			$video_html = str_replace( '?feature=oembed', '?feature=oembed&autoplay=1&&rel=0', $video_html );  
			
			if ( ! $phone ){ 
				
				$phone = $fields[ '_phone_additional'];
				
			} // end if  
			
			if ( ! $image ){
				
				$image = WCRESTPLUGINURL . 'post-types/people/images/personplaceholder.gif';
				
			}
			
			if ( ! empty( $phone ) ){
				
				$phone = str_replace( '-', '', $phone );
						
					if (strpos( $phone, '.' ) === false ){
			
						$phone_array = str_split( $phone , 3 );
						
						$phone = $phone_array[0];
						
						if ( isset( $phone_array[1] ) ) $phone .= '.' .  $phone_array[1];
						if ( isset( $phone_array[2] ) ) $phone .= '.' .  $phone_array[2];
						if ( isset( $phone_array[3] ) ) $phone .= $phone_array[3];
					
					} // End if
			
			} // end if
			
			$website = $fields[ '_primary_web_url'];
			$link = get_post_permalink();
			
			$scode_active = $fields[ '_shortcode_active_override' ];
			
			if ( empty( $scode_active ) ){
				
				$scode_active = get_theme_mod('crest_profile_shortcode_1', '');
				
			} // end if
			
			$scode_closed = $fields[ '_shortcode_closed_override' ];
			
			if ( empty( $scode_closed ) ){
				
				$scode_closed = get_theme_mod('crest_profile_shortcode_2', '');
				
			} // end if
			
			$shortcode_1 = $this->replace_values( $scode_active );
			$shortcode_2 = $this->replace_values( $scode_closed );
			
			if ( $fields[ '_bio_type' ] == 'manual' ) {
				
				$content = $fields[ '_personbio' ];
				
				$content = apply_filters( 'wptexturize', $content );
				$content = apply_filters( 'convert_smilies', $content );
				$content = apply_filters( 'convert_chars', $content );
				$content = apply_filters( 'wpautop', $content );
				$content = apply_filters( 'shortcode_unautop', $content );
				$content = apply_filters( 'prepend_attachment', $content );
				
			} else if ( ! empty( $fields[ '_description_html' ] ) ) {
				
				$content = htmlspecialchars_decode(  $fields[ '_description_html' ] );
				
			}// end if
			
			$address = '';
			
				
			$office_id = $fields[ '_office_id' ]; 
			
			if ( ! empty( $office_id ) ){
				
				$office = $this->get_office( $office_id );
				
				if ( ! empty( $office ) ){
					
					$address = $office['address1'] . '<br />' . $office['city'] . ', ' . $office['state'] . ' ' . $office['zip'];
					
				} // End if
				
			} // End if
			
			ob_start();
			
			include 'parts/include-profile.php';
			
			$content_html = ob_get_clean();
			
			$content = $content_html;
			
			//remove_filter( 'the_content', array( $this , 'the_content_filter' ),99999999 );
			
		} // end if
		
		return $content;
		
	}
	
	protected function get_office( $office_id ){
		
		$office = array();
		
		$args = array(
			'post_type' => 'office',
			'status'	=> 'publish',
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
		);
		
		$office_query = new WP_Query( $args );
		
		if ( $office_query->have_posts() ){
			
			while( $office_query->have_posts() ){
				
				$office_query->the_post();
				
				$id = get_post_meta( get_the_ID(), '_office_id', true );
				
				if ( $office_id == $id ){
					
					$office = array(
						'name' => get_the_title(),
						'link' => get_post_permalink(),
						'address1' =>get_post_meta( get_the_ID(), '_address1', true ),
						'city' => get_post_meta( get_the_ID(), '_city', true ),
						'state' => get_post_meta( get_the_ID(), '_state', true ),
						'zip' => get_post_meta( get_the_ID(), '_zip', true ),
					);
					
				} // end if
				
			} // end while
			
			wp_reset_postdata();
			
		} // end if
		
		return $office;
		
	} // end get_offices
	
	
	protected function replace_values( $shortcode ){
		
		global $post;
		
		$replace = array(
			'%crest_id%' => get_post_meta( $post->ID, '_crest_id', true ),
			'%primary_email%' => get_post_meta( $post->ID, '_primary_email', true ),
			'%rfg_office_staff_id%' => get_post_meta( $post->ID, '_rfg_office_staff_id', true ),
			'%first_name%' => get_post_meta( $post->ID, '_first_name', true ),
			'%last_name%' => get_post_meta( $post->ID, '_last_name', true ),
		);
		
		foreach( $replace as $key => $value ){
			
			$shortcode = str_replace( $key, $value, $shortcode );
			
		} // end foreach
		
		return $shortcode;
		
	}
	
	
}