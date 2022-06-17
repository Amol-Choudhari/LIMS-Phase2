<?php

namespace app\Model\Table;

use Cake\ORM\Table;
use App\Model\Model;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

class MCommodityCategoryTable extends Table
{

	var $name = "MCommodityCategory";
	public $useTable = 'm_commodity_category';

	/*******************************************************************************************************************************************************************************************************************************/
	/***
	 * Made by Shweta Apale 22-10-2021
	 * To get Commodity by Category
	 *  */

	public function getCommodityByCategory($category)
	{
		$conn = ConnectionManager::get('default');
		$q = $conn->execute("SELECT mcc.category_code,mc.category_code, mc.commodity_name, mc.commodity_code
		FROM m_commodity_category mcc
		INNER JOIN m_commodity mc ON mcc.category_code = mc.category_code
		WHERE mc.category_code = '$category'");
		$records = $q->fetchAll('assoc');

		$data = array();
		$i = 0;
		foreach ($records as $result) {
			$code = $result['commodity_code'];
			$data[$code] = $result['commodity_name'];
			$i++;
		}

		return $data;
	}

	/*******************************************************************************************************************************************************************************************************************************/
	/***
	 * Made by Shweta Apale 29-10-2021
	 * To get Commodity by Date
	 *  */

	public function getCommodityByDate($from_date,$to_date)
	{
		$conn = ConnectionManager::get('default');
		$q = $conn->execute("SELECT mc.commodity_code, mc.commodity_name FROM m_commodity mc
		INNER JOIN sample_inward si ON mc.commodity_code = si.commodity_code
		INNER JOIN final_test_result ftr ON si.commodity_code = ftr.commodity_code
		WHERE DATE(si.received_date) BETWEEN '$from_date' AND  '$to_date' GROUP BY mc.commodity_name, mc.commodity_code ORDER BY mc.commodity_name");
		$records = $q->fetchAll('assoc');

		$data = array();
		$i = 0;
		foreach ($records as $result) {
			$code = $result['commodity_code'];
			$data[$code] = $result['commodity_name'];
			$i++;
		}
		return $data;
	}
}
