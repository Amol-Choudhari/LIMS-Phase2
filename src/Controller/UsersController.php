<?php

namespace App\Controller;

use Cake\Event\Event;
use App\Network\Email\Email;
use Cake\ORM\Entity;
use Cake\Datasource\ConnectionManager;

class UsersController extends AppController{

	var $name = 'Users';

	public function beforeFilter($event) {
		parent::beforeFilter($event);

		$this->viewBuilder()->setLayout('form_layout');
		$this->viewBuilder()->setHelpers(['Form','Html']);
		$this->loadComponent('Createcaptcha');
		$this->loadComponent('Customfunctions');
		$this->loadComponent('Authentication');
		

	}


	//To create captcha code, called from component on 14-07-2017 by Amol
	public function createCaptcha() {
        $this->autoRender = false;
        $this->Createcaptcha->createCaptcha();
	}


/************************************************************************************************************************************************************************************************************************/

	public function refreshCaptchaCode() {
		$this->autoRender = false;
		$this->Createcaptcha->refreshCaptchaCode();
	}

/************************************************************************************************************************************************************************************************************************/

	public function home() {

		if ($this->Session->read('user_code') == null) {

			return $this->redirect(array('action' => 'login_user'));

		} else {

			$this->viewBuilder()->setLayout('admin_dashboard1');
			$usr = $_SESSION['user_code'];
			unset($_SESSION['stage_sample_code']);
		}
	}


/************************************************************************************************************************************************************************************************************************/

	//Login admin user method start
	public function loginUser() {

		// set variables to show popup messages from view file
		$message = '';
		$redirect_to = '';
		$message_theme = '';
		$already_loggedin_msg = 'no';

		if ($this->request->is('post')) {

			//check login lockout status, applied on 24-04-2018 by Amol
			$lockout_status = $this->Customfunctions->checkLoginLockout('DmiUserLogs',$this->request->getData('email'));

			if ($lockout_status == 'yes') {

				$message = 'Sorry... Your account is disabled for today, on account of 3 login failure.';
				$message_theme = 'failed';
				$redirect_to = $this->request->getAttribute('webroot');

			} else {

				$randsalt = $this->getRequest()->getSession()->read('randSalt');
				$captchacode1 = $this->getRequest()->getSession()->read('code');
				$logindata = $this->request->getData();

				$table = 'DmiUsers';
				$username = $this->request->getData('email'); //For Email Encoding
				$password = $this->request->getData('password');
				$captcharequest = $this->request->getData('captcha');

				$login_result =	$this->Authentication->userLoginLib($table,$username,$password,$randsalt); // calling login library function
				// show user login failed messages (by pravin 27/05/2017)

				if ($login_result == 0) {//this condition added on 08-11-2017 by Amol

					$message = 'Sorry... It seems you are LMIS module user. Please use "LMIS Login".';
					$message_theme = 'failed';
					$redirect_to = $this->request->getAttribute('webroot');

				} elseif ($login_result == 1) {

					//this custom functionn is called on 08-04-2021, to show remaining login attempts
					$remng_attempts_msg = $this->showRemainingLoginAttempts('DmiUserLogs',base64_encode($this->request->getData('email'))); //for email encoding
					$message = 'Username or password do not match. <br>'.$remng_attempts_msg;
					$message_theme = 'failed';
					$redirect_to = $this->request->getAttribute('webroot');

				} elseif ($login_result == 2) {

					//this custom functionn is called on 08-04-2021, to show remaining login attempts
					$remng_attempts_msg = $this->showRemainingLoginAttempts('DmiUserLogs',base64_encode($this->request->getData('email'))); //for email encoding
					$message = 'Username or password do not match. <br>'.$remng_attempts_msg;
					$message_theme = 'failed';
					$redirect_to = $this->request->getAttribute('webroot');

				} elseif ($login_result == 3) {

					$captcha_error_msg = 'Sorry... Wrong Code Entered';
					$this->set('captcha_error_msg',$captcha_error_msg);
					return null;


				} elseif ($login_result == 4) {

					//get applicant email id and apply masking before showing in message by Amol on 25-02-2021
					$email_id = $this->Customfunctions->getMaskedValue($username,'email');
					$message = 'Your password has been expired, The link to reset password is sent on email id '.$email_id;
					$redirect_to = $this->request->getAttribute('webroot');
				
				//created/updated/added on 25-06-2021 for multiple logged in check security updates, by Amol
				} elseif ($login_result == 5) {
									
					$already_loggedin_msg = 'yes';
				}
			}
		}

		// set variables to show popup messages from view file
		$this->set('already_loggedin_msg',$already_loggedin_msg);
		$this->set('message',$message);
		$this->set('message_theme',$message_theme);
		$this->set('redirect_to',$redirect_to);

	}

/************************************************************************************************************************************************************************************************************************/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>-|CHANGE PASSWORD|->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

	//Change password for admin user method start
	public function changePassword() {

		// set variables to show popup messages from view file
		$message = '';
		$redirect_to = '';
		$message_theme = '';

		$this->viewBuilder()->setLayout('admin_dashboard');
		$this->loadModel('DmiUsers');

		if ($this->Session->read('username') == null) {

			echo "Sorry You are not authorized to view this page..";?><a href="<?php echo $this->request->getAttribute('webroot');?>"> Please Login</a><?php
			exit;

		} else {

			$user_data = $this->DmiUsers->find('all',array('conditions'=>array('email IS'=>$this->Session->read('username'))))->toArray();

			if (!empty($user_data)) {

				if ($this->request->is('post')) {

					$randsalt = $this->Session->read('randSalt');

					$changepassdata = $this->request->getData();

					$table = 'DmiUsers';
					$username = $this->Session->read('username');
					$oldpassdata = $this->request->getData('old_password');
					$newpassdata = $this->request->getData('new_password');
					$confpassdata = $this->request->getData('confirm_password');


					$change_pass_result = $this->Authentication->changePasswordLib($table,$username,$oldpassdata,$newpassdata,$confpassdata,$randsalt); // calling change password library function
	
					if ($change_pass_result == 1) {

						$message = 'Sorry...username not matched to save new password';
						$message_theme = 'failed';
						$redirect_to = 'change_password';

					} elseif ($change_pass_result == 2) {

						$message = 'Sorry...Please Check old password again';
						$message_theme = 'failed';
						$redirect_to = 'change_password';

					} elseif ($change_pass_result == 3) {

						$message = 'Sorry...please Check. Confirm password not matched';
						$message_theme = 'failed';
						$redirect_to = 'change_password';

					} elseif ($change_pass_result == 4) {

						// SHOW ERROR MESSAGE IF NEW PASSWORD FOUND UNDER LAST THREE PASSWORDS OF USER By Aniket Ganvir dated 16th NOV 2020
						$message = 'This password matched with your last three passwords, Please enter different password';
						$redirect_to = 'change_password';

					} else {

						$message = 'Password Changed Successfully';
						$message_theme = 'success';
						$redirect_to = 'change_password';
					}
				}
			
			} else {

				echo "Sorry You are not authorized to view this page..";?><a href="<?php echo $this->request->getAttribute('webroot');?>"> Please Login</a><?php
				exit;
			}
		}

		// set variables to show popup messages from view file
		$this->set('message',$message);
		$this->set('message_theme',$message_theme);
		$this->set('redirect_to',$redirect_to);
	}


/************************************************************************************************************************************************************************************************************************/


	// forgot password for admin user method start
	public function forgotPassword() {

		// set variables to show popup messages from view file
		$message = '';
		$message_theme = '';
		$redirect_to = '';


		if ($this->request->is('post')) {

			//captcha check
			if ($this->request->getData('captcha') !="" && $this->Session->read('code') == $this->request->getData('captcha')) {

				$table = 'DmiUsers';
				$emailforrecovery = $this->request->getData('email');
				// calling forgot password library function
				$forgot_password_result = $this->Authentication->forgotPasswordLib($table,$emailforrecovery);

				if ($forgot_password_result == 1) {

					$message = 'Sorry... This email is not authorized';
					$message_theme = 'failed';
					$redirect_to = 'forgot_password';

				} elseif ($forgot_password_result == 2) {

					$message = 'Changed Password link Sent on '.$emailforrecovery;
					$message_theme = 'success';
					$redirect_to = 'forgot_password';

				}

			} else {

				$message = 'Sorry...Wrong Captcha Code Entered';
				$message_theme = 'failed';
				$redirect_to = 'forgot_password';
			}
		}

		// set variables to show popup messages from view file
		$this->set('message',$message);
		$this->set('message_theme',$message_theme);
		$this->set('redirect_to',$redirect_to);

	}


/************************************************************************************************************************************************************************************************************************/


	// reset password for admin user method start
	public function resetPassword() {

		$this->viewBuilder()->setLayout('form_layout');
		$this->loadModel('DmiUsers');
		$this->loadModel('DmiUsersResetpassKeys');

		// set variables to show popup messages from view file
		$message = '';
		$message_theme = '';
		$redirect_to = '';

		if	(empty($_GET['$key']) || empty($_GET['$id'])) {

			echo "Sorry You are not authorized to view this page..";?><a href="<?php echo $this->request->getAttribute('webroot');?>"> Please Login</a><?php
			exit;

		} else {

			$key_id = $_GET['$key'];
			$user_id = $this->Authentication->decrypt($_GET['$id']);
			$this->set('user_id',$user_id);

			//call function to check valid key
			$valid_key_result = $this->DmiUsersResetpassKeys->checkValidKey($user_id,$key_id);

			if ($valid_key_result == 1) {

				$user_data = $this->DmiUsers->find('all',array('conditions'=>array('email IS'=>$user_id)))->first();
				$record_id = $user_data['id'];

				if (!empty($user_data)) {

					if ($this->request->is('post')) {

						$randsalt = $this->Session->read('randSalt');

						$captchacode1 = $this->Session->read('code');

						$changepassdata = $this->request->getData();

						$table = 'DmiUsers';
						$username = $user_id;//$this->Session->read('username');
						$newpassdata = $this->request->getData('new_password');
						$confpassdata = $this->request->getData('confirm_password');

						// calling reset password library function
						$reset_pass_result = $this->Authentication->resetPasswordLib($table,$username,$newpassdata,$randsalt);

						if ($reset_pass_result == 1) {

							$message = 'Sorry...Email id not matched by id to save new password';
							$message_theme = 'failed';
							$redirect_to = 'reset_password';

						} elseif ($reset_pass_result == 2) {

							$message = 'Sorry...Incorrect captcha code';
							$message_theme = 'failed';
							$redirect_to = 'reset_password';

						} elseif ($reset_pass_result == 3) {

							$message = 'Sorry...please Check. Confirm password not matched';
							$message_theme = 'failed';
							$redirect_to = 'reset_password';

						} else {
							//update link key table status to 1 for successfully
							$this->DmiUsersResetpassKeys->updateKeySuccess($user_id,$key_id);

							$message = 'Password Changed Successfully';
							$message_theme = 'success';
							$redirect_to = '../../users/login_user';
						}
					}
				}

			} elseif ($valid_key_result == 2) {

				$message = 'Sorry.. This link to Reset Password was Expired. Please proceed through "Forgot Password" again.';
				$redirect_to = '../forgot_password';

			}
		}

		// set variables to show popup messages from view file
		$this->set('message',$message);
		$this->set('message_theme',$message_theme);
		$this->set('redirect_to',$redirect_to);
	}


/************************************************************************************************************************************************************************************************************************/

	//created on 13-05-2017 by Amol
	//method for login redirect for common user(DMI/LMIS)

	public function commonUserRedirectLogin($user_id) {

		$message = '';
		$message_theme = '';
		$redirect_to = '';

		$this->loadModel('DmiUsers');
		$this->loadModel('DmiUserLogs');

		if (!empty($user_id)) {

			$get_user_email_id = $this->DmiUsers->find('all',array('conditions'=>array('id IS'=>$user_id)))->first();

			$user_email_id = $get_user_email_id['email'];

			if ($this->request->is('post')) {

				$randsalt = $this->Session->read('randSalt');
				$table = 'DmiUsers';
				$username = $user_email_id;
				$password = $this->request->getData('password');

				$PassFromdb = $this->$table->find('all', array('fields'=>'password','conditions'=> array('email IS' => $username)))->first();

				if ($PassFromdb != null && $PassFromdb != '') {

					$passarray = $PassFromdb['password'];
					$PassFromdbsalted = $randsalt . $passarray; //adding random salt value to password
					$Dbpasssaltedsha512 = hash('sha512',$PassFromdbsalted);

					// check password to db password
					if ($password == $Dbpasssaltedsha512) {

						$this->Session->destroy();// destroy old session data
						session_start();

						$current_ip = $this->request->clientIp();

						if ($current_ip == '::1') {

							$current_ip = '127.0.0.1';
						}

						$DmiUserLogsEntity = $this->DmiUserLogs->newEntity(array(

							'email_id'=>$username,
							'ip_address'=>$current_ip,
							'date'=>date('Y-m-d'),
							'time_in'=>date('H:i:s'),
							'remark'=>'Success'
						));
											

						$this->DmiUserLogs->save($DmiUserLogsEntity);

						$user_data_query = $this->$table->find('all', array('conditions'=> array('email IS' => $username)))->first();
						$f_name = $user_data_query['f_name'];
						$l_name = $user_data_query['l_name'];
						$once_card_no = $user_data_query['once_card_no'];
						$division = $user_data_query['division'];
						$user_code = $user_data_query['id'];

						// taking user data in session variables
						$this->Session->write('username',$username);
						$this->Session->write('once_card_no',$once_card_no);
						$this->Session->write('division',$division);
						$this->Session->write('f_name',$f_name);
						$this->Session->write('l_name',$l_name);
						$this->Session->write('ip_address',$this->request->clientIp());
						$this->Session->write('user_code',$user_code);
						$this->Session->write('userloggedin','yes');
						$this->Session->write('role',$user_data_query['role']);
						$this->Session->write('posted_ro_office',$user_data_query['posted_ro_office']);

						$this->loadModel('DmiUserRoles');
						$user_flag = $this->DmiUserRoles->find('all', array('fields'=>'user_flag','conditions'=> array('user_email_id IS' => $username)))->first();
						$this->Session->write('user_flag',$user_flag['user_flag']);

						$this->loadModel('DmiRoOffices');
						$location = $this->DmiRoOffices->find('all', array('conditions'=> array('id IS' => $user_data_query['posted_ro_office'])))->first();

						//below if-else condition added on 21-05-2019 by Amol
						if (!empty($location)) {
							$this->set('location',$location);
							$ro_office=$location['ro_office'];
						} else {
							$ro_office = 'Unknown';
						}

						$this->Session->write('ro_office',$ro_office);
						$this->Session->write('profile_pic',$user_data_query['profile_pic']);
						$this->redirect('/dashboard/home');

					} else {

						$current_ip = $this->request->clientIp();
						
						if ($current_ip == '::1') {

							$current_ip = '127.0.0.1';
						}
							
						$DmiUserLogsEntity = $this->DmiUserLogs->newEntity(array(

							'email_id'=>$username,
							'ip_address'=>$current_ip,
							'date'=>date('Y-m-d'),
							'time_in'=>date('H:i:s'),
							'remark'=>'Failed'
						));
							
						$this->DmiUserLogs->save($DmiUserLogsEntity);

						$this->set('return_error_msg','Sorry.. Password does not matched.');
						return null;
						exit;
					}

				} else {

					$this->set('return_error_msg','Sorry.. This username does not exist.');
					return null;
					exit;
				}
			}
		
		} else {

			echo "Sorry.. No direct access";
			exit;
		}
		
		$this->set('return_error_msg',null);


	}


/************************************************************************************************************************************************************************************************************************/

	//to get and display DMI Dashboard User Logs.
	public function userLogs() {

        //Load Models
        $this->loadModel('DmiUsers');
        $this->loadModel('DmiUserLogs');

        if ($this->request->getSession()->read('username') == null) {

			echo "Sorry You are not authorized to view this page..";?><a href="<?php echo $this->request->getAttribute('webroot');?>"> Please Login</a><?php
			exit;

        }
        //Set the Layout
        $this->viewBuilder()->setLayout('admin_dashboard');

        $user_logs = $this->DmiUserLogs->find('all', array('conditions'=>array('email_id IS'=>$this->Session->read('username')),'order' => 'id DESC'))->toArray();
        //to hide current session logout time.
        $user_logs[0]['time_out'] = null;
        $this->set('user_logs',$user_logs);

    }

/************************************************************************************************************************************************************************************************************************/
	
	// USER ACTION HISTORY
    // @AUTHOR : Akash Thakre (Common/Migration/Upatation)
    // #Contributer : 
    // DATE : 19-04-2022
    
    public function userActionHistory() {

        $userId = $this->Session->read('username');
		$this->viewBuilder()->setLayout('admin_dashboard');
		$get_user_actions = $this->LimsUserActionLogs->getActionLog($userId);
		$this->set('get_user_actions', $get_user_actions);
   	}


/************************************************************************************************************************************************************************************************************************/
	
	
	//added the all user logs function on 29-11-2021 by Amol starts	-> updated on 29-04-2021 by Akash starts
	public function allUsersLogs() {

		$this->viewBuilder()->setLayout('admin_dashboard');
		$this->loadModel('DmiRoOffices');
		$this->loadModel('DmiUsers');
		$this->loadModel('DmiUserLogs');
		$username = $this->Session->read('username');

		if ($username == null) {

			echo "Sorry You are not authorized to view this page..";?><a href="<?php echo $this->request->getAttribute('webroot');?>"> Please Login</a><?php
			exit;
		}

		//by default
		$to_dt = date('Y-m-d');
		$from_dt = date('Y-m-d',strtotime('-1 month'));

		if ($this->request->is('post')) {

			//on search
			$to_dt = 	$this->request->getData('to_dt');
			$from_dt = $this->request->getData('from_dt');


			if (empty($from_dt) || empty($to_dt)) {

				return null;
			}
			$this->set(compact('to_dt','from_dt'));
		


			if (!empty($from_dt) || !empty($to_dt)) {

				//check current user,if RO/SO In-charge then show logs of users under his/her office only
				$check_incharge = $this->DmiRoOffices->find('list',array('fields'=>'id','conditions'=>array('ro_email_id IS'=>$username,'office_type IN'=>array('RAL','CAL'),'delete_status IS'=>null)))->toList();


				//get users for RO/SO incharge
				if (!empty($check_incharge)) {

					$get_users = $this->DmiUsers->find('list',array('keyField'=>'id','valueField'=>'email','conditions'=>array('division IN'=>array('LMIS','BOTH'),'posted_ro_office IN'=>$check_incharge)))->toArray();

				//get all users for Admin
				} else {

					$get_users = $this->DmiUsers->find('list',array('keyField'=>'id','valueField'=>'email','conditions'=>array('division IN'=>array('LMIS','BOTH'))))->toArray();

				}

				//get logs
				$user_logs = $this->DmiUserLogs->find('all', array('conditions'=>array('email_id IN'=>$get_users,'date(date) >=' =>$from_dt, 'date(date) <=' =>$to_dt),'order' => 'id DESC'))->toArray();

				//to hide current session logout time.
				$user_logs[0]['time_out'] = null;

				$this->set('user_logs',$user_logs);

			}

		}

	}

/************************************************************************************************************************************************************************************************************************/

	//added the function on 12-11-2020 by Amol
	public function adminLogs() {

		if ($this->Session->read('username') == null) {
			echo "Sorry You are not authorized to view this page..";?><a href="<?php echo $this->request->getAttribute('webroot');?>"> Please Login</a><?php
			exit;
		}

		$this->viewBuilder()->setLayout('admin_dashboard');
		$this->loadModel('DmiUserRoles');
		$this->loadModel('DmiUserLogs');

		//get admin users
		$get_admins = $this->DmiUserRoles->find('list',array('keyField'=>'id', 'valueField'=>'user_email_id','conditions'=>array('super_admin'=>'yes')))->toList();

		$user_logs = $this->DmiUserLogs->find('all', array('conditions'=>array('email_id IN'=>$get_admins),'order' => 'id DESC'))->toArray();
		//to hide current session logout time.
		$user_logs[0]['time_out'] = null;

		$this->set('user_logs',$user_logs);

	}

/************************************************************************************************************************************************************************************************************************/

	//to logout user from dashboard
	public function logout() {

		$this->loadModel('DmiUserLogs');
		$username = $this->getRequest()->getSession()->read('username');

		if (!empty($username)) {
			
			$list_id = $this->DmiUserLogs->find('list', array('valueField' => 'id', 'conditions' => array('email_id IS' => $username)))->toList();

			if (!empty($list_id)) {

				$fetch_last_id_query = $this->DmiUserLogs->find('all',array('fields'=>'id', 'conditions'=>array('id'=>max($list_id), 'remark'=>'Success')))->first();
				$fetch_last_id = $fetch_last_id_query['id'];
	
				$DmiUserLogsEntity = $this->DmiUserLogs->newEntity(array(
					'id'=> $fetch_last_id,
					'time_out'=>date('H:i:s')
				));
				$this->DmiUserLogs->save($DmiUserLogsEntity);
	

				$this->Authentication->browserLoginStatus($username,null);
	
				$this->Session->destroy();
	
				$this->redirect('/');
			
			} else {
	
				echo "Sorry You are not authorized to view this page..";?><a href="<?php echo $this->request->getAttribute('webroot');?>"> Please Login</a><?php
				exit;
			}
		
		} else {

			$this->redirect('/');
		}
	
	}

/************************************************************************************************************************************************************************************************************************/

	//User Profile added on 07-05-2021 by Amol
	//copied from DMI user profile
	public function userProfile() {

		$this->viewBuilder()->setLayout('admin_dashboard');
		$this->loadModel('DmiUserRoles');
		$this->loadModel('DmiUsers');
		$this->loadModel('DmiUserHistoryLogs');

		$this->loadComponent('Customfunctions');


		// set variables to show popup messages from view file
		$message = '';
		$message_theme = '';
		$redirect_to = '';

		// Show the assigned users list (Done by Pravin 08-03-2018)
		$assigned_old_roles = $this->DmiUserRoles->find('all',array('conditions'=>array('user_email_id IS'=>$this->request->getSession()->read('username'))))->toArray();
		$this->set('assigned_old_roles',$assigned_old_roles);

		$user_data = $this->DmiUsers->find('all',array('conditions'=>array('email IS'=>$this->request->getSession()->read('username'))))->toArray();

		if (!empty($user_data)){

			//get personal details masked by custom function to show in secure mode
			//applied on 12-10-2017 by Amol
			$user_data[0]['phone'] = $this->Customfunctions->getMaskedValue(base64_decode($user_data[0]['phone']),'mobile');  
			$user_data[0]['email'] = $this->Customfunctions->getMaskedValue(base64_decode($user_data[0]['email']),'email'); //for email encoding
			$this->set('user_data',$user_data);

			if (null !== ($this->request->getData('ok'))) {
				$this->redirect('/dashboard/home');
			} elseif (null !== ($this->request->getData('update'))) {

				//applied condition to check all post data for !empty validation on server side
				//on 21/10/2017 by Amol
				if (!empty($this->request->getData('f_name')) && !empty($this->request->getData('l_name')) && !empty($this->request->getData('phone')) /*&& !empty($this->request->getData('once_card_no'))*/) {

					//Html Encoding data before saving
					$htmlencodedfname = htmlentities($this->request->getData('f_name'), ENT_QUOTES);
					$htmlencodedlname = htmlentities($this->request->getData('l_name'), ENT_QUOTES);
					$htmlencodedlandline = htmlentities($this->request->getData('landline'), ENT_QUOTES);

					$fetch_user_id = $this->DmiUsers->find('all', array('fields'=>'id', 'conditions'=>array('email IS'=>$this->request->getSession()->read('username'))))->first();

					$user_id = $fetch_user_id['id'];


					//below query & conditions added on 12-10-2017 by Amol
					//To check if mobile,aadhar post in proper format, if not then save old value itself from DB
					$user_data = $this->DmiUsers->find('all',array('conditions'=>array('email IS'=>$this->request->getSession()->read('username'))))->first();

					if (preg_match("/^[X-X]{6}[0-9]{4}$/i", $this->request->getData('phone'),$matches)==1)
					{
						$htmlencodedphone = $user_data['phone'];
					}

					//added on 06-05-2021 for profile pic
					if ($this->request->getData('profile_pic')->getClientFilename() != null) {

						$attachment = $this->request->getData('profile_pic');
						$file_name = $attachment->getClientFilename();
						$file_size = $attachment->getSize();
						$file_type = $attachment->getClientMediaType();
						$file_local_path = $attachment->getStream()->getMetadata('uri');
						$profile_pic = $this->Customfunctions->fileUploadLib($file_name,$file_size,$file_type,$file_local_path); // calling file uploading function

					} else {

						$profile_pic = $user_data['profile_pic'];
					}

					$DmiusersEntity = $this->DmiUsers->newEntity(array(

						'id'=>$user_id,
						'f_name'=>$htmlencodedfname,
						'l_name'=>$htmlencodedlname,
						'landline'=>base64_encode($htmlencodedlandline),
						'profile_pic'=>$profile_pic //added on 06-05-2021 for profile pic

					));

					if ($this->DmiUsers->save($DmiusersEntity)) {

						//Save the user profile update logs history (Done by Pravin 13/02/2018)
						$DmiUserHistoryLogsEntity = $this->DmiUserHistoryLogs->newEntity(array(

							'f_name'=>$htmlencodedfname,
							'l_name'=>$htmlencodedlname,
							'email'=>$user_data['email'],
							'phone'=>$htmlencodedphone,
							'landline'=>base64_encode($htmlencodedlandline),
							'division'=>$user_data['division'],
							'role'=>$user_data['role'],
							'password'=>$user_data['password'],
							'created_by_user'=>$user_data['created_by_user'],
							'posted_ro_office'=>$user_data['posted_ro_office'],
							'profile_pic'=>$profile_pic //added on 06-05-2021 for profile pic

						));

						$this->DmiUserHistoryLogs->save($DmiUserHistoryLogsEntity);

						$this->Session->write('f_name',$htmlencodedfname);
						$this->Session->write('l_name',$htmlencodedlname);

						$message = 'Profile data updated successfully';
						$message_theme = 'success';
						$redirect_to = 'user_profile';

					} else {

						$message = 'Sorry...Please check your fields again';
						$message_theme = 'failed';
						$redirect_to = 'user_profile';
					}

				} else {

					$message =  "Please check some fields are not entered";
					$message_theme = 'failed';
					$redirect_to = 'user_profile';
				}
			}

		} else {

			echo "Sorry You are not authorized to view this page..";?><a href="<?php echo $this->request->getAttribute('webroot');?>"> Please Login</a><?php
			exit;
		}

		//Set variables to show popup messages from view file
		$this->set('message',$message);
		$this->set('message_theme',$message_theme);
		$this->set('redirect_to',$redirect_to);

	}


	//////////////////////////////////////////////////////////////////////////////////////////////////////////|[User Work Tranfer]|

	//For LIMS user deactivation or user transfer check	
	//this function is used to check the LIMS sample in progress with user, which is selected to deactivate or transfer and reallocated to another user.
	//Done By Pravin Bhakare 10-08-2019
	
	public function userPendingWorkTransfer() {
		
		//Load Models
		$this->loadModel('DmiUsers');
		$this->loadModel('DmiRoOffices');
		$this->loadModel('DmiUserRoles');
		$this->loadModel('Workflow');
		$this->loadModel('MSampleAllocate');
		$this->loadModel('SampleInward');
		$this->loadModel('LimsUserTransfer');

		// set variables to show popup messages from view file
		$message = '';
		$message_theme = '';
		$redirect_to = '';


		$authorized_user = $this->DmiUsers->find('all')->select(['id'])->join(['DmiUserRoles'=> ['table' => 'dmi_user_roles', 'type' => 'INNER','conditions' => ['DmiUserRoles.user_email_id = DmiUsers.email', 'DmiUserRoles.super_admin' => 'yes','DmiUsers.id' => $this->Session->read('user_code')]]])->first();
		
		$ral_cal_list = $this->DmiRoOffices->find('list',array('keyField'=>'id','valueField'=>'ro_office','conditions'=>array('office_type IN'=>array('RAL','CAL'),'delete_status IS NULL')))->toArray();
	
		//$ral_cal_list = $this->DmiRoOffices->find('all')->select(['id,ro_office'])->where(['office_type IN'=>array('RAL','CAL'),'delete_status IS NULL'])->combine(['id,ro_office'])->toArray();
		$this->set('ral_cal_list',$ral_cal_list);
		
		if (!empty($authorized_user)) {
			
			$this->viewBuilder()->setLayout('admin_dashboard');
			
			$updated_user_list = array();
			
			$user_lists = $this->DmiUsers->find('all',array('fields'=>array('id','f_name','l_name','email'),'conditions'=>array('status !='=>'disactive','division IN'=>array('BOTH','LMIS')),'order'=>'id'))->toArray();
			
			if (!empty($user_lists)) {
				
				foreach ($user_lists as $user) {
					
					$user_list[$user['id']] = trim($user['f_name'])." ".trim($user['l_name'])."(".trim($user['email']).")";
				}
			}
			
			asort($user_list);
			
			$this->set('user_list',$user_list);
				
			if ($this->request->is('post')) {
				
				$from_user = htmlentities($this->request->getData('from_user'), ENT_QUOTES);
				$to_user = htmlentities($this->request->getData('to_user'), ENT_QUOTES);
				$reason = htmlentities($this->request->getData('reason'), ENT_QUOTES);				
		
				if (!empty($from_user) && !empty($to_user) && !empty($reason) ) {
					
					if (is_numeric($from_user) && is_numeric($to_user)) {
						
						$valid_user = $this->DmiUsers->find('list',array('conditions'=>array('id IN'=>array($from_user,$to_user))))->toArray();
						
						if (count($valid_user) == 2 ) {
							
							//Check from user id in distination user code colume in workflow table and get list of original sample code.
							//$asDestinationUserSampleTemp = $this->Workflow->find('all',array('keyField'=>'id','valueField'=>'org_sample_code','conditions'=>array('dst_usr_cd'=>$from_user),'order'=>'id desc'))->toArray();
							
							$asDestinationUserSampleTemp = $this->Workflow->find('all')->select(['id','org_sample_code'])->where(['dst_usr_cd'=>$from_user])->order(['id' => 'DESC'])->combine('id', 'org_sample_code')->toArray();
							$asDestinationUserSample = array_unique($asDestinationUserSampleTemp);
							
							//Check all get original sample code is final graded or not and get final graded sample list.
							//$finalGradingCompletedSampleTemp = $this->Workflow->find('all',array('keyField'=>'id','valueField'=>'org_sample_code','conditions'=>array('org_sample_code IN'=>$asDestinationUserSample,'stage_smpl_flag'=>'FG')))->toArray();
							$finalGradingCompletedSampleTemp = $this->Workflow->find('all')->select(['id','org_sample_code'])->where(['org_sample_code IN'=>$asDestinationUserSample,'stage_smpl_flag'=>'FG'])->combine('id','org_sample_code')->toArray();
							$finalGradingCompletedSample = array_unique($finalGradingCompletedSampleTemp);
												
							//Getting the sample list that have not final graded yet.
							$PendingFinalGradingSample = array_diff($asDestinationUserSample,$finalGradingCompletedSample);	
							
							$teststatus = array();  
							$maxresultID = array(); 
							$pendingtest = array(); 
							$tabc = array(); 
							$chemistcode = array(); 
							$finalresult = array(); 
							$in_src_usr_cd_pr = array();
							$update_src_code_id = array();//updated on 15-05-2021

							foreach ($PendingFinalGradingSample as $eachkey => $eachValue ) {
								
								//Getting list of stage sample status flag for particular original sample code for from user id
								$result = $this->Workflow->find('list',array('keyField'=>'id','valueField'=>'stage_smpl_flag','conditions'=>array('org_sample_code IS'=>$eachValue,'dst_usr_cd IS'=>$from_user),'order'=>'id'))->toArray();
							
								//$result = $this->Workflow->find('all')->select(['id','stage_smpl_flag'])->where(['org_sample_code IS'=>$eachValue,'dst_usr_cd IS'=>$from_user])->combine(['id','stage_smpl_flag'])->order('id')->toArray();
								
								//Getting current stage sample status flag for particular original sample code for from user id
								$current_sample_status = $this->Workflow->find('all',array('fields'=>array('id','stage_smpl_flag'),'conditions'=>array('org_sample_code IS'=>$eachValue),'order'=>'id desc'))->first();
								
								
								//Checked and make list of TA and TABC stage sample status flag
								foreach ($result as $eachkey1 => $eachValue1) {
									
									if (trim($eachValue1) == 'TA' ) {
										
										$teststatus[] = $eachkey1;
									}

									if (trim($eachValue1) =='TABC') {
										
										$tabc[] = $eachkey1;
									}
								}
								
								if (in_array(trim($current_sample_status['stage_smpl_flag']),array('SD','TA','TABC'))) {
									
									$currentsamplestatus[] = array($current_sample_status['id'],$eachkey1);
								
								} else {
									
									$currentsamplestatus[] = array($current_sample_status['id']);
								}
															
								
								//store max id of particular original sample code
								$maxresultID[] = $eachkey1;					
							
								$in_src_usr_cd_pr_list = $this->Workflow->find('list',array('keyField'=>'id','valueField'=>'stage_smpl_cd','conditions'=>array('org_sample_code IS'=>$eachValue,'src_usr_cd IS'=>$from_user,'stage_smpl_flag'=>'TA'),'order'=>'id'))->toArray();
								//$in_src_usr_cd_pr_list = $this->Workflow->find('all')->select(['id','stage_smpl_cd'])->where(['org_sample_code IS'=>$eachValue,'src_usr_cd IS'=>$from_user,'stage_smpl_flag'=>'TA'])->combine(['id','stage_smpl_cd'])->order('id')->toArray();
								
								if (!empty($in_src_usr_cd_pr_list)) {

									$in_src_usr_cd_pr[] = $in_src_usr_cd_pr_list;
								}							
							}
							
							
							foreach ($in_src_usr_cd_pr as $eachloop) {
								
								foreach ($eachloop as $eachloopkey =>$eachloopvalue ) {
									
									$loopvaluewithFT = $this->Workflow->find('all',array('fields'=>array('id'),'conditions'=>array('stage_smpl_cd IS'=>$eachloopvalue,'stage_smpl_flag'=>'FT')))->first();
									
									if (empty($loopvaluewithFT)) {
										
										$update_src_code_id[] = trim($eachloopkey);
									}
								}
							}
							
							//Getting list of actual pending samples on from user side.
							foreach ($maxresultID as $resultKey =>$resultValue) {								
								
								if (in_array($resultValue,$currentsamplestatus[$resultKey])) {	
								
									$finalresult[] = $resultValue;
								}				
							}
							
							//Getting list of actual pending allocated test sample code on from user side.
							if (!empty($teststatus)) {
								
								foreach ($teststatus as $eachtest) {
									
									$teststagecd = $this->Workflow->find('all',array('fields'=>array('id','stage_smpl_cd'),'conditions'=>array('id IS'=>$eachtest)))->first();
									$testsamplestage = $this->Workflow->find('all',array('fields'=>array('id','stage_smpl_cd'),'conditions'=>array('stage_smpl_cd IS'=>$teststagecd['stage_smpl_cd'],'stage_smpl_flag'=>'FT')))->first();
									
									if (empty($testsamplestage)) {

										$pendingtest[] = $eachtest;
										$chemistcode[] = trim($teststagecd['stage_smpl_cd']);
									}
								}
							}

							$chemist_allocated = $this->MSampleAllocate->find('list',array('valueField'=>'chemist_code','valueField'=>'sr_no','conditions'=>array('chemist_code IN'=>$chemistcode)))->toArray();
							//$chemist_allocated = $this->MSampleAllocate->find('list')->select(['chemist_code','sr_no'])->where(['chemist_code IS'=>$chemistcode])->combine(['chemist_code','sr_no'])->toArray();
							
							$finalPendingList = array_unique(array_merge(array_diff($finalresult,$tabc),$pendingtest));
							
							$worktransfer = 'no';
							
							
							if (!empty($finalPendingList)) {
								
								foreach ($finalPendingList as $finalpendingvalue) {
									
							
									$original_code_value = $this->Workflow->find('all',array('fields'=>array('org_sample_code'),'conditions'=>array('id IS'=>$finalpendingvalue)))->first();  
									$original_code = $original_code_value['org_sample_code'];
								
									$dataValues[] = array(
									
										'id'=>$finalpendingvalue,
										'dst_usr_cd'=>$to_user
									
									);
								}
						
								$theWorkflowModelEntity = $this->Workflow->newEntities($dataValues);

								foreach ($theWorkflowModelEntity as $each) {	

									if ($this->Workflow->save($each)) {

										$worktransfer = 'yes';
									}
					
									$this->SampleInward->updateAll(array('user_code'=>"$to_user"),array('org_sample_code'=>$original_code));
								}
																
							}
							


							if (!empty($chemist_allocated)) {
								
								foreach ($chemist_allocated as $each_chemis_code) {

									$dataValues[] = array(
									
										'sr_no'=>$each_chemis_code,
										'alloc_to_user_code'=>$to_user
									
									);
								}
								
								$mSampleAllocateEntity = $this->MSampleAllocate->newEntities($dataValues);

								foreach ($mSampleAllocateEntity as $each) {

									if ($this->MSampleAllocate->save($each)) {
										
										$worktransfer = 'yes';
									}
								}
							}
							


							if (!empty($update_src_code_id)) {
								
								foreach ($update_src_code_id as $each_src_id) {

									$dataValues[] = array(

										'id'=>$each_src_id,
										'src_usr_cd'=>$to_user
									);
								}
									
								$theWorkflowModelEntity = $this->Workflow->newEntities($dataValues);

								foreach ($theWorkflowModelEntity as $each) {

									if ($this->Workflow->save($each)) {
										
										$worktransfer = 'yes';
									}
								}
							}

							
							if ($worktransfer == 'yes') {


								$limsUserTransferEntity = $this->LimsUserTransfer->newEntity(array(

									'from_user'=>$from_user,
									'to_user'=>$to_user,
									'reason'=>$reason,
									'user_id'=>$_SESSION['user_code'],
									'created'=>date('Y-m-d H:i:s'),
									'modified'=>date('Y-m-d H:i:s')
								));


								if ($this->LimsUserTransfer->save($limsUserTransferEntity)) {

									$message = 'Pending work transfered successfully';
									$message_theme = 'success';
									$redirect_to = 'user_pending_work_transfer';
								};
							}							
													
						} else {
							
							$message = 'From user or To user value is Invalid';
							$message_theme = 'failed';
						}
					
					} else {
						
						$message = 'From user or To user value is Invalid';
						$message_theme = 'failed';
					}					
					
				} else {
					
					$message = 'Check All Input Fields Properly!';
					$message_theme = 'failed';
				}
			}
			
		} else {
			
			return $this->redirect(array('action' => 'home'));
		}


		// set variables to show popup messages from view file
		$this->set('message',$message);
		$this->set('message_theme',$message_theme);
		$this->set('redirect_to',$redirect_to);


	}
		

	public function getToUserListForTransfer() {//updated function name on 14-04-2021 by Amol
		
		$this->autoRender = false;
		
		$from_user_id 	= $_POST['from_user_id'];
		$from_office_id = $_POST['from_office_id'];//added on 14-04-2021 by Amol
		$fromuserrole = $this->DmiUsers->find('all',array('fields'=>'role','conditions'=>array('id IS'=>$from_user_id)))->first();
		$user_lists   = $this->DmiUsers->find('all',array('fields'=>array('id','f_name','l_name','email'),
															'conditions'=>array('status !='=>'disactive','division IN'=>array('BOTH','LMIS'),
															'role'=>$fromuserrole['role'],'posted_ro_office'=>$from_office_id),'order'=>'f_name'))->toArray();
		//pr($user_lists); exit;
		$user_list = array();
		
		if (!empty($user_lists)) {
			
			foreach ($user_lists as $user) {				
				
				?><option value='<?php echo $user['id']; ?>'><?php echo trim($user['f_name'])." ".trim($user['l_name'])."(".trim(base64_decode($user['email'])).")"; ?></option><?php
			}
		}					
	}
		
	
	//created new function on 14-04-2021 by Amol
	public function getFromUserListForTransfer() {
		
		$this->autoRender = false;
		
		$from_office_id = $_POST['from_office_id'];
		$fromuserrole   = $this->DmiUsers->find('all',array('fields'=>'role','conditions'=>array('id IS'=>$from_office_id)))->first();
		$user_lists     = $this->DmiUsers->find('all',array('fields'=>array('id','f_name','l_name','email'),'conditions'=>array('status !='=>'disactive','posted_ro_office IN'=>$from_office_id),'order'=>'f_name'));
		$user_list = array();
		if (!empty($user_lists)) {
			
			foreach ($user_lists as $user) {				
				
				?><option value='<?php echo $user['id']; ?>'><?php echo trim($user['f_name'])." ".trim($user['l_name'])."(".trim(base64_decode($user['email'])).")"; ?></option><?php 
			}
		}					
	}

}
?>
