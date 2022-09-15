<?php

namespace app\Model\Table;
use Cake\ORM\Table;
use App\Model\Model;
use Cake\ORM\TableRegistry;

class LimsUserActionLogsTable extends Table{
	
	var $name = "LimsUserActionLogs";
    var $useTable = 'lims_user_action_logs';



    public function getActionLog($userId){
        return  $get_user_actions = $this->find('all', array('conditions' => array('user_id IS' => $userId,'action_perform IS NOT NULL'), 'order' => array('id desc'), 'limit' => '100'))->toArray();
    }



    // User Action Perform Log
	// Description : This fuction is created for Make an user action entry in user action log table
	// #CONTRIBUTER : Akash Thakre (u) (m) 
	// DATE : 27-04-2021

	public function saveActionLog($userAction,$status) {

		$user_id = $_SESSION['username'];

		$current_ip = $_SERVER['REMOTE_ADDR'];

		if ($current_ip == '::1') { $current_ip = '127.0.0.1'; }

		$newEntity = $this->newEntity(['user_id'=>$user_id,
                            'action_perform'=>$userAction,
                            'ipaddress'=>$current_ip,
                            'status'=>$status,
                            'created'=>date('Y-m-d H:i:s')]);

        if($this->save($newEntity)){  return true; }
	}

}

?>