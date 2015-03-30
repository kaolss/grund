<?php

/*
* Author: Karin H Olsson
* Author url: http://www.kobotolo.se
* Template Name: Masonry Page
*/

remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'masonry_loop' );
wp_enqueue_script( 'masonry' );
wp_enqueue_script( 'masonry-init', get_bloginfo( 'stylesheet_directory' ) . '/js/masonry-init.js', '', '', true ); 

add_filter( 'body_class', 'masonry_body_class' );
function masonry_body_class( $classes ) {
    $classes[] = 'masonry-page';
    return $classes;
}

function add_boxes($cat) {
    $s="";
    global $wpdb;
    $cat2=explode(",",$cat);

    foreach ($cat2 as $c ):
	if ($c[0]==="-") :
		if ($s!=="") $s.= " AND";
		if ($s==="") $s.= "(";
		$s .= " terms1.term_id <> $c[1] ";  
	    else:
		if ($s!=="") $s.= " OR";
		if ($s==="") $s.= "(";
	    $s .= " terms1.term_id = $c ";  
	endif;
    endforeach;
    if ($s!=="") $s.= ") AND";

    $category_tags = $wpdb->get_results("
	SELECT DISTINCT
	    terms2.slug as tag_ID,
	    terms2.name as tag_name,
	    t2.count as posts_with_tag
	FROM
	    $wpdb->posts as p1
	    LEFT JOIN $wpdb->term_relationships as r1 ON p1.ID = r1.object_ID
	    LEFT JOIN $wpdb->term_taxonomy as t1 ON r1.term_taxonomy_id = t1.term_taxonomy_id
	    LEFT JOIN $wpdb->terms as terms1 ON t1.term_id = terms1.term_id,

	$wpdb->posts as p2
	    LEFT JOIN $wpdb->term_relationships as r2 ON p2.ID = r2.object_ID
	    LEFT JOIN $wpdb->term_taxonomy as t2 ON r2.term_taxonomy_id = t2.term_taxonomy_id
	    LEFT JOIN $wpdb->terms as terms2 ON t2.term_id = terms2.term_id
	WHERE (
	    t1.taxonomy = 'category' AND
	    p1.post_status = 'publish' AND
	    $s 
	    t2.taxonomy = 'post_tag' AND
	    p2.post_status = 'publish' AND
	    p1.ID = p2.ID
	)
    ");

    if (count($category_tags)==0) return;
    $check = get_post_meta( get_the_id(),'check',true );
    if(  empty( $check ) ) $check=0;
    if ($check==0) {?> 
	<button id="entry" name="entry">Alla</button>
	<?php foreach ($category_tags as $tag) : ?>
	    <button id="tag-<?php echo $tag->tag_ID;?>" name ="tag-<?php echo $tag->tag_ID;?>"><?php echo $tag->tag_name;?></button>
	<?php endforeach;

    } 
    else { ?>    
	<br>
	<span>Alla:</span>
	<input class="myinput" type="checkbox" name="entry" id="entry" checked value="Alla">
	<?php    foreach ($category_tags as $tag) : ?>
	    <?php echo $tag->tag_name;?>
	    <input class="myinput" type="checkbox" name="tag-<?php echo $tag->tag_ID;?>" id="tag-<?php echo $tag->tag_ID;?>" value="Bike">
	<?php endforeach;
    }
}


function masonry_loop() {

	$query_args = wp_parse_args(
		genesis_get_custom_field( 'query_args' ),
		array(
			'showposts'        => '-1',//genesis_get_option( 'blog_cat_num' ),
		)
	);
    if (is_page()) add_boxes($query_args['cat']);
    if (is_category()) add_boxes(get_query_var('cat'));
    genesis_custom_loop( $query_args );
}

add_action ('loop_start','masonry_classes2');
function masonry_classes2 () {
 	echo '<div class="masonry-content">';
 	echo '<div class="grid-sizer"></div>';
	echo '<div class="gutter-width"></div>';
}
add_action ('loop_end','masonry_stop');
function masonry_stop () {
 	echo '</div>';
}


add_action( 'genesis_entry_header', 'kobotolo_image', 9 );
function kobotolo_image() {
    $image_args = array(
		'size' => 'masonry_thumb'
    );

    $image = genesis_get_image( $image_args );
    echo '<a rel="bookmark" href="'. get_permalink() .'">'. $image .'</a>';
}

genesis();
