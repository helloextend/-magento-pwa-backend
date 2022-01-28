<?php

namespace Extend\WarrantyGraphQl\Model\Resolver\Cart;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class WarrantyResolver implements ResolverInterface
{

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!$value['model']) {
            throw new GraphQlInputException(__('No Warranty plan found'));
        }

        $buyRequest = $value['model']->getBuyRequest();

        return [
            'planId' => $buyRequest->getData('planId'),
            'price' => $buyRequest->getData('price'),
            'term' => $buyRequest->getData('term'),
            'title' => $buyRequest->getData('title'),
            'product' => $buyRequest->getData('product')
        ];
    }
}
