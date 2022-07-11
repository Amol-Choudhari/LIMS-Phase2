<?php

namespace app\Model\Table;
use Cake\ORM\Table;
use App\Model\Model;
use Cake\ORM\TableRegistry;

class LimsDdoDetailsTable extends Table{

    var string $name = "LimsDdoDetails";
   
    public function getPaoDetails() {

		$paoNameDetails = array();
        $DmiRoOffices = TableRegistry::getTableLocator()->get('DmiRoOffices');
        $DmiUsers = TableRegistry::getTableLocator()->get('DmiUsers');

        $details = $this->find('all')->toArray();

        if(!empty($details)){

			$i = 0;
			foreach($details as $each){
               
				$lab_name = $DmiRoOffices->find('all')->select(['ro_office','office_type'])->where(['id IS'=> $each['lab_id']])->first();
                
                $ddo_name = $DmiUsers->find('all',array('conditions'=>array('id IS'=> $each['dmi_user_id'], 'status !=' =>'disactive')))->first();
 
                $paoNameDetails[$i] = array('id'=>$each['id'],'lab_name' => $lab_name['ro_office'],'lab_type'=>$lab_name['office_type'], 'ddo_name' => $ddo_name['f_name']." ".$ddo_name['l_name'],'ddo_email' => $ddo_name['email']);

                $i = $i + 1;
			}
            
		}else{

			$paoNameDetails = array();
		}
        
		return $paoNameDetails;
	}



    public function getRecordById($id){
        return $this->find()->where(['id IS' => $id])->first();
    }


    public function getRecordByOffice(){

        $DmiRoOffices = TableRegistry::getTableLocator()->get('DmiRoOffices');
        $DmiPaoDetails = TableRegistry::getTableLocator()->get('DmiPaoDetails');
        $DmiDistricts = TableRegistry::getTableLocator()->get('DmiDistricts');

        $username = $_SESSION['username'];
        $userFlag = $_SESSION['user_flag'];
        $posted_office_id = $_SESSION['posted_ro_office'];
			
		if($_SESSION['user_flag']=='RAL' || $_SESSION['user_flag']=='CAL'){
			$fetchDeatils = $this->find('all')->select(['ro_office_id'])->where(['lab_id' => $posted_office_id])->first();
			$posted_office_id = $fetchDeatils['ro_office_id'];
			
		}
		
        $pao_id = $DmiDistricts->getPaoId($posted_office_id);
        $ddoDetails = $DmiPaoDetails->getPaoDetails($pao_id);

        return $ddoDetails;
    }


    public function saveDetails($postData){

        $username = $_SESSION['username'];
        
        $DmiPaoDetails = TableRegistry::getTableLocator()->get('DmiPaoDetails');
        $DmiUsers = TableRegistry::getTableLocator()->get('DmiUsers');

        $pao_id = $DmiPaoDetails->getPaoDetailsbyPaoId($postData['ddo_id']);
        $ro_office_id = $DmiUsers->getUserDetailsById($postData['ddo_id']);
        
        if (isset($_SESSION['ddo_table_id'])) {

            $newEntity = $this->newEntity([

                'id'=>$_SESSION['ddo_table_id'],
                'lab_id'=>$postData['ral_office_id'],
                'pao_id'=>$pao_id,
                'change_reason'=>$postData['reason_to_change'],
                'change_by'=>$username,
                'modified'=>date('Y-m-d H:i:s'),
                'ro_office_id'=>$ro_office_id['posted_ro_office'],
                'dmi_user_id'=>$postData['ddo_id']
            ]);

        } else {

            $newEntity = $this->newEntity([

                'lab_id'=>$postData,
                'pao_id'=>$postData,
                'change_reason'=>$postData,
                'change_by'=>$username,
                'modified'=>date('Y-m-d H:i:s'),
                'ro_office_id'=>$postData,
                'dmi_user_id'=>$postData
            ]);
        }

		
        if($this->save($newEntity)){  return true; }		
    }

}

?>
