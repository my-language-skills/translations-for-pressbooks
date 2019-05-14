<?php

/**
 *
 *File responsible for adding option to mark original content
 */
add_action('admin_enqueue_scripts', 'om_enqueue_scripts');
add_action('wp_ajax_efp_mark_as_original', 'mark_as_original', 1);
add_filter( 'wpmu_blogs_columns', 'add_original_column' );
add_action( 'manage_sites_custom_column', 'render_original_column', 1, 3 );


function om_enqueue_scripts () {
	wp_enqueue_script( 'original-mark-script', plugin_dir_url( __FILE__ ).'assets/scripts/original-mark-admin.js');
}

function mark_as_original () {

	if ( ! current_user_can( 'manage_network' ) || ! check_ajax_referer( 'pressbooks-aldine-admin' ) ) {
		return;
	}

	$blog_id = absint( $_POST['book_id'] ); //absolute the post book_id 
	$is_original = $_POST['is_original'];

	if ( $is_original === 'true' ) {
		update_blog_option( $blog_id, 'efp_publisher_is_original', 1 );
	} else {
		delete_blog_option( $blog_id, 'efp_publisher_is_original' );
	}
}

function add_original_column ($columns) {
	$columns['is_original'] = __( 'Featured Book', 'extensions-for-pressbooks' );
	return $columns;
}

function render_original_column ($column, $blog_id ) {
	if ( 'is_original' === $column && ! is_main_site( $blog_id ) ) { ?>
		<input class="is-original" type="checkbox" name="is_original" value="1" aria-label="<?php echo esc_attr_x( 'Mark As Original Content', 'extensions-for-pressbooks' ); ?>" <?php checked( get_blog_option( $blog_id, 'efp_publisher_is_original' ), 1 ); ?> />
	<?php }
}
