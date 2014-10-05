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
class MMind_Shippingplus_Model_Config_Shippingplus extends Mage_Core_Model_Config_Data
{
	public function _afterSave()
	{
		Mage::getResourceModel('mmind_shippingplus/shippingplus_import')->uploadAndImport($this);
	}
}
