<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

$installer = $this;

foreach (array('cms_page', 'enterprise_cms_page_revision') as $table) {
    if ($installer->getConnection()->showTableStatus($installer->getTable($table))) {
        $installer->getConnection()->addColumn($installer->getTable($table), 'exclude_from_crosslinking', array(
            'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'nullable' => false,
            'default' => 0,
            'comment' => 'MageWorx flag for using in CrossLinking'
        ));
    }
}

$installer->endSetup();