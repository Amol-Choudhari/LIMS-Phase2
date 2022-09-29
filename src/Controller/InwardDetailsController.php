<?php
namespace App\Controller;

use Cake\Event\Event;
use App\Network\Email\Email;
use Cake\ORM\Entity;
use Cake\Datasource\ConnectionManager;

class InwardDetailsController extends AppController {

	var $name 		= 'InwardDetails';

	public function initialize(): void {

		parent::initialize();
		$this->viewBuilder()->setLayout('admin_dashboard');
		$this->viewBuilder()->setHelpers(['Form','Html']);
		$this->loadComponent('Customfunctions');
		$this->loadComponent('Paymentdetails');
		$this->loadModel('LimsSamplePaymentDetails');
	}

/****************************************************************************************************************************************************************************************************************************************************************/

	public function errormsg(){}

/****************************************************************************************************************************************************************************************************************************************************************/

	//TO VALIDATE LOGIN USER
	public function authenticateUser() {

		$this->loadModel('DmiUserRoles');
		$user_access = $this->DmiUserRoles->find('all',array('conditions'=>array('user_email_id IS'=>$this->Session->read('username'))))->first();

		if (!empty($user_access)) {
			//proceed
		} else {

			echo "Sorry.. You don't have permission to view this page.."; ?><a href="<?php echo $this->request->getAttribute('webroot');?>users/login_user">	Please Login</a><?php
			exit;
		}
	}

/****************************************************************************************************************************************************************************************************************************************************************/

	//TO OPEN SAMPLE INWARD FORM in EDIT MODE
	public function fetchInwardId($id) {

		//Get Original Sample Code by Inward Id
		$this->loadModel('SampleInwardDetails');
		$get_sample_code = $this->SampleInwardDetails->find('all',array('fields'=>'org_sample_code', 'conditions'=>array('id IS'=>$id)))->first();

		$this->Session->write('inward_id',$get_sample_code['inward_id']);
		$this->Session->write('org_sample_code',$get_sample_code['org_sample_code']);

		$this->redirect('/InwardDetails/sample_inward_details');
	}
  


/****************************************************************************************************************************************************************************************************************************************************************/


	//SAMPLE INWARD DETAILS METHOD
	public function sampleInwardDetails(){

		$this->authenticateUser();

		//Load MODELS
		$this->loadModel('SampleInward');
		$this->loadModel('Workflow');
		$this->loadModel('DmiUsers');
		$this->loadModel('DmiUserRoles');
		$this->loadModel('SampleInwardDetails');

		//Variables To Show the Message From View Files
		$message = '';
		$message_theme = '';
		$redirect_to = '';

		
		//payment progress
		if (isset($_SESSION['sample'])) {
			if ($_SESSION['sample'] == 3) { $_SESSION['is_payment_applicable'] = 'yes';}
		}

	
		//To Execute Query in Core Format
		$conn = ConnectionManager::get('default');
  		$user_flag = $_SESSION['user_flag'];
		
		//To Show List of Sample Type From Database
		$this->loadModel('MSampleType');
		$sam_type = $this->MSampleType->find('list',array('keyField'=>'sample_type_code','valueField'=>'sample_type_desc','order' => array('sample_type_desc' => 'ASC'),'conditions' => array('display' => 'Y')))->toArray();
		$this->set('Sample_Type',$sam_type);

		//To Show Sample Drawing Locations
		$drawal_locations = array('P'=>'Premises','M'=>'Market','O'=>'Other');
		$this->set('drawal_locations',$drawal_locations);

		//To Fetch Record Data To Show in Update/View Mode
		$sample_Details_data=array();
		$SaveUpdatebtn = 'save';
		$sample_Details_data = $this->SampleInwardDetails->find('all',array('conditions'=>array('org_sample_code IS'=>$this->Session->read('org_sample_code')),'order'=>'id desc'))->first();
	
		if (!empty($sample_Details_data)) {

			//For Progress-Bar
			$sample_details_form_status='saved';
			$SaveUpdatebtn = 'update';
			$_SESSION['sample'] = $sample_Details_data['sample_type_code'];
			
		} else {

			//Default Value Set To '' Blank
			$sample_Details_data['loc_id']='';
			$sample_Details_data['org_sample_code']='';
			$sample_Details_data['sample_type_code']='';
			$sample_Details_data['smpl_drwl_dt']='';
			$sample_Details_data['drawal_loc']='';
			$sample_Details_data['shop_name']='';
			$sample_Details_data['shop_address']='';
			$sample_Details_data['mnfctr_nm']='';
			$sample_Details_data['mnfctr_addr']='';
			$sample_Details_data['pckr_nm']='';
			$sample_Details_data['pckr_addr']='';
			$sample_Details_data['grade']='';
			$sample_Details_data['tbl']='';
			$sample_Details_data['pack_size']='';
			$sample_Details_data['remark']='';
			$sample_Details_data['lot_no']='';
			$sample_Details_data['no_of_packets']='';
			$sample_Details_data['category_code']='';
			$sample_Details_data['commodity_code']='';
			$sample_Details_data['ref_src_code']='';
			$sample_Details_data['expiry_month']='';
			$sample_Details_data['expiry_year']='';
			$sample_Details_data['acc_rej_flg']='';
			$sample_Details_data['rej_code']='';
			$sample_Details_data['rej_reason']='';

			//For Progress-Bar
			$sample_details_form_status='';

		}

		//Get Status Flag
		$get_status = $this->SampleInward->find('all',array('conditions' => array('org_sample_code IS'=>$this->Session->read('org_sample_code')),'order'=>'inward_id desc'))->first();

		if (!empty($get_status)) {

			$sample_Details_data['status_flag'] = $get_status['status_flag'];
			$inward_id = $get_status['inward_id'];

		} else {

			$sample_Details_data['status_flag']='';
			$inward_id = '000';//if sample details form saved first, then on sample inward reg. this field will be updated with same sample code on sample details.
		}

		$this->set('SaveUpdatebtn',$SaveUpdatebtn);
		$this->set('sample_Details_data',$sample_Details_data);
		$this->set('sample_details_form_status',$sample_details_form_status);

		//For Sample Details Progress-Bar
		if (!empty($this->Customfunctions->checkSampleIsSaved('sample_inward',$this->Session->read('org_sample_code')))) {
			$sample_inward_form_status = 'saved';
			$inward_section = 'Y';
		} else {
			$sample_inward_form_status = '';
			$inward_section = '';
		}
	
		//for paymnet progress bar
		if (!empty($this->Customfunctions->checkSampleIsSaved('payment_details',$this->Session->read('org_sample_code')))) {
			
			$payment_details = $this->LimsSamplePaymentDetails->find('all')->select('payment_confirmation')->where(['sample_code IS'=>$this->Session->read('org_sample_code')])->order(['id desc'])->first();
			$payment_details_form_status = trim($payment_details['payment_confirmation']);
			$payment_section = 'Y';

		} else {
			$payment_details_form_status = '';
			$payment_section = '';
		}
	
		$this->set('sample_inward_form_status',$sample_inward_form_status);
		$this->set('payment_details_form_status',$payment_details_form_status);

		//To Show/Hide Confirm BUTTON on Form
		$confirmBtnStatus = $this->Customfunctions->showHideConfirmBtn();
		$this->set('confirmBtnStatus',$confirmBtnStatus);


		if ($this->request->is('post')) {

			//HTML Encoding
			$postData = $this->request->getData();

			foreach ($postData as $key => $value) {

				$postData[$key] = htmlentities($postData[$key], ENT_QUOTES);
			}

			//Create Sample Code
			if ($this->Session->read('org_sample_code') == null) {

				$org_sample_code = $this->Customfunctions->createSampleCode();

			} else {

				$org_sample_code = $this->Session->read('org_sample_code');
			}

			if (null!==($this->request->getData('save'))) {

				//Check POST Data Validations
				$validate_err = $this->detailsPostValidations($this->request->getData());

				if ($validate_err != '') {

					$this->set('validate_err',$validate_err);
					return null;
				}

				$_SESSION["sample"] = $postData['sample_type_code'];

				$replica_serial_no=array();

				for ($i=1;$i<=$postData['no_of_packets'];$i++) {

					$replica_serial_no[$i]=$postData['replica_serial_no'.$i];

				}

				$postData['replica_serial_no'] = implode(",", $replica_serial_no);

				if ($postData['smpl_drwl_dt']!='') {

					$dStart = new \DateTime(date('Y-m-d H:i:s'));

					$date = $dStart->createFromFormat('d/m/Y', $postData['smpl_drwl_dt']);

					$smpl_drwl_dt = $date->format('Y/m/d');

					$smpl_drwl_dt = date('Y-m-d',strtotime($smpl_drwl_dt));

					$postData['smpl_drwl_dt'] = $smpl_drwl_dt;

				} else {

					$postData['smpl_drwl_dt'] = "";
				}

				$dst = $this->DmiUsers->find('all',array('conditions'=>array('role IN'=>array('RO/SO OIC','RAL/CAL OIC'),'status'=>'active','posted_ro_office'=>$_SESSION['posted_ro_office'])))->first();
	
					

				if ($this->request->getData('sample_type_code') == '3') {
					$isPaymentApplicable = 'Yes';
				} else {
					$isPaymentApplicable = 'No';
				}

				if ($dst==null) {

					$message = 'Please create Office Incharge!!!';
					$message_theme = 'warning';
					$redirect_to = 'sample_inward_details';

				} else {

					//In Below Array the First 5 Primary Key Fields Needs to be Present to Save the Record [inward_id, loc_id, org_sample_code, sample_type_code, fin_year]
					$dataArray = array(

						'inward_id'=>$inward_id,
						'loc_id'=>$postData['loc_id'],
						'org_sample_code'=>$org_sample_code,
						'sample_type_code'=>$postData['sample_type_code'],
						'fin_year'=>$postData['fin_year'],
						'smpl_drwl_dt'=>$postData['smpl_drwl_dt'],
						'tran_date'=>$postData['tran_date'],
						'drawal_loc'=>$postData['drawal_loc'],
						'shop_name'=>$postData['shop_name'],
						'shop_address'=>$postData['shop_address'],
						'mnfctr_nm'=>$postData['mnfctr_nm'],
						'mnfctr_addr'=>$postData['mnfctr_addr'],
						'pckr_nm'=>$postData['pckr_nm'],
						'pckr_addr'=>$postData['pckr_addr'],
						'grade'=>$postData['grade'],
						'tbl'=>$postData['tbl'],
						'pack_size'=>$postData['pack_size'],
						'remark'=>$postData['remark'],
						'lot_no'=>$postData['lot_no'],
						'no_of_packets'=>$postData['no_of_packets'],
						'replica_serial_no'=>$postData['replica_serial_no'],
						'user_code'=>$_SESSION["user_code"],
						'created'=>date('Y-m-d H:i:s'),
						'is_payment_applicable'=>$isPaymentApplicable,
						'inward_section'=>$inward_section,  # New Field - To Store Status - Akash - 10-08-2022
						'details_section'=>'Y',			    # New Field - To Store Status - Akash - 10-08-2022
						'payment_section'=>$payment_section # New Field - To Store Status - Akash - 10-08-2022


					);

					$SampleInwardDetailsEntity = $this->SampleInwardDetails->newEntity($dataArray);

					if ($this->SampleInwardDetails->save($SampleInwardDetailsEntity)) {	

						$tran_date	= $postData["tran_date"];

						$query = $conn->execute("SELECT u.id
													FROM dmi_users AS u
													INNER JOIN dmi_user_roles AS r ON u.email=r.user_email_id
													WHERE u.role IN('RO/SO OIC','RAL/CAL OIC') AND u.posted_ro_office='".$_SESSION["posted_ro_office"]."'AND r.user_flag='$user_flag'AND u.status != 'disactive' ");

						$user = $query->fetchAll('assoc');

						if (!empty($user)) {

							$user_code	= $user[0]['id'];

						} else {

							$user_code = null;
						}

						$workflow_data	 = array("org_sample_code"=>$org_sample_code,
												"src_loc_id"=>$_SESSION["posted_ro_office"],
												"src_usr_cd"=>$_SESSION["user_code"],
												"dst_loc_id"=>$_SESSION["posted_ro_office"],
												"dst_usr_cd"=>$user_code,
												"user_code"=>$_SESSION["user_code"],
												"stage_smpl_cd"=>$org_sample_code,
												"tran_date"=>$tran_date,
												"stage"=>"2",
												"stage_smpl_flag"=>"SD");

						$workflowEntity = $this->Workflow->newEntity($workflow_data);

						if ($this->Workflow->save($workflowEntity)) {

							$sampDetails = $this->SampleInwardDetails->find('all',array('fields'=>array('org_sample_code'),'conditions'=>array('org_sample_code'=>$org_sample_code),'order'=>'id desc'))->first();
							$_SESSION["org_sample_code"] =$sampDetails['org_sample_code'];//store in session to use in edit mode

							#Action
							$this->LimsUserActionLogs->saveActionLog('Sample Details Saved','Success');

							$message = 'You have successfully saved Sample details. Please note Sample Code is '.$org_sample_code;
							$message_theme = 'success';
							$redirect_to = 'sample_inward_details';

						} else {

							#Action
							$this->LimsUserActionLogs->saveActionLog('Sample Details Save','Failed');

							$message = 'Invalid parameters while saving workflow data, Please check parameters.';
							$message_theme = 'failed';
							$redirect_to = 'sample_inward_details';

						}


					} else {

						#Action
						$this->LimsUserActionLogs->saveActionLog('Sample Details Save','Failed');

						$message = 'Sorry... The sample details did not saved properly.';
						$message_theme = 'failed';
						$redirect_to = 'sample_inward_details';

					}
				}

			}

			//To Update The Sample Details
			if (null!== ($this->request->getData('update'))) {

				//Check POST Data Validations
				$validate_err = $this->detailsPostValidations($this->request->getData());

				if ($validate_err != '') {

					$this->set('validate_err',$validate_err);
					return null;
				}

				$replica_serial_no=array();

				for ($i=1;$i<=$postData['no_of_packets'];$i++) {

					$replica_serial_no[$i]=$postData['replica_serial_no'.$i];

				}

				$postData['replica_serial_no']=implode(",", $replica_serial_no);

				if ($postData['smpl_drwl_dt']!='') {

					$dStart = new \DateTime(date('Y-m-d H:i:s'));
					$date = $dStart->createFromFormat('d/m/Y', $postData['smpl_drwl_dt']);
					$smpl_drwl_dt=$date->format('Y/m/d');
					$postData['smpl_drwl_dt']=$smpl_drwl_dt;

				} else {

					$postData['smpl_drwl_dt'] ="";
				}


				if ($this->request->getData('sample_type_code') == '3') {
					$isPaymentApplicable = 'Yes';
				} else {
					$isPaymentApplicable = 'No';
				}

				//Fetch Sample Details
				$InwardDetails = $this->SampleInwardDetails->find('all',array('conditions' => array('org_sample_code IS' => $org_sample_code),'order'=>'id desc'))->first();

				//Below is Package of 5 Primary Key Fields Needs to be Unique To Update the Record Else it Will Save a New Record
				$postData['id'] = $InwardDetails['id'];//this is not
				$postData['inward_id'] = $InwardDetails['inward_id'];
				$postData['loc_id'] = $InwardDetails['loc_id'];
				$postData['org_sample_code'] = $InwardDetails['org_sample_code'];
				$postData['fin_year'] = $InwardDetails['fin_year'];
				$postData['sample_type_code'] = $InwardDetails['sample_type_code'];

				$dataArray = array(

					
					'id'=>$postData['id'],
					'inward_id'=>$postData['inward_id'],
					'org_sample_code'=>$postData['org_sample_code'],
					'sample_type_code'=>$postData['sample_type_code'],
					'smpl_drwl_dt'=>$postData['smpl_drwl_dt'],
					'tran_date'=>$postData['tran_date'],
					'fin_year'=>$postData['fin_year'],
					'loc_id'=>$postData['loc_id'],
					'drawal_loc'=>$postData['drawal_loc'],
					'shop_name'=>$postData['shop_name'],
					'shop_address'=>$postData['shop_address'],
					'mnfctr_nm'=>$postData['mnfctr_nm'],
					'mnfctr_addr'=>$postData['mnfctr_addr'],
					'pckr_nm'=>$postData['pckr_nm'],
					'pckr_addr'=>$postData['pckr_addr'],
					'grade'=>$postData['grade'],
					'tbl'=>$postData['tbl'],
					'pack_size'=>$postData['pack_size'],
					'remark'=>$postData['remark'],
					'lot_no'=>$postData['lot_no'],
					'no_of_packets'=>$postData['no_of_packets'],
					'replica_serial_no'=>$postData['replica_serial_no'],
					'modified'=>date('Y-m-d H:i:s'),
					'is_payment_applicable'=>$isPaymentApplicable,
					'inward_section'=>$inward_section,  # New Field - To Store Status - Akash - 10-08-2022
					'details_section'=>'Y',				# New Field - To Store Status - Akash - 10-08-2022
					'payment_section'=>$payment_section # New Field - To Store Status - Akash - 10-08-2022

				);

				$SampleInwardDetailsEntity = $this->SampleInwardDetails->newEntity($dataArray);

				if ($this->SampleInwardDetails->save($SampleInwardDetailsEntity)) {

					#Action
					$this->LimsUserActionLogs->saveActionLog('Sample Details Update','Success');

					$message = 'Sample details has been updated';
					$message_theme = 'success';
					$redirect_to = 'sample_inward_details';


				} else {

					#Action
					$this->LimsUserActionLogs->saveActionLog('Sample Details Update','Failed');

					$message = 'Sorry... The sample details did not updated properly.';
					$message_theme = 'failed';
					$redirect_to = 'sample_inward_details';

				}


			//To Confirm the Registered Sample
			} elseif (null!==($this->request->getData('confirm'))) {

				$this->loadModel('DmiUsers');
				$this->loadModel('SampleInward');
				$conn = ConnectionManager::get('default');

				$org_sample_code = $this->Session->read('org_sample_code');

				$user_role = $this->SampleInward->find('all',array('fields'=>array('loc_id','users','sample_type_code'),'conditions'=>array('org_sample_code'=>$this->Session->read('org_sample_code')),'order'=>'inward_id desc'))->first();

				$usercode = $user_role['users'];

				$user_role = $this->DmiUsers->find('all',array('fields'=>array('role'),'conditions'=>array('id IS'=>$usercode)))->first();

				//update status for payment sample
				if ($user_role['sample_type_code'] == '3') {
					$this->SampleInward->updateAll(array('status_flag'=>'PV'),array('org_sample_code'=>$org_sample_code));
				} else {
					$this->SampleInward->updateAll(array('status_flag'=>'S'),array('org_sample_code'=>$org_sample_code));
				}


				//get role and office where sample available after confirmed
				$query = $conn->execute("SELECT DISTINCT si.org_sample_code,w.dst_usr_cd,u.role,r.ro_office,w.src_usr_cd
										 FROM sample_inward AS si
										 INNER JOIN workflow AS w ON si.org_sample_code=w.org_sample_code
										 INNER JOIN dmi_users AS u ON u.id=w.dst_usr_cd
										 INNER JOIN dmi_ro_offices AS r ON r.id=w.dst_loc_id
										 WHERE si.org_sample_code='$org_sample_code'");

				$get_info = $query->fetchAll('assoc');
	
				// for commercial sample
				if ($user_role['sample_type_code'] == '3') {

					$confirm = $this->Paymentdetails->confirmSampleDetails();
					$org_sample_code = $_SESSION['org_sample_code'];
	
					if ($confirm == true){
						
						//get role and office where sample available after confirmed
						$get_info = $this->Workflow->find('all')->where(['org_sample_code IS' => $_SESSION['org_sample_code'],'stage_smpl_flag'=>'PS'])->order('id desc')->first();
	
						$this->loadModel('DmiUsers');
						$this->loadModel('DmiRoOffices');	

						$user =  $this->DmiUsers->getUserDetailsById($get_info['dst_usr_cd']);
						$office = $this->DmiRoOffices->getOfficeDetailsById($get_info['dst_loc_id']);
						
						$message_variable = 'Note :
											</br>The Commercial Sample Inward is saved with payment details and sent to <b>PAO/DDO :
											</br> '.base64_decode($user['email']).'  ('.$office[0].')</b>
											for payment verification, 
											</br>If the <b>DDO</b> user confirms the payment then it will be available to RO/SO OIC to forward.
											</br>If <b>DDO</b> user referred back  then you need to update details as per requirement and send again.';

						#SMS
						#$this->DmiSmsEmailTemplates->sendMessage(127,$get_info[0]['src_usr_cd'],$org_sample_code); #Inward
						#$this->DmiSmsEmailTemplates->sendMessage(128,$get_info[0]['dst_usr_cd'],$org_sample_code); #DDO 
						#$this->DmiSmsEmailTemplates->sendMessage(128,$get_info[0]['dst_usr_cd'],$org_sample_code); #RO

						#Action
						$this->LimsUserActionLogs->saveActionLog('Sample Sent to DDO','Success');
					}

				} else {
					
					#SMS
					#$this->DmiSmsEmailTemplates->sendMessage(127,$get_info[0]['src_usr_cd'],$org_sample_code); #source user
					#$this->DmiSmsEmailTemplates->sendMessage(128,$get_info[0]['dst_usr_cd'],$org_sample_code); #destination user

					#Action
					$this->LimsUserActionLogs->saveActionLog('Sample Confirmed','Success');

					$message_variable = 'Sample Code '.$org_sample_code.' has been Confirmed and Available to "'.$get_info[0]['role'].' ('.$get_info[0]['ro_office'].' )"';
				}
				
				$message = $message_variable;
				$message_theme = 'success';
				$redirect_to = '../inward/confirmed_samples';

			//to fetch common details for current location to prefilled the common fields
			} elseif (null!==($this->request->getData('fetch_common_details'))) {

				$loc_id = $this->Session->read('posted_ro_office');

				//get last sample details filled by this office
				$sample_Details_data = $this->SampleInwardDetails->find('all',array('conditions'=>array('loc_id IS'=>$loc_id),'order'=>'id desc'))->first();

				if (!empty($sample_Details_data)) {
					$this->set('sample_Details_data',$sample_Details_data);
				} else {

					$message = 'Sorry.. Currently there is no previous data available.';
					$message_theme = 'failed';
					$redirect_to = 'sample_inward_details';
				}

			}

		}

		// set variables to show popup messages from view file
		$this->set('message',$message);
		$this->set('message_theme',$message_theme);
		$this->set('redirect_to',$redirect_to);

	}

/****************************************************************************************************************************************************************************************************************************************************************/

/****************************************************************************************************************************************************************************************************************************************************************/

	public function checkInwDate(){

		$this->autoRender = false;
		$this->loadModel('SampleInward');
		$org_sample_code=$_POST['org_sample_code'];

		$date = $this->SampleInward->find('all',array('fields'=>'received_date','conditions'=>array('org_sample_code IS'=>$org_sample_code),'order'=>'inward_id desc'))->first();

		if (!empty($date)) {

			echo '~'.$date['received_date'].'~';

		} else {

			echo '~NULL~';
		}

		exit;

	}

/****************************************************************************************************************************************************************************************************************************************************************/

	public function get_sample_code1(){
		$str1="";
		$this->loadModel('SampleInward');

		// remove date seach conditions, pravin bhakare 30-10-2019
		$stage_sample_code1 = $this->SampleInward->query("SELECT si.inward_id,
														  TRIM(w.stage_smpl_cd) AS stage_sample_code,si.sample_type_code
														  FROM sample_inward AS si
														  INNER JOIN  workflow AS w ON w.org_sample_code=si.org_sample_code
														  AND si.display='Y'
														  AND si.status_flag IN('F','H')
														  AND w.stage_smpl_flag IN('OF','HF')
														  AND w.src_usr_cd='".$_SESSION['user_code']."' ");

		if (count($stage_sample_code1)>0) {

			echo json_encode($stage_sample_code1);
			exit;

			for ($i=0;$i<count($stage_sample_code1);$i++) {

				$str1.="<option value='".$stage_sample_code1[$i][0]['inward_id']."'>".$stage_sample_code1[$i][0]['stage_sample_code']."</option>";


			}

			echo $str1;
			exit;
		} else {

			echo "NO_DATA";

			exit;
		}


	}


/****************************************************************************************************************************************************************************************************************************************************************/


	//function to take post data and validate each field.
	public function detailsPostValidations($postData){

		$validation_status = '';

		if (!is_numeric($postData["loc_id"])) {

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

		if (!empty($postData["tran_date"])) {

			$res = preg_match('/(\d{4})-(\d{2})-(\d{2})/',$postData["tran_date"]);

			if ($res==0) {

				$validation_status = 'Invalid Transaction date';
			}
		}

		$res = preg_match('/(\d{2})\/(\d{2})\/(\d{4})/',$postData["smpl_drwl_dt"]);

		if ($res==0) {

			$validation_status = 'Invalid Letter date';
		}

		if (!in_array($postData["drawal_loc"],array('P','M','O'))) {

			$validation_status = 'Invalid Sample Type';
		}

		if (empty($postData["shop_name"]) || strlen($postData["shop_name"])>100) {

			$validation_status = 'Enter Shop Name';
		}

		if (empty($postData["shop_address"]) || strlen($postData["shop_address"])>200) {

			$validation_status = 'Enter Shop Address';
		}

		if (empty($postData["mnfctr_nm"]) || strlen($postData["mnfctr_nm"])>100) {

			$validation_status = 'Enter Manufacturer Name';
		}

		if (empty($postData["mnfctr_addr"]) || strlen($postData["mnfctr_addr"])>200) {

			$validation_status = 'Enter Manufacturer Address';
		}

		if (empty($postData["pckr_nm"]) || strlen($postData["pckr_nm"])>100) {

			$validation_status = 'Enter Packer Name';
		}

		if (empty($postData["pckr_addr"]) || strlen($postData["pckr_addr"])>200) {

			$validation_status = 'Enter Packer Address';
		}

		if (empty($postData["grade"]) || strlen($postData["grade"])>40) {

			$validation_status = 'Enter Grade';
		}

		if (empty($postData["tbl"]) || strlen($postData["tbl"])>100) {

			$validation_status = 'Enter TBL';
		}

		if (empty($postData["pack_size"]) || strlen($postData["pack_size"])>15) {

			$validation_status = 'Enter Pack Size';
		}

		if (empty($postData["remark"]) || strlen($postData["remark"])>25) {

			$validation_status = 'Enter Remark';
		}

		if (empty($postData["lot_no"]) || strlen($postData["lot_no"])>30) {

			$validation_status = 'Enter Lot No.';
		}

		if (!is_numeric($postData["no_of_packets"])) {

			$validation_status = 'Invalid No. of Packets';
		}

		if (!is_numeric($postData["no_of_packets"])) {

			$validation_status = 'Invalid No. of Packets';
		}

		$replica_serial_no=array();

		 for ($i=1;$i<=$postData['no_of_packets'];$i++) {

			if (empty($postData['replica_serial_no'.$i])) {

				$validation_status = 'Enter Proper Replica No.';
			}

		 }

		return $validation_status;

	}



}
?>
