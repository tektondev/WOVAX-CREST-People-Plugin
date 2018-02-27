<style>
	#crest-update-people > li {

		display: block;
		padding: 12px;
		border-bottom: 1px solid #ccc;
		position: relative;
	}
	#crest-update-people ul:after {
		clear:both;
		content: '';
		display: block;
	}
	#crest-update-people ul li {
		float: left;
		padding-right: 18px;
		height: 30px;
		line-height: 30px;
	}
	#crest-update-people ul li a {
		display: inline-block;
		padding: 0 18px;
		background-color: #0073aa;;
		color: #fff;
		text-decoration: none;
		border-radius: 2px;
		height: 30px;
		line-height: 30px;
	}
	#crest-update-people ul li a:hover {
		background-color: #555;
	}
	.crest-update-people-update-all {
		display: inline-block;
		padding: 0 22px;
		background-color: #0073aa;;
		color: #fff;
		text-decoration: none;
		border-radius: 2px;
		height: 50px;
		line-height: 50px;
		text-transform: uppercase;
		font-size: 18px;
	}
	.crest-update-people-update-all span.stop {
		display: none;
	}
	.crest-update-people-update-all.doing-update span.stop {
		display: block;
	}
	.crest-update-people-update-all.doing-update span.update {
		display: none;
	}
	.person-item.is-updated {
		padding-left: 40px !important;
		box-sizing: border-box;
	}
	.person-item.is-updated > ul:before {
		content: '\f058';
		display: inline-block;
		font-size: 20px;
		color: green;
		margin-right: 12px;
		font-family: fontAwesome;
		position: absolute;
		top: 16px;
		left: 0;
	}
	.person-item .changes {
		color: red;
		font-weight: bold;
		font-size: 12px;
	}
</style>
<div class="crest-update-people-controls crest-controls" style="margin-top: 60px;">
	<a href="#" class="crest-update-people-update-all"><span class="update">Update All People</span><span class="stop">Stop</span></a>
</div>
<div class="c-time">
Current Server Time: <?php echo date("D M d, Y G:i");?>
</div>
<ul id="crest-update-people">
	<?php echo $inner_html;?>
</ul>
<script>
	
	jQuery('body').on(
		'click',
		'.crest-update-people-update-all',
		function(){
			
			if ( jQuery(this).hasClass('doing-update') ){
				
				jQuery(this).removeClass('doing-update');
				
			} else {
			
				jQuery(this).addClass('doing-update');
				
				crest_update_person();
				
			}
		} 
	);
	
	jQuery('body').on(
		'click',
		'.person-item .update a',
		function( event ){
			
			event.preventDefault();
			
			var parent = jQuery(this).closest('.person-item');
			
			var update_id = parent.data('crestid');
				
			var update_office_id = parent.data('officeid');

			do_update( parent, update_id, update_office_id, false )
				
		} 
	);
	
	function crest_update_person(){
		
		if ( jQuery('.crest-update-people-update-all').hasClass('doing-update') ){
			
			var people = jQuery('.person-item');
			
			var i = 0;
			
			if ( people.filter('.is-updated').length ){
				
				i = people.filter('.is-updated').last().index();
				
				i++;
				
			} // End if
			
			console.log( i );
			
			
			if ( people.eq( i ).length ){
				
				var person_item = people.eq( i );
				
				var update_id = person_item.data('crestid');
				
				var update_office_id = person_item.data('officeid');
				
				person_item.addClass('is-updated');
				
				do_update( person_item, update_id, update_office_id, crest_update_person );
				
			} // End if
			
		} // end if
		
	} // End crest_update_person
	
	function do_update( person_item, update_id, update_office_id, callback ){
		
		var url = '<?php echo get_site_url();?>?crest-ajax-action=update-person';

		var data = { person_id:update_id, office_id: update_office_id };
		
		person_item.addClass('is-updated');

		console.log( data );

		jQuery.get(
			url,
			data,
			function( response ){
				
				console.log( response );
				
				person_item.find('.changes').html( 'Response: ' + response.msg );
				
				if( callback ){
					
					setTimeout( function(){ crest_update_person(); }, 1000);
					
				} // End if	

			},
			'json'
		) // end get
		
		
	} // End do_update
</script>