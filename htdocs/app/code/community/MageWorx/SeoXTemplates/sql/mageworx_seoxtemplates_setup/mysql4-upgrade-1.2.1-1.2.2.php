<?php
/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

$installer = $this;
$installer->startSetup();

$installer->getConnection()->changeColumn(
    $installer->getTable('mageworx_seoxtemplates/template_category'),
    'code',
    'code',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => '64k',
        'nullable'  => false,
        'comment'   => 'Template Code'
    )
);

$installer->addAttribute('catalog_category', 'category_seo_name',
    array(
    'group'            => 'General Information',
    'type'             => 'text',
    'backend'          => '',
    'frontend'         => '',
    'label'            => 'Category SEO Name',
    'input'            => 'text',
    'class'            => '',
    'source'           => '',
    'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'          => true,
    'required'         => false,
    'user_defined'     => false,
    'default'          => '',
    'searchable'       => true,
    'filterable'       => false,
    'comparable'       => false,
    'visible_on_front' => false,
    'unique'           => false,
    'sort_order'       => 60
));

$installer->endSetup();