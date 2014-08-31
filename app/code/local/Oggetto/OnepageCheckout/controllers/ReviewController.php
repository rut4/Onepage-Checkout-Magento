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
 * Onepage checkout review controller
 *
 * @category   Oggetto
 * @package    Oggetto_OnepageCheckout
 * @subpackage Controller
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_OnepageCheckout_ReviewController extends Oggetto_OnepageCheckout_AbstractController
{
    /**
     * Get this module data helper
     *
     * @return Oggetto_OnepageCheckout_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('onepageCheckout');
    }

    /**
     * Get totals table action
     *
     * @return void
     */
    public function getTotalsAction()
    {
        $this->loadLayout();

        /** @var Mage_Checkout_Block_Cart_Totals $block */
        $block = $this->getLayout()->getBlock('checkout.onepage.review.info.totals');
        $html = $block->toHtml();

        $this->getResponse()->setBody($html);
    }

    /**
     * Get place order button action
     *
     * @return void
     */
    public function getButtonAction()
    {
        /** @var Oggetto_OnepageCheckout_Model_Onepage $onepage */
        $onepage = Mage::helper('onepageCheckout')->getOnepage();

        /** @var Oggetto_Ajax_Model_Response $response */
        $response = Mage::getModel('ajax/response');

        /** @var Oggetto_Ajax_Helper_Data $ajax */
        $ajax = Mage::helper('ajax');

        try {
            $onepage->validateOrder();
            $this->loadLayout();

            /** @var Mage_Core_Block_Template $block */
            $block = $this->getLayout()->getBlock('checkout.onepage.review.button');
            $html = $block->toHtml();
            $response->success()->setMessage($html);
        } catch (Exception $e) {
            $html = "<p>{$e->getMessage()}</p>";
            $response->error()->setMessage($html);
        }
        $ajax->sendResponse($response);
    }

    /**
     * Save order action
     *
     * @return void
     */
    public function saveOrderAction()
    {
        /** @var Oggetto_OnepageCheckout_Model_Onepage $onepage */
        $onepage = $this->_helper()->getOnepage();

        $response = $this->_getResponseModel();

        $onepage->getQuote()
            ->collectTotals()
            ->save();
        try {
            $delivery = $this->_prepareDelivery();
            $additional = $this->_validateAdditional($this->getRequest()->getPost('additional', []));
            $onepage->saveOrder();
            if (!is_null($delivery)) {
                $delivery->setOrderId($onepage->getCheckout()->getLastOrderId());
                $delivery->save();
            }
            $this->_saveAdditional($additional);
            $this->_redirect('checkout/onepage/success');
        } catch (Exception $e) {
            Mage::logException($e);
            $response->error()
                ->setMessage($e->getMessage());
        }
        $onepage->getQuote()->save();
        $this->_sendResponse($response);
    }

    /**
     * Prepare delivery date and time model to save
     *
     * @return null|Oggetto_OnepageCheckout_Model_Delivery
     */
    protected function _prepareDelivery()
    {
        $deliveryData = $this->getRequest()->getPost('delivery', []);
        if (empty($deliveryData)) {
            return null;
        }

        /** @var Oggetto_OnepageCheckout_Model_Delivery $delivery */
        $delivery = Mage::getModel('onepageCheckout/delivery');
        if (isset($deliveryData['time'])) {
            $deliveryData['time'] = $delivery->getTimeRangeFromConfigId($deliveryData['time']);
        }
        $delivery->setData($deliveryData);
        $delivery->checkExcludeDays();
        $delivery->validate();
        return $delivery;
    }

    /**
     * Validate additional data
     *
     * @param array $additional Additional data
     * @return array Validated additional data
     */
    private function _validateAdditional(array $additional)
    {
        $validateResult = $this->_helper()->validateAdditional($additional);
        if (!empty($validateResult)) {
            Mage::throwException(implode('</br>', $validateResult['message']));
        }
        return $additional;
    }

    /**
     * Save additional information
     *
     * @param array $additional Additional information
     * @return void
     */
    protected function _saveAdditional(array $additional)
    {
        foreach ($additional as $fieldData) {
            /** @var Oggetto_OnepageCheckout_Model_CustomField $field */
            $field = Mage::getModel('onepageCheckout/customField');
            $field->setData($fieldData);
            $field->setOrderId($this->_helper()->getOnepage()->getCheckout()->getLastOrderId());
            $field->save();
        }
    }
}
