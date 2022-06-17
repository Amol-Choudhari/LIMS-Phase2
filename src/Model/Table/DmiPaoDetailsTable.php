<?php

namespace app\Model\Table;
use Cake\ORM\Table;
use App\Model\Model;
use Cake\ORM\TableRegistry;

class DmiPaoDetailsTable extends Table{

	var string $name = "DmiPaoDetails";

    //getPaoDetails
    public function getPaoDetails($tableId) {

        $details = $this->find('all')->where(['id IS' => $tableId])->first();
        if (!empty($details)) {
            return $details;
        }

    }

    //getPaoDetails
    public function getPaoDetailsbyPaoId($pao_id) {

        $details = $this->find('all')->select(['id'])->where(['pao_user_id IS' => $pao_id])->first();
        if (!empty($details)) {
            return $details['id'];
        }

    }




	public function getAllDdoList(){

		$pao_name_list = array();
		$DmiPaoDetails = TableRegistry::getTableLocator()->get('DmiPaoDetails');
		$DmiUsers = TableRegistry::getTableLocator()->get('DmiUsers');
		$DmiUserRoles = TableRegistry::getTableLocator()->get('DmiUserRoles');

		// check activated user condition to make pao user list
	   /* $pao_user_id_list = $this->find('all',array('joins'=>array(
			array('table' => 'dmi_users','alias' => 'users','type' => 'INNER','conditions' => array( 'Dmi_pao_detail.pao_user_id::integer = users.id','users.status !='=>'disactive')),
			array('table' => 'dmi_user_roles','alias' => 'u_roles','type' => 'INNER','conditions' => array( 'users.email = u_roles.user_email_id', 'u_roles.pao'=>'yes'))),
			'fields'=>array('id','pao_user_id'),'order'=>'id asc','conditions'=>array()))->toArray();
		*/
		$pao_user_id_list = $DmiUserRoles->getPaoUserList();


		if (!empty($pao_user_id_list)) {

		   $j=0;

			foreach ($pao_user_id_list as $pao_user_id) {

				$user_details = $DmiUsers->find('all',array('conditions'=>array('email IS'=>$pao_user_id['user_email_id'],'status'=>'active')))->toArray();//added status cond on 05-01-2022

				//Check user id
				if (!empty($user_details)) {

					$pao_id_list[$j] = $pao_user_id['id'];

					foreach ($user_details as $user_detail) {

						$user_full_name = $user_detail['f_name'].' '.$user_detail['l_name'];
						$pao_name_list[$user_detail['id']] = $user_full_name    .   '   ('.base64_decode($user_detail['email']).')'; //for email encoding
					}

					$j=$j+1;
				}
			}
		}

		return $pao_name_list;


	}


	
	
	
	public function getPaoDetailsForDmi($username) {
		
		$DmiDistricts = TableRegistry::getTableLocator()->get('DmiDistricts');
		$DmiRoOffices = TableRegistry::getTableLocator()->get('DmiRoOffices');
		$DmiUsers = TableRegistry::getTableLocator()->get('DmiUsers');
		
		$posted_ro_office = $DmiUsers->find('all')->select(['posted_ro_office'])->where(['email' => $username,'status'=>'active'])->first();
		$office_type = $DmiRoOffices->find()->select(['office_type'])->where(['id IS' => $posted_ro_office['posted_ro_office']])->first();
	
		if ($office_type['office_type'] == 'SO') {
			$getPaoIds = $DmiDistricts->find('all')->select(['pao_id','pao_id'])->where(['so_id IS' => $posted_ro_office['posted_ro_office']])->group('pao_id')->combine('pao_id','pao_id')->toArray();
		} else {
			$getPaoIds = $DmiDistricts->find('all')->select(['pao_id','pao_id'])->where(['ro_id IS' => $posted_ro_office['posted_ro_office']])->group('pao_id')->combine('pao_id','pao_id')->toArray();
		}
		
		$getPaoDetails = $this->find('all')->select(['pao_user_id','pao_user_id'])->where(['id IN' => $getPaoIds])->combine('pao_user_id','pao_user_id')->toArray();
		$paoNameDetails = $DmiUsers->find('all')->where(['id IN' => $getPaoDetails,'status'=>'active'])->toArray();
		
		
		return $paoNameDetails;
	}
	
	
	
	
}

?>
