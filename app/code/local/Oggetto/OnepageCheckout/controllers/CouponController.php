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

require_once Mage::getModuleDir('controllers', 'Oggetto_OnepageCheckout') . DS . 'AbstractController.php';

/**
 * Onepage checkout coupon controller
 *
 * @category   Oggetto
 * @package    Oggetto_OnepageCheckout
 * @subpackage Controller
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_OnepageCheckout_CouponController extends Oggetto_OnepageCheckout_AbstractController
{
    /**
     * Update action
     *
     * @return void
     */
    public function updateAction()
    {
        $this->getRequest();

        $response = $this->_getResponseModel();

        /**
         * No reason continue with empty shopping cart
         */
        if (!$this->_helper()->getQuote()->getItemsCount()) {
            $response->error();
            $this->_sendResponse($response);
            return;
        }

        $couponCode = (string)$this->getRequest()->getParam('coupon_code');
        if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
        }
        $oldCouponCode = $this->_getQuote()->getCouponCode();

        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            $response->error();
            $this->_sendResponse($response);
            return;
        }

        try {
            $codeLength = strlen($couponCode);
            $isCodeLengthValid = $codeLength && $codeLength <= Mage_Checkout_Helper_Cart::COUPON_CODE_MAX_LENGTH;

            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode($isCodeLengthValid ? $couponCode : '')
                ->collectTotals()
                ->save();

            if ($codeLength) {
                if ($isCodeLengthValid && $couponCode == $this->_getQuote()->getCouponCode()) {
                    $response->success()
                        ->setCoupon('valid')
                        ->setOperation('apply')
                        ->setMessage(
                            $this->__("Coupon code '%s' was applied.", Mage::helper('core')->escapeHtml($couponCode))
                        );
                } else {
                    $response->success()
                        ->setCoupon('invalid')
                        ->setOperation('apply')
                        ->setMessage(
                            $this->__("Coupon code '%s' is not valid.", Mage::helper('core')->escapeHtml($couponCode))
                        );
                }
            } else {
                $response->success()
                    ->setCoupon('valid')
                    ->setOperation('cancel')
                    ->setMessage($this->__('Coupon code was canceled.'));
            }

            $this->loadLayout();
            $totalsHtml = $this->getLayout()
                ->getBlock('checkout.onepage.review.info.totals')
                ->toHtml();

            $response->setTotals($totalsHtml);

        } catch (Mage_Core_Exception $e) {
            $response->error()
                ->setMessage($e->getMessage());
        } catch (Exception $e) {
            $response->error()
                ->setOperation('apply')
                ->setMessage($this->__('Cannot apply the coupon code.'));
            Mage::logException($e);
        }

        $this->_sendResponse($response);
    }
}
