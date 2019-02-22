<?php
namespace Sleepz\PreselectProduct\Model\Product;

use Sleepz\PreselectProduct\Model\ResourceModel\Product\Defaults\CollectionFactory;

class Defaults
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * Defaults constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param $product
     *
     * @return \Magento\Framework\DataObject
     */
    public function getDefaultProductId($product)
    {
        $collection = $this->collectionFactory->create()->setFlag(
            'require_stock_items',
            true
        )->setFlag(
            'product_children',
            true
        )->setProductFilter(
            $product
        );
        if ($this->getStoreFilter($product) !== null) {
            $collection->addStoreFilter($this->getStoreFilter($product));
        }

        return $collection->getFirstItem()->getId();
    }

    /**
     * Retrieve store filter for associated products
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return int|\Magento\Store\Model\Store
     */
    public function getStoreFilter($product)
    {
        $cacheKey = '_cache_instance_store_filter';
        return $product->getData($cacheKey);
    }

    /**
     * Save default product relations
     *
     * @param \Magento\Catalog\Model\Product $mainProduct the parent id
     * @param                                $productId
     *
     * @return $this
     * @internal param array $productIds the children id array
     */
    public function saveProduct($mainProduct, $productId)
    {
        if ($mainProduct instanceof \Magento\Catalog\Model\Product) {
            $oldProductId = $this->getDefaultProductId($mainProduct);
            if ($oldProductId) {
                $this->collectionFactory->create()->updateProduct($mainProduct, $productId);
            } else {
                $this->collectionFactory->create()->saveProduct($mainProduct, $productId);
            }
        }

        return $this;
    }
}
