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
 * Abstract onepage checkout controller
 *
 * @category   Oggetto
 * @package    Oggetto_OnepageCheckout
 * @subpackage Controller
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_OnepageCheckout_AbstractController extends Mage_Core_Controller_Front_Action
{
    /**
     * Get this module date helper
     *
     * @return Oggetto_OnepageCheckout_Helper_Data Data helper
     */
    protected function _helper()
    {
        return Mage::helper('onepageCheckout');
    }

    /**
     * Send ajax response
     *
     * @param Oggetto_Ajax_Model_Response $response Ajax response
     * @return void
     */
    protected function _sendResponse(Oggetto_Ajax_Model_Response $response)
    {
        $this->_helper()->getAjax()->sendResponse($response);
    }

    /**
     * Get ajax response model
     *
     * @return Oggetto_Ajax_Model_Response
     */
    protected function _getResponseModel()
    {
        return Mage::getModel('ajax/response');
    }
}
