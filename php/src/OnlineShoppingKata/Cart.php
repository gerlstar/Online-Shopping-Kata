<?php
namespace App\OnlineShoppingKata;
/**
 * Class Cart
 * @package OnlineShoppingKata
 *
 * While shopping online in a Store, the Cart stores the Items you intend to buy
 */
class Cart implements ModelObject
{
    private $items = [];
    private $unavailableItems = [];
    private $deliveryInformation;
    /**
     * each cart is delivered somehow 
     * (Home, pickup or shipping)
     * @param $deliveryInformation
     */
    public function setDeliveryInformation($deliveryInformation)
    {
        $this->deliveryInformation = $deliveryInformation;
    }
    public function getTotalWeight()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->getWeight();
        }
        return $total;
    }
    public function getDeliveryInformation()
    {
        return $this->deliveryInformation;
    }
    /**
     * @return Item[]|array
     */
    public function getItems()
    {
        return $this->items;
    }
    /**
     * @param Item $item
     */
    public function addItem(Item $item)
    {
        $this->items[] = $item;
    }
    /**
     * @param Item[] $items
     */
    public function addItems($items)
    {
        foreach ($items as $item) {
            $this->items[] = $item;
        }
    }
    /**
     * @param Item $item
     */
    public function markAsUnavailable(Item $item)
    {
        $this->unavailableItems[] = $item;
    }
    /**
     * @return string
     */
    public function __toString()
    {
        return "Cart{" .
            "items=" . $this->displayItems($this->items) .
            "unavailable=" . $this->displayItems($this->unavailableItems) .
            '}';
    }
    /**
     * @param $items
     * @return string
     */
    private function displayItems($items)
    {
        $itemDisplay = "\n";
        foreach ($items as $item) {
            /** @var $item Item */
            $itemDisplay .= $item . "\n";
        }
        return $itemDisplay;
    }
    /**
     * @throws UnsupportedOperationException
     */
    public function saveToDatabase()
    {
        throw new UnsupportedOperationException("missing from this exercise - shouldn't be called from a unit test");
    }
    /**
     * @return Item[]|array
     */
    public function getUnavailableItems()
    {
        return $this->unavailableItems;
    }
    public function reduceWeight($weight)
    {
        foreach ($this->getUnavailableItems() as $unavailableItem) {
            /** @var Item $unavailableItem */
            $weight -= $unavailableItem->getWeight();
        }
        return $weight;
    }
    public function switchItems(int $weight, Store $newStore)
    {
        $newItems = [];
        foreach ($this->getItems() as $item) {
            /** @var Item $item */
            if ($item instanceof StoreEvent && $newStore->hasItem($item)) {
                // if ($item->getType() === "EVENT") {
                $this->markAsUnavailable($item);
                //put the $item in the new store
                $newItems[] = $newStore->getItem($item->getName());
            } else if ($item instanceof StoreEvent) {
                $this->markAsUnavailable($item);
            } else if (!$newStore->hasItem($item)) {
                $this->markAsUnavailable($item);
            }
            $weight += $item->getWeight();
        }
        return [
            'weight' => $weight,
            'newItems' => $newItems,
        ];
    }
}
