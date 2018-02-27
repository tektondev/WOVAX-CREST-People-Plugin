<?php

class Settings_Page_Preload_WCREST extends Settings_Page_WCREST {
	
	public $parent_slug = 'edit.php?post_type=people'; 
	public $page_title = 'Preload People';
	public $menu_title = 'Preload People';
	public $capability = 'manage_options';
	public $menu_slug = 'preload-people';
	
	
	public function the_form( $settings ){
		
		$offices = get_option( '_wcrest_offices', array() );
		
		if ( ! is_array( $offices ) ) $offices = array();
		
		include 'parts/form.php';
			
	} //end the_form
	
} // end Settings_Page_WCREST