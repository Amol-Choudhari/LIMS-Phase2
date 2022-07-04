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
			echo "Sorry.. You don't have permission to view this page";
			exit();
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

			$message = 'Please fill the Sample Inward and Sample Details Section first.';
			$message_theme = 'failed';
			$redirect_to = '../inward/sample_inward';

		} else {

			if ($this->request->is('post')) {

				//HTML Encoding
				$postData = $this->request->getData();
	
				if (null!==($this->request->getData('save'))) {
	
					$savePaymentDetails = $this->Paymentdetails->saveSamplePaymentDetails($postData);
	
					if ($savePaymentDetails == true){
	
						$message_theme = 'success';
						$message = 'Payment Section Saved Successfully';
						$redirect_to = 'payment_details';
	
					} else {
	
						$message_theme = 'success';
						$message = 'Payment Section Saved Successfully';
						$redirect_to = 'payment_details';
					}
	
				} elseif (null!==($this->request->getData('confirm'))) {
	
					$confirm = $this->Paymentdetails->confirmSampleDetails();
					$org_sample_code = $_SESSION['org_sample_code'];
	
					if ($confirm == true){
						
						//get role and office where sample available after confirmed
						$get_info = $this->Workflow->find('all')->where(['org_sample_code IS' => $_SESSION['org_sample_code']])->order('id desc')->first();
	
						
						$user =  $this->DmiUsers->getUserDetailsById($get_info['dst_usr_cd']);
						$office = $this->DmiRoOffices->getOfficeDetailsById($get_info['dst_loc_id']);
						
						$message = 'Note :
						</br>The Commercial Sample Inward is saved with payment details and sent to <b>PAO/DDO '.base64_decode($user['email']).'  ('.$office[0].')</b>
						for payment verification, 
						</br>If the <b>DDO</b> user confirms the payment then it will be available to RO/SO OIC to forward.
						</br>If <b>DDO</b> user referred back  then you need to update details as per requirement and send again.';
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

}
?>
