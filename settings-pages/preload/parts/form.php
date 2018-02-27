<form id="crest-office-preload" method="post">
	<fieldset class="office-select">
    	<div class="crest-people-field crest-people-office">
        	<label>Select Office</label>
        	<select id="office_id" name="office_id">
				<?php foreach( $offices as $office_id => $name ):?>
                    <option value="<?php echo $office_id; ?>"><?php echo $name;?></option>
                <?php endforeach;?>
        	</select>
        </div>
        <div class="crest-people-field">
            <input type="submit" name="_preload_office" value="Preload" />
        </div>
		<div class="crest-people-field">
            <input id="force-update-control" type="checkbox" name="force-input" value="1" /> <label for="force-update-control">Force Update from CREST</label>
        </div>
		<div class="crest-people-field">
            <a href="" id="check-status">Check Status</a>
        </div>
        <ul id="people-loaded">
        </ul>
    </fieldset>
</form>
<?php 
$posts = get_posts( array( 'post_type' => 'people', 'posts_per_page' => -1 ) );

$people_ids = array();

foreach( $posts as $post ){
	
	$meta = get_post_meta( $post->ID, '_person_id', true );
	
	if ( ! empty( $meta ) ){
		
		$people_ids[] = '"' . $meta . '"';
		
	} // end if
	
}; 

?>
<script>
var crest_preload = {
	
	people: [<?php echo implode( ',', $people_ids );?>],
	
	init:function(){
		
		crest_preload.events();
		
	}, // end init
	
	events:function(){
		
		jQuery('#crest-office-preload').on(
			'submit',
			function( e ){
				e.preventDefault();
				crest_preload.ajax.get_people( jQuery( this ), crest_preload.load_person );
			}
		) // end #crest-office-preload submit
		
		jQuery('#check-status').on(
			'click',
			function( e ){
				e.preventDefault();
				
				crest_preload.ajax.check_status( crest_preload.ajax.check_status );
				//crest_preload.ajax.get_people( jQuery( this ), crest_preload.load_person );
			}
		) // end #crest-office-preload submit
		
	}, // end events
	
	ajax:{
		
		check_status:function( callback ){
			
			var url = '<?php echo get_site_url();?>?crest-ajax-action=check-status';
			
			console.log( url + '&person_id=' + crest_preload.people[0] );
			
			if ( crest_preload.people.length > 0 ){
				
				jQuery.get(
					url,
					{ person_id: crest_preload.people[0] },
					
					function( response ){
						
						crest_preload.people.splice( 0, 1 );
						
						jQuery('#people-loaded').prepend('<li>' + crest_preload.people.length + ' ' + response['msg'] + '</li>' );
						
						callback( crest_preload.ajax.check_status( crest_preload.ajax.check_status ) );
							
					},
					'json'
				) // end get
				
			} // End if
			
		},
		
		get_people:function( form, callback ){
			
			var url = '<?php echo get_site_url();?>?crest-ajax-action=query-office';
			
			var data = form.serialize();
			
			console.log( url );
			
			jQuery.get(
				url,
				data,
				function( response ){
					callback( response );	
				},
				'json'
			) // end get
			
		}, // end get_people
		
		get_person:function( i, people_ids, callback ){
			
			var url = '<?php echo get_site_url();?>?crest-ajax-action=update-person';
			
			var office_id_val = jQuery( '#office_id' ).val();
			
			var data = { person_id:people_ids[i], office_id: office_id_val };
			
			//if ( jQuery( '#force-update-control' ).val() ){
				//data['force-update'] = 1;
			//}
			
			console.log( url );
			
			console.log( data );
			
			jQuery.get(
				url,
				data,
				function( response ){
					console.log( response );
					callback( response, i, people_ids );	
					
				},
				'json'
			) // end get
			
		}, // end get_person
		
	}, // end ajax
	
	load_person:function( people ){
			
			var people_ids = people.response;
			
			crest_preload.ajax.get_person( 0, people_ids, crest_preload.callbacks.person_loaded );
		
	}, // end load people
	
	callbacks:{
		
		person_loaded:function( response, i, people_ids ){ 
			
			jQuery('#people-loaded').prepend('<li>' + ( i + 1 ) + '. ' + people_ids[i] + ' ' + response['msg'] + '</li>' );
			
			console.log( response );
			
			i++; 
			
			if ( i < people_ids.length ) {
				
			//if ( i < 10 ) {  
				
				setTimeout(function () {       
         			crest_preload.ajax.get_person( i, people_ids, crest_preload.callbacks.person_loaded );
				}, 500 ) 
				             
      		} else {
				
				jQuery('#people-loaded').prepend('<li>All Done!</li>' );
				
			}// end if 
			
		}
		
	}, // end callbacks
	
}
crest_preload.init();
</script>