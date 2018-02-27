<?php

class People_Factory_Crest_People {
	
	
	public function __construct(){
		
		require_once 'class-person-wcrest.php';
		
	} // End __construct
	
	
	public function get_person(){
		
		$person = new Person_WCREST();
		
		return $person;
		
	} // End get_person
	
	
	public function get_wp_people( $args = array(), $sort = true ){
		
		$people = array();
		
		$default_args = array(
			'post_type' 			=> 'people',
			'posts_per_page' 		=> -1,
			'post_status' 			=> 'publish',
		);
		
		foreach( $default_args as $key => $value ){
			
			if ( ! array_key_exists( $key, $args ) ){
				
				$args[ $key ] = $value;
				
			} // End if
			
		} // End foreach
		
		if ( ! empty( $_GET['force_update_id']) ){
			
			$force_update_id = sanitize_text_field( $_GET['force_update_id'] );
			
			$args['include'] = array( $force_update_id );
			
		} // End if
		
		$posts = get_posts( $args );
		
		foreach( $posts as $post ){
			
			$person = $this->get_person();
				
			$person->set_person_from_wp( $post->ID );
			
			if ( ! empty( $person->settings['_crest_id'] ) ){
				
				$people[ $post->ID ] = $person;
				
			}// End if
				
		} // End foreach
		
		/*$the_query = new WP_Query( $args );

		// The Loop
		if ( $the_query->have_posts() ) {
			
			while ( $the_query->have_posts() ) {
				
				$the_query->the_post();
				
				$person = $this->get_person();
				
				$person->set_person_from_wp( get_the_ID() );
				
				$people[ $person->settings['_crest_id']] = $person;
				
			} // End while
			
			wp_reset_postdata();
			
		} // End if*/
		
		if ( $sort  ){
		
			usort( $people, function($a, $b) {

				$a_name = $a->settings['_last_name'];

				$b_name = $b->settings['_last_name'];

				return ($a_name < $b_name) ? -1 : 1;

			});
			
		} // End if
		
		return $people;
		
	} // End get_people
	
	public function get_wp_people_ids( $args = array() ){
		
		$people_ids = array();
		
		$default_args = array(
			'post_type' 		=> 'people',
			'posts_per_page' 	=> -1,
			'post_status' 		=> 'publish',
		);
		
		foreach( $default_args as $key => $value ){
			
			if ( ! array_key_exists( $key, $args ) ){
				
				$args[ $key ] = $value;
				
			} // End if
			
		} // End foreach
		
		$posts = get_posts( $args );
		
		foreach( $posts as $post ){
			
			$post_id = $post->ID;
			
			$person_id = get_post_meta( $post_id, '_crest_id', true );
			
			if ( ! empty( $person_id ) ){
				
				$people_ids[ $post_id ] = $person_id;
				
			}// End if
			
		} // End foreach
		
		return $people_ids;
		
	} // End get_people
	
} // End People_Factory_Crest_People