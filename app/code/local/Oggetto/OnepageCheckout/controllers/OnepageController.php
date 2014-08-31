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
 * Onepage checkout main page controller
 *
 * @category   Oggetto
 * @package    Oggetto_OnepageCheckout
 * @subpackage Controller
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_OnepageCheckout_OnepageController extends Oggetto_OnepageCheckout_AbstractController
{
    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        $onepage = $this->_helper()->getOnepage();
        $onepage->initCheckout();

        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $onepage->getQuote();

        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('checkout/cart');
            return;
        }

        $this->loadLayout();
        $this->getLayout()
            ->getBlock('head')
            ->setTitle($this->_helper()->getTitle());

        $this->renderLayout();
    }

    /**
     * Subscribe to newsletter action
     *
     * @return void
     */
    public function subscribeAction()
    {
        $subsribe = $this->getRequest()->getParam('subscribe');

        $onepage = $this->_helper()->getOnepage();
        $quote = $onepage->getQuote();

        /** @var Mage_Newsletter_Model_Subscriber $subscriber */
        $subscriber = Mage::getModel('newsletter/subscriber');

        $email = $quote->getCustomerEmail();
        if (is_null($email)) {
            $email = $quote->getBillingAddress()->getEmail();
        }
        if (!is_null($email)) {
            if ($subsribe == 'on') {
                $subscriber->subscribe($email);
            } else {
                $subscriber->loadByEmail($email);
                $subscriber->unsubscribe();
            }
        }
    }
}
