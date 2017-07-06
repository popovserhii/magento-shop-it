<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

abstract class MageWorx_XSitemap_Model_Mysql4_Catalog_Product_AbstractXml extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * For sites where all products belong to one website and
     * distribution of products in shops will be organized
     * by purpose of a product in category belonging to certain shops.
     * In this case in sitemap excess products of other shops.
     * Set FILTER_PRODUCT = 1 to prevent it.
     *
     */
    const FILTER_PRODUCT = 0;

    /**
     * Collection Zend Db select
     *
     * @var Zend_Db_Select
     */
    protected $_select;

    /**
     * Attribute cache
     *
     * @var array
     */
    protected $_attributesCache = array();

    abstract public function getCollection($storeId, $onlyCount = false, $limit = 4000000000, $from = 0);

    /**
     * Init resource model (catalog/category)
     */
    protected function _construct()
    {
        $this->_init('catalog/product', 'entity_id');
    }

    /**
     * Add attribute to filter
     *
     * @param int $storeId
     * @param string $attributeCode
     * @param mixed $value
     * @param string $type
     *
     * @return Zend_Db_Select
     */
    protected function _addFilter($storeId, $attributeCode, $value, $type = '=')
    {
        if (!isset($this->_attributesCache[$attributeCode])) {
            $attribute = Mage::getSingleton('catalog/product')->getResource()->getAttribute($attributeCode);

            $this->_attributesCache[$attributeCode] = array(
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id'   => $attribute->getId(),
                'table'          => $attribute->getBackend()->getTable(),
                'is_global'      => $attribute->getIsGlobal() == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                'backend_type'   => $attribute->getBackendType()
            );
        }

        $attribute = $this->_attributesCache[$attributeCode];

        if (!$this->_select instanceof Zend_Db_Select) {
            return false;
        }

        switch ($type) {
            case '=':
                $conditionRule = '=?';
                break;
            case 'in':
                $conditionRule = ' IN(?)';
                break;
            default:
                return false;
                break;
        }

        if ($attribute['backend_type'] == 'static') {
            $this->_select->where('e.' . $attributeCode . $conditionRule, $value);
        }
        else {
            $this->_select->join(
                    array('t1_' . $attributeCode => $attribute['table']),
                    'e.entity_id=t1_' . $attributeCode . '.entity_id AND t1_' . $attributeCode . '.store_id=0', array()
                )
                ->where('t1_' . $attributeCode . '.attribute_id=?', $attribute['attribute_id']);

            if ($attribute['is_global']) {
                $this->_select->where('t1_' . $attributeCode . '.value' . $conditionRule, $value);
            }
            else {
                $this->_select->joinLeft(
                        array('t2_' . $attributeCode => $attribute['table']),
                        $this->_getWriteAdapter()->quoteInto('t1_' . $attributeCode . '.entity_id = t2_' . $attributeCode . '.entity_id AND t1_' . $attributeCode . '.attribute_id = t2_' . $attributeCode . '.attribute_id AND t2_' . $attributeCode . '.store_id=?',
                            $storeId), array()
                    )
                    ->where('IFNULL(t2_' . $attributeCode . '.value, t1_' . $attributeCode . '.value)' . $conditionRule,
                        $value);
            }
        }

        return $this->_select;
    }

    /**
     * Prepare product
     *
     * @param array $productRow
     * @return Varien_Object
     */
    protected function _prepareProduct(array $productRow, $isAddImage = false)
    {
        $product    = new Varien_Object();
        $product->setId($productRow[$this->getIdFieldName()]);

        if(!empty($productRow['url'])){
            if(strpos($productRow['url'], '@') !== false){
                list($productUrl, $categoryId) = explode('@', $productRow['url']);
            }else{
                $productUrl = $productRow['url'];
            }
        }

        $categoryPart = (!empty($categoryId)) ? '/category/' . $categoryId : "";

        $productUrl = !empty($productUrl) ? $productUrl : 'catalog/product/view/id/' . $product->getId();
        $product->setUrl($productUrl);
        $product->setUpdatedAt($productRow['updated_at']);
        $product->setCreatedAt($productRow['created_at']);

        $productTargetPath = !empty($productRow['target_path']) ? $productRow['target_path'] : 'catalog/product/view/id/' . $product->getId() . $categoryPart;
        $product->setTargetPath($productTargetPath);

        if (isset($productRow['canonical_cross_domain'])){
            $product->setCanonicalCrossDomain($productRow['canonical_cross_domain']);
        }

        if ($isAddImage) {

            if (isset($productRow['media'])) {
                $product->setImage($productRow['media']);
            }

            $attribute  = Mage::getSingleton('catalog/product')->getResource()->getAttribute('media_gallery');
            $media      = Mage::getResourceSingleton('catalog/product_attribute_backend_media');
            $gallery = $media->loadGallery($product, new Varien_Object(array('attribute' => $attribute)));
            if (count($gallery)) {
                $product->setGallery($gallery);
            }
        }

        return $product;
    }

    /**
     * See description for FILTER_PRODUCT
     * @param int $storeId
     * @return array
     */
    protected function _getStoreProductIds($storeId)
    {
        $categories = Mage::getResourceModel('xsitemap/catalog_category')->getCollection($storeId);
        $catIds     = array_keys($categories);

        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('category_id', array('in' => $catIds));

        return $collection->getAllIds();
    }

}