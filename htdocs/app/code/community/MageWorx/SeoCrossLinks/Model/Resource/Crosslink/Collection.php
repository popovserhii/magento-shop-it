<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Model_Resource_Crosslink_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('mageworx_seocrosslinks/crosslink');
    }

    /**
     * Map of template fields
     *
     * @var array
     */
    protected $_map = array('fields' => array(
        'crosslink_id'     => 'main_table.crosslink_id',
        'store'            => 'store_table.store_id'
    ));

    /**
     * Add type filter
     *
     * @return this
     */
    public function addEnabledFilter()
    {
        return $this->getSelect()->where('main_table.status = 1');
    }

    /**
     * Add filter by some text
     *
     * @param string $content
     * @return this
     */
    public function addContentFilter($content)
    {
        return $this->getSelect()->where("(?) LIKE CONCAT('%', TRIM(BOTH '+' FROM `keyword`), '%')", $content);
    }

    /**
     * Add product destination filter
     *
     * @return this
     */
    public function addInProductFilter()
    {
        return $this->getSelect()->where('main_table.in_product = 1');
    }

    /**
     * Add category destination filter
     *
     * @return this
     */
    public function addInCategoryFilter()
    {
        return $this->getSelect()->where('main_table.in_category = 1');
    }

    /**
     * Add CMS Page destination filter
     *
     * @return this
     */
    public function addInCmsPageFilter()
    {
        return $this->getSelect()->where('main_table.in_cms_page = 1');
    }

    /**
     * Add blog destination filter
     *
     * @return this
     */
    public function addInBlogFilter()
    {
        return $this->getSelect()->where('main_table.in_blog = 1');
    }

    /**
     * Add filter by store
     *
     * @param int|Mage_Core_Model_Store $store
     * @param bool $withAdmin
     * @return Mage_Cms_Model_Resource_Page_Collection
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            if ($store instanceof Mage_Core_Model_Store) {
                $store = array($store->getId());
            }

            if (!is_array($store)) {
                $store = array($store);
            }

            if ($withAdmin) {
                $store[] = Mage_Core_Model_App::ADMIN_STORE_ID;
            }

            $this->addFilter('store_id', array('in' => $store), 'public');
        }
        return $this;
    }

    /**
     * Join store relation table if there is store filter
     */
    protected function _renderFiltersBefore()
    {
        $this->getSelect()->join(
            array('store_table' => $this->getTable('mageworx_seocrosslinks/crosslink_store')),
            'main_table.crosslink_id = store_table.crosslink_id',
            array()
        )->group('main_table.crosslink_id');

        /*
         * Allow analytic functions usage because of one field grouping
         */
        $this->_useAnalyticFunction = true;
        return parent::_renderFiltersBefore();
    }

    /**
     * Retrive count items in collection
     *
     * @return int
     */
    public function getSize()
    {
        $select = clone $this->getSelect();
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $data = $this->_fetchAll($select);
        return count($data);
    }
}