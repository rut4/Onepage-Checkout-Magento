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
 * Onepage block
 *
 * @category   Oggetto
 * @package    Oggetto_OnepageCheckout
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_OnepageCheckout_Block_Onepage extends Mage_Checkout_Block_Onepage_Abstract
{
    private $_columnCount;
    private $_title;

    /**
     * Get this module data helper
     *
     * @return Oggetto_OnepageCheckout_Helper_Data Data helper
     */
    protected function _helper()
    {
        return $this->helper('onepageCheckout');
    }

    /**
     * Get layout column count
     *
     * @return string Column count
     */
    public function getColumnCount()
    {
        if (empty($this->_columnCount)) {
            $this->_columnCount = $this->_helper()->getColumnCount();
        }
        return $this->_columnCount;
    }

    /**
     * Get layout module title
     *
     * @return string Title
     */
    public function getTitle()
    {
        if (empty($this->_title)) {
            $this->_title = Mage::helper('onepageCheckout')->getTitle();
        }
        return $this->_title;
    }

    /**
     * Is shipping same as billing
     *
     * @return bool
     */
    public function isShippingSameAsBilling()
    {
        $shipping = $this->_helper()->getQuote()->getShippingAddress();
        if ($shipping->getId()) {
            return (bool)$shipping->getSameAsBilling();
        } else {
            return false;
        }
    }

    /**
     * Is shipping same as billing checked by default
     *
     * @return bool
     */
    public function isShippingSameAsBillingCheckedDefault()
    {
        return $this->_helper()->isShippingSameAsBillingConfig();
    }

    /**
     * Should show coupon field
     *
     * @return bool
     */
    public function shouldShowCouponField()
    {
        return $this->_helper()->shouldShowCouponField();
    }

    /**
     * Get is module enabled
     *
     * @return bool
     */
    public function isModuleEnabled()
    {
        return $this->_helper()->isOnepageModuleEnabled();
    }

    /**
     * Should show shipping address form
     *
     * @return bool
     */
    public function shouldShowShippingAddressForm()
    {
        return $this->_helper()->shouldShowShippingAddressForm();
    }

    /**
     * Is quote virtual
     *
     * @return bool
     */
    public function isQuoteVirtual()
    {
        return $this->_helper()->isQuoteVirtual();
    }
}
