<?php


namespace Extend\WarrantyGraphQl\Observer;


use Extend\Warranty\Model\Product\Type as WarrantyType;
use Magento\Checkout\Helper\Cart;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class RemoveWarranties implements ObserverInterface
{

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var $item \Magento\Quote\Model\Quote\Item */
        $item = $observer->getEvent()->getQuoteItem();
        if ($item->getProductType() !== WarrantyType::TYPE_CODE) {
            $sku = $item->getSku();

            $quote = $item->getQuote();
            $items = $quote->getAllItems();

            $removeWarranty = true;
            foreach ($items as $item) {
                if ($item->getSku() === $sku) {
                    $removeWarranty = false;
                    break;
                }
            }

            if ($removeWarranty) {
                foreach ($items as $item) {
                    if ($item->getProductType() === WarrantyType::TYPE_CODE &&
                        $item->getOptionByCode('associated_product')->getValue() === $sku) {

                        $quote->removeItem($item->getItemId());
                    }
                }
            }
        }
    }
}
