<?php

/**
 * MageMind
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magemind.com/magento-license
 *
 * @category   sql
 * @package    MMind_Shippingplus
 * @copyright  Copyright (c) 2014 MageMind (http://www.magemind.com)
 * @license    http://www.magemind.com/magento-license
 */

$installer = $this;
$installer->startSetup();

/**
 * Tablerate weight vs destination or price vs destination
 */

$table = $installer->getConnection()
    ->newTable($installer->getTable('mmshippingplus/shippingplus'))
    ->addColumn('mmshippingplus_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11,
        array(
            'nullable' => false
        ), 'Website ID')
    ->addColumn('dest_country', Varien_Db_Ddl_Table::TYPE_VARCHAR, 5,
        array(
            'nullable' => false
        ), 'Destination Country')
    ->addColumn('dest_region', Varien_Db_Ddl_Table::TYPE_VARCHAR, 5,
        array(
            'nullable' => false
        ), 'Destination Region')
    ->addColumn('dest_zip', Varien_Db_Ddl_Table::TYPE_VARCHAR, 10,
        array(
            'nullable' => false
        ), 'Destination ZIP Code')
    ->addColumn('weight', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4',
        array(
            'nullable' => false
        ), 'Order Weight')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4',
        array(
            'nullable' => false
        ), 'Price')
    ->addColumn('charge', Varien_Db_Ddl_Table::TYPE_VARCHAR, '12,4',
        array(
            'nullable' => false
        ), 'Charge Fee on shipping price')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 150,
        array(
            'nullable' => false
        ), 'Type of shipping method')
    ->setComment('MMind Shippingplus Tablerate');

$installer->getConnection()->createTable($table);

$installer->endSetup();
