<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_XSitemap_Model_Observer
{
    const XML_PATH_GENERATION_ENABLED = 'mageworx_seo/google_sitemap/enabled';
    const XML_PATH_CRON_EXPR          = 'crontab/jobs/generate_sitemaps/schedule/cron_expr';
//    const XML_PATH_ERROR_TEMPLATE     = 'mageworx_seo/google_sitemap/error_email_template';
    const XML_PATH_ERROR_IDENTITY     = 'mageworx_seo/google_sitemap/error_email_identity';
    const XML_PATH_ERROR_RECIPIENT    = 'mageworx_seo/google_sitemap/error_email';
    const PROCESS_ID                  = 'xsitemap';

    public $indexProcess;

    public function __construct()
    {
        $this->indexProcess = Mage::getModel('index/process'); //new Mage_Index_Model_Process();
        $this->indexProcess->setId(self::PROCESS_ID);
    }

    public function unlock()
    {
        $this->indexProcess->unlock();
    }

    public function scheduledGenerateSitemaps()
    {
        if (!Mage::getStoreConfigFlag(self::XML_PATH_GENERATION_ENABLED)) {
            return;
        }

        if ($this->indexProcess->isLocked()) {
            return;
        }
        // Set an exclusive lock.
        $this->indexProcess->lockAndBlock();
        register_shutdown_function(array($this, "unlock"));
        $errors = array();

        $steps = array(
            'category',
            'product',
            'tag',
            'cms',
            'additional_links',
            'blog',
            'fishpig_attribute_splash_pages',
            'fishpig_attribute_splash_pro_pages',
            'sitemap_finish'
        );

        $collection = Mage::getModel('xsitemap/sitemap')->getCollection();
        /* @var $collection Mage_Sitemap_Model_Mysql4_Sitemap_Collection */

        foreach ($collection as $sitemap) {
            /* @var $sitemap Mage_Sitemap_Model_Sitemap */
            try {
                foreach ($steps as $step) {
                    $sitemap->generateXml($step);
                    while ($sitemap->getCounter() < $sitemap->getTotalProduct()) {
                        $sitemap->generateXml($step);
                    }
                }
                unset($sitemap);
            }
            catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if ($errors && Mage::getStoreConfig(self::XML_PATH_ERROR_RECIPIENT)) {
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);

            $emailTemplate = Mage::getModel('core/email_template');
            /* @var $emailTemplate Mage_Core_Model_Email_Template */
            $emailTemplate->setDesignConfig(array('area' => 'backend'))
                ->sendTransactional(
                    'xsitemap_generate_error_email_template',
                    Mage::getStoreConfig(self::XML_PATH_ERROR_IDENTITY),
                            Mage::getStoreConfig(self::XML_PATH_ERROR_RECIPIENT), null,
                            array('warnings' => join("\n", $errors))
            );

            $translate->setTranslateInline(true);
        }
    }

}