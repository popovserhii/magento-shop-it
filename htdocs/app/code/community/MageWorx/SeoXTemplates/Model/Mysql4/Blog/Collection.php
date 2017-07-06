<?php
/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoXTemplates_Model_Mysql4_Blog_Collection extends AW_Blog_Model_Mysql4_Blog_Collection
{
    /**
     * Get size
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

    /**
     * Perform operations after collection load
     *
     * @return Mage_Cms_Model_Resource_Page_Collection
     */
    protected function _afterLoad()
    {
        if($this->getFlag('mageworx_store')){
            foreach ($this->getItems() as $view) {
            if ( $view->getStoreIds() && $view->getStoreIds() != 0 ) {
                $view->setStoreIds(array_unique(explode(',', $view->getStoreIds())));
                }
            }
        }
        return parent::_afterLoad();
    }

    public function joinCategories()
    {
        $this->getSelect()
            ->joinLeft(
                array('post_cat_table' => $this->getTable('blog/post_cat')),
                'main_table.post_id = post_cat_table.post_id',
                array()
             )
            ->joinLeft(
                array('cat_table' => $this->getTable('blog/cat')),
                'post_cat_table.cat_id = cat_table.cat_id',
                array('cat_ids' => 'GROUP_CONCAT(cat_table.title)')
             )
             ->group('main_table.post_id');

        return $this;
    }

    public function joinStores()
    {
        $this->getSelect()
            ->joinLeft(
                array('blog_store_table' => $this->getTable('store')),
                'main_table.post_id = blog_store_table.post_id',
                array('store_ids' => 'GROUP_CONCAT(blog_store_table.store_id)')
            )
            ->group('main_table.post_id');

        return $this;
    }
}
