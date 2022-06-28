<?php

namespace app\Model\Table;
use Cake\ORM\Table;
use App\Model\Model;

class DmiDistrictsTable extends Table{
	
	
	public $validate = array(
	
		'district_name'=>array(
				'rule'=>array('maxLength',100),		
				'allowEmpty'=>true,
			),
		'state_id'=>array(
				'rule'=>'Numeric',	
				'allowEmpty'=>true,
			),
		'ro_id'=>array(
				'rule'=>'Numeric',	
				'allowEmpty'=>true,
			),
		'pao_id'=>array(
				'rule'=>'Numeric',
				'allowEmpty'=>true,
			),
	);


	//get the paoi ids by office 

	public function getPaoId($posted_office_id){
		
		$userFlag = $_SESSION['user_flag'];

		if ($userFlag == 'RO') {
			$details = $this->find('all')->select(['pao_id'])->where(['ro_id' => $posted_office_id])->first();
		} elseif ($userFlag == 'SO') {
			$details = $this->find('all')->select(['pao_id'])->where(['so_id' => $posted_office_id])->first();
		}

		return $details['pao_id'];

		
	}


}

?>