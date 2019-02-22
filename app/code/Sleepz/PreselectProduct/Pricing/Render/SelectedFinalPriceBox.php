<?php

namespace Sleepz\PreselectProduct\Pricing\Render;

use Magento\Catalog\Pricing\Render\FinalPriceBox;

class SelectedFinalPriceBox extends FinalPriceBox
{
    private $layerResolver;
    private $productCollectionFactory;
    private $productRepository;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Pricing\SaleableInterface $saleableItem,
        \Magento\Framework\Pricing\Price\PriceInterface $price,
        \Magento\Framework\Pricing\Render\RendererPool $rendererPool,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        array $data = [],
        \Magento\Catalog\Model\Product\Pricing\Renderer\SalableResolverInterface $salableResolver = null,
        \Magento\Catalog\Pricing\Price\MinimalPriceCalculatorInterface $minimalPriceCalculator = null
    ) {
        $this->layerResolver = $layerResolver;
        $this->productRepository = $productRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $saleableItem, $price, $rendererPool, $data, $salableResolver, $minimalPriceCalculator);
    }

    public function getConfiguredSimple()
    {
        if(!$this->isProductList()) {
            return false;
        }
        $layer = $this->layerResolver->get();
        $activeFilters = $layer->getState()->getFilters();
        $filterSearch = [];
        foreach ($activeFilters as $k => $filter) {
            $attributeId = $filter->getFilter()->getAttributeModel()->getId();
            $filterValueArray = explode(",", $filter->getValueString());
            $filterSearch[$attributeId] = $filterValueArray[0];
        }

        $product = $this->getSaleableItem();
        $data = $product->getTypeInstance()->getConfigurableOptions($product);

        $options = [];
        $attrCodes = [];
        foreach ($data as $attrId => $attr) {
            foreach ($attr as $optVal) {
                if(!isset($options[$attrId])) {
                    $options[$attrId] = [];
                }
                if(!isset($options[$attrId][$optVal['value_index']])) {
                    $options[$attrId][$optVal['value_index']] = [];
                }
                $options[$attrId][$optVal['value_index']][] = $optVal['sku'];
                $attrCodes[$attrId] = $optVal['attribute_code'];
            }
        }

        $searchResults = [];
        foreach ($filterSearch as $searchId => $searchVal) {
            if(isset($options[$searchId]) && isset($options[$searchId][$searchVal])) {
                $searchResults[] = $options[$searchId][$searchVal];
            }
        }
        if(count($searchResults) > 1) {
            $result = call_user_func_array('array_intersect',$searchResults);
        } elseif (count($searchResults) == 1) {
            $result = reset($searchResults);
        } else {
            $result = false;
        }

        if($result && is_array($result) && count($result)) {
            $simpleProduct = null;
            foreach ($result as $foundSimpleSku) {
                $foundSimpleProduct = $this->productRepository->get($foundSimpleSku);
                if($simpleProduct) {
                    if($foundSimpleProduct->getPriceInfo()->getPrice('final_price')->getValue() < $simpleProduct->getPriceInfo()->getPrice('final_price')->getValue()) {
                        $simpleProduct = $foundSimpleProduct;
                    }
                } else {
                    $simpleProduct = $foundSimpleProduct;
                }
            }
            if($simpleProduct) {
                $urlArray = [];
                foreach ($attrCodes as $urlAttrId => $urlAttrCode) {
                    $urlArray[$urlAttrId] = $simpleProduct->getData($urlAttrCode);
                }
                $this->getSaleableItem()->setHashUrlPath(http_build_query($urlArray));
                return $simpleProduct;
            }
        }
        return false;
    }

    public function getConfiguredPrice()
    {
        $simpleProduct = $this->getConfiguredSimple();
        if($simpleProduct) {
            return $simpleProduct->getPriceInfo()->getPrice('final_price')->getValue();
        }
    }
}