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
class MMind_Shippingplus_Model_Mysql4_Shippingplus_Import extends Mage_Core_Model_Resource_Db_Abstract
{
	/**
	 * Number of coloumns in tablerate file
	 *
	 * @var int
	 */
	protected $tablerate_cols = 7;

	/**
	 * Import table rates website ID
	 *
	 * @var int
	 */
	protected $_importWebsiteId = 0;

	/**
	 * Errors in import process
	 *
	 * @var array
	 */
	protected $_importErrors = array();

	/**
	 * Define main table and id field name
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('mmind_shippingplus/shippingplus', 'mmind_shippingplus_id');
	}

	/**
	 * Upload CSV file of Tablerate
	 *
	 * @see MMind_Shippingplus_Model_Config_Shippingplus._afterSave()
	 * @param Varien_Object $object
	 * @throws Mage_Core_Exception
	 * @return MMind_Shippingplus_Model_Mysql4_Shippingplus_Import
	 ***/
	public function uploadAndImport(Varien_Object $object)
	{
		if (empty($_FILES['groups']['tmp_name']['mmind_shippingplus']['fields']['import_tablerate']['value']))
			return $this;

		$csvFile = $_FILES['groups']['tmp_name']['mmind_shippingplus']['fields']['import_tablerate']['value'];
		$website = Mage::app()->getWebsite($object->getScopeId());

		$this->_importWebsiteId = (int)$website->getId();

		$io = new Varien_Io_File();
		$info = pathinfo($csvFile);
		$io->open(array('path' => $info['dirname']));
		$io->streamOpen($info['basename'], 'r');

		// check and skip headers
		$headers = $io->streamReadCsv();
		if ($headers === false || count($headers) < $this->tablerate_cols) {
			$io->streamClose();
			Mage::throwException(Mage::helper('mmind_shippingplus')->__('Invalid CSV Table Rates File Format'));
		}

		$adapter = $this->_getWriteAdapter();
		$adapter->beginTransaction();

		try {
			$rowNumber = 1;
			$importData = array();

			// delete old data by website and condition name
			$condition = array('website_id = ?' => $this->_importWebsiteId,);
			$adapter->delete($this->getMainTable(), $condition);

			while (false !== ($csvLine = $io->streamReadCsv())) {
				$rowNumber++;

				if (empty($csvLine)) {
					continue;
				}

				$row = $this->_getImportRow($csvLine, $rowNumber);

				if ($row !== false) {
					// Import with Multi country
					$countries = explode("|", $row[1]);
					foreach ($countries as $country) {
						$row[1] = $country;
						$importData[] = $row;
					}
				}

				if (count($importData) == 5000) {
					$this->_saveImportData($importData);
					$importData = array();
				}

			}
			$this->_saveImportData($importData);
			$io->streamClose();
		} catch (Mage_Core_Exception $e) {
			$io->streamClose();
			Mage::throwException($e->getMessage());
		} catch (Exception $e) {
			$io->streamClose();
			Mage::logException($e);
			Mage::throwException(Mage::helper('mmind_shippingplus')->__('An error occurred while import CSV Table rates.'));
		}

		$adapter->commit();

		if ($this->_importErrors) {
			$error = Mage::helper('mmind_shippingplus')->__('%1$d records have been imported. See the following list of errors for each record that has not been imported: %2$s', $this->_importedRows, implode(" \n", $this->_importErrors));
			Mage::throwException($error);
		}

		return $this;

	}

	/**
	 * Validate row for import and return table rate array or false
	 * Error will be add to _importErrors array
	 *
	 * @param array $row
	 * @param int $rowNumber
	 * @return array|false
	 */
	protected function _getImportRow($row, $rowNumber = 0)
	{
		// validate row
		if (count($row) < $this->tablerate_cols) {
			$this->_importErrors[] = Mage::helper('mmind_shippingplus')->__('Invalid CSV Table Rates format in the Row #%s', $rowNumber);
			return false;
		}

		// strip whitespace from the beginning and end of each row
		foreach ($row as $k => $v) {
			$row[$k] = trim($v);
		}

		return array(
			$this->_importWebsiteId,
			$row[0],
			$row[1],
			$row[2],
			$row[3],
			str_replace(',', '.', $row[4]),
			$row[5],
			$row[6]
		);
	}

	/**
	 * Save import data batch
	 *
	 * @param array $data
	 * @return MMind_Shippingplus_Model_Mysql4_Shippingplus_Import
	 */
	protected function _saveImportData(array $data)
	{
		if (!empty($data)) {
			$columns = array(
				'website_id',
				'dest_country',
				'dest_region',
				'dest_zip',
				'weight',
				'price',
				'charge',
				'type'
			);
			$this->_getWriteAdapter()->insertArray($this->getMainTable(), $columns, $data);
			$this->_importedRows += count($data);
		}
		return $this;
	}
}
