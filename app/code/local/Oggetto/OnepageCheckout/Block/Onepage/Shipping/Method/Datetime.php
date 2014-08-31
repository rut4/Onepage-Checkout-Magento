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
 * Delivery date and time block
 *
 * @category   Oggetto
 * @package    Oggetto_OnepageCheckout
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_OnepageCheckout_Block_Onepage_Shipping_Method_Datetime extends Mage_Core_Block_Template
{
    /**
     * Get this module helper
     *
     * @return Oggetto_OnepageCheckout_Helper_Data Data helper
     */
    protected function _helper()
    {
        return $this->helper('onepageCheckout');
    }

    /**
     * Should use time range
     *
     * @return bool Use time range
     */
    public function shouldUseTimeRange()
    {
        return $this->_helper()->getUseTimeRange();
    }

    /**
     * Should use calendar
     *
     * @return bool Use calendar
     */
    public function shouldUseCalendar()
    {
        return $this->_helper()->shouldUseCalendar();
    }

    /**
     * Get time ranges
     *
     * @return array Time ranges
     */
    public function getTimeRanges()
    {
        return $this->_helper()->getTimeRanges();
    }

    /**
     * Format time range array to string
     *
     * @param array $timeRange Time range as array
     * @return string Time range as string
     */
    public function formatTimeRange(array $timeRange)
    {
        return $this->_helper()->formatTimeRange($timeRange);
    }
}
