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
}

?>