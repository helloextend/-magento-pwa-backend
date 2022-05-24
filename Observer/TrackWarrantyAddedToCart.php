<?php


namespace Extend\WarrantyGraphQl\Observer;


use Extend\Warranty\Model\Product\Type as WarrantyType;
use Magento\Checkout\Helper\Cart;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class TrackWarrantyAddedToCart implements ObserverInterface
{

    /**
     * @var \Extend\Warranty\Helper\Tracking
     */
    protected $_trackingHelper;


    /**
     * QuoteRemoveItem constructor.
     * @param \Extend\Warranty\Helper\Tracking $trackingHelper
     */
    public function __construct(
        \Extend\Warranty\Helper\Tracking $trackingHelper
    )
    {
        $this->_trackingHelper = $trackingHelper;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $warrantyData = $observer->getWarrantyData();
        $qty = $observer->getQty();
        if ($this->_trackingHelper->isTrackingEnabled()) {
            if (!isset($warrantyData['component']) || $warrantyData['component'] !== 'modal') {
                $trackingData = [
                    'eventName' => 'trackOfferAddedToCart',
                    'productId' => $warrantyData['product'] ?? '',
                    'productQuantity' => $qty,
                    'warrantyQuantity' => $qty,
                    'planId' => $warrantyData['planId'] ?? '',
                    'area' => 'product_page',
                    'component' => $warrantyData['component'] ?? 'buttons',
                ];
            } else {
                $trackingData = [
                    'eventName' => 'trackOfferUpdated',
                    'productId' => $warrantyData['product'] ?? '',
                    'productQuantity' => $qty,
                    'warrantyQuantity' => $qty,
                    'planId' => $warrantyData['planId'] ?? '',
                    'area' => 'product_page',
                    'component' => $warrantyData['component'] ?? 'buttons',
                ];
            }
            $this->_trackingHelper->setTrackingData($trackingData);
        }
    }
}
