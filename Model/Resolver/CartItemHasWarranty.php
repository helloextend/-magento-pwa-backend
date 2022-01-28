<?php


namespace Extend\WarrantyGraphQl\Model\Resolver;


use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\QuoteGraphQl\Model\Cart\GetCartForUser;

class CartItemHasWarranty implements ResolverInterface
{

    /**
     * @var GetCartForUser
     */
    private $getCartForUser;

    /**
     * @param GetCartForUser $getCartForUser
     */
    public function __construct(
        GetCartForUser $getCartForUser
    )
    {
        $this->getCartForUser = $getCartForUser;
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return bool|\Magento\Framework\GraphQl\Query\Resolver\Value|mixed
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $product = $value['product'];

        $quoteItem = $value['model'];
        $quote = $quoteItem->getQuote();

        foreach ($quote->getAllVisibleItems() as $item) {
            if ($item->getProductType() === 'warranty') {
                if ($item->getOptionByCode('associated_product')->getValue() === $quoteItem->getSku()) {
                    return true;
                }
            }
        }
        return false;
    }
}
