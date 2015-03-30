<?php

/*
* Author: Karin H Olsson
* Author url: http://www.kobotolo.se
*/
    
function related_posts_tags () {
    global $post;
    $count = 0;
    $postIDs = array( $post->ID );
    $related = '';
    $tags = wp_get_post_tags( $post->ID );
	foreach ( $tags as $tag ) {
	    $tagID[] = $tag->term_id;
	}
        $args = array(
	'tag__in'               => $tagID,
	'post__not_in'          => $postIDs,
	'showposts'             => 5,
	'ignore_sticky_posts'   => 1,
	'tax_query'             => array(
	array(
	    'taxonomy'  => 'post_format',
	    'field'     => 'slug',
	    'terms'     => array(
		'post-format-link',
		'post-format-status',
		'post-format-aside',
		'post-format-quote'
		),
		'operator'  => 'NOT IN'
	)
	)
	);
    $tag_query = new WP_Query( $args );
    if ( $tag_query->have_posts() ) {
	while ( $tag_query->have_posts() ) {
	    $tag_query->the_post();
	    $img = genesis_get_image() ? genesis_get_image( array( 'size' => 'related_thumb' ) ) : '<img src="' . get_bloginfo( 'stylesheet_directory' ) . '/images/related.png" alt="' . get_the_title() . '" />'; 
                    $related .= '<li><a href="' . get_permalink() . '" rel="bookmark" title="Permanent Link to' . get_the_title() . '">' . $img . get_the_title() . '</a></li>';
		$postIDs[] = $post->ID;
	    $count++;
	}
    }
    if ( $related ) {
	printf( '<div class="related-posts"><h3>Liknande poster</h3><ul>%s</ul></div>', $related );
    }
    wp_reset_query();
    }

add_action( 'genesis_after_entry', 'related_posts_tags' );
genesis();
