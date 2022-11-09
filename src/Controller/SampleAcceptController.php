<?php
namespace App\Controller;

use Cake\Event\Event;
use App\Network\Email\Email;
use Cake\ORM\Entity;
use Cake\Datasource\ConnectionManager;
use Cake\View;

class SampleAcceptController extends AppController
{

	var $name 		= 'SampleAccept';


	
	public function initialize(): void
	{
		parent::initialize();
		$this->viewBuilder()->setLayout('admin_dashboard');
		$this->viewBuilder()->setHelpers(['Form','Html']);
		$this->loadComponent('Customfunctions');
	}

/********************************************************************************************************************************************************************************************************************************/

	//to validate login user
	public function authenticateUser(){

		$this->loadModel('DmiUserRoles');
		$user_access = $this->DmiUserRoles->find('all',array('conditions'=>array('user_email_id IS'=>$this->Session->read('username'))))->first();

		if(!empty($user_access)){
			//proceed
		}else{
			$this->customAlertPage("Sorry You don't have permission to view this page..");
			exit;
		}
	}

/********************************************************************************************************************************************************************************************************************************/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>|Sample Accept Method Starts|>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

	public function sampleAccept(){

		$this->authenticateUser();
		$this->viewBuilder()->setLayout('admin_dashboard');
		$accpt_sample_cd = trim($this->Session->read('accpt_sample_cd'));//trim added on 03-06-2022 by Shreeya
	
		if(!empty($accpt_sample_cd)){

			$this->loadModel('SampleInward');
			$this->loadModel('MCommodityCategory');
			$this->loadModel('MLab');
			$this->loadModel('DmiRoOffices');
			$this->loadModel('Workflow');
			$this->loadModel('DmiUsers');
			$this->loadModel('DmiUserRoles');
			$this->loadModel('MUnitWeight');
			$conn = ConnectionManager::get('default');

			//Set Variables To View Popup Messages
			$message = '';
			$message_theme ='';
			$redirect_to = '';

			$get_sample_code = $this->Workflow->find('all',array('fields'=>'org_sample_code','conditions'=>array('stage_smpl_cd IS'=>$accpt_sample_cd,'stage_smpl_flag IN'=>array('OF','HF'),'display'=>'Y')))->first();
			
			$sample_code = $get_sample_code['org_sample_code'];

			$getqty =  $this->SampleInward->find()->select(['sample_total_qnt', 'parcel_size'])->where(['org_sample_code IS' => $sample_code])->toArray();

			$unit_id = $getqty[0]['parcel_size'];
			
			$unit_query = $this->MUnitWeight->find()->select(['unit_weight'])->where(['unit_id IS'=>$unit_id])->toArray();
			
			$unit = $unit_query[0]['unit_weight'];

			$this->set('getqty',$getqty);
			$this->set('unit',$unit);
			$this->set('samples_list',array($accpt_sample_cd=>$accpt_sample_cd));

			$offices	= "";

			if ($this->request->is('post')) {

				$postdata = $this->request->getData();	
				$sample_code = trim($this->request->getData('stage_sample_code'));
				
				//Check Post Data Validations
				$validate_err = $this->acceptPostValidations($this->request->getData());
				
				if ($validate_err != '') {

					$this->set('validate_err',$validate_err);
					return null;
				}

				
				//HTML Encoding
				foreach ($postdata as $key => $value) {

					$postdata[$key] = htmlentities($this->request->getData($key), ENT_QUOTES);
				}

					$acceptstatus_remark = $this->request->getData("acceptstatus_remark");
					$acceptstatus_flag = $this->request->getData("acceptstatus_flag");

				if ($acceptstatus_flag == "A") {

					//To Store All the Homoginization. and Obervations Values in Sample Obs Reg Rable
					$this->saveSampleRegObsRecords($this->request->getData());

				}
				
				$get_org_sample_code = $this->Workflow->find('all',array('fields'=>'org_sample_code','conditions'=>array('stage_smpl_cd IS'=>$sample_code,'stage_smpl_flag IN'=>array('OF','HF'),'display'=>'Y')))->first();
				$ogrsample = $get_org_sample_code['org_sample_code'];
				$user_code	= $this->request->getData("dst_usr_cd");
				$actual_received_qty = $postdata['actual_received_qty'];
				
				$query = $conn->execute("UPDATE sample_inward SET actual_received_qty ='$actual_received_qty' WHERE org_sample_code='$ogrsample'") ;
				
				if ($this->request->getData('result_dupl_flag')=='D') {
					
					//For Duplicate Analysis
					$query = $conn->execute("UPDATE sample_inward SET result_dupl_flag='D' WHERE org_sample_code='$ogrsample'");

				} else {
					
					//For Single Analysis
					$query = $conn->execute("UPDATE sample_inward SET result_dupl_flag='S' WHERE org_sample_code='$ogrsample'");

				}


				if ($user_code) {

					if (!is_numeric($user_code)) {

						$this->Flash->set('Invalid user code');

						return $this->redirect(array('action' => 'sample_accept'));
					}

					$location = explode("~",$_POST["dst_loc_id"]);
					$tran_date = trim($this->request->getData("tran_date"));
					$dispatch_date	= date("Y/m/d");
					$acceptstatus_date = date("Y/m/d");
					
					if ($_POST["ral_cal"]=='HO') { 
							
						$flag = "HS";
					
					} else { 
						
						$flag = "AS"; 
					}

					$workflow_data	= array("org_sample_code"=>$ogrsample, 
											"src_loc_id"=>$_SESSION["posted_ro_office"], 
											"src_usr_cd"=>$_SESSION["user_code"],
											"dst_loc_id"=>$location[0],
											"dst_usr_cd"=>$user_code,
											"stage_smpl_cd"=>$sample_code,
											"tran_date"=>$tran_date,
											"user_code"=>$_SESSION["user_code"],
											"stage"=>"3",
											"stage_smpl_flag"=>$flag);

					$workflowEntity = $this->Workflow->newEntity($workflow_data);

					if ($this->Workflow->save($workflowEntity)) {

						if ($_POST["ral_cal"]=='HO') {

							$str="UPDATE sample_inward SET status_flag='H',dispatch_date='$dispatch_date' WHERE stage_sample_code='$ogrsample'";
						
						} elseif ($_POST["ral_cal"]=='CAL') {

							$str = "UPDATE sample_inward SET status_flag='AS',
										   phy_accept_sample_date='$dispatch_date',
										   acceptstatus_flag='$acceptstatus_flag',
										   acceptstatus_date='$acceptstatus_date',
										   acceptstatus_remark='$acceptstatus_remark',
										   phy_accept_sample_flag='Y',phy_accpet_user_cd=".$_SESSION["user_code"]." where stage_sample_code='$ogrsample'";
						
						} else {
						
							$str="UPDATE sample_inward set status_flag='AS',
										 phy_accept_sample_date='$dispatch_date',
										 acceptstatus_flag='$acceptstatus_flag',
										 acceptstatus_date='$acceptstatus_date',
										 acceptstatus_remark='$acceptstatus_remark',
										 phy_accept_sample_flag='Y',
										 phy_accpet_user_cd=".$_SESSION["user_code"]." where stage_sample_code='$ogrsample'";
						}

						$conn->execute($str);

						$query = $conn->execute("SELECT user_flag,ro_office 
												 FROM workflow
												 INNER JOIN dmi_users ON workflow.dst_usr_cd=dmi_users.id
												 INNER JOIN dmi_user_roles ON dmi_users.email=dmi_user_roles.user_email_id
												 INNER JOIN dmi_ro_offices ON dmi_users.posted_ro_office=dmi_ro_offices.id
												 INNER JOIN sample_inward ON workflow.org_sample_code=sample_inward.org_sample_code 
												 WHERE workflow.org_sample_code='$ogrsample'AND workflow.dst_usr_cd=$user_code ");

						$user_flag1 = $query->fetchAll('assoc');

						$this->set('user_flag1',$user_flag1);
						$user_flag_new = $user_flag1[0]['user_flag'];
						$ro_office_new = $user_flag1[0]['ro_office'];

						$frd_usr_cd = $this->Workflow->find('all')->where(['stage_smpl_cd' => $sample_code, 'stage_smpl_flag' => 'OF'])->first();
						$oic = $this->DmiRoOffices->getOfficeIncharge($_SESSION['posted_ro_office']);

						if ($acceptstatus_flag=="A") {

							#SMS: Sample Accept
							$this->DmiSmsEmailTemplates->sendMessage(92,$frd_usr_cd['dst_usr_cd'],$sample_code); #To Accepting User
							$this->DmiSmsEmailTemplates->sendMessage(93,$frd_usr_cd['src_usr_cd'],$sample_code); #To Forwarding User
							$this->DmiSmsEmailTemplates->sendMessage(150,$oic,$sample_code); #OIC of Current Posted Office

							// For Maintaining Action Log by Akash (26-04-2022)
							$this->LimsUserActionLogs->saveActionLog('Sample Accept','Success');
							$message = 'The sample with registration code '.$sample_code.' is Accpeted by '.$user_flag_new.' '.$ro_office_new.'';
							$message_theme = 'success';
							$redirect_to = 'available_to_accept_list';

						} else {

							$conn->execute("UPDATE sample_inward SET acc_rej_flg='R', reject_date=now() WHERE org_sample_code='$ogrsample'");

							$frd_usr_cd = $this->Workflow->find('all')->where(['stage_smpl_cd' => $sample_code, 'stage_smpl_flag' => 'OF'])->first();

							#SMS: Sample Rejected
							$this->DmiSmsEmailTemplates->sendMessage(94,$frd_usr_cd['dst_usr_cd'],$sample_code); #To Rejecting User
							$this->DmiSmsEmailTemplates->sendMessage(95	,$frd_usr_cd['src_usr_cd'],$sample_code); #To Forwarding User
							$this->DmiSmsEmailTemplates->sendMessage(151,$oic,$sample_code); #OIC of Current Posted Office


							// For Maintaining Action Log by Akash (26-04-2022)
							$this->LimsUserActionLogs->saveActionLog('Sample Reject','Success');
							$message = 'The sample with registration code '.$sample_code.' is Rejected by '.$user_flag_new.' '.$ro_office_new.'';
							$message_theme = 'success';
							$redirect_to = 'available_to_accept_list';
						}

					} else {

						$this->LimsUserActionLogs->saveActionLog('Sample Accept','Failed'); #Action
						$message = 'Sorry.. There is some technical issues. please check';
						$message_theme = 'failed';
						$redirect_to = 'available_to_accept_list';
					}
				
				} else {
					
					$this->LimsUserActionLogs->saveActionLog('Sample Accept','Failed'); #Action
					$message = 'Sorry.. No officer selected';
					$message_theme = 'failed';
					$redirect_to = 'available_to_accept_list';
				}

			}

			// set variables to show popup messages from view file
			$this->set('message',$message);
			$this->set('message_theme',$message_theme);
			$this->set('redirect_to',$redirect_to);

		}
	}


/********************************************************************************************************************************************************************************************************************************/

	// to get ready to accept sample list
	public function readyToAcceptSamplesList(){

		$this->loadModel('SampleInward');
		$this->loadModel('Workflow');
		$conn = ConnectionManager::get('default');

		$getSamplecodes=array();

		if ($_SESSION['user_flag']=='HO') {

			$query = $conn->execute("SELECT TRIM(w.stage_smpl_cd) AS stage_sample_code
									 FROM sample_inward AS si 
									 INNER JOIN  workflow AS w ON w.org_sample_code=si.org_sample_code AND si.display='Y' AND si.status_flag='H'  AND w.stage_smpl_flag='HF' AND w.dst_usr_cd='".$_SESSION['user_code']."'ORDER BY w.id DESC");
		} else {

			$query = $conn->execute("SELECT TRIM(w.stage_smpl_cd) AS stage_sample_code
									 FROM sample_inward AS si 
									 INNER JOIN  workflow AS w On w.org_sample_code=si.org_sample_code AND si.display='Y' AND si.status_flag='F'  AND w.stage_smpl_flag='OF' AND w.dst_usr_cd='".$_SESSION['user_code']."'ORDER BY w.id DESC");

		}

		$result = $query->fetchAll('assoc');

		//creating array format requird for listing in view
		foreach($result as $esch) {

			$getSamplecodes[$esch['stage_sample_code']] = $esch['stage_sample_code'];
		}

		return($getSamplecodes);
	}


/********************************************************************************************************************************************************************************************************************************/


	//Common Function To Save Records In Sample Reg. Obs Table With Sample Code
	public function saveSampleRegObsRecords($postData){

		$k=array();
		$k=$postData["homCnt"];

		//on updating sample, deleting old records from sample re. obs table for this sample
		//and then again save new records for the same
		$this->loadModel('MSampleRegObs');
		$this->MSampleRegObs->deleteAll(array('stage_sample_code' => $postData["stage_sample_code"]), false);


		//Creating Array
		$sample_reg=array();

		for($i=0;$i<$k;$i++){

			if(isset($postData["general_obs_code".$i])){

				$abc=explode('~',$postData["general_obs_code".$i]);

				$sample_reg[] = array("m_sample_obs_code"=>$abc[1],
										"stage_sample_code"=>$postData["stage_sample_code"],
										"category_code"=>$postData["category_code"],
										"commodity_code"=>$postData["commodity_code"],
										"m_sample_obs_type_code"=>$abc[0],
										"user_code"=>$_SESSION["user_code"],
										'created'=>date('Y-m-d H:i:s'),
										'modified'=>date('Y-m-d H:i:s'));
			}

		}
		//Creating Multiple Entities on Data Array To Save In Loop
		if(!empty($sample_reg)){

			$MSampleRegObsEntity = $this->MSampleRegObs->newEntities($sample_reg);

			foreach($MSampleRegObsEntity as $each){
				$this->MSampleRegObs->save($each);
			}
		}
	}


/********************************************************************************************************************************************************************************************************************************/


	//method to get the homo. & oberv. select dropdowns according to the sample commodity
	//this is called through ajax whwn office type selected
	// load window when accepted radio button checked. else hidden
	public function getCommodityObs(){

		$this->autoRender = false;
		$conn = ConnectionManager::get('default');

		$this->loadModel('MCommodity');
		
		$category_code	= $_POST['category_code'];
		$commodity_code	= $_POST['commodity_code'];

		if (!isset($category_code) || !is_numeric($category_code)) {

			echo "[error]~Category Code is missing";
			exit;
		}
		
		if (!isset($commodity_code) || !is_numeric($commodity_code)) {

			echo "[error]~commodity code is missing";
			exit;
		}

		$conditions = array('commodity_code'=>$commodity_code,'category_code'=>$category_code);

		$query="SELECT mso.m_sample_obs_code,mso.m_sample_obs_desc 
				FROM m_sample_obs AS mso
				INNER JOIN m_commodity_obs AS mco ON mco.m_sample_obs_code=mso.m_sample_obs_code AND mco.category_code='$category_code' AND mco.commodity_code='$commodity_code'
				GROUP BY mso.m_sample_obs_code ,mso.m_sample_obs_desc  
				ORDER BY mso.m_sample_obs_desc ASC";

		$query = $conn->execute($query);

		$res = $query->fetchAll('assoc');

		echo '~'.json_encode($res).'~';
		exit;

	}


/********************************************************************************************************************************************************************************************************************************/

	//method to get the homo. & oberv. options list for each selcted dropdowns
	//this is called through ajax whwn office type selected
	// load window when accepted radio button checked. else hidden
	public function getCommodityObs1(){

		$this->autoRender = false;
		$conn = ConnectionManager::get('default');
		$this->loadModel('MSampleRegObs');
		$sample_obs_code = $_POST['m_sample_obs_code'];

		if(!is_numeric($sample_obs_code)){// || $commodity_code==''
			echo '[error]~Sample Observation code is missing!';
			exit;
		}

		$conditions = array('m_sample_obs_code' => $sample_obs_code);

		$query	= "SELECT mco.m_sample_obs_type_code,mco.m_sample_obs_type_value,mco.m_sample_obs_code 
		           FROM m_sample_obs AS mso
			       INNER JOIN m_sample_obs_type AS mco ON mco.m_sample_obs_code=mso.m_sample_obs_code AND mso.m_sample_obs_code ='$sample_obs_code'
			       GROUP BY mco.m_sample_obs_type_code,mco.m_sample_obs_type_value,mco.m_sample_obs_code 
				   ORDER BY mco.m_sample_obs_type_value ASC";

		$query=$conn->execute($query);
		$res = $query->fetchAll('assoc');

		echo '~'.json_encode($res).'~';
		exit;

	}



/********************************************************************************************************************************************************************************************************************************/


	//method to get already stored values for homo. & obeser. for selected sample in sample obs reg table
	//when already accepted sample will be open in view mode with sample code.
	public function getCommodityRgs(){

		$this->autoRender = false;
		$conn = ConnectionManager::get('default');

		if (isset($_POST['sample_code'])) {

			$sample_code=$_POST['sample_code'];
			if(!is_numeric($sample_code)){// || $commodity_code==''
				echo '[error]~Invaild sample code!';
				exit;
			}

			$query1="SELECT stage_sample_code FROM sample_inward WHERE inward_id='$sample_code'";
			$query1=$conn->execute($query1);
			$res = $query1->fetchAll('assoc');

			$sample_code1=$res[0]['stage_sample_code'];

			$query2="SELECT m_sample_reg_obs_code,m_sample_obs_code,m_sample_obs_type_code FROM m_sample_reg_obs WHERE stage_sample_code='$sample_code1'";
			$query2=$conn->execute($query2);
			$res = $query2->fetchAll('assoc');

			echo '~'.json_encode($res).'~';
			//exit;
		}
	}



/********************************************************************************************************************************************************************************************************************************/


	//called through ajax to fetch user for selected office type
	public function getUser(){

		$conn = ConnectionManager::get('default');

		if (isset($_POST['dst_loc_id'])) {

			$user_office_pattern = '/^[0-9]+$/';
			$office_pattern = '/^[A-Z]+$/';

			$user_office_val = preg_match($user_office_pattern,$_POST['dst_loc_id']);
			$office_val = preg_match($office_pattern,$_POST['user_flag']);

			if ($user_office_val==0){ echo "[error]"; exit; }
			if ($office_val==0){ echo "[error]"; exit; }

			$dst_loc_id = $_POST['dst_loc_id'];
			$flag = $_POST['user_flag'];

		} else {

			$dst_loc_id="";
			$flag="";
		}

		$this->loadModel('DmiUsers');

		  if ($flag=="HO") {
			  
				$query = $conn->execute("SELECT u.id,u.f_name,u.l_name FROM dmi_users AS u
										 INNER JOIN dmi_user_roles AS ur ON u.email=ur.user_email_id
										 WHERE posted_ro_office=$dst_loc_id AND ur.user_flag='$flag' AND u.id=".$_SESSION["user_code"]." ");
		  } else {

				$query = $conn->execute("SELECT u.id,u.f_name,u.l_name FROM dmi_users AS u
										 INNER JOIN dmi_user_roles AS ur ON u.email=ur.user_email_id
										 WHERE posted_ro_office=$dst_loc_id AND role='Inward Officer' AND ur.user_flag='$flag' AND u.id=".$_SESSION["user_code"]." ");
			}

		  $user_data = $query->fetchAll('assoc');

		  if (!empty($user_data)) {

			  echo '~'.json_encode($user_data).'~';
			  
		  } else {
			  echo '~'."0".'~';
		  }

		exit;
	}

/********************************************************************************************************************************************************************************************************************************/

	//called through ajax to get offices list on Selection of office type
	public function getOffice(){

		$this->autoRender=false;
		$conn = ConnectionManager::get('default');

		$type=$_POST['ral'];
		$str="";
		$location=$_SESSION['posted_ro_office'];

		if($type=="RAL"){

			if($_SESSION['user_flag']=='RO' || $_SESSION['user_flag']=='SO'){

					$query1=$conn->execute("SELECT ur.user_flag,o.id,o.ro_office 
											FROM dmi_users AS u
											INNER JOIN dmi_user_roles AS ur ON u.email=ur.user_email_id
											INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
											INNER JOIN user_role AS r ON r.role_name=u.role AND ur.user_flag='RAL' AND u.posted_ro_office=$location
											WHERE u.status !='disactive'
											GROUP BY ur.user_flag,o.id,o.ro_office 
											ORDER BY o.ro_office ASC");

				if($query1){

					$query=$conn->execute("SELECT ur.user_flag,o.id,o.ro_office 
										   FROM dmi_users AS u
										   INNER JOIN dmi_user_roles AS ur ON u.email=ur.user_email_id
										   INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
										   INNER JOIN user_role AS r ON r.role_name=u.role AND ur.user_flag='RAL' AND u.posted_ro_office=$location
										   WHERE u.status !='disactive'
										   GROUP BY ur.user_flag,o.id,o.ro_office 
										   ORDER BY o.ro_office ASC");
				
				} else {

					$query=$conn->execute("SELECT ur.user_flag,o.id,o.ro_office 
									       FROM dmi_users AS u
										   INNER JOIN dmi_user_roles AS ur ON u.email=ur.user_email_id
										   INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
										   INNER JOIN user_role AS r ON r.role_name=u.role AND ur.user_flag='RAL' AND posted_ro_office=$location
										   WHERE u.status !='disactive'
										   GROUP BY ur.user_flag,o.id,o.ro_office 
										   ORDER BY o.ro_office ASC");
				}

			}else{

					$query=$conn->execute("SELECT ur.user_flag,o.id,o.ro_office 
									       FROM dmi_users AS u
										   INNER JOIN dmi_user_roles AS ur ON u.email=ur.user_email_id
										   INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
										   INNER JOIN user_role AS r ON r.role_name=u.role AND ur.user_flag='RAL' AND posted_ro_office=$location
										   WHERE u.status !='disactive'
										   GROUP BY ur.user_flag,o.id,o.ro_office 
										   ORDER BY o.ro_office ASC");
			}

		} 
		
		if($type=="CAL") {

				$query=$conn->execute("SELECT ur.user_flag,o.id,o.ro_office 
								       FROM dmi_users AS u
									   INNER JOIN dmi_user_roles AS ur ON u.email=ur.user_email_id
									   INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
									   INNER JOIN user_role AS r ON r.role_name=u.role AND ur.user_flag='CAL' AND posted_ro_office=$location
									   WHERE u.status !='disactive'
									   GROUP BY ur.user_flag,o.id,o.ro_office 
									   ORDER BY o.ro_office ASC");

		} 
		
		if($type=="HO"){

			$query=$conn->execute("SELECT ur.user_flag,o.id,o.ro_office 
								   FROM dmi_users AS u
								   INNER JOIN dmi_user_roles AS ur ON u.email=ur.user_email_id
								   INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
								   INNER JOIN user_role AS r ON r.role_name=u.role  AND ur.user_flag='HO' AND posted_ro_office=$location
								   WHERE u.status !='disactive'
								   GROUP BY ur.user_flag,o.id,o.ro_office 
								   ORDER BY o.ro_office ASC");
		}

		$offices = $query->fetchAll('assoc');

		foreach($offices as $office1)
		{
			$location=$office1['id'];
			$type=$office1['user_flag'];
			$desc=$office1['ro_office'];
			$str.="<option value='".$location."' >".$type." - ".$desc."</option>";
		}
		echo $str;

	}


/********************************************************************************************************************************************************************************************************************************/

	//to generate and view forwarded sample letter with pdf on accept sample window
	public function gnrtSmplFrwdLtr(){

	  $ltr_sample_cd = $this->Session->read('ltr_sample_cd');

		if(!empty($ltr_sample_cd)){

			$this->viewBuilder()->setLayout('admin_dashboard');
			$this->loadModel('SampleInward');
			$this->loadModel('MSampleType');

			// set variables to show popup messages from view file
			$message = '';
			$redirect_to = '';

			//$samples_list = $this->readyToAcceptSamplesList();

			$this->set('samples_list',array($ltr_sample_cd=>$ltr_sample_cd));

			$sam_type=$this->MSampleType->find('all',array('conditions' => array('display' => 'Y')))->toArray();
			$this->set('Sample_Type',$sam_type);
		}

	}


/********************************************************************************************************************************************************************************************************************************/

	//the view for forward letter pdf with values.
	public function frdLetterPdf($stage_sample_code){

		$this->viewBuilder()->setLayout('pdf_layout');
		$sample_code = $stage_sample_code;
		$this->loadModel('SampleInward');
		$conn = ConnectionManager::get('default');

		$query = $conn->execute("SELECT si.*,b.sample_type_desc,c.container_desc,a.unit_weight,w.dst_usr_cd,w.dst_loc_id,w.src_usr_cd,w.src_loc_id,w.stage_smpl_cd,m.commodity_name
								 FROM sample_inward AS si 
								 INNER JOIN m_sample_type AS b ON b.sample_type_code = si.sample_type_code
								 INNER JOIN m_container_type AS c ON c.container_code = si.container_code
								 INNER JOIN m_unit_weight AS a ON a.unit_id = si.parcel_size
								 INNER JOIN workflow AS w ON w.org_sample_code = si.org_sample_code
								 INNER JOIN m_commodity AS m ON m.commodity_code = si.commodity_code
								 WHERE w.stage IN('3','4') AND si.display='Y' AND si.status_flag IN('F','H') AND w.stage_smpl_cd='$sample_code'");

		$str_data = $query->fetchAll('assoc');
		$this->set('str_data',$str_data);

		$user_code=$str_data[0]['dst_usr_cd'];
		$location_code=$str_data[0]['dst_loc_id'];

		$this->loadModel('DmiUsers');

		$query=$conn->execute("SELECT u.f_name,u.l_name,ur.role_name,ml.ro_office,r.user_flag 
							   FROM dmi_users AS u
							   INNER JOIN user_role AS ur ON ur.role_name=u.role
							   INNER JOIN dmi_user_roles AS r ON u.email=r.user_email_id
							   INNER JOIN dmi_ro_offices AS ml ON ml.id=u.posted_ro_office AND ml.id='$location_code' AND u.id='$user_code' AND u.status != 'disactive' ");

		$user_data = $query->fetchAll('assoc');
		$this->set('user_data',$user_data);

		//below code added, to show Officer Incharge Name & Designation in 'From' at bottom
		//Either sent by any officer, name should be dsplay only of Incharge
		$src_user_code=$str_data[0]['src_usr_cd'];
		$src_location_code=$str_data[0]['src_loc_id'];

		$query=$conn->execute("SELECT u.f_name,u.l_name,u.role,ml.ro_office,r.user_flag 
							   FROM dmi_users AS u
							   INNER JOIN dmi_user_roles AS r ON u.email=r.user_email_id
							   INNER JOIN dmi_ro_offices AS ml ON ml.id=u.posted_ro_office AND ml.id='$src_location_code' AND u.email=ml.ro_email_id AND u.status != 'disactive' ");

		$src_user_data = $query->fetchAll('assoc');
		$this->set('src_user_data',$src_user_data);

		//call to the pdf creaation common method
		$this->callTcpdf($this->render(),'I');
	}


/********************************************************************************************************************************************************************************************************************************/

	//function to take post data and validate each field.
	public function acceptPostValidations($postData){

		$validation_status = '';
		$sample_code = $postData['stage_sample_code'];
		$patternb		= '/^[0-9]+$/';
		$rttttttv		= preg_match($patternb,$sample_code);

		if ($rttttttv==0){
			$validation_status = 'Select proper sample code';
		}

		$inward_id		= trim($postData['inward_id']);
		if($inward_id!=''){
			$rttttttv		= preg_match($patternb,$inward_id);
			if ($rttttttv==0){
				$validation_status = 'Invalid Inward Id';
			}
		}

		$arr	= array("RAL","CAL","HO");
		if(!in_array($postData['ral_cal'],$arr)){
			$validation_status = 'Invalid Office Type';
		}

		$patternb		= '/^[0-9]+$/';
		$rttttttv		= preg_match($patternb,$postData['sample_type']);
		if($postData["sample_type"]!=''){
			if ($rttttttv==0){
				$validation_status = 'Invalid Sample Type';
			}
		}

		if(!is_numeric($postData["commodity_code"])){
			$validation_status = 'Select Proper Commodity';
		}

		if(!is_numeric($postData["dst_usr_cd"])){
			$validation_status = 'Invalid Destination Code';
		}

		if($postData["src_loc_id"]!=''){
			$validation_status = 'Invalid Source Location';
		}

		if(!is_numeric($postData["homCnt"])){
			$validation_status = 'Invalid Homogenization count';
		}

		$tran_date=$postData["tran_date"];
		$patternb='/(\d{4})-(\d{2})-(\d{2})/';
		$rttttttv=preg_match($patternb,$tran_date);
		if($postData["tran_date"]!=''){
			if ($rttttttv==0){
				$validation_status = 'Invalid Transaction date';
			}
		}

		if(!is_numeric($postData["category_code"])){
			$validation_status = 'Invalid Commodity Category';
		}

		return $validation_status;

	}


/********************************************************************************************************************************************************************************************************************************/


	//list of sample available samples to forward
	public function availableToAcceptList(){

		$res = $this->getSampleListToAccept();
		$this->set('res',$res);
	}


/********************************************************************************************************************************************************************************************************************************/


	//created common function to fetch list , to be used for dashboard counts also, on 28-04-2021 by Amol
	public function getSampleListToAccept(){
		
		$conn = ConnectionManager::get('default');
		$user_cd=$this->Session->read('user_code');

		$sample_list = $this->readyToAcceptSamplesList();

		//set array format
		$cus_string= '';
		foreach($sample_list as $each){

			$cus_string .= $each."','";
		}

		$query = $conn->execute("SELECT  si.inward_id, w.stage_smpl_cd, si.received_date, st.sample_type_desc, mcc.category_name, mc.commodity_name, ml.ro_office, w.modified AS forwarded_on
									FROM sample_inward AS si
									INNER JOIN m_sample_type AS st ON si.sample_type_code=st.sample_type_code
									INNER JOIN m_commodity_category AS mcc ON si.category_code=mcc.category_code
									INNER JOIN dmi_ro_offices AS ml ON ml.id=si.loc_id
									INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
									INNER JOIN workflow AS w ON w.org_sample_code = si.org_sample_code
									WHERE w.stage_smpl_cd IN ('$cus_string') 
									ORDER BY w.modified DESC");

		$res = $query ->fetchAll('assoc');
		
		return $res;
		
	}


/********************************************************************************************************************************************************************************************************************************/

	//to redirect on forward window
	public function redirectToAccept($accpt_sample_cd){

		$this->Session->write('accpt_sample_cd',$accpt_sample_cd);
		$this->redirect(array('controller'=>'SampleAccept','action'=>'sample_accept'));

	}

/********************************************************************************************************************************************************************************************************************************/

	//to redirect on generate letter window
	public function redirectToGnrtLtr($ltr_sample_cd){

		$this->Session->write('ltr_sample_cd',$ltr_sample_cd);
		$this->redirect(array('controller'=>'SampleAccept','action'=>'gnrt_smpl_frwd_ltr'));

	}

/********************************************************************************************************************************************************************************************************************************/

	//to show listing of Accepted samples window
	public function acceptedList(){

		$conn = ConnectionManager::get('default');

		//by default
		$to_dt = date('Y-m-d');		
		$from_dt = date('Y-m-d',strtotime('-1 month'));
		
		if ($this->request->is('post')){
			
			//on search
			$to_dt = 	$this->request->getData('to_dt');		
			$from_dt = $this->request->getData('from_dt');
			
			
			if(empty($from_dt) || empty($to_dt)){
				
				return null;
			}
			$this->set(compact('to_dt','from_dt'));
		}

		if(!empty($from_dt) || !empty($to_dt)){
			//to get the list of accepted samples
			$query = $conn->execute("SELECT si.stage_sample_code, mc.commodity_name, st.sample_type_desc, w.stage_smpl_cd, w.tran_date
									 FROM sample_inward AS si 
									 INNER JOIN m_sample_type AS st ON si.sample_type_code=st.sample_type_code
									 INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
									 INNER JOIN workflow AS w ON si.org_sample_code=w.org_sample_code 
									 AND w.src_usr_cd='".$_SESSION["user_code"]."'AND w.stage_smpl_flag='AS' AND date(si.created) >= '$from_dt' AND date(si.created) <= '$to_dt' ORDER BY w.id DESC");

			$res3 = $query->fetchAll('assoc');
		
			if (count($res3)>0) {

				$this->set('res3',$res3);
			}
		}
	}


}
?>
