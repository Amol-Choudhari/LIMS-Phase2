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

		//print_r($postData); exit;
		//HTML ENCODING
		$charges_type = null;
		$charges = htmlentities($postData['charges'], ENT_QUOTES);

	
		$category_code = htmlentities($postData['category_code'], ENT_QUOTES);
		$commodity_code	= htmlentities($postData['commodity_code'], ENT_QUOTES);

		//edit array
		if ($record_id != null) {
	
			$data_array = array(
				'id'=>$record_id,
				'charges'=>$charges,
				'user_email_id'=>$_SESSION['username'],
				'modified'=>date('Y-m-d H:i:s'),
			);

		} else {
			//add array
			$data_array = array(
				'charges_type'=>$charges_type,
				'charges'=>$charges,
				'user_email_id'=>$_SESSION['username'],
				'created'=>date('Y-m-d H:i:s'),
				'modified'=>date('Y-m-d H:i:s'),
				'category_code'=>$category_code,
				'commodity_code'=>$commodity_code
			);
		}
		
		$saveEntity = $this->newEntity($data_array);

		if ($this->save($saveEntity)) {

			return true;
		}
	
	}


	public function getChargeById($id){

		$detail = $this->find('all')->where(['id' => $id])->first();
		return array('category_code'=>$detail['category_code'],'commodity_code'=>$detail['commodity_code'],'charges'=>$detail['charges']);
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


	public function checkIfAlreadyUsed($record_id){

		$detail = $this->find('all')->where(['commodity_code' => $record_id, 'delete_status IS NULL'])->first();
		if ($detail != null) {
			return true;
		} else {
			return false;
		}
	}

	public function getChargesForPayment($id){

		$detail = $this->find('all')->select(['charges'])->where(['commodity_code IS' => $id, 'delete_status IS NULL'])->first();
		if (!empty($detail)) {
			$charge = $detail['charges'];
		} else {
			$charge = 'N/A';
		}

		return $charge; 
	}


}

?>