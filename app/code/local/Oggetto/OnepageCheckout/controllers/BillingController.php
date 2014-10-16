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
 * Onepage checkout billing controller
 *
 * @category   Oggetto
 * @package    Oggetto_OnepageCheckout
 * @subpackage Controller
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_OnepageCheckout_BillingController extends Oggetto_OnepageCheckout_AbstractController
{
    /**
     * Save billing action
     *
     * @return void
     */
    public function saveAction()
    {
        $request = $this->getRequest();
        $billingData = $request->getPost('billing', []);
        if (isset($billingData['use_for_shipping'])) {
            $billingData['use_for_shipping'] = $billingData['use_for_shipping'] == 'on' ? 1 : 0;
        }
        $result = $this->_helper()
            ->getOnepage()
            ->saveBilling($billingData, null);

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
