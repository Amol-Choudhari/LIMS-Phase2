<?php
	namespace app\Controller\Component;
	use Cake\Controller\Controller;
	use Cake\Controller\Component;
	use Cake\Controller\ComponentRegistry;
	use Cake\ORM\Table;
	use Cake\ORM\TableRegistry;
	use Cake\Datasource\EntityInterface;

	class CustomfunctionsComponent extends Component {

		public $components= array('Session','PaymentDetails');
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

		} elseif ($table == 'DmiCustomerLogs') {

			$get_logs_records = $Dmitable->find('all',array('fields'=>array('id'),'conditions'=>array('customer_id IS'=>$user_id),'order'=>'id Desc'))->toArray();
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
				
			echo "<script>alert('One of selected drop down value is not proper')</script>";
				
			$this->Session->destroy();
				
			exit();

		} else {
				
			return $post_input_request;
		}
	}


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--------<File Upload Library>-------->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
						
	//FILE UPLOAD LIBRARY FOR FILE UPLOADING
	public function fileUploadLib($file_name,$file_size,$file_type,$file_local_path) {

		$valid_extension_file = array('jpeg','pdf','jpg');
		$get_extension_value = explode('.',$file_name);

		if (count($get_extension_value) != 2 ) {

			$message = 'Invalid file type.';
			echo '<script type="text/javascript">alert("'.$message.'");</script>';
			$this->Session->destroy();
			echo "";?><a href="<?php echo $this->request->getAttribute('webroot');?>">Please Login</a><?php
			exit;

		} else {

			$extension_name = strtolower($get_extension_value[1]);

				if (in_array($extension_name,$valid_extension_file)) {

				} else {

					$message = 'Invalid file type.';
					echo '<script type="text/javascript">alert("'.$message.'");</script>';
					$this->Session->destroy();
					echo "";?><a href="<?php echo $this->request->getAttribute('webroot');?>">Please Login</a><?php
					exit;
				}
		}

		if (($file_size > 2097152)) {

				$message = 'File too large. File must be less than 2 megabytes.';
				echo '<script type="text/javascript">alert("'.$message.'");</script>';
				$this->Session->destroy();
				echo "";?><a href="<?php echo $this->request->getAttribute('webroot');?>">Please Login</a><?php
				exit;

		} elseif (($file_type != "application/pdf") && ($file_type != "image/jpeg")) {

			$message = 'Invalid file type. Only PDF, JPG types are accepted.';
			echo '<script type="text/javascript">alert("'.$message.'");</script>';
			$this->Session->destroy();
			echo "";?><a href="<?php echo $this->request->getAttribute('webroot');?>">Please Login</a><?php
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

									echo "<script>alert('File seems to be corrupted !')</script>";
									$this->Session->destroy();
									echo "";?><a href="<?php echo $this->request->getAttribute('webroot');?>">Please Login</a><?php	
									exit;
								}
						
						} else {

							echo "<script>alert('Sorry....modified PDF file')</script>";
							$this->Session->destroy();
							echo "";?><a href="<?php echo $this->request->getAttribute('webroot');?>">Please Login</a><?php
							exit;
						}
						
				} else {
								
					echo "<script>alert('Not getting file path')</script>";
								
					return false;
						
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

							echo "<script>alert('File seems to be corrupted !')</script>";

							$this->Session->destroy();

							echo "";?><a href="<?php echo $this->request->getAttribute('webroot');?>">Please Login</a><?php

							exit;
								
						}
							// CHECK IF IMAGE CONTENTS HAVING MALICIOUS CHARACTERS OR NOT
							$img_content = file_get_contents($file_local_path);
								
							$cleaned_img_content = $this->fileClean($img_content);

							if ($cleaned_img_content=='invalid') {

								echo "<script>alert('File seems to be corrupted !')</script>";
								$this->Session->destroy();
								echo "";?><a href="<?php echo $this->request->getAttribute('webroot');?>">Please Login</a><?php	
								exit;
							}
					
					} else {
												
						echo "<script>alert('Sorry....modified JPG file')</script>";
						$this->Session->destroy();
						echo "";?><a href="<?php echo $this->request->getAttribute('webroot');?>">Please Login</a><?php	
						exit;
						//return false;
					}
				
				} else {
					
					echo "<script>alert('Not getting file path')</script>";
								
					return false;

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
				
				echo "<script>alert('File not uploaded please select proper file')</script>";
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
				echo "<script>alert('one of YES/NO button input is not proper')</script>";
				return false;
		}
	}

/***************************************************************************************************************************************************************************************************/		

	//SERVER-SIDE VALIDATIONS FOR INTEGER INPUT
	public function integerInputCheck($post_input_request) {

		$min = 1;

		if (!filter_var($post_input_request, FILTER_VALIDATE_INT, array("options" => array("min_range"=>$min))) === false) {

			return $post_input_request;

		} else {

			echo "<script>alert('One of the given input should be in no. only')</script>";
			return false;
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

					echo"Sorry.. Something wrong happened. ";?><a href="<?php echo $this->request->getAttribute('webroot');?>"> Please Login again</a><?php
					exit;
				}

			} else {

				echo"Sorry.. Something wrong happened. ";?><a href="<?php echo $this->request->getAttribute('webroot');?>"> Please Login again</a><?php
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


	public function getSampleRegisterOffice($sample_code){

		if(empty($sample_code)){
			$sample_code = $this->Session->read('org_sample_code');
		}
		
		//to use this function in model also
		$Workflow = TableRegistry::getTableLocator()->get('Workflow');
		$LimsOfficeToDistrict = TableRegistry::getTableLocator()->get('LimsOfficeToDistrict');
		
		//get district id
		$get_dist_id = $Workflow->find('all',array('conditions'=>array('org_sample_code IS'=>$sample_code)))->first();
		$dist_id = $get_dist_id['dst_loc_id'];

		$district_details = $LimsOfficeToDistrict->find()->where(['ro_office_id' => $dist_id])->first();
	
		$ro_office_id = $district_details['ro_office_id']; 

		return $ro_office_id;
		
	}

/***************************************************************************************************************************************************************************************************/
	
	public function getPaoDetails($ro_office_id) {

		//Load Models
		$DmiPaoDetails = TableRegistry::getTableLocator()->get('DmiPaoDetails');
		$LimsOfficeToDistrict = TableRegistry::getTableLocator()->get('LimsOfficeToDistrict');
		$DmiUsers = TableRegistry::getTableLocator()->get('DmiUsers');
		
		//Find the Pao Details
		if (!empty($ro_office_id)) {
			
			$get_pao_details_id = $LimsOfficeToDistrict->find()->where(['ro_office_id' => $ro_office_id])->first();
			
			$pao_details = $DmiPaoDetails->find()->where(['id IS' => $get_pao_details_id['pao_id']])->first();
			
		}
		return $pao_details['id'];
	}


/***************************************************************************************************************************************************************************************************/

	public function sampleTypeInformation($org_sample_code) {

		//Load Models
		$SampleInward = TableRegistry::getTableLocator()->get('SampleInward');
		$SampleInwardDetails = TableRegistry::getTableLocator()->get('SampleInwardDetails');

		//Current Sample Details
		$SampleDetails = $SampleInward->find()->where(['org_sample_code IS' => $org_sample_code])->first();

		return $SampleDetails;
	}
	

	public function samplePaymentCharges($sample_code){

		$LimsSampleCharges = TableRegistry::getTableLocator()->get('LimsSampleCharges');
		$MSampleType = TableRegistry::getTableLocator()->get('MSampleType');
		$SampleInward = TableRegistry::getTableLocator()->get('SampleInward');
		
		$get_sample_type = $SampleInward->find()->select(['sample_type_code','category_code'])->where(['stage_sample_code IS' => $sample_code])->first();
		$sample_type = $MSampleType->find()->where(['sample_type_code' => $get_sample_type['sample_type_code']])->first();
		$charges = $LimsSampleCharges->find()->where(['sample_type' => $sample_type['sample_type_desc']])->first();

		$totalCharges = $charges['charges'];
	
		return $totalCharges;
	}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	public function saveSamplePaymentDetails($data){
		
		//Load Session Values
		$sample_code = $this->Session->read('org_sample_code');
		
		//Load Models
		$Workflow = TableRegistry::getTableLocator()->get('Workflow');
		$DmiSmsEmailTemplates = TableRegistry::getTableLocator()->get('DmiSmsEmailTemplates');
		$LimsSamplePaymentDetails = TableRegistry::getTableLocator()->get('LimsSamplePaymentDetails');
		$DmiPaoDetails = TableRegistry::getTableLocator()->get('DmiPaoDetails');
		$DmiUsers = TableRegistry::getTableLocator()->get('DmiUsers');
		
		//Set Variables to blank
		$payment_conirmation_status = '';
		$payment_receipt_docs = '';
		
		$lims_payment_id = $LimsSamplePaymentDetails->find('list', array('fields'=>'id','conditions'=>array('sample_code IS'=>$sample_code)))->toArray();
		
		if(!empty($lims_payment_id)){	
		
			$payment_confirmation_query = $LimsSamplePaymentDetails->find('all', array('conditions'=>array('id'=>max($lims_payment_id))))->first();
			$payment_conirmation_status = $LimsSamplePaymentDetails['payment_confirmation'];
			$payment_receipt_docs = $payment_confirmation_query['payment_receipt_docs'];
		}
		
		$sample_details = $Workflow->find('all',array('conditions'=>array('org_sample_code IS'=>$sample_code)))->toArray();
		$sample_details_fields = $sample_details[0];
	
		$district_id = $sample_details[0]['dst_loc_id'];
		$pao_id = $this->getPaoDetails($district_id); 
		
		if (empty($data['payment_amount']) && empty($data['payment_transaction_id']) && empty($data['bharatkosh_payment_done']) && empty($data['payment_trasaction_date'])) {
		
			return false;
		}
		
		if (empty($payment_receipt_docs)) {

			if (empty($data['payment_receipt_document']->getClientFilename())) {

				return false;
			}
		}
		
		$payment_amount = htmlentities($data['payment_amount'], ENT_QUOTES);
		$payment_transaction_id = htmlentities($data['payment_transaction_id'], ENT_QUOTES);

		$post_input_request = $data['bharatkosh_payment_done'];
		$bharatkosh_payment_done = $this->radioButtonInputCheck($post_input_request);
		
		if ($bharatkosh_payment_done == null) { 
			return false;
		}


		if(!empty($data['payment_receipt_document']->getClientFilename())) {
			
			$file_name = $data['payment_receipt_document']->getClientFilename();
			$file_size = $data['payment_receipt_document']->getSize();
			$file_type = $data['payment_receipt_document']->getClientMediaType();
			$file_local_path = $data['payment_receipt_document']->getStream()->getMetadata('uri');
			// calling file uploading function
			$payment_receipt_docs = $this->fileUploadLib($file_name,$file_size,$file_type,$file_local_path); 
		}
		
		$payment_trasaction_date = $this->changeDateFormat($data['payment_trasaction_date']);
		
		if($payment_conirmation_status == 'not_confirmed'){
			
			//find PAO email id
			$pao = $LimsSamplePaymentDetails->find('all', array('fields'=>'pao_id', 'conditions'=>array('org_sample_code IS'=>$sample_code)))->first();
			$pao_user_id = $DmiPaoDetails->find('all',array('fields'=>'pao_user_id', 'conditions'=>array('id IS'=>$pao['pao_id'])))->first();
			$pao_user_email_id = $DmiUsers->find('all',array('fields'=>'email', 'conditions'=>array('id IS'=>$pao_user_id['pao_user_id'])))->first();
		
			$lims_sample_payment_detailsEntity = $LimsSamplePaymentDetails->newEntity(array(

				'sample_code'=>$sample_code,
				'amount_paid'=>$payment_amount,
				'transaction_id'=>$payment_transaction_id,
				'transaction_date'=>$payment_trasaction_date,
				'payment_receipt_docs'=>$payment_receipt_docs,
				'bharatkosh_payment_done'=>$bharatkosh_payment_done,
				'reason_option_comment'=>$payment_confirmation_query['reason_option_comment'],
				'reason_comment'=>$payment_confirmation_query['reason_comment'],
				'district_id'=>$district_id,
				'payment_confirmation'=>'replied',
				'pao_id'=>$pao_id,
				'created'=>date('Y-m-d H:i:s'),
				'modified'=>date('Y-m-d H:i:s')
			));
						
			if($LimsSamplePaymentDetails->save($lims_sample_payment_detailsEntity)){
				
				$user_email_id = $pao_user_email_id['email'];
				$current_level = 'pao';
				$DmiSmsEmailTemplates->sendMessage(2056,$sample_code);
		
				return true;	
			}

		}else{

			$lims_sample_payment_detailsEntity = $LimsSamplePaymentDetails->newEntity(array(

				'sample_code'=>$sample_code,
				'amount_paid'=>$payment_amount,
				'transaction_id'=>$payment_transaction_id,
				'transaction_date'=>$payment_trasaction_date,
				'payment_receipt_docs'=>$payment_receipt_docs,
				'bharatkosh_payment_done'=>$bharatkosh_payment_done,
				'payment_confirmation'=>'saved',
				'district_id'=>$district_id, 
				'pao_id'=>$pao_id,
				'created'=>date('Y-m-d H:i:s'),
				'modified'=>date('Y-m-d H:i:s')
			));

			if($LimsSamplePaymentDetails->save($lims_sample_payment_detailsEntity)){
				
				return true;	
			}
		}
		
	}




	public function getSmsId($sms_action){

		$user_role = $_SESSION['role'];


		if ($sms_action=='inward') {
			$from_id = 75;
			$to_id = 76;
		}


		$Workflow = TableRegistry::getTableLocator()->get('Workflow');

		$sample_code = $_SESSION['org_sample_code'];
		$sendingTo = $_SESSION['user_code'];
		
		$getDetails =	$Workflow->find('all')->where(['stage_smpl_cd' => $sample_code])->order('id desc')->first();
		$receiver = $getDetails['dst_usr_cd'];
		
		return array('from_user'=>$sendingTo,'from_sms_id'=>$from_id,'to_user'=>$receiver,'to_sms_id'=>$to_id);
	}


}


?>
