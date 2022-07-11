<?php
namespace App\Controller;

use Cake\Event\Event;
use App\Network\Email\Email;
use Cake\ORM\Entity;
use Cake\Datasource\ConnectionManager;
use Cake\View;


class InwardController extends AppController{
	var $name = 'Inward';
	
	public function initialize(): void {
		parent::initialize();
		$this->viewBuilder()->setLayout('admin_dashboard');
		$this->viewBuilder()->setHelpers(['Form','Html']);
		$this->loadComponent('Customfunctions');
		$this->loadModel('LimsUserActionLogs');								 
	}

/****************************************************************************************************************************************************************************************************************************************************************/
	
	//to validate login user
	public function authenticateUser(){

		$this->loadModel('DmiUserRoles');
		$user_access = $this->DmiUserRoles->find('all',array('conditions'=>array('user_email_id IS'=>$this->Session->read('username'))))->first();

		if (!empty($user_access)) {
			//proceed
		} else {
			echo "Sorry.. You don't have permission to view this page";
			exit();
		}
	}

/****************************************************************************************************************************************************************************************************************************************************************/

	//to clear session variables
	public function freshRegistration(){

		//clear some session variables
		$this->Session->delete('inward_id');
		$this->Session->delete('org_sample_code');
		$this->Session->delete('stage_sample_code');
		$this->Session->delete('acc_rej_flg');

		$this->redirect('/inward/sample_inward');
	}


/****************************************************************************************************************************************************************************************************************************************************************/

	//to open sample inward form in edit mode
	public function fetchInwardId($inward_id){
		//get org sample code by inward id
		$this->loadModel('SampleInward');
		$get_sample_code = $this->SampleInward->find('all',array('fields'=>'org_sample_code', 'conditions'=>array('inward_id IS'=>$inward_id)))->first();

		$this->Session->write('inward_id',$inward_id);
		$this->Session->write('org_sample_code',$get_sample_code['org_sample_code']);

		$this->redirect('/inward/sample_inward');
	}

/****************************************************************************************************************************************************************************************************************************************************************/	


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--------<Sample Inward>-------->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

	//to register sample first time
	public function sampleInward(){

		$this->authenticateUser();
 		$this->loadModel('SampleInward');
		$this->loadModel('MCommodityCategory');
		//$this->loadModel('Mlab');
		$this->loadModel('DmiUsers');
		$this->loadModel('DmiUserRoles');
		$this->loadModel('MCommodity');
		$this->loadModel('UserRole');
		$conn = ConnectionManager::get('default');

		// set variables to show popup messages from view file
		$message = '';
		$message_theme = '';
		$redirect_to = '';

		$role_code = $this->Session->read('role_code');
		$user_cd = $this->Session->read('user_code');
		//check user role
		if ($_SESSION['user_flag']=='RO' || $_SESSION['user_flag']=='SO') {

			$query = $conn->execute("SELECT ur.user_flag,o.id,o.ro_office 
									 FROM dmi_users AS u 
									 INNER JOIN dmi_user_roles AS ur ON u.email=ur.user_email_id 
									 INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id AND u.id=".$_SESSION['user_code']."group by ur.user_flag,o.id,o.ro_office order by o.ro_office asc");
		
		} elseif ($_SESSION['user_flag']=='RAL') {

			$query = $conn->execute("SELECT dmi_user_roles.user_flag,dmi_ro_offices.id,dmi_ro_offices.ro_office FROM dmi_users
									 INNER JOIN dmi_user_roles ON dmi_users.email = dmi_user_roles.user_email_id
									 INNER JOIN dmi_ro_offices ON dmi_users.posted_ro_office=dmi_ro_offices.id AND dmi_user_roles.user_flag IN('RAL','RO','SO') AND role IN('Inward Officer','RO Officer','SO Officer')
									 WHERE dmi_users.status != 'disactive'
									 GROUP BY dmi_ro_offices.id,dmi_ro_offices.ro_office ,dmi_user_roles.user_flag 
									 ORDER BY dmi_ro_offices.ro_office,user_flag ASC");

									 

		} elseif ($_SESSION['user_flag']=='CAL') {

			$query = $conn->execute("SELECT ur.user_flag,o.id,o.ro_office FROM dmi_users AS u
									 INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
									 INNER JOIN dmi_user_roles AS ur ON u.email=ur.user_email_id AND ur.user_flag IN('RAL','CAL','RO','SO')
									 WHERE u.status != 'disactive'
									 GROUP BY ur.user_flag,o.id,o.ro_office 
									 ORDER BY o.ro_office ASC");
		
		} elseif ($_SESSION['user_flag']=='HO') {

			$query = $conn->execute("SELECT ur.user_flag,o.id,o.ro_office FROM dmi_users AS u
									 INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
									 INNER JOIN dmi_user_roles AS ur ON u.email=ur.user_email_id AND  ur.user_flag IN('RAL','CAL','RO','SO','HO')
									 WHERE u.status != 'disactive'
									 GROUP BY ur.user_flag,o.id,o.ro_office 
									 ORDER BY o.ro_office ASC");
		}

		$query_result = $query ->fetchAll('assoc');


		//create loop wise array to display cust users list
		$users=array();

		//default on first
		$users['0']='Others';
		
		foreach ($query_result as $each) {

			$users[$each['id']]='('.$each['user_flag'].') '.$each['ro_office'];
		}
		
		$this->set('users',$users);

		//get login user office to show default selected
		$get_office = $this->DmiUsers->find('all',array('conditions'=>array('id IS'=>$user_cd)))->first();
		$default_loc = $get_office['posted_ro_office'];
		$this->set('default_loc',$default_loc);

		//designation list
		$desig_list = $this->UserRole->find('list',array('keyField'=>'role_code','valueField'=>'role_name','conditions'=>array('role_name'=>$get_office['role'])))->toArray();
		$this->set('desig_list',$desig_list);

		//designation
		$get_desig = $this->UserRole->find('all',array('conditions'=>array('role_name'=>$get_office['role'])))->first();
		$this->set('default_desig',$get_desig['role_code']);

		//category lists
		$commodity_category = $this->MCommodityCategory->find('list',array('valueField'=>'category_name','conditions'=>array('display'=>'Y'),'order'=>'category_name'))->toArray();
		$this->set('commodity_category',$commodity_category);

		//send sample_condition to view
		$this->loadModel('MSampleCondition');
		$condition=$this->MSampleCondition->find('list',array('valueField'=>'sam_condition_desc','conditions' => array('display' => 'Y')))->toArray();
		$this->set('sample_condition',$condition);

		//send parcel_condition to view
		$this->loadModel('MParCondition');
		$par_cond=$this->MParCondition->find('list',array('valueField'=>'par_condition_desc','conditions' => array('display' => 'Y')))->toArray();
		$this->set('parcel_condition',$par_cond);

		//Reason for Rejection
		$this->loadModel('MRejectReason');
		$rej=$this->MRejectReason->find('list',array('valueField'=>'rej_reason','conditions' => array('display' => 'Y')))->toArray();
		$this->set('rej',$rej);

		//send a type of Sample to view
		$this->loadModel('MSampleType');
		$sam_type=$this->MSampleType->find('list',array('valueField'=>'sample_type_desc','conditions' => array('display' => 'Y')))->toArray();
		$this->set('Sample_Type',$sam_type);

		//physical appereance
		$this->loadModel('Workflow');
		$this->loadModel('MPhyApperance');
		$phy_appear=$this->MPhyApperance->find('list',array('valueField'=>'phy_appear_desc','conditions' => array('display' => 'Y')))->toArray();
      	$this->set('phy_app',$phy_appear);

		//Unit Weight of Parcel
		$this->loadModel('MUnitWeight');
		$grade_desc=$this->MUnitWeight->find('list',array('valueField'=>'unit_weight','conditions' => array('display' => 'Y')))->toArray();
      	$this->set('grade_desc',$grade_desc);

		//container types
		$this->loadModel('MContainerType');
		$con=$this->MContainerType->find('list',array('valueField'=>'container_desc','conditions' => array('display' => 'Y')))->toArray();
      	$this->set('con',$con);

		//months array
		$monthArray = array('1'=>'January','2'=>'February','3'=>'March','4'=>'April','5'=>'May','6'=>'June','7'=>'July','8'=>'August','9'=>'September','10'=>'October','11'=>'November','12'=>'December');
		$this->set('monthArray',$monthArray);

		//to fetch record data to show in update/view mode
		$sample_inward_data=array();
		//for progress bar
		$sample_inward_form_status='';
		
		//moved the query inside due to condition on null and fetching random record.
		//and taking value in variable and used in !empty condition, if the condition was on direct null session variable
		//on 23-03-2021 by Akash
		$org_samp_sess_var = $this->Session->read('org_sample_code');
		
		$sample_inward_data = $this->SampleInward->find('all',array('conditions'=>array('org_sample_code IS'=>$org_samp_sess_var),'order'=>'inward_id desc'))->first();
		
		if (!empty($org_samp_sess_var) && !empty($sample_inward_data)) {
			
			$this->set('default_loc',$sample_inward_data['loc_id']);
			
			// this below condition is added by Akash on 13-06-2022 for progress bar.
			if (!empty($sample_inward_data)) {
				//for progress bar
				$sample_inward_form_status='saved';
			}
			
			//get selected commdity from category id
			$this->loadModel('MCommodity');
			
			$commodity_list = $this->MCommodity->find('list',array('keyField'=>'commodity_code','valueField'=>'commodity_name','conditions'=>array('category_code IS'=>$sample_inward_data['category_code'])))->toArray();

		} else {

			//all default value set to ''
			$sample_inward_data['loc_id']='';
			$sample_inward_data['designation']='';
			$sample_inward_data['users']='';
			$sample_inward_data['letr_ref_no']='';
			$sample_inward_data['user_code']='';
			$sample_inward_data['tran_date']='';
			$sample_inward_data['fin_year']='';
			$sample_inward_data['reject_date']='';
			$sample_inward_data['letr_date']='';
			$sample_inward_data['received_date']='';
			$sample_inward_data['container_code']='';
			$sample_inward_data['entry_flag']='';
			$sample_inward_data['par_condition_code']='';
			$sample_inward_data['sam_condition_code']='';
			$sample_inward_data['sample_type_code']='';
			$sample_inward_data['sample_total_qnt']='';
			$sample_inward_data['parcel_size']='';
			$sample_inward_data['category_code']='';
			$sample_inward_data['commodity_code']='';
			$sample_inward_data['ref_src_code']='';
			$sample_inward_data['expiry_month']='';
			$sample_inward_data['expiry_year']='';
			$sample_inward_data['acc_rej_flg']='';
			$sample_inward_data['rej_code']='';
			$sample_inward_data['rej_reason']='';
			$sample_inward_data['status_flag']='';

			$commodity_list = array(); 
		}

		$this->set('commodity_list',$commodity_list);
		$this->set('sample_inward_data',$sample_inward_data);
		$this->set('sample_inward_form_status',$sample_inward_form_status);

		//for sample details progress bar
		if (!empty($this->Customfunctions->checkSampleIsSaved('sample_details',$this->Session->read('org_sample_code')))) {

			$sample_details_form_status = 'saved';
		
		} else {
			
			$sample_details_form_status = '';
		}
		
		$this->set('sample_details_form_status',$sample_details_form_status);

		//to show/hide Confirm btn on form
		$confirmBtnStatus = $this->Customfunctions->showHideConfirmBtn();
		$this->set('confirmBtnStatus',$confirmBtnStatus);
		
		if ($this->request->is('post')) {

			$postData = $this->request->getData();
			
			//html encode the each post inputs
			foreach ($postData as $key => $value) {
		
				$postData[$key] = htmlentities($postData[$key], ENT_QUOTES);
			}

			if ($postData["loc_id"]=='0') {

				$postData["users"]='0';
			}

			// genrate sample code, first time on sample reg. not on update
			//if inward_id exist in post data then this request is for update sample.
			//else saving first time.

			if ($this->Session->read('org_sample_code')==null) {

				$valid_sample_code=$this->Customfunctions->createSampleCode();
				$postData["stage_sample_code"] = $valid_sample_code;
			
			} else {

				$postData["stage_sample_code"] = $this->Session->read('org_sample_code');
			}
			
			$ref_src_code    = $postData['ref_src_code'];
			
			$src_usr_id      = $postData["users"];
			
			$src_loc_id      = $postData["loc_id"];

			$org_sample_code = $postData["stage_sample_code"];
			
			$sample_code1    = $postData["stage_sample_code"];
			
			$tran_date       = $postData["tran_date"];
			
		
			//print_r($category_code); exit;
			$workflow_data_with_SI	= array("org_sample_code"=>$org_sample_code, 
											"src_loc_id"=>$src_loc_id, 
											"src_usr_cd"=>$src_usr_id,
											"dst_loc_id"=>$_SESSION["posted_ro_office"],
											"dst_usr_cd"=>$_SESSION["user_code"],
											"user_code"=>$_SESSION["user_code"],
											"stage_smpl_cd"=>$sample_code1,
											"tran_date"=>$tran_date,
											"stage"=>"1",
											"stage_smpl_flag"=>"SI");

											
			$_SESSION["sample"] = $postData['sample_type_code'];
			
			$_SESSION["loc_id"] = $postData["loc_id"];

			$_SESSION["user"] = $postData["users"];
			
			$_SESSION["loc_user_id"] = $_SESSION["user_code"];

			$_SESSION["category_code"] = $postData["category_code"];


			if ($postData["loc_id"]=='Others' || $postData["loc_id"]=='0') {
				
				$_SESSION["loc_id"] ="0";
				$postData["loc_id"]=0;
				$postData['other_flag']='O';
				$postData["users"]='0';
				$postData["designation"]='0';
			
			} else {
				
				$postData['other_flag']='S';
				$postData['name']=null;
				$postData['address']=null;
			}



			//commonly creating variables for save/edit_sample
			$dStart = new \DateTime(date('Y-m-d H:i:s'));

			$date = $dStart->createFromFormat('d/m/Y', $postData['letr_date']);
			$letr_date = $date->format('Y/m/d');
			$letr_date = date('Y-m-d',strtotime($letr_date));

			$date1 = $dStart->createFromFormat('d/m/Y', $postData['received_date']);
			$received_date = $date1->format('Y/m/d');
			$received_date = date('Y-m-d',strtotime($received_date));

			$postData["org_sample_code"]=$postData['stage_sample_code'];
			
			$postData['user_code']=$_SESSION["user_code"];
			
			$stage_sample_code=$postData['stage_sample_code'];
			
			$postData['status_flag']='D';

			$this->loadModel('MSampleRegObs');

			//to save record
			if (null!==($this->request->getData('save'))) {
	
			
				//check post data validations
				$validate_err = $this->inwardPostValidations($this->request->getData());
				
				if ($validate_err != '') {

					$this->set('validate_err',$validate_err);
					return null;
				}

				//creating entity to save record in workflow table with status flag SI (first stage)
				$WorkflowEntity = $this->Workflow->newEntity($workflow_data_with_SI);
			
				$this->Workflow->save($WorkflowEntity);

				if ($_SESSION['user_flag'] =='RAL' || $_SESSION['user_flag'] =='CAL') {

					$dst = $this->DmiUsers->find('all',array('conditions'=>array('role'=>'RAL/CAL OIC','status'=>'active','posted_ro_office'=>$_SESSION['posted_ro_office'])))->first();

					if ($dst==null) {

						$message = 'Please create Office Incharge for respective RAL/CAL!!!';
						$message_theme = 'warning';
						$redirect_to = 'sample_inward';

					} else {

						$dst_usr_cd1 = $dst['id'];

						$workflow_data_with_SD = array("org_sample_code"=>$org_sample_code, 
													   "src_loc_id"=>$src_loc_id, 
													   "src_usr_cd"=>$src_usr_id,
													   "dst_loc_id"=>$_SESSION["posted_ro_office"],
													   "dst_usr_cd"=>$dst_usr_cd1,
													   "user_code"=>$_SESSION["user_code"],
													   "stage_smpl_cd"=>$sample_code1,
													   "tran_date"=>$tran_date,
													   "stage"=>"2",
													   "stage_smpl_flag"=>"SD");

						//creating entity to save record in workflow table with status flag SD (Second Stage), if sample reg. by CAL/RAL
						$WorkflowEntity = $this->Workflow->newEntity($workflow_data_with_SD);
						$this->Workflow->save($WorkflowEntity);
					}
				}

				
			

				//get inward id
				$get_inward = $this->SampleInward->find('all',array('fields'=>'inward_id','order'=>'inward_id desc'))->first();
				$inward_id = $get_inward['inward_id']+1;//for new inward

				//data array to be saved in sample inward table
				
				$dataArray = array(
					'inward_id'				=>	$inward_id,//this
					'loc_id'				=>	$postData['loc_id'],//this
					'stage_sample_code'		=>	$org_sample_code,//this
					'sample_type_code'		=>	$postData['sample_type_code'],//this
					'fin_year'				=>	$postData['fin_year'],//this
					'org_sample_code'		=>	$org_sample_code,//this
					'designation'			=>	$postData['designation'],
					'letr_ref_no'			=>	$postData['letr_ref_no'],
					'users'					=>	$postData['users'],
					'user_code'				=>	$postData['user_code'],
					'tran_date'				=>	$postData['tran_date'],
					'reject_date'			=>	$postData['reject_date'],
					'letr_date'				=>	$letr_date,
					'received_date'			=>	$received_date,
					'container_code'		=>	$postData['container_code'],
					'entry_flag'			=>	$postData['entry_flag'],
					'par_condition_code'	=>	$postData['par_condition_code'],
					'sam_condition_code'	=>	$postData['sam_condition_code'],
					'sample_total_qnt'		=>	$postData['sample_total_qnt'],
					'parcel_size'			=>	$postData['parcel_size'],
					'category_code'			=>	$postData['category_code'],
					'commodity_code'		=>	$postData['commodity_code'],
					'ref_src_code'			=>	$postData['ref_src_code'],
					'expiry_month'			=>	$postData["expiry_month"],
					'expiry_year'			=>	$postData["expiry_year"],
					'acc_rej_flg'			=>	$postData["acc_rej_flg"],
					'rej_code'				=>	$postData["rej_code"],
					'rej_reason'			=>	$postData["rej_reason"],
					'status_flag'			=>	$postData['status_flag'],
					'name'					=>	$postData['name'],
					'address'				=>	$postData['address'],
					'created'				=>	date('Y-m-d H:i:s')

				);
			
				$SampleInwardEntity = $this->SampleInward->newEntity($dataArray);

				if ($this->SampleInward->save($SampleInwardEntity)) {
			
					$inward = $this->SampleInward->find('all',array('fields'=>array('inward_id','org_sample_code'),'conditions'=>array('org_sample_code IS'=>$org_sample_code),'order'=>'inward_id desc'))->first();

					$inward_id = $inward['inward_id'];
					
					//store in session to use in edit mode
					$_SESSION["org_sample_code"] =$inward['org_sample_code'];

					if ($postData["acc_rej_flg"] == "A") {
						
						$_SESSION['stage_sample_code'] = $postData["stage_sample_code"];

					} else {

						$_SESSION['stage_sample_code'] ="";
					}

				
					//$this->Customfunctions->getSampleRegisterOffice($org_sample_code);
					$this->Customfunctions->sampleTypeInformation($org_sample_code);
					// For Maintaining Action Log by Akash (26-04-2022)
					$this->LimsUserActionLogs->saveActionLog('New Sample Saved','Success');														
					$message = 'You have successfully saved Sample Inward. Please note Sample Code is '.$org_sample_code;
					$message_theme = 'success';
					$redirect_to = 'fetch_inward_id/'.$inward_id;

				} else {

					// For Maintaining Action Log by Akash (26-04-2022)
					$this->LimsUserActionLogs->saveActionLog('Sample Save','Failed');
					$message = 'Sorry... Sample Registration Failed';
					$message_theme = 'failed';
					$redirect_to = 'sample_inward';
		
				}

			} elseif (null!==($this->request->getData('update'))) {
				
				//check post data validations
				$validate_err = $this->inwardPostValidations($this->request->getData());
				
				if ($validate_err != '') {

					$this->set('validate_err',$validate_err);
					return null;
				}

				
				//To Update selected registered sample, if id exist in post data
				
				if ($this->Session->read('org_sample_code')!=null) {

					$inward_id = $this->Session->read('inward_id');

					//below is package of 5 primary key fields, need to be unique to update the record
					//else it will save a new record
					$get_inward = $this->SampleInward->find('all',array('fields'=>array('inward_id','loc_id','stage_sample_code','fin_year','sample_type_code'),'conditions'=>array('org_sample_code'=>$this->Session->read('org_sample_code')),'order'=>'inward_id desc'))->first();
					$inward_id = $get_inward['inward_id'];
					$loc_id = $get_inward['loc_id'];
					$stage_sample_code = $get_inward['stage_sample_code'];
					$fin_year = $get_inward['fin_year'];
					$sample_type_code = $get_inward['sample_type_code'];

					//data array to be saved in sample inward table
					$dataArray = array(
						'inward_id'          =>  $inward_id,//this
						'loc_id'             =>  $loc_id,//this
						'stage_sample_code'  =>  $stage_sample_code,//this
						'sample_type_code'   =>  $sample_type_code,//this
						'fin_year'           =>  $fin_year,//this
						'org_sample_code'    =>  $org_sample_code,//this
						'designation'        =>  $postData['designation'],
						'letr_ref_no' 		 =>  $postData['letr_ref_no'],
						'users' 			 =>  $postData['users'],
						'user_code' 		 =>  $postData['user_code'],
						'tran_date' 		 =>  $postData['tran_date'],
						'reject_date' 		 =>  $postData['reject_date'],
						'letr_date' 		 =>  $letr_date,
						'received_date' 	 =>  $received_date,
						'container_code' 	 =>  $postData['container_code'],
						'entry_flag' 		 =>  $postData['entry_flag'],
						'par_condition_code' =>  $postData['par_condition_code'],
						'sam_condition_code' =>  $postData['sam_condition_code'],
						'sample_total_qnt' 	 =>  $postData['sample_total_qnt'],
						'parcel_size' 		 =>  $postData['parcel_size'],
						'category_code' 	 =>  $postData['category_code'],
						'commodity_code' 	 =>  $postData['commodity_code'],
						'ref_src_code' 	 	 =>  $postData['ref_src_code'],
						'expiry_month' 		 =>  $postData["expiry_month"],
						'expiry_year' 		 =>  $postData["expiry_year"],
						'acc_rej_flg' 		 =>  $postData["acc_rej_flg"],
						'rej_code' 			 =>  $postData["rej_code"],
						'rej_reason' 		 =>  $postData["rej_reason"],
						'status_flag' 		 =>  $postData['status_flag'],
						'name' 				 =>  $postData['name'],
						'address' 			 =>  $postData['address'],
						'modified' 			 =>  date('Y-m-d H:i:s')

					);

					$SampleInwardEntity = $this->SampleInward->newEntity($dataArray);

					//to update the sample details on registration window
					if ($this->SampleInward->save($SampleInwardEntity)) {

						$_SESSION['received_date'] = $postData["received_date"];

						if ($postData["acc_rej_flg"]=="A") {

							$_SESSION['stage_sample_code'] = $postData["stage_sample_code"];
							$_SESSION['received_date'] = $postData["received_date"];

						} else {

							$_SESSION['stage_sample_code'] = "";
						}

						$_SESSION['acc_rej_flg'] = $postData["acc_rej_flg"];

						// For Maintaining Action Log by Akash (26-04-2022)
						$this->LimsUserActionLogs->saveActionLog('New Sample Update','Success');
						$message = 'The Sample Inward has been updated successfully...!';
						$message_theme = 'success';
						$redirect_to = 'fetch_inward_id/'.$inward_id;//to open in edit mode

					} else {

						// For Maintaining Action Log by Akash (26-04-2022)
						$this->LimsUserActionLogs->saveActionLog('New Sample Update','Failed');
						$message = 'Sorry... The Sample Inward did not updated properly.';
						$message_theme = 'failed';
						$redirect_to = 'sample_inward';
					}
				}

			} elseif (null!==($this->request->getData('confirm'))) {

				$this->loadModel('DmiUsers');
				$this->loadModel('SampleInward');
				$conn = ConnectionManager::get('default');

				$org_sample_code = $this->Session->read('org_sample_code');

				$user_role = $this->SampleInward->find('all',array('fields'=>array('loc_id','users'),'conditions'=>array('org_sample_code'=>$this->Session->read('org_sample_code')),'order'=>'inward_id desc'))->first();
				
				$usercode = $user_role['users'];
				$user_role = $this->DmiUsers->find('all',array('fields'=>array('role'),'conditions'=>array('id IS'=>$usercode)))->first();


				//update status
				$this->SampleInward->updateAll(array('status_flag'=>'S'),array('org_sample_code'=>$org_sample_code));

				//get role and office where sample available after confirmed
				$query = $conn->execute("SELECT DISTINCT si.org_sample_code,w.dst_usr_cd,u.role,r.ro_office 
									     FROM sample_inward AS si
										 INNER JOIN workflow AS w ON si.org_sample_code=w.org_sample_code
										 INNER JOIN dmi_users AS u ON u.id=w.dst_usr_cd
										 INNER JOIN dmi_ro_offices AS r ON r.id=w.dst_loc_id 
										 WHERE si.org_sample_code='$org_sample_code'");

				$get_info = $query->fetchAll('assoc');
  				
				
				//call to the common SMS/Email sending method
				$this->loadModel('DmiSmsEmailTemplates');
				//SAMPLE INWARD CONFIRMED
				//FROM
				//$this->DmiSmsEmailTemplates->sendMessage(75,$get_info[0]['dst_usr_cd']);
				//To
				//$this->DmiSmsEmailTemplates->sendMessage(76,$get_info[1]['dst_usr_cd']);


				/* COMMENTED TEMORPILY
				if ($user_role['role'] == 'Inward Officer') {
					
					//message when Inward Officer confirmed the sample 
					$this->DmiSmsEmailTemplates->sendMessage(2000,$org_sample_code,$userCode=$get_info[0]['dst_usr_cd']);	
					//message  to the ral/cal oic
					$this->DmiSmsEmailTemplates->sendMessage(2001,$org_sample_code,$userCode=$get_info[1]['dst_usr_cd']);

				} elseif ($user_role['role'] == 'RAL/CAL OIC') {
	
					//sms template id RAL/CAL OIC confirmed the sample
					$this->DmiSmsEmailTemplates->sendMessage(2005,$org_sample_code,$userCode=$get_info[0]['dst_usr_cd']);
					
				} elseif ($user_role['role'] == 'Jr Chemist' || $user_role['role'] == 'Sr Chemist'){
					//Message When Chemist Confirmed the Sample
					$this->DmiSmsEmailTemplates->sendMessage(2015,$org_sample_code,$userCode=$get_info[0]['dst_usr_cd']);
					//To Message
					$this->DmiSmsEmailTemplates->sendMessage(2017,$org_sample_code,$userCode=$get_info[1]['dst_usr_cd']);

				} elseif ($user_role['role'] == 'Lab Incharge') {
					//Message When Lab Incharge
					$this->DmiSmsEmailTemplates->sendMessage(2018,$org_sample_code,$userCode=$get_info[1]['dst_usr_cd']);
					//To Message
					$this->DmiSmsEmailTemplates->sendMessage(2019,$org_sample_code,$userCode=$get_info[0]['dst_usr_cd']);

				}
				*/
				
				
				// For Maintaining Action Log by Akash (26-04-2022)
				$this->LimsUserActionLogs->saveActionLog('New Sample Confirmed','Success');
				$message = 'Sample Code '.$org_sample_code.' has been Confirmed and Available to "'.$get_info[0]['role'].' ('.$get_info[0]['ro_office'].' )"';
				$message_theme = 'success';
				$redirect_to = 'confirmed_samples';


			}

		}
		// set variables to show popup messages from view file
		$this->set('message',$message);
		$this->set('message_theme',$message_theme);
		$this->set('redirect_to',$redirect_to);
	}



/****************************************************************************************************************************************************************************************************************************************************************/	

	//to show list of saved sample by current user
	public function savedSamples(){

		$user_flag = $this->Session->read('user_flag');

		$sampleArray = $this->getSavedSamplesList();
		
		$this->set(compact('sampleArray','user_flag'));
	}


/****************************************************************************************************************************************************************************************************************************************************************/	



/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--------<Get Saved Samples List>-------->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
	
	//created common function to fetch list , to be used for dashboard counts also, on 28-04-2021 by Amol
	public function getSavedSamplesList(){
		
		$conn = ConnectionManager::get('default');
		$user_cd = $this->Session->read('user_code');
		$loc_id = $this->Session->read('posted_ro_office');
		$user_flag = $this->Session->read('user_flag');

		if ($user_flag=='RO' || $user_flag=='SO') {

			//get all samples from workflow table with status (SI.SD) by current user
			$this->loadModel('Workflow');
			$workflowData = $this->Workflow->find('all',array('fields'=>array('org_sample_code'),'conditions'=>array('src_usr_cd IS'=>$user_cd,'src_loc_id IS'=>$loc_id),'group'=>array('org_sample_code')))->toArray();

			//creating array of values for each of the sample code regt. but not confirmed
			$sampleArray = array();
			$i=0;
			
			foreach ($workflowData as $each_sample) {

				//check the sample is not confirmed
				$this->loadModel('SampleInward');
				$getInward = $this->SampleInward->find('all',array('fields'=>array('org_sample_code','received_date','inward_id','status_flag'),'conditions'=>array('org_sample_code IS'=>$each_sample['org_sample_code']),'order'=>'inward_id desc'))->first();

				//det inward, if saved

				if (!empty($getInward) && trim($getInward['status_flag'])=='D') {

					$sampleArray[$i]['received_date'] = $getInward['received_date'];
					$sampleArray[$i]['inward_id'] = $getInward['inward_id'];
					$sampleArray[$i]['org_sample_code'] = $each_sample['org_sample_code'];

				}

				//get sample details, if saved
				$this->loadModel('SampleInwardDetails');
				$getDetails = $this->SampleInwardDetails->find('all',array('fields'=>array('org_sample_code','smpl_drwl_dt','id'),'conditions'=>array('org_sample_code IS'=>$each_sample['org_sample_code']),'order'=>'id desc'))->first();

					if (!empty($getInward)) {

						if (trim($getInward['status_flag'])=='D') {

							if (!empty($getDetails)) {

								$sampleArray[$i]['smpl_drwl_dt'] = $getDetails['smpl_drwl_dt'];
								$sampleArray[$i]['id'] = $getDetails['id'];
								$sampleArray[$i]['org_sample_code'] = $each_sample['org_sample_code'];

							}

						}
					
					} else {

						if (!empty($getDetails)) {

							$sampleArray[$i]['smpl_drwl_dt'] = $getDetails['smpl_drwl_dt'];
							$sampleArray[$i]['id'] = $getDetails['id'];
							$sampleArray[$i]['org_sample_code'] = $each_sample['org_sample_code'];

						}
					}

					if (!empty($sampleArray[$i]['org_sample_code'])) {

						$i=$i+1;
					}
			}

		} else {

		 	//if user flag is CAL/RAL

			$query = $conn->execute("SELECT si.inward_id, si.stage_sample_code, si.received_date, 
											si.letr_date, si.org_sample_code, si.expiry_month, si.expiry_year, 
											st.sample_type_desc, ct.container_desc, pc.par_condition_desc, 
											sc.sam_condition_desc, mcc.category_name, mc.commodity_name, 
											ml.ro_office, si.sample_total_qnt, si.acc_rej_flg, si.rej_reason, si.rej_code, si.users
									FROM sample_inward AS si
									INNER JOIN m_sample_type AS st ON si.sample_type_code=st.sample_type_code
									INNER JOIN m_container_type AS ct ON si.container_code=ct.container_code
									INNER JOIN m_par_condition AS pc ON si.par_condition_code=pc.par_condition_code
									INNER JOIN m_sample_condition AS sc ON si.sam_condition_code=sc.sam_condition_code
									INNER JOIN m_commodity_category AS mcc ON si.category_code=mcc.category_code
									INNER JOIN dmi_ro_offices AS ml ON ml.id=si.loc_id
									INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code AND si.display='Y' AND  si.status_flag IN('D','AS') 
									AND si.user_code='$user_cd' AND acceptstatus_flag IN('R','N') 
									GROUP BY si.inward_id, si.stage_sample_code, si.received_date, si.letr_date, 
											 si.org_sample_code, si.expiry_month, si.expiry_year, st.sample_type_desc, 
											 ct.container_desc, pc.par_condition_desc, sc.sam_condition_desc, 
											 mcc.category_name, mc.commodity_name, ml.ro_office, si.sample_total_qnt,
											 si.acc_rej_flg, si.rej_reason, si.rej_code, si.users  
									ORDER BY si.received_date DESC");

			$sampleArray = $query ->fetchAll('assoc');
		}
		
		return $sampleArray;
	}


/****************************************************************************************************************************************************************************************************************************************************************/

	
	//to show list of confirmed sample by current user
	public function confirmedSamples(){

		$conn = ConnectionManager::get('default');
		$user_cd=$this->Session->read('user_code');

		$query = $conn->execute("SELECT si.inward_id, si.stage_sample_code, si.received_date,
										si.letr_date, si.org_sample_code, si.expiry_month,
										si.expiry_year, st.sample_type_desc, ct.container_desc,
										pc.par_condition_desc, sc.sam_condition_desc, mcc.category_name,
										mc.commodity_name, ml.ro_office, si.sample_total_qnt, si.acc_rej_flg,
										si.rej_reason, si.rej_code, si.users
								 FROM sample_inward AS si
								 INNER JOIN m_sample_type AS st ON si.sample_type_code=st.sample_type_code
								 INNER JOIN m_container_type AS ct ON si.container_code=ct.container_code
								 INNER JOIN m_par_condition AS pc ON si.par_condition_code=pc.par_condition_code
								 INNER JOIN m_sample_condition AS sc ON si.sam_condition_code=sc.sam_condition_code
								 INNER JOIN m_commodity_category AS mcc ON si.category_code=mcc.category_code
								 INNER JOIN dmi_ro_offices AS ml ON ml.id=si.loc_id
								 INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code AND si.display='Y'  
								 AND si.status_flag not IN('D')  AND acc_rej_flg IN ('P','R','A') AND si.user_code='$user_cd' 
								 GROUP BY si.inward_id, si.stage_sample_code, si.received_date,
								 		  si.letr_date, si.org_sample_code, si.expiry_month, si.expiry_year,
										  st.sample_type_desc, ct.container_desc, pc.par_condition_desc,
										  sc.sam_condition_desc, mcc.category_name, mc.commodity_name,
										  ml.ro_office, si.sample_total_qnt, si.acc_rej_flg,
										  si.rej_reason, si.rej_code, si.users  
								ORDER BY si.received_date DESC");

		$res = $query ->fetchAll('assoc');
		$this->set('res',$res);
	}


/****************************************************************************************************************************************************************************************************************************************************************/


	//to generate sample slip pdf
	public function getSampleSlip($sample_code){

		$this->viewBuilder()->setLayout('pdf_layout');
		$conn = ConnectionManager::get('default');

		$this->loadModel('SampleInward');
		$query	= $conn->execute("SELECT m_par_condition.par_condition_desc, m_sample_condition.sam_condition_desc,
										 m_sample_type.sample_type_desc, sample_inward.org_sample_code,
										 sample_inward.users, sample_inward.ref_src_code, m_commodity_category.category_name,
										 m_commodity.commodity_name, sample_inward.fin_year, sample_inward.loc_id,
										 m_container_type.container_desc, sample_inward.inward_id, sample_inward.stage_sample_code,
										 sample_inward.parcel_size, sample_inward.sample_total_qnt, sample_inward.letr_ref_no,
										 sample_inward.letr_date, sample_inward.received_date, sample_inward.designation,
										 sample_inward.stage_sample_code, sample_inward.expiry_month, sample_inward.expiry_year,
										 sample_inward.acc_rej_flg, sample_inward.status_flag, dmi_ro_offices.ro_office,
										 m_unit_weight.unit_weight, m_phy_apperance.phy_appear_desc
									FROM sample_inward
									INNER JOIN m_phy_apperance ON (m_phy_apperance.phy_appear_code = sample_inward.entry_flag)
									INNER JOIN m_sample_condition ON (m_sample_condition.sam_condition_code=sample_inward.sam_condition_code)
									INNER JOIN m_par_condition ON (m_par_condition.par_condition_code=sample_inward.par_condition_code)
									INNER JOIN m_sample_type ON (m_sample_type.sample_type_code=sample_inward.sample_type_code)
									INNER JOIN dmi_ro_offices ON (sample_inward.loc_id=dmi_ro_offices.id)
									INNER JOIN m_unit_weight ON (m_unit_weight.unit_id=sample_inward.parcel_size)
									INNER JOIN m_container_type ON sample_inward.container_code=m_container_type.container_code
									INNER JOIN m_commodity_category ON (m_commodity_category.category_code=sample_inward.category_code)
									INNER JOIN m_commodity ON (m_commodity.commodity_code=sample_inward.commodity_code) 
									AND sample_inward.stage_sample_code='$sample_code'");

		$sample_data = $query->fetchAll('assoc');

		//to get designation
		$designation = $sample_data[0]['designation'];
		$query = $conn->execute("SELECT role_name FROM user_role WHERE role_code='$designation'");
		$des_Arr = $query->fetchAll('assoc');

		$sample_data[0]['designation'] 	= $des_Arr[0]['role_name'];

		$this->set('sample_data',$sample_data);

		//call to the pdf creaation common method
		$this->callTcpdf($this->render(),'I');
	}

/****************************************************************************************************************************************************************************************************************************************************************/

	public function getCommodityCategoryById() {
		
		$str="";
		$this->loadModel('MCommodity');
		$this->loadModel('MCommodityCategory');
		$category_code = $_POST['category_code'];

		if (!is_numeric($category_code)) {

			echo "[error]~Enter a proper Commodity Category.";
			exit;
		}

		$conditions = array('category_code' => $this->request->getData('category_code'));

		if ($this->MCommodity->hasAny($conditions)) {
			//do something
		} else {

			echo "[error]~Invalid Category code";
			exit;
		}

		$count = $this->MCommodityCategory->find('count',array('conditions'=>array('category_code IS' => $category_code, 'display' => 'Y')));

		if ($count>0) {

			$category = $this->MCommodity->find('all', array('order'=>'commodity_name asc','conditions'=>array('category_code IS'=>$category_code,'display'=>'Y')));

			foreach ($category as $category) {
				
				$str.="<option value='".$category['commodity_code']."'>".$category['commodity_name']."</option>";
			}
			
			echo $str;
			exit;
		} else {

			$this->Session->setFlash(__('Enter a proper Commodity Category..'));
		}
	}

/****************************************************************************************************************************************************************************************************************************************************************/

	public function getUsersByLocId(){

		$conn = ConnectionManager::get('default');
		$this->loadModel('Users');
		$this->loadModel('DmiUsers');

		$loc_id		= $_POST['loc_id'];
		$role_code	= $_POST['role_code'];

		if (!isset($_POST['loc_id']) || !is_numeric($_POST['loc_id'])) {

			echo "[error]~Invalid location code.";
			exit;
		}

		if (!isset($_POST['role_code'])|| !is_numeric($_POST['role_code'])) {

			echo "[error]~Invalid Role code.";
			exit;
		}

		$query = $conn->execute("SELECT du.id,du.f_name,du.l_name 
								 FROM dmi_users AS du
								 INNER JOIN user_role AS r ON r.role_name=du.role 
								 WHERE du.status!='disactive' 
								 AND du.posted_ro_office=".$loc_id." and role_code=".$role_code);

		$usersnm = $query->fetchAll('assoc');

		echo '~'.json_encode($usersnm).'~';
		exit;

	}


/****************************************************************************************************************************************************************************************************************************************************************/
	
	//to get user degination from selction of location dropdown
	public function getDesignationByLocId() {

		$conn = ConnectionManager::get('default');
		
		$this->loadModel('DmiUsers');
		
		$loc_id	= $_POST['loc_id'];
		
		$role = $this->Session->read('role');

		if (!is_numeric($loc_id) || !is_numeric($_POST['loc_id']) || $loc_id=='') {
			
			echo "[error]~Invalid code";
			exit;
		}

		//taking id and role name from user_role table
		if ($role=='Inward Officer') {

			$query = $conn->execute("SELECT DISTINCT(role_code) AS id,role 
									 FROM dmi_user_roles  AS u
									 INNER JOIN dmi_users AS du ON du.email=u.user_email_id
									 INNER JOIN user_role AS r ON r.role_name=du.role 
									 WHERE posted_ro_office=$loc_id 
									 AND role IN('Inward Officer','RO Officer','SO Officer')");

		}else{

			$query = $conn->execute("SELECT DISTINCT(role_code) AS id,role FROM dmi_user_roles AS u
									 INNER JOIN dmi_users AS du ON du.email=u.user_email_id
									 INNER JOIN user_role AS r ON r.role_name=du.role  
									 WHERE posted_ro_office=$loc_id 
									 AND role='$role'");

		}

		$usersnm = $query->fetchAll('assoc');

		echo '~'.json_encode($usersnm).'~';
        exit;
	}

/****************************************************************************************************************************************************************************************************************************************************************/
	
	//function to take post data and validate each field.
	public function inwardPostValidations($postData) {

		$validation_status = '';

		if (!is_numeric($postData["loc_id"])){

			$validation_status = 'Select proper Location';
		}

		if (!is_numeric($postData["sample_type_code"])) {
			
			$validation_status = 'Invalid Sample Type';
		}

		if (!empty($postData["fin_year"])) {

			$res = preg_match('/(\d{4})-(\d{4})/',$postData["fin_year"]);
			
			if ($res==0) {

				$validation_status = 'Invalid Financial Year';
			}
		}

		if (!empty($postData["designation"])) {

			if (!is_numeric($postData["designation"])) {

				$validation_status = 'Invalid Designation';
			}
		}

		if (strlen($postData["letr_ref_no"])>40) {

			$validation_status = 'Invalid Letter Ref. No.';
		}

		if (!empty($postData["users"])) {

			if (!is_numeric($postData["users"])) {

				$validation_status = 'Invalid User code';
			}
		}

		if (!is_numeric($postData["user_code"])) {

			$validation_status = 'Invalid User code';
		}

		if (!empty($postData["tran_date"])) {

			$res = preg_match('/(\d{4})-(\d{2})-(\d{2})/',$postData["tran_date"]);
			
			if ($res==0) {

				$validation_status = 'Invalid Transaction date';
			}
		}

		$res = preg_match('/(\d{2})\/(\d{2})\/(\d{4})/',$postData["letr_date"]);
		
		if ($res==0) {

			$validation_status = 'Invalid Letter date';
		}

		$res = preg_match('/(\d{2})\/(\d{2})\/(\d{4})/',$postData["received_date"]);
		
		if ($res==0) {

			$validation_status = 'Invalid Inward date';
		}

		if (!is_numeric($postData["container_code"])) {

			$validation_status = 'Invalid Container code';
		}

		if (!is_numeric($postData["entry_flag"])) {

			$validation_status = 'Invalid Physical Appearance code';
		}

		if (!is_numeric($postData["par_condition_code"])) {

			$validation_status = 'Invalid parcel condition code';
		}

		if (!is_numeric($postData["sam_condition_code"])) {

			$validation_status = 'Invalid Sample condition code';
		}

		if (!is_numeric($postData["sample_total_qnt"])) {

			$validation_status = 'Invalid Quantity';
		}

		if (!is_numeric($postData["parcel_size"])) {

			$validation_status = 'Invalid Quantity Unit';
		}

		if (!is_numeric($postData["category_code"])) {

			$validation_status = 'Invalid Commodity Category';
		}

		if (!is_numeric($postData["commodity_code"])) {

			$validation_status = 'Invalid Commodity';
		}

		if (strlen($postData["ref_src_code"])>10) {

			$validation_status = 'Invalid Ref.Src. Code';
		}

		if (!is_numeric($postData["expiry_month"])) {

			$validation_status = 'Invalid Expiry Month';
		}

		if (!is_numeric($postData["expiry_year"])) {

			$validation_status = 'Invalid Expiry Year';
		}

		if (!in_array($postData["acc_rej_flg"],array("A","R","P"))) {

			$validation_status = 'Invalid Status Selected';
		}

		if ($postData["acc_rej_flg"]=='R') {

			if (!is_numeric($postData["rej_code"])) {

				$validation_status = 'Invalid Reject Reason';
			}

			if (strlen($postData["rej_reason"])>100) {

				$validation_status = 'Invalid Reject Remark';
			}
		}

		if (!empty($postData["name"])) {

			if (strlen($postData["name"])>30) {

				$validation_status = 'Enter Proper Name';
			}
		}

		if (!empty($postData["address"])) {

			if (strlen($postData["address"])>50) {
				
				$validation_status = 'Enter Proper Address';
			}
		}


		return $validation_status;

	}

	public function payment(){			

		$this->viewBuilder()->setLayout('admin_dashboard');
		
		$sample_code = $this->Session->read('org_sample_code');
		$this->loadModel('SampleInward');

		$sample_payment_details = array();
		$sample_inward_form_status = '';
	 		
		if (!empty($sample_code)) {

			$sample_payment_details = $this->SampleInward->find('all',array('conditions'=>array('org_sample_code IS'=>$sample_code),'order'=>'inward_id desc'))->first();
			$this->set('sample_payment_details',$sample_payment_details);
			
			//for progress bar
			$sample_inward_form_status = 'saved';

			//get selected commdity from category id
			$this->loadModel('MCommodity');
			
			$commodity_list = $this->MCommodity->find('list',array('keyField'=>'commodity_code','valueField'=>'commodity_name','conditions'=>array('category_code IS'=>$sample_payment_details['category_code'])));

		} else {

			
			$commodity_list = array(); 
		}

		//to show/hide Confirm btn on form
		$confirmBtnStatus = $this->Customfunctions->showHideConfirmBtn(); //print_r($confirmBtnStatus); exit;
		$this->set('confirmBtnStatus',$confirmBtnStatus);
		



			if (!empty($this->Customfunctions->checkSampleIsSaved('sample_inward',$this->Session->read('org_sample_code')))) {			
		
				$sample_inward_form_status = 'saved';
			
			} else {
				
				$sample_inward_form_status = '';
			}
			
			$this->set('sample_inward_form_status',$sample_inward_form_status);
					
		if (!empty($sample_code)) {
			
			// set variables to show popup messages from view file
			$message_theme = ''; 
			$message = '';
			$redirect_to = '';
			
			//Check Sample Information if its commercial or challenged sample	
			$SampleInformation = $this->Customfunctions->sampleTypeInformation($sample_code);
		
			$this->set('SampleInformation',$SampleInformation);
			
			$office_type = $this->Customfunctions->getSampleRegisterOffice($sample_code);			
			$this->set('form_type',$office_type);	
				
			//$section_details = $this->DmiCommonScrutinyFlowDetails->currentSectionDetails($application_type,$office_type,$firm_type,$form_type,1);
			//$allSectionDetails = $this->DmiCommonScrutinyFlowDetails->allSectionList($application_type,$office_type,$firm_type,$form_type);
			// get previous and next button id 
			//$previousBtn =	$this->Customfunctions->getNextPreSec($allSectionDetails);				
			//$previous_button_url = 'application/section/'.$previousBtn[2];
			// For change flow
			// if return value 1 (all forms saved), return value 2 (all forms approved), return value 0 (all forms not saved or approved)
			//$all_section_status = $this->Customfunctions->formStatusValue($allSectionDetails,$customer_id);
			//$payment_table = $this->DmiFlowWiseTablesLists->getFlowWiseTableDetails($application_type,'payment');				
			//$final_submit_details = $this->Customfunctions->finalSubmitDetails($customer_id,'application_form');			
			//$this->set('final_submit_details',$final_submit_details);		
			//$progress_bar_status = $this->Progressbar->formsProgressBarStatus($allSectionDetails,$customer_id);
			//$this->set('progress_bar_status',$progress_bar_status);
			//$firm_detail = $this->DmiChangeFirms->sectionFormDetails($customer_id);
			//$firm_details = $firm_detail[0];
			//$this->set('firm_details',$firm_details);
			
			//for sample details progress bar
			if (!empty($this->Customfunctions->checkSampleIsSaved('sample_details',$this->Session->read('org_sample_code')))) {

				$sample_details_form_status = 'saved';
		
			} else {
			
				$sample_details_form_status = '';
			}
		
			$this->set('sample_details_form_status',$sample_details_form_status);

	
			$PaymentDetails = $this->Customfunctions->fetchSamplePaymentDetails($sample_code,$office_type);
			$this->set('PaymentDeatils',$PaymentDetails);
			
			$this->loadModel('LimsSamplePaymentDetails');
			$this->loadModel('MCommodity');
			$this->loadModel('MCommodityCategory');

		//To Fetch Record Data To Show in Update/View Mode
		$sample_Details_data=array();
		$SaveUpdatebtn = 'save';
		$sample_Details_data = $this->LimsSamplePaymentDetails->find('all',array('conditions'=>array('sample_code IS'=>$this->Session->read('org_sample_code')),'order'=>'id desc'))->first();
			
			if (!empty($sample_Details_data)) {

				//For Progress-Bar
				$sample_details_form_status='saved';
				$SaveUpdatebtn = 'update';

			} else {
			}

			$sample_charge = $this->Customfunctions->samplePaymentCharges($sample_code);
			$this->set('sample_charge',$sample_charge);
			

			$lims_sample_payment_id = $this->LimsSamplePaymentDetails->find('list', array('valueField'=>'id','conditions'=>array('sample_code IS'=>$sample_code)))->toArray();
			if (!empty($lims_sample_payment_id)) { 
				$process_query = 'Updated'; 
			} else { 
				$process_query = 'Saved'; 
			}
					
			$sub_commodity_id = $SampleInformation['commodity_code'];
	
			if (!empty($SampleInformation['commodity_code'])) {
	
					//Commodity
					$Commodity = $this->MCommodity->find('all',array('conditions'=>array('commodity_code IS'=>$sub_commodity_id)))->first();
					$this->set('Commodity',$Commodity);
		
					//Commodity
					$Category = $this->MCommodityCategory->find('all',array('conditions'=>array('category_code IN'=>$Commodity['category_code'], 'display'=>'Y')))->toArray();
					$this->set('Category',$Category);

			}	
		
		/*	if (!empty($firm_details['packaging_materials'])) {

				$this->loadModel('DmiPackingTypes');
				$packaging_materials = explode(',',$firm_details['packaging_materials']);				 
				$packaging_type = $this->DmiPackingTypes->find('list', array('valueField'=>'packing_type', 'conditions'=>array('id IN'=>$packaging_materials)));			 
				$this->set('packaging_type',$packaging_type);	
			} */
			
		/*	if (!empty($final_submit_details)) {

				$final_submit_status = $final_submit_details['status'];
			
			} else {

				$final_submit_status = 'no_final_submit';
			}

			$this->set('final_submit_status',$final_submit_status); */
			
			// set variables to show popup messages from view file
			//$this->set('previous_button_url',$previous_button_url);
			//$this->set('allSectionDetails',$allSectionDetails);
			//$this->set('all_section_status',$all_section_status);
			//$this->set('section_details',$section_details);
			
			if (null !== ($this->request->getData('confirm'))) {
				
				//applied this condition on 26-03-2018 by Amol, with esign or without
				if (!empty($this->request->getData('once_no'))) {

					//calling common function for esigning//applied on 01-11-2017 by Amol
					//$this->process_to_esign($customer_id);
					//print_r("hi"); exit;
				
				} else {

					//proceed without esign
					$this->Session->write('with_esign','no');
					$final_submit_call_result =  $this->Customfunctions->applicationFinalSubmitCall($customer_id,$all_section_status);

					if ($final_submit_call_result == true) {

						$message_theme = 'success';
						$message = $sample_code.' - Final submitted successfully ';
						$redirect_to = '../applicationformspdfs/'.$section_details['forms_pdf'];
						$this->viewBuilder()->setVar('message', $message);
						$this->viewBuilder()->setVar('redirect_to', $redirect_to);								
					
					} else {

						$message_theme = 'failed';
						$message = $sample_code.' - All Sections not filled, Please fill all Section and then Final Submit ';
						$redirect_to = '../application/application-for-certificate';
						$this->viewBuilder()->setVar('message', $message);
						$this->viewBuilder()->setVar('redirect_to', $redirect_to);									
					}		
														
					$this->render('/element/message_boxes');
				}
								

			} elseif (null !== ($this->request->getData('save'))) {  

				// Save payment details by applicant (done by pravin 13/10/2017)		
				$get_payment_details = $this->Customfunctions->saveSamplePaymentDetails($this->request->getData());
				
				if ($get_payment_details == true) {
					
					$message_theme = 'success';
					$message = $sample_code.' - Payment Section, '.$process_query.' successfully';
					$redirect_to = 'payment';
					$this->viewBuilder()->setVar('message', $message);
					$this->viewBuilder()->setVar('redirect_to', $redirect_to);	
					$this->render('/element/message_boxes');
				}				
			
			}

			$this->set('message_theme',$message_theme);
			$this->set('message',$message);
			$this->set('redirect_to',$redirect_to);			
		}

	}


}
?>
