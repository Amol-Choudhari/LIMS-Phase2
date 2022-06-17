<?php
namespace app\Model\Table;
	use Cake\ORM\Table;
	use App\Model\Model;
	use Cake\ORM\TableRegistry;
	

	class DmiPasswordLogsTable extends Table{
	
			var $name = "DmiPasswordLogs";
			var $useTable = 'dmi_password_logs';
			
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>//

	public function savePasswordLogs($username, $user_type, $password){
	
		date_default_timezone_set('Asia/Kolkata');

		$newEntity = $this->newEntity(array(
			'username'=>$username,
			'user_type'=>$user_type,
			'password'=>$password,
			'created'=>date('Y-m-d H:i:s')
		));
		if ($this->save($newEntity)){ return true;  }

		
		$this->Authentication->userActionPerformLog("Password Changed","Success");
	}

//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>//		
			
	public function checkPastThreePassword($username, $user_type, $password){
				
		$result = "empty";
	
		
			$lastThreePassword = $this->find('all', array('conditions' => array('username'=>$username, 'user_type'=>$user_type), 'order' => 'id DESC', 'limit' => '3'));
			
			foreach($lastThreePassword as $passwordLog) {
				
				$passwordInDb = $passwordLog['password'];
				
				if($password == $passwordInDb)
				{
					$result = 'found';
				}
				
			}
			
			return $result;
			exit;
					
		
	}
	

}

?>