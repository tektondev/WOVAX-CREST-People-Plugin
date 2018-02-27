<?php

class Post_Type_WCREST {
	
	public $post_type;
	public $post_type_args = array();
	public $post_type_labels = array();
	public $fields = array();
	
	
	public function init(){
		
		//if ( isset( $this->post_type ) && $this->post_type ) { 
		
			//add_action( 'init', array( $this , 'register_post_type' ) );
			
		//} // end if
		
		//if ( method_exists( $this, 'the_editor' ) ){
			
			//add_action( 'edit_form_after_title', array( $this, 'the_editor_form' ) );
			
		//} // end if
		
		//if ( ! empty( $this->fields ) ){
			
			//add_action( 'save_post_' . $this->post_type, array( $this, 'save' ) );
			
		//} // end if
		
		//if ( method_exists( $this, 'the_content_filter' ) ){
		
			//add_filter( 'the_content', array( $this , 'the_content_filter' ), 99999999 );
		
		//} // end if
		
	} // end init
	
	
	public function the_editor_form( $post ){
		
		if ( $post->post_type == $this->post_type ){
		
			$settings = $this->get_settings( $post->ID ); 
		
			$this->the_editor( $post, $settings );
		
		} // end if
		
	} // end the_editor_form
	
	
	public function get_settings( $post_id ){
		
		$settings = array();
		
		foreach( $this->fields as $key => $type ){
			
			$settings[ $key ] = get_post_meta( $post_id, $key, true );
			
			$settings[ $key . '_manual' ] = get_post_meta( $post_id, $key . '_manual', true );
			
		} // end foreach
		
		return $settings;
		
	} // end get_settings
	
	
	public function register_post_type(){
		
		$args = $this->post_type_args;
		
		if ( ! empty( $this->post_type_labels ) ){
			
			$args['labels'] = $this->post_type_labels;
			
		} // end if
		
		register_post_type( $this->post_type, $args );
		
	} // end register_post_type
	
	
	public function save( $post_id ){
		
		$save_settings = array();
		
		foreach( $this->fields as $key => $type ){
			
			if ( isset( $_POST[ $key ] ) ){
				
				$save_settings[ $key ] =  $_POST[ $key ];
				
			} // end if
			
			if ( isset( $_POST[ $key . '_manual' ]  ) ){
				
				$save_settings[ $key . '_manual' ] =  $_POST[ $key . '_manual' ];
				
			} // end if
			
		} // end foreach
		
		foreach( $save_settings as $key => $value ){
			
			update_post_meta( $post_id, $key, $value );
			
		} // end foreach
		
	} // end save
	
} // end Post_Type_WCREST