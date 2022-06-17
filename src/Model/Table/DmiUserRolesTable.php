<?php
	namespace app\Model\Table;
	use Cake\ORM\Table;
	use App\Model\Model;
	use Cake\ORM\TableRegistry;

	class DmiUserRolesTable extends Table{

	var $name = "DmiUserRoles";

	public $validate = array(

			'user_email_id'=>array(
					'rule'=>array('maxLength',100),
				),
			'add_user'=>array(
					'rule'=>array('maxLength',10),
				),
			'page_draft'=>array(
					'rule'=>array('maxLength',10),
				),
			'page_publish'=>array(
					'rule'=>array('maxLength',10),
				),
			'menus'=>array(
					'rule'=>array('maxLength',10),
				),
			'file_upload'=>array(
					'rule'=>array('maxLength',10),
				),
			'mo_smo_inspection'=>array(
					'rule'=>array('maxLength',10),
				),
			'io_inspection'=>array(
					'rule'=>array('maxLength',10),
				),
			'allocation_mo_smo'=>array(
					'rule'=>array('maxLength',10),
				),
			'allocation_io'=>array(
					'rule'=>array('maxLength',10),
				),
			'reallocation'=>array(
					'rule'=>array('maxLength',10),
				),
			'form_verification_home'=>array(
					'rule'=>array('maxLength',10),
				),
			'allocation_home'=>array(
					'rule'=>array('maxLength',10),
				),
			'ro_inspection'=>array(
					'rule'=>array('maxLength',10),
				),
			'set_roles'=>array(
					'rule'=>array('maxLength',10),
				),
			'allocation_dy_ama'=>array(
					'rule'=>array('maxLength',10),
				),
			'allocation_ho_mo_smo'=>array(
					'rule'=>array('maxLength',10),
				),
			'allocation_jt_ama'=>array(
					'rule'=>array('maxLength',10),
				),
			'allocation_ama'=>array(
					'rule'=>array('maxLength',10),
				),
			'dy_ama'=>array(
					'rule'=>array('maxLength',10),
				),
			'ho_mo_smo'=>array(
					'rule'=>array('maxLength',10),
				),
			'jt_ama'=>array(
					'rule'=>array('maxLength',10),
				),
			'ama'=>array(
					'rule'=>array('maxLength',10),
				),
			'masters'=>array(
					'rule'=>array('maxLength',10),
				),
			'super_admin'=>array(
					'rule'=>array('maxLength',10),
				),
			'renewal_verification'=>array(
					'rule'=>array('maxLength',10),
				),
			'renewal_allocation'=>array(
					'rule'=>array('maxLength',10),
				),
			'view_reports'=>array(
					'rule'=>array('maxLength',10),
				),
			'pao'=>array(
					'rule'=>array('maxLength',10),
				),
			'sample_inward'=>array(
					'rule'=>array('maxLength',20),
				),
			'sample_forward'=>array(
					'rule'=>array('maxLength',20),
				),
			'generate_inward_letter'=>array(
					'rule'=>array('maxLength',20),
				),
			'sample_allocated'=>array(
					'rule'=>array('maxLength',20),
				),
			'sample_testing_progress'=>array(
					'rule'=>array('maxLength',20),
				),
			'sample_result_approval'=>array(
					'rule'=>array('maxLength',20),
				),
			'finalized_sample'=>array(
					'rule'=>array('maxLength',20),
				),
			'administration'=>array(
					'rule'=>array('maxLength',20),
				),
			'reports'=>array(
					'rule'=>array('maxLength',20),
				),
			'dashboard'=>array(
					'rule'=>array('maxLength',20),
				),
			'dashboard'=>array(
					'rule'=>array('maxLength',20),
				),
			'dashboard'=>array(
					'rule'=>array('maxLength',20),
				),
			'HO'=>array(
					'rule'=>array('maxLength',20),
				),
			'RO'=>array(
					'rule'=>array('maxLength',20),
				),
			'SO'=>array(
					'rule'=>array('maxLength',20),
				),
			'RAL'=>array(
					'rule'=>array('maxLength',20),
				),
			'CAL'=>array(
					'rule'=>array('maxLength',20),
				),
			'user_flag'=>array(
					'rule'=>array('maxLength',20),
				),
			'out_forward'=>array(
					'rule'=>array('maxLength',20),
				),
			'once_update_permission'=>array(
					'rule'=>array('maxLength',10),
				),
			'old_appln_data_entry'=>array(
					'rule'=>array('maxLength',10),
				),
			'so_inspection'=>array(
					'rule'=>array('maxLength',10),
				),
			'smd_inspection'=>array(
					'rule'=>array('maxLength',10),
				),




	);

    //get all the PAO roles user by default
    public function getPaoUserList(){
        return $this->find('all')->select(['user_email_id'])->where(['pao' => 'yes'])->toArray();
    }
	
	
	
	// getHORoles
	// Author : Akash Thakre
	// Description : This function will get the array of DYAMA , JTAMA , AMA name and Email
	// Date : 30-05-2022

	public function getHORoles() {
		
		$dyama = $this->find('all')->select(['user_email_id'])->where(['dy_ama' => 'yes'])->first();
		$jtama = $this->find('all')->select(['user_email_id'])->where(['jt_ama' => 'yes'])->first();
		$ama = $this->find('all')->select(['user_email_id'])->where(['ama' => 'yes'])->first();
		
		return array('dy_ama'=>$dyama['user_email_id'],'jt_ama'=>$jtama['user_email_id'],'ama'=>$ama['user_email_id']);
	}

   


	// getHOScrutinizerForCurrentOffice
	// Author : Akash Thakre
	// Description : This function will get the array of (a) Name of user, (b) Email, for HO scrutinizers officer .
	// Date : 30-05-2022

	public function getHOScrutinizerForCurrentOffice() {

		$ho_scrutinizer_list = array();
		$DmiUsers = TableRegistry::getTableLocator()->get('DmiUsers');
		$DmiUserRoles = TableRegistry::getTableLocator()->get('DmiUserRoles');

		$get_user_details = $this->find('all')->select(['user_email_id'])->where(['ho_mo_smo' => 'yes'])->toArray();

		if(!empty($get_user_details)){   

			$i = 0;	
			foreach($get_user_details as $each){

				$scrutinizers = $DmiUsers->find('all',array('conditions'=>array('email IS'=> $each['user_email_id'],'status !=' =>'disactive')))->first();

				$ho_scrutinizer_list[$i] = array('ho_scrutinizers_name' => $scrutinizers['f_name'].' '.$scrutinizers['l_name'], 'ho_scrutinizers_email' => $scrutinizers['email']);
		
				$i = $i + 1;	
			}
			
		}else{
			
			$ho_scrutinizer_list = array();
		}	
		
		return $ho_scrutinizer_list;
	}
	
	
	
}

?>
