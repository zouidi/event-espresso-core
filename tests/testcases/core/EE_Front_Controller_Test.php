<?php
/**
 * Class EE_Front_Controller_Test
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         4.9.0
 */
class EE_Front_Controller_Test extends EE_UnitTestCase {

	/**
	 * @var EE_Front_Controller_Mock $front_controller
	 */
	protected $front_controller;

	/**
	 * @var array $post_shortcodes
	 */
	protected $post_shortcodes = array();

	/**
	 * @var array $critical_page_shortcodes
	 */
	protected $critical_page_shortcodes = array();

	/**
	 * @var array $test_conditions
	 */
	protected $test_conditions = array();



	public function setUp() {
		parent::setUp();
		$this->front_controller = $this->build_front_controller_mock();

		$this->post_shortcodes = array(
			'sample-page'            => array( 'ESPRESSO_TICKET_SELECTOR' => 23, ),
			'thank-you'              => array( 'ESPRESSO_THANK_YOU' => 6, ),
			'registration-checkout'  => array( 'ESPRESSO_CHECKOUT' => 4, ),
			'transactions'           => array( 'ESPRESSO_TXN_PAGE' => 5, ),
			'registration-cancelled' => array( 'ESPRESSO_CANCELLED' => 7, ),
			'os-event-list'          => array( 'ESPRESSO_EVENTS' => 14, ),
			'posts'                  => array(
				'ESPRESSO_EVENTS'          => array( 14 => true, ),
				'ESPRESSO_TICKET_SELECTOR' => array( 23 => true, ),
				'ESPRESSO_CHECKOUT'        => array( 4 => true, ), // << should not be there
			),
		);
		// copy our test data to the registry
		$this->registry->CFG->core->post_shortcodes = $this->post_shortcodes;
		$this->critical_page_shortcodes = array(
			'ESPRESSO_CHECKOUT',
			'ESPRESSO_TXN_PAGE',
			'ESPRESSO_THANK_YOU',
			'ESPRESSO_CANCELLED',
		);
		// each test represents a set of incoming request vars
		$this->test_conditions = array(
			'TEST A - "Sample Page" using TICKET_SELECTOR shortcode' => array(
				'term_exists'    => false,
				'current_post'   => 'sample-page',
				'page_for_posts' => 'posts',
				'query_vars'     => array(
					'page'     => '',
					'pagename' => 'sample-page',
				),
			),
			'TEST B - "Old School Event List" page' => array(
				'term_exists'    => false,
				'current_post'   => 'os-event-list',
				'page_for_posts' => 'posts',
				'query_vars'     => array(
					'page'     => '',
					'pagename' => 'os-event-list',
				),
			),
			'TEST C - "Registration Checkout" page' => array(
				'term_exists'    => false,
				'current_post'   => 'registration-checkout',
				'page_for_posts' => 'posts',
				'query_vars'     => array(
					'page'     => '',
					'pagename' => 'registration-checkout',
				),
			),
			'TEST D - "Thank You" page' => array(
				'term_exists'    => false,
				'current_post'   => 'thank-you',
				'page_for_posts' => 'posts',
				'query_vars'     => array(
					'page'     => '',
					'pagename' => 'registration-checkout/thank-you',
				),
			),
			'TEST E - "Tests Category" archive page' => array(
				'term_exists'    => true,
				'current_post'   => 'posts',
				'page_for_posts' => 'posts',
				'query_vars'     => array(
					'espresso_event_categories' => 'tests',
				),
			),
			'TEST F - "Food Tag" archive page' => array(
				'term_exists'    => false,
				'current_post'   => 'posts',
				'page_for_posts' => 'posts',
				'query_vars'     => array(
					'tag' => 'food',
				),
			),
		);
		// don't run any shortcode logic after loading
		add_filter(
			'FHEE__EE_Front_Controller__initialize_shortcode_if_active_on_page__run_shortcode',
			'__return_false'
		);
	}



	public function test_process_post_shortcodes() {
		global $WP;
		if ( ! $WP instanceof WP ) {
			$WP = new WP();
		}
		//grab a copy of the shortcode data currently in the registry, so that we can reset it after each test
		$original_shortcodes_array = $this->registry->shortcodes;
		// echo "\n registry->shortcodes: \n";
		// var_dump( $this->registry->shortcodes );
		// can't unset loaded classes, so we'll have to keep track of what HAS been loaded so far
		$already_loaded = array();
		foreach ( $this->test_conditions as $test_name => $test_conditions ){
			$WP->query_vars = $test_conditions['query_vars'];
			foreach ( $this->post_shortcodes as $post_name => $post_shortcodes ){
				$this->front_controller->process_post_shortcodes(
					$post_shortcodes,
					$test_conditions['term_exists'],
					$test_conditions['current_post'],
					$test_conditions['page_for_posts'],
					$post_name,
					$WP
				);
				foreach ( $post_shortcodes as $shortcode_name => $post_id ) {
					$shortcode_class = 'EES_' . $shortcode_name;
					// first test if it's a category, or a tag page, or the WP "Posts" page (ie:the blog)
					// but don't load critical page shortcodes on the posts page or any kind of archive
					if (
						(
							$test_conditions['term_exists']
							|| $test_conditions['current_post'] === $test_conditions['page_for_posts']
						) && ! in_array( $shortcode_name, $this->critical_page_shortcodes )
					) {
						// we erringly threw this into our test data to make sure that it did NOT get loaded
						if ( $shortcode_name === 'ESPRESSO_CHECKOUT' ) {
							EE_UnitTestCase::assertNull(
								$this->registry->shortcodes->{$shortcode_name},
								sprintf(
									'%sFailed to assert that the %s shortcode was NOT loaded for the posts page.' . "\n" .
									'The Registry contained: "%s" and the test conditions were "%s"',
									"\n" .$test_name . "\n",
									$shortcode_name,
									var_export( $this->registry->shortcodes->{$shortcode_name}, true ),
									"\n" . var_export(
										array(
											'post_name' => $post_name,
											'current_post' => $test_conditions['current_post'],
											'page_for_posts' => $test_conditions['page_for_posts'],
											'term_exists' => $test_conditions['term_exists'],
										),
										true
									)
								)
							);

						} else {
							EE_UnitTestCase::assertInstanceOf(
								$shortcode_class,
								$this->registry->shortcodes->{$shortcode_name},
								sprintf(
									'%sFailed to assert that the %s shortcode was loaded for %s page.' . "\n" .
									'The Registry contained: "%s" and the test conditions were "%s"',
									"\n" . $test_name . "\n",
									$shortcode_name,
									$test_conditions['term_exists'] ? 'a taxonomy' : 'the WP Posts',
									var_export( $this->registry->shortcodes->{$shortcode_name}, true ),
									"\n" . var_export(
										array(
											'post_name'   => $post_name,
											'current_post' => $test_conditions['current_post'],
											'page_for_posts' => $test_conditions['page_for_posts'],
											'term_exists'    => $test_conditions['term_exists'],
										),
										true
									)
								)
							);
						}
						$already_loaded[ $shortcode_class ] = true;

					} else if ( $post_name === $test_conditions['current_post'] ) {
						// if ( ! isset( $this->registry->shortcodes->{$shortcode_name} ) ) {
						// 	echo "\n shortcode_name: " . $shortcode_name . "\n";
						// 	echo "\n this->registry->shortcodes->{$shortcode_name}: \n";
						// 	var_dump( $this->registry->shortcodes->{$shortcode_name} );
						// }
						// or if we are on the specific page
						EE_UnitTestCase::assertInstanceOf(
							$shortcode_class,
							$this->registry->shortcodes->{$shortcode_name},
							sprintf(
								'%s Failed to assert that the %s shortcode was loaded for the %s post.' . "\n" .
								'The Registry contained: "%s" and the test conditions were "%s"',
								"\n" . $test_name . "\n",
								$shortcode_name,
								$post_name,
								var_export( $this->registry->shortcodes->{$shortcode_name}, true ),
								"\n" . var_export(
									array(
										'post_name'   => $post_name,
										'current_post' => $test_conditions['current_post'],
										'page_for_posts' => $test_conditions['page_for_posts'],
										'term_exists'    => $test_conditions['term_exists'],
									),
									true
								)
							)
						);
						$already_loaded[ $shortcode_class ] = true;

					} else if ( ! isset( $already_loaded[ $shortcode_class ] ) ) {
						// and if none of the above, then make sure the shortcode didn't load in error
						EE_UnitTestCase::assertNotInstanceOf(
							$shortcode_class,
							$this->registry->shortcodes->{$shortcode_name},
							sprintf(
								'%s: Failed to assert that the %s shortcode was NOT loaded when it should not have been',
								$test_name,
								$shortcode_name
							)
						);
					}
				}
			}
			// reset registry shortcode data
			$this->registry->shortcodes = $original_shortcodes_array;
		}
	}

}
// End of file EE_Front_Controller_Test.php
// Location: wp-content/plugins/event-espresso-core/tests/testcases/core/EE_Front_Controller_Test.php