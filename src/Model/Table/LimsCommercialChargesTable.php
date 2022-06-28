<?php
namespace app\Model\Table;
	use Cake\ORM\Table;
	use App\Model\Model;
	use Cake\ORM\TableRegistry;
	
class LimsCommercialChargesTable extends Table{
	
	var $name = "LimsCommercialCharges";

    public function getAllCharges(){

       return $this->find('all')->where('delete_status IS NULL')->toArray();
    }
	

	// Save details 
	// Description : Save the details of the charges
	// Author : Akash Thakre
	// Date : 2022

	public function saveCharges($postData,$record_id=null){

		//HTML ENCODING
		$charges_type = htmlentities($postData['charges_type'], ENT_QUOTES);
		$charges = htmlentities($postData['charges'], ENT_QUOTES);

		//save array
		$data_array = array(
			'charges_type'=>$charges_type,
			'charges'=>$charges,
			'user_email_id'=>$_SESSION['username'],
			'created'=>date('Y-m-d H:i:s'),
			'modified'=>date('Y-m-d H:i:s')
		);

		//edit array
		if ($record_id != null) {

			$data_array = array(
				'id'=>$record_id,
				'charges_type'=>$charges_type,
				'charges'=>$charges,
				'user_email_id'=>$_SESSION['username'],
				'modified'=>date('Y-m-d H:i:s')
			);
		}
		
		$saveEntity = $this->newEntity($data_array);

		if ($this->save($saveEntity)) {

			return true;
		}
	
	}


	public function getChargeById($id){

		$detail = $this->find('all')->where(['id' => $id])->first();
		return array('charges_type'=>$detail['charges_type'],'charges'=>$detail['charges']);
	}


	public function deleteChargeById($record_id){

		$entity = $this->newEntity(array(

			'id'=>$record_id,
			'delete_status'=>'yes',
			'user_email_id'=>$_SESSION['username'],
			'modified'=>date('Y-m-d H:i:s')
		));
	
			if ($this->save($entity)) {

				return true;
			}

	}

}

?>