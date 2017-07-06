<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Model_System_Config_Source_Payment
{
    public function toOptionArray()
    {
        /**
         * From http://wiki.goodrelations-vocabulary.org/
         */

        return array(
            array(
                'label' => 'ByBankTransferInAdvance',
                'value' => 'http://purl.org/goodrelations/v1#ByBankTransferInAdvance'
            ),
            array(
                'label' => 'ByInvoice',
                'value' => 'http://purl.org/goodrelations/v1#ByInvoice'
            ),
            array(
                'label' => 'Cash',
                'value' => 'http://purl.org/goodrelations/v1#Cash'
            ),
            array(
                'label' => 'CheckInAdvance',
                'value' => 'http://purl.org/goodrelations/v1#CheckInAdvance'
            ),
            array(
                'label' => 'COD',
                'value' => 'http://purl.org/goodrelations/v1#COD'
            ),
            array(
                'label' => 'DirectDebit',
                'value' => 'http://purl.org/goodrelations/v1#DirectDebit'
            ),
            array(
                'label' => 'PayPal',
                'value' => 'http://purl.org/goodrelations/v1#PayPal'
            ),
            array(
                'label' => 'PaySwarm',
                'value' => 'http://purl.org/goodrelations/v1#PaySwarm'
            ),
            array(
                'label' => 'AmericanExpress',
                'value' => 'http://purl.org/goodrelations/v1#AmericanExpress'
            ),
            array(
                'label' => 'DinersClub',
                'value' => 'http://purl.org/goodrelations/v1#DinersClub'
            ),
            array(
                'label' => 'Discover',
                'value' => 'http://purl.org/goodrelations/v1#Discover'
            ),
            array(
                'label' => 'MasterCard',
                'value' => 'http://purl.org/goodrelations/v1#MasterCard'
            ),
            array(
                'label' => 'VISA',
                'value' => 'http://purl.org/goodrelations/v1#VISA'
            ),
            array(
                'label' => 'JCB',
                'value' => 'http://purl.org/goodrelations/v1#JCB'
            ),
            array(
                'label' => 'GoogleCheckout',
                'value' => 'http://purl.org/goodrelations/v1#GoogleCheckout'
            )
        );            
    }
}
