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
class MMind_Shippingplus_Model_Config_Source_Charge
{
	const TYPE_CHARGE_FIX = 'fix';
	const TYPE_CHARGE_PERCENTAGE = 'percentage';

	public function toOptionArray()
	{
		$charge = array(
			array(
				'value' => self::TYPE_CHARGE_FIX,
				'label' => Mage::helper('mmind_shippingplus')->__('Fix Charge')
			),
			array(
				'value' => self::TYPE_CHARGE_PERCENTAGE,
				'label' => Mage::helper('mmind_shippingplus')->__('Percentage Charge')
			)
		);

		return $charge;
	}
}
