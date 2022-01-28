<?php


namespace Extend\WarrantyGraphQl\Model\Resolver\Cart;


use Magento\CatalogGraphQl\Model\Resolver\Product\ProductFieldsSelector;
use Magento\CatalogGraphQl\Model\Resolver\Products\DataProvider\Deferred\Product as ProductDataProvider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ValueFactory;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class WarrantyProductResolver implements ResolverInterface
{
    /**
     * @var ProductDataProvider
     */
    private $productDataProvider;

    /**
     * @var ValueFactory
     */
    private $valueFactory;

    /**
     * @var ProductFieldsSelector
     */
    private $productFieldsSelector;

    /**
     * @param ProductDataProvider $productDataProvider
     * @param ValueFactory $valueFactory
     * @param ProductFieldsSelector $productFieldsSelector
     */
    public function __construct(
        ProductDataProvider $productDataProvider,
        ValueFactory $valueFactory,
        ProductFieldsSelector $productFieldsSelector
    )
    {
        $this->productDataProvider = $productDataProvider;
        $this->valueFactory = $valueFactory;
        $this->productFieldsSelector = $productFieldsSelector;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['product']['sku'])) {
            throw new GraphQlInputException(__('No child sku found for product link.'));
        }
        $this->productDataProvider->addProductSku($value['product']['sku']);
        $fields = $this->productFieldsSelector->getProductFieldsFromInfo($info);
        $this->productDataProvider->addEavAttributes($fields);

        $result = function () use ($value) {
            $data = $value['product'] ?? $this->productDataProvider->getProductBySku($value['sku']);
            if ($value['model']) {
                $quoteItem = $value['model'];
            }
            if (empty($data)) {
                return null;
            }
            if (!isset($data['model'])) {
                throw new LocalizedException(__('"model" value should be specified'));
            }
            $productModel = $data['model'];
            /** @var \Magento\Catalog\Model\Product $productModel */
            $data = $productModel->getData();
            $data['model'] = $productModel;

            if ($quoteItem) {
                $data['price'] = $quoteItem->getPrice();
            }
            if (!empty($productModel->getCustomAttributes())) {
                foreach ($productModel->getCustomAttributes() as $customAttribute) {
                    if (!isset($data[$customAttribute->getAttributeCode()])) {
                        $data[$customAttribute->getAttributeCode()] = $customAttribute->getValue();
                    }
                }
            }
            if ($quoteItem) {
                $data['price'] = $quoteItem->getPrice();
                $data['model']->setPrice($quoteItem->getPrice());
            }
            return array_replace($value, $data);
        };
        return $this->valueFactory->create($result);
    }
}
