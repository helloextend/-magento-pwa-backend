<?php


namespace Extend\WarrantyGraphQl\Model\Resolver;


use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use \Extend\Warranty\Helper\Api\Data as WarrantyApiConfig;
use \Extend\Warranty\ViewModel\Installation as WarrantyInstallation;
use Magento\Framework\Serialize\Serializer\Json;

class StoreConfig implements ResolverInterface
{

    private static $extendSDisplaySettingDisabled = 'DISABLED';

    /**
     * @var WarrantyApiConfig
     */
    protected $warrantyApiConfig;

    /**
     * @var WarrantyInstallation
     */
    protected $warrantyInstallation;

    protected $jsonSerializer;

    public function __construct(
        WarrantyApiConfig    $warrantyConfig,
        WarrantyInstallation $warrantyInstallation,
        Json                 $jsonSerializer
    )
    {
        $this->warrantyApiConfig = $warrantyConfig;
        $this->warrantyInstallation = $warrantyInstallation;
        $this->jsonSerializer = $jsonSerializer;
    }

    public function resolve(
        Field       $field,
                    $context,
        ResolveInfo $info,
        array       $value = null,
        array       $args = null
    )
    {
        if (empty($this->computedSettings)) {
            $this->computedSettings = [
                'warranty_environment' => self::$extendSDisplaySettingDisabled,             // Extend\Warranty\ViewModel\Installation -> getJsonConfig -> environment         // Extend\Warranty\ViewModel\Installation -> getExtendStoreId
                'warranty_js_lib_url' => self::$extendSDisplaySettingDisabled,             // Extend\Warranty\ViewModel\Installation -> getJsonConfig -> environment            // Extend\Warranty\ViewModel\Installation -> getJsMode
            ];

            if ($this->warrantyApiConfig->isExtendEnabled()) {
                $jsonConfig = $this->warrantyInstallation->getJsonConfig();
                $jsonConfig = $this->jsonSerializer->unserialize($jsonConfig);
                $this->computedSettings = [
                    'warranty_environment' => $jsonConfig['environment'],
                    'warranty_js_lib_url' => $this->warrantyInstallation->getJsMode(),
                    'warranty_store_id' => isset($jsonConfig['storeId']) ? $jsonConfig['storeId'] : ''
                ];
            }
        }

        return $this->computedSettings[$info->fieldName] ?? null;
    }
}
