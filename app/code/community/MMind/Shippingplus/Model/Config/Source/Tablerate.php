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
 * @category   model
 * @package    MMind_Shippingplus
 * @copyright  Copyright (c) 2014 MageMind (http://www.magemind.com)
 * @license    http://www.magemind.com/magento-license
 */
class MMind_Shippingplus_Model_Config_Source_Tablerate
{
	public function toOptionArray()
	{
		$tablerate = array(
			array(
				'value' => 'weight_destination',
				'label' => Mage::helper('mmind_shippingplus')->__('Weight vs Destination')
			),
			array(
				'value' => 'price_destination',
				'label' => Mage::helper('mmind_shippingplus')->__('Price vs Destination')
			)
		);

		return $tablerate;
	}
}
