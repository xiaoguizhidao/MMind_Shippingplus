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
class MMind_Shippingplus_Model_Carrier_Shippingplus extends Mage_Shipping_Model_Carrier_Abstract
{
	protected $_code = 'mmshippingplus';
	protected $_default_condition_name = 'weight_destination';
	protected $_free_ship_tablerate = -1;

	/**
	 * Check if carrier has shipping label option available
	 *
	 * @return boolean
	 */
	public function isShippingLabelsAvailable()
	{
		return false;
	}

	/**
	 * Collect rate to get shipping method
	 *
	 * @param Mage_Shipping_Model_Rate_Request $request
	 * @return Mage_Shipping_Model_Rate_Request $request
	 */
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
		$result = "";
		$ship_price = 0;

		if (!$this->getConfigFlag('active'))
			return false;

		$website_id = (int)$request->getWebsiteId();

		// Default condition Name: Weight vs. Destination
		$weight = $request->getPackageWeight();

		// Check Weight Limit
		if ($this->getConfigFlag('active_weight_limit') && $weight >= $this->getConfigData('weight_limit'))
			return false;

		// Condition Name: Price Vs. Destination
		if ($this->getConfigData('condition_name') != $this->_default_condition_name)
			// The weight is now the price
			$weight = Mage::helper('mmshippingplus')->getOrderAmount();

		// Get country, region and postcode data
		$country = Mage::helper('mmshippingplus')->getCustomerCountryCode();
		$region = Mage::helper('mmshippingplus')->getCustomerRegionCode();
		$postcode = Mage::helper('mmshippingplus')->getCustomerPostcode();

		// Free shipping by qty
		$freeQty = 0;
		if ($request->getAllItems()) {
			foreach ($request->getAllItems() as $item) {
				if ($item->getProduct()->isVirtual() || $item->getParentItem())
					continue;

				if ($item->getHasChildren() && $item->isShipSeparately()) {
					foreach ($item->getChildren() as $child) {
						if ($child->getFreeShipping() && !$child->getProduct()->isVirtual())
							$freeQty += $item->getQty() * ($child->getQty() - (is_numeric($child->getFreeShipping()) ? $child->getFreeShipping() : 0));
					}
				} elseif ($item->getFreeShipping()) {
					$freeQty += ($item->getQty() - (is_numeric($item->getFreeShipping()) ? $item->getFreeShipping() : 0));
				}
			}
		}

		if (!$request->getConditionName()) {
			$request->setConditionName($this->getConfigData('condition_name') ? $this->getConfigData('condition_name') : $this->_default_condition_name);
		}

		// Check tablerate with condition
		$tablerate = Mage::getModel('mmshippingplus/shippingplus')->getCollection()->setOrder('weight', 'DESC')->addFieldToFilter('website_id', array('in' => $website_id))->addFieldToFilter('dest_country', array('in' => array(
			'*',
			$country
		)))->addFieldToFilter('dest_zip', array('in' => array(
			'*',
			$postcode
		)))->addFieldToFilter('dest_region', array('in' => array(
			'*',
			$region
		)))->addFieldToFilter('weight', array('lteq' => $weight))->addFieldToFilter('type', array('eq' => $this->getConfigData('condition_name')));

		// Tablerate price
		$ship_price = $tablerate->getFirstItem()->getPrice();

		// Price with shipping weight range
		if ($this->getConfigFlag('active_ship_kg')) {
			if ($this->getConfigData('ship_kg_country'))
				$kg_country = explode(',', $this->getConfigData('ship_kg_country'));

			$country = Mage::helper('mmshippingplus')->getCustomerCountryCode(2);

			if (in_array($country, $kg_country)) {
				if ($weight >= $this->getConfigData('ship_from_kg') && $weight <= $this->getConfigData('ship_to_kg'))
					$ship_price = $this->getConfigData('ship_kg_price');
			}
		}

		// Price with shipping price range
		if ($this->getConfigFlag('active_ship_price')) {
			if ($this->getConfigData('ship_price_country'))
				$price_country = explode(',', $this->getConfigData('ship_price_country'));

			$country = Mage::helper('mmshippingplus')->getCustomerCountryCode(2);

			if (in_array($country, $price_country)) {
				$amount = Mage::helper('mmshippingplus')->getOrderAmount();

				if ($amount >= $this->getConfigData('ship_from_price') && $amount <= $this->getConfigData('ship_to_price'))
					$ship_price = $this->getConfigData('ship_price_price');
			}

		}

		if (!is_null($ship_price) && $ship_price != 0) {
			// Free shipping by tablerate
			$ship_price = $ship_price == $this->_free_ship_tablerate ? 0 : $ship_price;

			// Check if price has charge
			$charge = $tablerate->getFirstItem()->getCharge();

			if ($charge > 0) {
				$amount = Mage::helper('mmshippingplus')->getOrderAmount(MMind_Shippingplus_Model_Config_Source_Rangeprice::TYPE_SUBTOTAL);
				// Charge type
				if ($this->getConfigData('charge_type') == MMind_Shippingplus_Model_Config_Source_Charge::TYPE_CHARGE_FIX) {
					// Fix price
					$ship_price += $charge;
				} else {
					// Percentage price
					$ship_price += ($amount * $charge) / 100;
				}
			}

			// Package weight and qty free shipping
			$oldWeight = $request->getPackageWeight();
			$oldQty = $request->getPackageQty();

			$request->setPackageWeight($request->getFreeMethodWeight());
			$request->setPackageQty($oldQty - $freeQty);

			$result = Mage::getModel('shipping/rate_result');

			$request->setPackageWeight($oldWeight);
			$request->setPackageQty($oldQty);

			$method = Mage::getModel('shipping/rate_result_method');

			$method->setCarrier($this->_code);
			$method->setCarrierTitle($this->getConfigData('title'));

			$method->setMethod($this->_code);
			$method->setMethodTitle($this->getConfigData('method_name'));

			if ($request->getFreeShipping() === true || ($request->getPackageQty() == $freeQty))
				$ship_price = 0;

			$method->setPrice($ship_price);
			$method->setCost(0);

			$result->append($method);

			return $result;
		} else {
			// View method also with zero price
			if ($this->getConfigData('active_shipping_zeroprice')) {
				// Package weight and qty free shipping
				$oldWeight = $request->getPackageWeight();
				$oldQty = $request->getPackageQty();

				$request->setPackageWeight($request->getFreeMethodWeight());
				$request->setPackageQty($oldQty - $freeQty);

				$result = Mage::getModel('shipping/rate_result');

				$request->setPackageWeight($oldWeight);
				$request->setPackageQty($oldQty);

				$method = Mage::getModel('shipping/rate_result_method');

				$method->setCarrier($this->_code);
				$method->setCarrierTitle($this->getConfigData('title'));

				$method->setMethod($this->_code);
				$method->setMethodTitle($this->getConfigData('method_name'));

				$ship_price = 0;

				$method->setPrice($ship_price);
				$method->setCost(0);

				$result->append($method);

				return $result;
			}
		}
	}

	public function getAllowedMethods()
	{
		return array($this->_code => $this->getConfigData('method_name'));
	}
}
