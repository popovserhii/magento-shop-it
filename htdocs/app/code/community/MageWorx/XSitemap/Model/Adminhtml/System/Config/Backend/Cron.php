<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_XSitemap_Model_Adminhtml_System_Config_Backend_Cron extends Mage_Core_Model_Config_Data
{
    const CRON_STRING_PATH = 'crontab/jobs/xsitemap_generate/schedule/cron_expr';
    const CRON_MODEL_PATH  = 'crontab/jobs/xsitemap_generate/run/model';

    protected function _afterSave()
    {
        $enabled    = $this->getData('groups/google_sitemap/fields/enabled/value');
        $time       = $this->getData('groups/google_sitemap/fields/time/value');
        $frequncy   = $this->getData('groups/google_sitemap/fields/frequency/value');
        $errorEmail = $this->getData('groups/google_sitemap/fields/error_email/value');

        $frequencyDaily   = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_DAILY;
        $frequencyWeekly  = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_WEEKLY;
        $frequencyMonthly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_MONTHLY;

        $cronDayOfWeek = date('N');

        $cronExprArray = array(
            intval($time[1]), # Minute
            intval($time[0]), # Hour
            ($frequncy == $frequencyMonthly) ? '1' : '*', # Day of the Month
            '*', # Month of the Year
            ($frequncy == $frequencyWeekly) ? '1' : '*', # Day of the Week
        );

        $cronExprString = join(' ', $cronExprArray);

        try {
            Mage::getModel('core/config_data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
            Mage::getModel('core/config_data')
                ->load(self::CRON_MODEL_PATH, 'path')
                ->setValue((string) Mage::getConfig()->getNode(self::CRON_MODEL_PATH))
                ->setPath(self::CRON_MODEL_PATH)
                ->save();
        }
        catch (Exception $e) {
            throw new Exception(Mage::helper('cron')->__('Unable to save Cron expression'));
        }
    }

}
