<?php
namespace app\Model\Table;
	use Cake\ORM\Table;
	use App\Model\Model;
	use Cake\ORM\TableRegistry;

class DmiRoOfficesTable extends Table{

	var $name = "DmiRoOffices";

	public $validate = array(

			'ro_office'=>array(
					'rule'=>array('maxLength',200),
					'allowEmpty'=>false,
				),
			'ro_office_address'=>array(
					'rule'=>array('maxLength',200),
					'allowEmpty'=>false,
				),
			'short_code'=>array(
					'rule'=>array('maxLength',10),
					'allowEmpty'=>false,
				),
			'ro_email_id'=>array(
					'rule'=>array('maxLength',200),
					'allowEmpty'=>false,
				),
			'delete_status'=>array(
					'rule'=>array('maxLength',10),
				),
			'user_email_id'=>array(
					'rule'=>array('maxLength',200),
				),
			'ro_office_phone'=>array(
					'rule1'=>array(
							'rule'=>array('lengthBetween', 6, 15),
							'allowEmpty'=>false,
							'last'=>false,
						),
					'rule2'=>array(
							'rule'=>'Numeric',
						)
				),

	);
	
	
	
	// getRoOfficeEmail
	// Author : Akash Thakre
	// Description : This function will get the email by table id
	// Date : 30-05-2022

	public function getRoOfficeEmail($id) {

		$get_user_details = $this->find()->select(['ro_email_id'])->where(['id' => $id])->first();
		$userEmail = $get_user_details['ro_email_id'];
		return $userEmail;
	}

	// getOfficeDetails
	// Author : Akash Thakre
	// Description : This function will get the array of (a) Office Name, (b) Office Type, (c) Incharge Email, (d) RO table ID for SO by Username.
	// Date : 30-05-2022

	public function getOfficeDetails($username) {

		$DmiUsers = TableRegistry::getTableLocator()->get('DmiUsers');

		$getUser = $DmiUsers->find('all')->where(['email' => $username,'status !='=>'disactive'])->first();
		$posted_ro_office = $getUser['posted_ro_office'];

		$officeDetails = $this->find('all',array('conditions'=>array('id IS' => $posted_ro_office, 'OR'=>array('delete_status IS NULL','delete_status'=>'no'))))->first();

		$office_name = $officeDetails['ro_office'];
		$office_type = $officeDetails['office_type'];
		$office_email = $officeDetails['ro_email_id'];

		return array($office_name,$office_type,$office_email,$posted_ro_office);
	}


	public function getJrChemist($username) {

		$posted_ro_office = $this->getOfficeDetails($username);
		$DmiUsers = TableRegistry::getTableLocator()->get('DmiUsers');

		$get_user_details = $DmiUsers->find('all')->where(['posted_ro_office IS' => $posted_ro_office[3],'status !='=>'disactive'])->toArray();

		if(!empty($get_user_details)){

			$i = 0;
			foreach($get_user_details as $each){

				$check_user_role = $DmiUsers->find('all')->where(['email IS'=> $each['email']])->first();

				if(!empty($check_user_role)){

					if($check_user_role['role'] == 'Jr Chemist') {

						$jr_chemist_details = $DmiUsers->find('all',array('conditions'=>array('email IS'=> $each['email'], 'status !=' =>'disactive')))->first();

						$jr_chemist[$i] = array('jr_chemist_name' => $jr_chemist_details['f_name'].' '.$jr_chemist_details['l_name'], 'jr_chemist_email' => $jr_chemist_details['email']);

						$i = $i + 1;
					}
				}
			}

		}else{

			$jr_chemist = array();
		}

		return $jr_chemist;
	}



	public function getSrChemist($username) {

		$posted_ro_office = $this->getOfficeDetails($username);
		$DmiUsers = TableRegistry::getTableLocator()->get('DmiUsers');

		$get_user_details = $DmiUsers->find('all')->where(['posted_ro_office IS' => $posted_ro_office[3],'status !='=>'disactive'])->toArray();

		if(!empty($get_user_details)){

			$i = 0;
			foreach($get_user_details as $each){

				$check_user_role = $DmiUsers->find('all')->where(['email IS'=> $each['email']])->first();

				if(!empty($check_user_role)){

					if($check_user_role['role'] == 'Sr Chemist') {

						$sr_chemist_details = $DmiUsers->find('all',array('conditions'=>array('email IS'=> $each['email'], 'status !=' =>'disactive')))->first();

						$sr_chemist[$i] = array('sr_chemist_name' => $sr_chemist_details['f_name'].' '.$sr_chemist_details['l_name'], 'sr_chemist_email' => $sr_chemist_details['email']);

						$i = $i + 1;
					}
				}
			}

		}else{

			$sr_chemist = array();
		}

		return $sr_chemist;
	}




	public function getInward($username) {

		$posted_ro_office = $this->getOfficeDetails($username);
		$DmiUsers = TableRegistry::getTableLocator()->get('DmiUsers');

		$get_user_details = $DmiUsers->find('all')->where(['posted_ro_office IS' => $posted_ro_office[3],'status !='=>'disactive'])->toArray();

		if(!empty($get_user_details)){

			$i = 0;
			foreach($get_user_details as $each){

				$check_user_role = $DmiUsers->find('all')->where(['email IS'=> $each['email']])->first();

				if(!empty($check_user_role)){

					if($check_user_role['role'] == 'Inward Officer') {

						$inward_details = $DmiUsers->find('all',array('conditions'=>array('email IS'=> $each['email'], 'status !=' =>'disactive')))->first();

						$inward[$i] = array('inward_name' => $inward_details['f_name'].' '.$inward_details['l_name'], 'inward_email' => $inward_details['email']);

						$i = $i + 1;
					}
				}
			}

		}else{

			$inward = array();
		}

		return $inward;
	}


	public function getLabIncharge() {

		$DmiUsers = TableRegistry::getTableLocator()->get('DmiUsers');
		$get_user_details = $DmiUsers->find('all')->where(['role' =>'Lab Incharge','status !='=>'disactive'])->first();
		if(!empty($get_user_details)){

			$lab_inchrage = array('lab_inchrage_name' => $get_user_details['f_name'].' '.$get_user_details['l_name'], 'lab_inchrage_email' => $get_user_details['email']);
		}
		return $lab_inchrage;
	}

	public function getDol() {

		$DmiUsers = TableRegistry::getTableLocator()->get('DmiUsers');
		$get_user_details = $DmiUsers->find('all')->where(['role' =>'DOL','status !='=>'disactive'])->first();
		if(!empty($get_user_details)){

			$dol = array('dol_name' => $get_user_details['f_name'].' '.$get_user_details['l_name'], 'dol_email' => $get_user_details['email']);
		}
		return $dol;
	}




    public function getOfficeDetailsById($id) {

        $officeDetails = $this->find('all',array('conditions'=>array('id IS' => $id, 'OR'=>array('delete_status IS NULL','delete_status'=>'no'))))->first();
        $office_name = $officeDetails['ro_office'];
        $office_type = $officeDetails['office_type'];
        $office_email = $officeDetails['ro_email_id'];
        return array($office_name,$office_type,$office_email);
    }


    //get Lab details
    public function getLabs(){

        return $this->find('list',array('keyField'=>'id','valueField'=>'ro_office','conditions'=>array('office_type'=>'RAL','delete_status IS NULL'),'order'=>'ro_office ASC'))->toArray();
    }

	
	
	// getIoForCurrentOffice
	// Author : Akash Thakre
	// Description : This function will get the array of (a) Name of user, (b) Email, for Inspection officer by Username for current office of that username.
	// Date : 30-05-2022

	public function getIoForCurrentOffice() {

		$io_user_list = array();
		$customer_id='';
		$DmiUsers = TableRegistry::getTableLocator()->get('DmiUsers');
		$DmiAllocations = TableRegistry::getTableLocator()->get('DmiAllocations');

		if (isset($_POST['search_applicant_id'])) {
			$customer_id = $_POST['search_applicant_id'];
		}
		
		if (!empty($customer_id)) {
			
			$getAllocatedUser = $DmiAllocations->getAllocatedIo($customer_id);
			
			if (empty($getAllocatedUser)) {
				$io_user_list = $this->getIoAllocatedUsersDirectly();
			} else {
				$getName = $DmiUsers->getFullName($getAllocatedUser);
				$io_user_list[0] = array('io_name' => $getName, 'io_email' => $getAllocatedUser);
			}

		} else {
	
			$io_user_list = $this->getIoAllocatedUsersDirectly();
		}

		return $io_user_list;
	}



	// getScrutinizerForCurrentOffice
	// Author : Akash Thakre
	// Description : This function will get the array of (a) Name of user, (b) Email, for Inspection officer by Username for current office of that username.
	// Date : 30-05-2022

	public function getScrutinizerForCurrentOffice() {
        
		$scrutinizer_list = array();
		$customer_id='';
		$DmiUsers = TableRegistry::getTableLocator()->get('DmiUsers');
		$DmiAllocations = TableRegistry::getTableLocator()->get('DmiAllocations');
		
		if (isset($_POST['search_applicant_id'])) {
			$customer_id = $_POST['search_applicant_id'];
		}
		
		if (!empty($customer_id)) {
			
			$getAllocatedUser = $DmiAllocations->getAllocatedIo($customer_id);
			
			if (empty($getAllocatedUser)) {
				$scrutinizer_list = $this->getAllocatedScrutinizerDirectly();
			} else {
				$getName = $DmiUsers->getFullName($getAllocatedUser);
				$scrutinizer_list[0] = array('scrutinizers_name' => $getName, 'scrutinizers_email' => $getAllocatedUser);
			}

		

		} else {
			
			$scrutinizer_list = $this->getAllocatedScrutinizerDirectly();
		}

		return $scrutinizer_list;		
	}





	public function getAllocatedScrutinizerDirectly(){

		$scrutinizer_list = array();
		$DmiUsers = TableRegistry::getTableLocator()->get('DmiUsers');
		$DmiUserRoles = TableRegistry::getTableLocator()->get('DmiUserRoles');

		$officeID = $DmiUsers->getPostedOffId($_SESSION['username']);
		$get_user_details = $DmiUsers->find('all')->where(['posted_ro_office IS' => $officeID, 'status !=' => 'disactive'])->toArray();

		if(!empty($get_user_details)){

			$i = 0;
			foreach($get_user_details as $each){

				$check_user_role = $DmiUserRoles->find('all')->select(['user_email_id','mo_smo_inspection'])->where(['user_email_id IS'=> $each['email']])->first();

				if(!empty($check_user_role)){

					if($check_user_role['mo_smo_inspection'] == 'yes') {

						$scrutinizers = $DmiUsers->find('all',array('conditions'=>array('email IS'=> $each['email'], 'status !=' =>'disactive')))->first();

						$scrutinizer_list[$i] = array('scrutinizers_name' => $scrutinizers['f_name'].' '.$scrutinizers['l_name'], 'scrutinizers_email' => $scrutinizers['email']);

						$i = $i + 1;
					}
				}
			}

		}else{

			$scrutinizer_list = array();
		}
	
		return $scrutinizer_list;
	
	}


	public function getIoAllocatedUsersDirectly(){

		$DmiUsers = TableRegistry::getTableLocator()->get('DmiUsers');
		$DmiUserRoles = TableRegistry::getTableLocator()->get('DmiUserRoles');
	
		//get posted ro office id
		$posted_ro_office = $DmiUsers->getPostedOffId($_SESSION['username']);
		
		$get_user_details = $DmiUsers->find('all')->where(['posted_ro_office IS' => $posted_ro_office, 'status !=' => 'disactive'])->toArray();
		
		if(!empty($get_user_details)){

			$i = 0;
			foreach($get_user_details as $each){

				$check_user_role = $DmiUserRoles->find('all')->select(['user_email_id','io_inspection'])->where(['user_email_id IS'=> $each['email']])->first();

				if(!empty($check_user_role)){

					if($check_user_role['io_inspection'] == 'yes') {

						$io_details = $DmiUsers->find('all',array('conditions'=>array('email IS'=> $each['email'], 'status !=' =>'disactive')))->first();

						$io_user_list[$i] = array('io_name' => $io_details['f_name'].' '.$io_details['l_name'], 'io_email' => $io_details['email']);

						$i = $i + 1;
					}
				}
			}

		}else{

			$io_user_list = array();
		}
	
		return $io_user_list;
	}


	
	
	
	
	
	
	
}

?>
