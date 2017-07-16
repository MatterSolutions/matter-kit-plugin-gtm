<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Plugin Name: Matter Kit - Tag Manager
 * Plugin URI: http://www.mattersolutions.com.au
 * Description: A plugin to add Google Tag manager to themes built on the Matter Kit framework.
 * Version: 0.9.1
 * Author: MatterSolutions
 * Author URI: http://www.mattersolutions.com.au
 * License: GPL2
 */


class MatterKitGTM {


	function __construct() {

		add_action( 'init', array( $this, '__mttr_check_for_required_hooks' ) );
		add_action( 'customize_register', array( $this, '__mttr_gtm_customiser_settings' ), 50 );

	}



	/* ---------------------------------------------------------
	*	Check for our required hook
	 ---------------------------------------------------------*/
	public function __mttr_check_for_required_hooks() {

		$this->__mttr_add_gtm();

	}





	/* ---------------------------------------------------------
	*	Hook Google Tag manager, or give an error in the admin
	 ---------------------------------------------------------*/

	public function __mttr_add_gtm() {

		// Check to see if the page setup hooks exist
		if ( !has_action( 'mttr_page_setup' ) ) {

			$this->__mttr_admin_notice( 'Uh oh! Your theme doesn\'t have the \'mttr_page_setup\' action hook defined. Your Google Tag Manager code may not be dislaying.' );
			return false;

		// Add GTM code if exists
		} else {

			if ( $this->mttr_get_gtm_code() ) {

				add_action( 'init', array( $this, 'mttr_output_gtm_code' ), 11 );
				return true;

			} else {

				$this->__mttr_admin_notice( 'Google Tag Manager hasn\'t been set up. Please visit the customiser to <a href="' . get_admin_url( null, 'customize.php?autofocus%5Bcontorl%5D=mttr_gtm_code' ) . '">add your Google Tag Manager ID</a>.' );
				return false;

			}

		}

	}




	/* ---------------------------------------------------------
	*	Output an admin notice
	 ---------------------------------------------------------*/

	public function __mttr_admin_notice( $message, $type = 'update-nag' ) {

		if ( is_admin() && !defined('DOING_AJAX') ) {

			echo '<div class="' . sanitize_html_class( $type ) . ' notice">';

				echo '<p>';

					_e( $message, 'mttr' );

				echo '</p>';

		  echo '</div>';

		}

	}





	public function __mttr_gtm_customiser_settings( $wp_customize ) {

		$wp_customize->add_section( 'mttr_gtm' , array(

		    'title' => __( 'Matter Kit - Tag Manager', 'mttr' ),
		    'description' => __( 'Manage your Google Tag Manager Code', 'mttr' ),
		    'priority'   => 50,

		) );


		// Add the tag manager id
		$wp_customize->add_setting( 'mttr_gtm_code' );


		// Add a control to upload the logo
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'mttr_gtm_code',

			array(

				'label' => 'Tag Manager ID',
				'section' => 'mttr_gtm',
				'settings' => 'mttr_gtm_code',

			) )

		);

	}





	/* ---------------------------------------------------------
	*	Output the Google Tag Manager Code
	 ---------------------------------------------------------*/

	public function mttr_output_gtm_code() {

		add_action( 'mttr_page_setup', array( $this, 'mttr_output_gtm_code_body' ), 4 );
		add_action( 'mttr_page_setup', array( $this, 'mttr_output_gtm_code_head' ), 5 );

	}





	/* ---------------------------------------------------------
	*	Output the Google Tag Manager Code In The Head
	 ---------------------------------------------------------*/

	public function mttr_output_gtm_code_head() {

		$gtm_id = $this->mttr_get_gtm_code();

		if ( $gtm_id ) {

			?><!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?php echo esc_html( $gtm_id ); ?>');</script>
<!-- End Google Tag Manager --><?php

		}

	}







	/* ---------------------------------------------------------
	*	Output the Google Tag Manager Code In The Body
	 ---------------------------------------------------------*/

	public function mttr_output_gtm_code_body() {

		$gtm_id = $this->mttr_get_gtm_code();

		if ( $gtm_id ) {

			?><!-- Google Tag Manager (noscript) -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=<?php echo esc_html( $gtm_id ); ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) --><?php

		}

	}



	/* ---------------------------------------------------------
	*	Get the Google Tag Manager Code
	 ---------------------------------------------------------*/

	public function mttr_get_gtm_code() {

		$gtm_id = get_theme_mod( 'mttr_gtm_code' );

		if ( $gtm_id ) {

			return sanitize_html_class( $gtm_id );

		}

		return false;

	}

}





/* ---------------------------------------------------------
*	Invoke the class
 ---------------------------------------------------------*/

function mttr_run_matter_kit_gtm() {

	$plugin = new MatterKitGTM();

}
mttr_run_matter_kit_gtm();





