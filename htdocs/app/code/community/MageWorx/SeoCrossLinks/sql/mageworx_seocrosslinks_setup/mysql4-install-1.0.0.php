<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

$installer = $this;
$installer->startSetup();

if (!$installer->getConnection()->isTableExists($installer->getTable('mageworx_seocrosslinks/crosslink'))) {

    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_seocrosslinks/crosslink'))

        ->addColumn('crosslink_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'CrossLink ID')

        ->addColumn('keyword', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable'  => false,
            ), 'Keyword')

        ->addColumn('link_title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable'  => false,
            ), 'Title Link')

        ->addColumn('link_target', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => 1,
            ), 'Target Link')

        ->addColumn('replacement_count', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => 1,
            ), 'Count of Replacements')

         ->addColumn('ref_static_url', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable'  => true,
            ), 'Reference by Custom URL')

        ->addColumn('ref_product_sku', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable'  => true,
            ), 'Reference by Product SKU')

        ->addColumn('ref_category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => true,
            ), 'Reference by Category ID')

        ->addColumn('in_product', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => 1,
            ), 'Use in Product Page')

        ->addColumn('in_category', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => 1,
            ), 'Use in Category Page')

        ->addColumn('in_cms_page', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => 1,
            ), 'Use in CMS Page')

        ->addColumn('in_blog', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => 1,
            ), 'Use in Blog')

        ->addColumn('store_ids', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable'  => false,
            ), 'Store IDs')

        ->addColumn('priority', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => 0,
            ), 'Priority')

        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => 1,
            ), 'Status');

    $installer->getConnection()->createTable($table);
}

if (!$installer->getConnection()->isTableExists($installer->getTable('mageworx_seocrosslinks/crosslink_store'))) {

    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_seocrosslinks/crosslink_store'))

        ->addColumn('crosslink_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Cross Link ID')

         ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Store ID')

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seocrosslinks/crosslink_store',
                'crosslink_id',
                'mageworx_seocrosslinks/crosslink',
                'crosslink_id'
            ),
            'crosslink_id', $installer->getTable('mageworx_seocrosslinks/crosslink'), 'crosslink_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seocrosslinks/crosslink_store',
                'store_id',
                'core/store',
                'store_id'
            ),
            'store_id', $installer->getTable('core/store'), 'store_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);

    $installer->getConnection()->createTable($table);
}

$installer->addAttribute('catalog_product', 'exclude_from_crosslinking',
    array(
    'group'            => 'Meta Information',
    'type'             => 'int',
    'backend'          => '',
    'frontend'         => '',
    'label'            => 'Exclude from Cross Linking',
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
    'sort_order'       => 68
));

$installer->addAttribute('catalog_category', 'exclude_from_crosslinking',
    array(
    'group'    => 'General Information',
    'type'     => 'int',
    'label'    => 'Exclude from Cross Linking',
    'input'    => 'select',
    'source'   => 'eav/entity_attribute_source_boolean',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'required' => false,
    'default'  => 0
));

$installer->endSetup();