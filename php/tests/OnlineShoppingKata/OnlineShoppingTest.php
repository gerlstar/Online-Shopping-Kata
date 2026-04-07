<?php

namespace Tests\OnlineShoppingKata;


use App\OnlineShoppingKata\Cart;
use App\OnlineShoppingKata\DeliveryInformation;
use App\OnlineShoppingKata\Item;
use App\OnlineShoppingKata\OnlineShopping;
use App\OnlineShoppingKata\Session;
use App\OnlineShoppingKata\Shipping;
use App\OnlineShoppingKata\Pickup;
use App\OnlineShoppingKata\Store;
use App\OnlineShoppingKata\HomeDelivery;
use App\OnlineShoppingKata\StoreEvent;
use PHPUnit\Framework\TestCase;

class OnlineShoppingTest extends TestCase
{
    /**
     * Stores
     */
    private $backaplan;
    private $nordstan;

    /**
     * Items
     */
    private $cherryBloom;
    private $rosePetal;
    private $blusherBrush;
    private $eyelashCurler;
    private $wildRose;
    private $cocoaButter;
    private $masterclass;
    private $makeoverNordstan;
    private $makeoverBackaplan;

    protected function setUp() : void
    {
        $this->nordstan = new Store("Nordstan", false, '');
        $this->backaplan = new Store("Backaplan", true, '');

        $this->cherryBloom = new Item("Cherry Bloom", "LIPSTICK", 30);
        $this->rosePetal = new Item("Rose Petal", "LIPSTICK", 30);
        $this->blusherBrush = new Item("Blusher Brush", "TOOL", 50);
        $this->eyelashCurler = new Item("Eyelash curler", "TOOL", 100);
        $this->wildRose = new Item("Wild Rose", "PURFUME", 200);
        $this->cocoaButter = new Item("Cocoa Butter", "SKIN_CREAM", 250);

        $this->nordstan->addStockedItems([
            $this->cherryBloom,
            $this->rosePetal,
            $this->blusherBrush,
            $this->eyelashCurler,
            $this->wildRose,
            $this->cocoaButter
        ]);
        $this->backaplan->addStockedItems([
            $this->cherryBloom,
            $this->rosePetal,
            $this->eyelashCurler,
            $this->wildRose,
            $this->cocoaButter
        ]);

        // Store events add themselves to the stocked items at their store
        $this->masterclass = new StoreEvent("Eyeshadow Masterclass", $this->nordstan);
        $this->makeoverNordstan = new StoreEvent("Makeover", $this->nordstan);

        $this->makeoverBackaplan = new StoreEvent("Makeover", $this->backaplan);
    }

    public function testWarehouseUseCase()
    {
       
        $cart = new Cart();
        $cart->addItem($this->masterclass); //event
        $cart->addItem($this->makeoverNordstan);//event; will be switched to makeoverBackaplan

        $session = new Session();
        $deliveryInfo = new Shipping();
        
        $session->put("DELIVERY_INFO", $deliveryInfo);
        $session->put("CART", $cart);
        $shopping = new OnlineShopping($session);

        $shopping->switchStore();

        $unavailableItems = $cart->getUnavailableItems();

        $this->assertTrue(count($unavailableItems) == 2);


        $deliveryInfo =$cart->getDeliveryInformation();
       
         $this->assertTrue($deliveryInfo instanceof Shipping);

    }


    public function testSwitchStore()
    {

        $cart = new Cart();
        $cart->addItem($this->cherryBloom);
        $cart->addItem($this->blusherBrush);

        $session = new Session();
        $deliveryInfo = new HomeDelivery($this->nordstan, 0);
        
        $cart->setDeliveryInformation($deliveryInfo);

        $session->put("DELIVERY_INFO", $deliveryInfo);
        $session->put("CART", $cart);
        $shopping = new OnlineShopping($session);
       
        $shopping->switchStore($this->backaplan);

        $deliveryInfoNew = $cart->getDeliveryInformation();

        $this->assertTrue($deliveryInfoNew instanceof Pickup);
    }

    public function testPickup()
    {
 
        $cart = new Cart();
        $cart->addItem($this->cherryBloom);
        $cart->addItem($this->blusherBrush); //not in backaplan
 
        $session = new Session();
        $deliveryInfo = new Pickup($this->nordstan, 0);
        
        $cart->setDeliveryInformation($deliveryInfo);

        $session->put("DELIVERY_INFO", $deliveryInfo);
        $session->put("CART", $cart);
        $shopping = new OnlineShopping($session);

        //before count of cart items
         $items = $cart->getItems();
       $this->assertCount(2, $items);

        $shopping->switchStore($this->backaplan);

        $deliveryInfoNew = $cart->getDeliveryInformation();

        $this->assertTrue($deliveryInfoNew instanceof Pickup);
       
        $unavailable = $cart->getUnavailableItems();

        $items = $cart->getItems();
        // there is now an unavailable item and its still in
        //get items but we now know they are different amounts
        //because the store $this->backaplan doesnt have all 
        //the items that is in the $cart
        $this->assertTrue(count($items) != count($unavailable));
     

        
    }
}
