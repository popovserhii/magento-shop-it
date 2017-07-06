<?php
/**
 * MageWorx
 * MageWorx SeoTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoTemplates
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

$installer = $this;
$installer->startSetup();

if ($installer->getConnection()->showTableStatus($installer->getTable('seosuite_report_product'))) {
    $installer->getConnection()->addColumn($installer->getTable('seosuite_report_product'), 'url', "varchar(1024) NOT NULL");
    $installer->getConnection()->addColumn($installer->getTable('seosuite_report_product'), 'url_dupl', "smallint(5) unsigned NOT NULL");
}

$installer->endSetup();