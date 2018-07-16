<?php

$installer = $this;

$installer->startSetup();

$table = $this->getTable('sales/quote_address');
$installer->run("ALTER TABLE  $table ADD  `insurance` decimal(12,4) NOT NULL DEFAULT 0");
$installer->run("ALTER TABLE  $table ADD  `base_insurance` decimal(12,4) NOT NULL DEFAULT 0");

$table = $this->getTable('sales/order_address');
$installer->run("ALTER TABLE  $table ADD  `insurance` decimal(12,4) NOT NULL DEFAULT 0");
$installer->run("ALTER TABLE  $table ADD  `base_insurance` decimal(12,4) NOT NULL DEFAULT 0");

$installer->endSetup();