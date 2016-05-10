<?php
if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class EE_Front_Controller_Mock
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         4.9.0
 */
class EE_Front_Controller_Mock extends EE_Front_Controller{


	/**
	 * @param $post_shortcodes
	 * @param $term_exists
	 * @param $current_post
	 * @param $page_for_posts
	 * @param $post_name
	 * @param $WP
	 */
	public function process_post_shortcodes(
		$post_shortcodes,
		$term_exists,
		$current_post,
		$page_for_posts,
		$post_name,
		$WP
	) {
		$this->_process_post_shortcodes(
			$post_shortcodes,
			$term_exists,
			$current_post,
			$page_for_posts,
			$post_name,
			$WP
		);
	}


}
// End of file EE_Front_Controller_Mock.core.php
// Location: wp-content/plugins/event-espresso-core/tests/mocks/core/EE_Front_Controller_Mock.core.php