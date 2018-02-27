<?php

class WCrest_Update_People {
	
	public function __construct(){
		
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		
		$this->check_offices();
		
		$this->check_people();
		
	} // End __construct
	
	public function check_offices(){
		
		require_once WCRESTPLUGINPATH . 'classes/class-people-wcrest.php';
		
		$people = new People_Wcrest();
		
		$people->check_office_staff();
		
	} // End check_offices
	
	public function check_people(){
		
		$msg = '';
	
		require_once WCRESTPLUGINPATH . 'classes/class-people-factory-crest.php';
		
		$people_factory = new People_Factory_Crest_People();
		
		/*
		* Get all published people from the WP
		*/
		$args = array(
			//'meta_key'  		=> '_last_checked',
  			//'orderby'   		=> 'meta_value_num',
			//'orderby'   		=> 'meta_value',
  			'order'     		=> 'ASC',
			'posts_per_page' 	=> -1,
			'cache_results'  	=> false,
			'post_status'		=> 'publish',
		);
		
		// Array of all WP people with post_id as index
		$wp_people = $people_factory->get_wp_people( $args, false );
		
		// People to be updated (we'll use this later)
		$wp_update_people = array();
		
		// Priority ids to be updated
		$wp_update_ids = array();
		
		// People Id's to check against for duplicates
		$wp_people_ids = array();
		
		// Update no matter what
		$force_update = ( ! empty( $_GET['do_force_update'] ) ) ? true:false;
		
		// We'll build the response here
		$json = array();
		
		/*
		* Loop through all people and check for duplicate crest ids.
		* Remove duplicate post if it exists
		*/
		foreach( $wp_people as $person_id => $wp_person ){
			
			// Check if in duplicate array
			if ( in_array( $wp_person->settings['_crest_id'], $wp_people_ids ) ){
				
				// If yes trash post
				//wp_trash_post( $person_id  );
				
				// And crest id to be priority updated
				$wp_update_ids[] = $wp_person->settings['_crest_id'];
				
			} else {
				
				// And id to array
				$wp_people_ids[] = $wp_person->settings['_crest_id'];
				
			} // End if
			
		} // End foreach
	
		/*
		* Reformat the array so that it uses crest id instead of post id
		*/
		foreach( $wp_people as $person_id => $wp_person ){
			
			// Set post id from wordpress
			$wp_person->settings['_post_id'] = $person_id;
			
			if ( array_key_exists( $wp_person->settings['_crest_id'], $wp_people ) ){
				
				$wp_update_people[ $wp_person->settings['_crest_id'] ] = $wp_people[ $wp_person->settings['_crest_id'] ];
				
				wp_trash_post( $person_id  );
				
				echo 'Removed Duplicate: ' . $wp_person->settings['_display_name'] . ', ' . $wp_person->settings['_last_checked'] . ', ' . $wp_person->settings['_crest_id'] . '<br/>';
				
			} else {
				
				$wp_people[ $wp_person->settings['_crest_id'] ] = $wp_person;
				
				$wp_people[ $wp_person->settings['_crest_id'] ] = $wp_person;
				
				unset( $wp_people[ $person_id ] );
				
			} // End if
			
		} // End foreach
		
		// Sort array by last checked
		uasort( $wp_people , function ($a, $b) {
			
			$f_time =  str_replace( 'UTC', ' ', $a->settings['_last_checked'] );
			
			$f_date = strtotime( $f_time);
			
			//$f_date = DateTime::createFromFormat('Y-m-d H:i:s', $f_time);
			
			$s_time =  str_replace( 'UTC', ' ', $b->settings['_last_checked'] );
			
			//$s_date = DateTime::createFromFormat('Y-m-d H:i:s', $s_time);
			
			$s_date = strtotime($s_time);
			
			
			//var_dump( $f_date );
			
			//var_dump( $a->settings['_last_checked'] );
			
				//$s_date = DateTime::createFromFormat('y-m-dTH:i:s', $b->settings['_last_checked']);
			
			//var_dump( $b->settings['_last_checked'] );
			
				return ( $f_date > $s_date ) ? 1 : -1;
			
				//return strnatcmp($a->settings['_last_checked'],$b->settings['_last_checked']); // or other function/code
			}
		);
		
		//foreach( $wp_update_people_duplicates as $dup_crest_id ){
			
			//if ( array_key_exists( $dup_crest_id, $wp_people ) ){
				
			//} // End if
			
		//} // End foreach
		
		$wp_people = array_slice ( $wp_people, 0, 5, true );
		
		$wp_update_people = array_merge( $wp_update_people, $wp_people );
		
		//foreach( $wp_update_people as $crest_id => $wp_update_person ){
			
			//echo $wp_update_person->settings['_display_name'] . ', ' .$wp_update_person->settings['_last_checked'] . ', ' . $crest_id . '<br/>';
			
		//} // End foreach
		
		
		
		//var_dump( $wp_people );
		
		foreach( $wp_update_people as $person_id => $wp_person ){
			
			echo 'Checked: ' . $wp_person->settings['_display_name'] . ', ' .$wp_person->settings['_last_checked'] . ', ' . $person_id . '<br/>';
			
			require_once WCRESTPLUGINPATH . 'classes/class-crest-wcrest.php';
			
			$crest = new Crest_WCREST();
			
			//var_dump( $wp_person->settings['_crest_id'] );
			
			$response = $crest->get_person_by_id( $wp_person->settings['_crest_id'] );
			
			
			if ( ! empty( $response['status'] ) ){
				
				require_once  WCRESTPLUGINPATH . 'classes/class-person-wcrest.php';
				
				$person = new Person_WCREST();
				
				if ( $person->set_person_from_crest( $response['response'] ) ){
					
					$person->settings['_post_id'] = $wp_person->settings['_post_id'];
					
					if ( ! empty( $person->settings['_display_name'] ) && ( $person->settings['_status'] !== 'Active' ) ){
						
						if ( ! empty ( $person->settings['_post_id'] ) ) {
							
							wp_trash_post( $person->settings['_post_id']  );
							
							$json = array( 'status' => false, 'msg' => 'Person Removed: ' . $person->settings['_display_name'] . ', ' . $person->settings['_crest_id'] );
							
						} // End if
						
						$json = array( 'status' => false, 'msg' => 'Error No Post ID: ' . $person->settings['_crest_id'] );
						
						//var_dump( $person );
						
					} else {
						
						$person_response = $person->create_person( $force_update );
						
						$json = $person_response;
						
					}// End if
					
					//$force_update = false;
					
					//$person_response = $person->create_person( $force_update );
					
					//if -- ( $person_response['response'] && ! empty( $office_id ) ){
						
						//--$person->append_office( $person_response['response'], $office_id );
						
					//} --// end if
					
					//$json = $person_response;
					
					//$json['name'] = $person->settings['_display_name'];
					
				//} else {
					
				//	$json = array( 'status' => false, 'msg' => 'Could not set person' );
					
				}// end if;
				
			} else {
				
				$json = array( 'status' => false, 'msg' => 'Invalid Response' );
				
			}
			
			$msg .= json_encode( $json );
			
		} // End foreach*/
		
			
		echo $msg;
			
		
		//echo $msg;*/
		
	} // End check_people
	
} // End WCrest_Update_People

$update_people = new WCrest_Update_People(); 