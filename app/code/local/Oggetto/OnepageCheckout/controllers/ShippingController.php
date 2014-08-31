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
 * Onepage checkout shipping controller
 *
 * @category   Oggetto
 * @package    Oggetto_OnepageCheckout
 * @subpackage Controller
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_OnepageCheckout_ShippingController extends Oggetto_OnepageCheckout_AbstractController
{
    /**
     * Get shipping methods action
     *
     * @return void
     */
    public function getMethodsAction()
    {
        $this->loadLayout();

        /** @var Mage_Checkout_Block_Onepage_Shipping_Method_Available $block */
        $block = $this->getLayout()->getBlock('checkout.onepage.shipping_method.available');
        $html = $block->toHtml();

        $this->getResponse()->setBody($html);
    }

    /**
     * Save shipping method action
     *
     * @return void
     */
    public function saveMethodAction()
    {
        $method = $this->getRequest()->getPost('shipping_method');
        $this->_helper()
            ->getOnepage()
            ->saveShippingMethod($method);
        // ->saveQuote();
        $this->_helper()
            ->getQuote()
            ->collectTotals()
            ->save();

        $response = $this->_getResponseModel();

        $this->loadLayout();
        $totalsHtml = $this->getLayout()
            ->getBlock('checkout.onepage.review.info.totals')
            ->toHtml();

        $response->success()
            ->setTotals($totalsHtml);

        if ($this->_helper()->isDeliveryDateTimeEnabled() && $this->_helper()->isSetupDeliveryAllowedForCurrentMethod()) {
            $deliveryHtml = $this->getLayout()
                ->getBlock('checkout.onepage.shipping_method.datetime')
                ->toHtml();

            $response->setDelivery($deliveryHtml);
        }

        $this->_sendResponse($response);
    }

    /**
     * Save shipping address action
     *
     * @return void
     */
    public function saveAction()
    {
        $request = $this->getRequest();
        $shippingData = $request->getPost('shipping', []);
        $result = $this->_helper()
            ->getOnepage()
            ->saveShipping($shippingData, null);

        $response = $this->_getResponseModel();

        if (empty($result)) {
            $response->success();
        } else {
            $response->error()
                ->setMessage($result['message']);
        }

        $this->_sendResponse($response);
    }
}
