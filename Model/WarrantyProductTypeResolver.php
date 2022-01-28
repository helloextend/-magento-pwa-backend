<?php


namespace Extend\WarrantyGraphQl\Model;


use Extend\Warranty\Model\Product\Type as Type;
use Magento\Framework\GraphQl\Query\Resolver\TypeResolverInterface;

class WarrantyProductTypeResolver implements TypeResolverInterface
{
    /**
     * @inheritdoc
     */
    public function resolveType(array $data): string
    {
        if (isset($data['type_id']) && $data['type_id'] == Type::TYPE_CODE) {
            return 'WarrantyProduct';
        }
        return '';
    }
}
