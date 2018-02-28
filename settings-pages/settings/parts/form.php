<style>
	#crest-people-settings form {
		margin: 0;
		padding: 30px 0;
		border-bottom: 1px solid #ccc;
		border-top: 1px solid #fff;
	}
	#crest-people-settings label {
		font-weight: bold;
		display:  block;
	}
	#crest-people-settings .crest-people-field {
		padding-bottom: 18px;
	}
	#crest-people-settings input[type="text"] {
		height: 40px;
		line-height: 40px;
		border: 1px solid #0073aa;
		width: 250px;
	}
	#crest-people-settings input[type="submit"] {
		height: 40px;
		line-height: 40px;
		background-color: #0073aa;
		color: #fff;
		padding: 0 40px;
		outline: none;
		border: none;
		border-radius: 3px;
	}
</style>
<div id="crest-people-settings">
<form id="crest-people-update" method="post">
	<fieldset class="update-info">
    	<div class="crest-people-field">
        	<label>User Name</label>
        	<input type="text" name="_wcrest_user" value="<?php echo $settings['_wcrest_user'];?>" />
        </div>
        <div class="crest-people-field">
        	<label>Password</label>
        	<input type="text" name="_wcrest_pwd"  value="<?php echo $settings['_wcrest_pwd'];?>" />
        </div>
        <div class="crest-people-field">
            <input type="submit" name="_save_settings" value="Save" />
        </div>
    </fieldset>
    <input type="hidden" name="_do_update" value="is_true" />
</form>
<form id="crest-office-find" method="post">
	<fieldset class="office-find">
    	<?php if ( ! empty( $email ) ):?>
        <div class="crest-people-field">
        	<?php if ( ! empty( $office_id ) ):?>
            	<?php echo $email;?> = <?php echo $office_id;?>
            <?php else:?>
            	Sorry, office ID not found for that email 
            <?php endif;?>
        </div>	
        <?php endif;?>
        <div class="crest-people-field">
        	<label>Email</label>
        	<input type="text" name="_email"  value="<?php echo $email;?>" />
        </div>
        <div class="crest-people-field">
        	<input type="submit" name="_office_find" value="Add Office" />
        </div>
    </fieldset>
</form>
<form id="crest-office-add" method="post">
	<fieldset class="office-info">
		<div class="crest-people-field">
        	<label>Add Office (name)</label>
            <input type="text" name="_add_office_name" />
        </div>
        <div class="crest-people-field">
        	<label>Office ID</label>
        	<input type="text" name="_add_office_id" />
        </div>
    </fieldset>
    <input type="hidden" name="_do_update" value="is_true" />
    <div class="crest-people-field">
        <input type="submit" name="_add_office" value="Add Office" />
    </div>
</form>
<form id="crest-office-update" method="post">
	<fieldset class="office-info">
        <?php foreach( $settings['_wcrest_offices'] as $office_id => $name ):?>
        <div class="crest-people-field crest-people-office">
        	<label><?php echo $office_id; ?></label>
        	<input type="text" name="_wcrest_office[<?php echo $office_id;?>]" value="<?php echo $name;?>" />
            <a href="#" class="remove">X</a>
        </div>
        <?php endforeach;?>
        <div class="crest-people-field">
            <input type="submit" name="_update_office" value="Update" />
        </div>
    </fieldset>
</form>
<form id="crest-id-blacklist" method="post">
	<fieldset class="blacklist-info">
       <div class="crest-people-field crest-id-blacklist">
        	<label>Blacklist CREST ID's (Separate By Comma)</label>
        	<input type="text" name="_wcrest_blacklist" value="<?php echo $settings['_wcrest_blacklist'];?>" />
        </div>
        <div class="crest-people-field">
            <input type="submit" name="_id_blacklist" value="Update" />
        </div>
    </fieldset>
</form>
<script>
jQuery('.crest-people-office .remove').on( 'click', function( e ){
	e.preventDefault();
	jQuery( this ).closest( '.crest-people-field' ).remove();
	});
</script>
</div>