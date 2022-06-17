<?php
namespace app\Model\Table;
	use Cake\ORM\Table;
	use App\Model\Model;
	use Cake\ORM\TableRegistry;
	use Cake\ORM\Entity;


/*App::uses('Dmi_ro_office','Model');
App::uses('Dmi_appl_with_ro_mapping_log','Model');*/

class DmiApplWithRoMappingsTable extends Table{

	var $name = "DmiApplWithRoMappings";

	public $validate = array(

			'customer_id'=>array(
					'rule'=>array('maxLength',50),
				),
			'office_id'=>array(
					'rule'=>'Numeric',
				),

	);


	//This function is used to get RO office shortcode from application Id
	public function getOfficeDetails($customer_id){

		$DmiRoOffices = TableRegistry::getTableLocator()->get('DmiRoOffices');

		//get office id from mapping table
		$office_id = $this->find('all',array('conditions'=>array('customer_id IS'=>$customer_id)))->first();
				
		//get office details from office table
		$office_details = $DmiRoOffices->find('all',array('conditions'=>array('id IS'=>$office_id['office_id'],'OR'=>array('delete_status IS NULL','delete_status'=>'no'))))->first();

		return $office_details;
	}


	//This function is used to save Application with RO mapping record while new firm added.
	public function saveRecord($customer_id,$office_id){

		//check if record already alailable
		$checkMappedRecord = $this->find('all',array('conditions'=>array('customer_id IS'=>$customer_id)))->first();

		if(empty($checkMappedRecord)){

			//save record in main table
			$DmiApplWithRoMappingEntity  = $this->newEntity(array(
				'customer_id'=>$customer_id,
				'office_id'=>$office_id,
				'created'=>date('Y-m-d H:i:s'),
				'modified'=>date('Y-m-d H:i:s')

			));

			$this->save($DmiApplWithRoMappingEntity);


			//save record in log table
			$DmiApplWithRoMappingLogs = TableRegistry::getTableLocator()->get('DmiApplWithRoMappingLogs');

			$DmiApplWithRoMappingLogsEntity = $DmiApplWithRoMappingLogs->newEntity(array(
				'customer_id'=>$customer_id,
				'office_id'=>$office_id,
				'created'=>date('Y-m-d H:i:s')
			));

			$DmiApplWithRoMappingLogs->save($DmiApplWithRoMappingLogsEntity);

		}
	}



	//store all available firms in ro mapping table
	//for first time till date, then each new firm added will be saved in this table
	public function saveAllRecordsOnce(){

		$DmiFirms = TableRegistry::getTableLocator()->get('DmiFirms');
		$DmiDistricts = TableRegistry::getTableLocator()->get('DmiDistricts');
		//get all added firms from firm table
		$get_firms = $DmiFirms->find('all',array('order'=>'id ASC'))->toArray();

		foreach($get_firms as $each_firm){

			$customer_id = $each_firm['customer_id'];
			//get office id from district table for each firm
			$get_district_office_id = 	$DmiDistricts->find('all',array('conditions'=>array('id IS'=>$each_firm['district'],'OR'=>array('delete_status IS NULL','delete_status'=>'no'))))->first();

			if (!empty($get_district_office_id['so_id'])) {
				$office_id = $get_district_office_id['so_id'];//for SO oofice
			}else{
				$office_id = $get_district_office_id['ro_id']; //for RO office
			}
			//calling above save function
			$this->saveRecord($customer_id,$office_id);

		}
	}
}

?>
