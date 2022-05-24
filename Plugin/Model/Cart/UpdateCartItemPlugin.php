<?php


namespace Extend\WarrantyGraphQl\Plugin\Model\Cart;

use Magento\Checkout\Model\Session as CheckoutSession;
use \Magento\Framework\Event\ManagerInterface;

class UpdateCartItemPlugin
{

    /**
     * @var \Extend\Warranty\Model\Normalizer
     */
    protected $normalizer;

    /**
     * Event manager proxy
     *
     * @var ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * UpdateCartItemPlugin constructor.
     * @param \Extend\Warranty\Model\Normalizer $normalizer
     * @param ManagerInterface $eventManager
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        \Extend\Warranty\Model\Normalizer $normalizer,
        ManagerInterface $eventManager,
        CheckoutSession $checkoutSession
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->_eventManager = $eventManager;
        $this->normalizer = $normalizer;
    }

    /**
     * TODO Update once base extend warranty have function to normalize quote and cart
     *
     * @param $subject
     * @param $result
     * @param $cart
     * @param $cartItemId
     * @param $quantity
     * @param $customizableOptionsData
     * @return mixed
     */
    public function afterExecute($subject, $result, $cart, $cartItemId, $quantity, $customizableOptionsData)
    {
        $cartId = $this->checkoutSession->getQuoteId();
        $this->checkoutSession->setQuoteId($cart->getId());

        $this->_eventManager->dispatch(
            'extend_checkout_cart_update_items_after',
            ['cart' => false]
        );

        $this->checkoutSession->setQuoteId($cartId);

        return $result;
    }
}
