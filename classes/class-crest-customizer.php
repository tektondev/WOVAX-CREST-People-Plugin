<?php

class CREST_Customizer {
	
	
	public function init(){
		
		//add_action( 'customize_register', array( $this , 'add_options' ) );
		
	} // end init
	
	
	public function add_options( $wp_customize ){
		
		$wp_customize->add_setting( 'crest_profile_shortcode_1_label' , array(
			'default'     => '',
			'transport'   => 'refresh',
		) );
		
		$wp_customize->add_setting( 'crest_profile_shortcode_1' , array(
			'default'     => '',
			'transport'   => 'refresh',
		) );
		
		$wp_customize->add_setting( 'crest_profile_shortcode_2_label' , array(
			'default'     => '',
			'transport'   => 'refresh',
		) );
		
		$wp_customize->add_setting( 'crest_profile_shortcode_2' , array(
			'default'     => '',
			'transport'   => 'refresh',
		) );
		
		$wp_customize->add_section( 'crest_profile' , array(
			'title'     => 'CREST People Profiles',
			'priority'  => 30,
		) );
		

		$wp_customize->add_control(
			new WP_Customize_Control(
			   $wp_customize,
			   'crest_profile_shortcode_1_label_control',
			   array(
				   'label'      => 'Shortcode 1 Label',
				   'section'    => 'crest_profile',
				   'settings'   => 'crest_profile_shortcode_1_label',
				   'type'       => 'text',
			   )
		   )
		);
		
		$wp_customize->add_control(
			new WP_Customize_Control(
			   $wp_customize,
			   'crest_profile_shortcode_1_control',
			   array(
				   'label'      	=> 'Shortcode 1',
				   'section'    	=> 'crest_profile',
				   'settings'   	=> 'crest_profile_shortcode_1',
				   'type'       	=> 'text',
				   'description' 	=> 'Supported replace codes: %crest_id%, %primary_email%, %rfg_office_staff_id%, %first_name%, %last_name%',
			   )
		   )
		);
		
		$wp_customize->add_control(
			new WP_Customize_Control(
			   $wp_customize,
			   'crest_profile_shortcode_2_label_control',
			   array(
				   'label'      => 'Shortcode 2 Label',
				   'section'    => 'crest_profile',
				   'settings'   => 'crest_profile_shortcode_2_label',
				   'type'       => 'text',
			   )
		   )
		);
		
		$wp_customize->add_control(
			new WP_Customize_Control(
			   $wp_customize,
			   'crest_profile_shortcode_2_control',
			   array(
				   'label'      => 'Shortcode 2',
				   'section'    => 'crest_profile',
				   'settings'   => 'crest_profile_shortcode_2',
				   'type'       => 'text',
				   'description' 	=> 'Supported replace codes: %crest_id%, %primary_email%, %rfg_office_staff_id%',
			   )
		   )
		);
		
	}
	
} // end CREST_Customizer