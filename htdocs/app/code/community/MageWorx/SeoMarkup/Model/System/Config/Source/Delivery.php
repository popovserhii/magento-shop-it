<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoMarkup_Model_System_Config_Source_Delivery
{
    public function toOptionArray()
    {
        return array(
            array(
                'label' => 'DeliveryModeDirectDownload',
                'value' => 'http://purl.org/goodrelations/v1#DeliveryModeDirectDownload'
            ),
            array(
                'label' => 'DeliveryModeFreight',
                'value' => 'http://purl.org/goodrelations/v1#DeliveryModeFreight'
            ),
            array(
                'label' => 'DeliveryModeMail',
                'value' => 'http://purl.org/goodrelations/v1#DeliveryModeMail'
            ),
            array(
                'label' => 'DeliveryModeOwnFleet',
                'value' => 'http://purl.org/goodrelations/v1#DeliveryModeOwnFleet'
            ),
            array(
                'label' => 'DeliveryModePickUp',
                'value' => 'http://purl.org/goodrelations/v1#DeliveryModePickUp'
            ),
            array(
                'label' => 'DHL',
                'value' => 'http://purl.org/goodrelations/v1#DHL'
            ),
            array(
                'label' => 'FederalExpress',
                'value' => 'http://purl.org/goodrelations/v1#FederalExpress'
            ),
            array(
                'label' => 'UPS',
                'value' => 'http://purl.org/goodrelations/v1#UPS'
            )           
        );
    }
}
