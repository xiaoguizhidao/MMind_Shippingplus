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
class MMind_Shippingplus_Model_Config_Source_Rangeprice
{
	const TYPE_SUBTOTAL = 'subtotal';
	const TYPE_GRANDTOTAL = 'grandtotal';

	public function toOptionArray()
	{
		$rangeprice = array(
			array(
				'value' => self::TYPE_SUBTOTAL,
				'label' => Mage::helper('mmshippingplus')->__('Subtotal')
			),
			array(
				'value' => self::TYPE_GRANDTOTAL,
				'label' => Mage::helper('mmshippingplus')->__('Grand Total')
			)
		);

		return $rangeprice;
	}
}
