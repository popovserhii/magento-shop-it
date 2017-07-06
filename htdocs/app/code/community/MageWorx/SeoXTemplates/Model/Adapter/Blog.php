<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
abstract class MageWorx_SeoXTemplates_Model_Adapter_Blog extends MageWorx_SeoXTemplates_Model_Adapter
{
    protected function _apply($template)
    {
        $properties  = $this->_attributeCodes;
        $connection  = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();

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

        foreach ($this->_collection as $post) {

            $post->setStoreId($this->_storeId);
            $propertyValue = $this->_converter->convert($post, $template->getCode());

            foreach ($properties as $propertyName) {

                if(!$this->_testMode){
                    if(trim($propertyValue) == ''){
                        continue;
                    }
                    $connection->update(
                        $tablePrefix . 'aw_blog',
                        array($propertyName => $propertyValue),
                        "post_id = '{$post->getPostId()}'"
                    );
                }
                elseif($this->_testMode == 'csv'){

                    if(trim($propertyValue) == ''){
                        $valueForCsv = $post->getData($propertyName);
                    }else{
                        $valueForCsv = $propertyValue;
                    }

                    $report = array(
                        'entity_id'      => $post->getId(),
                        'entity_name'    => $post->getTitle(),
                        'property'       => $propertyName,
                        'old_value'      => $post->getData($propertyName),
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
            Mage::helper('mageworx_seoxtemplates')->__('Post ID'),
            Mage::helper('mageworx_seoxtemplates')->__('Post Title'),
            Mage::helper('mageworx_seoxtemplates')->__('Property'),
            Mage::helper('mageworx_seoxtemplates')->__('Current Value'),
            Mage::helper('mageworx_seoxtemplates')->__('New Value')
        );
    }

}
