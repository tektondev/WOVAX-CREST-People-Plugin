<style>
.crest-people-field {
	display: inline-block;
	width: 44%;
	margin-right: 4%;
	padding: 0.5rem;
}
.crest-people-field label {
	display: block;
	font-weight: bold;
}
.crest-people-field input[type="text"] {
	display: block;
	width: 98%;
	height: 30px;
	line-height: 30px;
	text-indent: 8px;
}
#people-editor {
	padding-top: 2rem;
	padding-bottom: 2rem;
}
#people-editor nav label {
	display: inline-block;
	padding: 0.5rem 2rem;
	font-size: 14px;
	text-decoration: none;
	color: #333;
	cursor: pointer;
	text-transform: uppercase;
	font-weight: bold;
}
#people-editor nav label.active {
	background-color: #333;
	color: #ddd;
}
#people-editor nav label:hover {
	background-color: #555;
	color: #ddd;
}
#people-editor nav {
	border-bottom: 2px solid #333;
	margin-bottom: 1rem;
}
#people-editor .bio-type input {
	display: none;
}
#people-editor .bio-section {
	position: absolute;
	top: -9999rem;
	left: 0;
}
#people-editor .bio-section.active {
	position: relative;
	top: 0;
	left: 0;
}
#people-editor .bio-section textarea {
	width: 100%;
	height: 200px;
}
</style>
<div class="crest-people-field">
    <label>Active Properties Shortcode</label>
    <input type="text" name="_shortcode_active_override"  value="<?php echo $settings[ '_shortcode_active_override' ];?>" />
</div>
<div class="crest-people-field">
    <label>Closed Properties Shortcode</label>
    <input type="text" name="_shortcode_closed_override"  value="<?php echo $settings[ '_shortcode_closed_override' ];?>" />
</div>
<div class="crest-people-field">
    <label>Youtube Video URL</label>
    <input type="text" name="_youtube_video_url"  value="<?php echo $settings[ '_youtube_video_url' ];?>" />
</div>
<hr />
<h3>From CREST</h3>
<?php foreach( $crest_fields as $field => $label ):?>
<div class="crest-people-field">
    <label><?php echo $label;?></label>
    <input type="text" name="<?php echo $field;?>_manual"  value="<?php if ( ! empty( $settings[ $field . '_manual' ] ) ) echo $settings[ $field . '_manual' ];?>" placeholder="<?php echo $settings[ $field ];?>" />
</div>
<?php endforeach;?>
<div class="crest-people-field">
    <label>Offices</label>
    <input type="text" name="_offices"  value="<?php echo $settings[ '_offices' ];?>" />
</div>
<div id="people-editor">
	<nav><label for="bio_type_crest" class="<?php if ( $bio_type == 'crest') echo 'active';?>">Crest Bio</label><label for="bio_type_manual" class="<?php if ( $bio_type == 'manual') echo 'active';?>">Manual Bio</label></nav>
    <div class="bio-sections">
    	<div class="bio-section<?php if ( $bio_type == 'crest') echo ' active';?>"><textarea disabled="disabled"><?php echo $post->post_content;?></textarea></div>
    	<div class="bio-section<?php if ( $bio_type == 'manual') echo ' active';?>"><?php wp_editor( $settings[ '_personbio' ], '_personbio' );?></div>
    </div>
    <div class="bio-type">
    	<input id="bio_type_crest" type="radio" name="_bio_type"  value="crest" <?php checked( 'crest', $bio_type);?> />
        <input id="bio_type_manual" type="radio" name="_bio_type"  value="manual" <?php checked( 'manual', $bio_type);?> />
    </div>
</div>
<script>
jQuery( 'body' ).on(
	'change',
	'#people-editor .bio-type input',
	function(){
		var i = jQuery( this ).index();
		jQuery( '#people-editor nav label' ).eq( i ).addClass('active').siblings().removeClass('active');
		jQuery( '#people-editor .bio-sections .bio-section' ).eq( i ).addClass('active').siblings().removeClass('active');
	} 
);
</script>