<?php

namespace app\Model\Table;

use Cake\ORM\Table;
use App\Model\Model;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

class MCommodityCategoryTable extends Table
{

	var string $name = "MCommodityCategory";
	public string $useTable = 'm_commodity_category';

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


    // getCategory
    // Description : This function will return the category name by id.
    // Author : Akash Thakre
    // Date : 03-06-2022

    public function getCategory($id) {
		
		if (!empty($id)) {
			$getCategory = $this->find('all')->select(['category_name'])->where(['category_code' => $id])->first();
			$detail = $getCategory['category_name'];
		} else {
			$detail = '';
		}

        return $detail;
    }

}
