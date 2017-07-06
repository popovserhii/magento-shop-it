<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoBase_Block_Rss_Catalog_Special extends Mage_Rss_Block_Catalog_Special
{
    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('rss_catalog_special_'.$this->getStoreId().'_'.$this->_getCustomerGroupId());
        $this->setCacheLifetime(600);
    }

    protected function _toHtml()
    {
         //store id is store view id
        $storeId = $this->_getStoreId();
        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();

        //customer group id
        $custGroup =   $this->_getCustomerGroupId();

        $product = Mage::getModel('catalog/product');
        $todayDate = $product->getResource()->formatDate(time());

        $rulePriceWhere = "({{table}}.rule_date is null) or ({{table}}.rule_date='$todayDate' and {{table}}.website_id='$websiteId' and {{table}}.customer_group_id='$custGroup')";

        $specials = $product->setStoreId($storeId)->getResourceCollection()
            ->addAttributeToFilter('special_price', array('gt'=>0), 'left')
            ->addAttributeToFilter('special_from_date', array('date'=>true, 'to'=> $todayDate), 'left')
            ->addAttributeToFilter(array(
                array('attribute'=>'special_to_date', 'date'=>true, 'from'=>$todayDate),
                array('attribute'=>'special_to_date', 'is' => new Zend_Db_Expr('null'))
            ), '', 'left')
            ->addAttributeToSort('special_from_date', 'desc')
            ->addAttributeToSelect(array('name', 'short_description', 'description', 'price', 'thumbnail', 'special_to_date'), 'inner')
            ->joinTable('catalogrule/rule_product_price', 'product_id=entity_id', array('rule_price'=>'rule_price', 'rule_start_date'=>'latest_start_date'), $rulePriceWhere, 'left')
        ;

//public function join($table, $cond, $cols='*')
        $rulePriceCollection = Mage::getResourceModel('catalogrule/rule_product_price_collection')
            ->addFieldToFilter('website_id', $websiteId)
            ->addFieldToFilter('customer_group_id', $custGroup)
            ->addFieldToFilter('rule_date', $todayDate)
        ;
        $productIds = $rulePriceCollection->getProductIds();

        if (!empty($productIds)) {
            $specials->getSelect()->orWhere('e.entity_id in ('.implode(',',$productIds).')');
        }

        /*
        * need to put status and visibility after orWhere clause for catalog price rule products
        */
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($specials);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($specials);


        $newurl = Mage::helper('core/url')->getCurrentUrl();
        $title = Mage::helper('rss')->__('%s - Special Discounts', sprintf('%s (%s)', Mage::app()->getStore()->getGroup()->getName(), Mage::app()->getStore($storeId)->getName()));
        $lang = Mage::getStoreConfig('general/locale/code');

        $rssObj = Mage::getModel('rss/rss');
        $data = array('title' => $title,
                'description' => $title,
                'link'        => $newurl,
                'charset'     => 'UTF-8',
                'generator'   => Mage::helper('mageworx_seobase')->getRssGenerator(),
                'language'    => $lang
                );
        $rssObj->_addHeader($data);

        $results = array();
        /*
        using resource iterator to load the data one by one
        instead of loading all at the same time. loading all data at the same time can cause the big memory allocation.
        */
        Mage::getSingleton('core/resource_iterator')
            ->walk($specials->getSelect(), array(array($this, 'addSpecialXmlCallback')), array('rssObj'=> $rssObj, 'results'=> &$results));

        if (sizeof($results)>0) {
           usort($results, array(&$this, 'sortByStartDate'));
           foreach ($results as $result) {
               $product->setData($result);
               //$product->unsetData()->load($result['entity_id']);

               $special_price = ($result['use_special'] ? $result['special_price'] : $result['rule_price']);
               $description = '<table><tr>'.
                '<td><a href="'.$product->getProductUrl().'"><img src="'. $this->helper('catalog/image')->init($product, 'thumbnail')->resize(75, 75) .'" border="0" align="left" height="75" width="75"></a></td>'.
                '<td  style="text-decoration:none;">'.$product->getDescription().
                '<p> Price:'.Mage::helper('core')->currency($product->getPrice()).
                ' Special Price:'. Mage::helper('core')->currency($special_price).
                ($result['use_special'] && $result['special_to_date'] ? '<br/> Special Expires on: '.$this->formatDate($result['special_to_date'], Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM) : '').
                '</p>'.
                '</td>'.
                '</tr></table>';
                $data = array(
                        'title'         => $product->getName(),
                        'link'          => $product->getProductUrl(),
                        'description'   => $description,

                        );
                $rssObj->_addEntry($data);
           }
        }
        return $rssObj->createRssXml();
    }

    public function addSpecialXmlCallback($args)
    {
       $row = $args['row'];
       $special_price = $row['special_price'];
       $rule_price = $row['rule_price'];
       if (!$rule_price || ($rule_price && $special_price && $special_price<=$rule_price)) {
           $row['start_date'] = $row['special_from_date'];
           $row['use_special'] = true;
       } else {
           $row['start_date'] = $row['rule_start_date'];
           $row['use_special'] = false;
       }
       $args['results'][] = $row;
    }


    /**
     * Function for comparing two items in collection
     *
     * @param   Varien_Object $item1
     * @param   Varien_Object $item2
     * @return  boolean
     */
    public function sortByStartDate($a, $b)
    {
        return $a['start_date']>$b['start_date'] ? -1 : ($a['start_date']<$b['start_date'] ? 1 : 0);
    }
}