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

$tableName = $this->getTable('mmind_shippingplus/shippingplus');

/**
 * Tablerate weight vs destination or price vs destination
 */
$installer->run("
CREATE TABLE IF NOT EXISTS $tableName (
  `mmind_shippingplus_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `dest_country` varchar(5) NOT NULL,
  `dest_region` varchar(5) NOT NULL,
  `dest_zip` varchar(10) NOT NULL,
  `weight` decimal(12,4) NOT NULL,
  `price` decimal(12,4) NOT NULL,
  `charge` decimal(12,4) NOT NULL,
  `type` varchar(150) NOT NULL,
  PRIMARY KEY (`mmind_shippingplus_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 ;
");

$installer->endSetup();
