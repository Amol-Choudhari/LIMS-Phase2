<?php

//To access the properties of main controller used initialize function.

namespace app\Controller\Component;
use Cake\Controller\Controller;
use Cake\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Datasource\EntityInterface;
use Cake\Utility\Security;

class AuthenticationComponent extends Component {

    public $components= array('Session');
    public $controller = null;
    public $session = null;

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function initialize(array $config):void{
        parent::initialize($config);
        $this->Controller = $this->_registry->getController();
        $this->Session = $this->getController()->getRequest()->getSession();
    }

/***************************************************************************************************************************************************************************************************/  
    
    //TO ENCRYPT THE STRING
    public function encrypt($string) {
        
        $result = '';
        $key="D@M@I753||=+753agmark(nic)";
            for($i=0; $i<strlen($string); $i++) {

                    $char = substr($string, $i, 1);
                    $keychar = substr($key, ($i % strlen($key))-1, 1);
                    $char = chr(ord($char)+ord($keychar));
                    $result.=$char;
            }

        return base64_encode($result);
    }

/***************************************************************************************************************************************************************************************************/

    //TO DECRYPT THE STRING
    public function decrypt($string) {

        $result = ''; $key="D@M@I753||=+753agmark(nic)";
        $string = base64_decode($string);

        for($i=0; $i<strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key))-1, 1);
            $char = chr(ord($char)-ord($keychar));
            $result.=$char;
        }

        return $result;
    }

/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>------[Forgot Password Library]------>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
   
    //FORGOT PASSWORD LIBRARY 
    public function forgotPasswordLib($table,$emailforrecovery,$customer_id=null) {

        $Dmitable = TableRegistry::getTableLocator()->get($table);
        
        if (!empty($customer_id)) {

            $get_record_details = $Dmitable->find('all', array('conditions'=> array('email IS' => $emailforrecovery, 'customer_id IS'=>$customer_id)))->first();

        } else {

            $get_record_details = $Dmitable->find('all', array('conditions'=> array('email IS' => $emailforrecovery)))->first();
        }

        if ($get_record_details == null) {

            return 1;

        } else {

            if ($table=='DmiCustomers' || $table=='DmiFirms') {

                $key_id = md5($get_record_details['id'].time().rand());

                // Added the urlencode funtion to fix the issue of +,<,# etc issue in gettin through get parameter
                $encrypted_user_id = urlencode($this->encrypt($get_record_details['customer_id']));
                $controller = 'customers';

            } elseif ($table=='DmiUsers') {

                $key_id = md5($get_record_details['id'].time().rand());

                // Added the urlencode funtion to fix the issue of +,<,# etc issue in gettin through get parameter
                $encrypted_user_id = urlencode($this->encrypt($emailforrecovery));
                $controller = 'users';
            }

            $url = 'home.?'.'$key='.$key_id.'&'.'$id='.$encrypted_user_id;
            $host_path = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
            $sendlink = "<html><body><a href='$host_path/DMI/$controller/reset_password/$url'>Please click here to set Password</a></body></html>";
            $to = base64_encode($emailforrecovery); //for email encoding
            $subject = 'DMI AGMARK Set Password Link';
           
            $txt = 	'Hello' .
                "<html><body><br></body></html>".'Click the below link OR copy it to browser address bar:' .
                "<html><body><br></body></html>" .$host_path.'/DMI/'.$controller.'/reset_password/'.$url.
                "<html><body><br></body></html>".'Above link will be active only for 24 hours. If expired, then try to set your password from forgot Password option on DMI portal'.
                "<html><body><br></body></html>".'Thanks & Regards,' .
                "<html><body><br></body></html>" .'Directorate of Marketing & Inspection,' .
                "<html><body><br></body></html>" .'Ministry of Agriculture and Farmers Welfare,' .
                "<html><body><br></body></html>" .'Government of India.';

            //	$txt = $sendlink;
            $headers = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers .= "From: dmiqc@nic.in";
            //	mail($to,$subject,$txt,$headers, '-f dmiqc@nic.in'); //added new parameter '-f dmiqc@nic.in' on 08-12-2018 by Amol
            //commented for testing mode

            //store reset password link keys in DB
            if ($table=='DmiCustomers' || $table=='DmiFirms') {
                $DmiApplicantsResetpassKeys = TableRegistry::getTableLocator()->get('DmiApplicantsResetpassKeys');
                $DmiApplicantsResetpassKeys->saveKeyDetails($get_record_details['customer_id'],$key_id);

            } elseif ($table=='DmiUsers') {

                $DmiUsersResetpassKeys = TableRegistry::getTableLocator()->get('DmiUsersResetpassKeys');
                $DmiUsersResetpassKeys->saveKeyDetails($emailforrecovery,$key_id);
            }

            //$this->Session->write('username',$emailforrecovery);
            return 2;
        }

    }


/***************************************************************************************************************************************************************************************************/

/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>-------[Change Password Library]--------->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

    //CHANGE PASSWORD LIBRARY
    public function changePasswordLib($table,$username,$oldpassdata,$newpassdata,$confpassdata,$randsalt) {

        //CHECK LAST THREE PASSWORD WITH NEW PASSWORD IF FOUND, THROW ERROR FOR RESTRICT BRUTE FORCE ATTACK UNDER SECURITY AUDIT By Aniket Ganvir dated 16th NOV 2020
        $newpassdataEncoded = htmlentities($newpassdata, ENT_QUOTES);
        $passwordWithoutSalt = substr($newpassdataEncoded,strlen($randsalt));
        $DmiPasswordLogs = TableRegistry::getTableLocator()->get('DmiPasswordLogs');
        $checkPastThreePassword = $DmiPasswordLogs->checkPastThreePassword($username, $table, $passwordWithoutSalt);

		if($checkPastThreePassword == 'found') {
			return 4;
		}
        
        $Dmitable = TableRegistry::getTableLocator()->get($table);

		if ($newpassdata == $confpassdata) {

            //Admin Users
            if ($table == 'DmiUsers') { 

                $PassFromdb = $Dmitable->find('all', array('fields'=>'password','conditions'=> array('email IS' => $username)))->first();
                
            //Customers  
            } elseif ($table == 'DmiCustomers' || $table == 'Dmi_firm') {

                $PassFromdb = $Dmitable->find('all', array('fields'=>'password','conditions'=> array('customer_id IS' => $username)))->first();
            }

            $passarray = $PassFromdb['password'];
            $PassFromdbsalted = $randsalt . $passarray;
            $Dbpasssaltedsha512 = hash('sha512',$PassFromdbsalted);

				if ($oldpassdata == $Dbpasssaltedsha512) {

                    $Removesaltnewpass = substr($newpassdata,strlen($randsalt));
                    
                    //For Admin Users
                    if ($table == 'DmiUsers') { 

                        $Dmitable_id = $Dmitable->find('all',array('fields'=>'id','conditions'=>array('email IS'=>$username),'order'=>array('id desc')))->first();
                    
                    //For Customers
                    } elseif ($table == 'DmiCustomers' || $table == 'DmiFirms') {

                        $Dmitable_id = $Dmitable->find('all',array('fields'=>'id','conditions'=>array('customer_id IS'=>$username),'order'=>array('id desc')))->first();
                    }

                    if ($Dmitable_id) {

                        $DmitableEntity = $Dmitable->newEntity(['id'=>$Dmitable_id['id'],'password'=>$Removesaltnewpass,'modified'=>date('Y-m-d H:i:s')]);
                        $Dmitable->save($DmitableEntity);
                        // MAINTAIN PASSWORD LOGS FOR RESTRICT BRUTE FORCE ATTACK By Aniket Ganvir dated 16th NOV 2020
                        $DmiPasswordLogs->savePasswordLogs($username, $table, $Removesaltnewpass);

                    } else {

                        $this->userActionPerformLog("Password Changed","Failed");
                        return 1;
                    }
                } else {

                    $this->userActionPerformLog("Password Changed","Failed");
                    return 2;
			    }
        } else {

            $this->userActionPerformLog("Password Changed","Failed");
            return 3;
        }
    
    }

/***************************************************************************************************************************************************************************************************/

/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>-------<Reset Password Library>--------->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

    //RESET PASSWORD LIBRARY
    public function resetPasswordLib($table,$username,$newpassdata,$randsalt) {

        $Dmitable = TableRegistry::getTableLocator()->get($table);
        // CHECK LAST THREE PASSWORD WITH NEW PASSWORD IF FOUND, THROW ERROR FOR RESTRICT BRUTE FORCE ATTACK UNDER SECURITY AUDIT // By Aniket Ganvir date 16th NOV 2020
        $newpassdataEncoded = htmlentities($newpassdata, ENT_QUOTES);
        $passwordWithoutSalt = substr($newpassdataEncoded,strlen($randsalt));
        $DmiPasswordLogs = TableRegistry::getTableLocator()->get('DmiPasswordLogs');
        $checkPastThreePassword = $DmiPasswordLogs->checkPastThreePassword($username, $table, $passwordWithoutSalt);

        if ($checkPastThreePassword == 'found') {
            return 4;
        }

        //this condition added on 14-02-2018 by Amol
        if ($table=='DmiFirms' || $table=='DmiCustomers') {

            $form_name = TableRegistry::getTableLocator()->get('DmiCustomers');

            /* Update the last logs user entery with 'Success' status - Change on 05-12-2018 - By Pravin Bhakare - Suggested by Navin Sir */
            /* Why Change :- If user lock by three consecutive unsuccessful login and if user change the password successfuly then system automatically unlock the user */
            $log_table = TableRegistry::getTableLocator()->get('DmiCustomerLogs');
        } else {

            $form_name = TableRegistry::getTableLocator()->get('DmiUsers');
            /* Update the last logs user entery with 'Success' status - Change on 05-12-2018 - By Pravin Bhakare - Suggested by Navin Sir */
            /* Why Change :- If user lock by three consecutive unsuccessful login and if user change the password successfuly then system automatically unlock the user */
            $log_table = TableRegistry::getTableLocator()->get('DmiUserLogs');
        }

        $Dmilogtable = $log_table;

        if ($newpassdata == $this->request->getData('confirm_password')) {

            if ($this->request->getData('captcha') !="" && $_SESSION["code"] == $this->request->getData('captcha')) {

                $Removesaltnewpass = substr($newpassdata,strlen($randsalt)) ;

                //For Admin Users
                if ($table == 'DmiUsers') { 

                    $Dmitable_id = $Dmitable->find('all',array('fields'=>'id','conditions'=>array('email IS'=>$username),'order'=>array('id desc')))->first();
                    /* Update the last logs user entery with 'Success' status - Change on 05-12-2018 - By Pravin Bhakare - Suggested by Navin Sir */
                    /* Why Change :- If user lock by three consecutive unsuccessful login and if user change the password successfuly then system automatically unlock the user */
                    $log_ids = $Dmilogtable->find('all',array('fields'=>'id','conditions'=>array('email_id IS'=>$username),'order'=>array('id desc')))->first();
                    
                //For Customers
                } elseif ($table == 'DmiCustomers' || $table == 'DmiFirms') {

                    $Dmitable_id = $Dmitable->find('all',array('fields'=>'id','conditions'=>array('customer_id IS'=>$username),'order'=>array('id desc')))->first();
                    /* Update the last logs user entery with 'Success' status - Change on 05-12-2018 - By Pravin Bhakare - Suggested by Navin Sir */
                    /* Why Change :- If user lock by three consecutive unsuccessful login and if user change the password successfuly then system automatically unlock the user */
                    $log_ids = $Dmilogtable->find('all',array('fields'=>'id','conditions'=>array('customer_id IS'=>$username),'order'=>array('id desc')))->first();

                }

                    if ($Dmitable_id) {

                        $DmitableEntity = $Dmitable->newEntity(['id'=>$Dmitable_id['id'],'password'=>$Removesaltnewpass,'modified'=>date('Y-m-d H:i:s')]);
                        $Dmitable->save($DmitableEntity);

                        // MAINTAIN PASSWORD LOGS FOR RESTRICT BRUTE FORCE ATTACK By Aniket Ganvir dated 16th NOV 2020
                        $DmiPasswordLogs->savePasswordLogs($username, $table, $Removesaltnewpass);

                        /* Update the last logs user entery with 'Success' status - Change on 05-12-2018 - By Pravin Bhakare - Suggested by Navin Sir */
                        /* Why Change :- If user lock by three consecutive unsuccessful login and if user change the password successfuly then system automatically unlock the user */
                    
                        if (!empty($log_ids['id'])) {

                            $log_id = $log_ids['id'];
                            $log_tableEntity = $log_table->newEntity(['id'=>$log_id,
                                'ip_address'=>$this->request->clientIp(),
                                'date'=>date('Y-m-d'),
                                'time_in'=>date('H:i:s'),
                                'remark'=>'Success',
                                'unlock'=>'by_login_user']);
                        
                                $log_table->save($log_tableEntity);
                        }

                    } else {

                        $this->userActionPerformLog("Password Reset","Failed");
                        return 1;
                    }
            } else {

              $this->userActionPerformLog("Password Reset","Failed");
                return 2;
            }

        } else {

            $this->userActionPerformLog("Password Reset","Failed");
            return 3;
        }


    }

/***************************************************************************************************************************************************************************************************/

/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>-------<User Login Library>------->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

    //USER LOGIN LIBRARY
    public function userLoginLib($table_name,$username,$password,$randsalt) {

        $table = TableRegistry::getTableLocator()->get($table_name);
        $DmiUserLogs = TableRegistry::getTableLocator()->get('DmiUserLogs');
        $DmiUserRoles = TableRegistry::getTableLocator()->get('DmiUserRoles');
        $DmiRoOffices = TableRegistry::getTableLocator()->get('DmiRoOffices');

		$username = base64_encode($username);//for email encoding
        //Captcha Check
        if ($this->getController()->getRequest()->getData('captcha') !="" && $this->Session->read('code') == $this->getController()->getRequest()->getData('captcha')) {

            $PassFromdb = $table->find('all', array('fields'=>'password','conditions'=> array('email IS' => $username, 'status'=>'active','division IN'=>array('LMIS','BOTH'))))->first();

            if ($PassFromdb != null && $PassFromdb != '') {

                $passarray = $PassFromdb['password'];
                $emailforrecovery = $PassFromdb['email'];

	            if (strlen($passarray) == 128 ) {

                    //Adding Random Salt Value To Password
                    $PassFromdbsalted = $randsalt . $passarray; 
                    //Encrypting Salted Password To sha512
                    $Dbpasssaltedsha512 = hash('sha512',$PassFromdbsalted); 

                    $current_ip = $this->Controller->getRequest()->clientIp();
                    if($current_ip == '::1')
                    {
                        $current_ip = '127.0.0.1';
                    }

                    //Check & Match Password To Database Password
                    if ($password == $Dbpasssaltedsha512) {

                        //created/updated/added on 25-06-2021 for multiple logged in check security updates, by Amol
                        $checkLog = $this->alreadyLoggedInCheck($username);
                        if($checkLog == 'norecord'){
                                
                            //the logic from here is transffered to the function and called here
                            //on 25-06-2021 by Amol
                            $this->userProceedLogin($username,$table);

                        }else{
                            
                            $_SESSION['username'] = $username;
                            $_SESSION['userloggedin'] = 'no';
                            return 5;
                        }
                
                    }else{
                      

                        $DmiUserLog = $DmiUserLogs->newEntity([
                                
                            'email_id'=>$username,
                            'ip_address'=>$current_ip,
                            'date'=>date('Y-m-d'),
                            'time_in'=>date('H:i:s'),
                            'remark'=>'Failed']);

                        $DmiUserLogs->save($DmiUserLog);


                        // this echo statment commented because this messages shows on usersController side (by pravin 27/05/2017)
                        //echo "<script> alert('login failed')</script>";
                        return 1;
                    }

                }else{

                    $this->forgotPasswordLib($table_name,$emailforrecovery);
                    $user_data_query = $table->find('all', array('conditions'=> array('email' => $username)))->first();
                    $mobileno = $user_data_query['phone'];
                    $sms_message = 'Your password has been expired, the link to reset password is sent on email id '.base64_decode($emailforrecovery).'. AGMARK'; //for email encoding
                    $template_id = 1107161673473567580;
                    $this->sendSms($mobileno,$sms_message,$template_id);
                    return 4;
                }

            }else{

                // this echo statment commented because this messages shows on usersController side (by pravin 27/05/2017)
                //echo "<script> alert('This username does not exist')</script>";
                return 2;
            }
        }
        else
        {
            // this echo statment commented because this messages shows on usersController side (by pravin 27/05/2017)
            //echo "<script> alert('Wrong Captcha Code Entered')</script>";
            return 3;
        }

    }

/*----------------------------------------------------------------/User Login Library function ends/---------------------------------------------------------------------------------*/


/***************************************************************************************************************************************************************************************************/
   
    //FOR SEND SMS
    public function sendSms($mobileno,$sms_message,$template_id){

        if(!empty($mobileno))
        {/*
            $Dmi_sent_sms_log = ClassRegistry::init('Dmi_sent_sms_log');

            //code to send sms starts here
            //echo "sendsms.php";
            // Initialize the sender variable
            $sender=urlencode("AGMARK");
            //$uname=urlencode("aqcms.sms");
            $uname="aqcms.sms";
            //$pass=urlencode("Y&nF4b#7q");
            $pass="Y%26nF4b%237q";
            $send=urlencode("AGMARK");
            $dest='91'.base64_decode($mobileno);
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
            $message_id = null;
            $MID = null;
            $sent_date = date('Y-m-d H:i:s');

            // if message lenght is greater than 160 character then add one more parameter "concat=1" (Done by pravin 07-03-2018)
            if(strlen($msg) <= 160 ){

                curl_setopt($ch, CURLOPT_POSTFIELDS,"username=$uname&pin=$pass&signature=$send&mnumber=$dest&message=$msg&dlt_entity_id=$entity_id&dlt_template_id=$template_id");

            }else{

                curl_setopt($ch, CURLOPT_POSTFIELDS,"username=$uname&pin=$pass&signature=$send&mnumber=$dest&message=$msg&concat=1&dlt_entity_id=$entity_id&dlt_template_id=$template_id");
            }

            // Set URL return value to True to return the transfer as a string
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // The URL session is executed and passed to the browser
            $curl_output =curl_exec($ch);
            //echo $curl_output;

            //code to send sms ends here
            //query to save SMS sending logs in DB // added on 11-10-2017
            $Dmi_sent_sms_log->save_sms_log($message_id, $mobileno, $MID, $sent_date, $msg);
            */
        }
    }


/***************************************************************************************************************************************************************************************************/

    // Created new function for valided multiple browser login, Done by Pravin Bhakare , 12-11-2020
    public function browserLoginStatus($username,$curr_loggedin) {

        $DmiLoginStatus = TableRegistry::getTableLocator()->get('DmiLoginStatuses');

        $countspecialchar = substr_count($username ,"/");

        if($countspecialchar == 1){ $userType = 'dp'; }
        if($countspecialchar == 3){ $userType = 'df'; }
        if($countspecialchar == 0){ $userType = 'du'; }

        $current_ip = $this->getController()->getRequest()->clientIp();
        
        if($current_ip == '::1') {

            $current_ip = '127.0.0.1';
        }

        $loginStatusCreated = date('Y-m-d H:i:s');
        $currLoggedin = $DmiLoginStatus->find('all',array('conditions'=>array('user_id IS'=>$username,'user_type IS'=>$userType),'order'=>'id'))->first();
        
        $loginStatusId = '';    
        if (!empty($currLoggedin)) {

            $loginStatusId = $currLoggedin['id'];
            $loginStatusCreated =  $currLoggedin['created'];
        }

        $sessionid = md5(rand());

        $newEntity =   $DmiLoginStatus->newEntity(array(
                'id'=>$loginStatusId,
                'user_id'=>$username,
                'user_type'=>$userType,
                'curr_loggedin'=>$curr_loggedin,
                'ipaddress'=>$current_ip,
                'sessionid'=>$sessionid,
                'created'=>$loginStatusCreated,
                'modified'=>date('Y-m-d H:i:s')
            ));


            $DmiLoginStatus->save($newEntity);

            $_SESSION['browser_session_d'] = $sessionid;
    }

/***************************************************************************************************************************************************************************************************/
      
    //Make an user action entry in user action log table, Done by pravin bhakare, 11-02-2021 // Added from DMI AUDIT on 27-04-2021 by Akash.
    public function userActionPerformLog($userAction,$status){

        $username = $this->Session->read('customer_id');
        $user_id = $this->Session->read('username');
        $DmiUserActionLogs = TableRegistry::getTableLocator()->get('DmiUserActionLogs');

        $current_ip = $_SERVER['REMOTE_ADDR'];

        if($current_ip == '::1'){ $current_ip = '127.0.0.1'; }

        $user_id = $_SESSION['username'];

        $DmiUserActionLog = $DmiUserActionLogs->newEntity(['customer_id'=>$username,
                            'user_id'=>$user_id,
                            'action_perform'=>$userAction,
                            'ipaddress'=>$current_ip,
                            'status'=>$status,
                            'created'=>date('Y-m-d H:i:s')]);
                            
        $DmiUserActionLogs->save($DmiUserActionLog);


    }




    //created/updated/added on 25-06-2021 for multiple logged in check security updates, by Amol
    //this function contains the login logic for Authorized user
    public function userProceedLogin($username,$table){

        
        $DmiUserLogs = TableRegistry::getTableLocator()->get('DmiUserLogs');
        $DmiUserRoles = TableRegistry::getTableLocator()->get('DmiUserRoles');
        $DmiRoOffices = TableRegistry::getTableLocator()->get('DmiRoOffices');

        $this->Controller->getRequest()->getSession()->destroy();// destroy old Session data
        Session_start();

        // Update status of browser login history, Done By Pravin Bhakare 12-11-2020
        $this->browserLoginStatus($username,'yes');

        $current_ip = $this->Controller->getRequest()->clientIp();

        if($current_ip == '::1')
        {
            $current_ip = '127.0.0.1';
        }

        $DmiUserLog = $DmiUserLogs->newEntity(
            ['email_id'=>$username,
             'ip_address'=>$current_ip,
             'date'=>date('Y-m-d'),
             'time_in'=>date('H:i:s'),
             'remark'=>'Success']
        );

        $DmiUserLogs->save($DmiUserLog);

        $user_data_query = $table->find('all', array('conditions'=> array('email IS' => $username)))->first();
        $f_name = $user_data_query['f_name'];
        $l_name = $user_data_query['l_name'];

        //taking aadhar no. as default '000000000000', now no provosion to store aadhar no.
        //updated on 15-06-2018 by Amol
        $once_card_no = '000000000000';//$user_data_query[$table]['once_card_no'];
        $division = $user_data_query['division'];
        $user_code = $user_data_query['id'];
        $role=$user_data_query['role'];
        $posted_ro_office=$user_data_query['posted_ro_office'];

        $user_flag = $DmiUserRoles->find('all', array('fields'=>'user_flag','conditions'=> array('user_email_id IS' => $username)))->first();

        $location = $DmiRoOffices->find('all', array('conditions'=> array('id IS' => $posted_ro_office)))->first();

        //below if-else condition added on 21-05-2019 by Amol
        if(!empty($location)){
            $this->Controller->set('location',$location);
            $ro_office=$location['ro_office'];
        }else{
            $ro_office = 'Unknown';
        }

        // taking user data in Session variables
        $this->Session->write('userloggedin','yes');
        $this->Session->write('username',$username);
        $this->Session->write('once_card_no',$once_card_no);
        $this->Session->write('last_login_time_value',time()); // Store the "login time" into Session for checking user activity time (Done by pravin 24/4/2018)
        $this->Session->write('division',$division);
        $this->Session->write('f_name',$f_name);
        $this->Session->write('l_name',$l_name);
        $this->Session->write('ip_address',$current_ip);

        $this->Session->write('user_flag',$user_flag['user_flag']);
        $this->Session->write('user_code',$user_code);
        $this->Session->write('role',$role);
        $this->Session->write('posted_ro_office',$posted_ro_office);
        $this->Session->write('ro_office',$ro_office);
        $this->Session->write('profile_pic',$user_data_query['profile_pic']); //added on 06-05-2021 for profile pic

        //print_r($this->Session->read('division'));exit();

        $userrolequery = $table->find('all', array('fields'=>'role','conditions'=> array('email IS' => $username)))->first();

        $userrole = $userrolequery['role'];

        if($this->Session->read('division') == 'LMIS' || $this->Session->read('division') == 'BOTH'){
            $this->Controller->redirect('/dashboard/home');
        }

    }




    //this function is created from the function created in customerscontroller "already_logged_in()"
    //now the call from customercontroller through ajax call is depricated, as need to check after matching user details
    //so now calling in login library functions after password and user matched
    //on 25-06-2021 by Amol
    public function alreadyLoggedInCheck($userID){
        
       
        $DmiLoginStatus = TableRegistry::getTableLocator()->get('DmiLoginStatuses');
        $result = null;        
        $countspecialchar = substr_count($userID ,"/");
        
        if($countspecialchar == 1){ $userType = 'dp'; }
        if($countspecialchar == 3){ $userType = 'df'; }
        if($countspecialchar == 0){ $userType = 'du'; }
            
            $currLoggedin = $DmiLoginStatus->find('all',array('fields'=>array('curr_loggedin'),'conditions'=>array('user_id'=>$userID,'user_type'=>$userType),'order'=>'id'))->first();
            
            if(!empty($currLoggedin)){
                $currLoggedinRes = $currLoggedin['curr_loggedin'];
                if($currLoggedinRes == 'yes'){
                    $result = 'yes';
                }else{
                    $result = 'norecord';
                }
            }else{
                $result = 'norecord';
            }

        return  $result;        
        
    }

}


?>
