<?php

class AJAX_Template_WCREST {
	
	
	public function __construct(){
		
		ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
		
		if ( ! empty( $_GET['crest-ajax-action'] ) ){
			
			switch( $_GET['crest-ajax-action'] ){
				case 'view-person':
					$this->view_person();
					break;
				case 'query-office':
					$this->query_office();
					break;
				case 'update-person':
					$this->load_person();
					break;
				case 'check-status':
					$this->check_status();
					break;
				case 'set_id_image':
					$this->set_id_image();
					break;
			} // end switch
			
		} else {
			
			$json = array( 'status' => false, 'msg' => 'No Action Found' );
			
			die( json_encode( $json ) );
			
		} // end if
		
	} // end __construct
	
	
	private function set_id_image(){
		
	} // End set_id_image
	
	
	private function view_person(){
		
		$person_id = sanitize_text_field( $_GET['person_id'] );
			
		$office_id = ( isset( $_GET['person_id'] )  )? sanitize_text_field( $_GET['person_id'] ) : '';

		require_once WCRESTPLUGINPATH . 'classes/class-crest-wcrest.php';

		$crest = new Crest_WCREST();

		$response = $crest->get_person_by_id( $person_id );
		
		require_once  WCRESTPLUGINPATH . 'classes/class-person-wcrest.php';
				
		$person = new Person_WCREST();
		
		$person->set_person_from_crest( $response['response'] );
		
		echo '<textarea style="width: 100%; height: 2000px;">' . htmlspecialchars( $response['response'] ) . '</textarea>';
		
	} // End view_person
	
	
	private function check_status(){
		
		if ( isset( $_GET['person_id'] ) ){
			
			$person_id = sanitize_text_field( $_GET['person_id'] );
			
			$office_id = ( isset( $_GET['person_id'] )  )? sanitize_text_field( $_GET['person_id'] ) : '';
			
			require_once WCRESTPLUGINPATH . 'classes/class-crest-wcrest.php';
			
			$crest = new Crest_WCREST();
			
			$response = $crest->get_person_by_id( $person_id );
			
			if ( ! empty( $response['status'] ) ){
				
				require_once  WCRESTPLUGINPATH . 'classes/class-person-wcrest.php';
				
				$person = new Person_WCREST();
				
				if ( $person->set_person_from_crest( $response['response'] ) ){
					
					echo json_encode( $person->check_status( $person_id ) );
					
				} // End if
				
			} // End if
			
		} // End if
		
	} // End check_status
	
	
	private function query_office(){
		
		if ( isset( $_GET['office_id'] ) ){
			
			$office_id = sanitize_text_field( $_GET['office_id'] );
			
			require_once WCRESTPLUGINPATH . 'classes/class-crest-wcrest.php';
			
			$crest = new Crest_WCREST();
			
			$response = $crest->get_people_ids_by_office( $office_id );
			
			echo json_encode( $response );
			
			die();
			
		} else {
			
			$json = array( 'status' => false, 'msg' => 'No Office ID' );
			
			die( json_encode( $json ) );
			
		} // end if
		
	} // end query_office
	
	
	private function load_person(){
		
		if ( isset( $_GET['person_id'] ) ){
			
			$person_id = sanitize_text_field( $_GET['person_id'] );
			
			$office_id = ( isset( $_GET['person_id'] )  )? sanitize_text_field( $_GET['person_id'] ) : '';
			
			require_once WCRESTPLUGINPATH . 'classes/class-crest-wcrest.php';
			
			$crest = new Crest_WCREST();
			
			$response = $crest->get_person_by_id( $person_id );
			
			if ( ! empty( $response['status'] ) ){
				
				require_once  WCRESTPLUGINPATH . 'classes/class-person-wcrest.php';
				
				$person = new Person_WCREST();
				
				if ( $person->set_person_from_crest( $response['response'] ) ){
					
					$force_update = ( isset( $_GET['force-update'] ) )? true : false;
					
					$person_response = $person->create_person( $force_update );
					
					if ( $person_response['response'] && ! empty( $office_id ) ){
						
						$person->append_office( $person_response['response'], $office_id );
						
					} // end if
					
					echo json_encode( $person_response );
					
					die();
					
				} else {
					
					$json = array( 'status' => false, 'msg' => 'Could not set person' );
			
					die( json_encode( $json ) );
					
				}// end if;
				
			} else {
				
				$json = array( 'status' => false, 'msg' => 'Invalid Response' );
			
				die( json_encode( $json ) );
				
			}
			
			//echo json_encode( $response );
			
			die();
			
		} else {
			
			$json = array( 'status' => false, 'msg' => 'No Person ID' );
			
			die( json_encode( $json ) );
			
		} // end if
		
	}
	
} // end AJAX_Template_WCREST

$wcrest_template = new AJAX_Template_WCREST();