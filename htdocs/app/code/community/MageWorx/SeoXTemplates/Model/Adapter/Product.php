<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
abstract class MageWorx_SeoXTemplates_Model_Adapter_Product extends MageWorx_SeoXTemplates_Model_Adapter
{
    protected function _apply($template)
    {
        $attributes  = array();
        $connection  = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();

        $select        = $connection->select()
            ->from($tablePrefix . 'eav_entity_type')
            ->where("entity_type_code = 'catalog_product'");
        $productTypeId = $connection->fetchOne($select);

        foreach ($this->_attributeCodes as $_attrName) {
            $select                 = $connection->select()
                ->from($tablePrefix . 'eav_attribute')
                ->where("entity_type_id = $productTypeId AND (attribute_code = '" . $_attrName . "')");

            if($res = $connection->fetchRow($select)){
                $attributes[$_attrName] = $res;
            }
        }

        if($this->_testMode == 'csv' && $this->_collection->count()){

            $path = Mage::helper('mageworx_seoxtemplates')->getCsvFilePath();
            $name = Mage::helper('mageworx_seoxtemplates')->getCsvFileName($template);
            $file = Mage::helper('mageworx_seoxtemplates')->getCsvFile($path, $name);

            if(!file_exists($file)){
                $csvHeader = $this->_getHeaderData();
            }

            $io = new Varien_Io_File();
            $io->open(array('path' => $path));
            $io->streamOpen($file, 'a+');
            $io->streamLock(true);
            $io->setAllowCreateFolders(true);

            if(!empty($csvHeader)){
                $io->streamWriteCsv($csvHeader);
            }
        }

        foreach ($this->_collection as $product) {

            $product->setStoreId($this->_storeId);
            $attributeValue = $this->_converter->convert($product, $template->getCode());

            foreach ($attributes as $attribute) {

                if(!$this->_testMode){

                    if(trim($attributeValue) == ''){
                        continue;
                    }

                    $select = $connection->select()->from($tablePrefix . 'catalog_product_entity_' . $attribute['backend_type'])->
                    where("entity_type_id = $productTypeId AND attribute_id = '$attribute[attribute_id]' AND entity_id = {$product->getId()} AND store_id = {$this->_storeId}");

                    $row    = $connection->fetchRow($select);
                    if ($row) {

                        $connection->update(
                            $tablePrefix . 'catalog_product_entity_' . $attribute['backend_type'],
                            array('value' => $attributeValue),
                            "entity_type_id = $productTypeId AND attribute_id = '$attribute[attribute_id]' AND entity_id = {$product->getId()} AND store_id = {$this->_storeId}"
                        );
//
                        $data = array(
                            'entity_type_id' => $productTypeId,
                            'attribute_id'   => $attribute['attribute_id'],
                            'entity_id'      => $product->getId(),
                            'store_id'       => $this->_storeId,
                            'value'          => $attributeValue
                        );
//                        Mage::log('UPDATE', null, 'seo.log');
//                        Mage::log($data, null, 'seo.log');
                    }
                    else {

                        $data = array(
                            'entity_type_id' => $productTypeId,
                            'attribute_id'   => $attribute['attribute_id'],
                            'entity_id'      => $product->getId(),
                            'store_id'       => $this->_storeId,
                            'value'          => $attributeValue
                        );

                        $connection->insert($tablePrefix . 'catalog_product_entity_' . $attribute['backend_type'], $data);

//                        Mage::log('INSERT', null, 'seo.log');
//                        Mage::log($data, null, 'seo.log');
                    }
                }
                elseif($this->_testMode == 'csv'){

                    if(trim($attributeValue) == ''){
                        $valueForCsv = $product->getData($attribute['attribute_code']);
                    }else{
                        $valueForCsv = $attributeValue;
                    }

                    $report = array(
                        'attribute_id'   => $attribute['attribute_id'],
                        'attribute_code' => $attribute['attribute_code'],
                        'entity_id'      => $product->getId(),
                        'entity_name'    => $product->getName(),
                        'store_id'       => $this->_storeId,
                        'store_name'     => Mage::helper('mageworx_seoxtemplates/store')->getStoreById($this->_storeId)->getName(),
                        'old_value'      => $product->getData($attribute['attribute_code']),
                        'value'          => $valueForCsv
                    );
                    $io->streamWriteCsv($report);
                }
            }
        }
    }

    /**
     * Retrive header for report
     * @return array
     */
    protected function _getHeaderData()
    {
        return array(
            Mage::helper('mageworx_seoxtemplates')->__('Attribute ID'),
            Mage::helper('mageworx_seoxtemplates')->__('Attribute Code'),
            Mage::helper('mageworx_seoxtemplates')->__('Product ID'),
            Mage::helper('mageworx_seoxtemplates')->__('Product Name'),
            Mage::helper('mageworx_seoxtemplates')->__('Store ID'),
            Mage::helper('mageworx_seoxtemplates')->__('Store Name'),
            Mage::helper('mageworx_seoxtemplates')->__('Current Value'),
            Mage::helper('mageworx_seoxtemplates')->__('New Value')
        );
    }

}