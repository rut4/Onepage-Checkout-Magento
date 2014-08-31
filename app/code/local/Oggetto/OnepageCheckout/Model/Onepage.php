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
 * Onepage checkout model
 *
 * @category   Oggetto
 * @package    Oggetto_OnepageCheckout
 * @subpackage Model
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_OnepageCheckout_Model_Onepage extends Mage_Checkout_Model_Type_Onepage
{
    /**
     * Error message of "customer already exists"
     *
     * @var string
     */
    private $_customerEmailExistsMessage = '';

    /**
     * @var Oggetto_OnepageCheckout_Helper_Data
     */
    protected $_helper;

    /**
     * @var string
     */
    protected $_checkoutMethod;

    /**
     * Class constructor
     * Set customer already exists message
     */
    public function __construct()
    {
        parent::__construct();
        $this->_helper = Mage::helper('onepageCheckout');
        $this->_customerEmailExistsMessage = $this->_helper->__(
            'There is already a customer registered using this email address. Please login using this email address '
            . 'or enter a different email address to register your account.');
    }

    /**
     * Initialize quote state to be valid for one page checkout
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function initCheckout()
    {
        $customerSession = $this->getCustomerSession();

        /**
         * want to load the correct customer information by assigning to address
         * instead of just loading from sales/quote_address
         */
        $customer = $customerSession->getCustomer();
        if ($customer) {
            $this->getQuote()->assignCustomer($customer);
        }
        return $this;
    }

    /**
     * Get quote checkout method
     *
     * @return string
     */
    public function getCheckoutMethod()
    {
        if ($this->getCustomerSession()->isLoggedIn()) {
            return self::METHOD_CUSTOMER;
        }
        if (!$this->_checkoutMethod && $checkoutMethod = $this->getQuote()->getCheckoutMethod()) {
            $this->_checkoutMethod = $checkoutMethod;
        }
        if (!$this->_checkoutMethod) {
            if ($this->_helper->isAllowedGuestCheckout()) {
                $this->getQuote()->setCheckoutMethod(self::METHOD_GUEST);
                $this->_checkoutMethod = self::METHOD_GUEST;
            } else {
                $this->getQuote()->setCheckoutMethod(self::METHOD_REGISTER);
                $this->_checkoutMethod = self::METHOD_REGISTER;
            }
        }
        return $this->_checkoutMethod;
    }

    /**
     * Specify checkout method
     *
     * @param string $method Checkout method code
     * @return array
     */
    public function saveCheckoutMethod($method)
    {
        if (empty($method)) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
        }

        $this->getQuote()->setCheckoutMethod($method)->save();
        $this->_checkoutMethod = $method;
        return array();
    }

    /**
     * Save billing address information to quote
     * This method is called by One Page Checkout JS (AJAX) while saving the billing information.
     *
     * @param array $data Billing data
     * @param int $customerAddressId Customer address ID
     * @return array
     */
    public function saveBilling($data, $customerAddressId)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
        }

        if (isset($data['register']) && $data['register'] == 'on' || !$this->_helper->isAllowedGuestCheckout()) {
            $this->saveCheckoutMethod(self::METHOD_REGISTER);
        } else if ($this->getCustomerSession()->isLoggedIn()) {
            $this->saveCheckoutMethod(self::METHOD_CUSTOMER);
        } else {
            $this->saveCheckoutMethod(self::METHOD_GUEST);
        }

        $address = $this->getQuote()->getBillingAddress();
        /* @var $addressForm Mage_Customer_Model_Form */
        $addressForm = Mage::getModel('onepageCheckout/customer_form');
        $addressForm->setFormCode('customer_address_edit')
            ->setEntityType('customer_address')
            ->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());

        $addressForm->setEntity($address);
        // emulate request object
        $addressData    = $addressForm->extractData($addressForm->prepareRequest($data));
        $addressErrors  = $addressForm->validateData($addressData);
        if ($addressErrors !== true) {
            return array('error' => 1, 'message' => array_values($addressErrors));
        }
        $addressForm->compactData($addressData);
        //unset billing address attributes which were not shown in form
        foreach ($addressForm->getAttributes() as $attribute) {
            if (!isset($data[$attribute->getAttributeCode()])) {
                $address->setData($attribute->getAttributeCode(), NULL);
            }
        }
        $address->setCustomerAddressId(null);
        // Additional form data, not fetched by extractData (as it fetches only attributes)
        $address->setSaveInAddressBook(empty($data['save_in_address_book']) ? 0 : 1);

        // set email for newly created user
        if (!$address->getEmail() && $this->getQuote()->getCustomerEmail()) {
            $address->setEmail($this->getQuote()->getCustomerEmail());
        }

        $address->implodeStreetAddress();

        if (true !== ($result = $this->_validateCustomerData($data))) {
            return $result;
        }

        if (!$this->getQuote()->getCustomerId() && self::METHOD_REGISTER == $this->getCheckoutMethod()) {
            if ($this->_customerEmailExists($address->getEmail(), Mage::app()->getWebsite()->getId())) {
                return array('error' => 1, 'message' => $this->_customerEmailExistsMessage);
            }
        }

        if (!$this->getQuote()->isVirtual()) {
            $this->_saveBilling($data, $address);

        }

        $this->getQuote()->collectTotals();
        $this->getQuote()->save();

        if (!$this->getQuote()->isVirtual()) {
            //Recollect Shipping rates for shipping methods
            $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        }

        return array();
    }

    /**
     * Save checkout shipping address
     *
     * @param array $data Shipping address data
     * @param int $customerAddressId Customer address id
     * @return array
     */
    public function saveShipping($data, $customerAddressId)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
        }
        $address = $this->getQuote()->getShippingAddress();

        /* @var $addressForm Mage_Customer_Model_Form */
        $addressForm = Mage::getModel('onepageCheckout/customer_form');
        $addressForm->setFormCode('customer_address_edit')
            ->setEntityType('customer_address')
            ->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());

        $addressForm->setEntity($address);
        // emulate request object
        $addressData    = $addressForm->extractData($addressForm->prepareRequest($data));
        $addressErrors  = $addressForm->validateData($addressData);
        if ($addressErrors !== true) {
            return array('error' => 1, 'message' => $addressErrors);
        }
        $addressForm->compactData($addressData);
        // unset shipping address attributes which were not shown in form
        foreach ($addressForm->getAttributes() as $attribute) {
            if (!isset($data[$attribute->getAttributeCode()])) {
                $address->setData($attribute->getAttributeCode(), NULL);
            }
        }

        $address->setCustomerAddressId(null);
        // Additional form data, not fetched by extractData (as it fetches only attributes)
        $address->setSaveInAddressBook(empty($data['save_in_address_book']) ? 0 : 1);
        $address->setSameAsBilling(empty($data['same_as_billing']) ? 0 : 1);

        $address->implodeStreetAddress();
        $address->setCollectShippingRates(true);

        $this->getQuote()->collectTotals()->save();
        return array();
    }

    /**
     * Specify quote shipping method
     *
     * @param string $shippingMethod Shipping method code
     * @return array
     */
    public function saveShippingMethod($shippingMethod)
    {
        if (empty($shippingMethod)) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid shipping method.'));
        }
        $rate = $this->getQuote()
            ->getShippingAddress()
            ->getShippingRateByCode($shippingMethod);
        if (!$rate) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid shipping method.'));
        }
        $this->getQuote()
            ->getShippingAddress()
            ->setShippingMethod($shippingMethod);
        return array();
    }

    /**
     * Specify quote payment method
     *
     * @param array $data Payment data
     * @return array
     */
    public function savePayment($data)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => $this->_helper->__('Invalid data.'));
        }
        $quote = $this->getQuote();
        if ($quote->isVirtual()) {
            $quote->getBillingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        } else {
            $quote->getShippingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        }

        // shipping totals may be affected by payment method
        if (!$quote->isVirtual() && $quote->getShippingAddress()) {
            $quote->getShippingAddress()->setCollectShippingRates(true);
        }

        $data['checks'] = Mage_Payment_Model_Method_Abstract::CHECK_USE_CHECKOUT
            | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY
            | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY
            | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX
            | Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL;

        $payment = $quote->getPayment();
        $payment->importData($data);

        $quote->save();

        return array();
    }

    /**
     * Validate quote state to be integrated with one page checkout process
     *
     * @return void
     */
    public function validate()
    {
        if ($this->getCheckoutMethod() == self::METHOD_GUEST && !$this->_helper->isAllowedGuestCheckout()) {
            Mage::throwException(
                $this->_helper->__('Sorry, guest checkout is not enabled. Please try again or contact store owner.')
            );
        }
    }

    /**
     * Involve new customer to system
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    protected function _involveNewCustomer()
    {
        $customer = $this->getQuote()->getCustomer();
        if ($customer->isConfirmationRequired()) {
            $customer->sendNewAccountEmail('confirmation', '', $this->getQuote()->getStoreId());
            $url = Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail());
            $this->getCustomerSession()->addSuccess(
                $this->_helper->__(
                    'Account confirmation is required. Please, check your e-mail for confirmation link. To resend '
                    . 'confirmation email please <a href="%s">click here</a>.', $url)
            );
        } else {
            $customer->sendNewAccountEmail('registered', '', $this->getQuote()->getStoreId());
            $this->getCustomerSession()->loginById($customer->getId());
        }
        return $this;
    }

    /**
     * Create order based on checkout type. Create customer if necessary.
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function saveOrder()
    {
        $this->validate();
        $isNewCustomer = false;
        switch ($this->getCheckoutMethod()) {
            case self::METHOD_GUEST:
                $this->_prepareGuestQuote();
                break;
            case self::METHOD_REGISTER:
                $this->_prepareNewCustomerQuote();
                $isNewCustomer = true;
                break;
            default:
                $this->_prepareCustomerQuote();
                break;
        }

        /** @var Oggetto_OnepageCheckout_Model_Service_Quote $service */
        $service = Mage::getModel('onepageCheckout/service_quote', $this->getQuote());
        $service->submitAll();

        if ($isNewCustomer) {
            try {
                $this->_involveNewCustomer();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        $this->_checkoutSession->setLastQuoteId($this->getQuote()->getId())
            ->setLastSuccessQuoteId($this->getQuote()->getId())
            ->clearHelperData();

        $order = $service->getOrder();
        if ($order) {
            Mage::dispatchEvent('checkout_type_onepage_save_order_after',
                array('order' => $order, 'quote' => $this->getQuote()));

            /**
             * a flag to set that there will be redirect to third party after confirmation
             * eg: paypal standard ipn
             */
            $redirectUrl = $this->getQuote()->getPayment()->getOrderPlaceRedirectUrl();
            /**
             * we only want to send to customer about new order when there is no redirect to third party
             */
            if (!$redirectUrl && $order->getCanSendNewEmailFlag()) {
                try {
                    $order->sendNewOrderEmail();
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            // add order information to the session
            $this->_checkoutSession->setLastOrderId($order->getId())
                ->setRedirectUrl($redirectUrl)
                ->setLastRealOrderId($order->getIncrementId());

            // as well a billing agreement can be created
            $agreement = $order->getPayment()->getBillingAgreement();
            if ($agreement) {
                $this->_checkoutSession->setLastBillingAgreementId($agreement->getId());
            }
        }

        $profiles = $service->getRecurringPaymentProfiles();
        if ($profiles) {
            $ids = array();
            foreach ($profiles as $profile) {
                $ids[] = $profile->getId();
            }
            $this->_checkoutSession->setLastRecurringProfileIds($ids);
        }

        Mage::dispatchEvent(
            'checkout_submit_all_after',
            array('order' => $order, 'quote' => $this->getQuote(), 'recurring_profiles' => $profiles)
        );

        return $this;
    }

    /**
     * Validate quote state to be able submitted from one page checkout page
     *
     * @deprecated after 1.4 - service model doing quote validation
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function validateOrder()
    {
        if ($this->getQuote()->getIsMultiShipping()) {
            Mage::throwException($this->_helper->__('Invalid checkout type.'));
        }

        if (!$this->getQuote()->isVirtual()) {
            $address = $this->getQuote()->getShippingAddress();
            $method = $address->getShippingMethod();
            $rate = $address->getShippingRateByCode($method);
            if (!$this->getQuote()->isVirtual() && (!$method || !$rate)) {
                Mage::throwException($this->_helper->__('Please specify shipping method.'));
            }
        }

        if (!($this->getQuote()->getPayment()->getMethod())) {
            Mage::throwException($this->_helper->__('Please select valid payment method.'));
        }
    }

    /**
     * Save billing address from data
     *
     * @param array $data Billing data
     * @param Mage_Sales_Model_Quote_Address $address Billing address
     * @return void
     */
    protected function _saveBilling($data, $address)
    {
        /**
         * Billing address using otions
         */
        $usingCase = isset($data['use_for_shipping']) ? (int)$data['use_for_shipping'] : 0;

        switch ($usingCase) {
            case 0:
                $shipping = $this->getQuote()->getShippingAddress();
                $shipping->setSameAsBilling(0);
                break;
            case 1:
                $billing = clone $address;
                $billing->unsAddressId()->unsAddressType();
                $shipping = $this->getQuote()->getShippingAddress();
                $shippingMethod = $shipping->getShippingMethod();

                // Billing address properties that must be always copied to shipping address
                $requiredBillingAttributes = array('customer_address_id');

                // don't reset original shipping data, if it was not changed by customer
                foreach ($shipping->getData() as $shippingKey => $shippingValue) {
                    if (!is_null($shippingValue) && !is_null($billing->getData($shippingKey))
                        && !isset($data[$shippingKey]) && !in_array($shippingKey, $requiredBillingAttributes)
                    ) {
                        $billing->unsetData($shippingKey);
                    }
                }
                $shipping->addData($billing->getData())
                    ->setSameAsBilling(1)
                    ->setSaveInAddressBook(0)
                    ->setShippingMethod($shippingMethod)
                    ->setCollectShippingRates(true);
                break;
        }
    }
}
