<?php
/**
 * Prevent file from being accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WP_Simple_Flexslider_Frontend{
	var $prefix;

	/**
	 * Construct the class
	 */
	public function __construct(){
		$this->prefix = "_wp_simple_flexslider_";

		add_action( 'wp_head', 					array( $this, 'enqueue_scripts' ) );
		add_shortcode( 'wp_simple_flexslider', 	array( $this, 'register_shortcode') );
	}

	/**
	 * Appending scripts for Flexslider on the front end side
	 * 
	 * @access public
	 * @since 0.1
	 * @return void
	 */
	public function enqueue_scripts () {

		$visibility = apply_filters( 'wp_simple_flexslider_insert_frontend_script', true );

		if( $visibility ) {
			wp_enqueue_style( 'jquery-flexslider', WP_SIMPLE_FLEXSLIDER_URL . 'flexslider/flexslider.css', array(), '2.3.0', 'all' );
			wp_register_script( 'jquery-flexslider', WP_SIMPLE_FLEXSLIDER_URL . 'flexslider/jquery.flexslider-min.js', array( 'jquery'), '2.3.0', 'all' );
			wp_enqueue_script( 'wc-simple-flexslider-frontend', WP_SIMPLE_FLEXSLIDER_URL . 'js/wp-simple-flexslider-frontend.js', array( 'jquery', 'jquery-flexslider' ), '0.1', 'all' );
		}
	}

	/**
	 * Registering wp simple flexslider shortcode
	 * 
	 * @access public
	 * @since 0.1
	 * @return string
	 */
	public function register_shortcode ( $atts ) {
		$args = shortcode_atts( array(
			'id' => false
		), $atts );

		if( $args['id'] ){

			ob_start();

			$this->the_slideshow( intval( $args['id'] ) );

			return ob_get_clean();
		} else {
			return;
		}
	}

	/**
	 * Outputting slideshow
	 * 
	 * @access public
	 * @since 0.1
	 * @param int 		post id
	 * @return void
	 */
	public function the_slideshow( $post_id ){

		$slideshow = get_post_meta( $post_id, "{$this->prefix}data", true );

		$defaults_slide = array(
			'slide_id' 			 => false,
			'slide_url' 		 => false,
			'slide_caption' 	 => false,
			'slide_target_blank' => false
		);		

		/**
		 * Check if slideshow data exists
		 */
		if( $slideshow && is_array( $slideshow ) && ! empty( $slideshow ) ) :

			echo '<div class="wp-simple-flexslider">';

			echo '<div class="slides">';

			foreach ( $slideshow as $slide ):

				$slide = wp_parse_args( $slide, $defaults_slide );

				$slide_image = wp_get_attachment_image_src( $slide['slide_id'], 'full' );

				if( $this->has_value( $slide_image[0] ) ){

					if( $this->has_value( $slide['slide_url'] ) && $this->has_value( $slide['slide_target_blank'] ) ){
						$tag 	= "a";
						$attr 	= " href='{$slide['slide_url']}' target='_blank'";
					} elseif( $this->has_value( $slide['slide_url'] ) ){
						$tag 	= "a";
						$attr 	= " href='{$slide['slide_url']}'";
					} else {
						$tag 	= "div";
						$attr 	= "";
					}

					echo "<{$tag}{$attr} class='slide-item'>";

						echo "<img src='{$slide_image['0']}' alt='{$slide['slide_caption']}' />";

					echo "</{$tag}>";
				}

			endforeach;

			echo '</div><!-- .slides -->';

			echo '</div><!-- .wp-simple-flexslider -->';
		endif;
	}

	/**
	 * Check if particular variable has value or not
	 * 
	 * @access public
	 * @since 0.1
	 * @param int variable
	 * @return bool
	 */
	public function has_value( $variable ){

		if( $variable && '' != $variable ){
			return true;
		} else {
			return false;
		}
	}
}
new WP_Simple_Flexslider_Frontend;