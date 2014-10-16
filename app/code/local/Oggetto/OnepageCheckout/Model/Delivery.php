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
 * Delivery model
 *
 * @category   Oggetto
 * @package    Oggetto_OnepageCheckout
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_OnepageCheckout_Model_Delivery extends Mage_Core_Model_Abstract
{
    /**
     * Initialization with resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('onepageCheckout/delivery');
    }

    /**
     * Get this module data helper
     *
     * @return Oggetto_OnepageCheckout_Helper_Data Data helper
     */
    protected function _helper()
    {
        return Mage::helper('onepageCheckout');
    }

    /**
     * Validate delivery data before save
     *
     * @return void
     */
    public function validate()
    {
        if ($this->_helper()->shouldUseCalendar() && !$this->getDate()) {
            Mage::throwException('Please, specify the delivery date.');
        }
        if ($this->_helper()->getUseTimeRange() && !$this->getTime()) {
            Mage::throwException('Please, specify the delivery time.');
        }
        $date   = date_parse($this->getDate());
        $now    = date_parse(date('Y-m-d'));
        if ($date['year'] < $now['year'] || $date['month'] < $now['month'] || $date['day'] < $now['day']) {
            Mage::throwException('Invalid date.');
        }
    }

    /**
     * Get time range config id
     *
     * @param int $id Range config id
     * @return string Formatted time range
     */
    public function getTimeRangeFromConfigId($id)
    {
        $ranges = $this->_helper()->getTimeRanges();
        $range = $ranges[$id];
        return $this->_helper()->formatTimeRange($range);
    }

    /**
     * Load delivery by order id
     *
     * @param int $id Order id
     * @return void
     */
    public function loadByOrderId($id)
    {
        $this->load($id, 'order_id');
    }

    /**
     * Check that choosing day isn't excluded
     *
     * @return void
     */
    public function checkExcludeDays()
    {
        $excludeDates = array_values($this->_helper()->getDeliveryExcludeDates());
        if ($date = $this->getDate()) {
            $date = date_parse($date);
            foreach ($excludeDates as $excludeDate) {
                if ($date['year'] == $excludeDate['date']['year'] || $excludeDate['date']['year'] == 0) {
                    if ($date['month'] == $excludeDate['date']['month']
                        && $date['day'] == $excludeDate['date']['day']) {
                        Mage::throwException('Sorry, but this date is exclude for delivery.');
                    }
                }
            }
        }
    }
}
