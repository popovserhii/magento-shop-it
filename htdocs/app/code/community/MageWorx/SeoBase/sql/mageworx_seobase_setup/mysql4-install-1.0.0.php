<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

$installer = $this;

if (!$installer->getConnection()->tableColumnExists($installer->getTable('cms_page'), 'meta_title')) {
    $installer->getConnection()->addColumn($installer->getTable('cms_page'), 'meta_title', "varchar(255) NOT NULL DEFAULT ''");
}

if (!$installer->getConnection()->tableColumnExists($installer->getTable('cms_page'), 'meta_robots')) {
    $installer->getConnection()->addColumn($installer->getTable('cms_page'), 'meta_robots', 'varchar(255) NOT NULL');
}

if ($installer->getConnection()->showTableStatus($installer->getTable('enterprise_cms_page_revision'))) {
    $installer->getConnection()->addColumn($installer->getTable('enterprise_cms_page_revision'), 'meta_title', "varchar(255) NOT NULL DEFAULT ''");
}

if ($installer->getConnection()->showTableStatus($installer->getTable('enterprise_cms_page_revision'))) {
    $installer->getConnection()->addColumn($installer->getTable('enterprise_cms_page_revision'), 'meta_robots', "varchar(255) NOT NULL");
}

if (!$installer->getConnection()->tableColumnExists($installer->getTable('cms_page'), 'mageworx_hreflang_identifier')) {
    $installer->getConnection()->addColumn($installer->getTable('cms_page'), 'mageworx_hreflang_identifier', "varchar(255) NOT NULL DEFAULT ''");
}

if (!$installer->getConnection()->tableColumnExists($installer->getTable('catalog/eav_attribute'), 'layered_navigation_canonical')) {
    $installer->getConnection()->addColumn($installer->getTable('catalog/eav_attribute'), 'layered_navigation_canonical', 'TINYINT(1) UNSIGNED NOT NULL DEFAULT \'0\'');
}

$installer->addAttribute('catalog_product', 'canonical_url',
    array(
    'group'            => 'Meta Information',
    'type'             => 'text',
    'backend'          => '',
    'frontend'         => '',
    'label'            => 'Canonical URL',
    'input'            => 'select',
    'class'            => '',
    'source'           => '',
    'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'          => true,
    'required'         => false,
    'user_defined'     => false,
    'default'          => '',
    'searchable'       => false,
    'filterable'       => false,
    'comparable'       => false,
    'visible_on_front' => false,
    'unique'           => false,
    'sort_order'       => 60
));

$installer->addAttribute('catalog_product', 'canonical_cross_domain', array(
    'group'             => 'Meta Information',
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Cross Domain Canonical URL',
    'input'             => 'select',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '0',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'sort_order'        => 50
));

$installer->addAttribute('catalog_product', 'meta_robots', array(
    'group'            => 'Meta Information',
    'type'             => 'text',
    'backend'          => '',
    'frontend'         => '',
    'label'            => 'Meta Robots',
    'input'            => 'select',
    'class'            => '',
    'source'           => '',
    'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'          => true,
    'required'         => false,
    'user_defined'     => false,
    'default'          => '',
    'searchable'       => false,
    'filterable'       => false,
    'comparable'       => false,
    'visible_on_front' => false,
    'unique'           => false,
    'sort_order'        => 60
));

$installer->addAttribute('catalog_category', 'meta_robots', array(
    'group'             => 'General Information',
    'type'              => 'text',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Meta Robots',
    'input'             => 'select',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'sort_order'        => 60
));

$seobaseCollection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter(
    'path', array('like' => 'mageworx_seo/seobase/%')
);

if ($seobaseCollection->count() == 0) {
    $seosuiteCollection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter(
        'path', array('like' => 'mageworx_seo/seosuite/%')
    );

    if ($seosuiteCollection->count() > 0) {
        foreach ($seosuiteCollection as $coreConfig) {
            $coreConfig->setConfigId(null);
            $path = str_replace('mageworx_seo/seosuite/', 'mageworx_seo/seobase/', $coreConfig->getPath());
            $coreConfig->setPath($path)->save();
        }
    }
}

$installer->endSetup();