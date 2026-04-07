<?php

namespace App\OnlineShoppingKata;

/**
 * Class Store
 * @package OnlineShoppingKata
 *
 * Represents a physical Store where you can go and buy
 * products and attend events.
 */
class Store implements ModelObject
{
    private $address;
    private $itemsInStock = [];
    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $droneDelivery;

    /**
     * Store constructor.
     * @param $name
     * @param $droneDelivery
     */
    public function __construct($name, $droneDelivery, string $address)
    {
        $this->name = $name;
        $this->droneDelivery = $droneDelivery;
        $this->address = $address;
    }

    public function getAddress(): string{
        return $this->address;
    }

    /**
     * @param Item[] $items
     */
    public function addStockedItems($items)
    {
        $this->itemsInStock = $items;

        // foreach ($items as $item) {
        //     /** @var Item $item */
        //     $this->itemsInStock['name'] = $item;
        // }
    }

    /**
     * @param StoreEvent $storeEvent
     */
    public function addStoreEvent(StoreEvent $storeEvent)
    {
        $this->itemsInStock[$storeEvent->getName()] = $storeEvent;
    }

    /**
     * @param Item[] $items
     */
    public function removeStockedItems($items)
    {
        foreach ($items as $item) {
            /** @var Item $item */
            unset($this->itemsInStock[$item->getName()]);
        }
    }

    /**
     * @param Item $item
     * @return bool
     */
    public function hasItem(Item $item)
    {
        // var_dump($this->itemsInStock);
        $result = array_filter($this->itemsInStock, function ($entry) use ($item) {
            return $entry->getName() == $item->getName();
        });

        return count($result) > 0;
        // return key_exists($item->getName(), $this->itemsInStock);
    }

    /**
     * @param $name
     * @return Item
     */
    public function getItem($name)
    {
        return $this->itemsInStock[$name];
    }

    /**
     * @return bool
     */
    public function hasDroneDelivery() {
        return $this->droneDelivery;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "Store{" .
            "name='" . $this->name . "\', " .
            "droneDelivery=" . $this->droneDelivery .
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