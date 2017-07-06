<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_product', 'exclude_from_sitemap',
    array(
    'group'            => 'Meta Information',
    'type'             => 'int',
    'backend'          => '',
    'frontend'         => '',
    'label'            => 'Exclude from XML and HTML Sitemaps',
    'input'            => 'select',
    'class'            => '',
    'source'           => 'eav/entity_attribute_source_boolean',
    'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'          => true,
    'required'         => false,
    'user_defined'     => false,
    'default'          => '0',
    'searchable'       => false,
    'filterable'       => false,
    'comparable'       => false,
    'visible_on_front' => false,
    'unique'           => false,
    'sort_order'       => 70
));

$installer->addAttribute('catalog_category', 'exclude_from_sitemap',
    array(
    'group'    => 'General Information',
    'type'     => 'int',
    'label'    => 'Exclude from XML and HTML Sitemaps',
    'input'    => 'select',
    'source'   => 'eav/entity_attribute_source_boolean',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'required' => false,
    'default'  => 0
));

$installer->endSetup();