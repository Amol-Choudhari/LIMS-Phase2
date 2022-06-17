<?php

namespace app\Model\Table;

use Cake\ORM\Table;
use App\Model\Model;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;

class DmiUsersTable extends Table
{

	var $name = "DmiUsers";
	//var $uses = array('Dmi_ro_office','Dmi_user_role','Dmi_allocation','Dmi_renewal_allocation');

	public $validate = array(

		'f_name' => array(
			'rule' => array('maxLength', 100),
			'allowEmpty' => false,
		),
		'l_name' => array(
			'rule' => array('maxLength', 100),
			'allowEmpty' => false,
		),
		'email' => array(
			'rule' => array('maxLength', 200),
			'allowEmpty' => false,
		),
		'division' => array(
			'rule' => array('maxLength', 100),
			'allowEmpty' => false,
		),
		'phone' => array(
			'rule' => 'Numeric',
			'allowEmpty' => false,
		),
		'role' => array(
			'rule' => array('maxLength', 100)
		),
		'once_card_no' => array(
			'rule' => array('maxLength', 50)
		),
		'posted_ro_office' => array(
			'rule' => 'Numeric',
			'allowEmpty' => true, //changed to true on 14-08-2018
		),
		'landline' => array(
			'rule' => array('maxLength', 15)
		),
		//below 2 added on 14-08-2018
		'posted_so_office' => array(
			'rule' => array('maxLength', 10)
		),
		'posted_smd_office' => array(
			'rule' => array('maxLength', 10)
		),
	);

	//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>//

	public function findMoList($current_action)
	{

		$username = $_SESSION['username'];
		$mo_user_list = array();

		//below code added on 08-09-2018 by Amol
		//calling global varaible for roles
		$current_user_roles = Configure::read('current_user_roles');
		//adding role wise table modals for RO/SO/SMD
		if (
			$current_user_roles[0]['ro_inspection'] == 'yes' ||
			$current_user_roles[0]['so_inspection'] == 'yes'
		) { //updated 15-03-2019, common table for RO/SO office

			$Dmi_allocation = TableRegistry::getTableLocator()->get('DmiAllocations');
			$allocation_modal = 'DmiAllocations';
			$Dmi_office = TableRegistry::getTableLocator()->get('DmiRoOffices');
			$Dmi_office_model = 'DmiRoOffices';
			$ofsc_email_condtn = array('ro_email_id' => $username);

			if ($current_action == 'renewal_home') {
				$Dmi_allocation = TableRegistry::getTableLocator()->get('DmiRenewalAllocations');
				$allocation_modal = 'DmiRenewalAllocations';
			}
		}
		//till here

		// Import another model in this model							
		$Dmi_user_role = TableRegistry::getTableLocator()->get('DmiUserRoles');


		$find_ro_id = $Dmi_office->find('all', array('fields' => 'id', 'conditions' => $ofsc_email_condtn))->first();

		if (!empty($find_ro_id)) {
			$ro_id = $find_ro_id[$Dmi_office_model]['id'];
			//$find_user_belongs = $this->find('all',array('conditions'=>array('posted_ro_office'=>$ro_id)));


			// Change logic for Display Multiple MO's under each RO by posted office to allocated MO's under each RO on dashborad
			// Now Display all MO/SMO's list in allocation mo_list dropdown and Display the only allocated MO's under each RO on dashborad
			//Done By pravin 15-09-2017

			//	$table = 'Dmi_allocation';	
			$find_user_belongs = $Dmi_allocation->find('all', array('fields' => 'level_1', 'conditions' => array('level_3 IS' => $username, 'level_1 !=' => null), 'group' => 'level_1'))->toArray();

			/*	if($current_action == 'renewal_home')
				
			{	
				$table = 'Dmi_renewal_allocation';
				$find_user_belongs = $Dmi_renewal_allocation->find('all',array('fields'=>'level_1','conditions'=>array('level_3'=>$username,'level_1 !='=>null),'group'=>'level_1'));
			}
		*/

			if (!empty($find_user_belongs)) {
				$mo = 0;
				foreach ($find_user_belongs as $check_role) {

					//$check_user_role = $Dmi_user_role->find('first',array('conditions'=>array('user_email_id'=>$check_role['Dmi_user']['email'])));
					$check_user_role = $Dmi_user_role->find('all', array('conditions' => array('user_email_id IS' => $check_role['level_1'])))->first();

					if (!empty($check_user_role)) // line applied on 30-03-2017 by Amol
					{
						if ($check_user_role['mo_smo_inspection'] == 'yes') {
							$mo_details = $this->find('all', array('conditions' => array('email IS' => $check_role['level_1'])))->first();

							//below line changed on 01-04-2017 by Amol(to show name of MO)
							//$mo_user_list[$mo] = $check_role['Dmi_user']['email'].','.$check_role['Dmi_user']['f_name'].','.$check_role['Dmi_user']['l_name'].','.$check_role['Dmi_user']['id'];
							$mo_user_list[$mo] = $mo_details['email'] . ',' . $mo_details['f_name'] . ',' . $mo_details['l_name'] . ',' . $mo_details['id'];

							$mo = $mo + 1;
						}
					}
				}
			} else {

				$mo_user_list = array();
			}
		}

		return $mo_user_list;
	}



	//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>//		


	public function findIoList($current_action)
	{ //added this "$current_action" argument on 10-07-2018 by Amol

		$username = $_SESSION['username'];
		$io_user_list = array();

		//below code added on 08-09-2018 by Amol
		//calling global varaible for roles
		$current_user_roles = Configure::read('current_user_roles');
		//adding role wise table modals for RO/SO/SMD
		if (
			$current_user_roles[0]['ro_inspection'] == 'yes' ||
			$current_user_roles[0]['so_inspection'] == 'yes'
		) { //updated 15-03-2019, common table for RO/SO office

			$Dmi_allocation = TableRegistry::getTableLocator()->get('DmiAllocations');
			$allocation_modal = 'DmiAllocations';
			$Dmi_office = TableRegistry::getTableLocator()->get('DmiRoOffices');
			$Dmi_office_model = 'DmiRoOffices';
			$ofsc_email_condtn = array('ro_email_id' => $username);

			if ($current_action == 'renewal_home') {
				$Dmi_allocation = TableRegistry::getTableLocator()->get('DmiRenewalAllocations');
				$allocation_modal = 'DmiRenewalAllocations';
			}
		}
		//till here

		// Import another model in this model							
		$Dmi_user_role = TableRegistry::getTableLocator()->get('DmiUserRoles');

		//changes query logic from "first" to "list" while getting multiple RO ids, on 13-02-2018 by Amol
		$find_ro_id = $Dmi_office->find('list', array('fields' => 'id', 'conditions' => $ofsc_email_condtn))->toArray();

		if (!empty($find_ro_id)) {
			$ro_id = $find_ro_id; //providing multiple ro ids array, on 13-02-2018 by Amol

			//$find_user_belongs = $this->find('all',array('conditions'=>array('posted_ro_office'=>$ro_id)));
			// Change logic for Display Multiple IO's under each RO by posted office to allocated IO's under each RO on dashborad
			// Now Display all IO's list in allocation IO_list dropdown and Display the only allocated IO's under each RO on dashborad
			//Done By pravin 03/03/2018	

			//	$table = 'Dmi_allocation';
			$find_user_belongs = $Dmi_allocation->find('all', array('fields' => 'level_2', 'conditions' => array('level_3 IS' => $username, 'level_2 !=' => null), 'group' => 'level_2'))->toArray();


			/*	if($current_action == 'renewal_home')					
			{	
				$table = 'Dmi_renewal_allocation';
				$find_user_belongs = $Dmi_renewal_allocation->find('all',array('fields'=>'level_2','conditions'=>array('level_3'=>$username,'level_2 !='=>null),'group'=>'level_2'));
			}
		*/

			$io = 0;
			foreach ($find_user_belongs as $check_role) {

				//$check_user_role = $Dmi_user_role->find('first',array('conditions'=>array('user_email_id'=>$check_role['Dmi_user']['email'])));
				$check_user_role = $Dmi_user_role->find('all', array('conditions' => array('user_email_id IS' => $check_role['level_2'])))->first();

				if (!empty($check_user_role)) // line applied on 30-03-2017 by Amol
				{
					if ($check_user_role['io_inspection'] == 'yes') //line changed on 29-03-2017 by Amol
					{

						$IO_details = $this->find('all', array('conditions' => array('email' => $check_role['level_2'])))->toArray();

						//below line changed on 01-04-2017 by Amol(to show name of MO) //added concatination of id on 02-06-2017 to use in multiple io listing
						$io_user_list[$io] = $IO_details['email'] . ',' . $IO_details['f_name'] . ',' . $IO_details['l_name'] . ',' . $IO_details['id'];
						$io = $io + 1;
					}
				}
			}
		}

		return $io_user_list;
	}
	/*******************************************************************************************************************************************************************************************************************************/
	/***
	 * Made by Shweta Apale 10-11-2021
	 * To get Ral lab by Lab
	 *  */

	public function getRallabByLab($lab)
	{
		$conn = ConnectionManager::get('default');


		if ($lab == $_SESSION['user_flag']) {
			$ralLab = "SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab,CONCAT(o.id,'~', r.user_flag) AS ral_lab_key from dmi_users as u 
					Inner Join dmi_ro_offices as o On u.posted_ro_office = o.id
					Inner Join dmi_user_roles as r on r.user_email_id = u.email
					and r.user_flag = '" . $_SESSION['user_flag'] . "' AND u.posted_ro_office = '" . $_SESSION['posted_ro_office'] . "'
					WHERE u.status = 'active'
					GROUP BY r.user_flag,o.id,o.ro_office order by o.id asc";
		} else {
			$ralLab = "SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab,CONCAT(o.id,'~', r.user_flag) AS ral_lab_key from dmi_users as u 
					Inner Join dmi_ro_offices as o On u.posted_ro_office=o.id
					Inner Join dmi_user_roles as r on r.user_email_id=u.email
					and r.user_flag='$lab'
					where u.status = 'active'
					group by r.user_flag,o.id,o.ro_office order by o.id asc";
		}

		//  pr($users);exit;
		$q = $conn->execute($ralLab);

		$records = $q->fetchAll('assoc');

		$data = array();
		$i = 0;
		foreach ($records as $result) {
			$code = $result['ral_lab_key'];
			$data[$code] = $result['ral_lab'];
			$i++;
		}
		return $data;
	}

	/*******************************************************************************************************************************************************************************************************************************/
	/***
	 * Made by Shweta Apale 18-11-2021
	 * To get Users by Ral Lab
	 *  */

	public function getUserByRalLab($lab, $ral_lab_no)
	{
		$conn = ConnectionManager::get('default');

		$users = "SELECT du.id,CONCAT(du.f_name,' ',du.l_name) AS chemist_name 
				FROM dmi_users AS du 
				INNER JOIN dmi_user_roles AS dur ON dur.user_email_id=du.email
				INNER JOIN dmi_ro_offices AS o ON o.id=du.posted_ro_office AND
				dur.user_flag='$lab' AND o.id='$ral_lab_no' AND du.role IN('Jr Chemist','Sr Chemist','Cheif Chemist')
				WHERE du.status !='disactive'
				GROUP BY du.id,du.f_name,du.l_name ORDER BY du.f_name ASC ";

		//  pr($users);exit;
		$q = $conn->execute($users);

		$records = $q->fetchAll('assoc');

		$data = array();
		$i = 0;
		foreach ($records as $result) {
			$code = $result['id'];
			$data[$code] = $result['chemist_name'];
			$i++;
		}
		return $data;
	}



	// Get the user posted office id
	// Done by Pravin Bhakare 11-10-2021
	public function getPostedOffId($userid){

		$result = $this->find('all',array('fields'=>array('posted_ro_office'),'conditions'=>array('email IS'=>$userid)))->first();
		return $result['posted_ro_office'];
	}


	//Get User Name
		// Author : Akash Thakre
		// Description : This funtions takes email (encoded) and returns the name of that user
		// Date : 31-05-2022

		public function getFullName($username) {

			$getName = $this->find('all')->where(['email' => $username, 'status !=' => 'disactive'])->first();
			$fullname = $getName['f_name']. " ".$getName['l_name'];
			return $fullname;
		}
}
