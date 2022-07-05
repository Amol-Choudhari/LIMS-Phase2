<?php
namespace App\Controller;

use Cake\Event\Event;
use App\Network\Email\Email;
use Cake\ORM\Entity;
use Cake\Datasource\ConnectionManager;
use Cake\View;


class IlcForwardController extends AppController{

	var $name = 'IlcForward';
    public function initialize(): void {

		parent::initialize();
		$this->viewBuilder()->setLayout('admin_dashboard');
		$this->viewBuilder()->setHelpers(['Form','Html']);
		$this->loadComponent('Ilc');
		$this->loadModel("IlcSelectedRals");
		$this->loadComponent('Customfunctions');
	}

	// save record
	public function saveSelectList(){

		$this->autoRender = false;

		$forw_sample_cd = $this->Session->read('forw_sample_cd');
		$postData = $this->request->getData();
		
	    $labName    	= $postData['labName'];
		$usrName    	= $postData['usrName'];
		$stage_sample_code 	= $postData['stage_sample_code'];
		$sample_type 	= $postData['sample_type'];
		$date = date('Y-m-d H:i:s');

		//added a line for updated status according to status (0,1) done 13/06/2022 by shreeya
		$this->IlcSelectedRals->updateAll(array('status' => 0,'modified'=>"$date"),array('sample_type' => $sample_type,'stage_sample_code' => $stage_sample_code));
		// added for each loop for save multiple record done 09/06/2022 by shreeya
		$i=0;

		foreach($labName as $eachLab)
		{
			$savelist[] = array(
			
				'ral_name_val'=>$eachLab,
				'inwd_off_val'=>$usrName[$i],
				'stage_sample_code'=>$stage_sample_code,
				'sample_type'=>$sample_type,
				'modified'=>date('Y-m-d H:i:s'),
				'created'=>date('Y-m-d H:i:s'),
				'status'=>1

			);
			$i=$i+1;
		}
		//creating entities for array
		$Saveselect = $this->IlcSelectedRals->newEntities($savelist);
		//saving data in loop
		foreach($Saveselect as $select){	
			$this->IlcSelectedRals->save($select);	
		}  

			
		
	}

	
}

?>
