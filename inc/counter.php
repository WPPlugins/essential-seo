<?php
/**
 * Counter Example:
 */


/* character counter */
add_action( 'admin_enqueue_scripts', 'essential_seo_character_counter' );


/**
 * Enqueue Character Counter Script
 * 
 * @since 0.1.0
 */
function essential_seo_character_counter() {

	/* Register post character counter */
	wp_register_script( 'essential-seo-counter', ESSENTIAL_SEO_URI . 'js/counter.js', array('jquery'), ESSENTIAL_SEO_VERSION );

	/* globalize object */
	global $pagenow, $post_type;

	/* get all public post types */
	$public_post_types = get_post_types( array( 'public' => true ) );

	/* in post edit screen */
	if ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ){

		/* only in public post type */
		if ( in_array( $post_type, $public_post_types )  ) {
			wp_enqueue_script( 'essential-seo-counter' );
			wp_localize_script('essential-seo-counter', 'essential_seo_vars',
				array(
					'title' => '<p style="text-align:left;">' . _x( 'Title length:', 'counter', 'essential-seo' ) . '<input type="text" value="0" maxlength="3" size="3" id="counter-title" readonly="">' . _x( 'character(s). Most search engines use a maximum of 60 chars for the title.', 'counter', 'essential-seo') . '</p>',
					'description' => '<p style="text-align:left;">' . _x( 'Meta Description length:', 'counter', 'essential-seo' ) . '<input type="text" value="0" maxlength="3" size="3" id="counter-desc" readonly="">' . _x( 'character(s). Most search engines use a maximum of 160 chars for the description.', 'counter', 'essential-seo') . '</p>',
				)
			);
		}
	}
}