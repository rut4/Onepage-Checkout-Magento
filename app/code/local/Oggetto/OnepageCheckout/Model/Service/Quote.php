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
 * Quote service model
 *
 * @category   Oggetto
 * @package    Oggetto_OnepageCheckout
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_OnepageCheckout_Model_Service_Quote extends Mage_Sales_Model_Service_Quote
{
    /**
     * Validate quote data before converting to order
     *
     * @return Mage_Sales_Model_Service_Quote Quote
     */
    protected function _validate()
    {
        if (!$this->getQuote()->isVirtual()) {
            $address = $this->getQuote()->getShippingAddress();
            /* @var $addressForm Mage_Customer_Model_Form */
            $addressForm = Mage::getModel('onepageCheckout/customer_form');
            $addressForm->setFormCode('customer_address_edit')
                ->setEntityType('customer_address')
                ->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());

            $addressForm->setEntity($address);
            // emulate request object
            $addressData    = $addressForm->extractData($addressForm->prepareRequest($address->getData()));
            $addressErrors  = $addressForm->validateData($addressData);
            if ($addressErrors !== true) {
                Mage::throwException(
                    Mage::helper('onepageCheckout')->__(
                        'Please check shipping address information. %s',
                        implode(' ', $addressErrors))
                );
            }

            $method = $address->getShippingMethod();
            $rate = $address->getShippingRateByCode($method);
            if (!$this->getQuote()->isVirtual() && (!$method || !$rate)) {
                Mage::throwException(Mage::helper('onepageCheckout')->__('Please specify a shipping method.'));
            }
        }

        $address = $this->getQuote()->getBillingAddress();
        /* @var $addressForm Mage_Customer_Model_Form */
        $addressForm = Mage::getModel('onepageCheckout/customer_form');
        $addressForm->setFormCode('customer_address_edit')
            ->setEntityType('customer_address')
            ->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());
        $addressForm->setEntity($address);
        // emulate request object
        $addressData    = $addressForm->extractData($addressForm->prepareRequest($address->getData()));
        $addressErrors  = $addressForm->validateData($addressData);

        if ($addressErrors !== true) {
            Mage::throwException(
                Mage::helper('onepageCheckout')->__(
                    'Please check billing address information. %s',
                    implode(' ', $addressErrors))
            );
        }

        if (!($this->getQuote()->getPayment()->getMethod())) {
            Mage::throwException(Mage::helper('onepageCheckout')->__('Please select a valid payment method.'));
        }

        return $this;
    }
}
