<?php
namespace app\Controller\Component;
use Cake\Controller\Controller;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Datasource\EntityInterface;
use QRcode;
class CustomfunctionsComponent extends Component {

	public $components= array('Session','PaymentDetails','Ilc');
	public $controller = null;
	public $session = null;

	public function initialize(array $config):void{
		parent::initialize($config);
		$this->Controller = $this->_registry->getController();
		$this->Session = $this->getController()->getRequest()->getSession();
	}

/***************************************************************************************************************************************************************************************************/

	//CHECK FAILED ATTEMPTS OF USER
	public function checkLoginLockout($table,$user_id) {

		$Dmitable = TableRegistry::getTableLocator()->get($table);

		//check in DB logs table
		if ($table == 'DmiUserLogs') {

			$get_logs_records = $Dmitable->find('all',array('fields'=>array('id'),'conditions'=>array('email_id IS'=>$user_id),'order'=>'id Desc'))->toArray();
		} 

		$i = 0;

		foreach ($get_logs_records as $each) {

			$each_log_details = $Dmitable->find('all',array('conditions'=>array('id'=>$each['id'])))->first();
			$remark[$i] = $each_log_details['remark'];
			$date[$i] = $each_log_details['date'];

			$i = $i+1;
		}

		$current_date = strtotime(date('d-m-Y'));


		$j = 0;
		$failed_count = 0;
		$lockout_status = null;
		
		while($j <= 2) {

			if (!empty($remark[$j])) {

				if ($remark[$j] == 'Failed') {

					$log_date = strtotime(str_replace('/','-',$date[$j]));

					if ($current_date == $log_date) {

						$lockout_status = 'yes';
					
					} else {

						$lockout_status = 'no';
					}

					$failed_count = $failed_count+1;
				}
			}

			$j = $j+1;
		}

		if ($failed_count == 3 && $lockout_status == 'yes') {

			return 'yes';
		
		} else {
			
			return 'no';
		}

	}


/***************************************************************************************************************************************************************************************************/		

	//CHECK IF THE PARENTHESIS IS MATCHED.
	function hasMatchedParenthesis($string) {

		$len = strlen($string);
		$stack = array();
		
		for ($i = 0; $i < $len; $i++) {

			switch ($string[$i]) {

				case '(': array_push($stack, 0);
					break;
				case ')':
					if (array_pop($stack) !== 0)
							return false;
					break;
				case '[': array_push($stack, 1);
					break;
				case ']':
					if (array_pop($stack) !== 1)
							return false;
					break;
				default: break;
			}
		}

		return (empty($stack));
	}

/***************************************************************************************************************************************************************************************************/		

	//METHOD FOR MULTIPLE-EXPLODE
	function multiexplode($delimiters, $string) {

		$ready = str_replace($delimiters, $delimiters[0], $string);
		$launch = explode($delimiters[0], $ready);
		return $launch;
	}

/***************************************************************************************************************************************************************************************************/		

	//GET MASKED VALUE FOR EMAIL_IDs
	public function getEmailMasked($email_id) {
		
		//$email_id = base64_decode($email_id);
		//print_r($email_id); exit;
		$em   = explode("@",$email_id);
		$name = implode('@', array_slice($em, 0, count($em)-1));
		$len  = floor(strlen($name)/2);

		$split_name = str_split($name, 1);

		$i=0;
		$j=0;
		
		foreach ($split_name as $each) {

			if ($i % 2 == 0) {

				$masked_value_array[$j] = str_replace($split_name[$i],'X', $each);

			} else {
				
				$masked_value_array[$j] = $each;
			}
				$i=$i+1;
				$j=$j+1;
		}

		$masked_value = implode('',$masked_value_array) . "@" . end($em);

		return $masked_value;

	}


/***************************************************************************************************************************************************************************************************/		

	//VALIDATES ALL POST DATA ON SERVER SIDE
	public function validateUniquePostData($value,$type) {

		if ($type == 'mobile') {

			if (preg_match("/^(?=.*[0-9])[0-9]{10}$/", $value,$matches)==1 || preg_match("/^[X-X]{6}[0-9]{4}$/i", $value,$matches)==1) {

				return true;
			
			} else {

				return false;
			
			}
		
		}

		if ($type == 'email') {

			if (preg_match("/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/", $value,$matches)==1) {

				return true;
			
			}else{

				return false;
			}
		}


		if ($type == 'aadhar') {

			if (preg_match("/^(?=.*[0-9])[0-9]{12}$/", $value,$matches)==1 || preg_match("/^[X-X]{8}[0-9]{4}$/i", $value,$matches)==1) {

				return true;
			
			} else {

				return false;
			}
		}

	}

/***************************************************************************************************************************************************************************************************/		

	//CREATES MASK ON PERSONAL IDENTIFICATION DETAILS
	public function getMaskedValue($value,$type){

		$masked_value = null;

		if ($type=='mobile') {

			$masked_value = substr_replace($value, str_repeat("X", 6), 0, 6);
		
		} elseif ($type=='email') {

			//calling email masking function
			$masked_value = $this->getEmailMasked($value);

		} elseif ($type=='aadhar') {

			$masked_value = substr_replace($value, str_repeat("X", 8), 0, 8);
		}

		return $masked_value;
	}


/***************************************************************************************************************************************************************************************************/		


	//SERVER SIDE VALIDATIONS FOR DROP-DOWN SELECT INPUT
	public function dropdownSelectInputCheck($table,$post_input_request) {

		$table = TableRegistry::getTableLocator()->get($table);
		$db_table_id_list = $table->find('list',array('valueField'=>'id'))->toArray();
		$min_id_from_list = min($db_table_id_list);
		$max_id_from_list = max($db_table_id_list);

		if (filter_var($post_input_request, FILTER_VALIDATE_INT, array("options" => array("min_range"=>$min_id_from_list, "max_range"=>$max_id_from_list))) === false) {
			
			$this->Controller->customAlertPage("One of selected drop down value is not proper");
			exit;

		} else {
				
			return $post_input_request;
		}
	}


/***************************************************************************************************************************************************************************************************/		
						
	//FILE UPLOAD LIBRARY FOR FILE UPLOADING
	public function fileUploadLib($file_name,$file_size,$file_type,$file_local_path) {

		$valid_extension_file = array('jpeg','pdf','jpg');
		$get_extension_value = explode('.',$file_name);

		if (count($get_extension_value) != 2 ) {

			$this->Controller->customAlertPage("Invalid file type.");
			exit;

		} else {

			$extension_name = strtolower($get_extension_value[1]);

			if (in_array($extension_name,$valid_extension_file)) {

			} else {

				$this->Controller->customAlertPage("Invalid file type.");
				exit;
			}
		}

		if (($file_size > 2097152)) {

			$this->Controller->customAlertPage("File too large. File must be less than 2 megabytes.");
			exit;

		} elseif (($file_type != "application/pdf") && ($file_type != "image/jpeg")) {

			$this->Controller->customAlertPage("Invalid file type. Only PDF, JPG types are accepted.");
			exit;

		} else {

			// For PDF files
			if ($file_type == "application/pdf" ) {
				
				if ($f = fopen($file_local_path, 'rb')) {
					
					$header = fread($f, 4);
					fclose($f);

					// Signature = PDF
					if (strncmp($header, "\x25\x50\x44\x46", 4)==0 && strlen ($header)==4) {
						
						// CHECK IF PDF CONTENT HAVING MALICIOUS CHARACTERS OR NOT
						$pdf_content = file_get_contents($file_local_path);

						$cleaned_pdf_content = $this->fileClean($pdf_content);

						if ($cleaned_pdf_content=='invalid') {

							$this->Controller->customAlertPage("File seems to be corrupted !");
							exit;
						}
					
					} else {

						$this->Controller->customAlertPage("Sorry....modified PDF file");
						exit;
					}
						
				} else {

					$this->Controller->customAlertPage("Not getting file path");
					exit;
				}
					//FOR IMAGE FILES
			} elseif ($file_type == "image/jpeg" ) {
				
				if ($f = fopen($file_local_path, 'rb')) {

					$header = fread($f, 3);
					
					fclose($f);
								
					// Signature = JPEG
					if (strncmp($header, "\xFF\xD8\xFF", 3)==0 && strlen ($header)==3) {

						// CHECK FOR CORRUPTED (MODIFIED) FILE
						$img_content = file_get_contents($file_local_path);
								
						$im = imagecreatefromstring($img_content);
						
						if ($im !== false) {
						
							// original file
						
						} else {

							$this->Controller->customAlertPage("File seems to be corrupted !");
							exit;
						}

						// CHECK IF IMAGE CONTENTS HAVING MALICIOUS CHARACTERS OR NOT
						$img_content = file_get_contents($file_local_path);
							
						$cleaned_img_content = $this->fileClean($img_content);

						if ($cleaned_img_content=='invalid') {

							$this->Controller->customAlertPage("File seems to be corrupted !");
							exit;
						}
					
					} else {

						$this->Controller->customAlertPage("Sorry....modified JPG file");
						exit;
					}
				
				} else {
					
					$this->Controller->customAlertPage("Not getting file path");
					exit;
				}

			}

			// File Uploading 
			$filecodedName = time().uniqid().$file_name;
			$uploadPath = $_SERVER["DOCUMENT_ROOT"].'/writereaddata/DMI/files/';
			$uploadFile = $uploadPath.$filecodedName;
			$uploadData = '';
		
			if (move_uploaded_file($file_local_path,$uploadFile)) {
					
				$uploadData = '/writereaddata/DMI/files/'.$filecodedName;

			} else {
				
				$this->Controller->customAlertPage("File not uploaded please select proper file");
				exit;
			}
		
		}

		if (!empty($uploadData)) {

			return $uploadData;
		}

	}


/***************************************************************************************************************************************************************************************************/		

	//CHECK IF UPLOADED FILES ARE MALICIOUS
	public function fileClean($str) {

		$BlacklistCharacters = TableRegistry::getTableLocator()->get('BlacklistCharacters');
		// $blacklists = array of blacklist characters from database
		$blacklists = $BlacklistCharacters->find('all');
		
		$malicious_found = '0';
		
		foreach ($blacklists as $b_list) {
			// Change by Pravin Bhakare 13-10-2020
			$charac = $b_list['charac'];		
			$posValue = strpos($str,$charac);
			
			if (!empty($posValue)) {

				$malicious_found = 1;
				break;
			}
		}
		
		if ($malicious_found > 0) {

			return 'invalid';
		}

		return $str;

	}

/***************************************************************************************************************************************************************************************************/		

	//CHECK DMI USER LAST LOGIN
	public function userLastLogins() {

		$DmiUserLogs = TableRegistry::getTableLocator()->get('DmiUserLogs');

		$list_id = $DmiUserLogs->find('list', array('fields'=>'id', 'conditions'=>array('email_id IS'=>$this->Session->read('username'), 'remark'=>'Success'),'order'=>'id'))->toArray();
		
		if (!empty($list_id)) {

			$i=0;
		
			foreach ($list_id as $id) {

				$list_id[$i]= $id;	

				$i=$i+1;

			}

			if ($i != 1) {
			
				$last_login_id = $list_id[$i-2];

			} else {

				$last_login = 'First login';
				return $last_login;
			}

			$last_login = $DmiUserLogs->find('all', array('fields'=>array('date','time_in'), 'conditions'=>array('id IS'=>$last_login_id)))->first();

			return $last_login;

		} else {

			$last_login = 'First login';
			return $last_login;
		}

	}

/***************************************************************************************************************************************************************************************************/		

	//SERVER-SIDE VALIDATIONS FOR RADIO BUTTON
	public function radioButtonInputCheck($post_input_request) {

		if ($post_input_request == 'yes' || $post_input_request == 'no' || $post_input_request == 'page' ||
				$post_input_request == 'external' || $post_input_request == 'top' || $post_input_request == 'side' ||
				$post_input_request == 'bottom' || $post_input_request == 'DMI' || $post_input_request == 'LMIS' ||
				$post_input_request == 'BOTH' || $post_input_request == 'n/a' ||

			// LMIS user roles option (Done By pravin 22/11/2017)
			$post_input_request == 'RO' || $post_input_request == 'SO' || $post_input_request == 'RAL' ||
			$post_input_request == 'CAL' || $post_input_request == 'HO'
		) {
			return $post_input_request;

		} else {
			return null;
		}
	}

/***************************************************************************************************************************************************************************************************/		

	//SERVER-SIDE VALIDATIONS FOR INTEGER INPUT
	public function integerInputCheck($post_input_request) {

		$min = 1;

		if (!filter_var($post_input_request, FILTER_VALIDATE_INT, array("options" => array("min_range"=>$min))) === false) {

			return $post_input_request;

		} else {

			return null;
		}

	}

/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>-------<Check Data Format>------->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>.*/		

	//CHECKS VALID DATE FORMAT 
	public function dateFormatCheck($date) {

		if (!empty($date)) {

			$input_date = explode('/',$date);
			$removeTime	= explode(' ',$input_date[2]);
			$year = $removeTime[0];
			
			if (count($input_date) == 3) {

				$zero_int_value = array('01','02','03','04','05','06','07','08','09');

				if (in_array($input_date[0],$zero_int_value, true)) {

					$day_value = str_replace('0','',$input_date[0]);

				} else {

					$day_value = $input_date[0];
				}

				$day_value = $this->integerInputCheck($day_value);

				if (in_array($input_date[1],$zero_int_value, true)) {

					$month_value = str_replace('0','',$input_date[1]);

				} else {

					$month_value = $input_date[1];
				}

				$month_value = $this->integerInputCheck($month_value);

				if (in_array($year,$zero_int_value, true)) {

					$year_value = str_replace('0','',$$year);

				} else {

					$year_value = $year;
				}

				$year_value = $this->integerInputCheck($year_value);

				$valid = checkdate(trim($month_value), trim($day_value), trim((int)$year_value));

				if ($valid == 1) {
					
					return $this->changeDateFormat($date);

				} else {

					$this->Controller->customAlertPage("Sorry.. Something wrong happened. ");
					exit;
				}

			} else {

				$this->Controller->customAlertPage("Sorry.. Something wrong happened. ");
				exit;

			}

		} else {
			
			return $this->changeDateFormat($date);
		}
	}

/***************************************************************************************************************************************************************************************************/		

	//CREATE SAMPLE CODE
	public function createSampleCode() {

		$SampleInward = TableRegistry::getTableLocator()->get('SampleInward');
		$sample_code = mt_rand(1000000,9999999);
		$duplicate_code = $SampleInward->find('all',array('fields'=>array('org_sample_code'),'conditions'=>array('org_sample_code IS'=>$sample_code)))->first();
		
		if (empty($duplicate_code)) {
			return $sample_code;
		} else {
			$this->createSampleCode();
		}
	}


/***************************************************************************************************************************************************************************************************/		

	//GENERATES CHEMIST CODE
	public function createChemistCode() {

		$MSampleAllocate = TableRegistry::getTableLocator()->get('MSampleAllocate');
		$md5_hash = md5(rand(0,9999999999));
		$sample_code = substr($md5_hash, 15, 5);

		$duplicate_code = $MSampleAllocate->find('all',array('fields'=>array('chemist_code'),'conditions'=>array('chemist_code IS'=>$sample_code)))->first();
		
		if (empty($duplicate_code)) {

			return $sample_code;
		} else {

			$this->createChemistCode();
		}
	}

/***************************************************************************************************************************************************************************************************/		

	//CREATE STAGE SAMPLE CODE METHOD - GENERATES NEW STAGE SAMPLE CODE
	public function createStageSampleCode() {

		$Workflow = TableRegistry::getTableLocator()->get('Workflow');
		$sample_code = mt_rand(1000000,9999999);
		$duplicate_code = $Workflow->find('all',array('fields'=>array('stage_smpl_cd'),'conditions'=>array('stage_smpl_cd IS'=>$sample_code)))->first();
		
		if (empty($duplicate_code)) {

			return $sample_code;
		} else {

			$this->createStageSampleCode();
		}
	}

/***************************************************************************************************************************************************************************************************/		

	//CHECK SAMPLE STATSUS FROM PROGRESS-BAR
	public function checkSampleIsSaved($check_for,$org_sample_code) {
		

		if ($check_for=='sample_inward') {

			$checkModel = TableRegistry::getTableLocator()->get('SampleInward');

		} elseif ($check_for=='sample_details') {

			$checkModel = TableRegistry::getTableLocator()->get('SampleInwardDetails');

		} elseif ($check_for=='payment_details') {
			$checkModel = TableRegistry::getTableLocator()->get('LimsSamplePaymentDetails');
		}

		//get sample code by inward id
		$check_sample_code=array();

		if ($org_sample_code != null) {

			if ($check_for == 'payment_details') {
				$check_sample_code = $checkModel->find('all',array('fields'=>'sample_code', 'conditions'=>array('sample_code IS'=>$org_sample_code)))->first();
			} else {
				$check_sample_code = $checkModel->find('all',array('fields'=>'org_sample_code', 'conditions'=>array('org_sample_code IS'=>$org_sample_code)))->first();
			}
		}
		
		return $check_sample_code;
	}


/***************************************************************************************************************************************************************************************************/		


	//SHOW OR HIDE CONFIRM BUTTON CONDITONALLY
	public function showHideConfirmBtn() {

		$action = 'hide';
		$org_sample_code = $this->Session->read('org_sample_code');
		$sampleInward = TableRegistry::getTableLocator()->get('SampleInward');
		$SampleInwardDetails = TableRegistry::getTableLocator()->get('SampleInwardDetails');
		$LimsSamplePaymentDetails = TableRegistry::getTableLocator()->get('LimsSamplePaymentDetails');

		if ($org_sample_code != null) {

			//check sample code with Accepted status in SampleInward table
			$check_inward = $sampleInward->find('all',array('fields'=>'org_sample_code', 'conditions'=>array('org_sample_code IS'=>$org_sample_code,'acc_rej_flg'=>'A'),'order'=>'inward_id desc'))->first();

			//check if current user is RO/SO then also check in SampleDetails table
			$user_flag = $this->Session->read('user_flag');
			$sampleType = trim($this->Session->read('sample'));
		
			$check_details = array();

			//RO SO Officer & Commercial
			if ($user_flag=='RO' || $user_flag=='SO') {

				$check_details = $SampleInwardDetails->find('all',array('fields'=>'org_sample_code', 'conditions'=>array('org_sample_code IS'=>$org_sample_code),'order'=>'id desc'))->first();
						
				if (!empty($check_inward) && !empty($check_details)) {

					$action = 'show';
				}
				
			} else {
					
				if (!empty($check_inward)) {
		
					$action = 'show';
				}
			}

			//For Commercial show hide button - 30-06-2022
			if ($sampleType == 3) {
				$check_details = $LimsSamplePaymentDetails->getPaymentDetails($org_sample_code);
				if (empty($check_details)) {

					$action = 'hide';
				} 
			}
		
		} else {

			$action = 'hide';
		}

		return $action; 
	}

		
/***************************************************************************************************************************************************************************************************/		

	//CHANGE DATE FORMAT
	public function changeDateFormat($date) {

		if (!empty($date)) {

			$result	= explode(' ',$date);
			
			if (count($result) == 2) {

				$date1 = $result[0];
				$time = $result[1];
				$date = date_create_from_format("d/m/Y" , trim($date1))->format("Y-m-d").$time;
			
			} else {
			
				$date1 = $result[0];
				$date = date_create_from_format("d/m/Y" , trim($date1))->format("Y-m-d")." 00:00:00";
			
			}				
			
		} else {
			
			$date;
		}

		return $date;
	}


/***************************************************************************************************************************************************************************************************/
	// get_sms_id
	// Author : Akash Thakre
	// Description : This will return the SMS ID for sending the message.
	// Date : 25-07-2022

	public function getSmsId($sms_action){

		$user_role = $_SESSION['role'];

		if ($sms_action=='inward') {
			$from_id = 75;
			$to_id = 76;
		}

		$Workflow = TableRegistry::getTableLocator()->get('Workflow');
		$sample_code = $_SESSION['org_sample_code'];
		$sendingTo = $_SESSION['user_code'];
		$getDetails = $Workflow->find('all')->where(['stage_smpl_cd' => $sample_code])->order('id desc')->first();
		$receiver = $getDetails['dst_usr_cd'];
		
		return array('from_user'=>$sendingTo,'from_sms_id'=>$from_id,'to_user'=>$receiver,'to_sms_id'=>$to_id);
	}

/***************************************************************************************************************************************************************************************************/
		
	//Get QR Code for Sample Test Report
	// Author : Shankhpal Shende
	// Description : This will return QR code for Sample Test Report
	// Date : 01/09/2022

	public function getQrCodeSampleTestReport($Sample_code_as,$sample_forwarded_office,$test_report){
				
		$LimsReportsQrcodes = TableRegistry::getTableLocator()->get('LimsReportsQrcodes'); //initialize model in component
		
		require_once(ROOT . DS .'vendor' . DS . 'phpqrcode' . DS . 'qrlib.php');

		// call to component for sample type Done by Shreeya on 15-11-2022
		$sampleTypeCode = $this->createSampleType($Sample_code_as);
		// added if conditon for ILC sample non grading sample done by Shreeya on 15-11-2022
		if($sampleTypeCode!=9){

			//updated by shankhpal on 21/11/2022
			$data = "Name of RO/SO:".$sample_forwarded_office[0]['user_flag'].",".$sample_forwarded_office[0]['ro_office']."##"."Address of RO/SO :".$sample_forwarded_office[0]['ro_office']."##"."Sample Code No :".$Sample_code_as."##"."Commodity :".$test_report[0]['commodity_name']."##"."Grade:".$test_report[0]['grade_desc'];
			
			$qrimgname = rand();
			
			$server_imagpath = '/writereaddata/LIMS/QRCodes/'.$qrimgname.".png";
			
			$file_path = $_SERVER["DOCUMENT_ROOT"].'/writereaddata/LIMS/QRCodes/'.$qrimgname.".png";
			
			$file_name = $file_path;
			
			QRcode::png($data,$file_name);
					
			$date = date('Y-m-d H:i:s');
			
			$workflow = TableRegistry::getTableLocator()->get('workflow');
			
			//$sample_code = $workflow->find('all',array(,'conditions'=>array('org_sample_code'=>$Sample_code_as),'order'=>'id asc'))->toArray();
			$sample_code = $workflow->find('all',array('fields'=>'org_sample_code', 'conditions'=>array('stage_smpl_cd IS'=>$Sample_code_as)))->first();
			
			$stage_smpl_code = $sample_code['org_sample_code'];
			
			$SampleReportAdd = $LimsReportsQrcodes->newEntity([
				'sample_code'=>$stage_smpl_code,
				'qr_code_path'=>$server_imagpath,
				'created'=>$date,
				'modified'=>$date
			]);

			$LimsReportsQrcodes->save($SampleReportAdd);
			
			$qrimage = $LimsReportsQrcodes->find('all',array('field'=>'qr_code_path','conditions'=>array('sample_code'=>$stage_smpl_code),'order'=>'id desc'))->first();
		
			return $qrimage;
		}

	}

/***************************************************************************************************************************************************************************************************/

	// CREATE SAMPLE TYPE
	// Contributer : Shreeya Bondre
	// Description : This will return Sample Type code for Sample 
	// Date : 16/06/2022

	public function createSampleType($forw_sample_cd) {

		$conn = ConnectionManager::get('default');
		$SampleInward = TableRegistry::getTableLocator()->get('SampleInward');
		$query="SELECT org_sample_code FROM workflow WHERE stage_smpl_cd = '$forw_sample_cd' AND display='Y' ";
	
		$sample_cd1 = $conn->execute($query);
		$sample_cd1 = $sample_cd1->fetchAll('assoc');
		$sample_cd = $sample_cd1[0]['org_sample_code'];
		$getSampleType = $SampleInward->find('all',array('fields'=>'sample_type_code','conditions'=>array('org_sample_code IS' => $sample_cd)))->first();
		$sampleTypeCode = $getSampleType['sample_type_code'];
		$this->Controller->set('sampleTypeCode',$sampleTypeCode );
		
		return $sampleTypeCode;
	}
	
	


}
?>
