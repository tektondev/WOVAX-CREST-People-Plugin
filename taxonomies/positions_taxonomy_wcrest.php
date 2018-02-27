<?php

class Positions_Taxonomy_WCREST {
	
	
	public function init(){
		
		add_action( 'init', array( $this, 'register_taxonomy' ), 99 );
		
	} // end init
	
	
	public function register_taxonomy(){
		
		register_taxonomy(
			'people_position',
			'people',
			array(
				'label' => 'Positions',
				'rewrite' => array( 'slug' => 'positions' ),
				'hierarchical' => true,
			)
		);
		
	} // end register_taxonomy
	
	
}