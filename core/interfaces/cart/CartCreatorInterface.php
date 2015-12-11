<?php
namespace EventEspresso\core\interfaces\cart;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



interface CartCreatorInterface {


	/**
	 * createCart
	 *
	 * @param  CartCalculatorRepositoryInterface $cartCalculatorRepository
	 * @return CartInterface
	 */
	public function getNewCart( CartCalculatorRepositoryInterface $cartCalculatorRepository );

}
// End of file CartCreatorInterface.php
// Location: /CartCreatorInterface.php