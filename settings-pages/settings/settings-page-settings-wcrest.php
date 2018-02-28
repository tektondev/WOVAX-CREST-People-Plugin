<?php

class Settings_Page_Settings_WCREST extends Settings_Page_WCREST {
	
	public $parent_slug = 'edit.php?post_type=people'; 
	public $page_title = 'CREST People Settings';
	public $menu_title = 'CREST People Settings';
	public $capability = 'manage_options';
	public $menu_slug = 'crest-settings';
	public $settings = array(
		'_wcrest_user' 		=> 'text',
		'_wcrest_pwd'  		=> 'text',
		'_wcrest_offices'  	=> 'array',
		'_wcrest_blacklist' => 'text',
	);
	
	
	public function render_page(){
		
		if ( isset( $_POST['_add_office'] ) ) {
			
			$this->save_office();
			
		} else if ( isset( $_POST['_update_office'] ) ) { 
		
			$this->update_office();
		
		} else if ( isset( $_POST['_id_blacklist'] ) ) { 
		
			$this->update_blacklist();
		
		} // end if
		
		parent::render_page();
		
	} // end render_page
	
	
	protected function update_blacklist(){
		
		if ( ! empty( $_POST['_wcrest_blacklist'] ) ){ 
		
			$option = sanitize_text_field( $_POST['_wcrest_blacklist']);
		
			update_option( '_wcrest_blacklist', $option );
				   
		} // End if
		
	} // End update_blacklist
	
	
	public function the_form( $settings ){
		
		$email = ( ! empty( $_POST['_email'] ) )? $_POST['_email'] : '';
		
		if ( $email ){
			
			require_once WCRESTPLUGINPATH . 'classes/class-crest-wcrest.php';
			
			$crest = new Crest_WCREST();
			
			$office_id = $crest->get_office_id_from_email( $email );
			
			$office_id = $office_id['response'];
			
		} else {
			
			$office_id = '';
			
		} // end if
		
		if ( ! is_array( $settings['_wcrest_offices'] ) ) $settings['_wcrest_offices'] = array();
		
		include 'parts/form.php';
		
	} //end the_form
	
	
	private function save_office(){
		
		$offices = get_option( '_wcrest_offices', array() );
		
		if ( ! empty( $_POST['_add_office_id'] ) ) {
			
			$office_id = sanitize_text_field(  $_POST['_add_office_id'] ); 
		
			$office_name = ( ! empty( $_POST['_add_office_name'] ) ) ? sanitize_text_field(  $_POST['_add_office_name'] ) : $office_id;
			
			$offices[ $office_id ] = $office_name;
			
			$this->save_option( '_wcrest_offices', $offices, 'array' );
			
		} // end if
		
	} // end save_office
	
	
	private function update_office(){
		
		$offices = ( ! empty( $_POST['_wcrest_office'] ) )? $_POST['_wcrest_office'] : array();
		
		$this->save_option( '_wcrest_offices', $offices, 'array' );
		
	} // end update_office
	
} // end Settings_Page_WCREST