<?php

$installer = $this;

$installer->startSetup();

$quoteAddressTable = $this->getTable('sales/quote_address');
$orderAddressTable = $this->getTable('sales/order_address');

$table = $installer->getConnection()
    ->newTable($installer->getTable('itransition_insurance/insurance'))
    ->addColumn('insurance_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ], 'ID')
    ->addColumn('insurance', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [], 'Insurance')
    ->addColumn('base_insurance', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [], 'Insurance Base currency')
    ->addColumn('quote_address', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true,
        'nullable' => false,
    ], 'Link with quote address')
    ->addColumn('order_address', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true,
        'nullable' => true,
    ], 'Link with order address')
    ->addColumn('created', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'default' =>  Varien_Db_Ddl_Table::TIMESTAMP_INIT,
    ], 'Date')
    ->addIndex($installer->getIdxName('itransition_insurance/insurance', ['quote_address']), ['quote_address'])
    ->addIndex($installer->getIdxName('itransition_insurance/insurance', ['order_address']), ['order_address'])
    ->addIndex(
        $installer->getIdxName('itransition_insurance/insurance', ['insurance_id', 'quote_address']),
        ['insurance_id', 'quote_address'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addIndex(
        $installer->getIdxName('itransition_insurance/insurance', ['insurance_id', 'order_address']),
        ['insurance_id', 'order_address'],
        ['type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE]
    )
    ->addForeignKey($installer->getFkName('itransition_insurance/insurance', 'quote_address', 'sales/quote_address', 'address_id'),
        'quote_address', $quoteAddressTable, 'address_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('itransition_insurance/insurance', 'order_address', 'sales/order_address', 'entity_id'),
        'order_address', $orderAddressTable, 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Link an insurance with a quote and an order');
$installer->getConnection()->createTable($table);

$installer->getConnection()->resetDdlCache();
$installer->endSetup();