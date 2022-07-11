<?php

namespace app\Model\Table;
use Cake\ORM\Table;
use App\Model\Model;
use Cake\ORM\TableRegistry;

class SampleInwardDetailsTable extends Table{

	var string $name = "SampleInwardDetails";

	public function getSampleDetails($sample_code){

		return $this->find('all')->where(['org_sample_code' => $sample_code])->first();
	}



}
