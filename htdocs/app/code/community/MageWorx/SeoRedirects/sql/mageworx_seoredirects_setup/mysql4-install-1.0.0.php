<?php
/**
 * MageWorx
 * MageWorx_SeoRedirects Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoRedirects
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
$installer = $this;

$installer->addAttribute('catalog_category', 'redirect_priority', array(
    'group'             => 'General Information',
    'type'              => 'text',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Product Redirect Priority',
    'input'             => 'text',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'user_defined'      => false,
    'default'           => '0',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'sort_order'        => 90,
    'frontend_class'    => 'validate-percents',
    'note'              => Mage::helper('mageworx_seoredirects')->__('100 is the highest priority.'),
));

if (!$installer->getConnection()->isTableExists($installer->getTable('mageworx_seoredirects/redirect_product'))) {

    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_seoredirects/redirect_product'))
        ->addColumn('redirect_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Redirect ID')
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => true,
            ), 'Deleted Product ID'
        )
        ->addColumn('product_name', Varien_Db_Ddl_Table::TYPE_TEXT, 1024, array(), 'Deleted Product Name')
        ->addColumn('product_sku', Varien_Db_Ddl_Table::TYPE_TEXT, 1024, array(), 'Deleted Product SKU')
        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
            ), 'Store Id')
        ->addColumn('request_path', Varien_Db_Ddl_Table::TYPE_TEXT, 1024, array(), 'Request Path')
        ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => true,
            ), 'Request Category ID')
        ->addColumn('priority_category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => true,
            ), 'Targeted Category ID')
        ->addColumn('date_created', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable'  => false,
            'default'   => '0000-00-00 00:00:00',
            ), 'Date Created')
        ->addColumn('hits', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => 0,
            ), 'Store Id')
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => 1,
            ), 'Status');

    $installer->getConnection()->createTable($table);
}

$installer->endSetup();