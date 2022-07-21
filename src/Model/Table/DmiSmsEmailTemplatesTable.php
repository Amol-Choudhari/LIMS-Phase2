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
		
		
		public function sendMessage($message_id, $sample_code, $userCode=null) {

			
			$DmiUsers = TableRegistry::getTableLocator()->get('DmiUsers');
			$DmiSentSmsLogs = TableRegistry::getTableLocator()->get('DmiSentSmsLogs');
			$DmiSentEmailLogs = TableRegistry::getTableLocator()->get('DmiSentEmailLogs');
			$Workflow = TableRegistry::getTableLocator()->get('Workflow');
			

			$find_message_record = $this->find('all',array('conditions'=>array('id IS'=>$message_id, 'status'=>'active')))->first();

			if (!empty($find_message_record)) {

				$destination_values = $find_message_record['destination'];				
				$destination_array = explode(',',$destination_values);
				$m=0;
				$e=0;
				$destination_mob_nos = array();
				$log_dest_mob_nos = array();				
				$destination_email_ids = array();

					
				//Sample Inward Confirmed
				if (in_array(101,$destination_array)) { 

					$sampleDetailsData = $Workflow->find('all',array('conditions'=>array('stage_smpl_cd IS'=>$sample_code)))->first();
					
					if (!empty($sampleDetailsData)) {

						$inwardOfficerData = $DmiUsers->find('all',array('conditions'=>array('id IS'=>$userCode)))->first();
						$source_user_email_id = base64_decode($inwardOfficerData['email']); //for email encoding
						$source_user_mobile_no = base64_decode($inwardOfficerData['phone']); //for mobile encoding

						//This is addded on 27-04-2021 for base64decoding by AKASH
						$destination_mob_nos[$m] = '91'.$source_user_mobile_no; 
						$log_dest_mob_nos[$m] = '91'.$source_user_mobile_no;
						$destination_email_ids[$e] = $source_user_email_id;

					} else {
						
						$destination_mob_nos[$m] = null;
						$log_dest_mob_nos[$m] = null;
						$destination_email_ids[$e] = null;
					}

					$m=$m+1;
					$e=$e+1;
				}

				
				//Sample Inward Confirmed - To
				if (in_array(102,$destination_array)) {


					$sampleDetailsData = $Workflow->find('all',array('conditions'=>array('stage_smpl_cd IS'=>$sample_code)))->first();
		
					if (!empty($sampleDetailsData)) {

						$ralCalOicOfficerData = $DmiUsers->find('all',array('conditions'=>array('id IS'=>$userCode)))->first();
					
						$destination_user_mobile_no = base64_decode($ralCalOicOfficerData['phone']); //for mobile encoding
						$destination_user_email_id = base64_decode($ralCalOicOfficerData['email']); //for email encoding
						
						//This is addded on 27-04-2021 for base64decoding by AKASH
						$destination_mob_nos[$m] = '91'.$destination_user_mobile_no;
						$log_dest_mob_nos[$m] = '91'.$destination_user_mobile_no;
						$destination_email_ids[$e] = $destination_user_email_id;
					
					} else {
						
						$destination_mob_nos[$m] = null;
						$log_dest_mob_nos[$m] = null;		   
						$destination_email_ids[$e] = null;
					}

					$m=$m+1;
					$e=$e+1;
					
				}

				//Chemist
				if (in_array(103,$destination_array)) {

					$sample = $Workflow->find('all',array('conditions'=>array('stage_smpl_cd IS'=>$sample_code)))->first();
					$destination_user_code = $sample['user_code'];

					if (!empty($destination_user_code)) {
						
						$chemistData = $DmiUsers->find('all',array('conditions'=>array('id IS'=>$userCode)))->first();
						$destination_user_mobile_no = $chemistData['phone'];
						$destination_user_email_id = $chemistData['email'];
						
						//This is addded on 27-04-2021 for base64decoding by AKASH
						$destination_mob_nos[$m] = '91'.$destination_user_mobile_no;
						$log_dest_mob_nos[$m] = '91'.$destination_user_mobile_no;
						$destination_email_ids[$e] = $destination_user_email_id;
					
					} else {
						
						$destination_mob_nos[$m] = null;
						$log_dest_mob_nos[$m] = null;		   
						$destination_email_ids[$e] = null;
					}

					$m=$m+1;
					$e=$e+1;
					
				}
			
				//chief_chemist
				if (in_array(104,$destination_array)) {

					$sample = $Workflow->find('all',array('conditions'=>array('stage_smpl_cd IS'=>$sample_code)))->first();
					$destination_user_code = $sample['user_code'];

					if (!empty($destination_user_code)) {
						
						$chiefChemistData = $DmiUsers->find('all',array('conditions'=>array('id IS'=>$userCode)))->first();
						$destination_user_mobile_no = $chiefChemistData['phone'];
						$destination_user_email_id = $chiefChemistData['email'];
						
						//This is addded on 27-04-2021 for base64decoding by AKASH
						$destination_mob_nos[$m] = '91'.base64_decode($destination_user_mobile_no);
						$log_dest_mob_nos[$m] = '91'.$destination_user_mobile_no;
						$destination_email_ids[$e] = base64_decode($destination_user_email_id);
					
					} else {
						
						$destination_mob_nos[$m] = null;
						$log_dest_mob_nos[$m] = null;		   
						$destination_email_ids[$e] = null;
					}

					$m=$m+1;
					$e=$e+1;
					
				}
		

				//lab_incharge	
				if (in_array(105,$destination_array)) {

					$sample = $Workflow->find('all',array('conditions'=>array('stage_smpl_cd IS'=>$sample_code)))->first();
					$destination_user_code = $sample['user_code'];

					if (!empty($destination_user_code)) {
						
						$labInchargeData = $DmiUsers->find('all',array('conditions'=>array('id IS'=>$userCode)))->first();
						$destination_user_mobile_no = $labInchargeData['phone'];
						$destination_user_email_id = $labInchargeData['email'];
						
						//This is addded on 27-04-2021 for base64decoding by AKASH
						$destination_mob_nos[$m] = '91'.base64_decode($destination_user_mobile_no);
						$log_dest_mob_nos[$m] = '91'.$destination_user_mobile_no;
						$destination_email_ids[$e] = base64_decode($destination_user_email_id);
					
					} else {
						
						$destination_mob_nos[$m] = null;
						$log_dest_mob_nos[$m] = null;		   
						$destination_email_ids[$e] = null;
					}

					$m=$m+1;
					$e=$e+1;
					
				}
			
				//dol
				if (in_array(106,$destination_array)) {

					$sample = $Workflow->find('all',array('conditions'=>array('stage_smpl_cd IS'=>$sample_code)))->first();
					$destination_user_code = $sample['user_code'];

					if (!empty($destination_user_code)) {
						
						$dolData = $DmiUsers->find('all',array('conditions'=>array('id IS'=>$userCode)))->first();
						$destination_user_mobile_no = $dolData['phone'];
						$destination_user_email_id = $dolData['email'];
						
						//This is addded on 27-04-2021 for base64decoding by AKASH
						$destination_mob_nos[$m] = '91'.base64_decode($destination_user_mobile_no);
						$log_dest_mob_nos[$m] = '91'.$destination_user_mobile_no;
						$destination_email_ids[$e] = base64_decode($destination_user_email_id);
					
					} else {
						
						$destination_mob_nos[$m] = null;
						$log_dest_mob_nos[$m] = null;		   
						$destination_email_ids[$e] = null;
					}

					$m=$m+1;
					$e=$e+1;
					
				}

				//inward_clerk
				if (in_array(107,$destination_array)) {

					$sample = $Workflow->find('all',array('conditions'=>array('stage_smpl_cd IS'=>$sample_code)))->first();
					$destination_user_code = $sample['user_code'];

					if (!empty($destination_user_code)) {
						
						$inwardClerkData = $DmiUsers->find('all',array('conditions'=>array('id IS'=>$userCode)))->first();
						$destination_user_mobile_no = $inwardClerkData['phone'];
						$destination_user_email_id = $inwardClerkData['email'];
						
						//This is addded on 27-04-2021 for base64decoding by AKASH
						$destination_mob_nos[$m] = '91'.base64_decode($destination_user_mobile_no);
						$log_dest_mob_nos[$m] = '91'.$destination_user_mobile_no;
						$destination_email_ids[$e] = base64_decode($destination_user_email_id);
					
					} else {
						
						$destination_mob_nos[$m] = null;
						$log_dest_mob_nos[$m] = null;		   
						$destination_email_ids[$e] = null;
					}

					$m=$m+1;
					$e=$e+1;
					
				}

				//outward_clerk
				if (in_array(108,$destination_array)) {

					$sample = $Workflow->find('all',array('conditions'=>array('stage_smpl_cd IS'=>$sample_code)))->first();
					$destination_user_code = $sample['user_code'];

					if (!empty($destination_user_code)) {
						
						$outwardClerkData = $DmiUsers->find('all',array('conditions'=>array('id IS'=>$userCode)))->first();
						$destination_user_mobile_no = $outwardClerkData['phone'];
						$destination_user_email_id = $outwardClerkData['email'];
						
						//This is addded on 27-04-2021 for base64decoding by AKASH
						$destination_mob_nos[$m] = '91'.base64_decode($destination_user_mobile_no);
						$log_dest_mob_nos[$m] = '91'.$destination_user_mobile_no;
						$destination_email_ids[$e] = base64_decode($destination_user_email_id);
					
					} else {
						
						$destination_mob_nos[$m] = null;
						$log_dest_mob_nos[$m] = null;		   
						$destination_email_ids[$e] = null;
					}

					$m=$m+1;
					$e=$e+1;
					
				}

				//RO SO officer
				if (in_array(109,$destination_array)) {

					$sampleDetailsData = $Workflow->find('all',array('conditions'=>array('stage_smpl_cd IS'=>$sample_code)))->first();

					if (!empty($sampleDetailsData)) {
						
						$roSoOfficerData = $DmiUsers->find('all',array('conditions'=>array('id IS'=>$userCode)))->first();
						$destination_user_mobile_no = $roSoOfficerData['phone'];
						$destination_user_email_id = $roSoOfficerData['email'];
						
						//This is addded on 27-04-2021 for base64decoding by AKASH
						$destination_mob_nos[$m] = '91'.base64_decode($destination_user_mobile_no);
						$log_dest_mob_nos[$m] = '91'.$destination_user_mobile_no;
						$destination_email_ids[$e] = base64_decode($destination_user_email_id);
					
					} else {
						
						$destination_mob_nos[$m] = null;
						$log_dest_mob_nos[$m] = null;		   
						$destination_email_ids[$e] = null;
					}

					$m=$m+1;
					$e=$e+1;
					
				}

				//RO-SO/OIC
				if (in_array(110,$destination_array)) {

					$sampleDetailsData = $Workflow->find('all',array('conditions'=>array('stage_smpl_cd IS'=>$sample_code)))->first();
						
					if (!empty($sampleDetailsData)) {
						
						$roSoOicOfficerData = $DmiUsers->find('all',array('conditions'=>array('id IS'=>$userCode)))->first();
						$destination_user_mobile_no = $roSoOicOfficerData['phone'];
						$destination_user_email_id = $roSoOicOfficerData['email'];
						
						//This is addded on 27-04-2021 for base64decoding by AKASH
						$destination_mob_nos[$m] = '91'.base64_decode($destination_user_mobile_no);
						$log_dest_mob_nos[$m] = '91'.$destination_user_mobile_no;
						$destination_email_ids[$e] = base64_decode($destination_user_email_id);
					
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
				$sms_message = $this->replaceDynamicValuesFromMessage($sample_code,$sms_message,$userCode);
	
				//replacing dynamic values in the email message
				$email_message = $this->replaceDynamicValuesFromMessage($sample_code,$email_message,$userCode);
				
				print_r($sms_message);
				print_r("</br>");
				print_r($destination_mob_nos_values);
				print_r("</br>");
				print_r($destination_email_ids_values);
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
		public function replaceDynamicValuesFromMessage($sample_code,$message,$userCode) {
			
			//Getting Count Before Execution
			$total_occurrences = substr_count($message,"%%");

			while($total_occurrences > 0){

				$matches = explode('%%',$message);//getting string between %% & %%

				if (!empty($matches[1])) {

					switch ($matches[1]) {

						case "sample_code":
							$message = str_replace("%%sample_code%%",$this->getReplaceDynamicValues('sample_code',$sample_code,$userCode),$message);
						break;

						case "sample_registration_date":
							$message = str_replace("%%sample_registration_date%%",$this->getReplaceDynamicValues('sample_registration_date',$sample_code,$userCode),$message);
						break;
							
						case "inward_officer":
							$message = str_replace("%%inward_officer%%",$this->getReplaceDynamicValues('inward_officer',$sample_code,$userCode),$message);
						break;
				
						case "commodities":
							$message = str_replace("%%commodities%%",$this->getReplaceDynamicValues('commodities',$sample_code,$userCode),$message);
						break;
							
					    case "s_user_role":
							$message = str_replace("%%s_user_role%%",$this->getReplaceDynamicValues('s_user_role',$sample_code,$userCode),$message);
						break;	

					    case "d_user_role":
							$message = str_replace("%%d_user_role%%",$this->getReplaceDynamicValues('d_user_role',$sample_code,$userCode),$message);
						break;	

					  	case "s_office":
							$message = str_replace("%%s_office%%",$this->getReplaceDynamicValues('s_office',$sample_code,$userCode),$message);
						break;

					  	case "d_office":
							$message = str_replace("%%d_office%%",$this->getReplaceDynamicValues('d_office',$sample_code,$userCode),$message);
						break;

					 	case "ro_so_oic":
							$message = str_replace("%%ro_so_oic%%",$this->getReplaceDynamicValues('ro_so_oic',$sample_code,$userCode),$message);
						break;			
							
					  	case "ral_cal_oic":
							$message = str_replace("%%ral_cal_oic%%",$this->getReplaceDynamicValues('ral_cal_oic',$sample_code,$userCode),$message);
						break;

		
					    case "chemist":
							$message = str_replace("%%chemist%%",$this->getReplaceDynamicValues('chemist',$sample_code,$userCode),$message);
						break;

					   	case "chief_chemist":
							$message = str_replace("%%chief_chemist%%",$this->getReplaceDynamicValues('chief_chemist',$sample_code,$userCode),$message);
						break;

					   	case "lab_incharge":
							$message = str_replace("%%lab_incharge%%",$this->getReplaceDynamicValues('lab_incharge',$sample_code,$userCode),$message);
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
		public function getReplaceDynamicValues($replace_variable_value,$sample_code, $userCode) {
			
			if (!isset($_SESSION['org_sample_code'])) { $_SESSION['org_sample_code']=null;}

			$Workflow = TableRegistry::getTableLocator()->get('Workflow');
			$DmiUsers = TableRegistry::getTableLocator()->get('DmiUsers');
			$DmiRoOffices = TableRegistry::getTableLocator()->get('DmiRoOffices');
			$MCommodityCategory = TableRegistry::getTableLocator()->get('MCommodityCategory');
			$MCommodity = TableRegistry::getTableLocator()->get('MCommodity');
			$SampleInward = TableRegistry::getTableLocator()->get('SampleInward');
			
			//Get the Source User and their role from sample 

			$sampleDetails = $SampleInward->getSampleDetails();
		


			switch ($replace_variable_value) {
					
					case "sample_code":
						$sample_code = $sampleInformation['stage_smpl_cd'];
						return $sample_code;  		
					break;
		
					case "sample_registration_date":
						$sample_resgistration_date = $sampleInformation['created'];
						return $sample_resgistration_date;  		
					break;
							
					case "inward_officer_name":
						$inward_officer = $inward_officer_data['f_name']." ".$inward_officer_data['l_name'];
						return $inward_officer; 
					break;
							
					case "commodities":
						$commodities = $get_commodity_name['commodity_name'];
						return $commodities;
					break;
					
					case "ral_cal_oic_name":
						$ral_cal_oic_name = $ral_cal_oic_data['f_name']." ".$ral_cal_oic_data['l_name'];
						return $ral_cal_oic_name; 
					break;

					case "source_office":
						$source_office = $source_office_posted['ro_office'];
						return $source_office; 
					break;

					case "source_office":
						$source_office = $source_office_posted['ro_office'];
						return $source_office; 
					break;
					
					case "ro_so_oic_name":
						$ro_so_oic = $ro_so_oic_data['f_name']." ".$ro_so_oic_data['l_name'];
						return $ro_so_oic; 
					break;

					case "ro_so_officer_name":
						$ro_so_officer = $ro_so_officer_data['f_name']." ".$ro_so_officer_data['l_name']; 
						return $ro_so_officer; 
					break;

					case "chemist":
						$chemist = $chemist['f_name']." ".$chemist['l_name']; 
						return $chemist; 
					break;

					case "lab_incharge":
						$lab_incharge = $lab_incharge['f_name']." ".$lab_incharge['l_name']; 
						return $lab_incharge; 
					break;

					case "source_user_role":
						$source_user_role = $source_user_role['role'];
						return $source_user_role; 
					break;

					case "destination_user_role":
						$destination_user_role = $destination_user_role['role']; 
						return $destination_user_role; 
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

}
