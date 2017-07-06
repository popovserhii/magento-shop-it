<?php
/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

$installer = $this;
$installer->startSetup();

$installer->getConnection()->changeColumn(
    $installer->getTable('mageworx_seoxtemplates/template_relation_product'),
    'id',
    'id',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true
    )
);

$installer->getConnection()->changeColumn(
    $installer->getTable('mageworx_seoxtemplates/template_relation_category'),
    'id',
    'id',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true
    )
);

$installer->getConnection()->changeColumn(
    $installer->getTable('mageworx_seoxtemplates/template_relation_blog'),
    'id',
    'id',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true
    )
);