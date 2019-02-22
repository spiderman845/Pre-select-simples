<?php
namespace Sleepz\PreselectProduct\Plugin\Controller\Adminhtml\Product\Initialization\Helper\Plugin;

use Sleepz\PreselectProduct\Model\Product\Defaults;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper;
use Magento\ConfigurableProduct\Model\Product\VariationHandler;
use Magento\Framework\App\RequestInterface;

class UpdateDefault
{
    /** @var ProductRepositoryInterface  */
    private $productRepository;

    /** @var RequestInterface */
    private $request;

    /** @var VariationHandler */
    private $variationHandler;

    /**
     * @param RequestInterface           $request
     * @param ProductRepositoryInterface $productRepository
     * @param VariationHandler           $variationHandler
     * @param Defaults                   $productDefaults
     */
    public function __construct(
        RequestInterface $request,
        ProductRepositoryInterface $productRepository,
        VariationHandler $variationHandler,
        Defaults $productDefaults
    ) {
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->variationHandler = $variationHandler;
        $this->productDefaults = $productDefaults;
    }

    /**
     * Update data for configurable product configurations
     *
     * @param Helper                                          $subject
     * @param ProductInterface|\Magento\Catalog\Model\Product $configurableProduct
     *
     * @return \Magento\Catalog\Model\Product
     * @SuppressWarnings(PMD.UnusedFormalParameter)
     */
    public function afterInitialize(
        Helper $subject,
        ProductInterface $configurableProduct
    ) {
        $defaultProductId = false;
        $configurableMatrix = $this->request->getParam('configurable-matrix-serialized', '[]');
        if (!empty($configurableMatrix) && $configurableMatrix != '[]') {
            $configurableMatrix = json_decode($configurableMatrix, true);
        } else {
            $productData = $this->request->getParam('product', '[]');
            if (isset($productData['configurable-matrix-serialized'])) {
                $configurableMatrix = json_decode($productData['configurable-matrix-serialized'], true);
            }
        }

        if (is_array($configurableMatrix) && !empty($configurableMatrix)) {
            foreach ($configurableMatrix as $item) {
                if (isset($item['checked']) && $item['checked'] == 1) {
                    $defaultProductId = $item['id'];
                }
            }
        }

        if ($defaultProductId) {
            $this->productDefaults->saveProduct($configurableProduct, $defaultProductId);
        }

        return $configurableProduct;
    }
}