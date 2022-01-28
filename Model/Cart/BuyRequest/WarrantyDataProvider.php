<?php


namespace Extend\WarrantyGraphQl\Model\Cart\BuyRequest;


use Magento\QuoteGraphQl\Model\Cart\BuyRequest\BuyRequestDataProviderInterface;

class WarrantyDataProvider implements BuyRequestDataProviderInterface
{
    public function execute(array $cartItemData): array
    {
        if (!isset($cartItemData['warranty'])) {
            return [];
        }
        $warranty = $cartItemData['warranty'];

        //TODO add warranty items validation

        return [
            'planId' => $warranty['planId'],
            'price' => $warranty['price'],
            'term' => $warranty['term'],
            'title' => $warranty['title'],
            'product' => $warranty['product']
        ];
    }
}
