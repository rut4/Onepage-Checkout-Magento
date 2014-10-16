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
 * Payment block
 *
 * @category   Oggetto
 * @package    Oggetto_OnepageCheckout
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_OnepageCheckout_Block_Onepage_Payment extends Mage_Checkout_Block_Onepage_Payment
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
     * Are payment methods available
     *
     * @return bool
     */
    public function arePaymentMethodsAvailable()
    {
        return (bool)$this->getQuote()->getPaymentsCollection()->count();
    }

    /**
     * Should reload payment methods on billing country change
     *
     * @return bool
     */
    public function shouldReloadOnCountryChange()
    {
        return $this->_helper()->shouldReloadPaymentMethodsOnCountryChange();
    }

    /**
     * Should reload payment methods on oreder totals change
     *
     * @return bool
     */
    public function shouldReloadOnOrderTotalChange()
    {
        return $this->_helper()->shouldReloadPaymentMethodsOnOrderTotalChange();
    }
}
