<?php


namespace Extend\WarrantyGraphQl\Observer;


use Extend\Warranty\Model\Product\Type as WarrantyType;
use Magento\Checkout\Helper\Cart;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class RemoveWarranties extends \Extend\Warranty\Observer\QuoteRemoveItem implements ObserverInterface
{

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$observer->getQuoteItem()) {
            /** @var $item \Magento\Quote\Model\Quote\Item */
            $item = $observer->getEvent()->getQuoteItem();
            $observer->setData('quote_item', $item);
        }
        return parent::execute($observer);
    }
}
