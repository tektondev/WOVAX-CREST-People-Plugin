<?php

class Offices_Wcrest {
	
	public function get_next_update_office( $set_updated = false ){
		
		$args = array(
			'post_type' 		=> 'office',
			'posts_per_page' 	=> 	-1,
			'status' 			=> 'publish',
			'order_by'			=> 'post_modified',
			'order'				=> 'ASC',
		);
		
		$the_query = new WP_Query( $args );
		
		$crest_id = false;
		
		$offices = array();

		// The Loop
		if ( $the_query->have_posts() ) {
			
			while ( $the_query->have_posts() ) {
				
				$the_query->the_post();
				
				$post_id = get_the_ID();
				
				$id = get_post_meta( $post_id, '_office_id', true );
				
				$last_checked = get_post_meta( $post_id, '_wovax_staff_checked', true );
				
				$offices[ $id ] = array( 'crest_id' => $id, 'checked' => $last_checked, 'post_id' => $post_id );
				
				//var_dump( $crest_id );
				
			} // End while
			
			wp_reset_postdata();
			
		} // End if
		
		usort( $offices, function($a, $b) {
			return ( $a['checked'] > $b['checked'] ) ? 1 : -1;
		});
		
		$next_office = reset( $offices );
		
		//var_dump( $next_office );
		
		$this->set_updated_date( $next_office['post_id'] );
		
		return $next_office;
		
	} // End get_next_update_office
	
	
	public function get_office_staff_ids( $office_id = false, $set_updated = false ){
		
		if ( ! $office_id ){
			
			$office = $this->get_next_update_office( $set_updated, true );
			
			$office_id = $office['crest_id'];
			
		} // End if
		
		require_once 'class-crest-wcrest.php';
			
		$crest = new Crest_WCREST();

		$response = $crest->get_people_ids_by_office( $office_id );

		if ( is_array( $response ) && ! empty( $response['response'] ) ){

			return $response['response'];

		} else {

			return array();

		} // End if
		
	} // End get_office_staff_ids
	
	
	public function set_updated_date( $post_id ){
		
		$date = date("Y-m-dTH:i:s");
		
		update_post_meta( $post_id, '_wovax_staff_checked', $date );
		
	} // End set_updated_date
	
	
} // End Offices_Wcrest
