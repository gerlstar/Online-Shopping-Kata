<?php

namespace App\OnlineShoppingKata;

class Shipping implements ModelObject
{

    private $deliveryAddress;
    // private Store $store;
    private int $weight;


    public function __construct()
    {
        // $this->store = $store;
        //  $this->weight = $weight;
        $address = "444 warehouse drive";
        //  $this->address = $address;
         $this->setDeliveryAddress($address);
    }

     public function setDeliveryAddress(string $address):void
    {
        $this->deliveryAddress = $address;
    }

    /**
     * @return string
     */
    public function __toString() {
        // return '';
        return "DeliveryInformation{" . "\n" .
            "type='PICKUP'\n" .
            "deliveryAddress='" . $this->deliveryAddress . '\'' . "\n" .
            "pickupLocation=" . $this->deliveryAddress . "\n" .
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
