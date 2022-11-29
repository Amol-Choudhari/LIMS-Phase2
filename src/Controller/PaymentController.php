<?php
namespace App\Controller;

use Cake\Event\Event;
use App\Network\Email\Email;
use Cake\ORM\Entity;
use Cake\Datasource\ConnectionManager;
use Cake\View;


class PaymentController extends AppController{

	var $name = 'Payment';

	public function initialize(): void {
		parent::initialize();
		$this->viewBuilder()->setLayout('admin_dashboard');
		$this->viewBuilder()->setHelpers(['Form','Html']);
		$this->loadComponent('Customfunctions');
		$this->loadModel('LimsUserActionLogs');
		$this->loadModel('LimsSamplePaymentDetails');
		$this->loadModel('SampleInward');
		$this->loadModel('Workflow');
		$this->loadModel('DmiUsers');
		$this->loadModel('DmiRoOffices');
	}


	//to validate login user
	public function authenticateUser(){

		$this->loadModel('DmiUserRoles');
		$user_access = $this->DmiUserRoles->find('all',array('conditions'=>array('user_email_id IS'=>$this->Session->read('username'))))->first();

		if (!empty($user_access)) {
			//proceed
		} else {
			$this->customAlertPage("Sorry.. You don't have permission to view this page..");
			exit;
		}
	}




    // Payment
    // Description : This is for the sample payment details
    // Date : 03-06-2022
    // Author : Akash Thakre


	public function paymentDetails(){

        $conn = ConnectionManager::get('default');
		$this->loadComponent('Paymentdetails');

		$message_theme = '';
		$message = '';
		$redirect_to = '';

		$postData = $this->request->getData();

		$this->Paymentdetails->paymentDetailsFunction($postData);

		//To Show/Hide Confirm BUTTON on Form
		$confirmBtnStatus = $this->Customfunctions->showHideConfirmBtn();
		$this->set('confirmBtnStatus',$confirmBtnStatus);


		$sample_inward_data = $this->SampleInward->find('all',array('conditions'=>array('org_sample_code IS'=>$_SESSION['org_sample_code']),'order'=>'inward_id desc'))->first();

		if ($sample_inward_data == null) {

			$this->LimsUserActionLogs->saveActionLog('Save Payment','Failed'); #Action
			$message = 'Please fill the Sample Inward and Sample Details Section first.';
			$message_theme = 'failed';
			$redirect_to = '../inward/sample_inward';

		} else {

			if ($this->request->is('post')) {

				//HTML Encoding
				$postData = $this->request->getData();
	
				if (null!==($this->request->getData('save'))) {
	
					$savePaymentDetails = $this->Paymentdetails->saveSamplePaymentDetails($postData);
					
					$sample = $this->LimsSamplePaymentDetails->find('all',array('conditions'=>array('sample_code' => $_SESSION['org_sample_code']),'order'=>'id desc'))->first();
					

					if ($savePaymentDetails == true){
						
						if ($sample['payment_confirmation']=='replied') {
							
							//get role and office where sample available after confirmed
							$get_info = $this->Workflow->find('all')->where(['org_sample_code IS' => $_SESSION['org_sample_code']])->order('id desc')->first();
							
							$sample_code = $_SESSION['org_sample_code'];

							//SMS Users Codes
							$s_user = $get_info['dst_usr_cd'];
							$d_user = $get_info['src_usr_cd'];
							$oic = $this->DmiRoOffices->getOfficeIncharge();
							
							$user =  $this->DmiUsers->getUserDetailsById($get_info['dst_usr_cd']);
							$office = $this->DmiRoOffices->getOfficeDetailsById($get_info['dst_loc_id']);
							$ro_so = $this->DmiUsers->getUserTableId($office[2]);
	
							#SMS: Commercial Sample Registered
							$this->DmiSmsEmailTemplates->sendMessage(128,$s_user,$sample_code); #Source
							$this->DmiSmsEmailTemplates->sendMessage(129,$d_user,$sample_code); #DDO 
							
							$this->LimsUserActionLogs->saveActionLog('Replied Payment','Success'); #Action
							$message = 'Your Reply Saved Successfully & Forwarded to DDO for further process.';

						} else {

							$this->LimsUserActionLogs->saveActionLog('Save Payment','Success'); #Action
							$message = 'Payment Section Saved Successfully';
						}

						$message_theme = 'success';
						$redirect_to = 'payment_details';
	
					} else {
						
						$this->LimsUserActionLogs->saveActionLog('Save Payment','Failed'); #Action
						$message = 'Payment Section Not Saved.';
						$message_theme = 'failed';
						$redirect_to = 'payment_details';
					}
	
				} elseif (null!==($this->request->getData('confirm'))) {
	
					$confirm = $this->Paymentdetails->confirmSampleDetails();
					$org_sample_code = $_SESSION['org_sample_code'];
	
					if ($confirm == true){
						
						//get role and office where sample available after confirmed
						$get_info = $this->Workflow->find('all')->where(['org_sample_code IS' => $_SESSION['org_sample_code']])->order('id desc')->first();
					
						//SMS Users Codes
						$s_user = $get_info['src_usr_cd'];
						$d_user = $get_info['dst_usr_cd'];
						$oic = $this->DmiRoOffices->getOfficeIncharge();
						
						$user =  $this->DmiUsers->getUserDetailsById($get_info['dst_usr_cd']);
						$office = $this->DmiRoOffices->getOfficeDetailsById($get_info['dst_loc_id']);
						$ro_so = $this->DmiUsers->getUserTableId($office[2]);
						
						if ($_SESSION['user_flag'] == 'SO' || $_SESSION['user_flag'] == 'RO') {
							#SMS: Commercial Sample Registered
							$this->DmiSmsEmailTemplates->sendMessage(120,$s_user,$org_sample_code); #Source
							$this->DmiSmsEmailTemplates->sendMessage(123,$d_user,$org_sample_code); #DDO 
							$this->DmiSmsEmailTemplates->sendMessage(122,$ro_so,$org_sample_code);  #RO
						} else {
							#SMS: Commercial Sample Registered
							$this->DmiSmsEmailTemplates->sendMessage(120,$s_user,$org_sample_code); #Source
							$this->DmiSmsEmailTemplates->sendMessage(123,$d_user,$org_sample_code); #DDO 
							$this->DmiSmsEmailTemplates->sendMessage(122,$ro_so,$org_sample_code);  #RO
							$this->DmiSmsEmailTemplates->sendMessage(156,$oic,$org_sample_code);    #OIC	
						}
						

						$this->LimsUserActionLogs->saveActionLog('Payment Section Confirm','Success'); #Action
						$message = 'Note :
									</br>You have registered the Commercial Sample successfully having Sample Code : '.$org_sample_code.' and it is saved with payment details.
									</br>The Sample in now sent to the <b>PAO/DDO : '.base64_decode($user['email']).'  ('.$office[0].')</b> for payment verification. 
									</br>If the <b>PAO/DDO</b> user verify the payment then it will be available to the forward the sample .
									</br>If the <b>PAO/DDO</b> user referred back  then you need to update details as per requirement and send again.';
						$message_theme = 'success';
						$redirect_to = '../inward/confirmed_samples';
			
					}
				}
			}
		}

		$this->set('message_theme',$message_theme);
		$this->set('message',$message);
		$this->set('redirect_to',$redirect_to);

	}



	public function paymentStatus(){

		$user_flag = $_SESSION['user_flag'];
		$this->set('user_flag',$user_flag);

		$conn = ConnectionManager::get('default');
		$user_cd=$this->Session->read('user_code');

		$query = $conn->execute("SELECT lspd.sample_code, lspd.payment_confirmation,si.inward_id,lspd.id
								 FROM lims_sample_payment_details AS lspd
								 INNER JOIN sample_inward AS si ON si.org_sample_code=lspd.sample_code
								 INNER JOIN workflow AS wf ON lspd.sample_code = wf.org_sample_code AND wf.stage_smpl_flag='SI'
								 AND wf.src_usr_cd ='$user_cd'
								 WHERE lspd.payment_confirmation='not_confirmed' AND lspd.id = (select id from lims_sample_payment_details where sample_code=lspd.sample_code order by id desc limit 1)
								 GROUP BY lspd.sample_code, lspd.payment_confirmation, si.inward_id,lspd.id
								 ORDER BY lspd.id desc");
								 //in the above query we are using subquery to get last record id for each sample, to compare it with 'not_confirmed' record
								 //to confirm it is the last record with 'not_confirmed' status

		$res = $query->fetchAll('assoc');
	
		$this->set('res',$res);

	}

}
?>
