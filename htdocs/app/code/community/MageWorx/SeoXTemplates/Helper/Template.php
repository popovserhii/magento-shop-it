<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
abstract class MageWorx_SeoXTemplates_Helper_Template extends Mage_Core_Helper_Abstract
{
    const ASSIGN_ALL_ITEMS        = 1;
    const ASSIGN_GROUP_ITEMS      = 3;
    const ASSIGN_INDIVIDUAL_ITEMS = 2;

    const CRON_ENABLED  = 1;
    const CRON_DISABLED = 2;

    const WRITE_FOR_EMPTY  = 1;
    const WRITE_FOR_ALL    = 2;

    /**
     * Retrive array (Type Id -> Type Title)
     * @return array
     */
    abstract function getTypeArray();

    /**
     * Retrive array (Type Id -> Type Code)
     * @return array
     */
    abstract function getTypeCodeArray();

    /**
     * Retrive array (Assign Type Id -> Relation Title)
     * @return array
     */
    abstract function getAssignTypeArray();

    /**
     *
     * @param int $writeFor
     * @return bool
     */
    public function isWriteForEmpty($writeFor)
    {
        return (self::WRITE_FOR_EMPTY == $writeFor) ? true : false;
    }

    /**
     *
     * @param int $writeFor
     * @return bool
     */
    public function isWriteForAll($writeFor)
    {
        return (self::WRITE_FOR_ALL == $writeFor) ? true : false;
    }

    /**
     *
     * @return int
     */
    public function getWriteForEmpty()
    {
        return self::WRITE_FOR_EMPTY;
    }

    /**
     *
     * @return int
     */
    public function getWriteForAll()
    {
        return self::WRITE_FOR_ALL;
    }

    /**
     *
     * @param int $assignType
     * @return bool
     */
    public function isAssignForAllItems($assignType)
    {
        return (self::ASSIGN_ALL_ITEMS == $assignType) ? true : false;
    }

    /**
     *
     * @param int $assignType
     * @return bool
     */
    public function isAssignForIndividualItems($assignType)
    {
        return (self::ASSIGN_INDIVIDUAL_ITEMS == $assignType) ? true : false;
    }

    /**
     *
     * @param int $assignType
     * @return bool
     */
    public function isAssignForGroupItems($assignType)
    {
        return (self::ASSIGN_GROUP_ITEMS == $assignType) ? true : false;
    }

    /**
     *
     * @return int
     */
    public function getAssignForAllItems()
    {
        return self::ASSIGN_ALL_ITEMS;
    }


    /**
     *
     * @return int
     */
    public function getAssignForIndividualItems()
    {
        return self::ASSIGN_INDIVIDUAL_ITEMS;
    }


    /**
     *
     * @return int
     */
    public function getAssignForGroupItems()
    {
        return self::ASSIGN_GROUP_ITEMS;
    }

    /**
     * Retrive type label by type id
     * @param int $id
     * @return string
     */
    public function getTypeByTypeId($id = null)
    {
        if (is_null($id)) {
            $id = $this->getTypeId();
        }
        $types = $this->getTypeArray();
        if (!empty($types[$id])) {
            return $types[$id];
        }
    }

    /**
     * Retrive type id by type code
     * @param string $typeName
     * @return int|null
     */
    public function getTypeIdByTypeCode($typeName)
    {
        $types = $this->getTypeCodeArray();
        $ids   = array_keys($types, $typeName);

        if (is_array($ids) && count($ids)) {
            return $ids[0];
        }
        return null;
    }

    /**
     * @toDo transfer to factory class
     * @param type $template
     * @return MageWorx_SeoXTemplates_Model_Adapter_Product_Abstract
     */
    public function getTemplateAdapterByModel($template)
    {
        $typeNames           = $this->getTypeCodeArray();
        $name                = str_ireplace(' ', '_', strtolower($typeNames[$template->getTypeId()]));
        $templateCodeArray   = explode('_', $name);
        $currentTemplateCode = array_shift($templateCodeArray) . "_" . join('', $templateCodeArray);
        $adapterClassName    = 'mageworx_seoxtemplates/adapter_' . $currentTemplateCode;

        return Mage::getSingleton($adapterClassName);
    }

    /**
     *
     * @return array
     */
    public function getUseCronArray()
    {
        return array(
            self::CRON_DISABLED => Mage::helper('catalog')->__('No'),
            self::CRON_ENABLED  => Mage::helper('catalog')->__('Yes'),
        );
    }

    /**
     *
     * @return type
     */
    public function getWriteForArray()
    {
        return array(
            self::WRITE_FOR_EMPTY  => Mage::helper('catalog')->__('Empty'),
            self::WRITE_FOR_ALL => Mage::helper('catalog')->__('All'),
        );
    }

    /**
     *
     * @return array
     */
    public function getAllTypeOptions()
    {
        return array('' => Mage::helper('catalog')->__('-- Please Select --')) + $this->getTypeArray();
    }
}
