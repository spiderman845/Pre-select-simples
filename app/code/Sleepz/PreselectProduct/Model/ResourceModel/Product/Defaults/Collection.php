<?php
namespace Sleepz\PreselectProduct\Model\ResourceModel\Product\Defaults;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * Link table name
     *
     * @var string
     */
    private $linkTable;

    /**
     * Assign link table name
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->linkTable = $this->getTable('sleepz_catalog_product_default_super_link');
    }

    /**
     * Init select
     * @return $this|\Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->join(
            ['link_table' => $this->linkTable],
            'link_table.product_id = e.entity_id',
            ['parent_id']
        );

        return $this;
    }

    private function getLinkTable()
    {
        return $this->getTable('sleepz_catalog_product_default_super_link');
    }

    /**
     * Set Product filter to result
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     */
    public function setProductFilter($product)
    {
        $this->getSelect()->where('link_table.parent_id = ?', (int)$product->getId());
        return $this;
    }

    public function updateProduct($mainProduct, $productId)
    {
        if ($mainProduct->getId() && $productId) {
            $this->getConnection()->update(
                $this->getLinkTable(),
                ['product_id' => (int)$productId],
                ['parent_id = ?' => $mainProduct->getId()]
            );
        }
    }

    public function saveProduct($mainProduct, $productId)
    {
        $data = ['product_id' => (int)$productId, 'parent_id' => (int)$mainProduct->getId()];
        $this->getConnection()->insertOnDuplicate($this->getLinkTable(), $data);
    }
}
