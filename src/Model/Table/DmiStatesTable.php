<?php

namespace app\Model\Table;
use Cake\ORM\Table;
use App\Model\Model;

class DmiStatesTable extends Table{
	
	
	public $validate = array(
		
			'state_name'=>array(
					'rule'=>array('maxLength',100),		
					'allowEmpty'=>false,
				),	
		);
}

?>