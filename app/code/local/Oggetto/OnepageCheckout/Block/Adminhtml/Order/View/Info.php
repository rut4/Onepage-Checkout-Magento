<?php
/**
 * Oggetto Web checkout extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Oggetto OnepageCheckout module to newer versions in the future.
 * If you wish to customize the Oggetto OnepageCheckout module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Oggetto
 * @package    Oggetto_OnepageCheckout
 * @copyright  Copyright (C) 2014 Oggetto Web ltd (http://oggettoweb.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order view info block
 *
 * @category   Oggetto
 * @package    Oggetto_OnepageCheckout
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_OnepageCheckout_Block_Adminhtml_Order_View_Info extends Mage_Adminhtml_Block_Sales_Order_View_Info
{
    /**
     * Get additional info block content
     *
     * @return string Additional info
     */
    public function getAdditionalInfo()
    {
        $order = $this->getOrder();
        $customFields = Mage::getModel('onepageCheckout/customField')
            ->getCollection()
            ->addFieldToFilter('order_id', $order->getId());

        return $this->_formatCollectionToHtml($customFields);
    }

    /**
     * Get delivery date and time info block content
     *
     * @return string Delivery info
     */
    public function getDeliveryInfo()
    {
        /** @var Oggetto_OnepageCheckout_Model_Delivery $delivery */
        $delivery = Mage::getModel('onepageCheckout/delivery');
        $delivery->loadByOrderId($this->getOrder()->getId());
        $info = [];
        if ($date = $delivery->getDate()) {
            $info['delivery_date']['label'] = 'Date';
            $info['delivery_date']['value'] = $date;
        }
        if ($time = $delivery->getTime()) {
            $info['delivery_time']['label'] = 'Time';
            $info['delivery_time']['value'] = $time;
        }

        return $this->_formatArrayToHtml($info);
    }

    /**
     * Format array data to html code
     *
     * @param array $array Data
     * @return string Html code
     */
    protected function _formatArrayToHtml(array $array)
    {
        $html = '';
        foreach ($array as $item) {
            $html .= $item['label'] . ': ' . $item['value'] . '<br>';
        }
        return $html;
    }

    /**
     * Format resource collection to html code
     *
     * @param Mage_Core_Model_Resource_Db_Collection_Abstract $collection Resource collection
     * @return string Html code
     */
    protected function _formatCollectionToHtml(Mage_Core_Model_Resource_Db_Collection_Abstract $collection)
    {
        $html = '';
        foreach ($collection as $item) {
            $html .= $item->getLabel() . ': ' . $item->getValue() . '<br>';
        }
        return $html;
    }
}
