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

if ($installer->getConnection()->showTableStatus($installer->getTable('enterprise_cms_page_revision'))) {
    $installer->getConnection()->addColumn($installer->getTable('enterprise_cms_page_revision'), 'mageworx_hreflang_identifier', "varchar(255) NOT NULL DEFAULT ''");
}

$installer->endSetup();