<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\Network\Session\DatabaseSession;
use App\Network\Email\Email;
use App\Network\Request\Request;
use App\Network\Response\Response;
use Cake\Datasource\ConnectionManager;

class DashboardController extends AppController{
		
		var $name = 'Dashboard';	

		public function beforeFilter($event) {
		parent::beforeFilter($event);	

					$this->viewBuilder()->setLayout('admin_dashboard');
					$this->viewBuilder()->setHelpers(['Form','Html']);
					$this->loadComponent('Customfunctions');
					$this->loadComponent('Ilc');
			
			$username = $this->getRequest()->getSession()->read('username');
			
			
			if ($username == null) {						
				echo "Sorry You are not authorized to view this page.."; ?><a href="<?php echo $this->request->getAttribute('webroot');?>users/login_user">Please Login</a><?php
				exit();					
			} else {

				$this->loadModel('DmiUsers');
				//check if user entry in Dmi_users table for valid user
				$check_user = $this->DmiUsers->find('all',array('conditions'=>array('email'=>$this->Session->read('username'))))->first();
				
				if (empty($check_user)) {

					echo "Sorry You are not authorized to view this page.."; ?><a href="<?php echo $this->request->getAttribute('webroot');?>users/login_user">Please Login</a><?php
					exit();
				}				
			}	
		}
		
	//phase 2 new code from here				
							
		public function home(){
			
			$this->viewBuilder()->setLayout('admin_dashboard');			
			$this->loadModel('DmiUserRoles');					

			$username = $this->getRequest()->getSession()->read('username');
			$this->set('username',$username);
					
			$type = 1;

			$check_user_role = $this->DmiUserRoles->find('all',array('conditions'=>array('user_email_id IS'=>$username)))->first();
			$this->set('check_user_role',$check_user_role);			

			if (null !==($this->request->getData('search_sample'))) {

				$sample_code = $this->request->getData('search_code'); 
				
				$this->dashboardSampleSearch($sample_code);
			}
			
			//added on 08-12-2021 by Amol, separate option to get pending work status
			$main_count_array=array();
			if (null !==($this->request->getData('get_pending_work'))) {

				//to show current user pending work statistic in popup window on home page
				$main_count_array = $this->dashboardpendingWorkCount($check_user_role);
				$this->set('main_count_array',$main_count_array);
			}
		
			//condition added on 08-12-2021 by Amol
			if(empty($main_count_array)){
				//to show lab status on dashboard
				$lab_status_count = $this->dashboardLabStatus();
				$this->set('lab_status_count',$lab_status_count);

				//to show user status on dashboard
				$user_status_count = $this->dashboardUserStatus();
			//removed pending samples counts, now showing allocated for test samples, as separate button for pending status now
			//on 08-12-2021 by Amol
			//	$user_status_count['pending_samples'] = array_sum($main_count_array);
				$this->set('user_status_count',$user_status_count);
				
				//to show recent activities
				$recent_activities = $this->getUserRecentActivities();
				$this->set('recent_activities',$recent_activities);
			}
		}
		
		
	//to get over all pending work count for user on dashboard
	public function dashboardpendingWorkCount($check_user_role){
			  
		$main_tab_count = array();
		$main_tab_count['saved_samples'] = 0;
		$main_tab_count['samples_to_accept'] = 0;
		$main_tab_count['samples_to_forward'] = 0;
		$main_tab_count['samples_to_allocate'] = 0;
		$main_tab_count['forward_to_lab_incharge'] = 0;
		$main_tab_count['returned_by_chemist'] = 0;
		$main_tab_count['samples_to_allocate_retest'] = 0;
		$main_tab_count['forward_to_lab_incharge_retest'] = 0;
		$main_tab_count['returned_by_chemist_retest'] = 0;
		$main_tab_count['to_accept_by_chemist'] = 0;
		$main_tab_count['enter_reading_by_chemist'] = 0;
		$main_tab_count['to_approve_readings'] = 0;
		$main_tab_count['to_grade_by_inward'] = 0;
		$main_tab_count['to_grade_by_oic'] = 0;
		  
		$current_level_arr = array();
		$username = $this->Session->read('username');		
		
		if (!empty($check_user_role)) {
			
			if ($check_user_role['sample_inward'] == 'yes') {
				
				//saved but not confirmed, or halfly saved
				$main_tab_count['saved_samples'] = count((new InwardController())->getSavedSamplesList());
				
				if ($_SESSION['role']=="Inward Officer") {
					//to be accepted by Inward Officer
					$main_tab_count['samples_to_accept'] = count((new SampleAcceptController())->getSampleListToAccept());
				}
			}
			
			if ($check_user_role['sample_forward'] == 'yes') {
				//to be forward to Inward officer
				$main_tab_count['samples_to_forward'] = count((new SampleForwardController())->getSampleListToForward());
			}
			
			if ($check_user_role['sample_allocated'] == 'yes') {
				
				//first allocation
				$main_tab_count['samples_to_allocate'] = count((new SampleAllocateController())->getSampleToAllocate());
				$main_tab_count['forward_to_lab_incharge'] = count((new SampleAllocateController())->getSampleToForwardInchrg());
				$main_tab_count['returned_by_chemist'] = count((new SampleAllocateController())->getSampleReturnedByChemist());
				
				//retest allocation
				$main_tab_count['samples_to_allocate_retest'] = count((new SampleAllocateController())->getSampleToAllocateRetest());
				$main_tab_count['forward_to_lab_incharge_retest'] = count((new SampleAllocateController())->getSampleToForwardInchrgRetest());
				$main_tab_count['returned_by_chemist_retest'] = count((new SampleAllocateController())->getSampleReturnedByChemistRetest());
				
			}
			
			if ($check_user_role['sample_testing_progress'] == 'yes') {
				
				//to be accepted by chemist
				$main_tab_count['to_accept_by_chemist'] = count((new TestController())->getSampleToAccept());
				
				//to enter reading by chemist
				$main_tab_count['enter_reading_by_chemist'] = count((new TestController())->getSampleToEnterReading());
				
			}
			
			if ($check_user_role['sample_result_approval'] == 'yes') {
				
				//to approve readings
				$main_tab_count['to_approve_readings'] = count((new ApproveReadingController())->getSampleToApproveReading());
				
			}
			
			if ($check_user_role['finalized_sample'] == 'yes') {
				
				if ($_SESSION['role']=="Inward Officer") {
					//to grade by Inward
					$main_tab_count['to_grade_by_inward'] = count((new FinalGradingController())->getSampleToGradeByInward());

				} else {  
					//to grade by OIC
					$main_tab_count['to_grade_by_oic'] = count((new FinalGradingController())->getSampleToGradeByOic());
					
				}
				
			}
			
			if ($check_user_role['reports'] == 'yes') {
			
			}

		}
		return $main_tab_count;

    }
	
	
	
	//to get the current user lab RAL/CAL status on dashboard
	public function dashboardLabStatus(){
		
		$lab_status_count = array();
		$lab_status_count['total_registered'] = 0;
		$lab_status_count['total_allocated'] = 0;
		$lab_status_count['results_approved'] = 0;
		$lab_status_count['report_finalized'] = 0;
		$lab_status_count['office_type'] = '';
		$username = $this->Session->read('username');
		
		//get current user lab id
		$this->loadModel('DmiUsers');
		$user_details = $this->DmiUsers->find('all',array('fields'=>'posted_ro_office','conditions'=>array('email'=>$username)))->first();
		$lab_id = $user_details['posted_ro_office'];
		
		//get office type
		$this->loadModel('DmiRoOffices');
		$get_office_type = $this->DmiRoOffices->find('all',array('fields'=>'office_type','conditions'=>array('id'=>$lab_id)))->first();
		$office_type = $get_office_type['office_type'];
		
		if ($office_type == 'RAL' || $office_type == 'CAL') {
		
			$this->loadModel('Workflow');
			//get total samples registered sample in the lab
			$get_total_registered = $this->Workflow->find('all',array('fields'=>'org_sample_code','conditions'=>array('OR'=>array('src_loc_id IS'=>$lab_id,'dst_loc_id IS'=>$lab_id),'stage_smpl_flag IN'=>array('SD','OF','LI')),'group'=>'org_sample_code'))->toArray();
			$lab_status_count['total_registered'] = count($get_total_registered);
			
			//get total samples allocated for test in the lab
			$get_total_allocated = $this->Workflow->find('all',array('fields'=>'org_sample_code','conditions'=>array('dst_loc_id IS'=>$lab_id,'stage_smpl_flag'=>'TA'),'group'=>'org_sample_code'))->toArray();
			$lab_status_count['total_allocated'] = count($get_total_allocated);
			
			//get total samples with approved results in the lab
			$get_approved_results = $this->Workflow->find('all',array('fields'=>'org_sample_code','conditions'=>array('dst_loc_id IS'=>$lab_id,'stage_smpl_flag'=>'AR'),'group'=>'org_sample_code'))->toArray();
			$lab_status_count['results_approved'] = count($get_approved_results);
			
			//get total samples with approved results in the lab
			$get_finalized_reports = $this->Workflow->find('all',array('fields'=>'org_sample_code','conditions'=>array('src_loc_id IS'=>$lab_id,'stage_smpl_flag'=>'FG'),'group'=>'org_sample_code'))->toArray();
			$lab_status_count['report_finalized'] = count($get_finalized_reports);
		
		} elseif($office_type == 'RO' || $office_type == 'SO') {
			
			$this->loadModel('Workflow');
			//get total samples registered sample in the lab
			$get_total_registered = $this->Workflow->find('all',array('fields'=>'org_sample_code','conditions'=>array('OR'=>array('src_loc_id IS'=>$lab_id,'dst_loc_id IS'=>$lab_id),'stage_smpl_flag IN'=>array('SD','OF','LI')),'group'=>'org_sample_code'))->toArray();
			$lab_status_count['total_registered'] = count($get_total_registered);
			
			
			foreach ($get_total_registered as $eachSample) {
				
				//get total samples allocated for test in the lab
				$get_total_allocated = $this->Workflow->find('all',array('fields'=>'org_sample_code','conditions'=>array('org_sample_code IS'=>$eachSample['org_sample_code'],'stage_smpl_flag'=>'TA'),'group'=>'org_sample_code'))->first();
				if( !empty($get_total_allocated)) {
					$lab_status_count['total_allocated'] = $lab_status_count['total_allocated']+1;
				}
				
				//get total samples with approved results in the lab
				$get_approved_results = $this->Workflow->find('all',array('fields'=>'org_sample_code','conditions'=>array('org_sample_code IS'=>$eachSample['org_sample_code'],'stage_smpl_flag'=>'AR'),'group'=>'org_sample_code'))->first();
				if (!empty($get_approved_results)) {
					$lab_status_count['results_approved'] = $lab_status_count['results_approved']+1;
				}
				
				//get total samples with approved results in the lab
				$get_finalized_reports = $this->Workflow->find('all',array('fields'=>'org_sample_code','conditions'=>array('org_sample_code IS'=>$eachSample['org_sample_code'],'stage_smpl_flag'=>'FG'),'group'=>'org_sample_code'))->first();
				if (!empty($get_finalized_reports)) {
					$lab_status_count['report_finalized'] = $lab_status_count['report_finalized']+1;
				}
				
			}

		}
		
		$lab_status_count['office_type'] = $office_type;
		
		return $lab_status_count;
	}
	
	
	//to get the current user status on dashboard
	public function dashboardUserStatus(){
		
		$user_status_count = array();
		$user_status_count['overall_samples'] = 0;
		$user_status_count['pending_samples'] = 0;
		$user_status_count['processed_samples'] = 0;
		$user_status_count['report_finalized'] = 0;
		$username = $this->Session->read('username');
		
		//get current user table id
		$this->loadModel('DmiUsers');
		$user_details = $this->DmiUsers->find('all',array('fields'=>'id','conditions'=>array('email'=>$username)))->first();
		$user_id = $user_details['id'];
		
		$this->loadModel('Workflow');
		//get over all samples handled by user
		$get_over_samples = $this->Workflow->find('all',array('fields'=>'org_sample_code','conditions'=>array('OR'=>array('src_usr_cd IS'=>$user_id,'dst_usr_cd IS'=>$user_id)),'group'=>'org_sample_code'))->toArray();
		$user_status_count['overall_samples'] = count($get_over_samples);
		
		//below logic updated on 08-12-2021 by Amol
		//removed pending samples, as separate button is for it on top, and added allocated for test samples count on the place
		$i=0;
		$sample_list = array('0'=>'1');//default temp value
		foreach($get_over_samples as $smplCd){
			
			$sample_list[$i]=$smplCd['org_sample_code'];
			$i=$i+1;	
		}
		
		//get total samples processed by the user
		$get_processed_samples = $this->Workflow->find('all',array('fields'=>'org_sample_code','conditions'=>array('org_sample_code IN'=>$sample_list,'src_usr_cd IS'=>$user_id,'dst_usr_cd IS NOT'=>$user_id),'group'=>'org_sample_code'))->toArray();
		$user_status_count['processed_samples'] = count($get_processed_samples);
		
		//get total samples allocated for test in the lab
		$get_total_allocated = $this->Workflow->find('all',array('fields'=>'org_sample_code','conditions'=>array('org_sample_code IN'=>$sample_list,'stage_smpl_flag'=>'TA'),'group'=>'org_sample_code'))->toArray();
		$user_status_count['total_allocated'] = count($get_total_allocated);
		
		//get total samples reports finalized, in which current user involved
		foreach($get_processed_samples as $eachSample){
			//check state for FG
			$check_if_finalized = $this->Workflow->find('all',array('fields'=>'org_sample_code','conditions'=>array('org_sample_code IS'=>$eachSample['org_sample_code'],'stage_smpl_flag'=>'FG')))->first();
			if(!empty($check_if_finalized)){
				
				$user_status_count['report_finalized'] = $user_status_count['report_finalized']+1;
			}
		}
				
		return $user_status_count;
	}
	
	
	//get user recent activities on dashboard
	public function getUserRecentActivities(){
		
		$username = $this->Session->read('username');
		//get current user table id
		$this->loadModel('DmiUsers');
		$user_details = $this->DmiUsers->find('all',array('fields'=>'id','conditions'=>array('email IS'=>$username)))->first();
		$user_id = $user_details['id'];
		
		//get recent activities
		$this->loadModel('Workflow');
		$getActivities = $this->Workflow->find('all',array('fields'=>array('org_sample_code','id'),'conditions'=>array('src_usr_cd IS'=>$user_id),'limit'=>'20','order'=>'id desc'))->toArray();
		
		$i=0;
		$getEachLsstRecord = array();
		$sample_code_arr = array();
		foreach($getActivities as $eachActivity){
			
			if(!in_array($eachActivity['org_sample_code'],$sample_code_arr) && $i<5){
				$getEachLsstRecord[$i] = $this->Workflow->find('all',array('conditions'=>array('src_usr_cd IS'=>$user_id,'org_sample_code IS'=>$eachActivity['org_sample_code']),'order'=>'id DESC'))->first();
				
				$sample_code_arr[$i]=$eachActivity['org_sample_code'];//once used, will not be taken again
			
				$i=$i+1;
			}
		}
		
		$result = array();
		$i=0;
		foreach($getEachLsstRecord as $eachSample){
			
			$result[$i]['sample_code'] = $eachSample['stage_smpl_cd'];
			
			//get stage sample flag wise activity details
			$result[$i]['activity'] = $this->getFlagWiseActivity(trim($eachSample['stage_smpl_flag']));
			$result[$i]['date'] = $eachSample['tran_date'];
			$i=$i+1;
		}
		
		return $result;
		
	}
	
	public function getFlagWiseActivity($stageSampleFlag){
		$activity = '';
		
		if($stageSampleFlag == 'SI'){
			$activity = 'Registered New Sample Inward';
		
		}elseif($stageSampleFlag == 'SD'){
			$activity = 'Saved New Sample Details';
		
		}elseif($stageSampleFlag == 'FT'){
			$activity = 'Finalized Test Results and Submitted';
		
		}elseif($stageSampleFlag == 'R'){
			$activity = 'Sample Marked for Retest';
		
		}elseif($stageSampleFlag == 'FR'){
			$activity = 'Sample Forwarded to RAL';
		
		}elseif($stageSampleFlag == 'AR'){
			$activity = 'Approved Sample Test Results';
		
		}elseif($stageSampleFlag == 'FO'){
			$activity = 'Sample Forwarded to OIC';
		
		}elseif($stageSampleFlag == 'FG'){
			$activity = 'Finalized Grading Report';
		
		}elseif($stageSampleFlag == 'TA'){
			$activity = 'Sample Allocated For Test';
		
		}elseif($stageSampleFlag == 'FS'){
			$activity = 'Sample Forward Back to RAL';
		
		}elseif($stageSampleFlag == 'FC'){
			$activity = 'Sample Forward Back to CAL';
		
		}elseif($stageSampleFlag == 'FGIO'){
			$activity = 'Applied Final Grades'; //By Inward Officer
		
		}elseif($stageSampleFlag == 'VC'){
			$activity = 'Verified Sample Test Results'; //By Inward Officer
		
		}elseif($stageSampleFlag == 'OF'){
			$activity = 'Forwarded to Inward Officer'; //by RO/SO OIC
		
		}elseif($stageSampleFlag == 'IF'){
			$activity = 'Forwarded to Other Inward Officer'; // by Inward Officer
		
		}elseif($stageSampleFlag == 'AS'){
			$activity = 'Accepted New Sample'; //By Inward Officer
		
		}elseif($stageSampleFlag == 'HF'){
			$activity = 'Forwarded to Inward Officer'; //by HO
		
		}elseif($stageSampleFlag == 'HS'){
			$activity = 'Accepted New Sample'; //by HO
		
		}elseif($stageSampleFlag == 'LI'){
			$activity = 'Sample Allocated For Test'; //by Lab Incharge
		
		}elseif($stageSampleFlag == 'RIF'){
			$activity = 'Forwarded Back For Retest';
		
		}elseif($stageSampleFlag == 'TABC'){
			$activity = 'Sample Accepted For Tests';
		
		}elseif($stageSampleFlag == 'VS'){
			$activity = 'Sample Verified';
		
		}
		
		return $activity;
	}
		
//phase 2 new code till above			


		// myTeam
		// Author : Akash Thakre
		// Description : This function is created to show the list of office users 
		// Date : 30-05-2022

        public function myTeam(){

            $username = $this->Session->read('username');
		
            $this->loadModel('DmiRoOffices');
			$this->loadModel('DmiUsers');
			$this->loadModel('DmiUserRoles');
			$this->loadModel('DmiApplWithRoMappings');
			$this->loadModel('DmiPaoDetails');
			$this->loadModel('DmiDistricts');

			
			if ($_SESSION['division'] == 'BOTH') {

				//get details
				$officeDetails = $this->DmiRoOffices->getOfficeDetails($username);
	
				$office_name = $officeDetails[0];
				$office_type = $officeDetails[1];
				
				//set pao
				$getPao = $this->DmiPaoDetails->getPaoDetailsForDmi($username);
				
				//Set HO Usersd
				$getHoUsers = $this->DmiUserRoles->getHORoles();

				$dy_ama = $getHoUsers['dy_ama'];
				$jt_ama = $getHoUsers['jt_ama'];
				$ama = $getHoUsers['ama'];
				
				//Full Name for Head Office Users
				$dy_ama_name = $this->DmiUsers->getFullName($dy_ama);
				$jt_ama_name = $this->DmiUsers->getFullName($jt_ama);
				$ama_name = $this->DmiUsers->getFullName($ama);
				
				//Get Scrutiny Officers
				$get_scrutinizers_list = $this->DmiRoOffices->getScrutinizerForCurrentOffice();
				$this->set('get_scrutinizers_list',$get_scrutinizers_list);

				// Get Inspection Officers
				$get_io_list = $this->DmiRoOffices->getIoForCurrentOffice();
				$this->set('get_io_list',$get_io_list);

				//Set HO MO SMO
				$ho_scrutinizers_list = $this->DmiUserRoles->getHoScrutinizerForCurrentOffice();
				$this->set('ho_scrutinizers_list',$ho_scrutinizers_list);

				if ($officeDetails[1] == 'SO') {
				
					$soInchargeEmail = $officeDetails[2];
					$soInchargeName = $this->DmiUsers->getFullName($soInchargeEmail);
					
					$roInchargeEmail = $this->DmiRoOffices->getRoOfficeEmail($officeDetails[3]);
					$roInchargeName = $this->DmiUsers->getFullName($roInchargeEmail);
					
				} else {
	
					$soInchargeEmail = '';
					$soInchargeName = '';
					$roInchargeEmail = $officeDetails[2];
					$roInchargeName = $this->DmiUsers->getFullName($roInchargeEmail);
	
				}

				//Set DDO
				$this->set('getPao',$getPao);

				//Set Office
				$this->set('office_name',$office_name);
				$this->set('office_type',$office_type);

				//Set RO Information
				$this->set('roInchargeEmail',$roInchargeEmail);
				$this->set('roInchargeName',$roInchargeName);

				//set SO Information
				$this->set('soInchargeEmail',$soInchargeEmail);
				$this->set('soInchargeName',$soInchargeName);

				$this->set(compact('dy_ama','jt_ama','ama'));
				$this->set(compact('dy_ama_name','jt_ama_name','ama_name'));
				
			} else {

				//get details 
				$officeDetails = $this->DmiRoOffices->getOfficeDetails($username);

				$office_name = $officeDetails[0];
				$office_type = $officeDetails[1];
				$officeIncharge =  $officeDetails[2];
				//Set Office
				$inchargeName = $this->DmiUsers->getFullName($officeIncharge);

				$getJrChemist = $this->DmiRoOffices->getJrChemist($username);
				$getSrChemist = $this->DmiRoOffices->getSrChemist($username);
				$getInward = $this->DmiRoOffices->getInward($username);
				$getLabIncharge = $this->DmiRoOffices->getLabIncharge(); 
				$postedRoOfficeId = $this->DmiUsers->getPostedOffId($username);
				$getDol = $this->DmiRoOffices->getDol();

				$this->set(compact('getJrChemist','getSrChemist','getInward','getLabIncharge','postedRoOfficeId','getDol'));
				$this->set(compact('office_name','office_type','officeIncharge','inchargeName'));
			}

			
			
		}
}
		
		
		
?>