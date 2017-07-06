<?php
/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

abstract class MageWorx_SeoXTemplates_Model_Mysql4_Template_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Map of template fields
     *
     * @var array
     */
    protected $_map = array('fields' => array(
        'template_id'   => 'main_table.template_id',
        'store_id'      => 'main_table.store_id',
    ));

    /**
     * Add cron filter
     *
     * @return MageWorx_SeoXTemplates_Model_Mysql4_Template_Collection
     */
    public function addCronFilter()
    {
        $this->getSelect()->where('main_table.use_cron = ?', MageWorx_SeoXTemplates_Helper_Template::CRON_ENABLED);
        return $this;
    }

    /**
     * Add store filter
     *
     * @param int $id
     * @return MageWorx_SeoXTemplates_Model_Mysql4_Template_Collection
     */
    public function addStoreFilter($id)
    {
        $this->getSelect()->where('main_table.store_id = ' . $id . ' or main_table.store_id = 0');
        return $this;
    }

    /**
     * Add type filter
     * @param int|array $ids
     * @return MageWorx_SeoXTemplates_Model_Mysql4_Template_Collection
     */
    public function addTypeFilter($ids){
        if(!is_array($ids)){
            $ids = array($ids);
        }
        if(!empty($ids)){
            $this->getSelect()->where('main_table.type_id IN (?)', $ids);
        }
        return $this;
    }

    /**
     * Add assign type filter
     * @param int|array $ids
     * @return MageWorx_SeoXTemplates_Model_Mysql4_Template_Collection
     */
    public function addAssignTypeFilter($ids){
        if(!is_array($ids)){
            $ids = array($ids);
        }
        if(!empty($ids)){
            $this->getSelect()->where('main_table.assign_type IN (?)', $ids);
        }
        return $this;
    }


    /**
     * Add template filter
     * @param int|array $ids
     * @return MageWorx_SeoXTemplates_Model_Mysql4_Template_Collection
     */
    public function excludeTemplateFilter($ids){
        if(!is_array($ids)){
            $ids = array($ids);
        }
        if(!empty($ids)){
            $this->getSelect()->where('main_table.template_id NOT IN (?)', $ids);
        }
        return $this;
    }

    /**
     * Add store filter
     * @param int $id
     * @return MageWorx_SeoXTemplates_Model_Mysql4_Template_Collection
     */
    public function addSpecificStoreFilter($id)
    {
        $this->getSelect()->where('main_table.store_id = ?', $id);
        return $this;
    }

    /**
     * Add reset filter
     * @return MageWorx_SeoXTemplates_Model_Mysql4_Template_Collection
     */
    public function addResetFilter()
    {
        $this->getSelect()->reset('where');
        return $this;
    }

    /**
     *
     * @param int|array $ids
     * @return MageWorx_SeoXTemplates_Model_Mysql4_Template_Collection
     */
    public function loadByIds($ids){
        if(!is_array($ids)){
            $ids = array($ids);
        }
        if(!empty($ids)){
            $this->getSelect()->where('main_table.template_id IN (?)', $ids);
        }
        return $this;
    }

}