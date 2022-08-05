<?php
	
	namespace app\Model\Table;
	use Cake\ORM\Table;
	use App\Model\Model;
	use App\Controller\AppController;
	use App\Controller\CustomersController;
	use Cake\ORM\TableRegistry;
	use Cake\Utility\Hash;
	use Cake\Datasource\ConnectionManager;

	class DmiSmsEmailTemplatesTable extends Table{

		var $name = "DmiSmsEmailTemplates";
						
		public $validate = array(
		
			'sms_message'=>array(	
			
						'rule' => 'notBlank',
					),
			'email_message'=>array(
			
						'rule' => 'notBlank',
					),
			'description'=>array(
					
					'rule' => 'notBlank',
				),
			'template_for'=>array(
					'rule'=>array('maxLength',20),
					'allowEmpty'=>false,	
				),	
			'email_subject'=>array(
					'rule'=>array('maxLength',200),
					'allowEmpty'=>false,	
				),		
				
		);
		
		
	public function sendMessage($message_id,$userCode,$sample_code) {

		
		$DmiUsers = TableRegistry::getTableLocator()->get('DmiUsers');
		$DmiSentSmsLogs = TableRegistry::getTableLocator()->get('DmiSentSmsLogs');
		$DmiSentEmailLogs = TableRegistry::getTableLocator()->get('DmiSentEmailLogs');
		$Workflow = TableRegistry::getTableLocator()->get('Workflow');
		

		$find_message_record = $this->find('all',array('conditions'=>array('id IS'=>$message_id, 'status'=>'active')))->first();

		if (!empty($find_message_record)) {

			$getUserId = $this->getUserDet($userCode,$find_message_record['destination']);

			$m=0;
			$e=0;
			$destination_mob_nos = array();
			$log_dest_mob_nos = array();
			$destination_email_ids = array();

			

			// Inward Officer
			if ($getUserId == 101) { 

				$inward_data = $DmiUsers->getUserDetailsById($userCode);

				if (!empty($inward_data)) {

					if (trim($inward_data['role']) == 'Inward Officer') {

						$email_id = base64_decode($inward_data['email']); //for email encoding
						$mobile_no = base64_decode($inward_data['phone']); //for mobile encoding

						//This is addded on 27-04-2021 for base64decoding by AKASH
						$destination_mob_nos[$m] = '91'.$mobile_no; 
						$log_dest_mob_nos[$m] = '91'.$mobile_no;
						$destination_email_ids[$e] = $email_id;
					
					}

				} else {
					
					$destination_mob_nos[$m] = null;
					$log_dest_mob_nos[$m] = null;
					$destination_email_ids[$e] = null;
				}

				$m=$m+1;
				$e=$e+1;
			}

			
			// RAL/CAL OIC
			if ($getUserId == 102) { 

				$ral_cal_oic_data = $DmiUsers->getUserDetailsById($userCode);
	
				if (!empty($ral_cal_oic_data)) {

					if (trim($ral_cal_oic_data['role']) == 'RAL/CAL OIC') {

						$email_id = base64_decode($ral_cal_oic_data['email']); //for email encoding
						$mobile_no = base64_decode($ral_cal_oic_data['phone']); //for mobile encoding

						//This is addded on 27-04-2021 for base64decoding by AKASH
						$destination_mob_nos[$m] = '91'.$mobile_no; 
						$log_dest_mob_nos[$m] = '91'.$mobile_no;
						$destination_email_ids[$e] = $email_id;
					}

				} else {
					
					$destination_mob_nos[$m] = null;
					$log_dest_mob_nos[$m] = null;		   
					$destination_email_ids[$e] = null;
				}

				$m=$m+1;
				$e=$e+1;
				
			}


			//Chemist
			if ($getUserId == 103) { 

				$jr_chemist_data = $DmiUsers->getUserDetailsById($userCode);

				if (!empty($jr_chemist_data)) {
					
					if (trim($jr_chemist_data['role']) == 'Jr Chemist') {

						$email_id = base64_decode($jr_chemist_data['email']); //for email encoding
						$mobile_no = base64_decode($jr_chemist_data['phone']); //for mobile encoding

						//This is addded on 27-04-2021 for base64decoding by AKASH
						$destination_mob_nos[$m] = '91'.$mobile_no; 
						$log_dest_mob_nos[$m] = '91'.$mobile_no;
						$destination_email_ids[$e] = $email_id;
					}

				} else {
					
					$destination_mob_nos[$m] = null;
					$log_dest_mob_nos[$m] = null;
					$destination_email_ids[$e] = null;
				}

				$m=$m+1;
				$e=$e+1;
				
			}
		

			// Chief chemist
			if ($getUserId == 104) { 

				$sr_chemist_data = $DmiUsers->getUserDetailsById($userCode);

				if (!empty($sr_chemist_data)) {
					
					if (trim($sr_chemist_data['role']) == 'Sr Chemist') {

						$email_id = base64_decode($sr_chemist_data['email']); //for email encoding
						$mobile_no = base64_decode($sr_chemist_data['phone']); //for mobile encoding

						//This is addded on 27-04-2021 for base64decoding by AKASH
						$destination_mob_nos[$m] = '91'.$mobile_no; 
						$log_dest_mob_nos[$m] = '91'.$mobile_no;
						$destination_email_ids[$e] = $email_id;
					}

				} else {
					
					$destination_mob_nos[$m] = null;
					$log_dest_mob_nos[$m] = null;
					$destination_email_ids[$e] = null;
				}

				$m=$m+1;
				$e=$e+1;
				
			}
	

			// Lab Incharge
			if ($getUserId == 105) { 

				$lab_incharge_data = $DmiUsers->getUserDetailsById($userCode);

				if (!empty($lab_incharge_data)) {
					
					if (trim($lab_incharge_data['role']) == 'Sr Chemist') {

						$email_id = base64_decode($lab_incharge_data['email']); //for email encoding
						$mobile_no = base64_decode($lab_incharge_data['phone']); //for mobile encoding

						//This is addded on 27-04-2021 for base64decoding by AKASH
						$destination_mob_nos[$m] = '91'.$mobile_no; 
						$log_dest_mob_nos[$m] = '91'.$mobile_no;
						$destination_email_ids[$e] = $email_id;
					}

				} else {
					
					$destination_mob_nos[$m] = null;
					$log_dest_mob_nos[$m] = null;
					$destination_email_ids[$e] = null;
				}

				$m=$m+1;
				$e=$e+1;
				
			}
		

			// DOL
			if ($getUserId == 106) { 

				$dol_data = $DmiUsers->getUserDetailsById($userCode);

				if (!empty($dol_data)) {
					
					if (trim($dol_data['role']) == 'dol') {

						$email_id = base64_decode($dol_data['email']); //for email encoding
						$mobile_no = base64_decode($dol_data['phone']); //for mobile encoding

						//This is addded on 27-04-2021 for base64decoding by AKASH
						$destination_mob_nos[$m] = '91'.$mobile_no; 
						$log_dest_mob_nos[$m] = '91'.$mobile_no;
						$destination_email_ids[$e] = $email_id;
					}

				} else {
					
					$destination_mob_nos[$m] = null;
					$log_dest_mob_nos[$m] = null;
					$destination_email_ids[$e] = null;
				}

				$m=$m+1;
				$e=$e+1;
				
			}


			// Inward Clerk
			if ($getUserId == 107) { 

				$inward_clerk_data = $DmiUsers->getUserDetailsById($userCode);

				if (!empty($inward_clerk_data)) {
					
					if (trim($inward_clerk_data['role']) == 'Inward Clerk') {

						$email_id = base64_decode($inward_clerk_data['email']); //for email encoding
						$mobile_no = base64_decode($inward_clerk_data['phone']); //for mobile encoding

						//This is addded on 27-04-2021 for base64decoding by AKASH
						$destination_mob_nos[$m] = '91'.$mobile_no; 
						$log_dest_mob_nos[$m] = '91'.$mobile_no;
						$destination_email_ids[$e] = $email_id;
					}

				} else {
					
					$destination_mob_nos[$m] = null;
					$log_dest_mob_nos[$m] = null;
					$destination_email_ids[$e] = null;
				}

				$m=$m+1;
				$e=$e+1;
				
			}

			// Outward Clerk
			if ($getUserId == 108) { 

				$outward_clerk_data = $DmiUsers->getUserDetailsById($userCode);

				if (!empty($outward_clerk_data)) {
					
					if (trim($outward_clerk_data['role']) == 'Outward Clerk') {

						$email_id = base64_decode($outward_clerk_data['email']); //for email encoding
						$mobile_no = base64_decode($outward_clerk_data['phone']); //for mobile encoding

						//This is addded on 27-04-2021 for base64decoding by AKASH
						$destination_mob_nos[$m] = '91'.$mobile_no; 
						$log_dest_mob_nos[$m] = '91'.$mobile_no;
						$destination_email_ids[$e] = $email_id;
					}

				} else {
					
					$destination_mob_nos[$m] = null;
					$log_dest_mob_nos[$m] = null;
					$destination_email_ids[$e] = null;
				}

				$m=$m+1;
				$e=$e+1;
				
			}

			// RO Officer
			if ($getUserId == 109) { 

				$ro_data = $DmiUsers->getUserDetailsById($userCode);

				if (!empty($ro_data)) {
					
					if (trim($ro_data['role']) == 'RO Officer') {

						$email_id = base64_decode($ro_data['email']); //for email encoding
						$mobile_no = base64_decode($ro_data['phone']); //for mobile encoding

						//This is addded on 27-04-2021 for base64decoding by AKASH
						$destination_mob_nos[$m] = '91'.$mobile_no; 
						$log_dest_mob_nos[$m] = '91'.$mobile_no;
						$destination_email_ids[$e] = $email_id;
					}

				} else {
					
					$destination_mob_nos[$m] = null;
					$log_dest_mob_nos[$m] = null;
					$destination_email_ids[$e] = null;
				}

				$m=$m+1;
				$e=$e+1;
				
			}

			// RO/SO-OIC
			if ($getUserId == 110) { 

				$ro_so_oic_data = $DmiUsers->getUserDetailsById($userCode);

				if (!empty($ro_so_oic_data)) {
					
					if (trim($ro_so_oic_data['role']) == 'RO/SO-OIC') {

						$email_id = base64_decode($ro_so_oic_data['email']); //for email encoding
						$mobile_no = base64_decode($ro_so_oic_data['phone']); //for mobile encoding

						//This is addded on 27-04-2021 for base64decoding by AKASH
						$destination_mob_nos[$m] = '91'.$mobile_no; 
						$log_dest_mob_nos[$m] = '91'.$mobile_no;
						$destination_email_ids[$e] = $email_id;
					}

				} else {
					
					$destination_mob_nos[$m] = null;
					$log_dest_mob_nos[$m] = null;
					$destination_email_ids[$e] = null;
				}

				$m=$m+1;
				$e=$e+1;
				
			}

			// PAO/DDO
			if ($getUserId == 111) { 

				$ddo_data = $DmiUsers->getUserDetailsById($userCode);

				if (!empty($ddo_data)) {

					$email_id = base64_decode($ddo_data['email']); //for email encoding
					$mobile_no = base64_decode($ddo_data['phone']); //for mobile encoding

					//This is addded on 27-04-2021 for base64decoding by AKASH
					$destination_mob_nos[$m] = '91'.$mobile_no; 
					$log_dest_mob_nos[$m] = '91'.$mobile_no;
					$destination_email_ids[$e] = $email_id;

				} else {
					
					$destination_mob_nos[$m] = null;
					$log_dest_mob_nos[$m] = null;
					$destination_email_ids[$e] = null;
				}

				$m=$m+1;
				$e=$e+1;
				
			}

			// HO
			if ($getUserId == 112) { 

				$ho_data = $DmiUsers->getUserDetailsById($userCode);

				if (!empty($ho_data)) {
					
					if (trim($ho_data['role']) == 'Head Office') {

						$email_id = base64_decode($ho_data['email']); //for email encoding
						$mobile_no = base64_decode($ho_data['phone']); //for mobile encoding

						//This is addded on 27-04-2021 for base64decoding by AKASH
						$destination_mob_nos[$m] = '91'.$mobile_no; 
						$log_dest_mob_nos[$m] = '91'.$mobile_no;
						$destination_email_ids[$e] = $email_id;
					}

				} else {
					
					$destination_mob_nos[$m] = null;
					$log_dest_mob_nos[$m] = null;
					$destination_email_ids[$e] = null;
				}

				$m=$m+1;
				$e=$e+1;
				
			}
		

			// SO Officer
			if ($getUserId == 113) { 

				$so_data = $DmiUsers->getUserDetailsById($userCode);

				if (!empty($so_data)) {
					
					if (trim($so_data['role']) == 'Head Office') {

						$email_id = base64_decode($so_data['email']); //for email encoding
						$mobile_no = base64_decode($so_data['phone']); //for mobile encoding

						//This is addded on 27-04-2021 for base64decoding by AKASH
						$destination_mob_nos[$m] = '91'.$mobile_no; 
						$log_dest_mob_nos[$m] = '91'.$mobile_no;
						$destination_email_ids[$e] = $email_id;
					}

				} else {
					
					$destination_mob_nos[$m] = null;
					$log_dest_mob_nos[$m] = null;
					$destination_email_ids[$e] = null;
				}

				$m=$m+1;
				$e=$e+1;
				
			}
			
		
			$sms_message = $find_message_record['sms_message']; 
			$destination_mob_nos_values = implode(',',$destination_mob_nos);
			$log_dest_mob_nos_values = implode(',',$log_dest_mob_nos);
			$email_message = $find_message_record['email_message'];
			$destination_email_ids_values = implode(',',$destination_email_ids);
			$email_subject = $find_message_record['email_subject'];
			$template_id = $find_message_record['template_id'];//added on 12-05-2021 by Amol, new field
			
			//replacing dynamic values in the email message
			$sms_message = $this->replaceDynamicValuesFromMessage($sms_message,$userCode,$sample_code);

			//replacing dynamic values in the email message
			$email_message = $this->replaceDynamicValuesFromMessage($email_message,$userCode,$sample_code);
			
			print_r($sms_message);
			print_r("</br>");
			print_r($destination_mob_nos_values);
			print_r("</br>");
			print_r($destination_email_ids_values);
			print_r("</br>");
			print_r($email_message);
			exit;

			//To send SMS on list of mobile nos.
			if (!empty($find_message_record['sms_message'])) {

				//code to send sms starts here	
				//echo "sendsms.php";
				// Initialize the sender variable
			/*	$uname="aqcms.sms";
				$pass="Y%26nF4b%237q";
				$send=urlencode("AGMARK");
				$dest=$destination_mob_nos_values;
				$msg=urlencode($sms_message);
			
				// Initialize the URL variable
				$URL="http://smsgw.sms.gov.in/failsafe/HttpLink";
				// Create and initialize a new cURL resource
				$ch = curl_init();
				// Set URL to URL variable
				curl_setopt($ch, CURLOPT_URL,$URL);
				// Set URL HTTPS post to 1
				curl_setopt($ch, CURLOPT_POST, true);
				// Set URL HTTPS post field values
				
				$entity_id = '1101424110000041576'; //updated on 18-11-2020
				
				// if message lenght is greater than 160 character then add one more parameter "concat=1" (Done by pravin 07-03-2018)
				if (strlen($msg) <= 160 ) {
					
					curl_setopt($ch, CURLOPT_POSTFIELDS,"username=$uname&pin=$pass&signature=$send&mnumber=$dest&message=$msg&dlt_entity_id=$entity_id&dlt_template_id=$template_id");
				
				} else {  
					
					curl_setopt($ch, CURLOPT_POSTFIELDS,"username=$uname&pin=$pass&signature=$send&mnumber=$dest&message=$msg&concat=1&dlt_entity_id=$entity_id&dlt_template_id=$template_id");
				}
				
				// Set URL return value to True to return the transfer as a string
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				// The URL session is executed and passed to the browser
				$curl_output =curl_exec($ch);
				//echo $curl_output;
			*/	
				//code to send sms ends here	
			
				
				//query to save SMS sending logs in DB // added on 11-10-2017
				$DmiSentSmsLogsEntity = $DmiSentSmsLogs->newEntity(array(
					'message_id'=>$message_id,
					'destination_list'=>$log_dest_mob_nos_values,
					'mid'=>null,
					'sent_date'=>date('Y-m-d H:i:s'),
					'message'=>$sms_message,
					'created'=>date('Y-m-d H:i:s'),
					'template_id'=>$template_id //added on 12-05-2021 by Amol
				)); 

				$DmiSentSmsLogs->save($DmiSentSmsLogsEntity);
				
			}
			
		
			//email format to send on mail with content from master
			$email_format = 'Dear Sir/Madam' . "\r\n\r\n" .$email_message. "\r\n\r\n" .
							'Thanks & Regards,' . "\r\n" .
							'Directorate of Marketing & Inspection,' . "\r\n" .
							'Ministry of Agriculture and Farmers Welfare,' . "\r\n" .
							'Government of India.';
			

			
			//To send Email on list of Email ids.
			if (!empty($find_message_record['email_message'])) {
				
				$to = $destination_email_ids_values;
				$subject = $email_subject;
				$txt = $email_format;
				$headers = "From: dmiqc@nic.in";

			//	mail($to,$subject,$txt,$headers);
				
				
				
				//query to save Email sending logs in DB // added on 11-10-2017
				$DmiSentEmailLogsEntity = $DmiSentEmailLogs->newEntity(array(
				
					'message_id'=>$message_id,
					'destination_list'=>$destination_email_ids_values,
					'sent_date'=>date('Y-m-d H:i:s'),
					'message'=>$sms_message,
					'created'=>date('Y-m-d H:i:s'),
					'template_id'=>$template_id //added on 12-05-2021 by Amol
				
				));
				$DmiSentEmailLogs->save($DmiSentEmailLogsEntity);
				
			}
		
		}//end of 1st if condition 24-07-2018
		
	}
		


	//REPLACE DYNAMIC VALUES IN MESSAGE STRING
	public function replaceDynamicValuesFromMessage($message,$userCode,$sample_code) {
		pr($message);
		//Getting Count Before Execution
		$total_occurrences = substr_count($message,"%%");

		while($total_occurrences > 0){

			$matches = explode('%%',$message);//getting string between %% & %%

			if (!empty($matches[1])) {

				switch ($matches[1]) {

					case "sample_code":
						$message = str_replace("%%sample_code%%",$this->getReplaceDynamicValues('sample_code',$userCode,$sample_code),$message);
					break;

					case "commodities":
						$message = str_replace("%%commodities%%",$this->getReplaceDynamicValues('commodities',$userCode,$sample_code),$message);
					break;

					case "src_user":
						$message = str_replace("%%src_user%%",$this->getReplaceDynamicValues('src_user',$userCode,$sample_code),$message);
					break;
			
					case "src_usr_role":
						$message = str_replace("%%src_usr_role%%",$this->getReplaceDynamicValues('src_usr_role',$userCode,$sample_code),$message);
					break;

					case "src_office":
						$message = str_replace("%%src_office%%",$this->getReplaceDynamicValues('src_office',$userCode,$sample_code),$message);
					break;

					case "dst_user":
						$message = str_replace("%%dst_user%%",$this->getReplaceDynamicValues('dst_user',$userCode,$sample_code),$message);
					break;

					case "dst_usr_role":
						$message = str_replace("%%dst_usr_role%%",$this->getReplaceDynamicValues('dst_usr_role',$userCode,$sample_code),$message);
					break;

					case "dst_office":
						$message = str_replace("%%dst_office%%",$this->getReplaceDynamicValues('dst_office',$userCode,$sample_code),$message);
					break;

					case "sample_flow":
						$message = str_replace("%%sample_flow%%",$this->getReplaceDynamicValues('sample_flow',$userCode,$sample_code),$message);
					break;

					case "category":
						$message = str_replace("%%category%%",$this->getReplaceDynamicValues('category',$userCode,$sample_code),$message);
					break;
						
					case "sample_date":
						$message = str_replace("%%sample_date%%",$this->getReplaceDynamicValues('sample_date',$userCode,$sample_code),$message);
					break;

					case "letr_ref_no":
						$message = str_replace("%%letr_ref_no%%",$this->getReplaceDynamicValues('letr_ref_no',$userCode,$sample_code),$message);
					break;

					case "ref_src_code":
						$message = str_replace("%%ref_src_code%%",$this->getReplaceDynamicValues('ref_src_code',$userCode,$sample_code),$message);
					break;

					case "exp_sample":
						$message = str_replace("%%exp_sample%%",$this->getReplaceDynamicValues('exp_sample',$userCode,$sample_code),$message);
					break;

					default:
						$message = $this->replaceBetween($message, '%%', '%%', '');	
						$default_value = 'yes';
					break;
				}
			}
				
			if (empty($default_value)) {	
			
				$total_occurrences = substr_count($message,"%%");//getting count after execution
				
			} else {
			
				$total_occurrences = $total_occurrences - 1;
			}
		}
		
		return $message;
	
	}
		
	
	//GET REPLACE DYNAMIC VALUES IN MESSAGE STRING
	public function getReplaceDynamicValues($replace_variable_value,$userCode,$sample_code) {
		
		$Workflow = TableRegistry::getTableLocator()->get('Workflow');
		$DmiUsers = TableRegistry::getTableLocator()->get('DmiUsers');
		$DmiRoOffices = TableRegistry::getTableLocator()->get('DmiRoOffices');
		$MCommodityCategory = TableRegistry::getTableLocator()->get('MCommodityCategory');
		$MCommodity = TableRegistry::getTableLocator()->get('MCommodity');
		$SampleInward = TableRegistry::getTableLocator()->get('SampleInward');
		$MSampleType = TableRegistry::getTableLocator()->get('MSampleType');
		//Get the Source User and their role from sample 

		$workflowData = $Workflow->find('all')->where(['stage_smpl_cd' => $sample_code])->order('id desc')->first();
		
		if(trim($workflowData['stage_smpl_flag']) == 'AS'){
			$workflowData = $Workflow->find('all')->where(['stage_smpl_cd' => $sample_code, 'stage_smpl_flag' => 'OF'])->first();
		}

		$sampleDetails = $SampleInward->getSampleDetailsByCode($workflowData['org_sample_code']);
		
		$sample_type = $MSampleType->getSampleType($sampleDetails['sample_type_code']);

		$commodities = $MCommodity->getCommodity($sampleDetails['commodity_code']);

		$get_category = $MCommodityCategory->getCategory($sampleDetails['category_code']);
		
		//Get User Name
		$src_user_name =  $DmiUsers->getUserDetailsById($workflowData['src_usr_cd']);
		$dst_user_name =  $DmiUsers->getUserDetailsById($workflowData['dst_usr_cd']);

		$month_name = $this->getMonthName($sampleDetails['expiry_month']);
		
		//User Role
		$src_usr_role =  $DmiUsers->getUserDetailsById($workflowData['src_usr_cd']);
		$dst_usr_role =  $DmiUsers->getUserDetailsById($workflowData['dst_usr_cd']);

		//Offices
		$src_office = $DmiRoOffices->getOfficeDetailsById($workflowData['src_loc_id']);
		$dst_office = $DmiRoOffices->getOfficeDetailsById($workflowData['dst_loc_id']);
		


		switch ($replace_variable_value) {
				
			case "sample_code":
				return $sample_code;
			break;

			case "commodities":
				return $commodities;
			break;

			case "src_user":
				$src_user = $src_user_name['f_name']." ".$src_user_name['l_name']; 
				return $src_user; 
			break;

			case "src_usr_role":
				return $src_usr_role['role'];
			break;	
			
			case "src_office":
				return $src_office[0]; 
			break;

			case "dst_user":
				$dst_user = $dst_user_name['f_name']." ".$dst_user_name['l_name'];
				return $dst_user; 
			break;
			
			case "dst_usr_role":
				return $dst_usr_role['role'];
			break;

			case "dst_office":
				return $dst_office[0]; 
			break;

			case "sample_flow":
				$sample_flow = $sample_type;
				return $sample_flow; 
			break;

			case "category":
				$category = $get_category; 
				return $category; 
			break;

			case "sample_date":
				$sample_date = $sampleDetails['created']; 
				return $sample_date; 
			break;

			case "letr_ref_no":
				$letr_ref_no = $sampleDetails['letr_ref_no'];
				return $letr_ref_no; 
			break;

			case "ref_src_code":
				$ref_src_code = $sampleDetails['ref_src_code'];
				return $ref_src_code; 
			break;

			case "exp_sample":
				$exp_sample = $month_name." ".$sampleDetails['expiry_year']; 
				return $exp_sample; 
			break;

			default:	
				$message = '%%';
			break;
		}
	}
		

	// This function replace the value between two character  (Done By pravin 9-08-2018)
	function replaceBetween($str, $needle_start, $needle_end, $replacement) {

		$pos = strpos($str, $needle_start);
		$start = $pos === false ? 0 : $pos + strlen($needle_start);

		$pos = strpos($str, $needle_end, $start);
		$end = $start === false ? strlen($str) : $pos;

		return substr_replace($str,$replacement,$start);
	}


	// This function replace the value between two character  (Done By pravin 9-08-2018)
	function getUserDet($userCode,$destination_values) {

		$DmiUsers = TableRegistry::getTableLocator()->get('DmiUsers');
		$details = $DmiUsers->getUserDetailsById($userCode);
		$getID = $this->smsUserId(trim($details['role']));
		$destination_array = explode(',',$destination_values);
		$lookUp = in_array($getID,$destination_array);
		if ($lookUp==1) {
			return $getID;
		}
	}


	
	public function smsUserId($role){

		//Current selected values from edit page for LMIS
		
		if ($role == 'Inward Officer') { $dest_id = 101; }

		if ($role == 'RAL/CAL OIC') { $dest_id = 102; }

		if ($role == 'Jr Chemist') { $dest_id = 103; }

		if ($role == 'Sr Chemist') { $dest_id = 104; }

		if ($role == 'Lab Incharge') { $dest_id = 105; }

		if ($role == 'DOL') { $dest_id = 106; }

		if ($role == 'inward_clerk') { $dest_id = 107; }

		if ($role == 'outward_clerk') { $dest_id = 108; }

		if ($role == 'RO Officer') { $dest_id = 109; }

		if ($role == 'RO/SO OIC') { $dest_id = 110; }

		if ($role == 'accounts') { $dest_id = 111; }

		if ($role == 'Head Office') { $dest_id = 112; }

		if ($role == 'SO Officer') { $dest_id = 113; }


		return $dest_id;
	}



	//This function is created for convert the month no to month name
	function getMonthName($value){
		$monthName = date("F", mktime(0, 0, 0, $value, 10));
		return $monthName;
	}
}
