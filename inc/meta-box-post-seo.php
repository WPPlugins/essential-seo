<?php
/**
 * Adds the SEO meta box to the post editing screen for public post types.  This feature allows the post author 
 * to set a custom title and description for the post, which will be viewed on the singular post page.  
 * To use this feature, the theme must support the 'essential-seo-seo' feature.  The functions in this file create
 * the SEO meta box and save the settings chosen by the user when the post is saved.
 *
 * @package    EssentialSEO
 * @subpackage Inc
 * @author     James Geiger <james@seamlessthemes.com>
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2013, James Geiger and Justin Tadlock
 * @link       http://seamlessthemes.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Add the post SEO meta box on the 'add_meta_boxes' hook. */
add_action( 'add_meta_boxes', 'essential_seo_meta_box_post_add_seo', 10, 2 );
add_action( 'add_meta_boxes', 'essential_seo_meta_box_post_remove_seo', 10, 2 );

/* Save the post SEO meta box data on the 'save_post' hook. */
add_action( 'save_post', 'essential_seo_meta_box_post_save_seo', 10, 2 );
add_action( 'add_attachment', 'essential_seo_meta_box_post_save_seo' );
add_action( 'edit_attachment', 'essential_seo_meta_box_post_save_seo' );

/**
 * Adds the post SEO meta box for all public post types.
 *
 * @since 0.1.0
 * @param string $post_type The post type of the current post being edited.
 * @param object $post The current post being edited.
 * @return void
 */
function essential_seo_meta_box_post_add_seo( $post_type, $post ) {

	$post_type_object = get_post_type_object( $post_type );

	/* Only add meta box if current user can edit, add, or delete meta for the post. */
	if ( ( true === $post_type_object->public ) && ( current_user_can( 'edit_post_meta', $post->ID ) || current_user_can( 'add_post_meta', $post->ID ) || current_user_can( 'delete_post_meta', $post->ID ) ) )
		add_meta_box( 'essential-seo-post-seo', __( 'SEO', 'essential-seo' ), 'essential_seo_meta_box_post_display_seo', $post_type, 'normal', 'high' );
}

/**
 * Remove the meta box from some post types.
 *
 * @since 1.3.0
 * @param string $post_type The post type of the current post being edited.
 * @param object $post The current post being edited.
 * @return void
 */ 
function essential_seo_meta_box_post_remove_seo( $post_type, $post ) {

	/* Removes post stylesheets support of the bbPress 'topic' post type. */
	if ( function_exists( 'bbp_get_topic_post_type' ) && bbp_get_topic_post_type() == $post_type )
		remove_meta_box( 'essential-seo-post-seo', bbp_get_topic_post_type(), 'normal' );

	/* Removes post stylesheets support of the bbPress 'reply' post type. */
	elseif ( function_exists( 'bbp_get_reply_post_type' ) && bbp_get_reply_post_type() == $post_type )
		remove_meta_box( 'essential-seo-post-seo', bbp_get_reply_post_type(), 'normal' );
}

/**
 * Displays the post SEO meta box.
 *
 * @since 0.1.0
 * @return void
 */
function essential_seo_meta_box_post_display_seo( $object, $box ) {

	wp_nonce_field( basename( __FILE__ ), 'essential-seo-post-seo' ); ?>

	<p>
		<label for="essential-seo-document-title"><?php _e( 'Document Title:', 'essential-seo' ); ?></label>
		<br />
		<input type="text" name="essential-seo-document-title" id="essential-seo-document-title" value="<?php echo esc_attr( get_post_meta( $object->ID, 'Title', true ) ); ?>" size="30" tabindex="30" style="width: 99%;" />
	</p>

	<p>
		<label for="essential-seo-meta-description"><?php _e( 'Meta Description:', 'essential-seo' ); ?></label>
		<br />
		<textarea name="essential-seo-meta-description" id="essential-seo-meta-description" cols="60" rows="2" tabindex="30" style="width: 99%;"><?php echo esc_textarea( get_post_meta( $object->ID, 'Description', true ) ); ?></textarea>
	</p>

<?php }

/**
 * Saves the post SEO meta box settings as post metadata.
 *
 * @since 0.1.0
 * @param int $post_id The ID of the current post being saved.
 * @param int $post The post object currently being saved.
 */
function essential_seo_meta_box_post_save_seo( $post_id, $post = '' ) {

	/* Fix for attachment save issue in WordPress 3.5. @link http://core.trac.wordpress.org/ticket/21963 */
	if ( !is_object( $post ) )
		$post = get_post();

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['essential-seo-post-seo'] ) || !wp_verify_nonce( $_POST['essential-seo-post-seo'], basename( __FILE__ ) ) )
		return $post_id;

	$meta = array(
		'Title' => 	$_POST['essential-seo-document-title'],
		'Description' => 	$_POST['essential-seo-meta-description']
	);

	foreach ( $meta as $meta_key => $new_meta_value ) {

		/* Get the meta value of the custom field key. */
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		/* If there is no new meta value but an old value exists, delete it. */
		if ( current_user_can( 'delete_post_meta', $post_id, $meta_key ) && '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, $meta_key, $meta_value );

		/* If a new meta value was added and there was no previous value, add it. */
		elseif ( current_user_can( 'add_post_meta', $post_id, $meta_key ) && $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, $meta_key, $new_meta_value, true );

		/* If the new meta value does not match the old value, update it. */
		elseif ( current_user_can( 'edit_post_meta', $post_id, $meta_key ) && $new_meta_value && $new_meta_value != $meta_value )
			update_post_meta( $post_id, $meta_key, $new_meta_value );
	}
}

?>