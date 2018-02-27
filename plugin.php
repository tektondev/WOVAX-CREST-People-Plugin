<?php
/*
Plugin Name: Wovax CREST People Updated
Plugin URI: https://www.wovax.com/
Description: Sync people with CREST feed.
Version: 1.0.0
Author: Wovax, Danial Bleile.
Author URI: https://www.wovax.com/
*/

class WCREST_People {
	
	// @var string Version
	public static $version = '0.0.1';
	
	public static $instance;
	
	public $people;
	public $crest;
	public $crest_settings_page;
	public $crest_preload_page;
	public $taxonomy_positions;
	
	
	public static function get_instance(){
		
		if ( null == self::$instance ) {
			 
            self::$instance = new self;
			self::$instance->init();
			
        } // end if
 
        return self::$instance;
		
	} // end get_instance
	
	
	private function init(){
		
		define( 'WCRESTPLUGINURL' , plugin_dir_url(__FILE__) );
		define( 'WCRESTPLUGINPATH' , plugin_dir_path(__FILE__) );
		
		
		require_once 'classes/class-crest-wcrest.php';
		$this->crest = new Crest_WCREST();
		
		$this->add_post_types();
		
		$this->add_settings_pages();
		
		$this->add_taxonomies();
		
		$this->add_customizer();
		
		add_filter( 'template_include', array( $this, 'template_include'), 99 );
		
		add_filter( 'cron_schedules', array( $this, 'add_schedule_event' ) );
		
		add_action( 'wcrest_check_people', array( $this, 'check_people') );
		
		//if ( isset( $_GET['check-people'] ) ){
			
			//add_action( 'admin_footer', array( $this, 'check_people_test') );
			
		//} // End if
		
	} // end init
	
	
	public function add_schedule_event( $schedules ) {
	// add a 'weekly' schedule to the existing set
		
		if ( empty( $schedules['5min'] ) ){
			
			$schedules['5min'] = array(
				'interval' => 300,
				'display' => 'Five Minutes',
			);
			
		} // End if

		return $schedules;
		
	} // End add_schedule_event
	
	
	public function check_people_test(){
			
		//$this->check_people();
		
	} // End check_people_test
	
	
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
		
		if ( isset( $_GET['check-people'] ) ){
			
			echo $msg;
			
		} // End if
		
		//echo $msg;*/
		
	} // End check_people
	
	
	private function add_customizer(){
		
		require_once 'classes/class-crest-customizer.php';
		
		$customizer = new CREST_Customizer();
		$customizer->init();
		
	} // end add_customizer
	
	
	private function add_post_types(){
		
		require_once 'post-types/post-type-wcrest.php';
		require_once 'post-types/people/people-post-type-wcrest.php';
		
		$this->people = new People_Post_Type_WCREST();
		$this->people->init();
		
	} // end add_post_types
	
	
	private function add_settings_pages(){
		
		require_once 'settings-pages/settings-page-wcrest.php';
		require_once 'settings-pages/settings/settings-page-settings-wcrest.php';
		require_once 'settings-pages/preload/settings-page-preload-wcrest.php';
		require_once 'settings-pages/crest-sync/settings-page-crest-sync.php';
		
		$this->crest_settings_page = new Settings_Page_Settings_WCREST();
		$this->crest_preload_page = new Settings_Page_Preload_WCREST();
		$this->crest_sync_page = new Settings_Page_CREST_Sync();
		
		$this->crest_settings_page->init();
		$this->crest_preload_page->init();
		$this->crest_sync_page->init();
	}
	
	
	private function add_taxonomies(){
		
		require_once 'taxonomies/positions_taxonomy_wcrest.php';
		
		$this->taxonomy_positions = new Positions_Taxonomy_WCREST();
		
		$this->taxonomy_positions->init();
		
	} // end add_taxonomies
	
	
	public function template_include( $template ){
		
		if ( ! empty( $_GET['crest-ajax-action'] ) ){
			
			$template =  WCRESTPLUGINPATH . 'templates/ajax-template-wcrest.php';
			
		} else if ( ! empty( $_GET['update-crest-people'] ) ){
			
			$template =  WCRESTPLUGINPATH . 'templates/update-people.php';
			
		}// end if
		
		return $template;
		
	} // end template_include
	
} // end WCREST_People

$wcrest = WCREST_People::get_instance();

function wpcrest_people_plugin_activate() {
		
	if ( ! wp_next_scheduled( 'wcrest_check_people' ) ) {

		wp_schedule_event( time(), '5min', 'wcrest_check_people' );

	}// End if

} // End my_activation

function wpcrest_people_plugin_deactivate() {
		
	wp_clear_scheduled_hook( 'wcrest_check_people' );

} // End 

register_activation_hook(__FILE__, 'wpcrest_people_plugin_activate');
		
register_deactivation_hook( __FILE__, 'wpcrest_people_plugin_deactivate') ;