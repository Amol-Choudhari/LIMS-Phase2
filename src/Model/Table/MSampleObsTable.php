<?php

namespace app\Model\Table;
use Cake\ORM\Table;
use App\Model\Model;
use Cake\ORM\TableRegistry;

class MSampleObsTable extends Table{

		var $name = "MSampleObs";

	public function initialize(array $config): void {	

        $this->setTable('m_sample_obs');
    }

}

?>