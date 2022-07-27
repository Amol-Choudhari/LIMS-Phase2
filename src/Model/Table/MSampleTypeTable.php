<?php

namespace app\Model\Table;
use Cake\ORM\Table;
use App\Model\Model;
use Cake\ORM\TableRegistry;

class MSampleTypeTable extends Table{
	
	var $name = "MSampleType";
	var $useTable = 'm_sample_type';


	public function getSampleType($id){
		$sample_type = $this->find('all')->select(['sample_type_desc'])->where(['sample_type_code' => $id, 'display !=' => 'N'])->first();
		return $sample_type['sample_type_desc'];
	}

}

?>