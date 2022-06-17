<?php

namespace app\Model\Table;

use Cake\ORM\Table;
use App\Model\Model;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

class MSampleAllocateTable extends Table
{

	var $name = "MSampleAllocate";
	var $useTable = 'm_sample_allocate';

	/*******************************************************************************************************************************************************************************************************************************/
	/***
	 * Made by Shweta Apale 13-11-2021
	 * To get Chemist Code by From date ,To date & Ral Lab
	 *  */
	public function getChemistCodeByDateRalLab($from_date, $to_date, $ral_lab_no)
	{
		$conn = ConnectionManager::get('default');
		if ($_SESSION['role'] == 'Jr Chemist' || $_SESSION['role'] == 'Sr Chemist' || $_SESSION['role'] == 'Cheif Chemist') {

			$chemist_code = "SELECT sa.chemist_code 
							FROM m_sample_allocate AS sa 
							INNER JOIN code_decode AS cd ON cd.org_sample_code = sa.org_sample_code AND date(sa.recby_ch_date) BETWEEN '$from_date' and '$to_date' AND sa.chemist_code!='-' AND cd.status_flag In('C','G') AND sa.lab_code='" . $_SESSION['posted_ro_office'] . "' AND sa.alloc_to_user_code = '" . $_SESSION['user_code'] . "'";
		} else {
			$chemist_code = "SELECT sa.chemist_code 
							FROM m_sample_allocate AS sa 
							INNER JOIN code_decode AS cd ON cd.org_sample_code=sa.org_sample_code AND date(sa.recby_ch_date) BETWEEN '$from_date' AND '$to_date' AND sa.chemist_code!='-' AND cd.status_flag In('C','G') AND sa.lab_code='" . $ral_lab_no . "'";
		}
		$q = $conn->execute($chemist_code);

		$records = $q->fetchAll('assoc');

		$data = array();
		$i = 0;
		foreach ($records as $result) {
			$code = $result['chemist_code'];
			$data[$code] = $result['chemist_code'];
			$i++;
		}
		return $data;
	}

	/*******************************************************************************************************************************************************************************************************************************/
	/***
	 * Made by Shweta Apale 13-11-2021
	 * To get Sample Code by From date ,To date & Chemist Code
	 *  */

	public function getSampleCodeByDateChemist($from_date, $to_date, $chemist_code)
	{
		$conn = ConnectionManager::get('default');

		$q = $conn->execute("SELECT sample_code FROM m_sample_allocate 
							WHERE chemist_code ='$chemist_code' AND date(alloc_date) BETWEEN '$from_date' AND '$to_date' GROUP BY sample_code");
		$records = $q->fetchAll('assoc');

		$data = array();
		$i = 0;
		foreach ($records as $result) {
			$code = $result['sample_code'];
			$data[$code] = $result['sample_code'];
			$i++;
		}
		return $data;
	}
}
