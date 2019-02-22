<?php

namespace Sleepz\PreselectProduct\Plugin\Ui\DataProvider\Product\Form\Modifier\Data;

use Magento\ConfigurableProduct\Ui\DataProvider\Product\Form\Modifier\Data\AssociatedProducts
    as OriginalAssociatedProducts;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Sleepz\PreselectProduct\Model\Product\Defaults;

class AssociatedProducts
{
    private $locator;

    private $productDefaults;

    public function __construct(
        LocatorInterface $locator,
        Defaults $productDefaults
    ) {
        $this->locator = $locator;
        $this->productDefaults = $productDefaults;
    }

    public function afterGetProductMatrix(
        OriginalAssociatedProducts $subject,
        array $productMatrix
    ) {
        if (!empty($productMatrix)) {
            $defaultProductId = $this->productDefaults->getDefaultProductId($this->locator->getProduct());

            foreach ($productMatrix as &$product) {
                $product['default_value'] = $product['id'];
                $product['checked'] = ($defaultProductId == $product['id']) ? 1 : 0;
            }
        }

        return $productMatrix;
    }
}
