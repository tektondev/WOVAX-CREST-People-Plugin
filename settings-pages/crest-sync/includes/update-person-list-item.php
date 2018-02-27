<li class="person-item" data-postid="<?php if ( ! empty( $person->settings['_post_id'] ) ) echo $person->settings['_post_id'];?>" data-crestid="<?php echo $person->settings['_crest_id'];?>" data-officeid="<?php echo $person->settings['_office_id'];?>">
	<ul>
		<li class="name"><strong><?php echo $person->settings['_display_name'];?></strong></li>
		<li class="mls-id">MLS ID: <?php echo $person->settings['_mls_id'];?></li>
		<li class="position">Position: <?php echo $person->settings['_position'];?></li>
		<li class="position">Last Updated (CREST): <?php echo $person->settings['_last_updated_crest'];?></li>
		<li class="position">Last Checked: <?php echo $person->settings['_last_checked'];?></li>
		<li class="update"><a href="#">Update Person</a></li>
		<li class="view-crest"><a href="<?php echo get_site_url();?>?crest-ajax-action=view-person&person_id=<?php echo $person->settings['_crest_id'];?>">View CREST Output</a></li>
	</ul>
	<span class="changes"></span>
</li>
