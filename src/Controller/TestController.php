<?php
namespace App\Controller;

use Cake\Event\Event;
use App\Network\Email\Email;
use Cake\ORM\Entity;
use Cake\Datasource\ConnectionManager;
use Cake\View;

class TestController extends AppController {

	var $name = 'Test';

	public function initialize(): void {
		parent::initialize();
		$this->viewBuilder()->setLayout('admin_dashboard');
		$this->viewBuilder()->setHelpers(['Form','Html']);
		$this->loadComponent('Customfunctions');

	}

/************************************************************************************************************************************************************************************************************************/

	//to validate login user
	public function authenticateUser() {

		$this->loadModel('DmiUserRoles');
		$user_access = $this->DmiUserRoles->find('all',array('conditions'=>array('user_email_id IS'=>$this->Session->read('username'))))->first();

		if (!empty($user_access)) {
			//proceed
		} else {
			echo "Sorry.. You don't have permission to view this page"; ?><a href="<?php echo $this->getRequest()->getAttribute('webroot');?>users/login_user">	Please Login</a><?php
			exit;
		}
	}

/************************************************************************************************************************************************************************************************************************/

	//to list allocated samples for accepting by chemist
	public function acceptSample() {

		$this->authenticateUser();
		$this->viewBuilder()->setLayout('admin_dashboard');
		$testalloc = $this->getSampleToAccept();
		$this->set('testalloc',$testalloc);
	}


/************************************************************************************************************************************************************************************************************************/


	//created common function to fetch list , to be used for dashboard counts also, on 28-04-2021 by Amol
	public function getSampleToAccept() {

		$alloc_user = $_SESSION['user_code'];
		$conn = ConnectionManager::get('default');

		$testalloc = $conn->execute("SELECT DISTINCT chemist_code,b.commodity_name,s.* FROM sample_inward AS a
									INNER JOIN m_sample_allocate as s on a.org_sample_code=s.org_sample_code
									INNER JOIN m_commodity AS b ON a.commodity_code=b.commodity_code
									WHERE alloc_cncl_flag='N' AND s.alloc_to_user_code=$alloc_user AND acptnce_flag='N' AND a.display='Y' AND s.display='Y' ");
		$testalloc = $testalloc->fetchAll('assoc');

		$testalloc1 = $conn->execute("SELECT DISTINCT b.chemist_code,a.test_name FROM m_sample_allocate AS s
									INNER JOIN sample_inward AS si ON s.org_sample_code=si.org_sample_code
									INNER JOIN actual_test_data AS b ON s.org_sample_code=b.org_sample_code
									INNER JOIN m_test AS a ON b.test_code = a.test_code
									WHERE alloc_cncl_flag='N' AND acptnce_flag='N' AND si.display='Y' AND s.display='Y' AND s.alloc_to_user_code=$alloc_user
									ORDER BY a.test_name ASC");

		$testalloc1 = $testalloc1->fetchAll('assoc');

		if (count($testalloc)>0) {

			$this->set('testalloc',$testalloc);
			$this->set('testalloc1',$testalloc1);
		}

		return $testalloc;
	}

/************************************************************************************************************************************************************************************************************************/

	public function acceptSampleBychemist() {

		$conn = ConnectionManager::get('default');
		$this->loadModel('Workflow');
		$tran_date = date('Y-m-d');
		$stage = 4;
		$trimmedString = trim($this->request->getData('final_str'),"-");

		$data = explode("-",$trimmedString);

		$chemist_code = array();
		$workflow_data = array();

		for ($i=0;$i<count($data);$i++) {

			$date	= date("Y/m/d");
			$conn->execute("UPDATE m_sample_allocate SET acptnce_flag='Y',recby_ch_date='$date' WHERE sr_no=".$data[$i]);

			$query = $conn->execute("SELECT chemist_code FROM m_sample_allocate  WHERE sr_no=".$data[$i]);
			$qr_res = $query->fetchAll('assoc');
			$chemist_code[]=$qr_res[0]['chemist_code'];

			$sample	= $conn->execute("SELECT * FROM m_sample_allocate  WHERE sr_no=".$data[$i]);
			$sample = $sample->fetchAll('assoc');

			$workflow_data[] = array("org_sample_code"=>$sample[0]['org_sample_code'], "src_loc_id"=>$sample[0]['lab_code'], "src_usr_cd"=>$sample[0]['alloc_by_user_code'],"dst_loc_id"=>$sample[0]['lab_code'],"dst_usr_cd"=>$sample[0]['alloc_to_user_code'],"stage_smpl_cd"=>$sample[0]['sample_code'],"user_code"=>$_SESSION["user_code"],"tran_date"=>$tran_date, "stage"=>$stage,"stage_smpl_flag"=>"TABC");
			
		}

		
		$workflowEntity = $this->Workflow->newEntities($workflow_data);

		foreach($workflowEntity as $each){

			$this->Workflow->save($each);
		}

	
		$this->LimsUserActionLogs->saveActionLog('Sample Accept for Test','Success'); #Action

		#SMS : Sample Accept for Test
		//$this->DmiSmsEmailTemplates->sendMessage(2033,$sample[0]['sample_code'],$sample[0]['alloc_to_user_code']);

		echo '#'.json_encode($chemist_code).'#';
		exit;
	}

/************************************************************************************************************************************************************************************************************************/

	public function allocCancel() {

		$conn = ConnectionManager::get('default');
		$this->loadModel('Workflow');
		$sendback_remark = htmlentities($_POST['sendback_remark'], ENT_QUOTES);

		$trimmedString  = trim($this->request->getData('final_str1'),"-");
		$data = explode("-",$trimmedString);
		$date = date('Y-m-d');

		$chemist_code  = array();
		$workflow_data = array();

		for($i=0;$i<count($data);$i++){

			$conn->execute("UPDATE m_sample_allocate SET alloc_cncl_flag='C',acptnce_flag='NABC',sendback_remark='$sendback_remark',recby_ch_date='$date'
			WHERE sr_no=".$data[$i]);

			$query = $conn->execute("SELECT chemist_code FROM m_sample_allocate WHERE sr_no=".$data[$i]);
			$qr_res = $query->fetchAll('assoc');
			$chemist_code[]=$qr_res[0]['chemist_code'];

			$sample = $conn->execute("SELECT * FROM m_sample_allocate  WHERE sr_no=".$data[$i]);
			$sample = $sample->fetchAll('assoc');
			$tran_date=date('Y-m-d');
			$stage=4;

			$conn->execute("DELETE FROM workflow WHERE stage_smpl_cd='".$sample[0]['chemist_code']."' And stage_smpl_flag='TA'");

			$workflow_data[] = array("org_sample_code"=>$sample[0]['org_sample_code'], "src_loc_id"=>$sample[0]['lab_code'], "src_usr_cd"=>$_SESSION["user_code"],"dst_loc_id"=>$sample[0]['lab_code'],"dst_usr_cd"=>$sample[0]['alloc_by_user_code'],"stage_smpl_cd"=>$sample[0]['sample_code'],"user_code"=>$_SESSION["user_code"],"tran_date"=>$tran_date, "stage"=>$stage,"stage_smpl_flag"=>"NABC");

		}

		$workflowEntity = $this->Workflow->newEntities($workflow_data);

		foreach ($workflowEntity as $each) {

			$this->Workflow->save($each);
		}


		#SMS : Test Sent Back
		#$this->DmiSmsEmailTemplates->sendMessage(2034,$sample[0]['sample_code'],$sample[0]['alloc_by_user_code']);
		#$this->DmiSmsEmailTemplates->sendMessage(2034,$sample[0]['sample_code'],$sample[0]['alloc_by_user_code']);

		$this->LimsUserActionLogs->saveActionLog('Test Sent Back','Success'); #Action


		echo '#'.json_encode($chemist_code).'#';
		exit;
	}


/************************************************************************************************************************************************************************************************************************/

	//to list the
	public function availableToEnterReading(){

		$result = $this->getSampleToEnterReading();
		$this->set('chemist_codes_list',$result);

	}


/************************************************************************************************************************************************************************************************************************/


	//created common function to fetch list , to be used for dashboard counts also, on 28-04-2021 by Amol
	public function getSampleToEnterReading() {

		$conn = ConnectionManager::get('default');
		$alloc_user 	= $_SESSION["user_code"];

		// Apply distinct condition to get unic code,
		$query = $conn->execute("SELECT DISTINCT(c.chemist_code) FROM code_decode AS c
									INNER JOIN m_sample_allocate AS s ON s.chemist_code=c.chemist_code
									INNER JOIN sample_inward AS si ON si.org_sample_code=c.org_sample_code
									WHERE c.alloc_to_user_code=$alloc_user AND c.display='Y' AND c.status_flag !='C' AND s.acptnce_flag='Y'");

		$chemist_codes = $query->fetchAll('assoc');
		//to be used in below core query format, that's why
		$arr = "IN(";
		foreach($chemist_codes as $each){
			$arr .= "'";
			$arr .= $each['chemist_code'];
			$arr .= "',";
		}
		$arr .= "'00')";//00 is intensionally given to put last value in string.
	 

		$query = $conn->execute("SELECT DISTINCT ON (sa.chemist_code) sa.chemist_code,
													st.sample_type_desc,
													mcc.category_name,
													mc.commodity_name,
													ml.ro_office,
													w.modified AS accepted_on
													FROM sample_inward AS si
									INNER JOIN m_sample_type AS st ON si.sample_type_code=st.sample_type_code
									INNER JOIN m_commodity_category AS mcc ON si.category_code=mcc.category_code
									INNER JOIN dmi_ro_offices AS ml ON ml.id=si.loc_id
									INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
									INNER JOIN workflow AS w ON si.org_sample_code = w.org_sample_code
									INNER JOIN m_sample_allocate AS sa ON w.stage_smpl_cd = sa.sample_code
									WHERE sa.chemist_code ".$arr."");

		$result = $query->fetchAll('assoc');

		return $result;
	}

/************************************************************************************************************************************************************************************************************************/

	public function redirectToEnterReading($chem_reading_cd) {

		$this->Session->write('chem_reading_cd',$chem_reading_cd);
		$this->redirect(array('controller'=>'Test','action'=>'enterTestReading'));
	}

/************************************************************************************************************************************************************************************************************************/


	public function enterTestReading() {

		//header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval';");
		
		$this->authenticateUser();
		$this->viewBuilder()->setLayout('admin_dashboard');
		$this->loadModel('MSampleAllocate');
		$this->loadModel('ActualTestData');
		$conn = ConnectionManager::get('default');

		// set variables to show popup messages from view file
		$message = '';
		$message_theme = '';
		$redirect_to = '';

		$chem_reading_cd = $this->Session->read('chem_reading_cd');

		if (!empty($chem_reading_cd)) {

			$this->set('chemist_code',array($chem_reading_cd=>$chem_reading_cd));

			//get stage sample code from chemist code
			$get_code = $this->MSampleAllocate->find('all',array('fields'=>'sample_code','conditions'=>array('chemist_code IS'=>$chem_reading_cd)))->first();
			$stage_sample_code = $get_code['sample_code'];
			$this->set('stage_sample_code',$stage_sample_code);

			if ($this->request->is('post')) {

				$postdata = $this->request->getData();
				//html encode the each post inputs
				foreach($postdata as $key => $value){

					$postdata[$key] = htmlentities($postdata[$key], ENT_QUOTES);
				}

				$chemist = $this->request->getData('chemist_code');
				$test_code = $this->request->getData('test_code');
				$user_code = $this->request->getData('user_code');
				$remark = $this->request->getData('remark');

				if ($remark==null || $remark=='') {

					$remark="N";
					$postdata['remark'] = $remark;
				}

				$test = $this->ActualTestData->find('all', array('conditions' => array('chemist_code IS' => $chemist, 'test_code IS' => $test_code)))->toArray();
				$sr_no = $test[0]['sr_no'];

				$postdata['sr_no'] = $sr_no;
				$postdata['user_code'] = $user_code;

				$comncmnt_dt = $this->MSampleAllocate->find('all', array('conditions' => array('chemist_code IS' => $chemist, 'commencement_date IS NULL')))->toArray();

				$actualTestDataEntity = $this->ActualTestData->newEntity($postdata);

				if ($this->ActualTestData->save($actualTestDataEntity)) {

					if	(count($comncmnt_dt)>0) {

						$date = date("Y/m/d");
						$conn->execute("UPDATE m_sample_allocate SET commencement_date='$date' WHERE chemist_code='$chemist'");
					}

					$this->LimsUserActionLogs->saveActionLog('Test Saved','Success'); #Action
					$message = 'The Test has been saved!';
					$message_theme = 'success';
					$redirect_to = 'enterTestReading';

				} else {

					$this->LimsUserActionLogs->saveActionLog('Test Saved','Failed'); #Action
					$message = 'Sorry.. The Test has not been saved properly!.';
					$message_theme = 'failed';
					$redirect_to = 'enterTestReading';
				}

			}

			// set variables to show popup messages from view file
			$this->set('message',$message);
			$this->set('message_theme',$message_theme);
			$this->set('redirect_to',$redirect_to);

		}
	}


/************************************************************************************************************************************************************************************************************************/


	public function getTestType() {

		$this->loadModel('ActualTestData');
		$test_select = $_POST['test_select'];
		
		$conn = ConnectionManager::get('default');

		if (!is_numeric($test_select) || $test_select=='') {
			echo "#[error]#";
			exit;
		}

		$query = $conn->execute("SELECT b.test_type_name,a.test_code
								FROM actual_test_data AS atd
								INNER JOIN m_test AS a ON a.test_code = atd.test_code
								INNER JOIN m_test_type AS b ON b.test_type_code = a.test_type_code
								WHERE atd.test_code = '$test_select'");

		$result = $query->fetchAll('assoc');
	
		$test = array();

		foreach ($result as $each) {

			$test[$each['test_type_name']] = $each['test_code'];
		}

		echo '#'.json_encode($test).'#';
		exit;
	}


/************************************************************************************************************************************************************************************************************************/

	public function getTestFormulae1() {

		$this->loadModel('TestFormula');
		$test_select = $_POST['test_select'];
		$today = date("Y-m-d");

		if (!is_numeric($test_select) || $test_select=='') {
			echo "#[error]#";
			exit;
		}

		$test = $this->TestFormula->find('list', array('keyField'=>'test_code','valueField'=>'test_formulae','conditions' =>array('test_code IS' => $test_select,'start_date <= NOW()','end_date IS Null')))->toList();

		echo '#'.json_encode($test).'#';

		exit;
   	}

/************************************************************************************************************************************************************************************************************************/

	public function getDependentTest() {

		$test_select = $_POST['test_select'];
		$conn = ConnectionManager::get('default');

		if (!is_numeric($test_select) || $test_select=='') {

			echo "#[error]#";
			exit;
		}

		$query = $conn->execute("SELECT tf.field_value,a.field_name FROM test_fields AS tf
								INNER JOIN m_fields AS a ON a.field_code = tf.field_code
								WHERE tf.test_code = '$test_select' AND tf.field_type='D'");

		$result = $query->fetchAll('assoc');
		$test = array();

		foreach ($result as $each) {

			$test[$each['field_value']] = $each['field_name'];
		}

		echo '#'.json_encode($test).'#';
		exit;

	}

/************************************************************************************************************************************************************************************************************************/

	public function getTestByName() {

		$this->loadModel('Test');
		$test_name = $_POST['test_name'];
		$conn = ConnectionManager::get('default');

		$query = $conn->execute("SELECT test_code FROM m_test WHERE test_name='$test_name' AND display='Y'");
		$test = $query->fetchAll('assoc');

		echo '#'.$test[0]['test_code'].'#';
		exit;
	}

/************************************************************************************************************************************************************************************************************************/

	public function getSampleDataBytest() {

		$this->loadModel('ActualTestData');
		$test_code = $_POST['test_c'];
		$sample_code = $_POST['sample_code'];
		$alloc_user1 = $_POST['user_code'];

		$category = $this->ActualTestData->find('all', array('fields' => array('result'),'conditions' => array('chemist_code IS' => $sample_code,'test_code IS' => $test_code,'alloc_to_user_code IS' => $alloc_user1)))->toArray();

		echo '#'.$category[0]['result']."~".count($category).'#';
		exit;
	}

/************************************************************************************************************************************************************************************************************************/

	public function getTestParameter1() {

		$this->loadModel('TestFields');
		$test_select = $_POST['test_select'];
		$conn = ConnectionManager::get('default');

		if (!is_numeric($test_select) || $test_select=='') {
			echo "#[error]#";
			exit;
		}

		$query = $conn->execute("SELECT field_value,a.field_name,field_validation,field_unit,test_code
								FROM test_fields AS tf
								INNER JOIN m_fields AS a ON a.field_code = tf.field_code
								WHERE test_code = '$test_select'");

		$test = $query->fetchAll('assoc');

		echo '#'.json_encode($test).'#';
		exit;
	}


/************************************************************************************************************************************************************************************************************************/

	public function getSample() {

		$sample_code = $_POST['sample_code'];
		$user_code = $_POST['user_code'];
		$test_select = $_POST['test_select'];
		$conn = ConnectionManager::get('default');

		if (!is_numeric($test_select)) {
			echo "#[error]~Incorrect Test Code#";
			exit;
		}

		$check = preg_match("/^[A-Za-z0-9]/",$sample_code);

		if ($check==0) {
			echo '#[error]~Invalid sample code!#';
			exit;
		}

		$query = $conn->execute("SELECT * FROM actual_test_data WHERE chemist_code='$sample_code' AND test_code=$test_select ");
		$result = $query->fetchAll('assoc');

		$test = array();

		foreach ($result as $key => $value) {
			$test[$key] = $value;
		}

		echo '#'.json_encode($test[0]).'#';
		exit;
	}

/************************************************************************************************************************************************************************************************************************/


	public function getTestParameter() {

		$this->loadModel('Master_Test_Field');
		$this->loadModel('TestFields');
		$test_code = $_POST['test_select'];
		$test_select = $_POST['test_select'];
		$conn = ConnectionManager::get('default');

		if (!isset($test_code) || !is_numeric($test_code)) {
			echo "#[error]~Invalid Test code#";
			exit;
		}

		$query = $conn->execute("SELECT a.field_name FROM test_fields AS tf INNER JOIN m_fields AS a ON a.field_code = tf.field_code WHERE tf.test_code = '$test_code' ORDER BY a.field_name ASC");
		$result = $query->fetchAll('assoc');
		$test = array();

		foreach ($result as $each) {
			$test[] = $each['field_name'];
		}

		echo '#'.json_encode($test).'#';
		exit;
	}


/************************************************************************************************************************************************************************************************************************/


	public function getCommodity() {

		$str = "";
		$sample_code = trim($_POST['sample_code']);
		$conn = ConnectionManager::get('default');

		if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $sample_code)) {
			echo "#[error]#";
			exit;
		}

		$query = $conn->execute("SELECT a.* FROM m_commodity AS a,sample_inward AS b
								WHERE a.commodity_code=b.commodity_code AND b.stage_sample_code IN(select distinct(org_sample_code)
								FROM actual_test_data
								WHERE chemist_code='$sample_code' AND org_sample_code!='')");

		$sample_code1 = $query->fetchAll('assoc');

		if ($sample_code1) {
			$str = $sample_code1[0]['commodity_name']."~".$sample_code1[0]['commodity_code'];
			echo '#'.$str.'#';
		}

		exit;
	}


/************************************************************************************************************************************************************************************************************************/


	public function getAllocDate() {

		$sample_code = trim($_POST['sample_code']);
		$conn = ConnectionManager::get('default');

		if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $sample_code)) {
			echo "#[error]#";
			exit;
		}

		$sample_code = $conn->execute("SELECT DISTINCT(login_timestamp),recby_ch_date,expect_complt FROM m_sample_allocate WHERE  chemist_code='$sample_code'");
		$sample_code = $sample_code->fetchAll('assoc');

		echo '#'.json_encode($sample_code).'#';
		exit;
	}


/************************************************************************************************************************************************************************************************************************/


	public function getSampleData() {

		$sample_code = trim($_POST['sample_code']);
		$commodity_code = trim($_POST['commodity_code']);
		$alloc_user1 = $_POST['user_code'];

		$final_results = $this->chemistAllocatedTests($sample_code,$commodity_code,$alloc_user1);
		echo '#'.json_encode($final_results).'#';
		exit;
	}

/************************************************************************************************************************************************************************************************************************/

	public function chemistAllocatedTests($sample_code,$commodity_code,$alloc_user1) {

		$this->loadModel('ActualTestData');
		$this->loadModel('MSampleAllocate');
		$this->loadModel('CommGrade');
		$this->loadModel('TestFormula');
		$this->loadModel('MTest');
		$this->loadModel('MTestMethod');

		$final_results = array();

		$allocated_tests = $this->ActualTestData->find('all',array('fields'=>array('sample_code','test_code','result','test_perfm_date'),'conditions'=>array('chemist_code IS'=>$sample_code,'alloc_to_user_code IS'=>$alloc_user1)))->toArray();

		foreach ($allocated_tests as $each_test) {

			$grading_method = $this->CommGrade->find('all',array('fields'=>array('method_code'=>'DISTINCT(method_code)'),'conditions'=>array('commodity_code IS'=>$commodity_code,'test_code IS'=>$each_test['test_code'],'display'=>'Y')))->toArray();

			foreach ($grading_method as $each_method) {

				$testname = $this->MTest->find('all',array('fields'=>array('test_name'),'conditions'=>array('test_code IS '=>$each_test['test_code'])))->first();

				$methodname = $this->MTestMethod->find('all',array('fields'=>array('method_name'),'conditions'=>array('method_code IS'=>$each_method['method_code'])))->first();

				$test_formula_method = $this->TestFormula->find('all',array('fields'=>array('method_code','test_code','unit','test_formula1'),'conditions'=>array('method_code IS'=>$each_method['method_code'],'test_code'=>$each_test['test_code'],'display'=>'Y','end_date IS'=>NULL)))->first();

				if (empty($test_formula_method)) {

					$method_name = 'Undefined';
					$unit =  '--';

					$test_formula_method = $this->TestFormula->find('all',array('fields'=>array('test_formula1'),'conditions'=>array('test_code IS'=>$each_test['test_code'],'display'=>'Y','end_date IS'=>NULL)))->first();

					$test_formula = $test_formula_method['test_formula1'];

				} else {

					$unit = $test_formula_method['unit'];
					$method_name = $methodname['method_name'];
					$test_formula = $test_formula_method['test_formula1'];
				}

				$results['test_code'] = $each_test['test_code'];
				$results['test_name'] = $testname['test_name'];
				$results['method_name'] = $method_name;
				$results['test_result'] = $each_test['result'];
				$results['test_unit'] = $unit;
				$results['test_formula1'] = $test_formula;
				$results['test_perfm_date'] = $each_test['test_perfm_date'];

				$final_results[] = $results;
			}
		}

		return $final_results;

	}


/************************************************************************************************************************************************************************************************************************/


	public function getIncompleteTest() {

		$str  = "";
		$flag = false;
		$conn = ConnectionManager::get('default');
		$sample_code = trim($_POST['sample_code']);

		if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $sample_code)) {
			echo "#[error]#";
			exit;
		}

		$test = $conn->execute("SELECT result FROM actual_test_data WHERE chemist_code='$sample_code'");
		$test = $test->fetchAll('assoc');

		for ($i=0;$i<count($test);$i++) {

			if ($test[$i]['result']=="") {

				$flag=true;
			}
		}

		if ($flag) {

			echo "#1#";
		}

		exit;
	}

/************************************************************************************************************************************************************************************************************************/


	public function getTestByCommodityId() {

		$sample_code = $_POST['sample_code'];
		$user_code = $_SESSION['user_code'];

		$conn = ConnectionManager::get('default');

		$query = $conn->execute("SELECT a.test_code,a.test_name FROM actual_test_data AS atd
								INNER JOIN m_test AS a ON a.test_code = atd.test_code
								WHERE chemist_code = '$sample_code' AND alloc_to_user_code = '$user_code'
								ORDER BY a.test_name DESC");

		$result = $query->fetchAll('assoc');
		$category = array();

		foreach ($result as $each) {

			$category[$each['test_code']] = $each['test_name'];
		}

		echo '#'.json_encode($category).'#';
		exit;
	}


/************************************************************************************************************************************************************************************************************************/

	public function getTestFormulae() {

		$test_select = $_POST['test_select'];
		$to_dt=date("Y-d-m");
		$conn = ConnectionManager::get('default');

		if (!is_numeric($test_select) || $test_select=='') {
			echo "[error]";
			exit;
		}

		$query = $conn->execute("SELECT c.test_type_name,b.test_formulae,b.res_validation_range FROM actual_test_data AS atd
								INNER JOIN m_test AS a ON a.test_code = atd.test_code
								INNER JOIN test_formula AS b ON b.test_code = atd.test_code
								INNER JOIN m_test_type AS c ON c.test_type_code = a.test_type_code
								INNER JOIN test_fields AS t ON t.test_code = atd.test_code
								WHERE atd.test_code = '$test_select' AND b.end_date IS NULL
								GROUP BY c.test_type_name,b.test_formulae,b.res_validation_range");

		$test = $query->fetchAll('assoc');

		if (count($test)>0) {
			echo '#'.json_encode($test).'#';
		} else {
			echo "#1#";
		}

		exit;
	}

/************************************************************************************************************************************************************************************************************************/

	public function getTestSinglevalue() {

		$conn = ConnectionManager::get('default');

		$valnew = $_POST['valnew'];

		$test_vallcd= $_POST['test_vallcd'];

		$testname = $conn->execute("SELECT test_name FROM m_test WHERE test_code=$test_vallcd");

		$testname = $testname->fetchAll('assoc');

		$testname_final = $testname[0]['test_name'];

		if ($testname_final == "Butyro Refractometer Reading at 40 deg. centigrade") {

			$testt = $conn->execute("SELECT br AS ri FROM referance_table WHERE ri=$valnew");

		} elseif ($testname_final = "Refractive Index") {

			$testt = $conn->execute("SELECT br AS ri FROM referance_table WHERE ri=$valnew");

		} elseif ($testname_final = "BR to BR") {

			$testt = $conn->execute("SELECT br AS ri FROM referance_table WHERE br=$valnew");

		} elseif ($testname_final = "RI to RI") {

			$testt = $conn->execute("SELECT ri AS ri  FROM referance_table WHERE ri=$valnew");

		} else {

			$testt = $conn->execute("SELECT br AS ri  FROM referance_table WHERE ri=$valnew");
		}

		$testt = $testt->fetchAll('assoc');

		if (count($testt)>0) {
			echo '#'.json_encode($testt).'#';
		} else {
			echo "#1#";
		}

		exit;
	}

/************************************************************************************************************************************************************************************************************************/

	public function updateCantPerformTest() {

		$conn = ConnectionManager::get('default');

		$chemist_code=$_POST["chemist_code"];
		$test_code=$_POST["test_code"];

		$test = $conn->execute("UPDATE actual_test_data SET result='NA' WHERE chemist_code='$chemist_code' AND test_code='$test_code'");
		echo 1;

		exit;

	}

/************************************************************************************************************************************************************************************************************************/


	public function finalizeSampleResult(){

		//$this->autoRender = false;
		$this->loadModel('Workflow');
		$this->loadModel('CodeDecode');
		$this->loadModel('DmiUsers');
		$this->loadModel('SampleInward');
		$conn = ConnectionManager::get('default');

		// set variables to show popup messages from view file
		$message = '';
		$message_theme = '';
		$redirect_to = '';

		$sample_code1 = $_POST['sample_code'];
		$chemist_code = trim($_POST['sample_code']);
		$posted_ro_office = trim($_POST['posted_ro_office']);
		$user_code = trim($_POST['user_code']);


		if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $sample_code1)) {
			echo "[error]";
			exit;
		}

		if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $chemist_code)) {
			echo "[error]";
			exit;
		}

		$sample_code1 = $this->CodeDecode->find('all', array('conditions'=> array('chemist_code IS' =>$chemist_code)))->first();

		$sample_code = $sample_code1['sample_code'];

		$tran_date = $this->request->getData("tran_date");

		$ogrsample1= $this->Workflow->find('all', array('conditions'=> array('stage_smpl_cd IS' => $chemist_code)))->toArray();
		$src_loc_id =$ogrsample1[0]['src_loc_id'];
		$src_usr_cd =$ogrsample1[0]['src_usr_cd'];
		$ogrsample = $ogrsample1[0]['org_sample_code'];
		$stage = $ogrsample1[0]['stage']+1;

		$ogrsample2 = $this->SampleInward->find('all', array('conditions'=> array('org_sample_code IS' => $sample_code)))->first();

		$workflow_data = array("org_sample_code"=>$ogrsample,
								"src_loc_id"=>$posted_ro_office,
								"src_usr_cd"=>$user_code,
								"dst_loc_id"=>$src_loc_id,
								"dst_usr_cd"=>$src_usr_cd ,
								"stage_smpl_cd"=>$chemist_code,
								"tran_date"=>$tran_date,
								"user_code"=>$user_code,
								"stage_smpl_flag"=>"FT",
								"stage"=>$stage);

		$workflowEntity = $this->Workflow->newEntity($workflow_data);

		$this->Workflow->save($workflowEntity);

		$sample_code1=trim($this->request->getData('sample_code'));

		$query = $conn->execute("SELECT si.org_sample_code FROM sample_inward AS si INNER JOIN workflow AS w ON w.org_sample_code = si.org_sample_code WHERE w.stage_smpl_cd = '$sample_code1'");

		$ogrsample3 = $query->fetchAll('assoc');

		$ogrsample_code = $ogrsample3[0]['org_sample_code'];

		$conn->execute("UPDATE sample_inward SET status_flag='T' WHERE org_sample_code='$ogrsample_code'");

		$conn->execute("UPDATE code_decode SET status_flag='C' WHERE chemist_code='$chemist_code'");

		$conn->execute("UPDATE m_sample_allocate SET acptnce_flag='F' WHERE chemist_code='$chemist_code'");

		$q = $conn->execute("SELECT dst_usr_cd FROM workflow AS w INNER JOIN m_sample_allocate AS s ON w.org_sample_code=s.org_sample_code WHERE stage_smpl_flag='FT' AND acptnce_flag='F' AND chemist_code='$chemist_code'");

		$q = $q->fetchAll('assoc');
		$dst = $q[0]['dst_usr_cd'];

		$t = $conn->execute("SELECT role FROM dmi_users AS u INNER JOIN workflow AS w ON u.id=w.dst_usr_cd WHERE dst_usr_cd='$dst' AND stage_smpl_cd='$chemist_code'");

		$t = $t->fetchAll('assoc');

		#SMS : Test Result Finalized
		//$this->DmiSmsEmailTemplates->sendMessage(2035,$sample_code1,$dst);
		//$this->DmiSmsEmailTemplates->sendMessage(2036,$sample_code1,$user_code);

		$this->LimsUserActionLogs->saveActionLog('Test Result Finalized','Success'); #Action
		$message = 'Result have been finalized and forwarded to '.$t[0]['role'].' for approval!';
		$message_theme = 'success';
		$redirect_to = 'availableToEnterReading';

		// set variables to show popup messages from view file
		$this->set('message',$message);
		$this->set('message_theme',$message_theme);
		$this->set('redirect_to',$redirect_to);

	}

/************************************************************************************************************************************************************************************************************************/

	// This function is used for to genrate chemist test report, done by pravin bhakare, 09-120-2019
	public function viewPrfmTest() {

		$this->viewBuilder()->setLayout('admin_dashboard');
		$conn = ConnectionManager::get('default');

		if (isset($_SESSION['username'])) {

			$alloc_user=$_SESSION['user_code'];

			$query = $conn->execute("SELECT DISTINCT(w.stage_smpl_cd),
														mcc.category_name,
														mc.commodity_code,
														w.tran_date,
														mc.commodity_name,
														mst.sample_type_desc FROM workflow AS w
									INNER JOIN sample_inward AS si ON si.org_sample_code = w.org_sample_code
									INNER JOIN m_sample_type AS mst ON mst.sample_type_code = si.sample_type_code
									INNER JOIN m_commodity_category AS mcc ON mcc.category_code = si.category_code
									INNER JOIN m_commodity AS mc ON mc.commodity_code = si.commodity_code
									WHERE w.src_usr_cd='$alloc_user' AND w.stage_smpl_flag='FT'");

			$list_of_finalized_test = $query->fetchAll('assoc');

			$this->set('list_of_finalized_test', $list_of_finalized_test);

		} else {

			$this->redirect(array('action' => '../users/login_user'));
		}
	}

/************************************************************************************************************************************************************************************************************************/

	// This function is used for to genrate chemist test report,
	public function chemistTestReportCode($sample_code,$commodity_code) {

		$this->Session->write('ABchmist_sample_code12',$sample_code);
		$this->Session->write('ABcommodity_code12',$commodity_code);
		$this->redirect(array('controller'=>'Test','action'=>'chemist_test_report'));

	}

/************************************************************************************************************************************************************************************************************************/

	public function chemistTestReport() {

		$this->viewBuilder()->setLayout('pdf_layout');
		$this->loadModel('ActualTestData');
		$this->loadModel('TestFormula');
		$this->loadModel('TestFields');
		$this->loadModel('Workflow');
		$this->loadModel('MSampleAllocate');
		$this->loadModel('SampleInward');
		$conn = ConnectionManager::get('default');

		$chemist_code = trim($this->Session->read('ABchmist_sample_code12'));
		$get_smpl_cd= $this->MSampleAllocate->find('all',array('fields'=>array('org_sample_code'),'conditions'=>array('chemist_code IS'=>$chemist_code)))->first();
	
		$commodity_code = $this->Session->read('ABcommodity_code12');
		$sample_code1 = $get_smpl_cd['org_sample_code'];

		// $sample_code1=$this->Session->read('sample_test_code');
		$alloc_user1 = $_SESSION['user_code'];
		


		// new added code

		$str="SELECT org_sample_code FROM workflow WHERE display='Y' ";

		if ($sample_code1!='') {

			$str.=" AND trim(stage_smpl_cd)='$sample_code1' GROUP BY org_sample_code";
		}

		$sample_code2 = $conn->execute($str);
		$sample_code2 = $sample_code2->fetchAll('assoc');
	
		$Sample_code = $sample_code2[0]['org_sample_code'];
	
		$str2="SELECT stage_smpl_cd FROM workflow WHERE display='Y' ";

		if ($sample_code1!='') {

			$str2.=" AND org_sample_code='$Sample_code' AND stage_smpl_flag='AS' GROUP BY stage_smpl_cd";
		}

		$sample_code3 = $conn->execute($str2);
		$sample_code3 = $sample_code3->fetchAll('assoc');
		
		$Sample_code_as=trim($sample_code3[0]['stage_smpl_cd']);
	
		$this->set('Sample_code_as',$Sample_code_as);
		
		$this->loadModel('MSampleRegObs');

		$query2 = "SELECT msr.m_sample_reg_obs_code,mso.m_sample_obs_code,mso.m_sample_obs_desc,mst.m_sample_obs_type_code,mst.m_sample_obs_type_value
					FROM m_sample_reg_obs AS msr
					INNER JOIN m_sample_obs_type AS mst ON mst.m_sample_obs_type_code=msr.m_sample_obs_type_code
					INNER JOIN m_sample_obs AS mso ON mso.m_sample_obs_code=mst.m_sample_obs_code AND stage_sample_code='$Sample_code_as'
					GROUP BY msr.m_sample_reg_obs_code,mso.m_sample_obs_code,mso.m_sample_obs_desc,mst.m_sample_obs_type_code,mst.m_sample_obs_type_value";

			
		$method_homo = $conn->execute($query2);
		$method_homo = $method_homo->fetchAll('assoc');
		
		$this->set('method_homo',$method_homo);

		if (null!==($this->request->getData('ral_lab'))) {

			$data=$this->request->getData('ral_lab');

			$data1=explode("~",$data);

			if ($data1[0]!='all') {

				$ral_lab=$data1[0];
				$ral_lab_name=$data1[1];
				$this->set('ral_lab_name',$ral_lab_name);

			} else {

				$ral_lab=$data1[0];
				$ral_lab_name='all';
			}

		} else {

			$ral_lab='';
			$ral_lab_name='all';
		}
		
		
		// new added code end

		$query = $conn->execute("SELECT wf.stage_smpl_cd,
										usr.f_name,
										usr.l_name,
										mcc.category_name,
										mc.commodity_name,
										mst.sample_type_desc,
										msa.recby_ch_date,
										usr.role,
										ct.container_desc, pc.par_condition_desc, /* added for condition of seal and container type  27/05/2022 shreeya*/
										roo.ro_office FROM sample_inward AS si
								INNER JOIN workflow AS wf ON wf.org_sample_code=si.org_sample_code
								INNER JOIN m_commodity_category AS mcc ON mcc.category_code = si.category_code
								INNER JOIN m_commodity AS mc ON mc.commodity_code = si.commodity_code
								INNER JOIN m_container_type AS ct ON ct.container_code = si.container_code/* added for condition of seal and container type  27/05/2022 shreeya*/
								INNER JOIN m_par_condition AS pc ON pc.par_condition_code = si.par_condition_code/* added for condition of seal and container type  27/05/2022 shreeya*/
								INNER JOIN m_sample_type AS mst ON mst.sample_type_code = si.sample_type_code
								INNER JOIN m_sample_allocate AS msa ON msa.chemist_code = wf.stage_smpl_cd
								INNER JOIN dmi_users AS usr ON usr.id = wf.src_usr_cd
								INNER JOIN dmi_ro_offices AS roo ON roo.id = usr.posted_ro_office
								WHERE wf.stage_smpl_cd='$chemist_code' AND wf.stage_smpl_flag='FT'");


		$sample_details = $query->fetchAll('assoc');
		$sample_details = $sample_details[0];
		
		// added for chemist quantity and unit 27/05/2022 shreeya start
		$get_smpl_qty= $this->MSampleAllocate->find('all',array('fields'=>array('sample_qnt','sample_unit'),'conditions'=>array('chemist_code IS'=>$chemist_code)))->first();
		$this->loadModel('MUnitWeight');

		$get_smpl_unit= $this->MUnitWeight->find('all',array('fields'=>array('unit_weight'),'conditions'=>array('unit_id IS'=>$get_smpl_qty['sample_unit'])))->first();

		$this->set('smpl_qty',$get_smpl_qty['sample_qnt']);
		$this->set('smpl_unit',$get_smpl_unit['unit_weight']);
		
		 // added for chemist quantity and unit 27/05/2022 shreeya start end

		 
		$test_finalized_date = $this->Workflow->find('all',array('fields'=>array('tran_date'),'conditions'=>array('stage_smpl_cd IS'=>$chemist_code,'stage_smpl_flag'=>'FT')))->first();

		$sample_allocated_test = $this->chemistAllocatedTests($chemist_code,$commodity_code,$alloc_user1);

		foreach ($sample_allocated_test as $each_test_code) {

	
			$query = $conn->execute("SELECT tf.field_value,mf.field_name,tf.field_unit,tf.test_code
									FROM test_fields AS tf
									INNER JOIN m_fields AS mf ON mf.field_code=tf.field_code
									WHERE tf.test_code =".$each_test_code['test_code']);

			$test_fields[] = $query->fetchAll('assoc');
			
			$filled_test_values[] = $this->ActualTestData->find('all',array('conditions'=>array('test_code IS' =>$each_test_code['test_code'],'chemist_code'=>$chemist_code)))->toArray();
		
		}


		// NEW CODE ADDED


		$test = $this->ActualTestData->find('all', array('fields' => array('test_code'=>'distinct(test_code)'),'conditions' =>array('org_sample_code IS' => $Sample_code, 'display' => 'Y')))->toArray();
		
		$test_string=array();

		$i=0;
		
		foreach ($test as $each) {

			$test_string[$i]=$each['test_code'];
			$i++;
		}

		foreach($test_string as $row1) {
		
			$query = $conn->execute("SELECT DISTINCT(grade.grade_desc),grade.grade_code,test_code
									FROM comm_grade AS cg
									INNER JOIN m_grade_desc AS grade ON grade.grade_code = cg.grade_code
									WHERE cg.commodity_code = '$commodity_code' AND cg.test_code = '$row1' AND cg.display = 'Y'");

			$commo_grade = $query->fetchAll('assoc');
			$str="";

			$this->set('commo_grade',$commo_grade );
			
		}

		$j=1;

		foreach ($test_string as $row) {
			
			$query = $conn->execute("SELECT cg.grade_code,cg.grade_value,cg.max_grade_value,cg.min_max
									FROM comm_grade AS cg
									INNER JOIN m_test_method AS tm ON tm.method_code = cg.method_code
									INNER JOIN m_test AS t ON t.test_code = cg.test_code
									WHERE cg.commodity_code = '$commodity_code' AND cg.test_code = '$row' AND cg.display = 'Y'
									ORDER BY cg.grade_code ASC");

			$data = $query->fetchAll('assoc');


			$query = $conn->execute("SELECT t.test_name,tm.method_name
									FROM comm_grade AS cg
									INNER JOIN m_test_method AS tm ON tm.method_code = cg.method_code
									INNER JOIN m_test AS t ON t.test_code = cg.test_code
									INNER JOIN test_formula AS tf ON tf.test_code = cg.test_code AND tm.method_code = cg.method_code
									WHERE cg.commodity_code = '$commodity_code' AND cg.test_code = '$row' AND cg.display = 'Y'
									ORDER BY t.test_name ASC");

			$data1 = $query->fetchAll('assoc');

			if (!empty($data1)) {
				$data_method_name = $data1[0]['method_name'];
				$data_test_name = $data1[0]['test_name'];
			} else {
				$data_method_name = '';
				$data_test_name = '';
			}

			$qry1 = "SELECT count(chemist_code)
					FROM final_test_result AS ftr
					INNER JOIN sample_inward AS si ON si.org_sample_code=ftr.org_sample_code AND si.result_dupl_flag='D' AND ftr.sample_code='$sample_code1'
					GROUP BY chemist_code ";
				
			$res2	= $conn->execute($qry1);
			$res2 = $res2->fetchAll('assoc');
			 
			//get sample type code from sample sample inward table, to check if sample type is "Challenged"
			//if sample type is "challenged" then get report for selected final values only, no matter if single/duplicate analysis
			//applied on 27-10-2011 by Amol
			$getSampleType = $this->SampleInward->find('all',array('fields'=>'sample_type_code','conditions'=>array('org_sample_code IS' => $Sample_code)))->first();
			$sampleTypeCode = $getSampleType['sample_type_code'];
			
			if($sampleTypeCode==4){
				
				$res2=array();//this will create report for selected final results, if this res set to blank
			}

			$count_chemist = '';
			$all_chemist_code = array();

			// get all  allocated chemist if sample is for duplicate analysis
			if (isset($res2[0]['count'])>0) {

				$all_chemist_code = $conn->execute("SELECT ftr.chemist_code
													FROM m_sample_allocate AS ftr
													INNER JOIN sample_inward AS si ON si.org_sample_code=ftr.org_sample_code AND si.result_dupl_flag='D' AND ftr.sample_code='$sample_code1' ");

				$all_chemist_code= $all_chemist_code->fetchAll('assoc');
				
				$count_chemist = count($all_chemist_code);
			}

			// to get approved final result by Inward officer test wise
			$this->loadModel('FinalTestResult');
			$test_result= $this->FinalTestResult->find('list',array('valueField' => 'final_result','conditions' =>array('org_sample_code IS' => $Sample_code,'test_code' => $row,'display'=>'Y')))->toArray();
		
			//if sample is for duplicate analysis
			//so get result chmeist wise
			$result_D = '';
			$result = array();

			if (isset($res2[0]['count'])>0) {

				$i=0;

				foreach ($all_chemist_code as $each) {

					$chemist_code = $each['chemist_code'];

					//get result for each chemist_code
					$get_results = $this->ActualTestData->find('all',array('fields'=>array('result'),'conditions'=>array('org_sample_code IS' => $Sample_code,'chemist_code IS'=>$chemist_code,'test_code IS'=>$row,'display'=>'Y')))->first();

					$result[$i] = $get_results['result'];

					$i=$i+1;

				}

				//else get result from final test rsult
				//for single anaylsis this is fianl approved result array
			} else {

				if (count($test_result)>0) {

					foreach ($test_result as $key=>$val) {
						$result = $val;
					}

				} else {
					$result="";
				}
			}


			// for duplicate anaylsis this is final approved result array
			if (count($test_result)>0) {

				foreach ($test_result as $key=>$val) {
					$result_D= $val;
				}

			} else {
				$result_D="";
			}

			$commencement_date= $this->MSampleAllocate->find('all',array('order' => array('commencement_date' => 'asc'),'fields' => array('commencement_date'),'conditions' =>array('org_sample_code IS' => $Sample_code, 'display' => 'Y')))->toArray();
			$this->set('comm_date',$commencement_date[0]['commencement_date']);

			if (!empty($count_chemist)) {
				$count_chemist1 =  $count_chemist;
			} else {
				$count_chemist1 = '';
			}

			$this->set('count_test_result',$count_chemist1);
		
			$minMaxValue = '';

			foreach ($commo_grade as $key=>$val) {
			
				$key = $val['grade_code'];

				foreach ($data as $data4) {

					$data_grade_code = $data4['grade_code'];

					if ($data_grade_code == $key) {

						$grade_code_match = 'yes';

						if (trim($data4['min_max'])=='Min') {
							$minMaxValue = "<br>(".$data4['min_max'].")";
						} elseif (trim($data4['min_max'])=='Max') {
							$minMaxValue = "<br>(".$data4['min_max'].")";
						}
					}
				}
			}

			$str.="<tr><td>".$j."</td><td>".$data_test_name.$minMaxValue."</td>";
		
			// Draw tested test reading values,
			foreach ($commo_grade as $key=>$val) {

				$key = $val['grade_code'];

				$grade_code_match = 'no';
			
				foreach ($data as $data4) {

					$data_grade_code = $data4['grade_code'];

					if ($data_grade_code == $key) {

						$grade_code_match = 'yes';

						if (trim($data4['min_max'])=='Range') {

							$str.="<td>".$data4['grade_value']."-".$data4['max_grade_value']."</td>";

						} elseif (trim($data4['min_max'])=='Min') {

							$str.="<td>".$data4['grade_value']."</td>";

						} elseif (trim($data4['min_max'])=='Max') {

							$str.="<td>".$data4['max_grade_value']."</td>";

						} elseif (trim($data4['min_max'])=='-1') {

							$str.="<td>".$data4['grade_value']."</td>";

						}
					}
				}

				if ($grade_code_match == 'no') {
					$str.="<td>---</td>";
				}
			}


			//for duplicate analysis chemist wise results
			if ($count_chemist1>0) {

				for ($g=0;$g<$count_chemist;$g++) {
					$str.="<td align='center'>".$result[$g]."</td>";
				}

				//for final result column
				$str.="<td align='center'>".$result_D."</td>";

			//for single analysis final results
			} else {
				// added of new column  in the table start 27/05/2022 shreeya
				$get_chemval = $this->ActualTestData->find('all',array('fields'=>array('result'),'conditions'=>array('org_sample_code IS' => $Sample_code,'chemist_code IS'=>$chemist_code,'test_code IS'=>$row,'display'=>'Y')))->first();
				$che_val = $get_chemval['result'];
				$str.="<td>".$che_val."</td>";

				// added of new column in the table end 27/05/2022 
				$str.="<td>".$result."</td>";
			}
			
			$str.="<td>".$data_method_name."</td></tr>";
			$j++;
		}

		$this->set('table_str',$str );


		$query = $conn->execute("SELECT si.*,mc.commodity_name, mcc.category_name, st.sample_type_desc, ct.container_desc, pc.par_condition_desc, uw.unit_weight, rf.ro_office, sa.sample_code, ur.user_flag, gd.grade_desc, u1.f_name, u1.l_name, rf2.ro_office
								FROM sample_inward AS si
								INNER JOIN m_commodity AS mc ON mc.commodity_code = si.commodity_code
								INNER JOIN m_commodity_category AS mcc ON mcc.category_code = si.category_code
								INNER JOIN m_sample_type AS st ON st.sample_type_code = si.sample_type_code
								INNER JOIN m_container_type AS ct ON ct.container_code = si.container_code
								INNER JOIN m_par_condition AS pc ON pc.par_condition_code = si.par_condition_code
								INNER JOIN dmi_ro_offices AS rf ON rf.id = si.loc_id
								INNER JOIN dmi_ro_offices AS rf2 ON rf2.id = si.grade_user_loc_id
								INNER JOIN m_unit_weight AS uw ON uw.unit_id = si.parcel_size
								INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code = si.org_sample_code
								INNER JOIN dmi_users AS u ON u.id = si.user_code
								INNER JOIN dmi_users AS u1 ON u1.id = si.grade_user_cd
								INNER JOIN dmi_user_roles AS ur ON u.email = ur.user_email_id
								INNER JOIN m_grade_desc AS gd ON gd.grade_code = si.grade
								WHERE si.org_sample_code = '$Sample_code'");

		$test_report = $query->fetchAll('assoc');


		if($test_report){

			$sample_final_date = $this->Workflow->find('all',array('fields'=>'tran_date','conditions'=>array('stage_smpl_flag'=>'FG','org_sample_code IS'=>$Sample_code)))->first();
			$sample_final_date['tran_date'] = date('d/m/Y');//taking current date bcoz creating pdf before grading for preview.
			
			$this->set('sample_final_date',$sample_final_date['tran_date']);
			$this->set('test_report',$test_report);

			// Call to function for generate pdf file,
			// change generate pdf file name,
			$current_date = date('d-m-Y');
			
			$test_report_name = 'grade_report_'.$sample_code1.'.pdf';

			// NEW CODE ADDED END
		}

		$this->set('test_finalized_date',$test_finalized_date['tran_date']);
		$this->set('sample_details',$sample_details);
		$this->set('sample_allocated_test',$sample_allocated_test);
		$this->set('test_fields',$test_fields);
		$this->set('filled_test_values',$filled_test_values);
		$current_date = date('d/m/Y');
		$pdfname = $sample_details['f_name'].'-'.$current_date.'-'.$chemist_code;
		//call to the pdf creaation common method
		$this->callTcpdf($this->render(),'I');
	
	}
	
	
	
	
	// This function is used for to genrate chemist test report,
	public function chemistTestReport_BAK() {

		$this->viewBuilder()->setLayout('pdf_layout');
		$this->loadModel('ActualTestData');
		$this->loadModel('TestFormula');
		$this->loadModel('TestFields');
		$this->loadModel('Workflow');
		$this->loadModel('MSampleAllocate');
		$conn = ConnectionManager::get('default');

		$chemist_code = $this->Session->read('ABchmist_sample_code12');
		$commodity_code = $this->Session->read('ABcommodity_code12');

		$alloc_user1 = $_SESSION['user_code'];

		$query = $conn->execute("SELECT wf.stage_smpl_cd,
										usr.f_name,
										usr.l_name,
										mcc.category_name,
										mc.commodity_name,
										mst.sample_type_desc,
										msa.recby_ch_date,
										usr.role,
										roo.ro_office FROM sample_inward AS si
								INNER JOIN workflow AS wf ON wf.org_sample_code=si.org_sample_code
								INNER JOIN m_commodity_category AS mcc ON mcc.category_code = si.category_code
								INNER JOIN m_commodity AS mc ON mc.commodity_code = si.commodity_code
								INNER JOIN m_sample_type AS mst ON mst.sample_type_code = si.sample_type_code
								INNER JOIN m_sample_allocate AS msa ON msa.chemist_code = wf.stage_smpl_cd
								INNER JOIN dmi_users AS usr ON usr.id = wf.src_usr_cd
								INNER JOIN dmi_ro_offices AS roo ON roo.id = usr.posted_ro_office
								WHERE wf.stage_smpl_cd='$chemist_code' AND wf.stage_smpl_flag='FT'");

		$sample_details = $query->fetchAll('assoc');
		$sample_details = $sample_details[0];


		$test_finalized_date = $this->Workflow->find('all',array('fields'=>array('tran_date'),'conditions'=>array('stage_smpl_cd IS'=>$chemist_code,'stage_smpl_flag'=>'FT')))->first();

		$sample_allocated_test = $this->chemistAllocatedTests($chemist_code,$commodity_code,$alloc_user1);

		foreach ($sample_allocated_test as $each_test_code) {

			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			/*	$test_fields[] = $this->TestFields->find('all',array('JOINs' =>array(array('table' =>'m_fields','alias' =>'mf','type' =>'INNER','conditions' =>array('mf.field_code=TestFields.field_code'))),	//
			//																	'fields' => array('TestFields.field_value','mf.field_name','TestFields.field_unit','TestFields.test_code'),						//
			//																	'conditions'=>array('TestFields.test_code' =>$each_test_code['test_code'])));*/													//
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

			$query = $conn->execute("SELECT tf.field_value,mf.field_name,tf.field_unit,tf.test_code
										FROM test_fields AS tf
										INNER JOIN m_fields AS mf ON mf.field_code=tf.field_code
										WHERE tf.test_code =".$each_test_code['test_code']);

			$test_fields[] = $query->fetchAll('assoc');

			$filled_test_values[] = $this->ActualTestData->find('all',array('conditions'=>array('test_code IS' =>$each_test_code['test_code'],'chemist_code'=>$chemist_code)))->toArray();

		}

		$this->set('test_finalized_date',$test_finalized_date['tran_date']);
		$this->set('sample_details',$sample_details);
		$this->set('sample_allocated_test',$sample_allocated_test);
		$this->set('test_fields',$test_fields);
		$this->set('filled_test_values',$filled_test_values);
		$current_date = date('d/m/Y');
		$pdfname = $sample_details['f_name'].'-'.$current_date.'-'.$chemist_code;

		//$final_reports = $this->download_report_pdf('chemist_test_report',$pdfname);

		//call to the pdf creaation common method
		$this->callTcpdf($this->render(),'I');

	}


}
?>
