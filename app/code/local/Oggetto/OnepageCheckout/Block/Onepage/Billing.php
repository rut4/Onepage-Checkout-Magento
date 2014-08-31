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
 * Billing block
 *
 * @category   Oggetto
 * @package    Oggetto_OnepageCheckout
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_OnepageCheckout_Block_Onepage_Billing extends Mage_Checkout_Block_Onepage_Billing
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
            $countryId = Mage::helper('core')->getDefaultCountry();
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
     * Get billing address
     *
     * @return Mage_Sales_Model_Quote_Address Billing address
     */
    public function getAddress()
    {
        if ($this->getQuote()) {
            return $this->getQuote()->getBillingAddress();
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
        return $this->_helper()->getAddressFieldConfig($field);
    }

    /**
     * Get is allowed guest checkout
     *
     * @return bool Is allowed guest checkout
     */
    public function isAllowedGuestCheckout()
    {
        return $this->_helper()->isAllowedGuestCheckout();
    }

    /**
     * Get is customer subscribed
     *
     * @return bool Is customer subscribed
     */
    public function isSubscribed()
    {
        $quote = $this->getQuote();
        $email = $quote->getCustomerEmail();
        if (is_null($email)) {
            $email = $quote->getBillingAddress()->getEmail();
        }

        /** @var Mage_Newsletter_Model_Subscriber $subscriber */
        $subscriber = Mage::getModel('newsletter/subscriber');
        $subscriber->loadByEmail($email);
        return $subscriber->isSubscribed();
    }

    /**
     * Get is enabled subscribe to newsletter checkbox
     *
     * @return bool Is enables subscribe to newsletter checkbox
     */
    public function shouldShowSubscribeToNewsletter()
    {
        return $this->_helper()->shouldShowSubscribeToNewsletter();
    }
}
