<?php

class People_Wcrest {
	
	public function check_office_staff( $office_id = false ){
		
		$office_staff_ids = $this->get_office_staff_ids( $office_id );
		
		$wp_people_ids = $this->get_wp_people_ids();
		
		//var_dump( $wp_people_ids );
		
		$i = 0;
		
		foreach( $office_staff_ids as $index => $staff_id ){
			
			if ( ! in_array( $staff_id, $wp_people_ids ) && $i < 5 ){
				
				echo 'not Found: ' . $staff_id . '<br/>';
				
				$this->create_person( $staff_id );
				
				$i++;
				
			} // End if
			
			if ( $i > 5 ) break;
			
		} // End foreach
		
	} // End check_office_staff
	
	
	public function get_office(){
		
		require_once 'class-offices-wcrest.php';
		
		$offices = new Offices_Wcrest();
		
		$office_id = $offices->get_next_update_office();
		
		return $office_id;
		
	} // End 
	
	
	public function get_office_staff_ids( $office_id ){
		
		require_once 'class-offices-wcrest.php';
		
		$offices = new Offices_Wcrest();
		
		$office_staff_ids = $offices->get_office_staff_ids( $office_id, true );
		
		return $office_staff_ids;
		
	} // End get_office_ids
	
	
	public function get_wp_people_ids(){
		
		require_once 'class-people-factory-crest.php';
		
		$people_factory = new People_Factory_Crest_People();
		
		$people_ids = $people_factory->get_wp_people_ids();
		
		return $people_ids;
		
	} // End get_wp_people_ids
	
	
	public function create_person( $person_id ){
		
		require_once 'class-crest-wcrest.php';
			
		$crest = new Crest_WCREST();
			
		$response = $crest->get_person_by_id( $person_id );
		
		if ( ! empty( $response['status'] ) ){
			
			require_once  'class-person-wcrest.php';
				
			$person = new Person_WCREST();
			
			$person->set_person_from_crest( $response['response'] );
			
			if( ! empty( $person->settings['_crest_id'] ) ){
				
				if ( ! empty( $person->settings['_display_name'] ) && ( $person->settings['_status'] === 'Active' ) ){
					
					$person->create_person();
					
					echo 'created: ' . $person->settings['_display_name'] . ', ' . $person->settings['_crest_id'] . '<br/>';
					
				} // End if
				
			} // End if
			
		} // End if
		
	} // End create_person

	
} // End People_Wcrest