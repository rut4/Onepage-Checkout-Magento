<?php
/**
 * Oggetto Web chekcout extension for Magento
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
 * @copyright  Copyright (C) 2012 Oggetto Web ltd (http://oggettoweb.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

try {
    $installer = $this;
    $installer->startSetup();
    $connection = $installer->getConnection();

    $connection->addColumn(
        $installer->getTable('sales/order'),
        'additional_comment',
        [
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable'  => true,
            'default'   => null,
            'comment'   => 'Comment'
        ]
    );

    $connection->addColumn(
        $installer->getTable('sales/order'),
        'additional_where_heard',
        [
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'    => 255,
            'nullable'  => true,
            'default'   => null,
            'comment'   => 'Where did customer hear about us'
        ]
    );

    $connection->addColumn(
        $installer->getTable('sales/order'),
        'additional_affiliate_name',
        [
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'    => 255,
            'nullable'  => true,
            'default'   => null,
            'comment'   => 'Affiliation name'
        ]
    );

    $installer->endSetup();
} catch (Exception $e) {
    Mage::logException($e);
}