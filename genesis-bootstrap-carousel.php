<?php
/*
	Plugin Name: Genesis Bootstrap Carousel
	Plugin URI: https://github.com/jtallant/genesis-bootstrap-carousel
	Description: A responsive image carousel for Genesis.
	Author: jtallant
	Author URI: http://justintallant.com

	Version: 0.1.0

	License: GNU General Public License v2.0 (or later)
	License URI: http://www.opensource.org/licenses/gpl-license.php
*/

define( 'GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD', 'genesis_bootstrap_carousel_settings' );
define( 'GENESIS_BOOTSTRAP_CAROUSEL_VERSION', '0.1.0' );

add_action( 'after_setup_theme', 'genesis_bootstrap_carousel_init', 15 );
/**
 * Loads required files and adds image via Genesis Init Hook
 */
function genesis_bootstrap_carousel_init() {

	/** require Genesis */
	if ( ! function_exists( 'genesis_get_option' ) )
		return;

	// translation support
	// TODO: Generate translation files
	// load_plugin_textdomain( 'genesis-bootstrap-carousel', false, '/genesis-bootstrap-carousel/languages/' );
	
	/** hook all frontend slider functions here to ensure Genesis is active **/
	add_action( 'wp_enqueue_scripts', 'genesis_bootstrap_carousel_scripts' );
	add_action( 'wp_print_styles', 'genesis_bootstrap_carousel_styles' );
	add_action( 'wp_head', 'genesis_bootstrap_carousel_head', 1 );
	add_action( 'wp_footer', 'genesis_bootstrap_carousel_params', 999 );
	add_action( 'widgets_init', 'genesis_bootstrap_carousel_register' );

	if ( 1 == genesis_get_bootstrap_carousel_option( 'html5_doctype' ) ) {
		remove_action('genesis_doctype', 'genesis_do_doctype');
		add_action('genesis_doctype', 'html5_doctype');
	}
	
	/** Include Admin file */
	if ( is_admin() ) require_once( dirname( __FILE__ ) . '/admin.php' );

	/** Add new image size */
	add_image_size( 'slider', ( int ) genesis_get_bootstrap_carousel_option( 'carousel_width' ), ( int ) genesis_get_bootstrap_carousel_option( 'carousel_height' ), TRUE );

}

add_action( 'genesis_settings_sanitizer_init', 'genesis_bootstrap_carousel_sanitization' );
/**
 * Add settings to Genesis sanitization
 *
 */
function genesis_bootstrap_carousel_sanitization() {

	genesis_add_option_filter( 'one_zero', GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD,
		array(
			'carousel_arrows',
			'carousel_excerpt_show',
			'carousel_title_show',
			'carousel_hide_mobile',
			'carousel_no_link',
			'html5_doctype',
			'disable_js',
			'disable_css'
		) );
	genesis_add_option_filter( 'no_html', GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD,
		array(
			'post_type',
			'posts_term',
			'exclude_terms',
			'include_exclude',
			'post_id',
			'posts_num',
			'posts_offset',
			'orderby',
			'carousel_interval',
			'carousel_height',
			'carousel_width',
			'carousel_excerpt_content',
			'carousel_excerpt_content_limit',
			'carousel_more_text',
			'carousel_excerpt_width',
			'location_vertical',
			'location_horizontal'
		) );
}

function html5_doctype() {

	$doctype = '<!DOCTYPE html>
	<html dir="' . get_bloginfo("text_direction") . '" lang="' . get_bloginfo("language") . '">
	<meta http-equiv="Content-Type" content="' . get_bloginfo( "html_type" ) . ' charset=' . get_bloginfo( "charset" ) . '" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	';
	$doctype = preg_replace('/\t/', '', $doctype);
	echo $doctype;
}

/**
 * Load the script files
 */
function genesis_bootstrap_carousel_scripts() {

	if ( 1 == genesis_get_bootstrap_carousel_option( 'disable_js') )
		return;

	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'bootstrap-carousel', plugins_url('js/carousel.min.js', __FILE__), array( 'jquery' ), GENESIS_BOOTSTRAP_CAROUSEL_VERSION, TRUE );

}

/**
 * Load the CSS files
 */
function genesis_bootstrap_carousel_styles() {

	if ( 1 == genesis_get_bootstrap_carousel_option( 'disable_css' ) )
		return;

	/** standard carousel styles */
	// wp_register_style( 'carousel_styles', plugins_url('carousel.css', __FILE__), array(), GENESIS_BOOTSTRAP_CAROUSEL_VERSION );
	wp_register_style( 'carousel_styles', plugins_url('carousel.min.css', __FILE__), array(), GENESIS_BOOTSTRAP_CAROUSEL_VERSION );
	wp_enqueue_style( 'carousel_styles' );

}

/**
 * Loads scripts and styles via wp_head hook.
 */
function genesis_bootstrap_carousel_head() {

		if ( 1 == genesis_get_bootstrap_carousel_option( 'disable_css' ) )
			return;

		$width = ( int ) genesis_get_bootstrap_carousel_option( 'carousel_width' );
		$height = ( int ) genesis_get_bootstrap_carousel_option( 'carousel_height' );

		$slideInfoWidth = ( int ) genesis_get_bootstrap_carousel_option( 'carousel_excerpt_width' );

		$vertical = genesis_get_bootstrap_carousel_option( 'location_vertical' );
		$horizontal = genesis_get_bootstrap_carousel_option( 'location_horizontal' );
		
		$hide_mobile = genesis_get_bootstrap_carousel_option( 'carousel_hide_mobile' );

		echo '
		<style type="text/css">
			.carousel-caption { width: ' . $slideInfoWidth . '%; }
			.carousel-caption { ' . $vertical . ': 0; }
			.carousel-caption { '. $horizontal . ': 0; }
			.carousel { max-width: ' . $width . 'px; max-height: ' . $height . 'px; }
		</style>';
		
		if ( $hide_mobile == 1 ) {
		echo '
		<style type="text/css"> 
			@media (max-width: 767px) {
				.carousel-caption { display: none !important; }
			}
		</style> ';
		}
}

/**
 * Outputs slider script on wp_footer hook.
 */
function genesis_bootstrap_carousel_params() {

	if ( 1 == genesis_get_bootstrap_carousel_option( 'disable_js' ) )
		return;

	$interval = ( int ) genesis_get_bootstrap_carousel_option( 'carousel_interval' );

	$output = '
	jQuery(document).ready(function($) {
		$(".carousel").carousel({
			interval: ' . $interval . '
	    });
	});';

	echo '<script type="text/javascript">' . $output . '</script>';
}

/**
 * Registers the slider widget
 */
function genesis_bootstrap_carousel_register() {
	register_widget( 'Genesis_Bootstrap_Carousel_Widget' );
}

/** Creates read more link after excerpt */
function genesis_bootstrap_carousel_excerpt_more( $more ) {
	global $post;
	static $read_more = null;

	if ( $read_more === null )
		$read_more = genesis_get_bootstrap_carousel_option( 'carousel_more_text' );

	if ( !$read_more )
		return '';

	return '&hellip; <a href="'. get_permalink( $post->ID ) . '">' . __( $read_more, 'genesis-bootstrap-carousel' ) . '</a>';
}

/**
 * Carousel Widget Class
 */
class Genesis_Bootstrap_Carousel_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'bootstrap_carousel_widget', // Base ID
			'Genesis - Bootstrap Carousel', // Name
			array( 'description' => __( 'Displays a carousel inside a widget area', 'genesis-bootstrap-carousel' ) )
		);
	}

	function save_settings( $settings ) {
		$settings['_multiwidget'] = 0;
		update_option( $this->option_name, $settings );
	}

	// display widget
	// TODO: Split display logic into smaller functions
	function widget( $args, $instance ) {
		extract( $args );

		echo $before_widget;

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		if ( $title )
			echo $before_title . $title . $after_title;

		$term_args = array( );

		if ( 'page' != genesis_get_bootstrap_carousel_option( 'post_type' ) ) {

			if ( genesis_get_bootstrap_carousel_option( 'posts_term' ) ) {

				$posts_term = explode( ',', genesis_get_bootstrap_carousel_option( 'posts_term' ) );

				if ( 'category' == $posts_term['0'] )
					$posts_term['0'] = 'category_name';

				if ( 'post_tag' == $posts_term['0'] )
					$posts_term['0'] = 'tag';

				if ( isset( $posts_term['1'] ) )
					$term_args[$posts_term['0']] = $posts_term['1'];

			}

			if ( !empty( $posts_term['0'] ) ) {

				if ( 'category' == $posts_term['0'] )
					$taxonomy = 'category';

				elseif ( 'post_tag' == $posts_term['0'] )
					$taxonomy = 'post_tag';

				else
					$taxonomy = $posts_term['0'];

			} else {

				$taxonomy = 'category';

			}

			if ( genesis_get_bootstrap_carousel_option( 'exclude_terms' ) ) {

				$exclude_terms = explode( ',', str_replace( ' ', '', genesis_get_bootstrap_carousel_option( 'exclude_terms' ) ) );
				$term_args[$taxonomy . '__not_in'] = $exclude_terms;

			}
		}

		if ( genesis_get_bootstrap_carousel_option( 'posts_offset' ) ) {
			$myOffset = genesis_get_bootstrap_carousel_option( 'posts_offset' );
			$term_args['offset'] = $myOffset;
		}

		if ( genesis_get_bootstrap_carousel_option( 'post_id' ) ) {
			$IDs = explode( ',', str_replace( ' ', '', genesis_get_bootstrap_carousel_option( 'post_id' ) ) );
			if ( 'include' == genesis_get_bootstrap_carousel_option( 'include_exclude' ) )
				$term_args['post__in'] = $IDs;
			else
				$term_args['post__not_in'] = $IDs;
		}

		$query_args = array_merge( $term_args, array(
			'post_type' => genesis_get_bootstrap_carousel_option( 'post_type' ),
			'posts_per_page' => genesis_get_bootstrap_carousel_option( 'posts_num' ),
			'orderby' => genesis_get_bootstrap_carousel_option( 'orderby' ),
			'order' => genesis_get_bootstrap_carousel_option( 'order' ),
			'meta_key' => genesis_get_bootstrap_carousel_option( 'meta_key' )
		) );

		$query_args = apply_filters( 'genesis_bootstrap_carousel_query_args', $query_args );
		add_filter( 'excerpt_more', 'genesis_bootstrap_carousel_excerpt_more' );

	echo '
	<div id="tbsCarousel" class="carousel slide">	
		<div class="carousel-inner">';
			$show_arrows = genesis_get_bootstrap_carousel_option( 'carousel_arrows' );
			$slider_posts = new WP_Query( $query_args );

			if ( $slider_posts->have_posts() ) {
				$count = 0;
				$show_excerpt = genesis_get_bootstrap_carousel_option( 'carousel_excerpt_show' );
				$show_title = genesis_get_bootstrap_carousel_option( 'carousel_title_show' );
				$show_type = genesis_get_bootstrap_carousel_option( 'carousel_excerpt_content' );
				$show_limit = genesis_get_bootstrap_carousel_option( 'carousel_excerpt_content_limit' );
				$more_text = genesis_get_bootstrap_carousel_option( 'carousel_more_text' );
				$no_image_link = genesis_get_bootstrap_carousel_option( 'carousel_no_link' );
			}


			while ( $slider_posts->have_posts() ) : $slider_posts->the_post();
				$count++;
				$class = ( $count == 1 ) ? 'item active' : 'item';

				echo '<div class="', $class, '">';

					if ( $no_image_link ) {
						echo '<img src="', genesis_get_image( "format=url&size=slider" ), '" alt="', get_the_title(), '" />';
					} else {
						echo '
						<a href="', get_permalink(), '" rel="bookmark">
							<img src="', genesis_get_image( "format=url&size=slider" ), '" alt="', get_the_title(), '" />
						</a>';
					}

					if ( $show_excerpt == 1 || $show_title == 1 ) {
						echo '<div class="carousel-caption">';

							if ( $show_title == 1 ) {
								echo '<h2><a href="', get_permalink(), '" rel="bookmark">', get_the_title(), '</a></h2>';
							}

							if ( $show_excerpt == 1 ) {

								if ( $show_type != 'full' ) {
									the_excerpt();
								} elseif ( $show_limit ) {
									the_content_limit( (int)$show_limit, esc_html( $more_text ) );
								} else {
									the_content( esc_html( $more_text ) );	
								}

							}

						echo '</div><!-- end .carousel-caption -->';
					}

				echo '</div><!-- end .item -->';

			endwhile;

		echo '
		</div><!-- end .carousel-inner -->';

		if ( $show_arrows ) {
			echo '
			<a class="carousel-control left" href="#tbsCarousel" data-slide="prev">&lsaquo;</a>
			<a class="carousel-control right" href="#tbsCarousel" data-slide="next">&rsaquo;</a>';
		}

	echo '
	</div><!-- end #tbsCarousel -->';

	echo $after_widget;
	wp_reset_postdata();
	remove_filter( 'excerpt_more', 'genesis_bootstrap_carousel_excerpt_more' );

	}

	/** Widget options */
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = $instance['title'];

		echo '<p><label for="', $this->get_field_id( 'title' ), '">'; _e( 'Title:', 'genesis-bootstrap-carousel' ); echo '<input class="widefat" id="', $this->get_field_id( 'title' ), '" name="', $this->get_field_name( 'title' ), '" type="text" value="', esc_attr( $title ), '" /></label></p>';
		printf( __( '<p>To configure slider options, please go to the <a href="%s">Carousel Settings</a> page.</p>', 'genesis-bootstrap-carousel' ), menu_page_url( 'genesis_bootstrap_carousel', 0 ) );
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '' ) );
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}

}

/**
 * Returns Carousel Option
 *
 * @param string $key key value for option
 * @return string
 */
function genesis_get_bootstrap_carousel_option( $key ) {
	return genesis_get_option( $key, GENESIS_BOOTSTRAP_CAROUSEL_SETTINGS_FIELD );
}

/**
 * Echos Carousel Option
 *
 * @param string $key key value for option
 */
function genesis_bootstrap_carousel_option( $key ) {

	if ( ! genesis_get_bootstrap_carousel_option( $key ) )
		return false;

	echo genesis_get_bootstrap_carousel_option( $key );
}