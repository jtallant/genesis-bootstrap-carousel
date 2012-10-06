<?php
/**
 * Creates settings and outputs admin menu and settings page
 */

/**
 * Return the defaults array
 *
 * @since 0.9
 */
function genesis_bootstrap_carousel_defaults() {

	$defaults = array(
		'post_type'                      => 'post',
		'posts_term'                     => '',
		'exclude_terms'                  => '',
		'include_exclude'                => '',
		'post_id'                        => '',
		'posts_num'                      => 5,
		'posts_offset'                   => 0,
		'orderby'                        => 'date',
		'carousel_interval'              => 5000,
		'carousel_arrows'                => 1,
		'carousel_no_link'               => 0,
		'carousel_width'                 => '940',
		'carousel_height'                => '380',
		'carousel_excerpt_content'       => 'excerpts',
		'carousel_excerpt_content_limit' => 150,
		'carousel_more_text'             => 'Continue Reading',
		'carousel_excerpt_show'          => 1,
		'carousel_excerpt_width'         => 100,
		'location_vertical'              => 'bottom',
		'location_horizontal'            => 'left',
		'carousel_hide_mobile'           => 1,
		'html5_docytpe'                  => 0,
		'css_js_pageids'                 => '',
		'disable_css'                    => 0,
		'disable_js'                     => 0
	);

	return apply_filters( 'bootstrap_carousel_settings_defaults', $defaults );

}

add_action( 'admin_init', 'register_genesis_bootstrap_carousel_settings' );
/**
 * This registers the settings field
 */
function register_genesis_bootstrap_carousel_settings() {

	register_setting( GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD, GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD );
	add_option( GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD, genesis_bootstrap_carousel_defaults(), '', 'yes' );

	if ( ! isset($_REQUEST['page']) || $_REQUEST['page'] != 'genesis_bootstrap_carousel' )
		return;

	if ( genesis_get_bootstrap_carousel_option( 'reset' ) ) {
		update_option( GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD, genesis_bootstrap_carousel_defaults() );

		genesis_admin_redirect( 'genesis_bootstrap_carousel', array( 'reset' => 'true' ) );
		exit;
	}

}

add_action('admin_notices', 'genesis_bootstrap_carousel_notice');
/**
 * This is the notice that displays when you successfully save or reset
 * the carousel settings.
 */
function genesis_bootstrap_carousel_notice() {

	if ( ! isset( $_REQUEST['page'] ) || $_REQUEST['page'] != 'genesis_bootstrap_carousel' )
		return;

	if ( isset( $_REQUEST['reset'] ) && 'true' == $_REQUEST['reset'] )
		echo '<div id="message" class="updated"><p><strong>' . __( 'Settings reset.', 'genesis-bootstrap-carousel' ) . '</strong></p></div>';
	elseif ( isset( $_REQUEST['settings-updated'] ) && $_REQUEST['settings-updated'] == 'true' )
		echo '<div id="message" class="updated"><p><strong>' . __( 'Settings saved.', 'genesis-bootstrap-carousel' ) . '</strong></p></div>';

}

add_action( 'admin_menu', 'genesis_bootstrap_carousel_settings_init', 15 );
/**
 * This is a necessary go-between to get our scripts and boxes loaded
 * on the theme settings page only, and not the rest of the admin
 */
function genesis_bootstrap_carousel_settings_init() {
	global $_genesis_bootstrap_carousel_settings_pagehook;

	// Add "Carousel Settings" submenu
	$_genesis_bootstrap_carousel_settings_pagehook = add_submenu_page( 'genesis', __( 'Carousel Settings', 'genesis-bootstrap-carousel' ), __( 'Carousel Settings', 'genesis-bootstrap-carousel' ), 'manage_options', 'genesis_bootstrap_carousel', 'genesis_bootstrap_carousel_settings_admin' );

	add_action( 'load-' . $_genesis_bootstrap_carousel_settings_pagehook, 'genesis_bootstrap_carousel_settings_scripts' );
	add_action( 'load-' . $_genesis_bootstrap_carousel_settings_pagehook, 'genesis_bootstrap_carousel_settings_boxes' );
}

/**
 * Loads the scripts required for the settings page
 */
function genesis_bootstrap_carousel_settings_scripts() {
	wp_enqueue_script( 'common' );
	wp_enqueue_script( 'wp-lists' );
	wp_enqueue_script( 'postbox' );
	wp_enqueue_script( 'genesis_bootstrap_carousel_admin_scripts', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), GENESIS_BOOTSTRAP_CAROUSEL_VERSION, TRUE );
}

/*
 * Loads the Meta Boxes
 */
function genesis_bootstrap_carousel_settings_boxes() {
	global $_genesis_bootstrap_carousel_settings_pagehook;

	add_meta_box( 'genesis-bootstrap-carousel-options', __( 'Genesis Bootstrap Carousel Settings', 'genesis-bootstrap-carousel' ), 'genesis_bootstrap_carousel_options_box', $_genesis_bootstrap_carousel_settings_pagehook, 'column1' );
}


add_filter( 'screen_layout_columns', 'genesis_bootstrap_carousel_settings_layout_columns', 10, 2 );
/**
 * Tell WordPress that we want only 1 column available for our meta-boxes
 */
function genesis_bootstrap_carousel_settings_layout_columns( $columns, $screen ) {
	global $_genesis_bootstrap_carousel_settings_pagehook;

	if ( $screen == $_genesis_bootstrap_carousel_settings_pagehook ) {
		// This page should have 1 column settings
		$columns[$_genesis_bootstrap_carousel_settings_pagehook] = 1;
	}

	return $columns;
}

/**
 * This function is what actually gets output to the page. It handles the markup,
 * builds the form, outputs necessary JS stuff, and fires <code>do_meta_boxes()</code>
 */
function genesis_bootstrap_carousel_settings_admin() {
		global $_genesis_bootstrap_carousel_settings_pagehook, $screen_layout_columns;

		$width = "width: 99%;";
		$hide2 = $hide3 = " display: none;";
?>
		<div id="gs" class="wrap genesis-metaboxes">
		<form method="post" action="options.php">

			<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
			<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
			<?php settings_fields( GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD ); // important!  ?>

			<?php screen_icon( 'plugins' ); ?>
			<h2>
				<?php _e( 'Genesis - Bootstrap Carousel', 'genesis-bootstrap-carousel' ); ?>
				<input type="submit" class="button-primary genesis-h2-button" value="<?php _e( 'Save Settings', 'genesis-bootstrap-carousel' ) ?>" />
				<input type="submit" class="button-highlighted genesis-h2-button" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[reset]" value="<?php _e( 'Reset Settings', 'genesis-bootstrap-carousel' ); ?>" onclick="return genesis_confirm('<?php echo esc_js( __( 'Are you sure you want to reset?', 'genesis-bootstrap-carousel' ) ); ?>');" />
			</h2>

			<div class="metabox-holder">
				<div class="postbox-container" style="<?php echo $width; ?>">
					<?php do_meta_boxes( $_genesis_bootstrap_carousel_settings_pagehook, 'column1', null ); ?>
				</div>
			</div>

			<div class="bottom-buttons">
				<input type="submit" class="button-primary" value="<?php _e('Save Settings', 'genesis-bootstrap-carousel') ?>" />
				<input type="submit" class="button-highlighted" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[reset]" value="<?php _e( 'Reset Settings', 'genesis-bootstrap-carousel' ); ?>" />
			</div>

		</form>
		</div>
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready( function($) {
				// close postboxes that should be closed
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				// postboxes setup
				postboxes.add_postbox_toggles('<?php echo $_genesis_bootstrap_carousel_settings_pagehook; ?>');
			});
			//]]>
		</script>

<?php
}

/**
 * Iterates through post types and generates a list options
 */
function gbc_post_type_options() {
	$post_types = get_post_types( array( 'public' => true ), 'names', 'and' );
	$post_types = array_filter( $post_types, 'genesis_bootstrap_carousel_exclude_post_types' );

	foreach ( $post_types as $post_type ) {
		echo '<option style="padding-right:10px;" value="', esc_attr( $post_type ), '"', selected( esc_attr( $post_type ), genesis_get_bootstrap_carousel_option( 'post_type' ), false ), '>', esc_attr( $post_type ), '</option>';
	}
}

/**
 * Generates optgroups for taxonomies and options for their terms
 */
function gbc_taxonomy_options() {
	$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
	$taxonomies = array_filter( $taxonomies, 'genesis_bootstrap_carousel_exclude_taxonomies' );
	$test = get_taxonomies( array( 'public' => true ), 'objects' );

	foreach ( $taxonomies as $taxonomy ) {
		$query_label = empty( $taxonomy->query_var ) ? $taxonomy->name : $taxonomy->query_var;

		echo '
		<optgroup label="', esc_attr( $taxonomy->labels->name ), '">
			<option style="margin-left: 5px; padding-right:10px;" value="', esc_attr( $query_label ), '"', selected( esc_attr( $query_label ), genesis_get_bootstrap_carousel_option( 'posts_term' ), false ), '>', $taxonomy->labels->all_items, '</option>';

			$terms = get_terms( $taxonomy->name, 'orderby=name&hide_empty=1' );

			foreach ( $terms as $term ) {
				echo '<option style="margin-left: 8px; padding-right:10px;" value="', esc_attr( $query_label ), ',', $term->slug, '"', selected( esc_attr( $query_label ) . ',' . $term->slug, genesis_get_bootstrap_carousel_option( 'posts_term' ), false ), '>-', esc_attr( $term->name ), '</option>';
			}
			
		echo '
		</optgroup>';
	}
}

/**
 * This function generates the form code to be used in the metaboxes
 *
 * @since 0.9
 */
function genesis_bootstrap_carousel_options_box() {
?>

	<div id="genesis-bootstrap-carousel-content-type">

		<h4><?php _e( 'Type of Content', 'genesis-bootstrap-carousel' ); ?></h4>

		<p>
			<label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[post_type]"><?php _e( 'Select a post type (post, page, or custom post type)', 'genesis-bootstrap-carousel' ); ?>?</label>
			<select id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[post_type]" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[post_type]" class="post-type-select">
				<?php gbc_post_type_options(); ?>
			</select>
		</p>

	</div>

	<div id="genesis-bootstrap-carousel-content-filter">

		<div id="genesis-bootstrap-carousel-taxonomy">

			<p>
				<strong style="display: block; font-size: 11px; margin-top: 10px;"><?php _e( 'By Taxonomy and Terms', 'genesis-bootstrap-carousel' ); ?></strong><label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[posts_term]"><?php _e( 'Choose a term to determine what slides to include', 'genesis-bootstrap-carousel' ); ?>.</label>

				<select id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[posts_term]" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[posts_term]" style="margin-top: 5px;">

					<option style="padding-right:10px;" value="" <?php selected( '', genesis_get_bootstrap_carousel_option( 'posts_term' ) ); ?>><?php _e( 'All Taxonomies and Terms', 'genesis-bootstrap-carousel' ); ?></option>

					<?php gbc_taxonomy_options(); ?>

				</select>
			</p>

			<p><strong style="display: block; font-size: 11px; margin-top: 10px;"><?php _e( 'Exclude by Taxonomy ID', 'genesis-bootstrap-carousel' ); ?></strong></p>

			<p>
				<label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[exclude_terms]"><?php printf( __( 'List which category, tag or other taxonomy IDs to exclude. (1,2,3,4 for example)', 'genesis-bootstrap-carousel' ), '<br />' ); ?></label>
			</p>

			<p>
				<input type="text" id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[exclude_terms]" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[exclude_terms]" value="<?php echo esc_attr( genesis_get_bootstrap_carousel_option( 'exclude_terms' ) ); ?>" style="width:60%;" />
			</p>

		</div>

		<p>
			<strong style="font-size:11px;margin-top:10px;"><label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[include_exclude]"><?php printf( __( 'Include or Exclude by %s ID', 'genesis-bootstrap-carousel' ), genesis_get_bootstrap_carousel_option( 'post_type' ) ); ?></label></strong>
		</p>

		<p><?php _e( 'Choose the include / exclude slides using their post / page ID in a comma-separated list. (1,2,3,4 for example)', 'genesis-bootstrap-carousel' ); ?></p>

		<p>
			<select style="margin-top: 5px;" id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[include_exclude]" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[include_exclude]">
				<option style="padding-right:10px;" value="" <?php selected( '', genesis_get_bootstrap_carousel_option( 'include_exclude' ) ); ?>><?php _e( 'Select', 'genesis-bootstrap-carousel' ); ?></option>
				<option style="padding-right:10px;" value="include" <?php selected( 'include', genesis_get_bootstrap_carousel_option( 'include_exclude' ) ); ?>><?php _e( 'Include', 'genesis-bootstrap-carousel' ); ?></option>
				<option style="padding-right:10px;" value="exclude" <?php selected( 'exclude', genesis_get_bootstrap_carousel_option( 'include_exclude' ) ); ?>><?php _e( 'Exclude', 'genesis-bootstrap-carousel' ); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[post_id]"><?php _e( 'List which', 'genesis-bootstrap-carousel' ); ?> <strong><?php echo genesis_get_bootstrap_carousel_option( 'post_type' ) . ' ' . __( 'ID', 'genesis-bootstrap-carousel' ); ?>s</strong> <?php _e( 'to include / exclude. (1,2,3,4 for example)', 'genesis-bootstrap-carousel' ); ?></label></p>
		<p>
			<input type="text" id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[post_id]" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[post_id]" value="<?php echo esc_attr( genesis_get_bootstrap_carousel_option( 'post_id' ) ); ?>" style="width:60%;" />
		</p>

		<p>
			<label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[posts_num]"><?php _e( 'Number of Slides to Show', 'genesis-bootstrap-carousel' ); ?>:</label>
			<input type="text" id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[posts_num]" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[posts_num]" value="<?php echo esc_attr( genesis_get_bootstrap_carousel_option( 'posts_num' ) ); ?>" size="2" />
		</p>

		<p>
			<label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[posts_offset]"><?php _e( 'Number of Posts to Offset', 'genesis-bootstrap-carousel' ); ?>:</label>
			<input type="text" id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[posts_offset]" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[posts_offset]" value="<?php echo esc_attr( genesis_get_bootstrap_carousel_option( 'posts_offset' ) ); ?>" size="2" />
		</p>

		<p>
			<label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[orderby]"><?php _e( 'Order By', 'genesis-bootstrap-carousel' ); ?>:</label>
			<select id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[orderby]" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[orderby]">
				<option style="padding-right:10px;" value="date" <?php selected( 'date', genesis_get_bootstrap_carousel_option( 'orderby' ) ); ?>><?php _e( 'Date', 'genesis-bootstrap-carousel' ); ?></option>
				<option style="padding-right:10px;" value="title" <?php selected( 'title', genesis_get_bootstrap_carousel_option( 'orderby' ) ); ?>><?php _e( 'Title', 'genesis-bootstrap-carousel' ); ?></option>
				<option style="padding-right:10px;" value="ID" <?php selected( 'ID', genesis_get_bootstrap_carousel_option( 'orderby' ) ); ?>><?php _e( 'ID', 'genesis-bootstrap-carousel' ); ?></option>
				<option style="padding-right:10px;" value="rand" <?php selected( 'rand', genesis_get_bootstrap_carousel_option( 'orderby' ) ); ?>><?php _e( 'Random', 'genesis-bootstrap-carousel' ); ?></option>
			</select>
		</p>

	</div>

	<hr class="div" />

	<h4><?php _e( 'Transition Settings', 'genesis-bootstrap-carousel' ); ?></h4>

		<p>
			<label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_interval]"><?php _e( 'Time Between Slides (in milliseconds)', 'genesis-bootstrap-carousel' ); ?>:
			<input type="text" id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_interval]" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_interval]" value="<?php echo genesis_get_bootstrap_carousel_option( 'carousel_interval' ); ?>" size="5" /></label>
		</p>

	<hr class="div" />

	<h4><?php _e( 'Display Settings', 'genesis-bootstrap-carousel' ); ?></h4>

		<p>
			<label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_width]"><?php _e( 'Maximum Carousel Width (in pixels)', 'genesis-bootstrap-carousel' ); ?>:
			<input type="text" id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_width]" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_width]" value="<?php echo genesis_get_bootstrap_carousel_option( 'carousel_width' ); ?>" size="5" /></label>
		</p>

		<p>
			<label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_height]"><?php _e( 'Maximum Carousel Height (in pixels)', 'genesis-bootstrap-carousel' ); ?>:
			<input type="text" id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_height]" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_height]" value="<?php echo genesis_get_bootstrap_carousel_option( 'carousel_height' ); ?>" size="5" /></label>
		</p>

		<p>
			<input type="checkbox" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_arrows]" id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_arrows]" value="1" <?php checked(1, genesis_get_bootstrap_carousel_option('carousel_arrows')); ?> /> <label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_arrows]"><?php _e( 'Display Next / Previous Arrows in Carousel?', 'genesis-bootstrap-carousel' ); ?></label>
		</p>

		<p>
			<input type="checkbox" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[html5_doctype]" id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[html5_doctype]" value="1" <?php checked(1, genesis_get_bootstrap_carousel_option('html5_doctype')); ?> /> <label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[html5_doctype]"><?php _e( 'Use html5 doctype? ( Bootstrap says it is required but it should work without it )', 'genesis-bootstrap-carousel' ); ?></label>
		</p>

		<p>
			<label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[css_js_pageids]"><?php _e( "Load carousel CSS and JS only on specific page ID(s)<br />Single page ID or comma separated list. Use -1 as the ID for the home page", 'genesis-bootstrap-carousel' ); ?>:</label>
		</p>

		<p>
			<input type="text" id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[css_js_pageids]" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[css_js_pageids]" value="<?php echo genesis_get_bootstrap_carousel_option( 'css_js_pageids' ); ?>" style="width: 60%;" />
		</p>

		<p>
			<input type="checkbox" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[disable_js]" id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[disable_js]" value="1" <?php checked(1, genesis_get_bootstrap_carousel_option('disable_js')); ?> /> <label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[disable_js]"><?php _e( 'Disable plugin JavaScript and load your own?', 'genesis-bootstrap-carousel' ); ?></label>
		</p>

		<p>
			<input type="checkbox" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[disable_css]" id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[disable_css]" value="1" <?php checked(1, genesis_get_bootstrap_carousel_option('disable_css')); ?> /> <label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[disable_css]"><?php _e( 'Disable plugin CSS and load your own?', 'genesis-bootstrap-carousel' ); ?></label>
		</p>

	<hr class="div" />

	<h4><?php _e( 'Content Settings', 'genesis-bootstrap-carousel' ); ?></h4>

		<p>
			<input type="checkbox" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_no_link]" id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_no_link]" class="link-images-to-posts" value="1" <?php checked(1, genesis_get_bootstrap_carousel_option('carousel_no_link')); ?> /> <label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_no_link]"><?php _e( 'Do not link Carousel image to Post/Page.', 'genesis-bootstrap-carousel' ); ?></label>
		</p>

		<p>
			<input type="checkbox" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_title_show]" id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_title_show]" class="show-title" value="1" <?php checked(1, genesis_get_bootstrap_carousel_option('carousel_title_show')); ?> /> <label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_title_show]"><?php _e( 'Display Post/Page Title in Carousel Caption?', 'genesis-bootstrap-carousel' ); ?></label>
		</p>
		<p>
			<input type="checkbox" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_excerpt_show]" id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_excerpt_show]" class="show-excerpt" value="1" <?php checked(1, genesis_get_bootstrap_carousel_option('carousel_excerpt_show')); ?> /> <label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_excerpt_show]"><?php _e( 'Display Content in Carousel Caption?', 'genesis-bootstrap-carousel' ); ?></label>
		</p>
		
		<p class="carousel-caption-options">
			<input type="checkbox" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_hide_mobile]" id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_hide_mobile]" value="1" <?php checked(1, genesis_get_bootstrap_carousel_option('carousel_hide_mobile')); ?> /> <label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_hide_mobile]"><?php _e( 'Hide Carousel Caption on Small Screen Sizes', 'genesis-bootstrap-carousel' ); ?></label>
		</p>
		
		<div class="carousel-caption-options">
			<div class="excerpt-options">
				<p>
					<?php _e( 'Select one of the following:', 'genesis-bootstrap-carousel' ); ?>
					<select name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_excerpt_content]" id="<?php echo GENESIS_SETTINGS_FIELD; ?>[carousel_excerpt_content]">
						<option value="full" <?php selected( 'full', genesis_get_option( 'carousel_excerpt_content', GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD ) ); ?>><?php _e( 'Display post content inside carousel capton', 'genesis-bootstrap-carousel' ); ?></option>
						<option value="excerpts" <?php selected( 'excerpts', genesis_get_option( 'carousel_excerpt_content', GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD ) ); ?>><?php _e( 'Display post excerpts inside carousel caption', 'genesis-bootstrap-carousel' ); ?></option>
					</select>
				</p>

				<p>
					<label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_more_text]"><?php _e( 'More Text (if applicable)', 'genesis-bootstrap-carousel' ); ?>:</label>
					<input type="text" id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_more_text]" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_more_text]" value="<?php echo esc_attr( genesis_get_option( 'carousel_more_text', GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD ) ); ?>" />
				</p>
			
				<p>
					<label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_excerpt_content_limit]"><?php _e( 'Limit content to', 'genesis-bootstrap-carousel' ); ?></label>
					<input type="text" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_excerpt_content_limit]" id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_excerpt_content_limit]" value="<?php echo esc_attr( genesis_option( 'carousel_excerpt_content_limit', GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD ) ); ?>" size="3" />
					<label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_excerpt_content_limit]"><?php _e( 'characters', 'genesis-bootstrap-carousel' ); ?></label>
				</p>

				<p><span class="description"><?php _e( 'Using this option will limit the text and strip all formatting from the text displayed. To use this option, choose "Display post content inside carousel caption" in the select box above.', 'genesis-bootstrap-carousel' ); ?></span></p>
			</div><!-- .excerpt-options -->

			<p>
				<label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_excerpt_width]"><?php _e( 'Carousel Caption Width (in percentage)', 'genesis-bootstrap-carousel' ); ?>:
				<input type="text" id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_excerpt_width]" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[carousel_excerpt_width]" value="<?php echo genesis_get_bootstrap_carousel_option( 'carousel_excerpt_width' ); ?>" size="5" /></label>
			</p>

			<p>
				<label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[location_vertical]"><?php _e( 'Carousel Caption Location (vertical)', 'genesis-bootstrap-carousel' ); ?>:</label>
				<select id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[location_vertical]" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[location_vertical]">
					<option style="padding-right:10px;" value="top" <?php selected( 'top', genesis_get_bootstrap_carousel_option( 'location_vertical' ) ); ?>><?php _e( 'Top', 'genesis-bootstrap-carousel' ); ?></option>
					<option style="padding-right:10px;" value="bottom" <?php selected( 'bottom', genesis_get_bootstrap_carousel_option( 'location_vertical' ) ); ?>><?php _e( 'Bottom', 'genesis-bootstrap-carousel' ); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[location_horizontal]"><?php _e( 'Carousel Caption Location (horizontal)', 'genesis-bootstrap-carousel' ); ?>:</label>
				<select id="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[location_horizontal]" name="<?php echo GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD; ?>[location_horizontal]">
					<option style="padding-right:10px;" value="left" <?php selected( 'left', genesis_get_bootstrap_carousel_option( 'location_horizontal' ) ); ?>><?php _e( 'Left', 'genesis-bootstrap-carousel' ); ?></option>
					<option style="padding-right:10px;" value="right" <?php selected( 'right', genesis_get_bootstrap_carousel_option( 'location_horizontal' ) ); ?>><?php _e( 'Right', 'genesis-bootstrap-carousel' ); ?></option>
				</select>
			</p>
		</div><!-- .carousel-caption-options -->
<?php
}

/*
 * Echos form submit button for settings page.
 */
function genesis_bootstrap_carousel_form_submit( $args = array( ) ) {
	echo '<p><input type="submit" class="button-primary" value="' . __( 'Save Changes', 'genesis-bootstrap-carousel' ) . '" /></p>';
}

/**
 * Used to exclude taxonomies and related terms from list of available terms/taxonomies in widget form().
 *
 * @since 0.1.0
 * @author Nick Croft
 *
 * @param string $taxonomy 'taxonomy' being tested
 * @return string
 */
function genesis_bootstrap_carousel_exclude_taxonomies( $taxonomy ) {

	$filters = array( '', 'nav_menu' );
	$filters = apply_filters( 'genesis_bootstrap_carousel_exclude_taxonomies', $filters );

	return ( ! in_array( $taxonomy->name, $filters ) );

}

/**
 * Used to exclude post types from list of available post_types in widget form().
 *
 * @since 0.1.0
 * @author Nick Croft
 *
 * @param string $type 'post_type' being tested
 * @return string
 */
function genesis_bootstrap_carousel_exclude_post_types( $type ) {

	$filters = array( '', 'attachment' );
	$filters = apply_filters( 'genesis_bootstrap_carousel_exclude_post_types', $filters );

	return ( ! in_array( $type, $filters ) );

}