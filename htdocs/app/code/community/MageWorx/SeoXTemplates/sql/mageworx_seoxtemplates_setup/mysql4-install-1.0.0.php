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

$installer->addAttribute('catalog_product', 'product_seo_name',
    array(
    'type'             => 'text',
    'backend'          => '',
    'frontend'         => '',
    'label'            => 'Product SEO Name',
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

///// Template Product

if(!$installer->getConnection()->isTableExists($installer->getTable('mageworx_seoxtemplates/template_product'))){

    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_seoxtemplates/template_product'))
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

        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Store ID')

        ->addColumn('code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
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

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seoxtemplates/template_product',
                'store_id',
                'core/store',
                'store_id'
            ),
            'store_id', $installer->getTable('core/store'), 'store_id',
             Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('Template Product Table created by MageWorx SeoXtemplates extension');

    $installer->getConnection()->createTable($table);
}

if(!$installer->getConnection()->isTableExists($installer->getTable('mageworx_seoxtemplates/template_relation_product'))){

    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_seoxtemplates/template_relation_product'))
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

        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Product ID')

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seoxtemplates/template_relation_product',
                'template_id',
                'mageworx_seoxtemplates/template_product',
                'template_id'
            ),
            'template_id', $installer->getTable('mageworx_seoxtemplates/template_product'), 'template_id',
             Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seoxtemplates/template_relation_product',
                'product_id',
                'catalog/product',
                'entity_id'
            ),
            'product_id', $installer->getTable('catalog/product'), 'entity_id',
             Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('Relation Template Product Table created by MageWorx SeoXtemplates extension');

     $installer->getConnection()->createTable($table);
}

if(!$installer->getConnection()->isTableExists($installer->getTable('mageworx_seoxtemplates/template_relation_attributeset'))){

    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_seoxtemplates/template_relation_attributeset'))
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

        ->addColumn('attributeset_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Attribute Set ID')

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seoxtemplates/template_relation_attributeset',
                'template_id',
                'mageworx_seoxtemplates/template_product',
                'template_id'
            ),
            'template_id', $installer->getTable('mageworx_seoxtemplates/template_product'), 'template_id',
             Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seoxtemplates/template_relation_attributeset',
                'attributeset_id',
                'eav/attribute_set',
                'attribute_set_id'
            ),
            'attributeset_id', $installer->getTable('eav/attribute_set'), 'attribute_set_id',
             Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('Relation Template Attribute Set Table created by MageWorx SeoXtemplates extension');

     $installer->getConnection()->createTable($table);
}



///// Template Category

if(!$installer->getConnection()->isTableExists($installer->getTable('mageworx_seoxtemplates/template_category'))){

    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_seoxtemplates/template_category'))
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

        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Store ID')

        ->addColumn('code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
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

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seoxtemplates/template_category',
                'store_id',
                'core/store',
                'store_id'
            ),
            'store_id', $installer->getTable('core/store'), 'store_id',
             Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('Template Category Table created by MageWorx SeoXtemplates extension');

    $installer->getConnection()->createTable($table);
}

if(!$installer->getConnection()->isTableExists($installer->getTable('mageworx_seoxtemplates/template_relation_category'))){

    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_seoxtemplates/template_relation_category'))
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

        ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            ), 'Category ID')

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seoxtemplates/template_relation_category',
                'template_id',
                'mageworx_seoxtemplates/template_category',
                'template_id'
            ),
            'template_id', $installer->getTable('mageworx_seoxtemplates/template_category'), 'template_id',
             Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)

        ->addForeignKey(
            $installer->getFkName(
                'mageworx_seoxtemplates/template_relation_category',
                'category_id',
                'catalog/category',
                'entity_id'
            ),
            'category_id', $installer->getTable('catalog/category'), 'entity_id',
             Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('Relation Template Category Table created by MageWorx SeoXtemplates extension');

     $installer->getConnection()->createTable($table);
}


$connection = $installer->getConnection('core_read');

$oldTemplateTableName      = $this->getTable('seosuite_template');
$oldTemplateStoreTableName = $this->getTable('seosuite_template_store');

$isExistsOldTemplateTable = (boolean) $connection->showTableStatus($oldTemplateTableName);
$isExistsOldTemplateStoreTable = (boolean) $connection->showTableStatus($oldTemplateStoreTableName);

if($isExistsOldTemplateTable && $isExistsOldTemplateStoreTable){

    $selectProduct = $connection->select()
    ->from(array('main_table' => Mage::getSingleton('core/resource')->getTableName('mageworx_seoxtemplates_template_product')), array('count' => new Zend_Db_Expr('COUNT(main_table.template_id)')));
    $rowProduct = $connection->fetchRow($selectProduct);

    $isProductEmpty = (is_array($rowProduct) && $rowProduct['count'] == 0) ? true : false;

    $selectCategory = $connection->select()
        ->from(array('main_table' => Mage::getSingleton('core/resource')->getTableName('mageworx_seoxtemplates_template_category')), array('count' => new Zend_Db_Expr('COUNT(main_table.template_id)')));
    $rowCategory = $connection->fetchRow($selectCategory);
    $isCategoryEmpty = (is_array($rowCategory) && $rowCategory['count'] == 0) ? true : false;

    if($isProductEmpty || $isCategoryEmpty){

        $select = $connection->select()
            ->from(array('t' => Mage::getSingleton('core/resource')->getTableName('seosuite_template')), array('template_id', 'template_code', 'template_name'))
            ->join(
                array('ts' => Mage::getSingleton('core/resource')->getTableName('seosuite_template_store')),
                'ts.template_id = t.template_id',
                array('template_key', 'store_id')
            )
            ->order(array('template_id', 'store_id'));

        $rowsArray = $connection->fetchAll($select);
        $uniqArray = array();

        foreach($rowsArray as $row){
            $uniqString = $row['template_code'] . '-' . $row['store_id'];
            if(array_search($uniqString, $uniqArray) !== false){
                continue;
            }
            if(!trim($row['template_key'])){
                continue;
            }
            $uniqArray[] = $uniqString;
            $codeParts = explode('_', $row['template_code']);
            $itemRole = $codeParts[0];

            if(!in_array($itemRole, array('product', 'category'))){
                continue;
            }

            if(!$isProductEmpty && $itemRole == 'product'){
                continue;
            }

            if(!$isCategoryEmpty && $itemRole == 'category'){
                continue;
            }

            $itemTypeId = Mage::helper("mageworx_seoxtemplates/template_$itemRole")->getTypeIdByTypeCode($row['template_code']);

            if(!$itemTypeId){
                continue;
            }

            $template = Mage::getModel("mageworx_seoxtemplates/template_$itemRole");
            $store = Mage::helper('mageworx_seoxtemplates/store')->getStoreById($row['store_id']);

            if($store->getId() !== null){
                $storeName = $store->getName();
                if($storeName == 'Admin'){
                    $storeName = 'All Stores';
                }

                $name = $row['template_name'] . ' for ' .  $storeName;

                $assignType = Mage::helper("mageworx_seoxtemplates/template_$itemRole")->getAssignForAllItems();
                $template->setName($name);
                $template->setAssignType($assignType);
                $template->setStoreId($row['store_id']);
                $template->setTypeId($itemTypeId);
                $template->setCode($row['template_key']);
                $template->setWriteFor(Mage::helper("mageworx_seoxtemplates/template_$itemRole")->getWriteForEmpty());
                $template->save();
            }
        }
    }
}

$installer->endSetup();