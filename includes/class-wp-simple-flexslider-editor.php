<?php
/**
 * Prevent file from being accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WP_Simple_Flexslider_Editor{

	var $prefix;

	/**
	 * Construct the class
	 */
	function __construct(){
		$this->prefix = "_wp_simple_flexslider_";

		add_action( 'admin_print_styles', 									array( $this, 'enqueue_scripts' ) );	
		add_action( 'add_meta_boxes', 										array( $this, 'register_meta_box' ) );	
		add_action( 'save_post', 											array( $this, 'save_meta_box' ) );
		add_action( 'wp_ajax_wp_simple_flexslider_product_finder', 			array( $this, 'endpoint_product_finder' ) );
		add_action( 'wp_ajax_nopriv_wp_simple_flexslider_product_finder', 	array( $this, 'endpoint_product_finder' ) );
	}

	/**
	 * Enqueueing scripts for slideshow editor
	 * 
	 * @access public
	 * 
	 * @return void
	 */
	public function enqueue_scripts(){
		/**
		 * Only enqueue the script on admin & slideshow editor screen
		 * Make sure that get_current_screen exists
		 */
		if( is_admin() && function_exists( 'get_current_screen' ) ){

			$screen = get_current_screen();

			if( 'wp_simple_flexslider' == $screen->post_type ){
				wp_enqueue_media();
				wp_enqueue_style( 'wp_simple_flexslider_editor', WP_SIMPLE_FLEXSLIDER_URL . 'css/wp-simple-flexslider-editor.css', array(), false, 'all' );
		        wp_enqueue_script( 'wp_simple_flexslider_editor', WP_SIMPLE_FLEXSLIDER_URL . 'js/wp-simple-flexslider-editor.js', array( 'jquery', 'jquery-ui-sortable' ), '0.1', true );
				
				$wp_simple_flexslider_editor_params = array(
					'no_duplicate_message' 			=> __( '%filename% slide have been added to this slideshow before. You cannot have one slide more than once in a slideshow.', 'wp-simple-flexslider'),
					'ajax_url'						=> admin_url( 'admin-ajax.php' ),
				);
				wp_localize_script( 'wp_simple_flexslider_editor', 'wp_simple_flexslider_editor_params', $wp_simple_flexslider_editor_params );
			}
		}
	}

	/**
	 * Registering slideshow meta box for configuring slideshow
	 * 
	 * @access public
	 * 
	 * @return void
	 */
	public function register_meta_box(){
		add_meta_box('slideshow-metabox', __( 'Slideshow', 'wp-simple-flexslider' ), array( $this, 'display_meta_box' ), 'wp_simple_flexslider' );
	}

	/**
	 * Displaying slideshow meta box
	 * 
	 * @access public
	 * 
	 * @return void
	 */
	public function display_meta_box(){
		global $post;

		/**
		 * Get currently saved slideshow
		 */
		$slideshow = get_post_meta( $post->ID, "{$this->prefix}data", true );
		?>
			<div class="slides-wrap">
				<?php 
				if( is_array( $slideshow ) && ! empty( $slideshow ) ) :
					foreach ( $slideshow as $slide ) :
						/**
						 * Just in case there's no slide id
						 */
						if( ! isset( $slide['slide_id'] ) )
							continue;

						$slide_id = (int)$slide['slide_id'];

						/**
						 * Get slide path
						 */
						$attachment = wp_get_attachment_image_src( intval( $slide_id ), 'full' );

						if( ! $attachment || ! isset( $attachment[0] ) )
							continue;
						?>

						<div class="wp-simple-flexslider-slide-wrap" data-slide-id="<?php echo $slide['slide_id']; ?>">
							<div class="slide">
								<div class="wp-simple-flexslider-inside">
									<img src="<?php echo esc_attr( $attachment[0] );?>" alt="">		
								</div>			
							</div><!-- .slide -->

							<div class="wp-simple-flexslider-slide-fields">
								<input type="number" class="wp-simple-flexslider-slide-id" name="slideshow[<?php echo $slide_id; ?>][slide_id]" value="<?php echo $slide_id; ?>" />								
							</div>

							<div class="wp-simple-flexslider-slide-actions">
								<div class="wp-simple-flexslider-inside">
									<textarea name="slideshow[<?php echo $slide_id; ?>][slide_caption]" class="input-text wp-simple-flexslider-slide-caption" placeholder="<?php _e( 'Describe this slide', 'wp-simple-flexslider' ); ?>"><?php echo ( isset( $slide['slide_caption'] ) ? esc_attr( $slide['slide_caption'] ) : '' ); ?></textarea>
									<a href="#" class="wp-simple-flexslider-slide-remove button"><?php _e( 'Remove', 'wp-simple-flexslider' ); ?></a>
								</div>
							</div>
						</div><!-- .wp-simple-flexslider-slide-wrap -->

						<?php 
					endforeach;
				endif; 
				?>
			</div>

			<div class="no-wp-simple-flexslider-slide-notice">
				<p><?php _e( "There is no slide for this slideshow yet. Click 'Add Slide' button below to start", "wp-simple-flexslider" ); ?></p>
			</div>

			<div class="slides-actions">
					<a href="#" class="wp-simple-flexslider-slide-add button button-large button-primary"><?php _e( 'Add Slide', 'wp-simple-flexslider' ); ?></a>				
					<a href="#" class="wp-simple-flexslider-slide-remove-all button button-large"><?php _e( 'Remove All Slides', 'wp-simple-flexslider' ); ?></a>				
			</div>
				
			<!-- Template for wp-simple-flexslider-slide-wrap -->
			<script id="template-wp-simple-flexslider-slide-wrap" type="text/template">
				<div class="wp-simple-flexslider-slide-wrap">
					<div class="slide">
						<div class="wp-simple-flexslider-inside">
							<img src="" alt="">				
						</div>			
					</div><!-- .slide -->

					<div class="wp-simple-flexslider-slide-fields">
						<input type="number" class="wp-simple-flexslider-slide-id" name="slideshow[%slide_id%][slide_id]" value="%slide_id%" />
					</div>

					<div class="wp-simple-flexslider-slide-actions">
						<div class="wp-simple-flexslider-inside">
							<textarea name="slideshow[%slide_id%][slide_caption]" class="input-text wp-simple-flexslider-slide-caption" placeholder="<?php _e( 'Describe this slide', 'wp-simple-flexslider' ); ?>"></textarea>							
							<a href="#" class="wp-simple-flexslider-slide-remove button"><?php _e( 'Remove', 'wp-simple-flexslider' ); ?></a>
						</div>
					</div>
				</div>		
			</script>
		<?php
		wp_nonce_field( "{$this->prefix}meta_box", "{$this->prefix}meta_box" );
	}

	/**
	 * Saving meta box data
	 * 
	 * @access public
	 * 
	 * @param int 		post ID
	 * 
	 * @return void
	 */
	public function save_meta_box( $post_id ){
		/**
		 * If save_post is triggered from front end, there will be no get_current_screen() loaded. Stop the process.
		 */
		if( !function_exists( 'get_current_screen' ) ){
			return;
		}

		$screen = get_current_screen();

		// Only run this on slideshow editor screen
		if ($screen != null && $screen->post_type != 'wp_simple_flexslider') 
			return;

		// Cancel if this is an autosave
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return;

		// Verify nonce
		if( !isset( $_POST["{$this->prefix}meta_box"] ) || 
			!wp_verify_nonce( $_POST["{$this->prefix}meta_box"], "{$this->prefix}meta_box" ) ) 
			return;

		// if our current user can't edit this post, bail
		if( !current_user_can( 'edit_posts' ) ) return;		

		// Save slideshow data
		if( ! $_POST['slideshow'] || empty( $_POST['slideshow'] ) ){
			update_post_meta( $post_id, "{$this->prefix}data", array() ); 			
		} else {
			update_post_meta( $post_id, "{$this->prefix}data", $_POST['slideshow'] ); 			
		}
	}

	/**
	 * Product finder endpoint
	 * 
	 * @access public
	 * 
	 * @return void  echoing json output
	 */
	public function endpoint_product_finder(){

		$output = array();

		if( isset( $_REQUEST['keyword'] ) && isset( $_REQUEST['_n'] ) && '' != $_REQUEST['keyword'] ){

			/**
			 * Verify nonce
			 */
			if( wp_verify_nonce( $_REQUEST['_n'], 'product_finder_nonce' ) ){

				$args = array(
					'post_status' 			=> 'publish',
					'post_type'				=> 'product',
					'edit_posts_per_page' 	=> 10,
					's'						=> sanitize_text_field( $_REQUEST['keyword'] )
				);

				$posts = get_posts( $args );

				if( $posts ){

					foreach ( $posts as $post ) {
						$output[] = array(
							'id' 	=> $post->ID,
							'text'	=> $post->post_title
						);
					}
				}
			}
		}

		echo json_encode( $output );

		die();
	}
}
new WP_Simple_Flexslider_Editor;