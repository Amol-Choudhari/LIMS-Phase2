<?php
namespace App\Controller;

use Cake\Event\Event;
use App\Network\Email\Email;
use Cake\ORM\Entity;
use Cake\Datasource\ConnectionManager;
use Cake\Http\Client\Request;
use Cake\View;
use Cake\ORM\TableRegistry;
use Cake\Routing\RouterBuilder;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\Router;
use PhpParser\Node\Expr\Print_;
use Cake\I18n\FrozenTime;


class AuditController extends AppController {

	var $name = 'Audit';

	//INITIALIZE COMPONENTS AND MODELS
	public function initialize(): void{
		parent::initialize();

		$this->viewBuilder()->setHelpers(['Form','Html']);
		$this->viewBuilder()->setLayout('admin_dashboard');

		$this->loadComponent('Customfunctions');
		$this->loadComponent('Inputvalidation');

	}

/*********************************************************************************************************************************************************************************************************************************/

	public function auditTrail() {

		//########################################################################////
	    //	/*if ($this->referer() != '' || $this->referer() != '/') {				//
		//		if (strpos($this->referer(), $this->webroot) == false) {			//
		//		$this->redirect('http://'.$_SERVER['SERVER_NAME'].Router::url('/'));//
		//			exit;															//
		//		}																	//
		//	}*/																		//
		//########################################################################////

		$this->loadModel('DmiUsers');
		$this->loadModel('DmiUserRoles');

		//$user_flag = $this->DmiUserRoles->query("SELECT user_flag FROM dmi_user_roles WHERE user_flag!='HO' GROUP BY user_flag ORDER BY user_flag");
		$user_flag = $this->DmiUserRoles->find('all')
										->select(['user_flag'])
										->where(['user_flag!'=>'HO'])
										->group('user_flag')
										->order('user_flag')
										->toArray();

		$this->set('user_flag',$user_flag);

		if ($this->request->is('post')) {

			$lab=$this->request->getData('lab');

			$rec_from_dt=$this->request->getData('rec_from_dt');
			$rec_to_dt=$this->request->getData('rec_to_dt');
			$date = FrozenTime::createFromFormat('d/m/Y',$rec_from_dt);
			$from_dt=$date->format('Y-m-d');
			$date1 = FrozenTime::createFromFormat('d/m/Y', $rec_to_dt);
			$to_dt=$date1->format('Y-m-d');
			$ral_lab	= $this->request->getData('ral_lab');

			//##########################################################//////
			// /* $ral_lab=explode("~",$this->request->getData['ral_lab']);///
			// $ral_lab_new=$ral_lab[1];									//
			// $ral_lab=$ral_lab[0]; */										//
		    //##########################################################//////


			if ($ral_lab!='0') {

				$ral_lab=explode("~",$this->request->getData('ral_lab'));
				$ral_lab_new=$ral_lab[1];
				$ral_lab=$ral_lab[0];

			} else {

				//$ral_lab_new	= '';
				//$ral_lab		= '';
			}

			$role	= $this->request->getData('role');

			if ($ral_lab!='0' && $lab!='0' && $role!='0') {

				$audit_trail=$this->DmiUserRoles->query("SELECT * FROM dmi_user_logs AS l
															INNER JOIN dmi_user_roles AS r ON r.user_email_id=l.email_id
															INNER JOIN dmi_users AS du ON du.email=l.email_id
															WHERE user_flag='$ral_lab_new' AND role='$role' AND l.date
															BETWEEN '$from_dt' AND '$to_dt' AND du.posted_ro_office='$ral_lab'
															ORDER BY date DESC");
			} elseif($role!='0') {

				$audit_trail=$this->DmiUserRoles->query("SELECT * FROM dmi_user_logs  AS l
															INNER JOIN dmi_user_roles AS r ON r.user_email_id=l.email_id
															INNER JOIN dmi_users AS du ON du.email=l.email_id
															WHERE l.date BETWEEN '$from_dt' AND '$to_dt' AND role='$role'
															ORDER BY date DESC");

				//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				/*  echo "SELECT * from dmi_user_logs  as l inner join dmi_user_roles as r on r.user_email_id=l.email_id inner join dmi_users as du on du.email=l.email_id where   l.date BETWEEN '$from_dt' and '$to_dt' and role='$role'  order by date DESC"; die;  */ ///
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

			} elseif ($ral_lab!='0') {

				$audit_trail=$this->DmiUserRoles->query("SELECT * FROM dmi_user_logs  AS l
															INNER JOIN dmi_user_roles AS r ON r.user_email_id=l.email_id
															INNER JOIN dmi_users AS du ON du.email=l.email_id
															WHERE du.posted_ro_office='$ral_lab' AND user_flag='$ral_lab_new' AND l.date
															BETWEEN '$from_dt' AND '$to_dt'
															ORDER BY date DESC");

				///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				/*  echo "SELECT * from dmi_user_logs  as l inner join dmi_user_roles as r on r.user_email_id=l.email_id inner join dmi_users as du on du.email=l.email_id where   du.posted_ro_office='$ral_lab' and user_flag='$ral_lab_new' and l.date BETWEEN '$from_dt' and '$to_dt'   order by date DESC";die;  *///
				/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

			} else {

			$audit_trail=$this->DmiUserRoles->query("SELECT * FROM dmi_user_logs  AS l
														INNER JOIN dmi_user_roles as r on r.user_email_id=l.email_id
														INNER JOIN dmi_users as du on du.email=l.email_id
														WHERE  l.date BETWEEN '$from_dt' and '$to_dt'
														ORDER BY date DESC");

			    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				/* echo "select * from dmi_user_logs  as l inner join dmi_user_roles as r on r.user_email_id=l.email_id inner join dmi_users as du on du.email=l.email_id where  l.date BETWEEN '$from_dt' and '$to_dt'   order by date DESC";die;  */
			    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

			}

			if (count($audit_trail)>0) {

				$this->set('audit_trail',$audit_trail);

			} else {
				$this->Session->setFlash(__('Record Not Found....!'));
				return $this->redirect(array('action' => 'audit_trail'));
			}

		}

	}

/*********************************************************************************************************************************************************************************************************************************/

	function get_designation() {

	  $str="";
		$user_flag_role=$_POST["user_flag"];

		if ($user_flag_role!='0') {

			$user_flag		= explode("~",$user_flag_role);
			$user_flag_new	= $user_flag[1];
			$user_flag1		= $user_flag[0];

		} else {

			$user_flag_new	= '';
			$user_flag1		= '';
		}

		$this->loadModel('DmiUsers');

		$str="<option  value=''>Select</option>";
		$str.="<option  value='0'>All</option>";

			if ($user_flag_role!='0') {

				$sample_code1 = $this->DmiUsers->query("SELECT DISTINCT role FROM dmi_users AS du
													    INNER JOIN dmi_user_roles AS r ON du.email=r.user_email_id
													    WHERE user_flag='$user_flag_new' AND posted_ro_office='$user_flag1'");

				for ($i=1;$i<count($sample_code1);$i++) {

					$str.= "<option value='".$sample_code1[$i][0]['role']."'>".$sample_code1[$i][0]['role']."</option>";
				}

			} else {

				$sample_code1 = $this->DmiUsers->query("SELECT DISTINCT role FROM dmi_users AS du
														INNER JOIN dmi_user_roles AS r ON du.email=r.user_email_id ");

				for ($i=1;$i<count($sample_code1);$i++) {

					$str.= "<option value='".$sample_code1[$i][0]['role']."'>".$sample_code1[$i][0]['role']."</option>";
				}
			}

			echo $str;
			exit;


	}

/*********************************************************************************************************************************************************************************************************************************/

	public function get_lab(){

		$str="";

		 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 //$condition=$this->Sample_Condition->find('all',array('order' => array('sam_condition_DESC' => 'ASC'),'conditions' => array('display' => 'Y')));//
		 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		 $this->loadModel('DmiUsers');

      $user_flag = $_POST['user_flag'];

			if ($user_flag=='0') {

					$users = $this->DmiUsers->query("SELECT r.user_flag,o.id,o.ro_office FROM dmi_users AS u
													 INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
													 INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND user_flag IN('RO','SO','RAL','CAL') AND u.status !='disactive'
													 GROUP BY r.user_flag,o.id,o.ro_office
													 ORDER BY o.id ASC");

			} else {


				if ($user_flag==$_SESSION['user_flag']) {

					$users = $this->DmiUsers->query("SELECT r.user_flag,o.id,o.ro_office FROM dmi_users AS u
													 INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
													 INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND user_flag in('RO','SO','RAL','CAL') AND u.status !='disactive' AND r.user_flag='".$_SESSION['user_flag']."' AND u.posted_ro_office='".$_SESSION['posted_ro_office']."'group by r.user_flag,o.id,o.ro_office order by o.id asc");
				} else {

					$users = $this->DmiUsers->query("SELECT r.user_flag,o.id,o.ro_office FROM dmi_users AS u
													 INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
													 INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND user_flag IN('RO','SO','RAL','CAL') AND u.status !='disactive' AND r.user_flag='$user_flag'
													 GROUP BY r.user_flag,o.id,o.ro_office
													 ORDER BY o.id ASC");
				}

			}

			$str="<option  value=''>Select</option>";
			$str.="<option  value='0'>All</option>";

			foreach ($users as $users1) {

				if ($user_flag==$_SESSION['user_flag']) {

					$str.="<option value='".$users1[0]['id']."~".$users1[0]['user_flag']."' selected>".$users1[0]['user_flag'].",".$users1[0]['ro_office']."</option>";

				} else {

					$str.="<option value='".$users1[0]['id']."~".$users1[0]['user_flag']."'>".$users1[0]['user_flag'].",".$users1[0]['ro_office']."</option>";
				}
			}

			echo $str;
			exit;
	}


/*********************************************************************************************************************************************************************************************************************************/

	public function totalLoginTrail(){

        $conn = ConnectionManager::get('default');
    	$query = $conn->execute("SELECT ul.*,r.ro_office,u.role,u.f_name,u.l_name
    							 FROM dmi_user_logs ul,dmi_users u,dmi_ro_offices r
    					     	 WHERE ul.remark='Success' AND ul.email_id=u.email AND r.id=u.posted_ro_office AND ul.time_out is null AND islogout!=2
    					     	 ORDER BY time_in DESC");

        $records = $query->fetchAll('assoc');

        if (count($records)>0) {
            $this->set('records',$records);
        } else {
            $this->Session->setFlash(__('Record Not Found....!'));
            return $this->redirect(array('action' => 'total_login_trail'));
        }

	}


/*********************************************************************************************************************************************************************************************************************************/

	public function taskTrack() {

		$this->loadModel('DmiUserRoles');
		$conn = ConnectionManager::get('default');
		$query = $conn->execute("SELECT ul.*,r.ro_office,u.role,u.f_name,u.l_name
								 FROM dmi_user_logs ul,dmi_users u,dmi_ro_offices r
							     WHERE ul.remark='Success' AND ul.email_id=u.email AND r.id=u.posted_ro_office AND  ul.islogout=1
							     ORDER BY ul.id DESC");

		$records = $query->fetchAll('assoc');

		$finalArr = array();

			for ($i=0;$i<count($records);$i++) {

				$subRecords	= $this->DmiUserRoles->query("SELECT form_name,trans_in_time FROM m_form_logs WHERE trans_id=".$records[$i]['id']." order by id asc");
				//Remove/change date format on 22-05-2019 by Amol
				$records[$i]['date'] = $records[$i]['date'];
				$records[$i]['time_in']	= date("h:i A", strtotime($records[$i]['time_in']));
				$records[$i]['time_out'] = date("h:i A", strtotime($records[$i]['time_out']));
				$records[$i]['subdata']	= $subRecords;
				array_push($finalArr,$records[$i]);
			}


			if (count($records)>0) {

				$this->set('finalArr',$finalArr);
				//$this->set('audit_trail',json_encode($finalArr));
			} else {

				$this->Session->setFlash(__('Record Not Found....!'));
				return $this->redirect(array('action' => 'task_track'));
			}


	}


/*********************************************************************************************************************************************************************************************************************************/

	public function hitcount() {

		//#############################################################################////
		//  /* if ($this->referer() != '' || $this->referer() != '/') {					///
		//	if (strpos($this->referer(), $this->webroot) == false) {					///
		//	   $this->redirect('http://'.$_SERVER['SERVER_NAME'].Router::url('/'));		///
		//		exit;																	///
		//	}																			///
		// } 																			///
		//#############################################################################////

		$this->loadModel('DmiUserRoles');

		$user_flag = $this->DmiUserRoles->query("SELECT user_flag FROM dmi_user_roles WHERE user_flag!='HO' GROUP BY user_flag ORDER BY user_flag");

		$this->set('user_flag',$user_flag);

		if ($this->request->is('post')){

			$lab			= $this->request->getData('lab');
			$rec_from_dt	= $this->request->getData('rec_from_dt');
			$rec_to_dt		= $this->request->getData('rec_to_dt');
			$date 			= FrozenTime::createFromFormat('d/m/Y',$rec_from_dt);
			$from_dt		= $date->format('Y-m-d');
			$date1 			= FrozenTime::createFromFormat('d/m/Y', $rec_to_dt);
			$to_dt			= $date1->format('Y-m-d');
			$ral_lab		= $this->request->getData('ral_lab');

			if ($ral_lab!='0') {
				$ral_labArr		= explode("~",$ral_lab);
				$ral_lab_Id		= $ral_labArr[0];
				$ral_lab_name	= $ral_labArr[1];
			}

			if ($ral_lab!='0' && $lab!='0') {

				$emailData = $this->DmiUserRoles->query("SELECT u.f_name ||' '|| u.l_name AS username,u.role,u.email,ur.user_flag,'".$ral_lab_name."' ||', '|| r.ro_office as ro_office from dmi_users u,dmi_user_roles ur,dmi_ro_offices r where u.email=ur.user_email_id and u.posted_ro_office=r.id  and ur.user_flag='".$lab."' and u.posted_ro_office='$ral_lab_Id'");

			}elseif ($ral_lab =='0' && $lab=='0'){

				$emailData = $this->DmiUserRoles->query("SELECT u.f_name ||' '|| u.l_name AS username,u.role,u.email,ur.user_flag ,r.ro_office from dmi_users u,dmi_user_roles ur,dmi_ro_offices r  WHERE u.email=ur.user_email_id AND u.posted_ro_office=r.id");

			}elseif ($lab == '0'){

				$emailData = $this->DmiUserRoles->query("SELECT u.f_name ||' '|| u.l_name AS username,u.role,u.email,ur.user_flag ,'".$ral_lab_name."' ||', '|| r.ro_office as ro_office from dmi_users u,dmi_user_roles ur,dmi_ro_offices r where u.email=ur.user_email_id and u.posted_ro_office=r.id and u.posted_ro_office='$ral_lab_Id'");

			} else {

				$emailData = $this->DmiUserRoles->query("SELECT u.f_name ||' '|| u.l_name AS username,u.role,u.email,ur.user_flag ,'".$lab."' || ', '|| r.ro_office as ro_office from dmi_users u,dmi_user_roles ur,dmi_ro_offices r where u.email=ur.user_email_id and u.posted_ro_office=r.id and ur.user_flag='".$lab."'");
			}

			$reportData	= array();

			foreach($emailData as $emailDetails){

				$tempArray		= array();
				$fetchCountData	= $this->DmiUserRoles->query("SELECT count(*) AS loginCount FROM dmi_user_logs WHERE email_id='".$emailDetails['email']."' and date BETWEEN '$from_dt' and '$to_dt'");
				$tempArray['username']		= $emailDetails['username'];
				$tempArray['email']			= $emailDetails['email'];
				$tempArray['role']			= $emailDetails['role'];
				$tempArray['office']		= $emailDetails['ro_office'];
				$tempArray['loginCount']	= $fetchCountData['logincount'];
				array_push($reportData,$tempArray);
			}

			if (count($reportData)>0) {

				$this->set('reportData',$reportData);
				$this->set('from_date',$this->request->getData('rec_from_dt'));
				$this->set('to_date',$this->request->getData('rec_to_dt'));
			} else {

				$this->Session->setFlash(__('Record Not Found....!'));
				return $this->redirect(array('action' => 'audit_trail'));
			}
		}

	}
}
?>
