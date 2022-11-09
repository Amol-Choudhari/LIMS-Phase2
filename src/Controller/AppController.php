<?php

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\Routing\Router;
use TCPDF;
use Cake\Cache\Cache;
use Cake\Http\ServerRequest;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize(): void {

        parent::initialize();

		if(!isset($_SESSION)){
    	  session_start();
		}

	
        $this->loadComponent('RequestHandler', ['enableBeforeRedirect' => false,]);

		//Load Component
		$this->loadComponent('Flash');
		$this->loadComponent('Beforepageload');
		$this->loadComponent('Createcaptcha');
		$this->loadComponent('Customfunctions');
		$this->loadComponent('Authentication');

		//Load Model
		$this->loadModel('LimsUserActionLogs');
		$this->loadModel('DmiSmsEmailTemplates');

        $this->Session = $this->request->getSession();


    }

	//This function is used to disable Cache from browser, No history will be saved on browser
	public function beforeRender($event){
		Cache::disable();
	}

	public function beforeFilter(EventInterface $event){
		parent::beforeFilter($event);
		
		//this redirection added on 02-09-2022 by Amol intentionally
		if ($this->getRequest()->getAttribute("here")=='/LIMS/') {
			$this->redirect('/users/login_user');
		}

    	//Changes done by Shweta Apale 21-10-2021
	    define('reporticoReport', $_SERVER['DOCUMENT_ROOT']."/LIMS/vendor/reportico");
	    define("ForReportsUserName", "postgres");
		define("ForReportsPassword", "123");
		define("ForReportsConnection", "10.158.81.41");
		define("ForReportsDatabaseInterfade", "PostgreSQL");
		define('ForReportsDB','testmigration');

		//below headers are set for "Content-Security-Policy", to allow inline scripts from same origin and report the outer origin scripts calls.
		//the "Content-Security-Policy" header is commmented from httpd.conf file now and set here.
		//26-10-2021 by Amol
		//header("Report-To {'group':'default','max_age':31536000,'endpoints':[{'url':'https://10.158.81.41/LIMS4.2/users/csp_report'}]}");
		//header("Content-Security-Policy-Report-Only: script-src 'self'; report-to default; report-uri https://10.158.81.41/LIMS4.2/users/csp_report");

		if($this->getRequest()->getSession()->check('username')){
			//do nothing
		}else{

			Router::url('/');
			//$this->redirect('/');
		}

		//For the Payment Section - Akash - 24-06-2022
		$this->Session->write('is_payment_applicable','no');
		$this->Beforepageload->setLogoutTime();
		$this->Beforepageload->getFooterContent();
		$this->Beforepageload->homePageContent();
		$this->Beforepageload->checkValidRequest();
		$this->Beforepageload->currentSessionStatus();

		$this->loadModel('DmiUserRoles');
		$this->loadModel('DmiUsers');
		// Check assigned roles for logged in user
		$username = $this->getRequest()->getSession()->read('username');
		$current_user_roles = $this->DmiUserRoles->find('all',array('conditions'=>array('user_email_id IS'=>$username)))->first();
		$this->set('current_user_roles',$current_user_roles);

		//check user division to show LMIS login link on dashboard
		$current_user_division = $this->DmiUsers->find('all',array('conditions'=>array('email IS'=>$username)))->first();
		$this->set('current_user_division',$current_user_division);

		$user_last_login = $this->Customfunctions->userLastLogins();
		$this->set('user_last_login',$user_last_login);



	}



	//This function is use to generate all other normal pdfs in the system, without esign
	public function callTcpdf($html,$mode,$file_name=null){

		//generatin pdf starts here
		//create new pdf using tcpdf
		require_once(ROOT . DS .'vendor' . DS . 'tcpdf' . DS . 'tcpdf.php');
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

			//$pdf->SetFooterMargin(5);

			$pdf->AddPage();

			$pdf->writeHTML($html, true, false, true, false, '');

			//start to add bg image for the 'esigned by' cell on document
			// get the current page break margin
			$bMargin = $pdf->getBreakMargin();
			// get current auto-page-break mode
			$auto_page_break = $pdf->getAutoPageBreak();
			// restore auto-page-break status
			$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
			// set the starting point for the page content
			$pdf->setPageMark();
			//end to add bg image on cell

			// reset pointer to the last page
			$pdf->lastPage();

			// Clean any content of the output buffer
			if(ob_get_length() > 0) {
				ob_end_clean();
			}

			if($file_name == null){
				$file_name = '';
			}
			$file_path = $_SERVER["DOCUMENT_ROOT"].'writereaddata/LIMS/reports/'.$file_name;

			//Close and output PDF document
			$pdf->my_output($file_path, $mode);
			//generatin pdf ends here
	}


	//this function is created to generate pdf with empty signature content space. for esign
	public function EsigncallTcpdf($html,$mode,$file_name=null){

		//generatin pdf starts here
		//create new pdf using tcpdf
		require_once(ROOT . DS .'vendor' . DS . 'tcpdf' . DS . 'tcpdf.php');
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

			$pdf->SetFooterMargin(5);

			//to set signature content block in pdf
			$info = array();
			$pdf->my_set_sign('', '', '', '', 2, $info);

			$pdf->AddPage();

			$pdf->writeHTML($html, true, false, true, false, '');

			//start to add bg image for the 'esigned by' cell on document
			// get the current page break margin
			$bMargin = $pdf->getBreakMargin();
			// get current auto-page-break mode
			$auto_page_break = $pdf->getAutoPageBreak();
			// restore auto-page-break status
			$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
			// set the starting point for the page content
			$pdf->setPageMark();
			//end to add bg image on cell

			//sig appearence will only for F mode when save and store file
			if($mode=='F'){
				$esigner = $this->Session->read('f_name').' '.$this->Session->read('l_name');

				// set bacground image on cell
				//to show esigned by block on pdf
				$img_file = 'img/checked.png';
				$pdf->Image($img_file, 165, 266, 8, 8, '', '', '', false, 300, '', false, false, 0);

				$pdf->SetFont('times', '', 8);
				$pdf->setCellPaddings(1, 2, 1, 1);
				$pdf->MultiCell(40, 10, 'Esigned by: '.$esigner."\n".'Date: '.$_SESSION['sign_timestamp'], 1, '', 0, 1, 150, 265, true);

				// define active area for signature appearance
				$pdf->setSignatureAppearance(150, 265, 40, 10);

				// reset pointer to the last page
				$pdf->lastPage();
			}

			// Clean any content of the output buffer
			if(ob_get_length() > 0) {
				ob_end_clean();
			}

			if($file_name == null){
				$file_name = '';
			}
			$file_path = $_SERVER["DOCUMENT_ROOT"].'writereaddata/LIMS/reports/'.$file_name;

			//Close and output PDF document
			$pdf->my_output($file_path, $mode);
			//generatin pdf ends here
	}

	
	//to check failed attempts of user and show remaining attempts on each failed attempt to lock account
	//on 08-04-2021 by Amol
	public function showRemainingLoginAttempts($table,$user_id){

		$this->loadModel($table);
		//check in DB logs table
		if($table == 'DmiUserLogs'){

			$get_logs_records = $this->$table->find('all',array('conditions'=>array('email_id IS'=>$user_id),'order'=>'id Desc'))->toArray();

		}elseif($table == 'DmiCustomerLogs'){

			$get_logs_records = $this->$table->find('all',array('conditions'=>array('customer_id IS'=>$user_id),'order'=>'id Desc'))->toArray();
		}

		$i = 0;
		foreach($get_logs_records as $each){

			$each_log_details = $this->$table->find('all',array('conditions'=>array('id IS'=>$each['id'])))->first();
			$remark[$i] = $each_log_details['remark'];
			$date[$i] = $each_log_details['date'];

			$i = $i+1;
		}

		$current_date = strtotime(date('d-m-Y'));


		$j = 0;
		$failed_count = 0;
		while($j <= 2) {

			if(!empty($remark[$j])){

				if($remark[$j] == 'Failed'){

					$failed_count = $failed_count+1;
				}
			}

			$j = $j+1;
		}

		if($failed_count == 1){
			return 'Please note: You have 2 more attempts to login';

		}elseif($failed_count == 2){
			return 'Please note: You have 1 more attempt to login';

		}elseif($failed_count == 3){
			return 'Sorry... Your account is disabled for today, on account of 3 login failure.';
		}


	}



	//created/updated/added on 25-06-2021 for multiple logged in check security updates, by Amol
	//this function is called from element "already_loggedin_msg", if applicant/user proceeds.
	//common for Applicant/user side
	public function proceedEvenMultipleLogin(){
		//$this->autoRender = 'false';
		$username = $this->Session->read('username');
		$countspecialchar = substr_count($username ,"/");
		$table = TableRegistry::getTableLocator()->get('DmiUsers');
		$this->Authentication->userProceedLogin($username,$table);

		
	}
	
	// Custom common alert page
	// Aniket G [14-10-2022][C]
    public function customAlertPage($msg = null) {

        $this->Session->destroy();
        $homeUrl = Router::url(['controller'=>'users','action'=>'login_user']);
        $msg_txt = ($msg == null) ? "Sorry something wrong happened !! " : $msg;
        $msg_icon = (in_array($msg_txt, array('Your session is expired due to inactivity','Your session has timed out due to inactivity'))) ? 'clock' : 'exclamation-circle';
        $msg_title = (in_array($msg_txt, array('Your session is expired due to inactivity','Your session has timed out due to inactivity'))) ? 'Session Expired' : 'Alert';

        $msg_content = '
			<html lang="en"><head>
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta http-equiv="Content-Language" content="en">
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<title>'.$msg_txt.'</title>
			<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
			<meta name="description" content="'.$msg_txt.'">
			<meta name="msapplication-tap-highlight" content="no">
			<link href="'.$homeUrl.'/favicon.ico" type="image/x-icon" rel="icon"><link href="'.$homeUrl.'/favicon.ico" type="image/x-icon" rel="shortcut icon"><meta charset="utf-8"><link rel="stylesheet" href="'.$homeUrl.'/css/adminlte.min.css"><link rel="stylesheet" href="'.$homeUrl.'/css/all.min.css"><style type="text/css">/* Chart.js */
			@-webkit-keyframes chartjs-render-animation{from{opacity:0.99}to{opacity:1}}@keyframes chartjs-render-animation{from{opacity:0.99}to{opacity:1}}.chartjs-render-monitor{-webkit-animation:chartjs-render-animation 0.001s;animation:chartjs-render-animation 0.001s;}.error_div{background:#dccac8;height:100vh;display:flex;align-items:center;font-family:system-ui;}.card-header{text-transform:initial;background:#eb5d57;color:white;font-size:17px;}#error_icon{font-size:32px;color:#eb5d57;}.btn_continue{background:#eb5d57;}.font_gainsboro{color:#5c5c5c;}</style></head>
			<body>
				<link rel="stylesheet" href="'.$homeUrl.'/css/element/session_expired.css"><div class="container-fluid error_div">
				<div class="card col-md-4 mx-auto p-0">
					<div class="card-header">
						'.$msg_title.'
					</div>
					<div class="card-body">
						<table class="table table-borderless font-weight-bold font_gainsboro">
							<tbody><tr>
								<td rowspan="2" class="align-top"><i class="fa fa-'.$msg_icon.'" id="error_icon"></i></td>
								<td>'.$msg_txt.'</td>
							</tr>
							<tr>
								<td>Click "Continue" to redirect to the Homepage.</td>
							</tr>
						</tbody></table>
						<a href="/DMI" class="btn btn_continue float-right text-white font-weight-bold">CONTINUE</a></div>
					</div>
				</div>
			</body>
			</html>';

		echo $msg_content;
        exit;
	}

}
