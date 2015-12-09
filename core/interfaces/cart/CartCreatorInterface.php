<?php
namespace EventEspresso\core\interfaces\cart;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



interface CartCreatorInterface {

	function newCart();

}
// End of file CartCreatorInterface.php
// Location: /CartCreatorInterface.php