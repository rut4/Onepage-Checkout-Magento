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
 * Checkout helper
 *
 * @category   Oggetto
 * @package    Oggetto_OnepageCheckout
 * @subpackage Helper
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_OnepageCheckout_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Get is quote virtual
     *
     * @return bool Is quote virtual
     */
    public function isQuoteVirtual()
    {
        return $this->getOnepage()
            ->getQuote()
            ->isVirtual();
    }

    /**
     * Get ajax module helper
     *
     * @return Oggetto_Ajax_Helper_Data Ajax module helper
     */
    public function getAjax()
    {
        return Mage::helper('ajax');
    }

    /**
     * Get onepage model singleton
     *
     * @return Oggetto_OnepageCheckout_Model_Onepage Onepage model Singleton
     */
    public function getOnepage()
    {
        return Mage::getSingleton('onepageCheckout/onepage');
    }

    /**
     * Get quote
     *
     * @return Mage_Sales_Model_Quote Quote
     */
    public function getQuote()
    {
        return $this->getOnepage()
            ->getQuote();
    }

    /**
     * Get address field config
     *
     * @param string $field Address field
     * @return bool|string False if field disabled, Optional or Required if field enabled
     */
    public function getAddressFieldConfig($field)
    {
        $config = Mage::getStoreConfig("onepage/address_fields/{$field}");
        return $config == '' ? false : $config;
    }

    /**
     * Get layout column count config
     *
     * @return string Column count
     */
    public function getColumnCount()
    {
        return Mage::getStoreConfig('onepage/general/layout_columns');
    }

    /**
     * Get layout main page title
     *
     * @return string Main page title
     */
    public function getTitle()
    {
        return Mage::getStoreConfig('onepage/general/title');
    }

    /**
     * Get is allowed guest checkout
     *
     * @return bool Is allowed guest checkout
     */
    public function isAllowedGuestCheckout()
    {
        return Mage::getStoreConfigFlag('onepage/general/registration_mode');
    }

    /**
     * Get show subscribe to newsletter
     *
     * @return bool Show subscribe to newsletter
     */
    public function shouldShowSubscribeToNewsletter()
    {
        return Mage::getStoreConfigFlag('onepage/fields_setup/subscribe_newsletter_show');
    }

    /**
     * Get show coupon field
     *
     * @return bool Show coupon field
     */
    public function shouldShowCouponField()
    {
        return Mage::getStoreConfigFlag('onepage/fields_setup/coupon_field_show');
    }

    /**
     * Get default country id
     *
     * @return string Default country id
     */
    public function getDefaultCountryId()
    {
        return Mage::getStoreConfig('onepage/shipping/default_country');
    }

    /**
     * Get is module enabled
     *
     * @return bool Is module enabled
     */
    public function isOnepageModuleEnabled()
    {
        return Mage::getStoreConfigFlag('onepage/general/enabled');
    }

    /**
     * Get required address attributes
     *
     * @return array Required address attributes
     */
    public function getRequiredAddressAttributes()
    {
        $fields = Mage::getStoreConfig('onepage/address_fields');
        $reqFields = [];
        foreach ($fields as $code => $value) {
            if ($value == 'req') {
                $reqFields[] = $code;
            }
        }
        $reqFields[] = 'firstname';
        $reqFields[] = 'lastname';
        $reqFields[] = 'email';
        if (!$this->isAllowedGuestCheckout()) {
            $reqFields[] = 'customer_password';
            $reqFields[] = 'confirm_password';
        }
        return $reqFields;
    }

    /**
     * Should reload payment methods on country change
     *
     * @return bool
     */
    public function shouldReloadPaymentMethodsOnCountryChange()
    {
        return Mage::getStoreConfigFlag('onepage/ajax/reload_methods_country_change');
    }

    /**
     * Should reload payment methods on order total change
     *
     * @return bool
     */
    public function shouldReloadPaymentMethodsOnOrderTotalChange()
    {
        return Mage::getStoreConfigFlag('onepage/ajax/reload_methods_order_total_change');
    }

    /**
     * Should reload shipping methods on country change
     *
     * @return bool
     */
    public function shouldReloadShippingMethodsOnCountryChange()
    {
        return Mage::getStoreConfigFlag('onepage/ajax/reload_shipping_methods_country_change');
    }

    /**
     * Should reload shipping methods on postal code change
     *
     * @return bool
     */
    public function shouldReloadShippingMethodsOnPostalCodeChange()
    {
        return Mage::getStoreConfigFlag('onepage/ajax/reload_shipping_methods_postal_change');
    }

    /**
     * Should reload shipping methods on region change
     *
     * @return bool
     */
    public function shouldReloadShippingMethodsOnRegionChange()
    {
        return Mage::getStoreConfigFlag('onepage/ajax/reload_shipping_methods_region_change');
    }

    /**
     * Should reload shipping methods on order total change
     *
     * @return bool
     */
    public function shouldReloadShippingMethodsOnOrderTotalChange()
    {
        return Mage::getStoreConfigFlag('onepage/ajax/reload_shipping_methods_order_total_change');
    }

    /**
     * Is shipping same as billing
     *
     * @return bool
     */
    public function isShippingSameAsBillingConfig()
    {
        return Mage::getStoreConfigFlag('onepage/shipping/ship_same_address_checkbox_checked');
    }

    /**
     * Should show shipping address form
     *
     * @return bool
     */
    public function shouldShowShippingAddressForm()
    {
        return Mage::getStoreConfigFlag('onepage/shipping/show_shipping_address_form');
    }

    /**
     * Get default shipping method
     *
     * @return string
     */
    public function getDefaultShippingMethod()
    {
        return Mage::getStoreConfig('onepage/shipping/default_method');
    }

    /**
     * Should show free shipping only if available
     *
     * @return bool
     */
    public function shouldShowFreeShippingOnly()
    {
        return Mage::getStoreConfigFlag('onepage/shipping/show_free_only');
    }

    /**
     * Get default payment method
     *
     * @return string
     */
    public function getDefaultPaymentMethod()
    {
        return Mage::getStoreConfig('onepage/payment/default_method');
    }

    /**
     * Is delivery date and time choosing enabled
     *
     * @return bool
     */
    public function isDeliveryDateTimeEnabled()
    {
        return Mage::getStoreConfigFlag('onepage/delivery_date_time/enabled');
    }

    /**
     * Should use calendar
     *
     * @return bool
     */
    public function shouldUseCalendar()
    {
        return Mage::getStoreConfigFlag('onepage/delivery_date_time/use_calendar');
    }

    /**
     * Get use time range
     *
     * @return bool Use time range
     */
    public function getUseTimeRange()
    {
        return Mage::getStoreConfigFlag('onepage/delivery_date_time/use_time_range');
    }

    /**
     * Get time ranges for delivery
     *
     * @return array
     */
    public function getTimeRanges()
    {
        return unserialize(Mage::getStoreConfig('onepage/delivery_date_time/time_ranges'));
    }

    /**
     * Is delivery date and time available for all shipping methods
     *
     * @return bool
     */
    public function isDeliveryDateTimeAvailableForAll()
    {
        return Mage::getStoreConfigFlag('onepage/delivery_date_time/display_mode');
    }

    /**
     * Get delivery date and time available sipping methods
     *
     * @return array
     */
    public function getDeliveryDateTimeMethodsAvailable()
    {
        return Mage::getStoreConfig('onepage/delivery_date_time/shipping_methods');
    }

    /**
     * Is setup delivery date and time allowed for current method
     *
     * @return bool
     */
    public function isSetupDeliveryAllowedForCurrentMethod()
    {
        if ($this->isDeliveryDateTimeAvailableForAll()) {
            return true;
        }
        $methods = $this->getDeliveryDateTimeMethodsAvailable();
        $currentMethod = $this->getOnepage()->getQuote()->getShippingAddress()->getShippingMethod();
        if (!$currentMethod) {
            return false;
        }
        if (!is_array($methods)) {
            return $methods == $currentMethod;
        }
        foreach ($methods as $code => $method) {
            if ($currentMethod == $code) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get formatted time range
     *
     * @param array $timeRange Time range as array
     * @return string Time range
     */
    public function formatTimeRange(array $timeRange)
    {
        $time = new DateTime();

        $time->setTime($timeRange['from']['hour'], $timeRange['from']['minute']);
        $result = $time->format('H:i') . ' - ';

        $time->setTime($timeRange['to']['hour'], $timeRange['to']['minute']);
        $result .= $time->format('H:i');

        return $result;
    }

    /**
     * Get delivery exclude dates
     *
     * @return array Delivery exclude dates
     */
    public function getDeliveryExcludeDates()
    {
        return unserialize(Mage::getStoreConfig('onepage/delivery_date_time/dates_to_exclude'));
    }

    /**
     * Are agreements enabled
     *
     * @return bool
     */
    public function areAgreementsEnabled()
    {
        return Mage::getStoreConfigFlag('onepage/terms_conditions/output_type');
    }

    /**
     * Get custom checkout fields
     *
     * @return array Custom checkout fields
     */
    public function getCustomCheckoutFields()
    {
        $fields = [];
        for ($i = 1; $i <= 5; $i++) {
            if ((bool)Mage::getStoreConfig("custom_fields/field{$i}/show")) {
                $fields[$i - 1] = Mage::getStoreConfig("custom_fields/field{$i}");
                $fields[$i - 1]['options'] = array_values(unserialize($fields[$i - 1]['options']));
                $options = &$fields[$i - 1]['options'];
                for ($j = 0; $j < count($options); $j++) {
                    $options[$j] = $options[$j]['options'];
                }
            }
        }
        return $fields;
    }

    /**
     * Validate additional info
     *
     * @param array $additional Additional info
     * @return array Validation result
     */
    public function validateAdditional(array $additional)
    {
        $validateResult = [];
        foreach ($additional as $name => $field) {
            if (Mage::getStoreConfig("custom_fields/{$name}/show") == 'req' && empty($field['value'])) {
                $validateResult['error'] = 1;
                $validateResult['message'][] = "Please, fill the {$field['label']} field";
            }
        }
        return $validateResult;
    }

    /**
     * Should reload totals on payment method change config
     *
     * @return bool
     */
    public function shouldReloadTotalsOnPaymentMethodChange()
    {
        return Mage::getStoreConfigFlag('onepage/ajax/reload_order_totals_method_change');
    }

    /**
     * Should reload totals on billing country change config
     *
     * @return bool
     */
    public function shouldReloadTotalsOnBillingCountryChange()
    {
        return Mage::getStoreConfigFlag('onepage/ajax/reload_order_totals_country_change');
    }

    /**
     * Should reload totals on shipping method change config
     *
     * @return bool
     */
    public function shouldReloadTotalsOnShippingMethodChange()
    {
        return Mage::getStoreConfigFlag('onepage/ajax/reload_order_totals_shipping_method_change');
    }

    /**
     * Should reload totals on billing region change config
     *
     * @return bool
     */
    public function shouldReloadTotalsOnBillingRegionChange()
    {
        return Mage::getStoreConfigFlag('onepage/ajax/reload_order_totals_region_change');
    }

    /**
     * Should reload totals on billing postcode change config
     *
     * @return bool
     */
    public function shouldReloadTotalsOnBillingPostcodeChange()
    {
        return Mage::getStoreConfigFlag('onepage/ajax/reload_order_totals_postal_code_change');
    }

    /**
     * Should show product name as link config
     *
     * @return bool
     */
    public function shouldShowProductNameAsLinkConfig()
    {
        return Mage::getStoreConfigFlag('onepage/shopping_cart/product_name_as_link');
    }

    /**
     * Should reload shipping methods on coupon code change
     *
     * @return bool
     */
    public function shouldReloadShippingMethodsOnCouponCodeChange()
    {
        return Mage::getStoreConfigFlag('onepage/ajax/reload_shipping_methods_coupon_code');
    }
}
