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
 * Shipping block
 *
 * @category   Oggetto
 * @package    Oggetto_OnepageCheckout
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_OnepageCheckout_Block_Onepage_Shipping extends Mage_Checkout_Block_Onepage_Shipping
{
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
     * Get country select
     *
     * @param string $type shipping or billing
     * @return string Country select html code
     */
    public function getCountryHtmlSelect($type)
    {
        $countryId = $this->getAddress()->getCountryId();
        if (is_null($countryId)) {
            $countryId = $this->_helper()->getDefaultCountryId();
        }
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName($type.'[country_id]')
            ->setId($type.':country_id')
            ->setTitle(Mage::helper('onepageCheckout')->__('Country'))
            ->setClass('validate-select input input_select')
            ->setValue($countryId)
            ->setOptions($this->getCountryOptions());
        return $select->getHtml();
    }

    /**
     * Get shipping address
     *
     * @return Mage_Sales_Model_Quote_Address Shipping address
     */
    public function getAddress()
    {
        if ($this->getQuote()) {
            return $this->getQuote()->getShippingAddress();
        } else {
            return parent::getAddress();
        }
    }

    /**
     * Get address field config
     *
     * @param string $field Address field
     * @return bool|string False if disabled, Optional or Required if enabled
     */
    public function getAddressFieldConfig($field)
    {
        /** @var Oggetto_OnepageCheckout_Helper_Data $helper */
        $helper = $this->helper('onepageCheckout');

        return $helper->getAddressFieldConfig($field);
    }

    /**
     * Get current shipping method
     *
     * @return string Shipping method code
     */
    public function getShippingMethod()
    {
        if ($this->getAddress()) {
            return $this->getAddress()->getShippingMethod();
        } else {
            return parent::getMethod();
        }
    }

    /**
     * Get current country id
     *
     * @return string Country id
     */
    public function getCountryId()
    {
        if ($country = $this->getAddress()->getCountryId()) {
            return $country;
        }
        if ($country = $this->_helper()->getDefaultCountryId()) {
            return $country;
        }
        return 'US';
    }

    /**
     * Are shipping methods available
     *
     * @return bool Are shipping methods available
     */
    public function areShippingMethodsAvailable()
    {
        /** @var Oggetto_OnepageCheckout_Model_Onepage $onepage */
        $onepage = Mage::getModel('onepageCheckout/onepage');
        $validationResult = $onepage->saveShipping($this->getAddress()->getData(), null);
        return !(bool)count($validationResult);
    }

    /**
     * Should reload shipping methods on country change
     *
     * @return bool
     */
    public function shouldReloadShippingMethodsOnCountryChange()
    {
        return $this->_helper()->shouldReloadShippingMethodsOnCountryChange();
    }

    /**
     * Should reload shipping methods on postcode change
     *
     * @return bool
     */
    public function shouldReloadShippingMethodsOnPostalCodeChange()
    {
        return $this->_helper()->shouldReloadShippingMethodsOnPostalCodeChange();
    }

    /**
     * Should reload shipping methods on region change
     *
     * @return bool
     */
    public function shouldReloadShippingMethodsOnRegionChange()
    {
        return $this->_helper()->shouldReloadShippingMethodsOnRegionChange();
    }

    /**
     * Is choosing delivery date and time enabled
     *
     * @return bool
     */
    public function isDeliveryDateTimeEnabled()
    {
        return $this->_helper()->isDeliveryDateTimeEnabled();
    }

    /**
     * Is choosing delivery date and time available for current shipping method
     *
     * @return bool
     */
    public function isAllowedForCurrentMethod()
    {
        return $this->_helper()->isSetupDeliveryAllowedForCurrentMethod();
    }

    /**
     * Should reload shipping methods on shipping country change config
     *
     * @return bool
     */
    public function shouldReloadOnCountryChange()
    {
        return $this->_helper()->shouldReloadShippingMethodsOnCountryChange();
    }

    /**
     * Should reload shipping methods on shipping postcode change config
     *
     * @return bool
     */
    public function shouldReloadOnPostcodeChange()
    {
        return $this->_helper()->shouldReloadShippingMethodsOnPostalCodeChange();
    }

    /**
     * Should reload shipping methods on shipping regions change config
     *
     * @return bool
     */
    public function shouldReloadOnRegionChange()
    {
        return $this->_helper()->shouldReloadShippingMethodsOnRegionChange();
    }

    /**
     * Should reload shipping methods on order total change config
     *
     * @return bool
     */
    public function shouldReloadOnOrderTotalChange()
    {
        return $this->_helper()->shouldReloadShippingMethodsOnOrderTotalChange();
    }

    /**
     * Should reload shipping methods on coupon code change config
     *
     * @return bool
     */
    public function shouldReloadOnCouponCodeChange()
    {
        return $this->_helper()->shouldReloadShippingMethodsOnCouponCodeChange();
    }
}
