<?php

class Settings_Page_CREST_Sync extends Settings_Page_WCREST {
	
	public $parent_slug = 'edit.php?post_type=people'; 
	public $page_title = 'CREST Sync';
	public $menu_title = 'CREST Sync';
	public $capability = 'manage_options';
	public $menu_slug = 'crest-sync';
	
	
	public function the_form( $settings ){
		
		//error_reporting(E_ALL);
		//ini_set('display_errors', 1);
		
		require_once WCRESTPLUGINPATH . 'classes/class-people-factory-crest.php';
		
		$people_factory = new People_Factory_Crest_People();
		
		$wp_people = $people_factory->get_wp_people();
		
		//var_dump( $wp_people );
		
		$html = $this->get_update_people_form( $wp_people );
		
		echo $html;	
		
		//require_once WCRESTPLUGINPATH . 'classes/class-people-wcrest.php';
		
		//$people = new People_Wcrest();
		
		//$people->check_office_staff();
		
		//$this->check_people();
			
	} //end the_form
	
	
	public function get_update_people_form( $wp_people ){
		
		$inner_html = '';
		
		foreach( $wp_people as $crest_id => $person ){
			
			ob_start();
			
			include 'includes/update-person-list-item.php';
			
			$inner_html .= ob_get_clean();
			
		} // End foreach
		
		ob_start();
			
		include 'includes/update-person-wrapper.php';

		$html = ob_get_clean();
		
		return $html;
		
	} // End $people_factory
	
	
	public function check_people(){
		
		/*$msg = 'Fail';
	
		require_once WCRESTPLUGINPATH . 'classes/class-people-factory-crest.php';
		
		$people_factory = new People_Factory_Crest_People();
		
		$args = array(
			'meta_key'  		=> '_last_checked',
  			'orderby'   		=> 'meta_value_num',
  			'order'     		=> 'ASC',
			'posts_per_page' 	=> 5,
		);
		
		$wp_people = $people_factory->get_wp_people( $args );
		
		foreach( $wp_people as $person_id => $wp_person ){
			
			require_once WCRESTPLUGINPATH . 'classes/class-crest-wcrest.php';
			
			$crest = new Crest_WCREST();
			
			$response = $crest->get_person_by_id( $wp_person->settings['_crest_id'] );
			
			if ( ! empty( $response['status'] ) ){
				
				require_once  WCRESTPLUGINPATH . 'classes/class-person-wcrest.php';
				
				$person = new Person_WCREST();
				
				if ( $person->set_person_from_crest( $response['response'] ) ){
					
					$force_update = false;
					
					$person_response = $person->create_person( $force_update );
					
					//if ( $person_response['response'] && ! empty( $office_id ) ){
						
						//$person->append_office( $person_response['response'], $office_id );
						
					//} // end if
					
					$json = $person_response;
					
					$json['name'] = $person->settings['_display_name'];
					
				} else {
					
					$json = array( 'status' => false, 'msg' => 'Could not set person' );
					
				}// end if;
				
			} else {
				
				$json = array( 'status' => false, 'msg' => 'Invalid Response' );
				
			}
			
			$msg .= json_encode( $json );
			
		} // End foreach
		
		echo $msg;
		
		//echo $msg;*/
		
	} // End check_people
	
} // end Settings_Page_WCREST