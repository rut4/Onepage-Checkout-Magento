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
 * @copyright  Copyright (C) 2012 Oggetto Web ltd (http://oggettoweb.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

try {
    $installer = $this;
    $installer->startSetup();
    $connection = $installer->getConnection();

    $connection->dropColumn($installer->getTable('sales/order'), 'additional_comment');
    $connection->dropColumn($installer->getTable('sales/order'), 'additional_where_heard');
    $connection->dropColumn($installer->getTable('sales/order'), 'additional_affiliate_name');

    $table = $connection->newTable($installer->getTable('onepageCheckout/customField'))
        ->addColumn('field_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true
        ], 'ID')
        ->addColumn('label', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
            'nullable'  => true,
            'length'    => 255
        ], 'Field Label')
        ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
            'nullable'  => true,
            'length'    => 255
        ], 'Field Value')
        ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
            'unsigned'  => true,
            'nullable'  => false
        ], 'Order ID')
        ->addForeignKey(
            $installer->getFkName('onepageCheckout/customField', 'order_id', 'sales/order', 'entity_id'),
            'order_id',
            $installer->getTable('sales/order'),
            'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->setComment('Custom Field');

    $connection->createTable($table);

    $installer->endSetup();
} catch (Exception $e) {
    Mage::logException($e);
}
