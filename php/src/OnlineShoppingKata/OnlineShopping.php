<?php
namespace App\OnlineShoppingKata;
/**
 * Class OnlineShopping
 * @package OnlineShoppingKata
 *
 * The online shopping company owns a chain of Stores selling
 * makeup and beauty products.
 * <p>
 * Customers using the online shopping website can choose a Store then
 * can put Items available at that store into their Cart.
 * <p>
 * If no store is selected, then items are shipped from
 * a central warehouse.
 *
 */
class OnlineShopping
{
    /**
     * @var Session
     */
    private $session;
    /**
     * OnlineShopping constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }
    /**
     * This method is called when the user changes the
     * store they are shopping at in the online shopping
     * website.
     *
     * @param Store|null $storeToSwitchTo
     */
    public function switchStore(?Store $storeToSwitchTo = null)
    {
        /** @var Cart $cart */
        $cart = $this->session->get('CART') ?? null;
        // die($this->session->get('DELIVERY_INFO') === undefined);
        // if ($this->session->get('DELIVERY_INFO')){
        $deliveryInformation = $this->session->get('DELIVERY_INFO');
        // }
        //no stores to switch to thus go to warehouse
        if ($storeToSwitchTo == null) {
            $this->useWarehouse($cart, $deliveryInformation);
        } else {
            if ($cart != null) {
                $weight = 0;
                $updateItemsAndWeight = $cart->switchItems($weight, $storeToSwitchTo);
                $newItems = $updateItemsAndWeight['newItems'];
                $weight = $updateItemsAndWeight['weight'];
                $weight = $cart->reduceWeight($weight);
                
                /** @var DeliveryInformation $deliveryInformation */
                /** @var Store $currentStore */
                $result = $this->validateDeliveryInfo($cart, $storeToSwitchTo, $weight);
                $storeToSwitchTo = $result['storeToSwitchTo'];
                $cart = $result['cart'];
                
                foreach ($newItems as $item) {
                    $cart->addItem($item);
                }
            }
            $this->session->put("STORE", $storeToSwitchTo);
        }
        // $this->session["STORE"] = $storeToSwitchTo;
        // $this->session->saveAll();
    }
    private function validateDeliveryInfo(Cart $cart, Store $storeToSwitchTo, int $weight)
    {
        $deliveryInformation = $cart->getDeliveryInformation();
        $currentStore = $deliveryInformation->getStore();
        $locationService = $this->session->get('LOCATION_SERVICE');
        $address = $deliveryInformation->getDeliveryAddress();
        if ($deliveryInformation instanceof HomeDelivery) {
            //if the delivery isnt nearby, they need to pick up 
            if (! $locationService->isWithinDeliveryRange($storeToSwitchTo, $address)) {
                $deliveryInformation = new Pickup($currentStore, $weight);
                $cart->setDeliveryInformation($deliveryInformation);
            } else {
                $deliveryInformation->setTotalWeight($weight);
                $deliveryInformation->setPickupLocation($storeToSwitchTo);
            }
        } else {
            // $address = $deliveryInformation->getDeliveryAddress();
            if ($locationService->isWithinDeliveryRange($storeToSwitchTo, $address)) {
                $deliveryInfo = new HomeDelivery($storeToSwitchTo, $weight);
                $cart->setDeliveryInformation($deliveryInfo);
            }
        }
        return [
            'cart' => $cart,
            'storeToSwitchTo' => $storeToSwitchTo,
        ];
    }
    private function useWarehouse(Cart $cart, $deliveryInformation)
    {
        if ($cart != null) {
            //any store events are marked as unavailable
            $filteredItems = array_values(array_filter($cart->getItems(), function ($cartItem, $key) {
                return $cartItem instanceof StoreEvent;
            }, ARRAY_FILTER_USE_BOTH));
            foreach ($filteredItems as $item) {
                $cart->markAsUnavailable($item);
            }
        }
        if ($deliveryInformation != null) {
            /** @var DeliveryInformation $deliveryInformation */
            //ship to warehouse
            $deliveryInformation = new Shipping();
            $cart->setDeliveryInformation($deliveryInformation);
        }
    }
    /**
     * @return string
     */
    public function __toString()
    {
        return "OnlineShopping{\n"
            . "session=" . $this->session . "\n}";
    }
}
