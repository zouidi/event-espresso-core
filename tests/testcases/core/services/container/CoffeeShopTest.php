<?php
use EventEspresso\core\services\container\CoffeeShop;
use EventEspresso\core\services\container\DependencyInjector;
use EventEspresso\core\services\container\Recipe;
use EventEspresso\core\services\container\CoffeeMaker;
use EventEspresso\core\services\container\NewCoffeeMaker;
use EventEspresso\core\services\container\SharedCoffeeMaker;
use EventEspresso\core\services\container\LoadOnlyCoffeeMaker;

if ( ! defined('EVENT_ESPRESSO_VERSION')) {
    exit('No direct script access allowed');
}



/**
 * Class CoffeeShopTest
 * Description
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class CoffeeShopTest extends EE_UnitTestCase
{

    /**
     * @var CoffeeShop $CoffeeShop
     */
    private $CoffeeShop;

    /**
     * @var DependencyInjector $DependencyInjector
     */
    private $DependencyInjector;



    /**
     * setUp
     */
    public function setUp()
    {
        // instantiate the container
        $this->CoffeeShop = new CoffeeShop();
        // create a dependency injector class for resolving class constructor arguments
        $this->DependencyInjector = new DependencyInjector(
            $this->CoffeeShop,
            new EEH_Array()
        );
        // and some coffeemakers, one for creating new instances
        $this->CoffeeShop->addCoffeeMaker(
            new NewCoffeeMaker( $this->CoffeeShop, $this->DependencyInjector),
            CoffeeMaker::BREW_NEW
        );
        // and one for shared services
        $this->CoffeeShop->addCoffeeMaker(
            new SharedCoffeeMaker( $this->CoffeeShop, $this->DependencyInjector),
            CoffeeMaker::BREW_SHARED
        );
    }



    public function addDefaultRecipes()
    {
        $this->CoffeeShop->addRecipe( new Recipe( Recipe::DEFAULT_ID));
    }



    public function test_addCoffeeMaker()
    {
        try {
            // and CoffeeMaker for classes that do not require instantiation
            $added = $this->CoffeeShop->addCoffeeMaker(
                new LoadOnlyCoffeeMaker( $this->CoffeeShop, $this->DependencyInjector),
                CoffeeMaker::BREW_LOAD_ONLY
            );
            $this->assertTrue($added);
        } catch (Exception $e) {
            $this->fail(
                sprintf(
                    'CoffeeShop::addCoffeeMaker() should Not have thrown the following Exception: %1$s'
                )
            );
        }
    }



    public function test_addClosure()
    {
        try {
            $closure = function () {
                return 'I AM CLOSURE';
            };
            $added = $this->CoffeeShop->addClosure( 'i-am-closure', $closure);
            $this->assertInstanceOf('Closure', $added);
        } catch (Exception $e) {
            $this->fail(
                sprintf(
                    'CoffeeShop::addClosure() should Not have thrown the following Exception: %1$s',
                    $e->getMessage()
                )
            );
        }
    }



    public function test_addService()
    {
        try {
            $added = $this->CoffeeShop->addService( 'i-am-stdClass', new stdClass());
            $this->assertTrue($added);
        } catch (Exception $e) {
            $this->fail(
                sprintf(
                    'CoffeeShop::addService() should Not have thrown the following Exception: %1$s',
                    $e->getMessage()
                )
            );
        }
    }



    public function test_addRecipe()
    {
        try {
            // add default recipe, which should handle loading for most PSR-4 compatible classes
            // as long as they are not type hinting for interfaces
            $this->assertTrue(
                $this->CoffeeShop->addRecipe( new Recipe( Recipe::DEFAULT_ID))
            );
        } catch (Exception $e) {
            $this->fail(
                sprintf(
                    'CoffeeShop::addCoffeeMaker() should Not have thrown the following Exception: %1$s',
                    $e->getMessage()
                )
            );
        }
    }



    public function test_getRecipe()
    {
        $this->addDefaultRecipes();
        try {
            $recipe = $this->CoffeeShop->getRecipe( Recipe::DEFAULT_ID);
            $this->assertInstanceOf('EventEspresso\core\services\container\RecipeInterface', $recipe);
            $this->assertEquals(Recipe::DEFAULT_ID, $recipe->identifier());
            $this->assertEquals(CoffeeMaker::BREW_NEW, $recipe->type());
        } catch (Exception $e) {
            $this->fail(
                sprintf(
                    'CoffeeShop::getRecipe() should Not have thrown the following Exception: %1$s',
                    $e->getMessage()
                )
            );
        }
    }



    public function test_addAliases()
    {
        try {
            $this->CoffeeShop->addAliases( 'EE_Class_For_Testing_Loading', array( 'Testing_Loading'));
        } catch (Exception $e) {
            $this->fail(
                sprintf(
                    'CoffeeShop::addAliases() should Not have thrown the following Exception: %1$s',
                    $e->getMessage()
                )
            );
        }
    }



    public function test_has()
    {
        $this->assertFalse($this->CoffeeShop->has( 'EE_Class_For_Testing_Loading'));
    }



    public function test_get()
    {
        $this->addDefaultRecipes();
        try {
            // attempt to get class that should NOT have a valid Recipe yet
            $this->CoffeeShop->get( 'EE_Class_For_Testing_Loading');
            $this->fail('CoffeeShop::get() should have thrown an Exception');
        } catch (Exception $e) {
            $this->assertInstanceOf('Exception', $e);
        }
    }



    public function test_brew_new()
    {
        $this->addDefaultRecipes();
        try {
            // attempt to get class that should NOT have a valid Recipe yet
            $this->CoffeeShop->get( 'EE_Class_For_Testing_Loading');
            $this->fail('CoffeeShop::get() should have thrown an Exception');
        } catch (Exception $e) {
            $this->assertInstanceOf('Exception', $e);
        }
        // add recipe for our mock class
        $added = $this->CoffeeShop->addRecipe(
            new Recipe(
                'EE_Class_For_Testing_Loading',
                CoffeeMaker::BREW_NEW,
                array(),
                array(
                    EE_TESTS_DIR . 'mocks/core/EE_Class_For_Testing_Loading.core.php',
                )
            )
        );
        $this->assertTrue($added);
        // attempt to brew class
        $mock1 = $this->CoffeeShop->brew( 'EE_Class_For_Testing_Loading');
        $this->assertInstanceOf('EE_Class_For_Testing_Loading', $mock1);
        // and another one which should be a NEW instance
        $mock2 = $this->CoffeeShop->brew( 'EE_Class_For_Testing_Loading');
        $this->assertInstanceOf('EE_Class_For_Testing_Loading', $mock2);
        $this->assertFalse($mock1 === $mock2);
    }



    public function test_brew_shared()
    {
        $this->addDefaultRecipes();
        try {
            // attempt to get class that should NOT have a valid Recipe yet
            $this->CoffeeShop->get( 'EE_Class_For_Testing_Loading');
            $this->fail('CoffeeShop::get() should have thrown an Exception');
        } catch (Exception $e) {
            $this->assertInstanceOf('Exception', $e);
        }
        // add recipe for our mock class
        $added = $this->CoffeeShop->addRecipe(
            new Recipe(
                'EE_Class_For_Testing_Loading',
                CoffeeMaker::BREW_SHARED,
                array(),
                array(
                    EE_TESTS_DIR . 'mocks/core/EE_Class_For_Testing_Loading.core.php',
                )
            )
        );
        $this->assertTrue($added);
        // attempt to brew class
        $mock1 = $this->CoffeeShop->brew( 'EE_Class_For_Testing_Loading');
        $this->assertInstanceOf('EE_Class_For_Testing_Loading', $mock1);
        // and another one which should be the SAME instance
        $mock2 = $this->CoffeeShop->brew( 'EE_Class_For_Testing_Loading');
        $this->assertInstanceOf('EE_Class_For_Testing_Loading', $mock2);
        $this->assertTrue($mock1 === $mock2);
    }



    public function test_brew_new_with_wildcard_recipe()
    {
        $this->addDefaultRecipes();
        try {
            // attempt to get class that should NOT have a valid Recipe yet
            $this->CoffeeShop->get( 'EE_Taxes');
            $this->fail('CoffeeShop::get() should have thrown an Exception');
        } catch (Exception $e) {
            $this->assertInstanceOf('Exception', $e);
        }
        // now add recipe for loading our entities as shared services (singletons)
        $added = $this->CoffeeShop->addRecipe(
            new Recipe(
                'EE_*',
                CoffeeMaker::BREW_NEW,
                array(),
                EE_CLASSES . '*.class.php'
            )
        );
        $this->assertTrue($added);
        // attempt to brew EE_Taxes class since it is not coupled to EE_Base_Class
        $object_1 = $this->CoffeeShop->brew( 'EE_Taxes', array());
        $this->assertInstanceOf('EE_Taxes', $object_1);
        // and another one which should be the SAME instance
        $object_2 = $this->CoffeeShop->brew( 'EE_Taxes', array());
        $this->assertInstanceOf('EE_Taxes', $object_2);
        $this->assertFalse($object_1 === $object_2);
    }



    public function test_brew_shared_with_wildcard_recipe()
    {
        $this->addDefaultRecipes();
        try {
            // attempt to get class that should NOT have a valid Recipe yet
            $this->CoffeeShop->get( 'EE_Taxes');
            $this->fail('CoffeeShop::get() should have thrown an Exception');
        } catch (Exception $e) {
            $this->assertInstanceOf('Exception', $e);
        }
        // now add recipe for loading our entities as shared services (singletons)
        $added = $this->CoffeeShop->addRecipe(
            new Recipe(
                'EE_*',
                CoffeeMaker::BREW_SHARED,
                array(),
                EE_CLASSES . '*.class.php'
            )
        );
        $this->assertTrue($added);
        // attempt to brew EE_Taxes class since it is not coupled to EE_Base_Class
        $object_1 = $this->CoffeeShop->brew( 'EE_Taxes', array());
        $this->assertInstanceOf('EE_Taxes', $object_1);
        // and another one which should be the SAME instance
        $object_2 = $this->CoffeeShop->brew( 'EE_Taxes', array());
        $this->assertInstanceOf('EE_Taxes', $object_2);
        $this->assertTrue($object_1 === $object_2);
    }



    public function test_brew_with_injected_dependency()
    {
        $this->addDefaultRecipes();
        try {
            // attempt to get class that should NOT have a valid Recipe yet
            $this->CoffeeShop->get( 'EE_Session_Mock');
            $this->fail('CoffeeShop::get() should have thrown an Exception');
        } catch (Exception $e) {
            $this->assertInstanceOf('Exception', $e);
        }
        // add default recipe for our mock classes
        $added = $this->CoffeeShop->addRecipe(
            new Recipe(
                'EE_*',
                CoffeeMaker::BREW_SHARED,
                array(),
                array(
                    EE_TESTS_DIR . 'mocks/core/*.core.php',
                )
            )
        );
        $this->assertTrue($added);
        // attempt to brew EE_Session_Mock class which type hints for EE_Encryption
        $session = $this->CoffeeShop->brew( 'EE_Session_Mock', array());
        $this->assertInstanceOf('EE_Session_Mock', $session);
        $this->assertInstanceOf('EE_Encryption', $session->encryption());
    }
}
// End of file CoffeeShopTest.php
// Location:\tests\testcases\core\services\container\CoffeeShopTest.php