<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoXTemplates_Model_Adapter_Product_Url extends MageWorx_SeoXTemplates_Model_Adapter_Product
{
    protected $_converterModelUri = 'mageworx_seoxtemplates/converter_product_url';
    protected $_attributeCodes    = array('url_path', 'url_key');
}