<?php

namespace App\OnlineShoppingKata;

class HomeDelivery extends DeliveryInformation implements ModelObject
{

    private $deliveryAddress;
    private $pickupLocation;
    private Store $store;
    private  $weight;


    public function __construct(Store $store, int $weight)
    {
        $this->store = $store;
         $this->weight = $weight;
    }

    public function getStore(){
        return $this->store;
    }

     public function setDeliveryAddress(Store $storeAddress):void
    {
        $this->deliveryAddress = $storeAddress->getAddress();
    }

    public function getDeliveryAddress(){
        return $this->deliveryAddress;
    }
     public function setPickupLocation(Store $store)
    {
        $this->pickupLocation = $store;
    }

    public function setTotalWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return string
     */
    public function __toString() {
        // return '';
        return "DeliveryInformation{" . "\n" .
            "type='PICKUP'\n" .
            "deliveryAddress='" . $this->deliveryAddress . '\'' . "\n" .
            "pickupLocation=" . $this->store->getAddress() . "\n" .
            "weight=" . $this->weight . "\n" .
            '}';
    }

    /**
     * @throws UnsupportedOperationException
     */
    public function saveToDatabase()
    {
        throw new UnsupportedOperationException("missing from this exercise - shouldn't be called from a unit test");
    }
}
