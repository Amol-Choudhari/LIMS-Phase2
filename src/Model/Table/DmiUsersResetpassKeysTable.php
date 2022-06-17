<?php
namespace app\Model\Table;
	use Cake\ORM\Table;
	use App\Model\Model;
	use Cake\ORM\TableRegistry;
	use Cake\Core\Configure;
	
class DmiUsersResetpassKeysTable extends Table{
	
	var $name = "DmiUsersResetpassKeys";
	
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>//	
	
	public function saveKeyDetails($user_id,$key_id){
		
		$saveEntity = $this->newEntity(array(
			'user_id'=>$user_id,
			'key'=>$key_id,
			'created_on'=>date('Y-m-d H:i:s')
		));
		
		$this->save($saveEntity);
		
	}
	
	
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>//


	public function checkValidKey($user_id,$key_id){
		
		//check record is available
		$get_record = $this->find('all',array('conditions'=>array('user_id IS'=>$user_id,'key IS'=>$key_id,'status is NULL')))->first();
		//print($key_id);exit;
		if(!empty($get_record)){
			//check key created on
			$created_on = $get_record['created_on'];
			$current_timestamp = date('d-m-Y H:i:s');
		
			$created_on = strtotime(str_replace('/','-',$created_on));
			$current_timestamp = strtotime($current_timestamp);
			
			$diff_in_seconds = $current_timestamp - $created_on;
			$diff_in_hours = ($diff_in_seconds/60)/60;//converted in hours
			
			if($diff_in_hours < 24){
				
				return 1;
			}else{
				
				//update status to 2, link expired
				$saveEntity = $this->newEntity(array(
					'id'=>$get_record['id'],
					'status'=>'2'
				));
				
				$this->save($saveEntity);
				
				return 2;
			}
			
		}else{
			return 2;
		}
	}
	
	
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>//	
	
	
	public function updateKeySuccess($user_id,$key_id){
		
		//check record is available
		$get_record = $this->find('all',array('conditions'=>array('user_id IS'=>$user_id,'key IS'=>$key_id,'status is NULL')))->first();
		
		if(!empty($get_record)){
			
			//update status to 1, link successfully used
			
			$saveEntity = $this->newEntity(array(
			
				'id'=>$get_record['id'],
				'status'=>'1'
			
			));
			$this->save($saveEntity);
		}

	}
	
	
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>//
	
	
	public function checkAadharAuth($user_id){
		
		//check record is available
		$get_record = $this->find('all',array('conditions'=>array('user_id IS'=>$user_id,'once_auth'=>'yes')))->first();
		if(!empty($get_record)){
			return 1;//aadhar auth done
		}else{
			return 2;//aadhar auth not done
		}
		
	}
	
	
	
}

?>