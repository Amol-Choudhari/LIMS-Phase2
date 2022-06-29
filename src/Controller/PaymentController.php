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
					$query = $conn->execute("SELECT DISTINCT si.org_sample_code,w.dst_usr_cd,u.role,r.ro_office
                                            FROM sample_inward AS si
                                            INNER JOIN workflow AS w ON si.org_sample_code=w.org_sample_code
                                            INNER JOIN dmi_users AS u ON u.id=w.dst_usr_cd
                                            INNER JOIN dmi_ro_offices AS r ON r.id=w.dst_loc_id
                                            WHERE si.org_sample_code='$org_sample_code'");
                    $get_info = $query->fetchAll('assoc');
            
                    $message = 'Sample Code '.$org_sample_code.' has been Confirmed and Available to "'.$get_info[0]['role'].' ('.$get_info[0]['ro_office'].' )"';
                    $message_theme = 'success';
                    $redirect_to = '../inward/confirmed_samples';
                } else {
                    $message = 'Sample Code '.$org_sample_code.' has been Confirmed and Available to "'.$get_info[0]['role'].' ('.$get_info[0]['ro_office'].' )"';
                    $message_theme = 'success';
                    $redirect_to = '../inward/confirmed_samples';
                }
				

			}

		}


		$this->set('message_theme',$message_theme);
		$this->set('message',$message);
		$this->set('redirect_to',$redirect_to);

	
	
	}


}
?>