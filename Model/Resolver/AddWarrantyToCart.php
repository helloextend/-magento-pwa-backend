<?php


namespace Extend\WarrantyGraphQl\Model\Resolver;


use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\QuoteGraphQl\Model\Cart\AddProductsToCart;
use Magento\QuoteGraphQl\Model\Cart\GetCartForUser;
use \Extend\WarrantyGraphQl\Helper\Data as WarrantyGraphQlHelper;

class AddWarrantyToCart implements ResolverInterface
{
    /**
     * @var GetCartForUser
     */
    private $getCartForUser;

    /**
     * @var AddProductsToCart
     */
    private $addProductsToCart;

    protected $warrantyGraphQlHelper;

    /**
     * @param GetCartForUser $getCartForUser
     * @param AddProductsToCart $addProductsToCart
     */
    public function __construct(
        GetCartForUser $getCartForUser,
        AddProductsToCart $addProductsToCart,
        WarrantyGraphQlHelper $warrantyGraphQlHelper
    )
    {
        $this->getCartForUser = $getCartForUser;
        $this->addProductsToCart = $addProductsToCart;
        $this->warrantyGraphQlHelper = $warrantyGraphQlHelper;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $maskedCartId = $args['input']['cart_id'];
        $_qty = isset($args['input']['qty']) && $args['input']['qty'] ? $args['input']['qty'] : 0;
        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();

        $cart = $this->getCartForUser->execute($maskedCartId, $context->getUserId(), $storeId);

        $warrantyProduct = $this->warrantyGraphQlHelper->getWarrantyProduct();
        $warrantyRequestData = $args['input']['warranty'];

        //Check Qty
        $_relatedProduct = $warrantyRequestData['product'];

        $productsQty = 1;
        $cartWarrantiesItems = 0;
        foreach ($cart->getAllVisibleItems() as $_item) {
            if ($_item->getSku() == $_relatedProduct) {
                $productsQty = $_item->getQty();
            }

            if ($_item->getProductType() === 'warranty'
                && $_item->getOptionByCode('associated_product')->getValue() == $_relatedProduct) {
                $cartWarrantiesItems += $_item->getQty();
            }
        }

        if ($productsQty < $cartWarrantiesItems + $_qty) {
            return [
                'cart' => [
                    'model' => $cart,
                ],
            ];
        }

        if(!$_qty && $productsQty > $cartWarrantiesItems){
            $_qty = $productsQty - $cartWarrantiesItems;
        }

        $cartItem = [];

        $cartItem['data']['sku'] = $warrantyProduct->getSku();
        $cartItem['data']['quantity'] = $_qty;
        $cartItem['warranty'] = $warrantyRequestData;

        $cartItems = [
            $cartItem
        ];
        $this->addProductsToCart->execute($cart, $cartItems);;

        return [
            'cart' => [
                'model' => $cart,
            ],
        ];

    }
}
