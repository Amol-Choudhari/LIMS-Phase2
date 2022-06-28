<?php	
	namespace app\Controller\Component;
	use Cake\Controller\Controller;
	use Cake\Controller\Component;	
	use Cake\Controller\ComponentRegistry;
	use Cake\ORM\Table;
	use Cake\ORM\TableRegistry;
	use Cake\Datasource\EntityInterface;

	class PaymentdetailsComponent extends Component {
	
		
		public $components= array('Session','Customfunctions');
		public $controller = null;
		public $session = null;

		public function initialize(array $config): void{

			parent::initialize($config);
			$this->Controller = $this->_registry->getController();
			$this->Session = $this->getController()->getRequest()->getSession();
			
		}
		
		public function paymentDetailsFunction($postData=null){

			//Load Models
			$SampleInwardDetails = TableRegistry::getTableLocator()->get('SampleInwardDetails');
			$SampleInward = TableRegistry::getTableLocator()->get('SampleInward');
			$MCommodityCategory = TableRegistry::getTableLocator()->get('MCommodityCategory');
			$MCommodity = TableRegistry::getTableLocator()->get('MCommodity');
			$DmiPaoDetails = TableRegistry::getTableLocator()->get('DmiPaoDetails');
			$LimsDdoDetails = TableRegistry::getTableLocator()->get('LimsDdoDetails');
			$LimsSamplePaymentDetails = TableRegistry::getTableLocator()->get('LimsSamplePaymentDetails');
			
			if($_SESSION['sample'] == '3'){
				$_SESSION['is_payment_applicable'] = 'yes';
			}
	
			//For Sample Details Progress-Bar
			if (!empty($this->Controller->Customfunctions->checkSampleIsSaved('sample_inward',$this->Session->read('org_sample_code')))) {
				$sample_inward_form_status = 'saved';
				$sample_details_form_status='saved';
				$SaveUpdatebtn = 'update';
			} else {
				$sample_details_form_status='';
				$SaveUpdatebtn = '';
				$sample_inward_form_status = '';
			}
	
		
	
			$this->Controller->set('sample_inward_form_status',$sample_inward_form_status);
			$this->Controller->set('sample_details_form_status',$sample_details_form_status);
	
			$sample_Details_data = $SampleInwardDetails->find('all',array('conditions'=>array('org_sample_code IS'=>$this->Session->read('org_sample_code')),'order'=>'id desc'))->first();
			
			if (!empty($sample_Details_data)) {
				//For Progress-Bar
				$sample_details_form_status='saved';
			}else{
				$sample_details_form_status='';
			}
			
			
			$sample_details = $SampleInward->getSampleDetails();
			
			$this->fetchSamplePaymentDetails($sample_details['org_sample_code'],$sample_details['loc_id']);
	
			$category = $MCommodityCategory->getCategory($sample_details['category_code']);
			$commodity = $MCommodity->getCommodity($sample_details['commodity_code']);
			$commercial_charges = 5000;
			$this->Controller->set(compact('category','commodity','commercial_charges'));
		

		}




		public function saveSamplePaymentDetails($postData){
			
			//Load Session Values
			$username = $this->Session->read('username');
			$sample_code = $this->Session->read('org_sample_code');
			
			//Load Models
			$Workflow = TableRegistry::getTableLocator()->get('Workflow');
			$SampleInward = TableRegistry::getTableLocator()->get('SampleInward');
			$DmiSmaEmailTemplates = TableRegistry::getTableLocator()->get('DmiSmsEmailTemplates');
			$LimsSamplePaymentDetails = TableRegistry::getTableLocator()->get('LimsSamplePaymentDetails');
			$DmiDistrict = TableRegistry::getTableLocator()->get('DmiDistricts');
			$DmiPaoDetails = TableRegistry::getTableLocator()->get('DmiPaoDetails');
			$DmiUsers = TableRegistry::getTableLocator()->get('DmiUsers');
			$LimsDdoDetails = TableRegistry::getTableLocator()->get('LimsDdoDetails');

			//Set Variables to blank
			$payment_conirmation_status = '';
			$payment_receipt_docs = '';
			
			$lims_payment_id = $LimsSamplePaymentDetails->find('list', array('fields'=>'id','conditions'=>array('sample_code IS'=>$sample_code)))->toArray();
			
			if(!empty($lims_payment_id)){	
			
				$payment_confirmation_query = $LimsSamplePaymentDetails->find('all', array('conditions'=>array('id'=>max($lims_payment_id))))->first();
				$payment_conirmation_status = $payment_confirmation_query['payment_confirmation']; 
				$payment_receipt_docs = $payment_confirmation_query['payment_receipt_docs']; 
			}	

			$pao_details = $LimsDdoDetails->getRecordByOffice();
			
			$destinationUser = $pao_details['pao_user_id'];
			$pao_id = $pao_details['id'];
			$destinationOffice = $DmiUsers->getUserDetailsById($destinationUser);
		
			if (empty($postData['payment_amount']) && empty($postData['payment_transaction_id']) && empty($postData['bharatkosh_payment_done']) && empty($postData['payment_trasaction_date'])) {
				
				return false;
			}
		
			if (empty($payment_receipt_docs)) {
				
				if (empty($postData['payment_receipt_document']->getClientFilename())) {
					
					return false;
				}
			}
			
			$payment_amount = htmlentities($postData['payment_amount'], ENT_QUOTES);
			
			$payment_transaction_id = htmlentities($postData['payment_transaction_id'], ENT_QUOTES);
		
			$post_input_request = $postData['bharatkosh_payment_done'];
			
			$bharatkosh_payment_done = $this->Customfunctions->radioButtonInputCheck($post_input_request);
			
			if ($bharatkosh_payment_done == null) { 
				return false;
			}				
				
			
			
			if(!empty($postData['payment_receipt_document']->getClientFilename())) {
				
				$file_name = $postData['payment_receipt_document']->getClientFilename();
				$file_size = $postData['payment_receipt_document']->getSize();
				$file_type = $postData['payment_receipt_document']->getClientMediaType();
				$file_local_path = $postData['payment_receipt_document']->getStream()->getMetadata('uri');
			
				$payment_receipt_docs = $this->Customfunctions->fileUploadLib($file_name,$file_size,$file_type,$file_local_path); // calling file uploading function
				
			}
			
			$payment_trasaction_date = $this->Customfunctions->changeDateFormat($postData['payment_trasaction_date']);

			$sample_details = $SampleInward->getSampleDetails();
		

			if($payment_conirmation_status == 'not_confirmed'){
				

				
				//find PAO email id (Done By pravin 4/11/2017)
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
					'payment_confirmation'=>'replied',
					'pao_id'=>$pao_id,
					'created'=>date('Y-m-d H:i:s'),
					'modified'=>date('Y-m-d H:i:s')
				));
							
				if ($LimsSamplePaymentDetails->save($lims_sample_payment_detailsEntity)) {
						
					//Entry in all applications current position table (Done By pravin 4/11/2017)
					$user_email_id = $pao_user_email_id['email'];
					$current_level = 'pao';
					//added on 23-07-2018 by Amol
					//$DmiSmaEmailTemplates->sendMessage(2056,$sample_code);
					return true;	
				}

			} else {
				
				$lims_sample_payment_detailsEntity = $LimsSamplePaymentDetails->newEntity(array(

					'sample_code'				=>	$sample_code,
					'sample_type'				=>	$sample_details['sample_type_code'],
					'amount_paid'				=>	$payment_amount,
					'transaction_id'			=>	$payment_transaction_id,											
					'transaction_date'			=>	$payment_trasaction_date,
					'payment_receipt_docs'		=>	$payment_receipt_docs,
					'bharatkosh_payment_done'	=>	$bharatkosh_payment_done,
					'payment_confirmation'		=>	'saved',
					'pao_id'					=>	$pao_id,
					'created'					=>	date('Y-m-d H:i:s'),	
					'modified'					=>	date('Y-m-d H:i:s')
				));
					
				if($LimsSamplePaymentDetails->save($lims_sample_payment_detailsEntity)){

					//Save the Workflow entry
					$workflow_data = array(

						'org_sample_code'	=>	$sample_code,
						'src_loc_id'		=>	$_SESSION['posted_ro_office'],
						'src_usr_cd'		=>	$_SESSION['user_code'],
						'dst_loc_id'		=>	$destinationOffice['posted_ro_office'],
						'dst_usr_cd'		=>	$destinationUser,
						'user_code'			=>	$_SESSION['user_code'],
						'stage_smpl_cd'		=>	$sample_code,
						'tran_date'			=>	date('Y-m-d H:i:s'),
						'stage'				=>	'3',
						'stage_smpl_flag'	=>	'PS' // Added this flag for "Payment Saved"
					);

					$workflowEntity = $Workflow->newEntity($workflow_data);

					if ($Workflow->save($workflowEntity)) {

						return true;
					}
				}
			}
		
		
		}
			
			
		public function fetchSamplePaymentDetails($sample_code) {
			
			//Load Models
			$DmiPaoDetails = TableRegistry::getTableLocator()->get('DmiPaoDetails');
			$DmiDistrict = TableRegistry::getTableLocator()->get('DmiDistricts');
			$LimsSamplePaymentDetails = TableRegistry::getTableLocator()->get('LimsSamplePaymentDetails');
			$LimsDdoDetails = TableRegistry::getTableLocator()->get('LimsDdoDetails');

			$process_query = 'insert';
			
			$bharatkosh_payment_done = '';
			$payment_amount = '';
			$payment_transaction_id = '';
			$selected_pao_alias_name = '';
			$payment_trasaction_date[0] = '';
			$payment_receipt_docs = '';
			$reason_list_comment = '';
			$reason_comment = '';
			
			$this->Controller->set('bharatkosh_payment_done',$bharatkosh_payment_done);
			$this->Controller->set('payment_amount',$payment_amount);
			$this->Controller->set('payment_transaction_id',$payment_transaction_id);
			$this->Controller->set('selected_pao_alias_name',$selected_pao_alias_name);
			$this->Controller->set('payment_trasaction_date',$payment_trasaction_date);
			$this->Controller->set('payment_receipt_docs',$payment_receipt_docs);
			$this->Controller->set('reason_list_comment',$reason_list_comment);
			$this->Controller->set('reason_comment',$reason_comment);

			$pao_alias_name = $DmiPaoDetails->find('list',array('valueField'=>'pao_alias_name'))->toArray();
			$this->Controller->set('pao_alias_name',$pao_alias_name);
			$pao_details = $LimsDdoDetails->getRecordByOffice();


			$this->Controller->set('pao_alias_name',$pao_details['pao_alias_name']);
			
			if(!empty($pao_details['id'])){
				$pao_to_whom_payment = $pao_details['pao_alias_name']; 	
			}else{
				$pao_to_whom_payment = null;
			}
			
		
			$listSamplePaymentId = $LimsSamplePaymentDetails->find('list', array('fields'=>'id','conditions'=>array('sample_code IS'=>$sample_code)))->toArray();
			
			if(!empty($listSamplePaymentId)){
				
				$process_query = 'update';
				
				$payment_confirmation_query = $LimsSamplePaymentDetails->find('all', array('conditions'=>array('id'=>max($listSamplePaymentId))))->first();
				
				$payment_confirmation = $payment_confirmation_query;
				$this->Controller->set('payment_confirmation_query',$payment_confirmation_query);
				
				$payment_confirmation_status = $payment_confirmation['payment_confirmation']; 
				$bharatkosh_payment_done = $payment_confirmation['bharatkosh_payment_done'];
				$payment_amount = $payment_confirmation['amount_paid'];
				$payment_transaction_id = $payment_confirmation['transaction_id'];
				$payment_trasaction_date = explode(' ',$payment_confirmation['transaction_date']);
				$payment_receipt_docs = $payment_confirmation['payment_receipt_docs'];
				$reason_list_comment = $payment_confirmation['reason_option_comment'];
				$reason_comment = $payment_confirmation['reason_comment'];
				$pao_to_whom_payment = $pao_alias_name[$payment_confirmation['pao_id']]; 
			
				$selected_pao = $DmiPaoDetails->find('all',array('fields'=>'pao_alias_name','conditions'=>array('id IS'=>$payment_confirmation['pao_id'])))->first();
				$selected_pao_alias_name = $selected_pao['pao_alias_name'];  
				$this->Controller->set('bharatkosh_payment_done',$bharatkosh_payment_done);
				$this->Controller->set('payment_amount',$payment_amount);
				$this->Controller->set('payment_transaction_id',$payment_transaction_id);
				$this->Controller->set('selected_pao_alias_name',$selected_pao_alias_name);
				$this->Controller->set('payment_trasaction_date',$payment_trasaction_date);
				$this->Controller->set('payment_receipt_docs',$payment_receipt_docs);
				$this->Controller->set('reason_list_comment',$reason_list_comment);
				$this->Controller->set('reason_comment',$reason_comment);
				$this->Controller->set('payment_confirmation_status',$payment_confirmation_status);
				
			}else{
				
				$payment_confirmation_status = 'payment_not_submit';
				$this->Controller->set('payment_confirmation_status',$payment_confirmation_status);
			}
			
			$fetch_pao_referred_back = array();
			$this->Controller->set('pao_to_whom_payment',$pao_to_whom_payment);
			
		}

	}
		
?>