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
class MMind_Shippingplus_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_TYPE_RANGEPRICE = 'carriers/mmind_shippingplus/ship_price_rangeprice';

	/**
	 * Get customer's country code from shipping address
	 *
	 * Check order priority:
	 * 1) logged customer
	 * 2) guest customer
	 * 3) shop country code -> if customer is not logged or in shopping cart with
	 * shipping not set
	 *
	 * @param $iso          int type of ISO code
	 * @return string       ISO 3 char country code
	 */
	public function getCustomerCountryCode($iso = 3)
	{
		$customer = Mage::helper('customer')->getCustomer();

		if ($customer && $customer->getShippingAddress() && $customer->getShippingAddress()->getId()) {
			// User logged
			$countryCode = $customer->getShippingAddress()->getCountry();
		} else {
			$quote = Mage::getSingleton('checkout/cart')->getQuote();
			// Guest user
			$countryCode = $quote->getShippingAddress()->getCountry();

			if (empty($countryCode)) {
				// Guest user with no shipping address choosen
				$countryCode = Mage::getStoreConfig('general/country/default');
			}
		}

		// ISO3 country code
		if ($iso == 3)
			$countryCode = Mage::getModel('directory/country')->load($countryCode)->getIso3Code();

		return $countryCode;
	}

	/**
	 * Get customer's postcode from shipping address
	 *
	 * Check order priority:
	 * 1) logged customer
	 * 2) guest customer
	 *
	 * @return string
	 */
	public function getCustomerPostcode()
	{
		$customer = Mage::helper('customer')->getCustomer();

		if ($customer && $customer->getShippingAddress() && $customer->getShippingAddress()->getId()) {
			// User logged
			$postcode = $customer->getShippingAddress()->getPostcode();
		} else {
			$quote = Mage::getSingleton('checkout/cart')->getQuote();
			// Guest user
			$postcode = $quote->getShippingAddress()->getPostcode();
		}

		return $postcode;
	}

	/**
	 * Get customer's region code from shipping address
	 *
	 * Check order priority:
	 * 1) logged customer
	 * 2) guest customer
	 *
	 * @return string       2 char region code
	 */
	public function getCustomerRegionCode()
	{
		$customer = Mage::helper('customer')->getCustomer();

		if ($customer && $customer->getShippingAddress() && $customer->getShippingAddress()->getId()) {
			// User logged
			$regionCode = $customer->getShippingAddress()->getRegionCode();
		} else {
			$quote = Mage::getSingleton('checkout/cart')->getQuote();
			// Guest user
			$regionCode = $quote->getShippingAddress()->getRegionCode();
		}

		return $regionCode;
	}

	/**
	 * Get the Order Subtotal or GrandTotal
	 *
	 * @param string
	 * @return float
	 */
	public function getOrderAmount($_rangeprice = null)
	{
        $quote = Mage::getModel('checkout/session')->getQuote();
        $quoteData = $quote->getData();

        // Subtotal or grand total
        switch($this->getTypeRangePrice($_rangeprice)){
            case ISM_Ismshipping_Model_Config_Source_Rangeprice::TYPE_SUBTOTAL:
                return $quoteData['subtotal'];
                break;
            case ISM_Ismshipping_Model_Config_Source_Rangeprice::TYPE_GRANDTOTAL:
                return $quoteData['subtotal_with_discount'];
                break;
            default:
                return $quoteData['subtotal'];
        }
	}

	/**
	 * Get the type of RangePrice to select (grandtotal or subtotal)
	 *
	 * @param string
	 * @return string
	 */
	public function getTypeRangePrice($_rangeprice = null)
	{
		return is_null($_rangeprice) ? Mage::getStoreConfig(self::XML_PATH_TYPE_RANGEPRICE) : $_rangeprice;
	}

}
