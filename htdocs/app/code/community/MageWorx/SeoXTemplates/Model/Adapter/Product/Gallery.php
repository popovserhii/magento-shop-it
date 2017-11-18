<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Model_Adapter_Product_Gallery extends MageWorx_SeoXTemplates_Model_Adapter_Product
{
    protected $_converterModelUri = 'mageworx_seoxtemplates/converter_product_gallery';

    protected function _apply($template)
    {
        $attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'media_gallery');
        $attributeId = $attribute->getAttributeId();

        $connection  = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();

        if($this->_testMode == 'csv' && $this->_collection->count()){
            $io = $this->_startCsvReport($template);
        }

        foreach ($this->_collection as $product) {

            $product->setStoreId($this->_storeId);
            $attributeValue = $this->_converter->convert($product, $template->getCode());

            $galleries = $this->_getGalleries($connection, $tablePrefix, $product, $attributeId);

            $copyFromDefaultStore = false;

            if (is_array($galleries) && count($galleries) == 0) {
                $galleries = $this->_getGalleries($connection, $tablePrefix, $product, $attributeId, true);
                $copyFromDefaultStore = true;
            }

            if (!(is_array($galleries) && count($galleries) != 0)) {
                continue;
            }

            foreach($galleries as $gallery){

                if($gallery['label'] != '') {
                    if(Mage::helper('mageworx_seoxtemplates/template_product')->isWriteForEmpty($template->getWriteFor())){
                        continue;
                    }
                }

                if(!$this->_testMode){

                    if($copyFromDefaultStore){
                        $data['value_id'] = $gallery['value_id'];
                        $data['store_id'] = $this->_storeId;
                        $data['label']    = $attributeValue;
                        $data['position'] = $gallery['position'];
                        $data['disabled'] = $gallery['disabled'];

                        $connection->insert($tablePrefix . 'catalog_product_entity_media_gallery_value', $data);
                    }else{

                        $connection->update(
                            $tablePrefix . 'catalog_product_entity_media_gallery_value',
                            array('label' => $attributeValue),
                            "value_id = {$gallery['value_id']} AND store_id = {$this->_storeId}"
                        );
                    }
                }
                elseif($this->_testMode == 'csv'){
                    $this->_addToReport($io, $product, $gallery, $attributeValue);
                }
            }
        }
    }

    protected function _getHeaderData()
    {
        return array(
            Mage::helper('mageworx_seoxtemplates')->__('Product ID'),
            Mage::helper('mageworx_seoxtemplates')->__('Product Name'),
            Mage::helper('mageworx_seoxtemplates')->__('Store ID'),
            Mage::helper('mageworx_seoxtemplates')->__('Store Name'),
            Mage::helper('mageworx_seoxtemplates')->__('File'),
            Mage::helper('mageworx_seoxtemplates')->__('Current Value'),
            Mage::helper('mageworx_seoxtemplates')->__('New Value')
        );
    }

    protected function _startCsvReport($template)
    {
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
        return $io;
    }

    protected function _getGalleries($connection, $tablePrefix, $product, $attributeId, $forDefaultStore = false)
    {
        $storeId = (!$forDefaultStore) ? $this->_storeId : 0;

        $select = $connection
              ->select()
              ->from(array('main_table' => $tablePrefix . 'catalog_product_entity_media_gallery'))
              ->joinLeft(
                  array('label_table' => $tablePrefix . 'catalog_product_entity_media_gallery_value'),
                  'main_table.value_id = label_table.value_id', array('*'))
              ->where("attribute_id = $attributeId AND entity_id = {$product->getId()} AND store_id = {$storeId}");
        return $connection->fetchAll($select);
    }

    protected function _addToReport($io, $product, $gallery, $attributeValue)
    {
        $report = array(
            'entity_id'      => $product->getId(),
            'entity_name'    => $product->getName(),
            'store_id'       => $this->_storeId,
            'store_name'     => Mage::helper('mageworx_seoxtemplates/store')->getStoreById($this->_storeId)->getName(),
            'file'           => $gallery['value'],
            'old_value'      => $gallery['label'],
            'value'          => $attributeValue
        );
        $io->streamWriteCsv($report);
    }
}
