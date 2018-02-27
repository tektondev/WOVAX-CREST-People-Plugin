<?php


class Settings_Page_WCREST {
	
	public $parent_slug; 
	public $page_title;
	public $menu_title;
	public $capability = 'manage_options';
	public $menu_slug;
	public $settings = array();
	
	
	public function init(){
		
		//if (session_status() == PHP_SESSION_NONE) {
			//session_start();
		//} // end if
		
		add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );
		
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		
	} // end init
	
	
	public function register_settings(){
		
		$settings = $this->settings;
		
		foreach( $settings as $key => $type ){
			
			register_setting( 'wcrest', $key ); 
			
		} // end foreach
		
	} // end register_settings
	
	
	public function add_submenu_page(){
		
		add_submenu_page( 
			$this->parent_slug,
			$this->page_title,
			$this->menu_title,
			$this->capability,
			$this->menu_slug,
			array( $this , 'render_page' )
		);
			
	} // end add_options_page
	
	
	public function render_page(){
		
		if ( isset( $_POST['_do_update'] )  && $_POST['_do_update'] == 'is_true' ) {
			
			$this->save();
			
		} // end if
		
		$settings = $this->get_settings();
		
		$this->the_form( $settings );
		
	} // end render_page
	
	
	private function save(){
		
		$settings = $this->settings;
		
		foreach( $settings as $key => $type ){
			
			if ( isset( $_POST[ $key ] ) ){
				
				$value = sanitize_text_field( $_POST[ $key ] );
				
				$this->save_option( $key, $value, $type );
				
			} // end if
			
		} // end foreach
		
	} // end save
	
	
	private function get_settings(){
		
		$settings = array();
		
		$settings_fields = $this->settings;
		
		foreach( $settings_fields as $key => $type ){
	
			$settings[ $key ] = get_option( $key, '');
			
		} // end foreach
		
		return $settings;
		
	} // end save
	
	
	protected function save_option( $key, $value, $type ){
		
		if ( get_option( $key ) !== false ) {
					
			update_option( $key, $value );
		
		} else {
		
			$deprecated = null;
			$autoload = 'no';
			add_option( $key, $value, $deprecated, $autoload );
			
		} // end if
		
	} // end save_option
	
} // end Settings_Page_WCREST