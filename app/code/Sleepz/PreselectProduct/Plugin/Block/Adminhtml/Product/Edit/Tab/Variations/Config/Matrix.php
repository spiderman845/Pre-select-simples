<?php

namespace Sleepz\PreselectProduct\Plugin\Block\Adminhtml\Product\Edit\Tab\Variations\Config;

use Sleepz\PreselectProduct\Model\Product\Defaults;
use Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Variations\Config\Matrix as MagentoMatrix;

class Matrix
{
    private $productDefaults;

    public function __construct(
        Defaults $productDefaults
    ) {
        $this->productDefaults = $productDefaults;
    }

    public function afterGetProductMatrix(
        MagentoMatrix $subject,
        $result
    ) {
        $usedProductId = $this->productDefaults->getDefaultProductId($subject->getProduct());

        if ($usedProductId) {
            foreach ($result as $i => $product) {
                if ($usedProductId == $product['productId']) {
                    $result[$i]['default'] = true;
                }
            }
        }

        return $result;
    }
}
