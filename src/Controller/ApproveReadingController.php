<?php
namespace App\Controller;

use Cake\Event\Event;
use App\Network\Email\Email;
use Cake\ORM\Entity;
use Cake\Datasource\ConnectionManager;
use Cake\View;

class ApproveReadingController extends AppController{

	var $name = 'ApproveReading';
	public function beforeFilter($event) {
		parent::beforeFilter($event);

		$this->viewBuilder()->setLayout('admin_dashboard');
		$this->viewBuilder()->setHelpers(['Form','Html']);
		$this->loadComponent('Customfunctions');
	}

/****************************************************************************************************************************************************************************************************************************/

	//to validate login user
	public function authenticateUser() {

		$this->loadModel('DmiUserRoles');
		$user_access = $this->DmiUserRoles->find('all',array('conditions'=>array('user_email_id IS'=>$this->Session->read('username'))))->first();

		if (!empty($user_access)) {
			//proceed
		} else {
			$this->customAlertPage('Sorry You are not authorized to view this page..');
			exit;
		}
	}

/****************************************************************************************************************************************************************************************************************************/

	public function availableForApproveReading() {

		$this->authenticateUser();
		$result = $this->getSampleToApproveReading();
		$this->set('approve_reading_sample',$result);

	}


/****************************************************************************************************************************************************************************************************************************/

	//created common function to fetch list , to be used for dashboard counts also, on 28-04-2021 by Amol
	public function getSampleToApproveReading() {

		$conn = ConnectionManager::get('default');
		$alloc_user 	= $_SESSION["user_code"];


        $query = $conn->execute("SELECT c.sample_code
								 FROM workflow AS w
								 INNER JOIN code_decode AS c ON w.org_sample_code=c.org_sample_code
								 INNER JOIN m_sample_allocate AS sa ON w.org_sample_code=sa.org_sample_code
								 INNER JOIN sample_inward AS si ON w.org_sample_code=si.org_sample_code
								 WHERE c.status_flag='C' AND c.display='Y'
								 AND w.dst_usr_cd='".$_SESSION['user_code']."' AND w.stage_smpl_flag='FT' AND si.status_flag!='SR' GROUP BY c.sample_code");

		$sample_codes = $query->fetchAll('assoc');

		//to be used in below core query format, that's why
		$arr = "IN(";
		foreach($sample_codes as $each){
			$arr .= "'";
			$arr .= $each['sample_code'];
			$arr .= "',";
		}
		$arr .= "'00')";//00 is intensionally given to put last value in string.

		//Updated Below query by Akash 13/07/2021
		$query = $conn->execute("SELECT DISTINCT ON (w.stage_smpl_cd) si.inward_id, w.stage_smpl_cd, si.received_date, st.sample_type_desc, mcc.category_name, mc.commodity_name, ml.ro_office, w.modified AS submitted_on
								 FROM sample_inward AS si
								 INNER JOIN m_sample_type AS st ON si.sample_type_code=st.sample_type_code
								 INNER JOIN m_commodity_category AS mcc ON si.category_code=mcc.category_code
								 INNER JOIN dmi_ro_offices AS ml ON ml.id=si.loc_id
								 INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
								 INNER JOIN workflow AS w ON w.org_sample_code = si.org_sample_code
								 WHERE w.stage_smpl_cd ".$arr." order by w.stage_smpl_cd desc");

		$result = $query->fetchAll('assoc');

		return $result;
	}

/****************************************************************************************************************************************************************************************************************************/

	public function redirectToApproveReading($approve_reading_sample) {

		$this->Session->write('approve_reading_sample',$approve_reading_sample);
		$this->redirect(array('controller'=>'ApproveReading','action'=>'approve_reading'));
	}


/****************************************************************************************************************************************************************************************************************************/


	public function approveReading() {

		$this->authenticateUser();
		$this->viewBuilder()->setLayout('admin_dashboard');

		$str1 = "";
		$this->loadModel('MCommodityCategory');
		$this->loadModel('ActualTestData');
		$this->loadModel('FinalTestResult');
		$this->loadModel('CodeDecode');
		$this->loadModel('SampleInward');
		$this->loadModel('Workflow');
		$this->loadModel('MCommodity');
		$this->loadModel('MTest');
		$conn = ConnectionManager::get('default');

		$approve_reading_sample = $this->Session->read('approve_reading_sample');

		if (!empty($approve_reading_sample)) {

			$this->set('samples_list',array($approve_reading_sample=>$approve_reading_sample));
			$this->set('stage_sample_code',$approve_reading_sample);//for hidden field, to use common script

			if ($this->request->is('post')) {

					$postdata = $this->request->getData();

				if ($postdata['button']=='add') {

					$sample_code = $postdata['sample_code'];
					$tests = $postdata['test_code'];

					if (null!==($this->request->getData('duplicate_flg'))) {
						$postdata['duplicate_flg'] = "D";
					} else {
						$postdata['duplicate_flg'] = "N";
					}

					$cnt= $conn->execute("SELECT * FROM final_test_result WHERE sample_code='$sample_code' AND test_code='$tests' AND display='Y'");
					$cnt = $cnt->fetchAll('assoc');


					if ($postdata['duplicate_flg'] == "D") {

						if ( count($cnt)>0) {

							$this->FinalTestResult->updateAll(array('display' => 'Y'),array('sample_code' => $sample_code,'test_code'=>$tests));
						}

						$ogrsample1	= $this->Workflow->find('all', array('conditions'=> array('stage_smpl_cd IS' => $sample_code)))->first();
						$ogrsample	= $ogrsample1['org_sample_code'];

						$postdata['org_sample_code'] = $ogrsample;

						$FinalTestResultEntity = $this->FinalTestResult->newEntity($postdata);

						if (!$this->FinalTestResult->save($FinalTestResultEntity)) {

							echo "#[error]~Test Result Not Saved. Please try again-1#";

						} else {

							$test_name	= $conn->execute("SELECT DISTINCT test_name
														  FROM final_test_result AS f
														  INNER JOIN m_test AS t ON f.test_code=t.test_code
														  WHERE sample_code='$sample_code' AND f.test_code='$tests' AND f.display='Y'");

							$test_name = $test_name->fetchAll('assoc');

							$this->ActualTestData->updateAll(array('status_flag' => 'A'),array('sample_code'=>$sample_code,'test_code'=>$tests,'display'=>'Y'));

							echo  '#'.json_encode($test_name).'#';
						}

					} else {

						if (count($cnt)>0) {

							$this->FinalTestResult->updateAll(array('display' => 'N'),array('sample_code' => $sample_code,'test_code'=>$tests));
						}

						$ogrsample1	= $this->Workflow->find('all', array('conditions'=> array('stage_smpl_cd IS' => $sample_code)))->first();
						$ogrsample	= $ogrsample1['org_sample_code'];
						$postdata['org_sample_code']	= $ogrsample;

						$FinalTestResultEntity = $this->FinalTestResult->newEntity($postdata);

						if (!$this->FinalTestResult->save($FinalTestResultEntity)) {

							echo "#[error]~Test Result Not Saved. Please try again-3#";

						} else {

							$test_name	= $conn->execute("SELECT test_name
														  FROM final_test_result AS f
														  INNER JOIN m_test AS t ON f.test_code=t.test_code
														  WHERE sample_code='$sample_code' AND f.test_code='$tests' AND f.display='Y'");

							$test_name = $test_name->fetchAll('assoc');

							$this->ActualTestData->updateAll(array('status_flag' => 'A'),array('sample_code'=>$sample_code,'test_code'=>$tests,'display'=>'Y'));

							echo  '#'.json_encode($test_name).'#';
						}
					}
				}

				exit;

			}
		}

	}

/*******************************************************************************************************************************************************************************************************************************************/

	public function getFinaliseFlag() {	

		$sample_code	= trim($_POST['sample_code']);
		$conn = ConnectionManager::get('default');

		if (preg_match('/[^A-Za-z0-9]/', $sample_code)) {
			echo '#[error]~Invaild sample code!#';
			exit;
		}

		$res_duplicate = $conn->execute("SELECT DISTINCT result_dupl_flag
										 FROM sample_inward AS si
										 INNER JOIN actual_test_data AS a ON si.org_sample_code=a.org_Sample_code
										 WHERE a.sample_code='$sample_code'");

		$res_duplicate = $res_duplicate->fetchAll('assoc');


		if ($res_duplicate[0]['result_dupl_flag']=='D ') {

			$res = $conn->execute("SELECT t.test_code,t.test_name,b.test_type_name
								   FROM actual_test_data AS a
								   INNER JOIN m_test AS t ON a.test_code = t.test_code
								   INNER JOIN m_test_type AS b ON t.test_type_code = b.test_type_code
								   INNER JOIN m_sample_allocate AS s ON a.chemist_code=s.chemist_code
								   WHERE a.sample_code='$sample_code' AND a.display='Y' 
								   AND s.acptnce_flag!='NABC' AND a.status_flag NOT IN('A')
								   GROUP BY t.test_code,t.test_name,b.test_type_name");

			$res = $res->fetchAll('assoc');

			$abc = count($res);
			echo '#'.$abc.'#';

		} else {

			$res = $conn->execute("SELECT count(*)
								   FROM actual_test_data AS a
								   INNER JOIN m_sample_allocate AS sa ON sa.chemist_code=a.chemist_code 
								   AND a.sample_code='$sample_code' AND a.display='Y' 
								   AND a.status_flag NOT IN('A') AND sa.acptnce_flag!='NABC'");

			$res = $res->fetchAll('assoc');

			echo '#'.$res[0]['count'].'#';

		}
		exit;

	}

/*******************************************************************************************************************************************************************************************************************************************/



	public function retestingSample() {

		$this->loadModel('Workflow');
		$this->loadModel('ActualTestData');
		$this->loadModel('CodeDecode');
		$this->loadModel('SampleInward');
		$this->loadModel('FinalTestResult');
		$this->loadModel('DmiUsers');
		$this->loadModel('MCommodity');

		$conn = ConnectionManager::get('default');

		$category_code=$_POST['category_code'];
		$commodity_code=$_POST['commodity_code'];
		$tran_date = $this->request->getData("tran_date");
		$postdata = $this->request->getData();
		$sample_code = trim($postdata['sample_code']);

		if (!isset($sample_code) || !is_numeric($sample_code)) {
			echo "#[error]~Invalid Sample code#";
			exit;
		}

		if (!isset($commodity_code) || !is_numeric($commodity_code)) {
			echo "#[error]~Invalid Commodity code#";
			exit;
		}

		if (!isset($category_code) || !is_numeric($category_code)) {
			echo "#[error]~Invalid Category code#";
			exit;
		}

		$patternb='/(\d{4})-(\d{2})-(\d{2})/';
		$rttttttv=preg_match($patternb,$tran_date);

		if ($rttttttv==0) {
			echo '#[error]~Invalid transaction date!#';
			exit;
		}

		if ($_POST['status_flag']=="R") {

			$status_flag='R';

		} else {
			echo '#[error]~Invalid Status Flag!#';
			exit;
		}

		$dst_loc =$_SESSION["posted_ro_office"];

		if ($_SESSION['user_flag']=='RAL') {

			$data= $this->DmiUsers->find('all', array('conditions'=> array('role' => 'RAL/CAL OIC' , 'status !='=>'disactive' )))->first();
			$dst_usr=$data['id'];

		} else {

			$data= $this->DmiUsers->find('all', array('conditions'=> array('role' => 'DOL' , 'status !='=>'disactive')))->first;
			$dst_usr=$data['id'];
		}

		$ogrsample1= $this->Workflow->find('all', array('conditions'=> array('stage_smpl_cd' => $sample_code)))->first();

		$ogrsample = $ogrsample1['org_sample_code'];

		$workflow_data = array("org_sample_code"=>$ogrsample,
							   "src_loc_id"=>$_SESSION["posted_ro_office"],
							   "src_usr_cd"=>$_SESSION["user_code"],
							   "dst_loc_id"=>$dst_loc,
							   "dst_usr_cd"=>$_SESSION["user_code"],
							   "stage_smpl_flag"=>"R",
							   "stage_smpl_cd"=>$sample_code,
							   "tran_date"=>$tran_date,
							   "user_code"=>$_SESSION["user_code"],
							   "stage"=>"6");

		$workflowEntity = $this->Workflow->newEntity($workflow_data);
		$this->Workflow->save($workflowEntity);

		$tests = $this->request->getData('test_code');
		$cnt = $conn->execute(" SELECT count(1) FROM final_test_result WHERE sample_code='$sample_code' AND test_code='$tests' AND display='Y'");
		$cnt = $cnt->fetchAll('assoc');

		if ($cnt[0]['count']>0) {

		  if ($this->FinalTestResult->updateAll(array('display' => 'N'),array('sample_code' => $sample_code,'test_code'=>$tests))) {

				$ogrsample1 = $this->Workflow->find('all', array('conditions'=> array('stage_smpl_cd' => $sample_code)))->first();
				$ogrsample = $ogrsample1['org_sample_code'];
				$ogrsample1['org_sample_code'] = $ogrsample;

				$FinalTestResultEntity = $this->FinalTestResult->newEntity($this->request->getData());

				if (!$this->FinalTestResult->save($FinalTestResultEntity)) {

					echo "#Sorry..Some Technical issue Occured.#";
					exit;
				}

			} else {

				echo "#Sorry..Some Technical issue Occured.#";
				exit;
			}

		}

		$sample_code = trim($this->request->getData('sample_code'));

		$query = $conn->execute("SELECT si.org_sample_code FROM sample_inward AS si
								 INNER JOIN workflow AS w ON w.org_sample_code = si.org_sample_code
							     WHERE w.stage_smpl_cd = '$sample_code'");

		$ogrsample3 = $query->fetchAll('assoc');

		$ogrsample_code = $ogrsample3[0]['org_sample_code'];

		$conn->execute("UPDATE sample_inward SET status_flag='SR' WHERE org_sample_code='$ogrsample_code'");

		#SMS: Sample Retest
		//$this->DmiSmsEmailTemplates->sendMessage(102,$_SESSION["user_code"],$sample_code); #Source
		$this->LimsUserActionLogs->saveActionLog('Sample Sent for Retest','Success'); #Action

	  	echo '#1#';
	  	exit;

	}

/*******************************************************************************************************************************************************************************************************************************************/


	public function checkFinalResultCount(){

		$sample_code = trim($_POST['sample_code']);
		$conn = ConnectionManager::get('default');

		$res = $conn->execute("SELECT count(*) FROM m_sample_allocate WHERE sample_code='$sample_code'");
		$res = $res->fetchAll('assoc');

		$res_count = $conn->execute("SELECT DISTINCT s.chemist_code FROM m_sample_allocate AS s
									 WHERE s.sample_code='$sample_code' AND s.acptnce_flag IN('F','NABC','N')
									 GROUP BY s.chemist_code");

		$res_count = $res_count->fetchAll('assoc');

		if ($res[0]['count']==count($res_count)) {

			echo '#1#';
		} else {
			echo '#0#';
		}
		exit;
	}

/*******************************************************************************************************************************************************************************************************************************************/


	public function getDetails(){

		$sample_code	= trim($_POST['sample_code']);
		$conn = ConnectionManager::get('default');

		$query = $conn->execute("SELECT a.test_code,a.test_name,b.test_type_name FROM actual_test_data AS atd
								 INNER JOIN m_test AS a ON a.test_code = atd.test_code
								 INNER JOIN m_test_type AS b ON a.test_type_code = b.test_type_code
								 INNER JOIN m_sample_allocate AS s ON atd.chemist_code = s.chemist_code
								 WHERE atd.sample_code='$sample_code' AND atd.display='Y' AND s.acptnce_flag !='NABC'
								 GROUP BY a.test_name,a.test_code,b.test_type_name");//group by added by Amol on 11-09-2020

		$res = 	$query->fetchAll('assoc');

		echo '#'.json_encode($res).'#';
		exit;

	}

/*******************************************************************************************************************************************************************************************************************************************/


	 //this function is added to call on ajax when test is click to view result by Inw ofcr.
	 //this will notify user the status of result submitted or not by chemist.
	 public function checkMultipleTestAlloc() {

		$this->autoRender = false;

		$sample_code=trim($_POST['sample_code']);
		$test_code=trim($_POST['test_code']);

		if (!isset($sample_code) || !is_numeric($sample_code)) {
			echo "#[error]~Invalid code#";
			exit;
		}

		if (!isset($test_code) || !is_numeric($test_code)) {
			echo "#[error]~Invalid code#";
			exit;
		}

		$this->loadModel('ActualTestData');
		$this->loadModel('DmiUsers');

		$chemist_name_one=null;
		$submit_status1=null;
		$chem_code1=null;
		$temp=null;

		$get_chemist_for_test = $this->ActualTestData->find('all',array('conditions'=>array('sample_code IS'=>$sample_code,'test_code IS'=>$test_code)))->toArray();
		$i=0;
		foreach ($get_chemist_for_test as $each) {

			//get chemist name for this test
			$user_details = $this->DmiUsers->find('all',array('conditions'=>array('id IS'=>$each['alloc_to_user_code'])))->first();
			$chemist_name = $user_details['f_name'].' '.$user_details['l_name'];
			$chemist_code = $each['chemist_code'];


			$test_performed = $each['test_perfm_date'];

			if (!empty($test_performed)) {

				$submit_status = 'Submitted';
			} else {
				$submit_status = 'Pending';
			}
			//$test_status[$i] = $chemist_code[$chemist_name];

			$chemist_name_one[$i] = $chemist_name;
			$submit_status1[$i] = $submit_status;
			$chem_code1[$i] = $chemist_code;
			$i++;

		}

		$temp_name = array_combine($chem_code1, $chemist_name_one);
		$temp_status = array_combine($chem_code1, $submit_status1);

		echo '#'.json_encode(array($temp_name,$temp_status)).'#';

		exit;
	}

/*******************************************************************************************************************************************************************************************************************************************/


	public function getfinalResult(){

		$this->loadModel('FinalTestResult');

		$sample_code=trim($_POST['sample_code']);
		$test_code=trim($_POST['test_code']);
		$duplicate_flag=trim($_POST['duplicate_flag']);


		if (!isset($sample_code) || !is_numeric($sample_code)) {
			echo "#[error]~Invalid code#";
			exit;
		}

		if (!isset($test_code) || !is_numeric($test_code)) {
			echo "#[error]~Invalid code#";
			exit;
		}

		if ($duplicate_flag=="D") {

			$final_result=$this->FinalTestResult->find('list', array(
				'keyField'=>'final_result','valueField'=>'final_result',
				'conditions'=>array('sample_code IS'=>$sample_code,'test_code IS'=>$test_code,'duplicate_flg'=>'D' )))->toArray();
		} else {

			$final_result=$this->FinalTestResult->find('list', array(
				'keyField'=>'final_result','valueField'=>'final_result',
				'conditions'=>array('sample_code IS'=>$sample_code,'test_code IS'=>$test_code,'display'=>'Y')))->toArray();
		}

		if (count($final_result)>0) {
			echo '#'.json_encode($final_result).'#';
		} else {
			echo  '#1#';
		}

		exit;
	}

/*******************************************************************************************************************************************************************************************************************************************/

	public function getAllocTest() {

		$sample_code=trim($_POST['sample_code']);
		$test_code=trim($_POST['test_code']);
		$conn = ConnectionManager::get('default');

		if (!isset($sample_code) || !is_numeric($sample_code)) {
			echo "#[error]~Invalid code#";
			exit;
		}

		if (!isset($test_code) || !is_numeric($test_code)) {
			echo "#[error]~Invalid code#";
			exit;
		}

		$query = $conn->execute("SELECT atd.chemist_code,atd.result FROM actual_test_data AS atd
								 INNER JOIN m_sample_allocate AS s ON s.org_sample_code=atd.org_sample_code
								 WHERE s.sample_code='$sample_code' AND test_code='$test_code' AND s.display='Y'
								 GROUP BY atd.chemist_code,atd.result");//group by added by Amol on 11-09-2020

		$res1 = $query->fetchAll('assoc');

		echo '#'.json_encode($res1).'#';
		exit;

	}

/*******************************************************************************************************************************************************************************************************************************************/


	public function viewData() {

		$conn = ConnectionManager::get('default');
		$sample_code = trim($_POST['sample_code']);
		$commodity_code	= trim($_POST['commodity_code']);
		$category_code	= trim($_POST['category_code']);



		if (!isset($category_code) || !is_numeric($sample_code)) {
			echo "#[error]~Invalid Sample code#";
			exit;
		}

		if (!is_numeric($commodity_code) || !isset($commodity_code)) {
			echo '#[error]~Invaild commodity code!#';
			exit;
		}

		if (!is_numeric($category_code) || !isset($category_code)) {
			echo '#[error]~Invaild category code!#';
			exit;
		}

		$qry = "SELECT DISTINCT a.sample_code,t.test_name ,a.final_result,a.test_code FROM final_test_result AS a
				INNER JOIN m_test AS t ON t.test_code=a.test_code
				WHERE a.display='Y' AND a.commodity_code='$commodity_code' AND a.category_code='$category_code'";

		if ($_POST['sample_code']) {
			$qry .=	"and a.sample_code='$sample_code' ";
		}

		$qry .=	"order by a.sample_code asc ";

		$res = $conn->execute($qry);
		$res = $res->fetchAll('assoc');

		echo '#'.json_encode($res).'#';
		exit;

	}


/*******************************************************************************************************************************************************************************************************************************************/


	public function forwardRal(){

		$this->loadModel('Workflow');
		$this->loadModel('ActualTestData');
		$this->loadModel('CodeDecode');
		$this->loadModel('SampleInward');
		$this->loadModel('DmiUsers');
		$conn = ConnectionManager::get('default');

		$tran_date=date('Y-m-d');
		$sample_code=trim($_POST['sample_code']);

		if (!isset($sample_code) || !is_numeric($sample_code)) {
			echo "#[error]~Invalid code#";
			exit;
		}

		$dst_loc = $_SESSION["posted_ro_office"];

		if ($_SESSION['user_flag']=='RAL' || $_SESSION['user_flag']=='CAL') {

			$data= $this->DmiUsers->find('all', array('conditions'=> array('role' =>'Inward Officer','posted_ro_office' => $dst_loc, 'status !='=>'disactive')))->first();
			$dst_usr=$data['id'];

		} else {

			$data= $this->DmiUsers->find('all', array('conditions'=> array('role' =>'Inward Officer','posted_ro_office' => $dst_loc, 'status !='=>'disactive')))->first();
			$dst_usr=$data['id'];
		}

		if ($_SESSION['user_flag']=='HO') {

			$data= $this->DmiUsers->find('all', array('conditions'=> array('role' =>'Head Office','posted_ro_office' => $dst_loc, 'status !='=>'disactive')))->first();
			$dst_usr=$data['id'];
		} else {

			// Change the logic, now send sample to inward officer. before change the sample send to DOL.
			$data= $this->DmiUsers->find('all', array('conditions'=> array('role' =>'Inward Officer','posted_ro_office' => $dst_loc , 'status !='=>'disactive')))->first();
			$dst_usr=$data['id'];
		}

		$ogrsample1= $this->Workflow->find('all', array('conditions'=> array('stage_smpl_cd' => $sample_code)))->first();

		$ogrsample=$ogrsample1['org_sample_code'];

		$workflow_data = array("org_sample_code"=>$ogrsample,
								"src_loc_id"=>$_SESSION["posted_ro_office"],
								"src_usr_cd"=>$_SESSION["user_code"],
								"dst_loc_id"=>$dst_loc,
								"dst_usr_cd"=>$dst_usr,
								"stage_smpl_flag"=>"FR",
								"stage_smpl_cd"=>$sample_code,
								"tran_date"=>$tran_date,
								"user_code"=>$_SESSION["user_code"],
								"stage"=>"6");

		$worklfowEntity = $this->Workflow->newEntity($workflow_data);

		$this->Workflow->save($worklfowEntity);

		$sample_code = trim($this->request->getData('sample_code'));

		$query = $conn->execute("SELECT si.org_sample_code FROM sample_inward AS si
								 INNER JOIN workflow AS w ON w.org_sample_code = si.org_sample_code
								 WHERE w.stage_smpl_cd = '$sample_code'");

		$ogrsample3 = $query->fetchAll('assoc');

		$ogrsample_code = $ogrsample3[0]['org_sample_code'];

		if ($_SESSION['user_flag']=='RAL') {

			$conn->execute("UPDATE sample_inward SET status_flag='FR',approve_rdng_date='$tran_date',ral_anltc_rslt_rcpt_dt='$tran_date' WHERE org_sample_code='$ogrsample_code'");

		} elseif ($_SESSION['user_flag']=='CAL') {

			$conn->execute("UPDATE sample_inward SET status_flag='FR',approve_rdng_date='$tran_date',cal_anltc_rslt_rcpt_dt='$tran_date' WHERE org_sample_code='$ogrsample_code'");

		} else {

			$conn->execute("UPDATE sample_inward SET status_flag='FR',approve_rdng_date='$tran_date',cal_anltc_rslt_rcpt_dt='$tran_date' WHERE org_sample_code='$ogrsample_code'");
		}

		$office_name = $conn->execute("SELECT ro_office FROM actual_test_data AS a
									   INNER JOIN dmi_ro_offices AS r ON r.id=a.lab_code
									   INNER JOIN workflow AS w ON a.org_sample_code=w.org_sample_code
									   WHERE a.sample_code='$sample_code' AND a.display='Y' AND w.stage_smpl_flag='AR'");

		$office_name = $office_name->fetchAll('assoc');

		$this->ActualTestData->updateAll(array('status_flag' => 'G'),array('sample_code'=>$sample_code,'display'=>'Y'));
		$this->CodeDecode->updateAll(array('status_flag' => 'G'),array('sample_code'=>$sample_code,'display'=>'Y'));

		$this->loadModel('DmiRoOffices');
		$oic = $this->DmiRoOffices->getOfficeIncharge();
		$chemistID = $this->Workflow->getChemistId($ogrsample);

		#SMS: Forward To RAL
		$this->DmiSmsEmailTemplates->sendMessage(104,$_SESSION["user_code"],$sample_code); #SOURCE
		$this->DmiSmsEmailTemplates->sendMessage(105,$dst_usr,$sample_code,); #INWARD
		$this->DmiSmsEmailTemplates->sendMessage(141,$oic,$sample_code,); #OIC
		$this->DmiSmsEmailTemplates->sendMessage(106,$chemistID,$sample_code,); #CHEMIST

		$this->LimsUserActionLogs->saveActionLog('Sample Sent Back to RAL','Success'); #Action

		echo  '#'.json_encode($office_name).'#';

		exit;

  	}


/*******************************************************************************************************************************************************************************************************************************************/


	public function forwardOic(){

		$this->loadModel('Workflow');
		$this->loadModel('ActualTestData');
		$this->loadModel('CodeDecode');
		$this->loadModel('SampleInward');
		$this->loadModel('DmiUsers');
		$conn = ConnectionManager::get('default');

		$tran_date=$this->request->getData('tran_date');
		$sample_code=trim($_POST['sample_code']);
		$dst_loc =$_SESSION["posted_ro_office"];

		if ($_SESSION['user_flag']=='CAL' || $_SESSION['user_flag']=='HO') {

			$data= $this->DmiUsers->find('all', array('conditions'=> array('role' => 'DOL', 'status !='=>'disactive')))->first();
			$dst_usr=$data['id'];
		}

		if ($_SESSION['user_flag']=='RAL') {

			$data= $this->DmiUsers->find('all', array('conditions'=> array('role' => 'RAL/CAL OIC','posted_ro_office'=>$dst_loc , 'status !='=>'disactive' )))->first();
			$dst_usr=$data['id'];
		}

		$ogrsample1= $this->Workflow->find('all', array('conditions'=> array('stage_smpl_cd IS' => $sample_code)))->first();

		$ogrsample=$ogrsample1['org_sample_code'];

		$workflow_data = array("org_sample_code"=>$ogrsample,
								"src_loc_id"=>$_SESSION["posted_ro_office"],
								"src_usr_cd"=>$_SESSION["user_code"],
								"dst_loc_id"=>$dst_loc,
								"dst_usr_cd"=>$dst_usr,
								"stage_smpl_flag"=>"FO",
								"stage_smpl_cd"=>$sample_code ,
								"tran_date"=>$tran_date,
								"user_code"=>$_SESSION["user_code"],
								"stage"=>"6");

		$workflowEntity = $this->Workflow->newEntity($workflow_data);

		$this->Workflow->save($workflowEntity);

		$sample_code=trim($this->request->getData('sample_code'));

		$query = $conn->execute("SELECT si.org_sample_code FROM sam AS si
								 INNER JOIN workflow AS w ON w.org_sample_code = si.org_sample_code
								 WHERE w.stage_smpl_cd = '$sample_code'");

		$ogrsample3 = $query->fetchAll('assoc');

		$ogrsample_code = $ogrsample3[0]['org_sample_code'];

		if ($_SESSION['user_flag']=='RAL') {

			$conn->execute("UPDATE sample_inward SET status_flag='FO',approve_rdng_date='$tran_date',ral_anltc_rslt_rcpt_dt='$tran_date' WHERE org_sample_code='$ogrsample_code'");

		} elseif ($_SESSION['user_flag']=='CAL') {

			$conn->execute("UPDATE sample_inward SET status_flag='FO',approve_rdng_date='$tran_date',cal_anltc_rslt_rcpt_dt='$tran_date' WHERE org_sample_code='$ogrsample_code'");

		} else {

			$conn->execute("UPDATE sample_inward SET status_flag='FO',approve_rdng_date='$tran_date',cal_anltc_rslt_rcpt_dt='$tran_date' WHERE org_sample_code='$ogrsample_code'");
		}

		$org_sample_code = $conn->execute("SELECT DISTINCT sample_code FROM actual_test_data AS a
										   INNER JOIN workflow AS w ON a.org_sample_code=w.org_sample_code
										   WHERE sample_code='$sample_code' AND a.display='Y' AND stage_smpl_flag='AR'");

		$org_sample_code = $org_sample_code->fetchAll('assoc');

		$this->actual_test_data->updateAll(array('status_flag' => 'G'),array('sample_code'=>$sample_code,'display'=>'Y'));
		$this->CodeDecode->updateAll(array('status_flag' => 'G'),array('sample_code'=>$sample_code,'display'=>'Y'));

		#SMS - Forward to OIC
		//$this->DmiSmsEmailTemplates->sendMessage(2015,$sample_code);
		//$this->DmiSmsEmailTemplates->sendMessage(2015,$sample_code);

		$this->LimsUserActionLogs->saveActionLog('Sample Sent to OIC','Success'); #Action

		echo  '#'.json_encode($org_sample_code).'#';

		exit;

	}


/*******************************************************************************************************************************************************************************************************************************************/

	public function checkForIsFinalize(){

		$sample_code = trim($_POST['sample_code']);
		$conn = ConnectionManager::get('default');

		if (preg_match('/[^A-Za-z0-9]/', $sample_code)){
			echo '#[error]~Invaild sample code!#';
			exit;
		}

		$res = $conn->execute("SELECT  * FROM workflow WHERE stage_smpl_cd='$sample_code' AND stage_smpl_flag='AR' ");
		$res = $res->fetchAll('assoc');

		//added for re-testing could not finalize samples 
		//on 12-09-2022 by shreeya
		if(count($res)>0)
		{
			$this->loadModel('Workflow');
			$checkRFlag = $this->Workflow->find('all',array('conditions'=>array('stage_smpl_flag'=>'R','stage_smpl_cd IS'=>$sample_code,'id >'=>$res[0]['id'])))->first();
			if(!empty($checkRFlag))
			{
				$res = array();
			}
			
		}
		
		if(count($res)>0)
		{
			echo "#Exists#";
		}else{
			echo "#Not#";
		}
		exit;
	}


/*******************************************************************************************************************************************************************************************************************************************/



	public function finalizedSample(){

		$this->loadModel('Workflow');
		$this->loadModel('ActualTestData');
		$this->loadModel('CodeDecode');
		$this->loadModel('SampleInward');
		$this->loadModel('DmiUsers');
		$conn = ConnectionManager::get('default');

		$tran_date=$this->request->getData('tran_date');

		$patternb='/(\d{4})-(\d{2})-(\d{2})/';
		$rttttttv=preg_match($patternb,$tran_date);

		if ($rttttttv==0){
			echo '#[error]~Invalid transaction date!#';
			exit;
		}

		$sample_code=trim($_POST['sample_code']);
		$dst_loc=$_POST['posted_ro_office'];
		$user_flag=$_POST['user_flag'];
		$user_code=$_POST['user_code'];

		if($user_flag=='RAL'){

			$data= $this->DmiUsers->find('all', array('conditions'=> array('role' =>'Inward Officer','posted_ro_office' => $dst_loc , 'status !='=>'disactive')))->first();
			$dst_usr=$data['id'];
		}
		else
		{
			/* Change the conditions for to find destination user id, after test result approved by lab incharge the application send to lab inward officer, */
			$data= $this->DmiUsers->find('all', array('conditions'=> array('role' => 'Inward Officer','posted_ro_office' => $dst_loc , 'status !='=>'disactive')))->first();
			$dst_usr=$data['id'];
		}

		$ogrsample1= $this->Workflow->find('all', array('conditions'=> array('stage_smpl_cd IS' => $sample_code)))->first();

		$ogrsample=$ogrsample1['org_sample_code'];

		$workflow_data = array("org_sample_code"=>$ogrsample,
								"src_loc_id"=>$dst_loc,
								"src_usr_cd"=>$user_code,
								"dst_loc_id"=>$dst_loc,
								"dst_usr_cd"=>$dst_usr,
								"stage_smpl_flag"=>"AR",
								"stage_smpl_cd"=>$sample_code,
								"tran_date"=>$tran_date,
								"user_code"=>$user_code,
								"stage"=>"6");

		$workflowEntity = $this->Workflow->newEntity($workflow_data);

		$this->Workflow->save($workflowEntity);

		$sample_code=trim($this->request->getData('sample_code'));

		$query = $conn->execute("SELECT si.org_sample_code FROM sample_inward AS si
								 INNER JOIN workflow AS w ON w.org_sample_code = si.org_sample_code
								 WHERE w.stage_smpl_cd = '$sample_code'");

		$ogrsample3 = $query->fetchAll('assoc');

		$ogrsample_code = $ogrsample3[0]['org_sample_code'];

		if ($user_flag=='RAL') {

			$conn->execute("UPDATE sample_inward SET status_flag='AR',approve_rdng_date='$tran_date',ral_anltc_rslt_rcpt_dt='$tran_date' WHERE org_sample_code='$ogrsample_code'");

		} elseif ($user_flag=='CAL') {

			$conn->execute("UPDATE sample_inward SET status_flag='AR',approve_rdng_date='$tran_date',cal_anltc_rslt_rcpt_dt='$tran_date' WHERE org_sample_code='$ogrsample_code'");
		}

		$org_sample_code = $conn->execute("SELECT DISTINCT sample_code FROM actual_test_data AS a
										   INNER JOIN workflow AS w ON a.org_sample_code=w.org_sample_code
										   WHERE sample_code='$sample_code' AND a.display='Y' AND stage_smpl_flag='AR'");

		$org_sample_code = $org_sample_code->fetchAll('assoc');

		echo  '#'.json_encode($org_sample_code).'#';

	 	exit;

  	}

/*******************************************************************************************************************************************************************************************************************************************/

	public function getDuplicateFlag(){

		$sample_code = trim($_POST['sample_code']);
		$conn = ConnectionManager::get('default');

		if (preg_match('/[^A-Za-z0-9]/', $sample_code)){
			echo '#[error]~Invaild sample code!#';
			exit;
		}

		$res = $conn->execute("SELECT count(*) FROM m_sample_allocate AS sa
							   INNER JOIN sample_inward AS a ON sa.org_sample_code=a.org_sample_code
						       WHERE sa.sample_code='$sample_code' AND a.result_dupl_flag='D'");

		$res = $res->fetchAll('assoc');

		echo '#'.$res[0]['count'].'#';
		exit;
	}


/*******************************************************************************************************************************************************************************************************************************************/

	public function viewApprovedResult(){

		$this->autoRender=false;
		 $conn = ConnectionManager::get('default');

		$this->loadModel('FinalTestResult');
		$this->loadModel('MTest');

		$sample_code = ($_POST['stage_sample_modal_view']);

		$query = $conn->execute("SELECT ftr.sample_code,ftr.final_result,mt.test_name FROM final_test_result AS ftr
								 INNER JOIN m_test AS mt ON mt.test_code = ftr.test_code
								 WHERE ftr.sample_code='$sample_code' AND ftr.display='Y' AND mt.display='Y'");

		$temp1 = $query->fetchAll('assoc');

		echo '#'.json_encode($temp1).'#';
		exit;
	}


/*******************************************************************************************************************************************************************************************************************************************/


	public function approvedResults(){

		$conn = ConnectionManager::get('default');

		$query = $conn->execute("SELECT w.stage_smpl_cd,mcc.category_name,mc.commodity_name,mst.sample_type_desc 
								 FROM workflow AS w
								 INNER JOIN sample_inward AS si ON si.org_sample_code = w.org_sample_code
								 INNER JOIN m_sample_type AS mst ON mst.sample_type_code = si.sample_type_code
								 INNER JOIN m_commodity_category AS mcc ON mcc.category_code = si.category_code
								 INNER JOIN m_commodity AS mc ON mc.commodity_code = si.commodity_code
								 WHERE w.src_usr_cd=".$_SESSION['user_code']." and w.stage_smpl_flag='AR'");

		$showapprovedresult = $query ->fetchAll('assoc');

		$this->set('showapprovedresult',$showapprovedresult);
	}


}



?>
