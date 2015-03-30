<?php
/*
File Name: Category Template
Author: Karin H Olsson
Based on plugin : Category template from http://en.bainternet.info. Many thanks
*/
/*  This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

*/
if (basename($_SERVER['PHP_SELF']) == basename (__FILE__)) {
	die('Sorry, but you cannot access this page directly.');
}
if (!class_exists('Custom_Category_Template')){
    class Custom_Category_Template{
	public function __construct()
	{
	add_filter( 'category_template', array($this,'get_custom_category_template' ));
	add_action ( 'edit_category_form_fields', array($this,'category_template_meta_box'));
	add_action( 'category_add_form_fields', array( &$this, 'category_template_meta_box') );
	add_action( 'created_category', array( &$this, 'save_category_template' ));
	add_action ( 'edited_category', array($this,'save_category_template'));
	do_action('Custom_Category_Template_constructor',$this);
    }
		
    public function category_template_meta_box( $tag ) {
	$t_id = $tag->term_id;
	$cat_meta = get_option( "category_templates");
	$template = isset($cat_meta[$t_id]) ? $cat_meta[$t_id] : false;
	?>
	<tr class="form-field">
	<th scope="row" valign="top"><label for="cat_Image_url"><?php _e('Category Template'); ?></label></th>
	<td>
	    <select name="cat_template" id="cat_template">
		<option value='default'><?php _e('Default Template'); ?></option>
		<?php page_template_dropdown($template); ?>
	    </select>
	    <br />
	    <span class="description"><?php _e('Select a specific template for this category'); ?></span>
	</td>
	</tr>
	<?php
	    do_action('Custom_Category_Template_ADD_FIELDS',$tag);
	}

	public function save_category_template( $term_id ) {
	    if ( isset( $_POST['cat_template'] )) {
		$cat_meta = get_option( "category_templates");
		$cat_meta[$term_id] = $_POST['cat_template'];
		update_option( "category_templates", $cat_meta );
		do_action('Custom_Category_Template_SAVE_FIELDS',$term_id);
	    }
	}

    function get_custom_category_template( $category_template ) {
	$cat_ID = absint( get_query_var('cat') );
	$cat_meta = get_option('category_templates');
	if (isset($cat_meta[$cat_ID]) && $cat_meta[$cat_ID] != 'default' ){
		$temp = locate_template($cat_meta[$cat_ID]);
		if (!empty($temp))
			return apply_filters("Custom_Category_Template_found",$temp);
	}
	return $category_template;
    }
    }//end class
}//end if

$cat_template = new Custom_Category_Template();