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
 * Onepage checkout payment controller
 *
 * @category   Oggetto
 * @package    Oggetto_OnepageCheckout
 * @subpackage Controller
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_OnepageCheckout_PaymentController extends Oggetto_OnepageCheckout_AbstractController
{
    /**
     * Get payment methods action
     *
     * @return void
     */
    public function getMethodsAction()
    {
        $this->loadLayout();

        /** @var Mage_Checkout_Block_Onepage_Payment_Methods $block */
        $block = $this->getLayout()->getBlock('checkout.payment.methods');
        $html = $block->toHtml();

        $this->getResponse()->setBody($html);
    }

    /**
     * Save payment method action
     *
     * @return void
     */
    public function saveMethodAction()
    {
        $data = $this->getRequest()->getPost('payment', array());

        /** @var Oggetto_OnepageCheckout_Model_Onepage $onepage */
        $this->_helper()->getOnepage()->savePayment($data);
    }
}
