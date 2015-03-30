<?php

/*
* Author: Karin H Olsson
* Author url: http://www.kobotolo.se
* Template Name: Landing Page
*/

remove_action( 'genesis_header', 'genesis_site_title' );
remove_action( 'genesis_header', 'genesis_do_header' );
remove_action( 'genesis_header', 'genesis_header_markup_close', 15 ) ;
remove_action( 'genesis_after_header', 'genesis_do_nav' );

remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

add_filter( 'body_class', 'landing_body_class' );
function landing_body_class( $classes ) {
    $classes[] = 'landing-page';
    return $classes;
}

add_action ('genesis_entry_content','landing_content');
function landing_content() {  ?>
    <div class="logo">
	<?php   $image= get_field( "logo" );?>
	<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
    
    </div>
    <div class="one-half first">
	<?php    do_action('genesis_site_title');the_field( "maintext" );?>
    </div>
    <div class="one-fourth">
	<?php   
	$arr = array(
        'format' => 'html',
        'size' => 'masonry_thumb',
        'num' => 0,
        'attr' => array( 'class' => 'post-image alignright' )
	    );
	$img = genesis_get_image( $arr);
	printf( '<a href="%s" title="%s">%s</a>', get_permalink(), the_title_attribute( 'echo=0' ), $img );		?>
	<div class="cta-buttons">
	    <a href="http://kobotolo.se"><button>L&auml;s mer p&aring; v&aring;r hemsida</button></a>
	    <a href="http://kobotolo.se"><button>Be oss kontakta dig</button></a>
	    <a href="http://kobotolo.se"><button>Maila oss</button></a>
    	</div>        
    </div>
    <div class="one-fourth">
	<?php   $value = the_field( "funktioner" );?>
    </div>
    <div class="first"></div>
<?php }

genesis();
