<?php
/**
 * MageWorx
 * MageWorx SeoTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();

///// Template Blog
if(!$installer->getConnection()->isTableExists($installer->getTable('mageworx_seoxtemplates/template_blog'))){

    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_seoxtemplates/template_blog'))
        ->addColumn('template_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Template ID')

        ->addColumn('type_id', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Template Type')

        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable'  => false,
            ), 'Template Name')

        ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, '64K', array(
            'nullable'  => false,
            ), 'Template Code')

        ->addColumn('assign_type', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Assign Type')

        ->addColumn('priority', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Priority')

        ->addColumn('date_modified', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable'  => true,
            ), 'Last Modify Date')

        ->addColumn('date_apply_start', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable'  => true,
            ), 'Last Apply Start Date')

        ->addColumn('date_apply_finish', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable'  => true,
            ), 'Last Apply Finish Date')

        ->addColumn('write_for', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'  => 1,
            ), 'Write for')

        ->addColumn('use_cron', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'  => 2,
            ), 'Use Cron')

        ->setComment('Template Blog Post Table created by MageWorx SeoXtemplates extension');

    $installer->getConnection()->createTable($table);
}

if(!$installer->getConnection()->isTableExists($installer->getTable('mageworx_seoxtemplates/template_relation_blog'))){

    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_seoxtemplates/template_relation_blog'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'ID')

        ->addColumn('template_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Template ID')

        ->addColumn('blog_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Blog ID')

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seoxtemplates/template_relation_blog',
                'template_id',
                'mageworx_seoxtemplates/template_blog',
                'template_id'
            ),
            'template_id', $installer->getTable('mageworx_seoxtemplates/template_blog'), 'template_id',
             Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)

        ->setComment('Relation Template Blog Post Table created by MageWorx SeoXtemplates extension');

     $installer->getConnection()->createTable($table);
}

$installer->getConnection()->changeColumn(
    $installer->getTable('mageworx_seoxtemplates/template_product'),
    'code',
    'code',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => '64k',
        'nullable'  => false,
        'comment'   => 'Template Code'
    )
);

$installer->getConnection()->changeColumn(
    $installer->getTable('mageworx_seoxtemplates/template_product'),
    'code',
    'code',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => '64k',
        'nullable'  => false,
        'comment'   => 'Template Code'
    )
);

$installer->endSetup();