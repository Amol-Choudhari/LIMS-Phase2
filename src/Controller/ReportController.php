<?php

namespace App\Controller;

use Cake\Datasource\ConnectionManager;
use DateTime;
use App\Controller\Component\ReportCustomComponent;
use Reportico\Engine\Builder;

class ReportController extends AppController
{

	var $name = 'Report';
	public function initialize(): void
	{
		parent::initialize();

		//validate
		$this->authenticateUser();

		//Layouts
		$this->viewBuilder()->setLayout('admin_dashboard');
		$this->viewBuilder()->setHelpers(['Form', 'Html']);

		//Components
		$this->loadComponent('Customfunctions');
		$this->loadComponent('Paginator');

		//Models
		$this->loadModel('MCommodityCategory');
		$this->loadModel('Workflow');
		$this->loadModel('MFinYear');
		$this->loadModel('MSampleType');
		$this->loadModel('MLabel');
		$this->loadModel('MReport');
		$this->loadModel('MSampleAllocate');
		$this->loadModel('MCommodity');
		$this->loadModel('MTest');
		$this->loadModel('MLab');
		$this->loadModel('DmiUsers');
		$this->loadModel('DmiUserRoles');
		$this->loadModel('SampleInward');
		$this->loadModel('TestFields');
		$this->loadModel('MTest');
		$this->loadModel('FinalTestResult');
		$this->loadModel('ActualTestData');
		$this->loadModel('CommGrade');
		$this->loadModel('MSampleRegObs');
		$this->loadModel('SampleInwardDetails');
		$this->loadModel('MReportLab');
		$this->loadModel('MTestType');
		$this->loadModel('MTestMethod');
		$this->loadModel('TestFormula');
		$this->loadModel('InwSampleFields');
		$this->loadModel('DmiRoOffices');
		$this->loadModel('MPhyApperance');
		$this->loadModel('MSampleCondition');
		$this->loadModel('MParCondition');
		$this->loadModel('MHomogenization');
		$this->loadModel('MGeneralObs');
		$this->loadModel('MUnitWeight');
		$this->loadModel('MGradeDesc');
		$this->loadModel('MLocation');
	}
	
	//to validate login user
	public function authenticateUser(){

		$this->loadModel('DmiUserRoles');
		$user_access = $this->DmiUserRoles->find('all',array('conditions'=>array('user_email_id IS'=>$this->Session->read('username'))))->first();

		if (!empty($user_access)) {
			//proceed
		} else {
			$this->customAlertPage("Sorry.. You don't have permission to view this page OR Your session is expired");
			exit;
		}
	}

	public function index()
	{
		$this->Session->delete('my_report_title_url');
		$this->Session->delete('my_report_title');

		$role = $_SESSION['role'];
		$conn = ConnectionManager::get('default');

		$qlabel = $conn->execute("SELECT ml.label_desc,ml.label_code FROM m_label ml
		INNER JOIN m_reportlabel mrl ON ml.label_code = mrl.label_code
		INNER JOIN m_report mr ON ml.label_code = mr.label_code
		WHERE mrl.role = '$role' AND ml.label_code != '14' GROUP BY ml.label_desc,ml.label_code ORDER BY ml.label_desc");
		$recordlabels = $qlabel->fetchAll('assoc');
		$this->set('recordlabels', $recordlabels);

		$q = $conn->execute("SELECT mr.report_code,mr.report_desc,mrl.role,ml.label_desc,mr.label_code FROM m_report mr
		INNER JOIN m_reportlabel mrl ON mr.report_code = mrl.report_code AND mrl.display = 'Y'
		INNER JOIN m_label ml ON mrl.label_code = ml.label_code AND mr.label_code = ml.label_code
		WHERE mrl.role = '$role' AND mr.display = 'Y' GROUP BY mr.report_code,ml.label_desc,mrl.role,mr.label_code ORDER BY mr.report_desc");
		$records = $q->fetchAll('assoc');
		$this->set('records', $records);
	}

	public function formFilter()
	{
		$title = $this->Session->read('my_report_title_url');//$title="test-report-for-commodity";
		$report_name = $this->Session->read('my_report_title');
		$this->set('title', $title);
		$this->set('report_name', $report_name);

		//Set Sample Type Value
		$querySample = $this->MSampleType->find('list', [
			'keyField' => 'sample_type_code',
			'valueField' => 'sample_type_desc',
		])
			->select(['sample_type_code', 'sample_type_desc'])->where(['display' => 'Y'])->order(['sample_type_desc' => 'ASC']);
		$samples = $querySample->toArray();
		$this->set('samples', $samples);

		$con = ConnectionManager::get('default');
		$query = $con->execute("SELECT sample_type_code, sample_type_desc FROM m_sample_type WHERE display = 'Y'");
		$samples_type = $query->fetchAll('assoc');
		$query->closeCursor();
		$this->set('samples_type', $samples_type);

		//Set All Commodity
		$queryCommodity = $this->MCommodity->find('list', [
			'keyField' => 'commodity_code',
			'valueField' => 'commodity_name',
		])
			->select(['commodity_code', 'commodity_name'])->where(['display' => 'Y'])->order(['commodity_name' => 'ASC']);
		$commodity = $queryCommodity->toArray();
		$this->set('commodity', $commodity);

		//To get User Flag as per Report Title
		$user_flags = $this->getLabName($report_name);
		$this->set('user_flags', $user_flags);
	}

	// To set Report Title in Session variable
	public function setTitleInSession()
	{
		$this->Session->write('my_report_title', $_POST['labelName']);
		$labelR = strtolower(str_replace([' ', '/', '(', ')', '&', ',', '.'], '-', $_POST['labelName']));
		$report_desc = substr($labelR, -1, 1);

		if ($report_desc == '-') {

			$report_str = substr($labelR, 0, strlen($labelR) - 1);
		} else {

			$report_str = $labelR;
		}
		$this->Session->write('my_report_title_url', $report_str);
		exit;
	}

	//To get Lab Name dropdown as per Report Title
	public function getLabName($report_name)
	{
		$str1 = "";
		$this->loadModel('MReportLab');
		if ($_SESSION['user_flag'] == 'RAL') {
			if ($_SESSION['role'] == 'Inward Officer' && $report_name == "Sample received from RO/SO/RAL/CAL") {
				$str = "SELECT ml.user_flag AS user_flag FROM dmi_user_roles AS ml 
						INNER JOIN dmi_users AS du ON du.email=ml.user_email_id
						INNER JOIN m_reportlabel AS mrl ON mrl.role=du.role
						INNER JOIN m_report AS mr ON mrl.report_code=mr.report_code AND ml.user_flag IN('RO','SO','RAL') AND mr.report_desc='$report_name' GROUP BY  ml.user_flag";
			} else {
				$str = "SELECT ml.user_flag AS user_flag FROM dmi_user_roles AS ml 
						INNER JOIN dmi_users AS du ON du.email=ml.user_email_id
						INNER JOIN m_reportlabel AS mrl ON mrl.role=du.role
						INNER JOIN m_report AS mr ON mrl.report_code=mr.report_code AND ml.user_flag='" . $_SESSION['user_flag'] . "' AND  mr.report_desc='$report_name' GROUP BY  ml.user_flag";
			}
		} else if ($_SESSION['user_flag'] == 'CAL') {
			if ($_SESSION['role'] == 'DOL' && $report_name == "Sample received from RO/SO/RAL/CAL" || $report_name == "Sample Register") {
				$str = "SELECT ml.user_flag AS user_flag FROM dmi_user_roles AS ml 
						INNER JOIN dmi_users AS du ON du.email=ml.user_email_id
						INNER JOIN m_reportlabel AS mrl ON mrl.role=du.role
						INNER JOIN m_report AS mr ON mrl.report_code=mr.report_code AND ml.user_flag IN('RO','SO','RAL','CAL') AND mr.report_desc='$report_name' GROUP BY  ml.user_flag";
			} else if ($_SESSION['role'] == 'DOL' && $report_name == "Samples Analyzed(Count)" || $report_name == "Samples Pending for Dispatch" || $report_name == "No. of Check, Private & Research Samples analyzed by RALs" || $report_name == "Samples Alloted/Analyzed/Pending Report(RAL/CAL)" || $report_name == "Performance Report of RAL/CAL") {
				$str = "SELECT ml.user_flag AS user_flag FROM dmi_user_roles AS ml 
						INNER JOIN dmi_users AS du ON du.email=ml.user_email_id
						INNER JOIN m_reportlabel AS mrl ON mrl.role=du.role
						INNER JOIN m_report AS mr ON mrl.report_code=mr.report_code AND ml.user_flag IN('RAL','CAL') AND mr.report_desc='$report_name' GROUP BY  ml.user_flag";
			} else if ($_SESSION['role'] == 'Admin' && $report_name == "Samples Accepted by Chemist For Testing" || $report_name == "Commodity-wise consolidated report of lab" || $report_name == "Commodity-wise Check & Challenged Samples Analysed" || $report_name == "Commodity-wise Check & Challenged Samples Analysed" || $report_name == "Time Taken for Analysis of Samples" || $report_name == "Category-wise Received Sample" || $report_name == "Brought forward,Analysed and carried forward of samples" || $report_name == "Sample Workflow" || $report_name == "Samples alloted to Chemist for testing" || $report_name == "Tested Samples" || $report_name == "Test result submitted by chemist" || $report_name == "Test result submitted by chemist with readings" || $report_name == "Sample Analyzed by Chemist" || $report_name == "Chemist –wise sample analysis" || $report_name == "Chemistwise Sample Pending & Analyze") {
				$str = "SELECT ml.user_flag AS user_flag FROM dmi_user_roles AS ml 
						INNER JOIN dmi_users AS du ON du.email=ml.user_email_id
						INNER JOIN m_reportlabel AS mrl ON mrl.role=du.role
						INNER JOIN m_report AS mr ON mrl.report_code=mr.report_code AND ml.user_flag IN('RAL','CAL') AND mr.report_desc='$report_name' GROUP BY  ml.user_flag";
			} else if ($_SESSION['role'] == 'Admin' && $report_name == "Sample Inward with Details" || $report_name == "Sample received from RO/SO/RAL/CAL" || $report_name == "Rejected Samples" || $report_name == "Forwarded sample") {
				$str = "SELECT ml.user_flag AS user_flag FROM dmi_user_roles AS ml 
						INNER JOIN dmi_users AS du ON du.email=ml.user_email_id
						INNER JOIN m_reportlabel AS mrl ON mrl.role=du.role
						INNER JOIN m_report AS mr ON mrl.report_code=mr.report_code AND ml.user_flag IN('RO','SO','RAL','CAL') AND mr.report_desc='$report_name' GROUP BY  ml.user_flag";
			} else if ($_SESSION['role'] == 'DOL' && $report_name == "Details of Sample Analyzed by Chemist"  || $report_name == "Monthly report of Carry Forward and Brought Forward"  || $report_name == "Information of Annexure E along with MPR division wise"  || $report_name == "Details of samples analyzed by RALs Annexure B" || $report_name == "Bifercation of samples analyzed by RAL"  || $report_name == "Monthly status of analyzed of Check samples and pending samples of RAL Annexure E"  || $report_name == "Commodity wise details of samples analyzed by RAL Annexure E" || $report_name == "Statement of Check Samples Brought forward/ Carry forward Annexure I"  || $report_name == "Time Taken Report"  || $report_name ==  "Sample allotment sheet of coding section to the I/C Analytical section of CAL, Nagpur"   || $report_name ==  "Sample allotment sheet of I/C Analytical section issued to the Chemist for analysis" || $report_name == "Perticulars of samples received and analyzed by RAL Annexure D"  || $report_name ==  "Common Report") {
				$str = "SELECT ml.lab_name AS user_flag FROM m_lab AS ml 
				INNER JOIN m_report_lab AS mrl ON mrl.lab_code=ml.lab_code
				INNER JOIN m_report AS mr ON mrl.report_code=mr.report_code AND ml.lab_name IN('RAL','CAL') AND  mr.report_desc='$report_name' ORDER BY lab_name";
			
			} else {
				$str = "SELECT ml.user_flag AS user_flag FROM dmi_user_roles AS ml 
						INNER JOIN dmi_users AS du ON du.email=ml.user_email_id
						INNER JOIN m_reportlabel AS mrl ON mrl.role=du.role
						INNER JOIN m_report AS mr ON mrl.report_code=mr.report_code AND ml.user_flag='" . $_SESSION['user_flag'] . "' AND mr.report_desc='$report_name' GROUP BY  ml.user_flag";
			}
		} else if ($_SESSION['user_flag'] == 'HO') {
			$str = "SELECT ml.lab_name AS user_flag FROM m_lab AS ml 
					INNER JOIN m_report_lab AS mrl ON mrl.lab_code=ml.lab_code
					INNER JOIN m_report AS mr ON mrl.report_code=mr.report_code AND mr.report_desc='$report_name' ORDER BY lab_name";
		} else {
			$str = "SELECT ml.user_flag AS user_flag FROM dmi_user_roles AS ml 
					INNER JOIN dmi_users AS du ON du.email=ml.user_email_id
					INNER JOIN m_reportlabel AS mrl ON mrl.role=du.role
					INNER JOIN m_report AS mr ON mrl.report_code=mr.report_code AND ml.user_flag='" . $_SESSION['user_flag'] . "' AND du.role='" . $_SESSION['role'] . "' AND mr.report_desc='$report_name' GROUP BY ml.user_flag";
		}
		//  pr($str);
		$con = ConnectionManager::get('default');
		$query = $con->execute($str);
		$sample = $query->fetchAll('assoc');
		return $sample;
	}


	public function rejectedSamples()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
			$from_date = $from_date->format('Y/m/d');
			$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
			$to_date = $to_date->format('Y/m/d');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user_id = $_SESSION['user_code'];

			$report_name = "Rejected Samples";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;
			$sql = "";
			if ($role == 'Inward Officer') {
				$query = ReportCustomComponent::getIoRejectSample($from_date, $to_date, $posted_ro_office, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT sr_no,received_date,reject_date,fullname,labname,org_sample_code,commodity_name,sample_type_desc,counts,report_date FROM temp_reportico_io_reject_sample where user_id = '$user_id'";
				}
			}
			if ($role == 'RO Officer') {
				$query = ReportCustomComponent::getRoRejectSample($from_date, $to_date, $posted_ro_office, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT sr_no,received_date,reject_date,fullname,labname,org_sample_code,commodity_name,sample_type_desc,counts,report_date FROM temp_reportico_ro_reject_sample where user_id = '$user_id'";
				}
			}
			if ($role == 'RAL/CAL OIC') {
				$query = ReportCustomComponent::getRalCalOicRejectSample($from_date, $to_date, $posted_ro_office, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT sr_no,received_date,reject_date,fullname,labname,org_sample_code,commodity_name,sample_type_desc,counts,report_date FROM temp_reportico_ral_cal_oic_reject_sample where user_id = '$user_id'";
				}
			}

			if ($role == 'RO/SO OIC') {
				$query = ReportCustomComponent::getRoSoOicRejectSample($from_date, $to_date, $posted_ro_office, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT sr_no,received_date,reject_date,fullname,labname,org_sample_code,commodity_name,sample_type_desc,counts,report_date FROM temp_reportico_ro_so_oic_reject_sample where user_id = '$user_id'";
				}
			}

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDolRejectSample($from_date, $to_date, $posted_ro_office, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT sr_no,received_date,reject_date,fullname,labname,org_sample_code,commodity_name,sample_type_desc,counts,report_date FROM temp_reportico_dol_reject_sample where user_id = '$user_id'";
				}
			}

			if ($role == 'Head Office') {
				$query = ReportCustomComponent::getHoRejectSample($from_date, $to_date, $posted_ro_office, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT sr_no,received_date,reject_date,fullname,labname,org_sample_code,commodity_name,sample_type_desc,counts,report_date FROM temp_reportico_ho_reject_sample where user_id = '$user_id'";
				}
			}
			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminRejectSample($from_date, $to_date, $posted_ro_office, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT sr_no,received_date,reject_date,fullname,labname,org_sample_code,commodity_name,sample_type_desc,counts,report_date FROM temp_reportico_admin_reject_sample where user_id = '$user_id'";
				}
			}
			if ($sql == "") {
				return $this->redirect('/report/index');
			}
			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)
				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("received_date")->justify("center")->label("Received Date")
				->column("reject_date")->justify("center")->label("Rejected Date")
				->column("fullname")->justify("center")->label("Received By")
				->column("labname")->justify("center")->label("Lab Name")
				->column("org_sample_code")->justify("center")->label("Sample Code")
				->column("commodity_name")->justify("center")->label("Commodity Name")
				->column("sample_type_desc")->justify("center")->label("Sample Type")
				->column("counts")->hide()

				// ->to('CSV') //Auto download excel file		

				->group("report_date")
				->header("report_date")
				->customHeader("Sample Recieved in the Period From : {$from_date} To : {$to_date}", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				//->customTrailer("{$name} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 10px;margin-bottom:10px;")
				//->customTrailer("({$email}) ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 25px;margin-bottom:10px;")
				//->customTrailer("{$role} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 45px; margin-bottom:10px;")


				->group("labname")
				->header("labname")

				->group("sample_type_desc")
				->header("sample_type_desc")

				->customTrailer("Total Number of Rejected Samples : {counts} ", "font-size: 10pt; font-weight: bold; width: 100%; margin-top: 10px;margin-bottom:10px;")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")
				//->footer("Time: date('Y-m-d H:i:s')", "font-size: 8pt; text-align: right; font-style: italic; width: 100%; margin-top: 55px; padding-bottom:60px;")

				 ->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "border-width: 1 0 0 0; top: 0px; font-size: 8pt; margin: 2px 0px 0px 0px; font-style: italic; margin-top: 55px;")
				->execute();
		}
	}

	public function sampleRegister()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
			$from_date = $from_date->format('Y/m/d');
			$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
			$to_date = $to_date->format('Y/m/d');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$category = $this->request->getData('Category');
			$commodity = $this->request->getData('Commodity');
			$sample = $this->request->getData('sample_type');
			$user_id = $_SESSION['user_code'];

			$report_name = "Sample Register";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'Inward Officer') {
				$query = ReportCustomComponent::getIoSampleRegister($from_date, $to_date, $posted_ro_office, $lab, $sample, $commodity, $category, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, letr_date, category_name, labname, letr_ref_no, org_sample_code, stage_sample_code, sample_qnt, unit_weight, par_condition_desc, sample_total_qnt, received_date, commodity_name, commodity_code, category_code, sample_type_desc, loc_id, dispatch_date, grading_date, remark, user_flag, report_date, counts
				FROM temp_reportico_io_sample_register WHERE user_id = '$user_id'";
				}
			}
			if ($role == 'RO Officer') {
				$query = ReportCustomComponent::getRoSampleRegister($from_date, $to_date, $posted_ro_office, $lab, $commodity, $category, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, letr_date, category_name, labname, letr_ref_no, org_sample_code, stage_sample_code, sample_qnt, unit_weight, par_condition_desc, sample_total_qnt, received_date, commodity_name, commodity_code, category_code, sample_type_desc, loc_id, dispatch_date, grading_date, remark, user_flag, report_date, counts
				FROM temp_reportico_ro_sample_register WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'SO Officer') {
				$query = ReportCustomComponent::getSoSampleRegister($from_date, $to_date, $posted_ro_office, $lab, $commodity, $category, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, letr_date, category_name, labname, letr_ref_no, org_sample_code, stage_sample_code, sample_qnt, unit_weight, par_condition_desc, sample_total_qnt, received_date, commodity_name, commodity_code, category_code, sample_type_desc, loc_id, dispatch_date, grading_date, remark, user_flag, report_date, counts
				FROM temp_reportico_so_sample_register WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'RAL/CAL OIC') {
				$query = ReportCustomComponent::getRalCalOicSampleRegister($from_date, $to_date, $posted_ro_office, $lab, $sample, $commodity, $category, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, letr_date, category_name, labname, letr_ref_no, org_sample_code, stage_sample_code, sample_qnt, unit_weight, par_condition_desc, sample_total_qnt, received_date, commodity_name, commodity_code, category_code, sample_type_desc, loc_id, dispatch_date, grading_date, remark, user_flag, report_date, counts
				FROM temp_reportico_ral_cal_oic_sample_register WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'RO/SO OIC') {
				$query = ReportCustomComponent::getRoSoOicSampleRegister($from_date, $to_date, $posted_ro_office, $lab, $commodity, $category, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, letr_date, category_name, labname, letr_ref_no, org_sample_code, stage_sample_code, sample_qnt, unit_weight, par_condition_desc, sample_total_qnt, received_date, commodity_name, commodity_code, category_code, sample_type_desc, loc_id, dispatch_date, grading_date, remark, user_flag, report_date, counts
				FROM temp_reportico_ro_so_oic_sample_register WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDolSampleRegister($from_date, $to_date, $posted_ro_office, $lab, $commodity, $sample, $category, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, letr_date, category_name, labname, letr_ref_no, org_sample_code, stage_sample_code, sample_qnt, unit_weight, par_condition_desc, sample_total_qnt, received_date, commodity_name, commodity_code, category_code, sample_type_desc, loc_id, dispatch_date, grading_date, remark, user_flag, report_date, counts
				FROM temp_reportico_dol_sample_register WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Head Office') {
				$query = ReportCustomComponent::getHoSampleRegister($from_date, $to_date, $posted_ro_office, $lab, $commodity, $sample, $category, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, letr_date, category_name, labname, letr_ref_no, org_sample_code, stage_sample_code, sample_qnt, unit_weight, par_condition_desc, sample_total_qnt, received_date, commodity_name, commodity_code, category_code, sample_type_desc, loc_id, dispatch_date, grading_date, remark, user_flag, report_date, counts
				FROM temp_reportico_ho_sample_register WHERE user_id = '$user_id'";
				}
			}
			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminSampleRegister($from_date, $to_date, $posted_ro_office, $lab, $commodity, $sample, $category, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, letr_date, category_name, labname, letr_ref_no, org_sample_code, stage_sample_code, sample_qnt, unit_weight, par_condition_desc, sample_total_qnt, received_date, commodity_name, commodity_code, category_code, sample_type_desc, loc_id, dispatch_date, grading_date, remark, user_flag, report_date, counts
				FROM temp_reportico_admin_sample_register WHERE user_id = '$user_id'";
				}
			}
			if ($sql == "") {
				return $this->redirect('/report/index');
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("letr_date")->justify("center")->label("Date of Receipt Sample")
				->column("category_name")->justify("center")->label("Nature of Commodity")
				->column("labname")->justify("center")->label("Source of Sample")
				->column("letr_ref_no")->justify("center")->label("Reference/File No")
				->column("org_sample_code")->justify("center")->label("Sample Code")
				->column("sample_qnt")->justify("center")->label("Quantity of Sample")
				->column("sample_type_desc")->justify("center")->label("Sample Type")
				->column("unit_weight")->justify("center")->label("Unit")
				->column("par_condition_desc")->justify("center")->label("Condition of Sealed")
				->column("sample_total_qnt")->justify("center")->label("Code No. of CAL")
				->column("received_date")->justify("center")->label("Date of Issuse of Sample")
				->column("dispatch_date")->justify("center")->label("Date of Reciept of Result")
				->column("user_flag")->hide()
				->column("stage_sample_code")->hide()
				->column("commodity_code")->hide()
				->column("category_code")->hide()
				->column("loc_id")->hide()
				->column("counts")->hide()

				// ->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customHeader("Sample Recieved in the Period From : {$from_date} To : {$to_date}", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				->group("commodity_name")
				->header("commodity_name")

				->group("sample_type_desc")
				->header("sample_type_desc")

				->customTrailer("Total Number of Samples : {counts} ", "")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}

	public function sampleRegistrationDetails()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
			$from_date = $from_date->format('Y/m/d');
			$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
			$to_date = $to_date->format('Y/m/d');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user_id = $_SESSION['user_code'];

			$report_name = "Sample Registration Details";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'RO Officer') {
				$query = ReportCustomComponent::getRoSampleRegistration($from_date, $to_date, $posted_ro_office, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, grade, commodity_name, name_address_packer, lot_no, pack_date, pack_size, tbl, shop_name_address, parcel_size, smpl_drwl_dt, name_officer, org_sample_code, dispatch_date, counts, report_date, lab_or_office_name FROM temp_reportico_ro_sample_registration WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'SO Officer') {
				$query = ReportCustomComponent::getSoSampleRegistration($from_date, $to_date, $posted_ro_office,  $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, grade, commodity_name, name_address_packer, lot_no, pack_date, pack_size, tbl, shop_name_address, parcel_size, smpl_drwl_dt, name_officer, org_sample_code, dispatch_date, counts, report_date, lab_or_office_name FROM temp_reportico_so_sample_registration WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'RAL/CAL OIC') {
				$query = ReportCustomComponent::getRalCalOicSampleRegistration($from_date, $to_date, $posted_ro_office,  $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, grade, commodity_name, name_address_packer, lot_no, pack_date, pack_size, tbl, shop_name_address, parcel_size, smpl_drwl_dt, name_officer, org_sample_code, dispatch_date, counts, report_date, lab_or_office_name FROM temp_reportico_ral_cal_oic_sample_registration WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'RO/SO OIC') {
				$query = ReportCustomComponent::getRoSoOicSampleRegistration($from_date, $to_date, $posted_ro_office,  $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, grade, commodity_name, name_address_packer, lot_no, pack_date, pack_size, tbl, shop_name_address, parcel_size, smpl_drwl_dt, name_officer, org_sample_code, dispatch_date, counts, report_date, lab_or_office_name FROM temp_reportico_ro_so_oic_sample_registration WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDolSampleRegistration($from_date, $to_date, $posted_ro_office,  $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, grade, commodity_name, name_address_packer, lot_no, pack_date, pack_size, tbl, shop_name_address, parcel_size, smpl_drwl_dt, name_officer, org_sample_code, dispatch_date, counts, report_date, lab_or_office_name FROM temp_reportico_dol_sample_registration WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminSampleRegistration($from_date, $to_date, $posted_ro_office,  $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, grade, commodity_name, name_address_packer, lot_no, pack_date, pack_size, tbl, shop_name_address, parcel_size, smpl_drwl_dt, name_officer, org_sample_code, dispatch_date, counts, report_date, lab_or_office_name FROM temp_reportico_admin_sample_registration WHERE user_id = '$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("grade")->justify("center")->label("Grade")
				->column("commodity_name")->justify("center")->label("Name of Commodity")
				->column("name_address_packer")->justify("center")->label("Name & Address of Autorised Packer")
				->column("lot_no")->justify("center")->label("Lot No")
				->column("pack_date")->justify("center")->label("Date of Packing")
				->column("pack_size")->justify("center")->label("Pack Size")
				->column("tbl")->justify("center")->label("TBL")
				->column("shop_name_address")->justify("center")->label("Name & Address of Shop/Packer premises from where sample drawn")
				->column("parcel_size")->justify("center")->label("Sample Size")
				->column("smpl_drwl_dt")->justify("center")->label("Date of Drawl Sample")
				->column("name_officer")->justify("center")->label("Name of Officer drawn the Sample")
				->column("org_sample_code")->justify("center")->label("Code No.")
				->column("dispatch_date")->justify("center")->label("Date of Sending Sample to RAL")
				->column("counts")->hide()

				/**
				 * Comment by Shweta Apale 25-10-2021
				 * These column were used in previous code but not required because this report is only for Sample Registratio Details
				 * Removed Column from query
				 *  si.ral_anltc_rslt_rcpt_dt,si.anltc_rslt_chlng_flg,si.misgrd_param_value,si.misgrd_report_issue_dt,si.misgrd_reason,
				 * si.chlng_smpl_disptch_cal_dt,si.grading_date,si.remark,si.grade, si.ral_lab_code, si.cal_anltc_rslt_rcpt_dt
				 * Commenting the unused columns
				 */
				// ->column("ral_anltc_rslt_rcpt_dt")->justify("center")->label("Date of reciept of result of RAL")
				// ->column("anltc_rslt_chlng_flg")->justify("center")->label("Parameter wise analytical result of RALs")
				// ->column("misgrd_param_value")->justify("center")->label("Whether Pass/Misgraded")
				// ->column("misgrd_report_issue_dt")->justify("center")->label("Date of issue of misgrading report/warming")
				// ->column("misgrd_reason")->justify("center")->label("Whether misgrading report challanged")
				// ->column("chlng_smpl_disptch_cal_dt")->justify("center")->label("Date of sending challenged sample to CAL")
				// ->column("cal_anltc_rslt_rcpt_dt")->justify("center")->label("Date of receipt of result from CAL")


				// ->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customHeader("Sample Recieved in the Period From : {$from_date} To : {$to_date}", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				->group("lab_or_office_name")
				->header("lab_or_office_name")
				->customTrailer("Total Number of Samples : {counts} ", "")

				->group("commodity_name")
				->header("commodity_name")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}

	public function sampleReceivedFromRoSoRalCal()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$label = $this->request->getData('label_name');
			$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
			$from_date = $from_date->format('Y/m/d');
			$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
			$to_date = $to_date->format('Y/m/d');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$category = $this->request->getData('Category');
			$commodity = $this->request->getData('Commodity');
			$user_id = $_SESSION['user_code'];

			$report_name = "Samples Received from RO/SO/RAL/CAL";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == "Inward Officer") {
				$query = ReportCustomComponent::getIoSampleReceivedRoSoRalCal($from_date, $to_date, $commodity, $posted_ro_office, $lab, $role, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, received_date, org_sample_code, category_name, commodity_name, sample_type_desc, role, ro_office, user_flag, lab_name, report_date, counts FROM temp_reportico_io_sample_received_rosoralcal WHERE user_id = '$user_id'";
				}
			}

			if ($role == "RAL/CAL OIC") {
				$query = ReportCustomComponent::getRalCalOicSampleReceivedRoSoRalCal($from_date, $to_date, $commodity, $posted_ro_office, $lab, $role, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, received_date, org_sample_code, category_name, commodity_name, sample_type_desc, role, ro_office, user_flag, lab_name, report_date, counts FROM temp_reportico_ral_cal_oic_sample_received_rosoralcal WHERE user_id = '$user_id'";
				}
			}

			if ($role == "DOL") {
				$query = ReportCustomComponent::getDolSampleReceivedRoSoRalCal($from_date, $to_date, $commodity, $posted_ro_office, $lab, $role, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, received_date, org_sample_code, category_name, commodity_name, sample_type_desc, role, ro_office, user_flag, lab_name, report_date, counts FROM temp_reportico_dol_sample_received_rosoralcal WHERE user_id = '$user_id'";
				}
			}

			if ($role == "Head Office") {
				$query = ReportCustomComponent::getHoSampleReceivedRoSoRalCal($from_date, $to_date, $commodity, $posted_ro_office, $lab, $role, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, received_date, org_sample_code, category_name, commodity_name, sample_type_desc, role, ro_office, user_flag, lab_name, report_date, counts FROM temp_reportico_ho_sample_received_rosoralcal WHERE user_id = '$user_id'";
				}
			}
			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminSampleReceivedRoSoRalCal($from_date, $to_date, $commodity, $posted_ro_office, $lab, $role, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, received_date, org_sample_code, category_name, commodity_name, sample_type_desc, role, ro_office, user_flag, lab_name, report_date, counts FROM temp_reportico_admin_sample_received_rosoralcal WHERE user_id = '$user_id'";
				}
			}
			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("received_date")->justify("center")->label("Received Date")
				->column("org_sample_code")->justify("center")->label("Sample Code")
				->column("category_name")->justify("center")->label("Category Name")
				->column("sample_type_desc")->justify("center")->label("Sample Type")
				->column("ro_office")->hide()
				->column("user_flag")->hide()
				->column("role")->hide()
				->column("counts")->hide()

				// ->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customHeader("Sample Recieved in the Period From : {$from_date} To : {$to_date}", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				->group("commodity_name")
				->header("commodity_name")

				->group("sample_type_desc")
				->header("sample_type_desc")
				->customTrailer("Total Number of Samples : {counts} ", "")

				->group("lab_name")
				->header("lab_name")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}

	public function samplesAcceptedByChemistForTesting()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
			$from_date = $from_date->format('Y/m/d');
			$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
			$to_date = $to_date->format('Y/m/d');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user = $this->request->getData('user');
			$user_id = $_SESSION['user_code'];

			$report_name = "Samples Accepted by Chemist For Testing";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'Inward Officer') {
				$query = ReportCustomComponent::getIoSampleAcceptedByChemistForTesting($from_date, $to_date, $posted_ro_office, $lab, $user, $ral_lab, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, received_date, org_sample_code, commodity_name, sample_type_desc, counts,ro_office, report_date, chemist_name, lab_name FROM temp_reportico_io_sample_accepted_chemist_testing WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'RAL/CAL OIC') {
				$query = ReportCustomComponent::getRalCalOicSampleAcceptedByChemistForTesting($from_date, $to_date, $posted_ro_office, $lab, $user, $ral_lab, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, received_date, org_sample_code, commodity_name, sample_type_desc, counts,ro_office, report_date, chemist_name, lab_name FROM temp_reportico_ral_cal_oic_sample_accepted_chemist_testing WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDolSampleAcceptedByChemistForTesting($from_date, $to_date, $posted_ro_office, $lab, $user, $ral_lab, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, received_date, org_sample_code, commodity_name, sample_type_desc, counts,ro_office, report_date, chemist_name, lab_name FROM temp_reportico_dol_sample_accepted_chemist_testing WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Head Office') {
				$query = ReportCustomComponent::getHoSampleAcceptedByChemistForTesting($from_date, $to_date, $posted_ro_office, $lab, $user, $ral_lab, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, received_date, org_sample_code, commodity_name, sample_type_desc, counts,ro_office, report_date, chemist_name, lab_name FROM temp_reportico_ho_sample_accepted_chemist_testing WHERE user_id = '$user_id'";
				}
			}
			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminSampleAcceptedByChemistForTesting($from_date, $to_date, $posted_ro_office, $lab, $user, $ral_lab, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, received_date, org_sample_code, commodity_name, sample_type_desc, counts,ro_office, report_date, chemist_name, lab_name FROM temp_reportico_admin_sample_accepted_chemist_testing WHERE user_id = '$user_id'";
				}
			}
			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("received_date")->justify("center")->label("Accepted Date")
				->column("org_sample_code")->justify("center")->label("Sample Code")
				->column("commodity_name")->justify("center")->label("Commodity Name")
				->column("sample_type_desc")->justify("center")->label("Sample Type")
				->column("counts")->hide()
				->column("ro_office")->hide()

				// ->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customHeader("Sample Recieved in the Period From : {$from_date} To : {$to_date}", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				->group("chemist_name")
				->header("chemist_name")

				->group("lab_name")
				->header("lab_name")

				->group("sample_type_desc")
				->header("sample_type_desc")

				->customTrailer("Total Number of accepted Samples by Chemist : {counts} ", "")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}

	public function samplesPendingForDispatch()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
			$from_date = $from_date->format('Y/m/d');
			$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
			$to_date = $to_date->format('Y/m/d');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user_id = $_SESSION['user_code'];

			$report_name = "Samples Pending for Dispatch";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'Inward Officer') {
				$query = ReportCustomComponent::getIoSamplePendingForDispatch($from_date, $to_date, $posted_ro_office, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT sr_no, received_date,org_sample_code, commodity_name, status,  sample_type_desc, report_date, counts FROM temp_reportico_io_sample_pending WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDolSamplePendingForDispatch($from_date, $to_date, $posted_ro_office, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT sr_no, received_date,org_sample_code, commodity_name, status,  sample_type_desc, report_date, counts FROM temp_reportico_dol_sample_pending WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Head Office') {
				$query = ReportCustomComponent::getHoSamplePendingForDispatch($from_date, $to_date, $posted_ro_office, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT sr_no, received_date,org_sample_code, commodity_name, status,  sample_type_desc, report_date, counts FROM temp_reportico_ho_sample_pending WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminSamplePendingForDispatch($from_date, $to_date, $posted_ro_office, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT sr_no, received_date,org_sample_code, commodity_name, status,  sample_type_desc, report_date, counts FROM temp_reportico_admin_sample_pending WHERE user_id = '$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("received_date")->justify("center")->label("Received Date")
				->column("org_sample_code")->justify("center")->label("Sample Code")
				->column("commodity_name")->justify("center")->label("Commodity Name")
				->column("sample_type_desc")->justify("center")->label("Sample Type")
				->column("status")->justify("center")->label("Status")
				->column("counts")->hide()

				// ->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customHeader("Sample Recieved in the Period From : {$from_date} To : {$to_date}", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				->group("sample_type_desc")
				->header("sample_type_desc")

				->customTrailer("Total Number of Samples : {counts} ", "")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}

	public function samplesAnalyzedCount()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
			$from_date = $from_date->format('Y/m/d');
			$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
			$to_date = $to_date->format('Y/m/d');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$sample_type = $this->request->getData('sample_type');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user_id = $_SESSION['user_code'];

			$report_name = "Samples Analyzed(Count)";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;
			$sql1 = "";
			if ($role == 'Inward Officer') {
				$query = ReportCustomComponent::getIoSampleAnalyzedCount($from_date, $to_date, $posted_ro_office, $sample_type, $lab, $ral_lab_no, $ral_lab_name);
				if ($sql1 == 1) {
					$sql1 = "SELECT  sr_no, sample_type_desc, commodity_name, count_samples, report_date, counts FROM temp_reportico_io_sample_analyzed WHERE user_id = '$user_id'";
				}
				if ($sql1 == "") {
					return $this->redirect("/report/index");
				}
				ini_set("include_path", reporticoReport);
				require_once("vendor/autoload.php");
				require_once("vendor/reportico-web/reportico/src/Reportico.php");

				Builder::build()
					->properties(["bootstrap_preloaded" => true])
					->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
					->title($report_name)

					->sql($sql1)

					->column("sr_no")->justify("center")->label("Sr. No.")
					->column("commodity_name")->justify("center")->label("Commodity Name")
					->column("count_samples")->justify("center")->label("No. of Sample Analyzed")
					->column("sample_type_desc")->justify("center")->label("Sample Type")
					->column("counts")->hide()

					// ->to('CSV') //Auto download excel file	

					->group("report_date")
					->header("report_date")
					->customHeader("Sample Recieved in the Period From : {$from_date} To : {$to_date}", "")
					->customTrailer("{$name} ", "")
					->customTrailer("({$email}) ", "")
					->customTrailer("{$role} ", "")

					->group("sample_type_desc")
					->header("sample_type_desc")

					->customTrailer("Total Number of Samples Analyzed : {counts} ", "")

					->page()
					->pagetitledisplay("TopOfFirstPage")

					->header($header, "")

					->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
					->execute();
			}
			$sql2 = "";
			if ($role == "RAL/CAL OIC") {
				$query = ReportCustomComponent::getRalCalOicSampleAnalyzedCount($from_date, $to_date, $posted_ro_office, $sample_type, $lab, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql2 = "SELECT  sr_no, sample_type_desc, commodity_name, count_samples, finalized, report_date, counts FROM temp_reportico_ral_sample_analyzed WHERE user_id = '$user_id'";
				}
				if ($sql2 == "") {
					return $this->redirect("/report/index");
				}
				ini_set("include_path", reporticoReport);
				require_once("vendor/autoload.php");
				require_once("vendor/reportico-web/reportico/src/Reportico.php");

				Builder::build()
					->properties(["bootstrap_preloaded" => true])
					->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
					->title("CAL/RAL Wise Sample Sent for Analysis Report")

					->sql($sql2)

					->column("sr_no")->justify("center")->label("Sr. No.")
					->column("commodity_name")->justify("center")->label("Commodity Name")
					->column("count_samples")->justify("center")->label("No. of Sample Forwarded")
					->column("finalized")->justify("center")->label("Finalized")
					->column("sample_type_desc")->justify("center")->label("Sample Type")
					->column("counts")->hide()

					// ->to('CSV') //Auto download excel file	

					->group("report_date")
					->header("report_date")
					->customHeader("Sample Recieved in the Period From : {$from_date} To : {$to_date}", "")
					->customTrailer("{$name} ", "")
					->customTrailer("({$email}) ", "")
					->customTrailer("{$role} ", "")

					->group("sample_type_desc")
					->header("sample_type_desc")

					->customTrailer("Total Number of Samples Analyzed : {counts} ", "")

					->page()
					->pagetitledisplay("TopOfFirstPage")

					->header($header, "")

					->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
					->execute();
			}

			$sql3 = "";
			if ($role == "RO/SO OIC") {
				$query = ReportCustomComponent::getRoSoOicSampleAnalyzedCount($from_date, $to_date, $posted_ro_office, $lab, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql3 = "SELECT  sr_no, sample_type_desc, commodity_name, count_samples, finalized, report_date, counts FROM temp_reportico_ro_so_oic_sample_analyzed WHERE user_id = '$user_id'";
				}
				if ($sql3 == "") {
					return $this->redirect("/report/index");
				}
				ini_set("include_path", reporticoReport);
				require_once("vendor/autoload.php");
				require_once("vendor/reportico-web/reportico/src/Reportico.php");

				Builder::build()
					->properties(["bootstrap_preloaded" => true])
					->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
					->title("CAL/RAL Wise Sample Sent for Analysis Report ")

					->sql($sql3)

					->column("sr_no")->justify("center")->label("Sr. No.")
					->column("commodity_name")->justify("center")->label("Commodity Name")
					->column("count_samples")->justify("center")->label("No. of Sample Forwarded")
					->column("finalized")->justify("center")->label("Finalized")
					->column("sample_type_desc")->justify("center")->label("Sample Type")
					->column("counts")->hide()

					// ->to('CSV') //Auto download excel file	

					->group("report_date")
					->header("report_date")
					->customHeader("Sample Recieved in the Period From : {$from_date} To : {$to_date}", "")
					->customTrailer("{$name} ", "")
					->customTrailer("({$email}) ", "")
					->customTrailer("{$role} ", "")

					->group("sample_type_desc")
					->header("sample_type_desc")

					->customTrailer("Total Number of Samples Analyzed : {counts} ", "")

					->page()
					->pagetitledisplay("TopOfFirstPage")

					->header($header, "")

					->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
					->execute();
			}

			$sql4 = "";
			if ($role == "DOL") {
				$query = ReportCustomComponent::getDolSampleAnalyzedCount($from_date, $to_date, $posted_ro_office, $lab, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql4 = "SELECT  sr_no, sample_type_desc, commodity_name, count_samples, finalized, report_date, counts FROM temp_reportico_dol_sample_analyzed WHERE user_id = '$user_id'";
				}
				if ($sql4 == "") {
					return $this->redirect("/report/index");
				}
				ini_set("include_path", reporticoReport);
				require_once("vendor/autoload.php");
				require_once("vendor/reportico-web/reportico/src/Reportico.php");

				Builder::build()
					->properties(["bootstrap_preloaded" => true])
					->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
					->title("CAL/RAL Wise Sample Sent for Analysis Report ")

					->sql($sql4)

					->column("sr_no")->justify("center")->label("Sr. No.")
					->column("commodity_name")->justify("center")->label("Commodity Name")
					->column("count_samples")->justify("center")->label("No. of Sample Forwarded")
					->column("finalized")->justify("center")->label("Finalized")
					->column("sample_type_desc")->justify("center")->label("Sample Type")
					->column("counts")->hide()

					// ->to('CSV') //Auto download excel file	

					->group("report_date")
					->header("report_date")
					->customHeader("Sample Recieved in the Period From : {$from_date} To : {$to_date}", "")
					->customTrailer("{$name} ", "")
					->customTrailer("({$email}) ", "")
					->customTrailer("{$role} ", "")

					->group("sample_type_desc")
					->header("sample_type_desc")

					->customTrailer("Total Number of Samples Analyzed : {counts} ", "")

					->page()
					->pagetitledisplay("TopOfFirstPage")

					->header($header, "")

					->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
					->execute();
			}

			$sql5 = "";
			if ($role == "Head Office") {
				$query = ReportCustomComponent::getHoSampleAnalyzedCount($from_date, $to_date, $posted_ro_office, $lab, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql5 = "SELECT  sr_no, sample_type_desc, commodity_name, count_samples, finalized, report_date, counts FROM temp_reportico_ho_sample_analyzed WHERE user_id = '$user_id'";
				}
				if ($sql5 == "") {
					return $this->redirect("/report/index");
				}

				ini_set("include_path", reporticoReport);
				require_once("vendor/autoload.php");
				require_once("vendor/reportico-web/reportico/src/Reportico.php");

				Builder::build()
					->properties(["bootstrap_preloaded" => true])
					->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
					->title("CAL/RAL Wise Sample Sent for Analysis Report ")

					->sql($sql5)

					->column("sr_no")->justify("center")->label("Sr. No.")
					->column("commodity_name")->justify("center")->label("Commodity Name")
					->column("count_samples")->justify("center")->label("No. of Sample Forwarded")
					->column("finalized")->justify("center")->label("Finalized")
					->column("sample_type_desc")->justify("center")->label("Sample Type")
					->column("counts")->hide()

					// ->to('CSV') //Auto download excel file	

					->group("report_date")
					->header("report_date")
					->customHeader("Sample Recieved in the Period From : {$from_date} To : {$to_date}", "")
					->customTrailer("{$name} ", "")
					->customTrailer("({$email}) ", "")
					->customTrailer("{$role} ", "")

					->group("sample_type_desc")
					->header("sample_type_desc")

					->customTrailer("Total Number of Samples Analyzed : {counts} ", "")

					->page()
					->pagetitledisplay("TopOfFirstPage")

					->header($header, "")

					->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
					->execute();
			}
		}
	}

	public function codingDecodingSection()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
			$from_date = $from_date->format('Y/m/d');
			$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
			$to_date = $to_date->format('Y/m/d');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user_id = $_SESSION['user_code'];


			if ($lab == "HO") {
				$report1 =  $lab . ',' . $_SESSION['ro_office'];
			} else if (isset($_SESSION['ro_office'])) {
				$report1 =  $lab . ',' . $_SESSION['ro_office'];
			} else {
				$report1 =  $lab . ',' . $_SESSION['ro_office'];
			}

			$report_name = "कोडींग/डिकोडिंग अनुभाग" . '<br>' . $report1;

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$customHeader1 = "Sample Recieved in the Period From : $from_date To : $to_date";

			$customHeader2 = "<p> प्रति,</p>
			<p >प्रभारी रसायन,मसाला,तेल,सूक्ष्मजीवशास्त्र/विषविद्या /खाद्यान्न अनुभाग |</p>
			<p>कृपया निन्म उल्लिखित कोडेड नमुने का विश्लेषण कर निर्धारित समय के भीतर अधोहस्ताक्षरी को रिपोर्ट भेज जाए |</p>
			<p>नमुने का ब्योरा:</p>";

			$customHeader = $customHeader1 . '<br>' . $customHeader2;

			$customTrailer1 = "<p class = 'text-right'>कोडींग अधिकारी के हस्ताक्षर</p>
			<p>कृते निदेशक प्रयोगशालाए</p>";

			$customTrailer2 = "<p class= 'text-center'>पावती</p>
			<p>प्रमाणित किया जाता है कि:</p>
			<p>	
				1. आईएसओ/आईईसी/ 17025-2005 के तहत प्रबंधकीय आवश्यकताओं के खंड 4.4 के संबंध में सौंपे गए 							
				कार्य करने की उचित व्यकवस्था है/ नहीं है।
			</p>							
			<p>
				2. सौंपे गए कार्य को करने के लिए आवश्य क कार्मिक, सूचना और उपयुक्त  संसाधनों, जिसमें रसायन, 							
				रीएजेन्ट्सं, ग्ला सवेयर, प्रमाणित संदर्भ सामग्री, संयंत्र, उपकरण, मान्य ता प्राप्तट पद्धतियों आदि का समावेश है, 							
				केन्द्रीय एगमार्क प्रयोगशाला, नागपुर में उपलब्ध् है। 	
			</p>						
			<p>
				3. कर्मियों में वह कौशल और निपुणता है जो प्रश्नुगत परीक्षण के प्रदर्शन के लिए आवश्यक है और वे माप 							
				की अनिश्चिंतता और सीमा का संसूचन आदि करने में भी सक्षम हैं। 
			</p>							
			<p>
				4. सौंपे गए कार्य के लिए जिम्मेदार कार्मिक द्वारा प्रयोग की जाने वाली विधि को पर्याप्त रूप से परिभाषित 							
				किया जाता है, उसका दस्ताकवेजीकरण किया जाता है एवं समझा जाता है।
			</p>							
			<p>5. जिन परीक्षण/पद्धतियों का चयन किया जाता है उनसे ग्राहकों की आवश्याकता पूर्ण होती है।</p> 							
			<p>6. जो अनुरोध/अनुबंध बनाया गया उसमें कोई मतभेद नहीं है और वह प्रयोगशाला और ग्राहक दोनों को मान्य  है। </p>";

			$customTrailer3 = "<p class='text-right'> तकनीकी प्रबंधक के हस्ताक्षर </p>";

			$sql = "";

			if ($role == 'Inward Officer') {
				$query = ReportCustomComponent::getIoCodingDecodingSection($from_date, $to_date, $posted_ro_office, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT  sr_no, commodity_name, stage_sample_code, sample_qnt, received_date, tran_date, sample_type_desc, remark, report_date FROM temp_reportico_io_coding_decoding WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'RAL/CAL OIC') {
				$query = ReportCustomComponent::getRalCalOicCodingDecodingSection($from_date, $to_date, $posted_ro_office, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT  sr_no, commodity_name, stage_sample_code, sample_qnt, received_date, tran_date, sample_type_desc, remark, report_date FROM temp_reportico_ral_cal_oic_coding_decoding WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Lab Incharge') {
				$query = ReportCustomComponent::getLabIncahrgeCodingDecodingSection($from_date, $to_date, $posted_ro_office, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT  sr_no, commodity_name, stage_sample_code, sample_qnt, received_date, tran_date, sample_type_desc, remark, report_date FROM temp_reportico_lab_incharge_coding_decoding WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDolCodingDecodingSection($from_date, $to_date, $posted_ro_office, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT  sr_no, commodity_name, stage_sample_code, sample_qnt, received_date, tran_date, sample_type_desc, remark, report_date FROM temp_reportico_dol_coding_decoding WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Head Office') {
				$query = ReportCustomComponent::getHoCodingDecodingSection($from_date, $to_date, $posted_ro_office, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT  sr_no, commodity_name, stage_sample_code, sample_qnt, received_date, tran_date, sample_type_desc, remark, report_date FROM temp_reportico_ho_coding_decoding WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminCodingDecodingSection($from_date, $to_date, $posted_ro_office, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT  sr_no, commodity_name, stage_sample_code, sample_qnt, received_date, tran_date, sample_type_desc, remark, report_date FROM temp_reportico_admin_coding_decoding WHERE user_id = '$user_id'";
				}
			}
			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("commodity_name")->justify("center")->label("पण्य का नाम")
				->column("stage_sample_code")->justify("center")->label("कोड संख्या")
				->column("sample_qnt")->justify("center")->label("जारी की गई मात्रा")
				->column("received_date")->justify("center")->label("जारी दिनांक")
				->column("tran_date")->justify("center")->label("आगे की गई दिनांक")
				->column("sample_type_desc")->justify("center")->label("नमुने का प्रकार")
				->column("remark")->justify("center")->label("टिप्पणी")

				// ->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customHeader($customHeader, "")

				->group("sample_type_desc")
				->header("sample_type_desc")

				->customTrailer($customTrailer1, "")
				->customTrailer($customTrailer2, "")
				->customTrailer($customTrailer3, "")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}

	public function samplesAllotedToChemistForTesting()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
			$from_date = $from_date->format('Y/m/d');
			$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
			$to_date = $to_date->format('Y/m/d');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user = $this->request->getData('user');
			$user_id = $_SESSION['user_code'];

			$report_name = "Samples Alloted to Chemist For Testing";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == "Inward Officer") {
				$query = ReportCustomComponent::getIoSampleAllotedToChemistForTesting($from_date, $to_date, $posted_ro_office, $lab, $user, $ral_lab_no);
				if ($query == 1) {
					$sql = " SELECT  sr_no, chemist_name, received_date, org_sample_code, commodity_name, sample_type_desc, alloc_date, report_date, counts FROM temp_reportico_io_sample_alloted_chemist_testing WHERE user_id = '$user_id'";
				}
			}

			if ($role == "RAL/CAL OIC") {
				$query = ReportCustomComponent::getRalCalOicSampleAllotedToChemistForTesting($from_date, $to_date, $posted_ro_office, $lab, $user, $ral_lab_no);
				if ($query == 1) {
					$sql = " SELECT  sr_no, chemist_name, received_date, org_sample_code, commodity_name, sample_type_desc, alloc_date, report_date, counts FROM temp_reportico_ral_cal_oic_sample_alloted_chemist_testing WHERE user_id = '$user_id'";
				}
			}

			if ($role == "Lab Incharge") {
				$query = ReportCustomComponent::getLabInchargeSampleAllotedToChemistForTesting($from_date, $to_date, $posted_ro_office, $lab, $ral_lab_no);
				if ($query == 1) {
					$sql = " SELECT  sr_no, chemist_name, received_date, org_sample_code, commodity_name, sample_type_desc, alloc_date, report_date, counts FROM temp_reportico_lab_incharge_sample_alloted_chemist_testing WHERE user_id = '$user_id'";
				}
			}

			if ($role == "DOL") {
				$query = ReportCustomComponent::getDolSampleAllotedToChemistForTesting($from_date, $to_date, $posted_ro_office, $lab, $user, $ral_lab_no);
				if ($query == 1) {
					$sql = " SELECT  sr_no, chemist_name, received_date, org_sample_code, commodity_name, sample_type_desc, alloc_date, report_date, counts FROM temp_reportico_dol_sample_alloted_chemist_testing WHERE user_id = '$user_id'";
				}
			}

			if ($role == "Head Office") {
				$query = ReportCustomComponent::getHoSampleAllotedToChemistForTesting($from_date, $to_date, $posted_ro_office, $lab, $user, $ral_lab_no);
				if ($query == 1) {
					$sql = " SELECT  sr_no, chemist_name, received_date, org_sample_code, commodity_name, sample_type_desc, alloc_date, report_date, counts FROM temp_reportico_ho_sample_alloted_chemist_testing WHERE user_id = '$user_id'";
				}
			}

			if ($role == "Admin") {
				$query = ReportCustomComponent::getAdminSampleAllotedToChemistForTesting($from_date, $to_date, $posted_ro_office, $lab, $user, $ral_lab_no);
				if ($query == 1) {
					$sql = " SELECT  sr_no, chemist_name, received_date, org_sample_code, commodity_name, sample_type_desc, alloc_date, report_date, counts FROM temp_reportico_admin_sample_alloted_chemist_testing WHERE user_id = '$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("received_date")->justify("center")->label("Registered Date")
				->column("org_sample_code")->justify("center")->label("Sample Code")
				->column("commodity_name")->justify("center")->label("Commodity Name")
				->column("alloc_date")->justify("center")->label("Alloted Date")
				->column("sample_type_desc")->justify("center")->label("Sample Type")
				->column("counts")->hide()

				// ->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customHeader("Sample Recieved in the Period From : {$from_date} To : {$to_date}", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				->group("chemist_name")
				->header("chemist_name")

				->group("sample_type_desc")
				->header("sample_type_desc")

				->customTrailer("Total Number of Samples : {counts} ", "")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}

	public function samplesAllotedToChemistForReTesting()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
			$from_date = $from_date->format('Y/m/d');
			$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
			$to_date = $to_date->format('Y/m/d');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user = $this->request->getData('user');
			$user_id = $_SESSION['user_code'];

			$report_name = "Samples Alloted by Chemist For Re-Testing";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == "Inward Officer") {
				$query = ReportCustomComponent::getIoSampleAllotedToChemistForRetesting($from_date, $to_date, $posted_ro_office, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT sr_no, received_date, org_sample_code, commodity_name, chemist_name, sample_type_desc, alloc_date, report_date, counts FROM temp_reportico_io_sample_alloted_chemist_retesting WHERE user_id = '$user_id'";
				}
			}

			if ($role == "RAL/CAL OIC") {
				$query = ReportCustomComponent::getRalCalOicSampleAllotedToChemistForRetesting($from_date, $to_date, $posted_ro_office, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT sr_no, received_date, org_sample_code, commodity_name, chemist_name, sample_type_desc, alloc_date, report_date, counts FROM temp_reportico_ral_cal_oic_sample_alloted_chemist_retesting WHERE user_id = '$user_id'";
				}
			}

			if ($role == "Lab Incharge") {
				$query = ReportCustomComponent::getLabInchargeSampleAllotedToChemistForRetesting($from_date, $to_date, $posted_ro_office, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT sr_no, received_date, org_sample_code, commodity_name, chemist_name, sample_type_desc, alloc_date, report_date, counts FROM temp_reportico_lab_incharge_sample_alloted_chemist_retesting WHERE user_id = '$user_id'";
				}
			}

			if ($role == "DOL") {
				$query = ReportCustomComponent::getDolSampleAllotedToChemistForRetesting($from_date, $to_date, $posted_ro_office, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT sr_no, received_date, org_sample_code, commodity_name, chemist_name, sample_type_desc, alloc_date, report_date, counts FROM temp_reportico_dol_sample_alloted_chemist_retesting WHERE user_id = '$user_id'";
				}
			}

			if ($role == "Head Office") {
				$query = ReportCustomComponent::getHoSampleAllotedToChemistForRetesting($from_date, $to_date, $posted_ro_office, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT sr_no, received_date, org_sample_code, commodity_name, chemist_name, sample_type_desc, alloc_date, report_date, counts FROM temp_reportico_ho_sample_alloted_chemist_retesting WHERE user_id = '$user_id'";
				}
			}

			if ($role == "Admin") {
				$query = ReportCustomComponent::getAdminSampleAllotedToChemistForRetesting($from_date, $to_date, $posted_ro_office, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT sr_no, received_date, org_sample_code, commodity_name, chemist_name, sample_type_desc, alloc_date, report_date, counts FROM temp_reportico_admin_sample_alloted_chemist_retesting WHERE user_id = '$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}
			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("received_date")->justify("center")->label("Received Date")
				->column("org_sample_code")->justify("center")->label("Sample Code")
				->column("commodity_name")->justify("center")->label("Commodity Name")
				->column("chemist_name")->justify("center")->label("Chemist Name")
				->column("alloc_date")->justify("center")->label("Alloted Date")
				->column("sample_type_desc")->label("Sample Type")
				->column("counts")->hide()

				// ->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customHeader("Sample Recieved in the Period From : {$from_date} To : {$to_date}", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				->group("sample_type_desc")
				->header("sample_type_desc")

				->customTrailer("Total Number of allocted Samples to Chemist for re-test: {counts} ", "")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}

	public function testedSamples()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
			$from_date = $from_date->format('Y/m/d');
			$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
			$to_date = $to_date->format('Y/m/d');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user_code = $this->request->getData('user_code');
			$commodity = $this->request->getData('Commodity');
			$sample_type = $this->request->getData('sample_type');
			$user_id = $_SESSION['user_code'];

			$report_name = "Tested Samples";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			} else if ($lab == '') {
				$header5 = " ";
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == "Inward Officer" || $role == "Jr Chemist" || $role == "Sr Chemist") {
				$query = ReportCustomComponent::getIoTestedSample($from_date, $to_date, $posted_ro_office, $user_code, $commodity, $lab, $sample_type, $ral_lab, $role, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, chemist_name, recby_ch_date, commodity_name, org_sample_code,  sample_type_desc, expect_complt, commencement_date, grade, report_date, counts FROM temp_reportico_io_tested_sample WHERE user_id = '$user_id'";
				}
			}

			if ($role == "RAL/CAL OIC") {
				$query = ReportCustomComponent::getRalCalOicTestedSample($from_date, $to_date, $posted_ro_office, $user_code, $commodity, $lab, $sample_type, $ral_lab, $role, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, chemist_name, recby_ch_date, commodity_name, org_sample_code,  sample_type_desc, expect_complt, commencement_date, grade, report_date, counts FROM temp_reportico_ral_cal_oic_tested_sample WHERE user_id = '$user_id'";
				}
			}

			if ($role == "DOL") {
				$query = ReportCustomComponent::getDolTestedSample($from_date, $to_date, $posted_ro_office, $user_code, $commodity, $lab, $sample_type, $ral_lab, $role, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, chemist_name, recby_ch_date, commodity_name, org_sample_code,  sample_type_desc, expect_complt, commencement_date, grade, report_date, counts FROM temp_reportico_dol_tested_sample WHERE user_id = '$user_id'";
				}
			}

			if ($role == "Head Office") {
				$query = ReportCustomComponent::getHoTestedSample($from_date, $to_date, $posted_ro_office, $user_code, $commodity, $lab, $sample_type, $ral_lab, $role, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, chemist_name, recby_ch_date, commodity_name, org_sample_code,  sample_type_desc, expect_complt, commencement_date, grade, report_date, counts FROM temp_reportico_ho_tested_sample WHERE user_id = '$user_id'";
				}
			}

			if ($role == "Admin") {
				$query = ReportCustomComponent::getAdminTestedSample($from_date, $to_date, $posted_ro_office, $user_code, $commodity, $lab, $sample_type, $ral_lab, $role, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, chemist_name, recby_ch_date, commodity_name, org_sample_code,  sample_type_desc, expect_complt, commencement_date, grade, report_date, counts FROM temp_reportico_admin_tested_sample WHERE user_id = '$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("recby_ch_date")->justify("center")->label("Accepted Date")
				->column("commodity_name")->justify("center")->label("Commodity Name")
				->column("org_sample_code")->justify("center")->label("Sample Code")
				->column("expect_complt")->justify("center")->label("Expected Date of Completion")
				->column("commencement_date")->justify("center")->label("Tests Completed on")
				->column("grade")->justify("center")->label("Remark")
				->column("sample_type_desc")->justify("center")->label("Sample Type")
				->column("counts")->hide()

				// ->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customHeader("Sample Recieved in the Period From : {$from_date} To : {$to_date}", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				->group("commodity_name")
				->header("commodity_name")

				->group("sample_type_desc")
				->header("sample_type_desc")

				->customTrailer("Total Number of Tested Samples : {counts} ", "")

				->group("chemist_name")
				->header("chemist_name")

				->group("lab_name")
				->header("lab_name")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}

	public function testResultSubmittedByChemist()
	{
		$this->autoRender = false;

		if ($_SESSION['role'] == 'Jr Chemist' || $_SESSION['role'] == 'Sr Chemist') {
			if ($this->request->is('post')) {
				$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
				$from_date = $from_date->format('Y/m/d');
				$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
				$to_date = $to_date->format('Y/m/d');
				$lab = $this->request->getData('lab');
				$posted_ro_office = $this->request->getData('posted_ro_office');
				$fname = $this->request->getData('fname');
				$lname = $this->request->getData('lname');
				$name = $fname . ' ' . $lname;
				$email = base64_decode($this->request->getData('email'));
				$role = $this->request->getData('role');
				$chemist_code = $this->request->getData('chemist_code');
				$sample_code = $this->request->getData('sample_code');

				$report_name = "Test result submitted by chemist";

				$header1 = "भारत सरकार/Goverment of India";
				$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
				$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
				$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

				if ($lab == 'RAL') {
					$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
				} else if ($lab == 'CAL') {
					$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
				} else if ($lab == 'RO') {
					$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
				} else if ($lab == 'SO') {
					$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
				} else if ($lab == '') {
					$header5 = " ";
				}

				$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
				$header = $headerone . '<br>' . $header5;

				if ($role == 'Jr Chemist') {
					$query = ReportCustomComponent::getJrTestSubmitByChemist($from_date, $to_date, $chemist_code);
					$sql = "SELECT sr_no, recby_ch_date, commodity_name, sample_code, chemist_name, chemist_code, test_name, result, report_date FROM temp_reportico_jr_test_submit_by_chemist";
				}

				if ($role == 'Sr Chemist') {
					$query = ReportCustomComponent::getSrTestSubmitByChemist($from_date, $to_date, $chemist_code);
					$sql = "SELECT sr_no, recby_ch_date, commodity_name, sample_code, chemist_name, chemist_code, test_name, result, report_date FROM temp_reportico_sr_test_submit_by_chemist";
				}
				ini_set("include_path", reporticoReport);
				require_once("vendor/autoload.php");
				require_once("vendor/reportico-web/reportico/src/Reportico.php");

				Builder::build()
					->properties(["bootstrap_preloaded" => true])
					->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
					->title($report_name)

					->sql($sql)

					->column("sr_no")->justify("center")->label("Sr. No.")
					->column("recby_ch_date")->justify("center")->label("Received Date")
					->column("chemist_code")->justify("center")->label("Chemist Code")
					->column("test_name")->justify("center")->label("Test Name")
					->column("result")->label("Result")

					// ->to('CSV') //Auto download excel file	

					->group("report_date")
					->header("report_date")
					->customHeader("Sample Recieved in the Period From : {$from_date} To : {$to_date}", "")
					->customTrailer("{$name} ", "")
					->customTrailer("({$email}) ", "")
					->customTrailer("{$role} ", "")

					->group("chemist_code")
					->header("chemist_code")

					->group("chemist_name")
					->header("chemist_name")

					->group("commodity_name")
					->header("commodity_name")

					->group("sample_code")
					->header("sample_code")

					->group("recby_ch_date")
					->header("recby_ch_date")

					->page()
					->pagetitledisplay("TopOfFirstPage")

					->header($header, "")

					->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
					->execute();
			}
		} else {
			if ($this->request->is('post')) {
				$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
				$from_date = $from_date->format('Y/m/d');
				$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
				$to_date = $to_date->format('Y/m/d');
				$lab = $this->request->getData('lab');
				$ral_lab = $this->request->getData('ral_lab');
				$ral_lab = explode('~', $ral_lab);
				$ral_lab_no = $ral_lab[0];
				$ral_lab_name = $ral_lab[1];
				$posted_ro_office = $this->request->getData('posted_ro_office');
				$fname = $this->request->getData('fname');
				$lname = $this->request->getData('lname');
				$name = $fname . ' ' . $lname;
				$email = base64_decode($this->request->getData('email'));
				$role = $this->request->getData('role');
				$chemist_code = $this->request->getData('chemist_code');
				$sample_code = $this->request->getData('sample_code');
				$user_id = $_SESSION['user_code'];

				$report_name = "Test result submitted by chemist";

				$header1 = "भारत सरकार/Goverment of India";
				$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
				$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
				$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

				if ($lab == 'RAL') {
					$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
				} else if ($lab == 'CAL') {
					$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
				} else if ($lab == 'RO') {
					$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
				} else if ($lab == 'SO') {
					$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
				} else if ($lab == '') {
					$header5 = " ";
				}

				$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
				$header = $headerone . '<br>' . $header5;

				$sql = "";

				if ($role == 'Inward Officer') {
					$query = ReportCustomComponent::getIoTestSubmitByChemist($from_date, $to_date, $chemist_code, $sample_code, $ral_lab_no, $ral_lab_name);
					if ($query == 1) {
						$sql = "SELECT sr_no, lab_name, recby_ch_date, commodity_name, sample_code, chemist_name, chemist_code, test_name, result, report_date FROM temp_reportico_io_test_submit_by_chemist WHERE user_id = '$user_id'";
					}
				}

				if ($role == 'RAL/CAL OIC') {
					$query = ReportCustomComponent::getRalCalOicTestSubmitByChemist($from_date, $to_date, $chemist_code, $sample_code, $ral_lab_no, $ral_lab_name);
					if ($query == 1) {
						$sql = "SELECT sr_no, lab_name, recby_ch_date, commodity_name, sample_code, chemist_name, chemist_code, test_name, result, report_date FROM temp_reportico_ral_cal_oic_test_submit_by_chemist WHERE user_id = '$user_id'";
					}
				}

				if ($role == 'DOL') {
					$query = ReportCustomComponent::getDolTestSubmitByChemist($from_date, $to_date, $chemist_code, $sample_code, $ral_lab_no, $ral_lab_name);
					if ($query == 1) {
						$sql = "SELECT sr_no, lab_name, recby_ch_date, commodity_name, sample_code, chemist_name, chemist_code, test_name, result, report_date FROM temp_reportico_dol_test_submit_by_chemist WHERE user_id = '$user_id'";
					}
				}

				if ($role == 'Head Office') {
					$query = ReportCustomComponent::getHoTestSubmitByChemist($from_date, $to_date, $chemist_code, $sample_code, $ral_lab_no, $ral_lab_name);
					if ($query == 1) {
						$sql = "SELECT sr_no, lab_name, recby_ch_date, commodity_name, sample_code, chemist_name, chemist_code, test_name, result, report_date FROM temp_reportico_ho_test_submit_by_chemist WHERE user_id = '$user_id'";
					}
				}

				if ($role == 'Admin') {
					$query = ReportCustomComponent::getAdminTestSubmitByChemist($from_date, $to_date, $chemist_code, $sample_code, $ral_lab_no, $ral_lab_name);
					if ($query == 1) {
						$sql = "SELECT sr_no, lab_name, recby_ch_date, commodity_name, sample_code, chemist_name, chemist_code, test_name, result, report_date FROM temp_reportico_admin_test_submit_by_chemist WHERE user_id = '$user_id'";
					}
				}

				if ($sql == "") {
					return $this->redirect("/report/index");
				}
				ini_set("include_path", reporticoReport);
				require_once("vendor/autoload.php");
				require_once("vendor/reportico-web/reportico/src/Reportico.php");

				Builder::build()
					->properties(["bootstrap_preloaded" => true])
					->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
					->title($report_name)

					->sql($sql)

					->column("sr_no")->justify("center")->label("Sr. No.")
					->column("recby_ch_date")->justify("center")->label("Received Date")
					->column("chemist_code")->justify("center")->label("Chemist Code")
					->column("test_name")->justify("center")->label("Test Name")
					->column("result")->label("Result")

					// ->to('CSV') //Auto download excel file	

					->group("report_date")
					->header("report_date")
					->customHeader("Sample Recieved in the Period From : {$from_date} To : {$to_date}", "")
					->customTrailer("{$name} ", "")
					->customTrailer("({$email}) ", "")
					->customTrailer("{$role} ", "")

					->group("lab_name")
					->header("lab_name")

					->group("chemist_code")
					->header("chemist_code")

					->group("chemist_name")
					->header("chemist_name")

					->group("commodity_name")
					->header("commodity_name")

					->group("sample_code")
					->header("sample_code")

					->group("recby_ch_date")
					->header("recby_ch_date")

					->page()
					->pagetitledisplay("TopOfFirstPage")

					->header($header, "")

					->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
					->execute();
			}
		}
	}

	public function reTestedSamples()
	{
		$this->autoRender = false;

		if ($_SESSION['role'] == 'Jr Chemist' || $_SESSION['role'] == 'Sr Chemist') {
			if ($this->request->is('post')) {
				$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
				$from_date = $from_date->format('Y/m/d');
				$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
				$to_date = $to_date->format('Y/m/d');
				$lab = $this->request->getData('lab');
				$posted_ro_office = $this->request->getData('posted_ro_office');
				$fname = $this->request->getData('fname');
				$lname = $this->request->getData('lname');
				$name = $fname . ' ' . $lname;
				$email = base64_decode($this->request->getData('email'));
				$role = $this->request->getData('role');
				$user_id = $_SESSION['user_code'];

				$report_name = "Re-Tested Samples";

				$header1 = "भारत सरकार/Goverment of India";
				$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
				$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
				$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

				if ($lab == 'RAL') {
					$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
				} else if ($lab == 'CAL') {
					$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
				} else if ($lab == 'RO') {
					$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
				} else if ($lab == 'SO') {
					$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
				} else if ($lab == '') {
					$header5 = " ";
				}

				$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
				$header = $headerone . '<br>' . $header5;

				if ($role == 'Jr Chemist') {
					$query = ReportCustomComponent::getJrReTestedSample($from_date, $to_date);
					$sql = "SELECT sr_no, received_date,org_sample_code, commodity_name, sample_type_desc,  ro_office, full_name, report_date, counts FROM temp_reportico_jr_retested_sample WHERE user_id = '$user_id'";
				}

				if ($role == 'Sr Chemist') {
					$query = ReportCustomComponent::getSrReTestedSample($from_date, $to_date);
					$sql = "SELECT sr_no, received_date,org_sample_code, commodity_name, sample_type_desc,  ro_office, full_name, report_date, counts FROM temp_reportico_sr_retested_sample WHERE user_id = '$user_id'";
				}
				ini_set("include_path", reporticoReport);
				require_once("vendor/autoload.php");
				require_once("vendor/reportico-web/reportico/src/Reportico.php");

				Builder::build()
					->properties(["bootstrap_preloaded" => true])
					->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
					->title($report_name)

					->sql($sql)

					->column("sr_no")->justify("center")->label("Sr. No.")
					->column("received_date")->justify("center")->label("Received Date")
					->column("org_sample_code")->justify("center")->label("Sample Code")
					->column("commodity_name")->justify("center")->label("Commodity Name")
					->column("sample_type_desc")->label("Sample Type")
					->column("full_name")->label("Chemist Name")
					->column("counts")->hide()
					->column("ro_office")->hide()

					// ->to('CSV') //Auto download excel file	

					->group("report_date")
					->header("report_date")
					->customHeader("Sample Recieved in the Period From : {$from_date} To : {$to_date}", "")
					->customTrailer("{$name} ", "")
					->customTrailer("({$email}) ", "")
					->customTrailer("{$role} ", "")

					->group("commodity_name")
					->header("commodity_name")

					->group("full_name")
					->header("full_name")

					->group("sample_type_desc")
					->header("sample_type_desc")

					->customTrailer("Total Number of Re-Tested Samples : {counts} ", "")

					->page()
					->pagetitledisplay("TopOfFirstPage")

					->header($header, "")

					->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
					->execute();
			}
		} else {
			if ($this->request->is('post')) {
				$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
				$from_date = $from_date->format('Y/m/d');
				$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
				$to_date = $to_date->format('Y/m/d');
				$lab = $this->request->getData('lab');
				$ral_lab = $this->request->getData('ral_lab');
				$ral_lab = explode('~', $ral_lab);
				$ral_lab_no = $ral_lab[0];
				$ral_lab_name = $ral_lab[1];
				$posted_ro_office = $this->request->getData('posted_ro_office');
				$fname = $this->request->getData('fname');
				$lname = $this->request->getData('lname');
				$name = $fname . ' ' . $lname;
				$email = base64_decode($this->request->getData('email'));
				$role = $this->request->getData('role');
				$user_id = $_SESSION['user_code'];

				$report_name = "Re-Tested Samples";

				$header1 = "भारत सरकार/Goverment of India";
				$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
				$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
				$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

				if ($lab == 'RAL') {
					$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
				} else if ($lab == 'CAL') {
					$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
				} else if ($lab == 'RO') {
					$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
				} else if ($lab == 'SO') {
					$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
				} else if ($lab == '') {
					$header5 = " ";
				}

				$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
				$header = $headerone . '<br>' . $header5;

				$sql = "";

				if ($role == 'Inward Officer') {
					$query = ReportCustomComponent::getIoReTestedSample($from_date, $to_date, $ral_lab_name, $ral_lab_no);
					if ($query == 1) {
						$sql = "SELECT sr_no, lab_name, received_date,org_sample_code, commodity_name, sample_type_desc,  ro_office, full_name, report_date, counts FROM temp_reportico_io_retested_sample WHERE user_id = '$user_id'";
					}
				}

				if ($role == 'RAL/CAL OIC') {
					$query = ReportCustomComponent::getRalCalOicReTestedSample($from_date, $to_date, $ral_lab_name, $ral_lab_no);
					if ($query == 1) {
						$sql = "SELECT sr_no, lab_name, received_date,org_sample_code, commodity_name, sample_type_desc,  ro_office, full_name, report_date, counts FROM temp_reportico_ral_cal_oic_retested_sample WHERE user_id = '$user_id'";
					}
				}

				if ($role == 'DOL') {
					$query = ReportCustomComponent::getDolReTestedSample($from_date, $to_date, $ral_lab_name, $ral_lab_no);
					if ($query == 1) {
						$sql = "SELECT sr_no, lab_name, received_date,org_sample_code, commodity_name, sample_type_desc,  ro_office, full_name, report_date, counts FROM temp_reportico_dol_retested_sample WHERE user_id = '$user_id'";
					}
				}

				if ($role == 'Admin') {
					$query = ReportCustomComponent::getAdminReTestedSample($from_date, $to_date, $ral_lab_name, $ral_lab_no);
					if ($query == 1) {
						$sql = "SELECT sr_no, lab_name, received_date,org_sample_code, commodity_name, sample_type_desc,  ro_office, full_name, report_date, counts FROM temp_reportico_admin_retested_sample WHERE user_id = '$user_id'";
					}
				}

				if ($sql == "") {
					return $this->redirect("/report/index");
				}

				ini_set("include_path", reporticoReport);
				require_once("vendor/autoload.php");
				require_once("vendor/reportico-web/reportico/src/Reportico.php");

				Builder::build()
					->properties(["bootstrap_preloaded" => true])
					->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
					->title($report_name)

					->sql($sql)

					->column("sr_no")->justify("center")->label("Sr. No.")
					->column("received_date")->justify("center")->label("Received Date")
					->column("org_sample_code")->justify("center")->label("Sample Code")
					->column("commodity_name")->justify("center")->label("Commodity Name")
					->column("sample_type_desc")->label("Sample Type")
					->column("full_name")->label("Chemist Name")
					->column("counts")->hide()
					->column("ro_office")->hide()

					// ->to('CSV') //Auto download excel file	

					->group("report_date")
					->header("report_date")
					->customHeader("Sample Recieved in the Period From : {$from_date} To : {$to_date}", "")
					->customTrailer("{$name} ", "")
					->customTrailer("({$email}) ", "")
					->customTrailer("{$role} ", "")

					->group("commodity_name")
					->header("commodity_name")

					->group("full_name")
					->header("full_name")

					->group("sample_type_desc")
					->header("sample_type_desc")

					->customTrailer("Total Number of Re-Tested Samples : {counts} ", "")

					->group("lab_name")
					->header("lab_name")

					->page()
					->pagetitledisplay("TopOfFirstPage")

					->header($header, "")

					->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
					->execute();
			}
		}
	}

	public function reTestedSamplesSubmittedByChemist()
	{
		$this->autoRender = false;

		if ($_SESSION['role'] == 'Jr Chemist' || $_SESSION['role'] == 'Sr Chemist') {
			if ($this->request->is('post')) {
				$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
				$from_date = $from_date->format('Y/m/d');
				$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
				$to_date = $to_date->format('Y/m/d');
				$lab = $this->request->getData('lab');
				$posted_ro_office = $this->request->getData('posted_ro_office');
				$fname = $this->request->getData('fname');
				$lname = $this->request->getData('lname');
				$name = $fname . ' ' . $lname;
				$email = base64_decode($this->request->getData('email'));
				$role = $this->request->getData('role');
				$user_id = $_SESSION['user_code'];

				$report_name = "Re-Tested Samples submitted by chemist";

				$header1 = "भारत सरकार/Goverment of India";
				$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
				$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
				$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

				if ($lab == 'RAL') {
					$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
				} else if ($lab == 'CAL') {
					$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
				} else if ($lab == 'RO') {
					$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
				} else if ($lab == 'SO') {
					$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
				} else if ($lab == '') {
					$header5 = " ";
				}

				$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
				$header = $headerone . '<br>' . $header5;

				$sql = "";

				if ($role == 'Jr Chemist') {
					$query = ReportCustomComponent::getJrReTestedSampleByChemist($from_date, $to_date);
					if ($query == 1) {
						$sql = "SELECT sr_no, received_date,org_sample_code, commodity_name, sample_type_desc,lab, full_name, report_date, counts FROM temp_reportico_jr_retested_sample_submit WHERE user_id = '$user_id'";
					}
				}

				if ($role == 'Sr Chemist') {
					$query = ReportCustomComponent::getSrReTestedSampleByChemist($from_date, $to_date);
					if ($query == 1) {
						$sql = "SELECT sr_no, received_date,org_sample_code, commodity_name, sample_type_desc,lab, full_name, report_date, counts FROM temp_reportico_sr_retested_sample_submit WHERE user_id = '$user_id'";
					}
				}
				if ($sql == "") {
					return $this->redirect("/report/index");
				}

				ini_set("include_path", reporticoReport);
				require_once("vendor/autoload.php");
				require_once("vendor/reportico-web/reportico/src/Reportico.php");

				Builder::build()
					->properties(["bootstrap_preloaded" => true])
					->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
					->title($report_name)

					->sql($sql)

					->column("sr_no")->justify("center")->label("Sr. No.")
					->column("received_date")->justify("center")->label("Received Date")
					->column("org_sample_code")->justify("center")->label("Sample Code")
					->column("commodity_name")->justify("center")->label("Commodity Name")
					->column("lab")->justify("center")->label("Lab Name")
					->column("sample_type_desc")->justify("center")->label("Sample Type")
					->column("counts")->hide()
					->column("full_name")->hide()

					// ->to('CSV') //Auto download excel file	

					->group("report_date")
					->header("report_date")
					->customHeader("Sample Recieved in the Period From : {$from_date} To : {$to_date}", "")
					->customTrailer("{$name} ", "")
					->customTrailer("({$email}) ", "")
					->customTrailer("{$role} ", "")

					->group("commodity_name")
					->header("commodity_name")

					->group("sample_type_desc")
					->header("sample_type_desc")

					->customTrailer("Total Number of Re-Tested Samples : {counts} ", "")

					->page()
					->pagetitledisplay("TopOfFirstPage")

					->header($header, "")

					->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
					->execute();
			}
		} else {
			if ($this->request->is('post')) {
				$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
				$from_date = $from_date->format('Y/m/d');
				$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
				$to_date = $to_date->format('Y/m/d');
				$lab = $this->request->getData('lab');
				$ral_lab = $this->request->getData('ral_lab');
				$ral_lab = explode('~', $ral_lab);
				$ral_lab_no = $ral_lab[0];
				$ral_lab_name = $ral_lab[1];
				$posted_ro_office = $this->request->getData('posted_ro_office');
				$fname = $this->request->getData('fname');
				$lname = $this->request->getData('lname');
				$name = $fname . ' ' . $lname;
				$email = base64_decode($this->request->getData('email'));
				$role = $this->request->getData('role');
				$user_id = $_SESSION['user_code'];

				$report_name = "Re-Tested Samples submitted by chemist";

				$header1 = "भारत सरकार/Goverment of India";
				$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
				$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
				$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

				if ($lab == 'RAL') {
					$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
				} else if ($lab == 'CAL') {
					$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
				} else if ($lab == 'RO') {
					$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
				} else if ($lab == 'SO') {
					$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
				} else if ($lab == '') {
					$header5 = " ";
				}

				$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
				$header = $headerone . '<br>' . $header5;

				$sql = "";

				if ($role == 'Inward Officer') {
					$query = ReportCustomComponent::getIoReTestedSampleByChemist($from_date, $to_date, $ral_lab_name, $ral_lab_no);
					if ($query == 1) {
						$sql = "SELECT sr_no, lab_name, received_date,org_sample_code, commodity_name, sample_type_desc,lab, full_name, report_date, counts FROM temp_reportico_io_retested_sample_submit WHERE user_id = '$user_id'";
					}
				}

				if ($role == 'RAL/CAL OIC') {
					$query = ReportCustomComponent::getRalCalOicReTestedSampleByChemist($from_date, $to_date, $ral_lab_name, $ral_lab_no);
					if ($query == 1) {
						$sql = "SELECT sr_no, lab_name, received_date,org_sample_code, commodity_name, sample_type_desc,lab, full_name, report_date, counts FROM temp_reportico_ral_cal_oic_retested_sample_submit WHERE user_id = '$user_id'";
					}
				}

				if ($role == 'DOL') {
					$query = ReportCustomComponent::getDolReTestedSampleByChemist($from_date, $to_date, $ral_lab_name, $ral_lab_no);
					if ($query == 1) {
						$sql = "SELECT sr_no, lab_name, received_date,org_sample_code, commodity_name, sample_type_desc,lab, full_name, report_date, counts FROM temp_reportico_dol_retested_sample_submit WHERE user_id = '$user_id'";
					}
				}

				if ($role == 'Admin') {
					$query = ReportCustomComponent::getAdminReTestedSampleByChemist($from_date, $to_date, $ral_lab_name, $ral_lab_no);
					if ($query == 1) {
						$sql = "SELECT sr_no, lab_name, received_date,org_sample_code, commodity_name, sample_type_desc,lab, full_name, report_date, counts FROM temp_reportico_admin_retested_sample_submit WHERE user_id = '$user_id'";
					}
				}

				if ($sql == "") {
					return $this->redirect("/report/index");
				}

				ini_set("include_path", reporticoReport);
				require_once("vendor/autoload.php");
				require_once("vendor/reportico-web/reportico/src/Reportico.php");

				Builder::build()
					->properties(["bootstrap_preloaded" => true])
					->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
					->title($report_name)

					->sql($sql)

					->column("sr_no")->justify("center")->label("Sr. No.")
					->column("received_date")->justify("center")->label("Received Date")
					->column("org_sample_code")->justify("center")->label("Sample Code")
					->column("commodity_name")->justify("center")->label("Commodity Name")
					->column("sample_type_desc")->justify("center")->label("Sample Type")
					->column("lab")->justify("center")->label("Lab Name")
					->column("counts")->hide()
					->column("full_name")->hide()

					// ->to('CSV') //Auto download excel file	

					->group("report_date")
					->header("report_date")
					->customHeader("Sample Recieved in the Period From : {$from_date} To : {$to_date}", "")
					->customTrailer("{$name} ", "")
					->customTrailer("({$email}) ", "")
					->customTrailer("{$role} ", "")

					->group("commodity_name")
					->header("commodity_name")

					->group("sample_type_desc")
					->header("sample_type_desc")

					->customTrailer("Total Number of Re-Tested Samples : {counts} ", "")

					->group("lab_name")
					->header("lab_name")

					->page()
					->pagetitledisplay("TopOfFirstPage")

					->header($header, "")

					->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
					->execute();
			}
		}
	}

	public function sampleAnalyzedByChemist()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
			$from_date = $from_date->format('Y/m/d');
			$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
			$to_date = $to_date->format('Y/m/d');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user = $this->request->getData('user');
			$user_id = $_SESSION['user_code'];

			$report_name = "Sample Analyzed by Chemist";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'Inward Officer') {
				$query = ReportCustomComponent::getIoSampleAnalyzedByChemist($from_date, $to_date, $posted_ro_office, $user, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, name_chemist, sample_received_from, letr_ref_no, commodity_name, sample_total_qnt, stage_sample_code, received_date, lab_code, alloc_date, grading_date, remark, report_date, counts FROM temp_reportico_io_sample_analyzed_chemist WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'RAL/CAL OIC') {
				$query = ReportCustomComponent::getRalCalOicSampleAnalyzedByChemist($from_date, $to_date, $posted_ro_office, $user, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, name_chemist, sample_received_from, letr_ref_no, commodity_name, sample_total_qnt, stage_sample_code, received_date, lab_code, alloc_date, grading_date, remark, report_date, counts FROM temp_reportico_ral_cal_oic_sample_analyzed_chemist WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDolSampleAnalyzedByChemist($from_date, $to_date, $posted_ro_office, $user, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, name_chemist, sample_received_from, letr_ref_no, commodity_name, sample_total_qnt, stage_sample_code, received_date, lab_code, alloc_date, grading_date, remark, report_date, counts FROM temp_reportico_dol_sample_analyzed_chemist WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminSampleAnalyzedByChemist($from_date, $to_date, $posted_ro_office, $user, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, name_chemist, sample_received_from, letr_ref_no, commodity_name, sample_total_qnt, stage_sample_code, received_date, lab_code, alloc_date, grading_date, remark, report_date, counts FROM temp_reportico_admin_sample_analyzed_chemist WHERE user_id = '$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("name_chemist")->justify("center")->label("Name of chemist whom alloted")
				->column("sample_received_from")->justify("center")->label("Sample Received From")
				->column("letr_ref_no")->justify("center")->label("Letter No/Date")
				->column("commodity_name")->justify("center")->label(" Name of Commodity")
				->column("sample_total_qnt")->justify("center")->label("Sample Quantity")
				->column("stage_sample_code")->justify("center")->label("Ro/So Code")
				->column("received_date")->justify("center")->label("Date of receipt in lab")
				->column("lab_code")->justify("center")->label("Lab Code")
				->column("alloc_date")->justify("center")->label("Date of allotment")
				->column("grading_date")->justify("center")->label("Date of receipt of result")
				->column("grading_date")->justify("center")->label("Date of Communication of report")
				->column("remark")->justify("center")->label("Remark")
				->column("counts")->hide()

				// ->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customHeader("Sample Recieved in the Period From : {$from_date} To : {$to_date}", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				->group("lab_name")
				->header("lab_name")

				->customTrailer("Total Number of Re-Tested Samples : {counts} ", "")


				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}

	public function categoryWiseReceivedSample()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
			$from_date = $from_date->format('Y/m/d');
			$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
			$to_date = $to_date->format('Y/m/d');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$sample_type = $this->request->getData('sample_type');
			$Category = $this->request->getData('Category');
			$user_id = $_SESSION['user_code'];

			$report_name = "Category-wise Received Sample";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'Inward Officer') {
				$query = ReportCustomComponent::getIoCategorywiseReceivedSample($from_date, $to_date, $posted_ro_office, $sample_type, $Category, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, category_name, count, sample_type_desc,  report_date, counts FROM temp_reportico_io_categorywise_received_sample WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'RAL/CAL OIC') {
				$query = ReportCustomComponent::getRalCalOicCategorywiseReceivedSample($from_date, $to_date, $posted_ro_office, $sample_type, $Category, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, category_name, count, sample_type_desc,  report_date, counts FROM temp_reportico_ral_cal_oic_categorywise_received_sample WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminCategorywiseReceivedSample($from_date, $to_date, $posted_ro_office, $sample_type, $Category, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, category_name, count, sample_type_desc,  report_date, counts FROM temp_reportico_admin_categorywise_received_sample WHERE user_id = '$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("category_name")->justify("center")->label("Category Name")
				->column("sample_type_desc")->label("Sample Type")
				->column("count")->justify("center")->label("No. of Received Sample")

				->column("counts")->hide()

				// ->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customHeader("Sample Recieved in the Period From : {$from_date} To : {$to_date}", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				->group("lab_name")
				->header("lab_name")

				->group("sample_type_desc")
				->header("sample_type_desc")

				->customTrailer("Total Number of Re-Tested Samples : {counts} ", "")


				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}

	public function timeTakenForAnalysisOfSamples()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
			$from_date = $from_date->format('Y/m/d');
			$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
			$to_date = $to_date->format('Y/m/d');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user_id = $_SESSION['user_code'];

			$report_name = "Time Taken for Analysis of Samples";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'Inward Officer') {
				$query = ReportCustomComponent::getIoTimeTakenAnalysisSample($from_date, $to_date, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, stage_sample_code, commodity_name AS name_of_sample, received_date, dispatch_date, time_taken, report_date FROM temp_reportico_io_timetaken_analysis WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'RAL/CAL OIC') {
				$query = ReportCustomComponent::getRalCalOicTimeTakenAnalysisSample($from_date, $to_date, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, stage_sample_code, commodity_name AS name_of_sample, received_date, dispatch_date, time_taken, report_date FROM temp_reportico_ral_cal_oic_timetaken_analysis WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminTimeTakenAnalysisSample($from_date, $to_date, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, stage_sample_code, commodity_name AS name_of_sample, received_date, dispatch_date, time_taken, report_date FROM temp_reportico_admin_timetaken_analysis WHERE user_id = '$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("stage_sample_code")->justify("center")->label("Code No.(RO/SO)")
				->column("received_date")->justify("center")->label("Date of receipt of sample in RALs/CAL")
				->column("dispatch_date")->justify("center")->label("Date of Dispatch of results to RO/SO/Others")
				// ->column("time_taken")->justify("center")->label("Reason For Delay") Commented in old code that's why commented this
				->column("time_taken")->hide()

				// ->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customHeader("Sample Recieved in the Period From : {$from_date} To : {$to_date}", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				->group("lab_name")
				->header("lab_name")

				->group("name_of_sample")
				->header("name_of_sample")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}

	public function commodityWiseResearchPrivateSamplesAnalysed()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$commodity = $this->request->getData('Commodity');
			$user_id = $_SESSION['user_code'];

			$report_name = "Commodity-wise Research & Private Samples analysed";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'Inward Officer') {
				$query = ReportCustomComponent::getIoCommodityWisePrivateAnalysis($lab, $posted_ro_office, $commodity, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, fin_year, commodity_name, sample_count, report_date, counts FROM temp_reportico_io_commoditywise_private_analysis WHERE user_id ='$user_id'";
				}
			}

			if ($role == 'RAL/CAL OIC') {
				$query = ReportCustomComponent::getRalCalOicCommodityWisePrivateAnalysis($lab, $posted_ro_office, $commodity, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, fin_year, commodity_name, sample_count, report_date, counts FROM temp_reportico_ral_cal_oic_commoditywise_private_analysis WHERE user_id ='$user_id'";
				}
			}

			if ($role == 'Head Office') {
				$query = ReportCustomComponent::getHoCommodityWisePrivateAnalysis($lab, $posted_ro_office, $commodity, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, fin_year, commodity_name, sample_count, report_date, counts FROM temp_reportico_ho_commoditywise_private_analysis WHERE user_id ='$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminCommodityWisePrivateAnalysis($lab, $posted_ro_office, $commodity, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, fin_year, commodity_name, sample_count, report_date, counts FROM temp_reportico_admin_commoditywise_private_analysis WHERE user_id ='$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}
			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("sample_count")->justify("center")->label("Research Samples")
				->column("counts")->hide()

				// ->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customHeader("Sample Analysed during Financial year : {fin_year}", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				->group("lab_name")
				->header("lab_name")

				->group("commodity_name")
				->header("commodity_name")
				->customTrailer("Total Number of Re-Tested Samples : {counts} ", "")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}

	public function consolidatedStatementOfBroughtForwardAndCarriedForwardOfSamples()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$month = $this->request->getData('month');
			$user = $this->request->getData('user');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user_id = $_SESSION['user_code'];

			$report_name = "Consolidated statement of Brought forward and carried forward of samples";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'Inward Officer') {
				$query = ReportCustomComponent::getIoConsolidateSatementBroughtFwdCarriedFwdSample($month, $user, $posted_ro_office, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, month,lab_name,role_name, bf_count, received_count, total, analyzed_count_one, analyzed_count_two, carried_for, chemist_name, report_date FROM temp_reportico_io_consoli_sample WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'RAL/CAL OIC') {
				$query = ReportCustomComponent::getRalCalOicConsolidateSatementBroughtFwdCarriedFwdSample($month, $user, $posted_ro_office, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, month,lab_name,role_name, bf_count, received_count, total, analyzed_count_one, analyzed_count_two, carried_for, chemist_name, report_date FROM temp_reportico_ral_cal_oic_consoli_sample WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDolConsolidateSatementBroughtFwdCarriedFwdSample($month, $user, $posted_ro_office, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, month,lab_name,role_name, bf_count, received_count, total, analyzed_count_one, analyzed_count_two, carried_for, chemist_name, report_date FROM temp_reportico_dol_consoli_sample WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminConsolidateSatementBroughtFwdCarriedFwdSample($month, $user, $posted_ro_office, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, month,lab_name,role_name, bf_count, received_count, total, analyzed_count_one, analyzed_count_two, carried_for, chemist_name, report_date FROM temp_reportico_admin_consoli_sample WHERE user_id = '$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("role_name")->justify("center")->label("Name of Post")
				->column("bf_count")->justify("center")->label("BF")
				->column("received_count")->justify("center")->label("Received During Month")
				->column("total")->justify("center")->label("Total")
				->column("analyzed_count_one")->justify("center")->label("Sample analyze during month original")
				->column("analyzed_count_two")->justify("center")->label("Duplicate")
				->column("carried_for")->justify("center")->label("Carried Forward")

				// ->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				->group("month")
				->header("month")

				->group("lab_name")
				->header("lab_name")

				->group("chemist_name")
				->header("chemist_name")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}

	public function noOfCheckPrivateResearchSamplesAnalyzedByRals()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
			$from_date = $from_date->format('Y/m/d');
			$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
			$to_date = $to_date->format('Y/m/d');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user_id = $_SESSION['user_code'];

			$report_name = "No. of Check, Private & Research Samples analyzed by RALs";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'Inward Officer') {
				$query = ReportCustomComponent::getIoNoCheckPrivateResearchSample($from_date, $to_date, $posted_ro_office, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, check_analyzed_count, res_analyzed_count, chk_analyzed_count, othr_analyzed_count,  report_date FROM temp_reportico_io_chk_pvt_research WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'RAL/CAL OIC') {
				$query = ReportCustomComponent::getRalCalOicNoCheckPrivateResearchSample($from_date, $to_date, $posted_ro_office, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, check_analyzed_count, res_analyzed_count, chk_analyzed_count, othr_analyzed_count,  report_date FROM temp_reportico_ral_cal_oic_chk_pvt_research WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDolNoCheckPrivateResearchSample($from_date, $to_date, $posted_ro_office, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, check_analyzed_count, res_analyzed_count, chk_analyzed_count, othr_analyzed_count,  report_date FROM temp_reportico_dol_chk_pvt_research WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminNoCheckPrivateResearchSample($from_date, $to_date, $posted_ro_office, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, check_analyzed_count, res_analyzed_count, chk_analyzed_count, othr_analyzed_count,  report_date FROM temp_reportico_admin_chk_pvt_research WHERE user_id = '$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("check_analyzed_count")->justify("center")->label("Checked")
				->column("res_analyzed_count")->justify("center")->label("Research")
				->column("chk_analyzed_count")->justify("center")->label("Challenged")
				->column("othr_analyzed_count")->justify("center")->label("Other")

				// ->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customHeader("Sample Analysed during Financial year : {$from_date} to {$to_date}", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				->group("lab_name")
				->header("lab_name")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}

	public function samplesAllotedAnalyzedPendingReportRalCal()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
			$from_date = $from_date->format('Y/m/d');
			$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
			$to_date = $to_date->format('Y/m/d');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user_id = $_SESSION['user_code'];

			$report_name = "Samples Alloted/Analyzed/Pending ";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'Inward Officer') {
				$query = ReportCustomComponent::getIoSampleAlloteAnalyzePending($from_date, $to_date, $posted_ro_office, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, allotment_count, analyzed_count, pending_count, report_date FROM temp_reportico_io_sample_allot_analyz_pend WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'RAL/CAL OIC') {
				$query = ReportCustomComponent::getRalCalOicSampleAlloteAnalyzePending($from_date, $to_date, $posted_ro_office, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, allotment_count, analyzed_count, pending_count, report_date FROM temp_reportico_ral_cal_oic_sample_allot_analyz_pend WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDolSampleAlloteAnalyzePending($from_date, $to_date, $posted_ro_office, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, allotment_count, analyzed_count, pending_count, report_date FROM temp_reportico_dol_sample_allot_analyz_pend WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminSampleAlloteAnalyzePending($from_date, $to_date, $posted_ro_office, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, allotment_count, analyzed_count, pending_count, report_date FROM temp_reportico_admin_sample_allot_analyz_pend WHERE user_id = '$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("allotment_count")->justify("center")->label("Sample Alloted")
				->column("analyzed_count")->justify("center")->label("Sample Analyzed")
				->column("pending_count")->justify("center")->label("Pending Samples")

				// ->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customHeader("Sample Analysed during Financial year : {$from_date} to {$to_date}", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				->group("lab_name")
				->header("lab_name")
				->customTrailer("Total Number of Samples : {allotment_count} ", "")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}

	public function performanceReportOfRalCal()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
			$from_date = $from_date->format('Y/m/d');
			$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
			$to_date = $to_date->format('Y/m/d');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$sample_type = $this->request->getData('sample_type');
			$user_id = $_SESSION['user_code'];

			$report_name = "Performance Report of RAL/CAL";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'Inward Officer') {
				$query = ReportCustomComponent::getIoPerformanceRalCal($from_date, $to_date, $posted_ro_office, $lab, $sample_type, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, progress_sample, tot_sample_month, report_date FROM temp_reportico_io_performance_ral_cal WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'RAL/CAL OIC') {
				$query = ReportCustomComponent::getRalCalOicPerformanceRalCal($from_date, $to_date, $posted_ro_office, $lab, $sample_type, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, progress_sample, tot_sample_month, report_date FROM temp_reportico_ral_cal_oic_performance_ral_cal WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDolPerformanceRalCal($from_date, $to_date, $posted_ro_office, $lab, $sample_type, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, progress_sample, tot_sample_month, report_date FROM temp_reportico_dol_performance_ral_cal WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminPerformanceRalCal($from_date, $to_date, $posted_ro_office, $lab, $sample_type, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, progress_sample, tot_sample_month, report_date FROM temp_reportico_admin_performance_ral_cal WHERE user_id = '$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("progress_sample")->justify("center")->label("Progressive Total(Sapmle analyze uptill now)")
				->column("tot_sample_month")->justify("center")->label("Total sample analyze during month")

				// ->to('CSV') //Auto download excel file	

				// ->expression("total_sum")->sum("progress_sample","lab_name")

				->group("report_date")
				->header("report_date")
				->customHeader("Sample Analysed during Financial year : {$from_date} to {$to_date}", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				->group("lab_name")
				->header("lab_name")
				// ->customTrailer("Total No of samples analyzed and pregress: {total_sum} ", "")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}

	public function chemist–wiseSampleAnalysis()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$month = $this->request->getData('month');
			$user = $this->request->getData('user');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$sample_type = $this->request->getData('sample_type');
			$user_id = $_SESSION['user_code'];

			$report_name = "Chemist-wise sample analysis";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'Inward Officer') {
				$query = ReportCustomComponent::getIoChemistWiseSampleAnalysis($month, $posted_ro_office, $user, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, month, chemist_name, check_analyzed_count, res_analyzed_count, res_challenged_count, othr_analyzed_count, report_date FROM temp_reportico_io_chemist_wise_samp_analysis WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'RAL/CAL OIC') {
				$query = ReportCustomComponent::getRalCalOicChemistWiseSampleAnalysis($month, $posted_ro_office, $user, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, month, chemist_name, check_analyzed_count, res_analyzed_count, res_challenged_count, othr_analyzed_count, report_date FROM temp_reportico_ral_cal_oic_chemist_wise_samp_analysis WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDolChemistWiseSampleAnalysis($month, $posted_ro_office, $user, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, month, chemist_name, check_analyzed_count, res_analyzed_count, res_challenged_count, othr_analyzed_count, report_date FROM temp_reportico_dol_chemist_wise_samp_analysis WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminChemistWiseSampleAnalysis($month, $posted_ro_office, $user, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, month, chemist_name, check_analyzed_count, res_analyzed_count, res_challenged_count, othr_analyzed_count, report_date FROM temp_reportico_admin_chemist_wise_samp_analysis WHERE user_id = '$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("check_analyzed_count")->justify("center")->label("Check")
				->column("res_analyzed_count")->justify("center")->label("Research")
				->column("res_challenged_count")->justify("center")->label("Chanlleged")
				->column("othr_analyzed_count")->justify("center")->label("Other")
				->column("month")->hide()

				// ->to('CSV') //Auto download excel file	

				// ->expression("total_sum")->sum("progress_sample","lab_name")

				->group("report_date")
				->header("report_date")
				->customHeader("Sample Analysed for Month : {month} ", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				->group("lab_name")
				->header("lab_name")

				->group("chemist_name")
				->header("chemist_name")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}

	public function testReportForCommodity()
	{
		if ($this->request->is('post')) {
			$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
			$from_date = $from_date->format('Y/m/d');
			$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
			$to_date = $to_date->format('Y/m/d');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$sample_code = $this->request->getData('sample_code');
			$commodity = $this->request->getData('Commodity');

			$con = ConnectionManager::get('default');

			$q = $con->execute("SELECT si.org_sample_code,si.report_pdf, si.commodity_code, wf.tran_date, mc.commodity_name,mcc.category_name,mst.sample_type_desc
			FROM sample_inward si
			INNER JOIN workflow wf ON si.org_sample_code = wf.org_sample_code
			INNER JOIN m_commodity mc ON si.commodity_code = mc.commodity_code
			INNER JOIN m_commodity_category mcc ON mc.category_code = mcc.category_code
			INNER JOIN m_sample_type mst ON mst.sample_type_code = si.sample_type_code
			WHERE si.report_pdf IS NOT NULL AND si.org_sample_code = '$sample_code' AND DATE(si.received_date) BETWEEN '$from_date' AND '$to_date' AND si.commodity_code = '$commodity' AND si.loc_id = '$posted_ro_office'
			GROUP BY si.org_sample_code,si.report_pdf, si.commodity_code, wf.tran_date, mc.commodity_name,mcc.category_name,mst.sample_type_desc");
			$records = $q->fetchAll('assoc');
			if (!empty($records)) {
				$this->set('records', $records);
			} else {
				$this->viewBuilder()->setLayout('pdf_layout');

				$this->loadModel('SampleInward');
				$this->loadModel('FinalTestResult');
				$this->loadModel('ActualTestData');
				$this->loadModel('CommGrade');
				$this->loadModel('MSampleAllocate');
				$this->loadModel('Workflow');
				$conn = ConnectionManager::get('default');

				$commodity_code = $commodity;
				$sample_code1 = $sample_code;

				$str1 = "SELECT org_sample_code FROM workflow WHERE display='Y' ";

				if ($sample_code1 != '') {

					$str1 .= " AND trim(stage_smpl_cd)='$sample_code1' GROUP BY org_sample_code";
				}

				$sample_code2 = $conn->execute($str1);
				$sample_code2 = $sample_code2->fetchAll('assoc');

				$Sample_code = $sample_code2[0]['org_sample_code'];

				$str2 = "SELECT stage_smpl_cd FROM workflow WHERE display='Y' ";

				if ($sample_code1 != '') {

					$str2 .= " AND org_sample_code='$Sample_code' AND stage_smpl_flag='AS' GROUP BY stage_smpl_cd";
				}

				$sample_code3 = $conn->execute($str2);
				$sample_code3 = $sample_code3->fetchAll('assoc');

				$Sample_code_as = trim($sample_code3[0]['stage_smpl_cd']);

				$this->set('Sample_code_as', $Sample_code_as);

				$this->loadModel('MSampleRegObs');

				$query2 = "SELECT msr.m_sample_reg_obs_code, mso.m_sample_obs_code, mso.m_sample_obs_desc, mst.m_sample_obs_type_code,mst.m_sample_obs_type_value
							   FROM m_sample_reg_obs AS msr
							   INNER JOIN m_sample_obs_type AS mst ON mst.m_sample_obs_type_code=msr.m_sample_obs_type_code
							   INNER JOIN m_sample_obs AS mso ON mso.m_sample_obs_code=mst.m_sample_obs_code AND stage_sample_code='$Sample_code_as'
							   GROUP BY msr.m_sample_reg_obs_code,mso.m_sample_obs_code,mso.m_sample_obs_desc,mst.m_sample_obs_type_code,mst.m_sample_obs_type_value";

				$method_homo = $conn->execute($query2);
				$method_homo = $method_homo->fetchAll('assoc');

				$this->set('method_homo', $method_homo);

				if (null !== ($this->request->getData('ral_lab'))) {

					$data = $this->request->getData('ral_lab');

					$data1 = explode("~", $data);
				

					if ($data1[0] != 'all') {

						$ral_lab = $data1[0];
						// $ral_lab_name = $data1[1];
						$this->set('ral_lab_name', $ral_lab);
					} else {

						$ral_lab = $data1[0];
						$ral_lab_name = 'all';
					}
				} else {

					$ral_lab = '';
					$ral_lab_name = 'all';
				}

				$test = $this->ActualTestData->find('all', array('fields' => array('test_code' => 'distinct(test_code)'), 'conditions' => array('org_sample_code IS' => $Sample_code, 'display' => 'Y')))->toArray();

				$test_string = array();

				$i = 0;

				foreach ($test as $each) {

					$test_string[$i] = $each['test_code'];
					$i++;
				}
				$str = "";
				foreach ($test_string as $row1) {

					$query = $conn->execute("SELECT DISTINCT(grade.grade_desc),grade.grade_code,test_code
												 FROM comm_grade AS cg
												 INNER JOIN m_grade_desc AS grade ON grade.grade_code = cg.grade_code
												 WHERE cg.commodity_code = '$commodity_code' AND cg.test_code = '$row1' AND cg.display = 'Y'");

					$commo_grade = $query->fetchAll('assoc');
					$str = "";

					$this->set('commo_grade', $commo_grade);
				}

				$j = 1;

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
					$getSampleType = $this->SampleInward->find('all', array('fields' => 'sample_type_code', 'conditions' => array('org_sample_code IS' => $Sample_code)))->first();
					$sampleTypeCode = $getSampleType['sample_type_code'];
					if ($sampleTypeCode == 4) {
						$res2 = array(); //this will create report for selected final results, if this res set to blank
					}

					$count_chemist = '';
					$all_chemist_code = array();


					//get al  allocated chemist if sample is for duplicate analysis
					if (isset($res2[0]['count']) > 0) {

						$all_chemist_code = $conn->execute("SELECT ftr.chemist_code
																  FROM m_sample_allocate AS ftr
																 INNER JOIN sample_inward AS si ON si.org_sample_code=ftr.org_sample_code AND si.result_dupl_flag='D' AND ftr.sample_code='$sample_code1' ");

						$all_chemist_code = $all_chemist_code->fetchAll('assoc');

						$count_chemist = count($all_chemist_code);
					}

					//to get approved final result by Inward officer test wise
					$test_result = $this->FinalTestResult->find('list', array('valueField' => 'final_result', 'conditions' => array('org_sample_code IS' => $Sample_code, 'test_code' => $row, 'display' => 'Y')))->toArray();

					//if sample is for duplicate analysis
					//so get result chmeist wise
					$result_D = '';
					$result = array();

					if (isset($res2[0]['count']) > 0) {

						$i = 0;

						foreach ($all_chemist_code as $each) {

							$chemist_code = $each['chemist_code'];

							//get result for each chemist_code
							$get_results = $this->ActualTestData->find('all', array('fields' => array('result'), 'conditions' => array('org_sample_code IS' => $Sample_code, 'chemist_code IS' => $chemist_code, 'test_code IS' => $row, 'display' => 'Y')))->first();

							$result[$i] = $get_results['result'];

							$i = $i + 1;
						}

						//else get result from final test rsult
						//for single anaylsis this is fianl approved result array
					} else {

						if (count($test_result) > 0) {

							foreach ($test_result as $key => $val) {

								$result = $val;
							}
						} else {

							$result = "";
						}
					}

					//for duplicate anaylsis this is final approved result array
					if (count($test_result) > 0) {

						foreach ($test_result as $key => $val) {
							$result_D = $val;
						}
					} else {
						$result_D = "";
					}

					$commencement_date = $this->MSampleAllocate->find('all', array('order' => array('commencement_date' => 'asc'), 'fields' => array('commencement_date'), 'conditions' => array('org_sample_code IS' => $Sample_code, 'display' => 'Y')))->toArray();
					$this->set('comm_date', $commencement_date[0]['commencement_date']);

					if (!empty($count_chemist)) {

						$count_chemist1 =  $count_chemist;
					} else {
						$count_chemist1 = '';
					}

					$this->set('count_test_result', $count_chemist1);


					$minMaxValue = '';

					foreach ($commo_grade as $key => $val) {

						$key = $val['grade_code'];

						foreach ($data as $data4) {

							$data_grade_code = $data4['grade_code'];

							if ($data_grade_code == $key) {

								$grade_code_match = 'yes';

								if (trim($data4['min_max']) == 'Min') {
									$minMaxValue = "<br>(" . $data4['min_max'] . ")";
								} elseif (trim($data4['min_max']) == 'Max') {
									$minMaxValue = "<br>(" . $data4['min_max'] . ")";
								}
							}
						}
					}

					$str .= "<tr><td>" . $j . "</td><td>" . $data_test_name . $minMaxValue . "</td>";

					// Draw tested test reading values,
					foreach ($commo_grade as $key => $val) {

						$key = $val['grade_code'];

						$grade_code_match = 'no';

						foreach ($data as $data4) {

							$data_grade_code = $data4['grade_code'];

							if ($data_grade_code == $key) {

								$grade_code_match = 'yes';

								if (trim($data4['min_max']) == 'Range') {

									$str .= "<td>" . $data4['grade_value'] . "-" . $data4['max_grade_value'] . "</td>";
								} elseif (trim($data4['min_max']) == 'Min') {

									$str .= "<td>" . $data4['grade_value'] . "</td>";
								} elseif (trim($data4['min_max']) == 'Max') {

									$str .= "<td>" . $data4['max_grade_value'] . "</td>";
								} elseif (trim($data4['min_max']) == '-1') {

									$str .= "<td>" . $data4['grade_value'] . "</td>";
								}
							}
						}

						if ($grade_code_match == 'no') {
							$str .= "<td>---</td>";
						}
					}

					//for duplicate analysis chemist wise results
					if ($count_chemist1 > 0) {

						for ($g = 0; $g < $count_chemist; $g++) {
							$str .= "<td  align='center'>" . $result[$g] . "</td>";
						}

						//for final result column
						$str .= "<td  align='center'>" . $result_D . "</td>";

						//for single analysis final results
					} else {
						$str .= "<td>" . $result . "</td>";
					}

					$str .= "<td>" . $data_method_name . "</td></tr>";
					$j++;
				}

				$this->set('table_str', $str);

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

				if ($test_report) {

					$query = $conn->execute("SELECT ur.user_flag,office.ro_office,usr.email
											 FROM workflow AS w
											 INNER JOIN dmi_ro_offices AS office ON office.id = w.src_loc_id
											 INNER JOIN dmi_users AS usr ON usr.id=w.src_usr_cd
											 INNER JOIN dmi_user_roles AS ur ON usr.email= ur.user_email_id
											 WHERE w.org_sample_code='$Sample_code'
											 AND stage_smpl_flag IN('OF','HF')");

					$sample_forwarded_office = $query->fetchAll('assoc');

					$sample_final_date = $this->Workflow->find('all', array('fields' => 'tran_date', 'conditions' => array('stage_smpl_flag' => 'FG', 'org_sample_code IS' => $Sample_code)))->first();
					$sample_final_date['tran_date'] = date('d/m/Y'); //taking current date bcoz creating pdf before grading for preview.


					$this->set('sample_final_date', $sample_final_date['tran_date']);
					$this->set('sample_forwarded_office', $sample_forwarded_office);
					$this->set('test_report', $test_report);
					// Call to function for generate pdf file,
					// change generate pdf file name,
					$current_date = date('d-m-Y');
					$test_report_name = 'grade_report_' . $sample_code1 . '.pdf';

					//store pdf path to sample inward table to preview further
					$pdf_path = '/writereaddata/LIMS/reports/' . $test_report_name;
					$this->SampleInward->updateAll(array('report_pdf' => "$pdf_path"), array('org_sample_code' => $Sample_code));

					$this->Session->write('pdf_file_name', $test_report_name);
					$this->callTcpdf($this->render(), 'I');
				}
			}
		}
	}

	public function commodityWiseConsolidatedReportOfLab()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$month = $this->request->getData('month');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$sample_type = $this->request->getData('sample_type');
			$commodity = $this->request->getData('Commodity');
			$user_id = $_SESSION['user_code'];

			$report_name = "Commodity-wise consolidated report of lab";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'Inward Officer') {
				$query = ReportCustomComponent::getIoCommodityConsolidatedReport($month, $posted_ro_office, $sample_type, $commodity, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, commodity_name, bf_count, analyzed_count, carried_for, month, report_date FROM temp_reportico_io_commodity_consolidated WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'RAL/CAL OIC') {
				$query = ReportCustomComponent::getRalCalOicCommodityConsolidatedReport($month, $posted_ro_office, $sample_type, $commodity, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, commodity_name, bf_count, analyzed_count, carried_for, month, report_date FROM temp_reportico_ral_cal_oic_commodity_consolidated WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Head Office') {
				$query = ReportCustomComponent::getHoCommodityConsolidatedReport($month, $posted_ro_office, $sample_type, $commodity, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, commodity_name, bf_count, analyzed_count, carried_for, month, report_date FROM temp_reportico_ho_commodity_consolidated WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminCommodityConsolidatedReport($month, $posted_ro_office, $sample_type, $commodity, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, commodity_name, bf_count, analyzed_count, carried_for, month, report_date FROM temp_reportico_admin_commodity_consolidated WHERE user_id = '$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("commodity_name")->justify("center")->label("Commodity Name")
				->column("bf_count")->justify("center")->label("Brought Forward")
				->column("analyzed_count")->justify("center")->label("Analyzed")
				->column("carried_for")->justify("center")->label("Carried Forward")
				->column("month")->hide()

				// ->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customHeader("Sample Analysed for Month : {month} ", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				->group("lab_name")
				->header("lab_name")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}

	public function commodityWiseCheckChallengedSamplesAnalysed()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
			$from_date = $from_date->format('Y/m/d');
			$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
			$to_date = $to_date->format('Y/m/d');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$commodity = $this->request->getData('Commodity');
			$user_id = $_SESSION['user_code'];

			$report_name = "Commodity-wise Check & Challenged Samples Analysed during Financial Year 2016-2017";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'Inward Officer') {
				$query = ReportCustomComponent::getIoCommodityCheckChallengedSample($from_date, $to_date, $commodity, $ral_lab_no, $ral_lab_name, $posted_ro_office);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, commodity_name, bf_count, received_count, total, pass_count, fail_count, total_analysis, cf_total, report_date FROM temp_reportico_io_commodity_check_challenged WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'RAL/CAL OIC') {
				$query = ReportCustomComponent::getRalCalOicCommodityCheckChallengedSample($from_date, $to_date, $commodity, $ral_lab_no, $ral_lab_name, $posted_ro_office);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, commodity_name, bf_count, received_count, total, pass_count, fail_count, total_analysis, cf_total, report_date FROM temp_reportico_ral_cal_oic_commodity_check_challenged WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminCommodityCheckChallengedSample($from_date, $to_date, $commodity, $ral_lab_no, $ral_lab_name, $posted_ro_office);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, commodity_name, bf_count, received_count, total, pass_count, fail_count, total_analysis, cf_total, report_date FROM temp_reportico_admin_commodity_check_challenged WHERE user_id = '$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("commodity_name")->justify("center")->label("Commodity Name")
				->column("bf_count")->justify("center")->label("B/F")
				->column("received_count")->justify("center")->label("Sample Received during")
				->column("total")->justify("center")->label("Total")
				->column("pass_count")->justify("center")->label("Sample Analyzed Standard")
				->column("fail_count")->justify("center")->label("Sample Analyzed Sub-Standard")
				->column("total_analysis")->justify("center")->label("Sample Analyzed Total")
				->column("cf_total")->justify("center")->label("C/F")

				// ->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				// ->customHeader("Sample Analysed for Month : {month} ", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				->group("lab_name")
				->header("lab_name")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}

	public function broughtForwardAnalysedAndCarriedForwardOfSamples()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$month = $this->request->getData('month');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user_id = $_SESSION['user_code'];

			$report_name = "Brought forward, analysed and carried forward of samples";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'Inward Officer') {
				$query = ReportCustomComponent::getIoBroughtForwardAnalysedCarrSample($month, $ral_lab, $posted_ro_office, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, chemist_name, bf_count, received_count, total, analyzed_count_in_month, analyzed_count_in_month_repeat, carried_for, month,report_date FROM temp_reportico_io_brg_fwd_ana_carr_fwd_sam WHERE user_id ='$user_id'";
				}
			}

			if ($role == 'RAL/CAL OIC') {
				$query = ReportCustomComponent::getRalCalOicBroughtForwardAnalysedCarrSample($month, $ral_lab, $posted_ro_office, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, chemist_name, bf_count, received_count, total, analyzed_count_in_month, analyzed_count_in_month_repeat, carried_for, month,report_date FROM temp_reportico_ral_cal_oic_brg_fwd_ana_carr_fwd_sam WHERE user_id ='$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminBroughtForwardAnalysedCarrSample($month, $ral_lab, $posted_ro_office, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, chemist_name, bf_count, received_count, total, analyzed_count_in_month, analyzed_count_in_month_repeat, carried_for, month,report_date FROM temp_reportico_admin_brg_fwd_ana_carr_fwd_sam WHERE user_id ='$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("bf_count")->justify("center")->label("BF")
				->column("received_count")->justify("center")->label("Received during month")
				->column("total")->justify("center")->label("Total")
				->column("analyzed_count_in_month")->justify("center")->label("Sample analyze during month original")
				->column("analyzed_count_in_month_repeat")->justify("center")->label("Duplicate")
				->column("carried_for")->justify("center")->label("Carried Forward")
				->column("month")->hide()

				// ->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customHeader("Sample Analysed for Month : {month} ", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				->group("chemist_name")
				->header("chemist_name")

				->group("lab_name")
				->header("lab_name")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}

	public function allOfficesStatisticsCounts()
	{
		$this->autoRender = false;
		$user_id = $_SESSION['user_code'];

		if ($this->request->is('post')) {
			$from_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('from_date'));
			$from_date = $from_date->format('Y/m/d');
			$to_date = DateTime::createFromFormat('d/m/Y', $this->request->getData('to_date'));
			$to_date = $to_date->format('Y/m/d');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$commodity = $this->request->getData('Commodity');
			$role = $this->request->getData('role');
			$office_type = $this->request->getData('office_type');

			$report_name = "Overall Statistical Counts(RAL/CAL)";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;

			if ($role == 'RAL/CAL OIC') {
				if ($office_type == 'RAL') {
					$query = ReportCustomComponent::getRalCalOicAllStaticsCounts($from_date, $to_date, $commodity, $office_type);
					$sql = "SELECT ofsc_name, inward, forward, forward_to_test, internal, external, commodity_name FROM temp_reportico_ral_cal_oic_all_office_statistic WHERE user_id ='$user_id'  ORDER BY ofsc_name";
					ini_set("include_path", reporticoReport);
					require_once("vendor/autoload.php");
					require_once("vendor/reportico-web/reportico/src/Reportico.php");

					Builder::build()
						->properties(["bootstrap_preloaded" => true])
						->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
						->title($report_name)

						->sql($sql)

						->column("ofsc_name")->justify("center")->label("RAL/CAL")
						->column("inward")->justify("center")->label("Sample Accepted by Inward Officer")
						->column("forward")->justify("center")->label("Forwarded by RAL/CAL to Other RAL/CAL")
						->column("forward_to_test")->justify("center")->label("With Chemist for Test")
						->column("internal")->justify("center")->label("Finalized by Incharge(Internal Sample)")
						->column("external")->justify("center")->label("Finalized by Incharge(RO/SO Sample)")

						// ->to('CSV') //Auto download excel file	

						->expression("total1")->sum("inward", "commodity_name")
						->expression("total2")->sum("forward", "commodity_name")
						->expression("total3")->sum("forward_to_test", "commodity_name")
						->expression("total4")->sum("internal", "commodity_name")
						->expression("total5")->sum("external", "commodity_name")

						->group("commodity_name")
						->header("commodity_name")
						->customHeader("During the Period From $from_date To $to_date ", "")
						->trailer("total1")->below("inward")->label("Total : ")
						->trailer("total2")->below("forward")->label("Total : ")
						->trailer("total3")->below("forward_to_test")->label("Total : ")
						->trailer("total4")->below("internal")->label("Total : ")
						->trailer("total5")->below("external")->label("Total : ")

						->page()
						->pagetitledisplay("TopOfFirstPage")

						->header($headerone, "")

						->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
						->execute();
				}


				if ($office_type == 'RO') {
					$query = ReportCustomComponent::getRalCalOicAllStaticsCounts($from_date, $to_date, $commodity, $office_type);
					$sql = "SELECT ofsc_name, pending, forward, result, commodity_name FROM temp_reportico_ral_cal_oic_ro_all_office_statistic WHERE user_id ='$user_id' ORDER BY ofsc_name";

					ini_set("include_path", reporticoReport);
					require_once("vendor/autoload.php");
					require_once("vendor/reportico-web/reportico/src/Reportico.php");

					Builder::build()
						->properties(["bootstrap_preloaded" => true])
						->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
						->title("Overall Statistical Counts(RO/SO)")

						->sql($sql)

						->column("ofsc_name")->justify("center")->label("RO/SO")
						->column("pending")->justify("center")->label("Pending for Forwarding")
						->column("forward")->justify("center")->label("Forwarded to RAL/CAL")
						->column("result")->justify("center")->label("Result Received")

						// ->to('CSV') //Auto download excel file	

						->expression("total1")->sum("pending", "commodity_name")
						->expression("total2")->sum("forward", "commodity_name")
						->expression("total3")->sum("result", "commodity_name")

						->group("commodity_name")
						->header("commodity_name")
						->customHeader("During the Period From $from_date To $to_date ", "")
						->trailer("total1")->below("pending")->label("Total : ")
						->trailer("total2")->below("forward")->label("Total : ")
						->trailer("total3")->below("result")->label("Total : ")

						->page()
						->pagetitledisplay("TopOfFirstPage")

						->header($headerone, "")

						->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
						->execute();
				}
			}

			if ($role == 'Head Office') {
				if ($office_type == 'RAL') {
					$query = ReportCustomComponent::getHoAllStaticsCounts($from_date, $to_date, $commodity, $office_type);

					$sql = "SELECT ofsc_name, inward, forward, forward_to_test, internal, external, commodity_name FROM temp_reportico_ho_all_office_statistic WHERE user_id ='$user_id' ORDER BY ofsc_name";


					ini_set("include_path", reporticoReport);
					require_once("vendor/autoload.php");
					require_once("vendor/reportico-web/reportico/src/Reportico.php");

					Builder::build()
						->properties(["bootstrap_preloaded" => true])
						->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
						->title($report_name)

						->sql($sql)

						->column("ofsc_name")->justify("center")->label("RAL/CAL")
						->column("inward")->justify("center")->label("Sample Accepted by Inward Officer")
						->column("forward")->justify("center")->label("Forwarded by RAL/CAL to Other RAL/CAL")
						->column("forward_to_test")->justify("center")->label("With Chemist for Test")
						->column("internal")->justify("center")->label("Finalized by Incharge(Internal Sample)")
						->column("external")->justify("center")->label("Finalized by Incharge(RO/SO Sample)")

						// ->to('CSV') //Auto download excel file	

						->expression("total1")->sum("inward", "commodity_name")
						->expression("total2")->sum("forward", "commodity_name")
						->expression("total3")->sum("forward_to_test", "commodity_name")
						->expression("total4")->sum("internal", "commodity_name")
						->expression("total5")->sum("external", "commodity_name")

						->group("commodity_name")
						->header("commodity_name")
						->customHeader("During the Period From $from_date To $to_date ", "")
						->trailer("total1")->below("inward")->label("Total : ")
						->trailer("total2")->below("forward")->label("Total : ")
						->trailer("total3")->below("forward_to_test")->label("Total : ")
						->trailer("total4")->below("internal")->label("Total : ")
						->trailer("total5")->below("external")->label("Total : ")

						->page()
						->pagetitledisplay("TopOfFirstPage")

						->header($headerone, "")

						->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
						->execute();
				}
				if ($office_type == 'RO') {
					$query = ReportCustomComponent::getHoAllStaticsCounts($from_date, $to_date, $commodity, $office_type);
					$sql = "SELECT ofsc_name, pending, forward, result, commodity_name FROM temp_reportico_ho_ro_all_office_statistic WHERE user_id ='$user_id' ORDER BY ofsc_name";
					ini_set("include_path", reporticoReport);
					require_once("vendor/autoload.php");
					require_once("vendor/reportico-web/reportico/src/Reportico.php");

					Builder::build()
						->properties(["bootstrap_preloaded" => true])
						->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
						->title("Overall Statistical Counts(RO/SO)")

						->sql($sql)

						->column("ofsc_name")->justify("center")->label("RO/SO")
						->column("pending")->justify("center")->label("Pending for Forwarding")
						->column("forward")->justify("center")->label("Forwarded to RAL/CAL")
						->column("result")->justify("center")->label("Result Received")

						// ->to('CSV') //Auto download excel file	

						->expression("total1")->sum("pending", "commodity_name")
						->expression("total2")->sum("forward", "commodity_name")
						->expression("total3")->sum("result", "commodity_name")

						->group("commodity_name")
						->header("commodity_name")
						->customHeader("During the Period From $from_date To $to_date ", "")
						->trailer("total1")->below("pending")->label("Total : ")
						->trailer("total2")->below("forward")->label("Total : ")
						->trailer("total3")->below("result")->label("Total : ")

						->page()
						->pagetitledisplay("TopOfFirstPage")

						->header($headerone, "")

						->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
						->execute();
				}
			}
		}
	}

	/**
	 * Used same function from 43 made changes in Query and removed return line
	 */
	public function finalSampleTestReports()
	{
		$con = ConnectionManager::get('default');
		$this->loadModel('SampleInward');
		$this->loadModel('workflow');

		$role = $_SESSION['role'];

		//condition updated on 20-05-2021 by Amol, if any user id replaced with same role in same office
		//so applied location wise condition RAL/CAL OIC user
		if ($role == 'RAL/CAL OIC') {
			//get user loc id
			$loc_id = $_SESSION['posted_ro_office'];
			$result = $con->execute("SELECT org_sample_code FROM workflow WHERE src_loc_id = '$loc_id' GROUP BY org_sample_code");
		} else {
			$result = $con->execute("SELECT org_sample_code FROM workflow WHERE src_usr_cd = '" . $_SESSION['user_code'] . "' GROUP BY org_sample_code");
		}

		$records = $result->fetchAll('assoc');
		$result->closeCursor();
		$final_reports = array();
		if (!empty($records)) {
			foreach ($records as $sample_code) {
				$sample_code = implode(',', $sample_code);
				$sample_code = trim($sample_code);
				$final_grading = $con->execute("SELECT w.stage_smpl_cd, w.tran_date, mcc.category_name, mc.commodity_name, mst.sample_type_desc, mc.commodity_code
				FROM workflow w
				INNER JOIN sample_inward si ON si.org_sample_code = w.org_sample_code
				INNER JOIN m_commodity_category mcc ON mcc.category_code = si.category_code
				INNER JOIN m_commodity mc ON mc.commodity_code = si.commodity_code
				INNER JOIN m_sample_type mst ON mst.sample_type_code = si.sample_type_code
				WHERE w.stage_smpl_flag = 'FG' AND w.org_sample_code = '$sample_code'");
				$recordsFG = $final_grading->fetchAll('assoc');

				$final_grading->closeCursor();
				if (!empty($recordsFG)) {
					$final_reports[] = $recordsFG;
				}
			}
		}
		$this->set('final_reports', $final_reports);
	}

	public function sampleTestReportCode($sample_code, $sample_test_mc)
	{

		$this->Session->write('sample_test_code', $sample_code);
		$this->Session->write('sample_test_mc', $sample_test_mc);
		$this->redirect('/report/sample_test_report');
	}

	/***
	 * Used same code as in 43 but with change in query format
	 */
	public function sampleTestReport()
	{
		$con = ConnectionManager::get('default');

		$this->viewBuilder()->setLayout('pdf_layout');

		$this->loadModel('SampleInward');
		$this->loadModel('FinalTestResult');
		$this->loadModel('ActualTestData');
		$this->loadModel('CommGrade');
		$this->loadModel('MSampleAllocate');
		$this->loadModel('Workflow');

		$commodity_code = $this->Session->read('sample_test_mc');
		$sample_code1 = $this->Session->read('sample_test_code');
		
		$str1 = "select org_sample_code from workflow where display='Y' ";
		if (!empty($sample_code1)) {
			$str1 .= " AND trim(stage_smpl_cd)='$sample_code1' group by org_sample_code";
		}

		$sample_code2 = $con->execute($str1); 
		$sample_code2 = $sample_code2->fetchAll('assoc');
		
		$Sample_code = $sample_code2[0]['org_sample_code'];
		
		$str2 = "select stage_smpl_cd from workflow where display='Y' ";
		if ($sample_code1 != '') {
			$str2 .= " AND org_sample_code='$Sample_code' and stage_smpl_flag='AS' group by stage_smpl_cd";
		}

		$sample_code3 = $con->execute($str2);
		$sample_code3 = $sample_code3->fetchAll('assoc');

		$Sample_code_as = trim($sample_code3[0]['stage_smpl_cd']);
		$this->set('Sample_code_as', $Sample_code_as);

		$this->loadModel('MSampleRegObs');

		$query2 = "SELECT msr.m_sample_reg_obs_code,mso.m_sample_obs_code,mso.m_sample_obs_desc,mst.m_sample_obs_type_code,mst.m_sample_obs_type_value from m_sample_reg_obs as msr 
				inner join m_sample_obs_type as mst ON mst.m_sample_obs_type_code=msr.m_sample_obs_type_code
				inner join m_sample_obs as mso ON mso.m_sample_obs_code=mst.m_sample_obs_code
				and stage_sample_code='$Sample_code_as' group by msr.m_sample_reg_obs_code,mso.m_sample_obs_code,mso.m_sample_obs_desc,mst.m_sample_obs_type_code,mst.m_sample_obs_type_value";

		$method_homo = $con->execute($query2);
		$method_homo = $method_homo->fetchAll('assoc');

		$this->set('method_homo', $method_homo);

		if (isset($this->request->data['ral_lab'])) {
			$data = $this->request->data['ral_lab'];

			$data1 = explode("~", $data);
			if ($data1[0] != 'all') {
				$ral_lab = $data1[0];
				$ral_lab_name = $data1[1];
				$this->set('ral_lab_name', $ral_lab_name);
			} else {
				$ral_lab = $data1[0];
				$ral_lab_name = 'all';
			}
		} else {
			$ral_lab = '';
			$ral_lab_name = 'all';
		}

		$test = $this->ActualTestData->find('all', array('fields' => array('test_code' => 'distinct(test_code)'), 'conditions' => array('org_sample_code IS' => $Sample_code, 'display' => 'Y')))->toArray();

		$test_string = array();
		$i = 0;
		foreach ($test as $test) {
			$test_string[$i] = $test['test_code'];
			$i++;
		}

		foreach ($test_string as $row1) {
			$query = $con->execute("SELECT DISTINCT(grade.grade_desc),grade.grade_code,test_code
												 FROM comm_grade AS cg
												 INNER JOIN m_grade_desc AS grade ON grade.grade_code = cg.grade_code
												 WHERE cg.commodity_code = '$commodity_code' AND cg.test_code = '$row1' AND cg.display = 'Y'");

			$commo_grade = $query->fetchAll('assoc');
			$str = "";
			$this->set('commo_grade', $commo_grade);
		}

		$j = 1;

		foreach ($test_string as $row) {
			$query = $con->execute("SELECT cg.grade_code,cg.grade_value,cg.max_grade_value,cg.min_max
												 FROM comm_grade AS cg
												 INNER JOIN m_test_method AS tm ON tm.method_code = cg.method_code
												 INNER JOIN m_test AS t ON t.test_code = cg.test_code
												 WHERE cg.commodity_code = '$commodity_code' AND cg.test_code = '$row' AND cg.display = 'Y'
												 ORDER BY cg.grade_code ASC");


			$data = $query->fetchAll('assoc');


			$query = $con->execute("SELECT t.test_name,tm.method_name
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

			$res2	= $con->execute($qry1);
			$res2 = $res2->fetchAll('assoc');

			//get sample type code from sample sample inward table, to check if sample type is "Challenged"
			//if sample type is "challenged" then get report for selected final values only, no matter if single/duplicate analysis
			//applied on 27-10-2011 by Amol
			$getSampleType = $this->SampleInward->find('all', array('fields' => 'sample_type_code', 'conditions' => array('org_sample_code IS' => $Sample_code)))->first();
			$sampleTypeCode = $getSampleType['sample_type_code'];
			if ($sampleTypeCode == 4) {
				$res2 = array(); //this will create report for selected final results, if this res set to blank
			}

			$count_chemist = '';
			if (isset($res2[0][0]['count']) > 0) {
				$test_result = $this->FinalTestResult->find('all', array(

					'fields' => array('chemist_code', 'test_code', 'final_result'),
					'conditions' => array('org_sample_code' => $Sample_code, 'test_code' => $row, 'duplicate_flg' => 'D')
				));

				$count_chemist = $this->FinalTestResult->query("select count(ftr.chemist_code) from m_sample_allocate as ftr  
																Inner join sample_inward as si ON 
																si.org_sample_code=ftr.org_sample_code and si.result_dupl_flag='D' and ftr.sample_code='$sample_code1' ");
			} else {

				$test_result = $this->FinalTestResult->find('list', array(
					'fields' => array('final_result'),
					'conditions' => array('org_sample_code IS' => $Sample_code, 'test_code' => $row, 'display' => 'Y')
				))->toArray();
			}


			if (isset($res2[0]['count']) > 0) {

				$i = 0;
				foreach ($test_result as $test_result1) {
					$result[$i] = $test_result1['Final_Test_Result']['final_result'];
					$i++;
				}
			} else {

				if (count($test_result) > 0) {
					foreach ($test_result as $key => $val) {
						$result = $val;
					}
				} else {
					$result = "";
				}
			}

			$commencement_date = $this->MSampleAllocate->find('all', array('order' => array('commencement_date' => 'asc'), 'fields' => array('commencement_date'), 'conditions' => array('org_sample_code' => $Sample_code, 'display' => 'Y')))->toArray();
			$this->set('comm_date', $commencement_date[0]['commencement_date']);

			if (!empty($count_chemist)) {
				$count_chemist1 =  $count_chemist[0][0]['count'];
			} else {
				$count_chemist1 = '';
			}
			$this->set('count_test_result', $count_chemist1);


			$minMaxValue = '';
			foreach ($commo_grade as $key => $val) {
				$key = $val['grade_code'];

				foreach ((array)$data as $data4) {

					$data_grade_code = $data4['grade_code'];

					if ($data_grade_code == $key) {

						$grade_code_match = 'yes';

						if (trim($data4['min_max']) == 'Min') {
							$minMaxValue = "<br/>(" . $data4['min_max'] . ")";
						} else if (trim($data4['min_max']) == 'Max') {
							$minMaxValue = "<br/>(" . $data4['min_max'] . ")";
						}
					}
				}
			}

			$str .= "<tr><td class='td1'>" . $j . "</td><td class='td1'>" . $data_test_name . $minMaxValue . "</td>";

			// Draw tested test reading values, Done by Pravin Bhakare, 26-06-2019
			foreach ($commo_grade as $key => $val) {
				$key = $val['grade_code'];

				$grade_code_match = 'no';
				foreach ((array)$data as $data4) {

					$data_grade_code = $data4['grade_code'];

					if ($data_grade_code == $key) {

						$grade_code_match = 'yes';

						if (trim($data4['min_max']) == 'Range') {
							$str .= "<td class='td1'>" . $data4['grade_value'] . "-" . $data4['max_grade_value'] . "</td>";
						} else if (trim($data4['min_max']) == 'Min') {

							$str .= "<td class='td1'>" . $data4['grade_value'] . "</td>";
						} else if (trim($data4['min_max']) == 'Max') {

							$str .= "<td class='td1'>" . $data4['max_grade_value'] . "</td>";
						} else if (trim($data4['min_max']) == '-1') {
							$str .= "<td class='td1'>" . $data4['grade_value'] . "</td>";
						}
					}
				}

				if ($grade_code_match == 'no') {
					$str .= "<td class='td1'>---</td>";
				}
			}

			if ($count_chemist1 > 0) {

				for ($g = 0; $g < $count_chemist[0][0]['count']; $g++) {
					$str .= "<td class='text-center'>" . $result[$g] . "</td>";
				}
			} else {
				$str .= "<td class='td1'>" . $result . "</td>";
			}

			$str .= "<td class='td1'>" . $data_method_name . "</td></tr>";
			$j++;
		}

		$this->set('table_str', $str);

		$query = $con->execute("SELECT si.*,mc.commodity_name, mcc.category_name, st.sample_type_desc, ct.container_desc, pc.par_condition_desc, uw.unit_weight, rf.ro_office, sa.sample_code, ur.user_flag, gd.grade_desc, u1.f_name, u1.l_name, rf2.ro_office
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

		if ($test_report) {

			$query = $con->execute("SELECT ur.user_flag,office.ro_office,usr.email
			 FROM workflow AS w
			 INNER JOIN dmi_ro_offices AS office ON office.id = w.src_loc_id
			 INNER JOIN dmi_users AS usr ON usr.id=w.src_usr_cd
			 INNER JOIN dmi_user_roles AS ur ON usr.email= ur.user_email_id
			 WHERE w.org_sample_code='$Sample_code'
			 AND stage_smpl_flag IN('OF','HF')");

			$sample_forwarded_office = $query->fetchAll('assoc');

			$sample_final_date = $this->Workflow->find('all', array('fields' => 'tran_date', 'conditions' => array('stage_smpl_flag' => 'FG', 'org_sample_code IS' => $Sample_code)))->first();
			$sample_final_date['tran_date'] = date('d/m/Y'); //taking current date bcoz creating pdf before grading for preview.

			$this->set('sample_final_date', $sample_final_date['tran_date']);
			$this->set('sample_forwarded_office', $sample_forwarded_office);
			$this->set('test_report', $test_report);

			// Call to function for generate pdf file, on 01-06-2019 , By Pravin Bhkare
			// change generate pdf file name, on 13-12-2019 , By Pravin Bhkare

			$current_date = date('d/m/Y');
			$test_report_name = 'Sample_test_report_' . $sample_code1 . "_" . $current_date;

			// $this->download_report_pdf('report/test_report_view', $test_report_name); Removed By Shweta Apale 16-11-2021 this line was in 43 code

			$this->Session->write('pdf_file_name', $test_report_name);
			$this->callTcpdf($this->render(), 'I');
		}
	}

	public function detailsOfSampleAnalyzedByChemist()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$month = $this->request->getData('month');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user_id = $_SESSION['user_code'];
			$year = $this->request->getData('year');
			$month_name = date("F", mktime(0, 0, 0, $month, 10));

			$report_name = "Details of Sample Analyzed by Chemist for the month of " . $month_name . ' , ' . $year;

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDolDetailsSampleAnalyzedByChemist($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, name_chemist, sample_type_desc, working_days, check_count, check_apex_count, challenged_count, ilc_count,research_count, retesting_count, other, project_sample, commodity_name, no_of_param, other_work, total_no, norm, counts,report_date FROM temp_reportico_dol_details_sample_analyzed WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Head Office') {
				$query = ReportCustomComponent::getHoDetailsSampleAnalyzedByChemist($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, name_chemist, sample_type_desc, working_days, check_count, check_apex_count, challenged_count, ilc_count,research_count, retesting_count, other, project_sample, commodity_name,  no_of_param, other_work, total_no, norm, counts,report_date FROM temp_reportico_ho_details_sample_analyzed WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminDetailsSampleAnalyzedByChemist($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, name_chemist, sample_type_desc, working_days, check_count, check_apex_count, challenged_count, ilc_count,research_count, retesting_count, other, project_sample, commodity_name, no_of_param, other_work, total_no, norm, counts,report_date FROM temp_reportico_admin_details_sample_analyzed WHERE user_id = '$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("working_days")->justify("center")->label("No. of working days")
				->column("check_count")->justify("center")->label("Check")
				->column("check_apex_count")->justify("center")->label("Check(APEX)")
				->column("challenged_count")->justify("center")->label(" Challenged")
				->column("ilc_count")->justify("center")->label("ILC")
				->column("research_count")->justify("center")->label("Research")
				->column("retesting_count")->justify("center")->label("Retesting")
				->column("other")->justify("center")->label("Other")
				->column("project_sample")->justify("center")->label("Project Samples")
				->column("commodity_name")->justify("center")->label("Name of Commodity")
				->column("no_of_param")->justify("center")->label("No. of parameters")
				->column("other_work")->justify("center")->label("Other Work")
				->column("norm")->justify("center")->label("Whether analyzed as per norm")
				->column("name_chemist")->justify("center")->label("Name of Chemist")
				->column("sample_type_desc")->justify("center")->label("Sample Type")
				->column("total_no")->justify("center")->label("Total Nos.")
				->column("counts")->hide()

				//->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customTrailer("Total Number of Samples : {counts} ", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				// ->customTrailer("{$name} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 10px;margin-bottom:10px;")
				// ->customTrailer("({$email}) ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 25px;margin-bottom:10px;")
				// ->customTrailer("{$role} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 45px; margin-bottom:10px;")


				->group("lab_name")
				->header("lab_name")

				->group("name_chemist")
				->header("name_chemist")

				->group("sample_type_desc")
				->header("sample_type_desc")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")


				// ->footer("Time: date('Y-m-d H:i:s')", "font-size: 8pt; text-align: right; font-style: italic; width: 100%; margin-top: 55px; padding-bottom:60px;")
				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}

	public function monthlyReportOfCarryForwardAndBroughtForward()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$month = $this->request->getData('month');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user_id = $_SESSION['user_code'];
			$year = $this->request->getData('year');
			$month_name = date("F", mktime(0, 0, 0, $month, 10));

			$report_name = "Monthly Report of Carry Forward & Brought Forward for the month of " . $month_name . ' , ' . $year;

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDolMonthlyCarryBroughtForward($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, category_name, lab_name, check_bf_count, check_apex_bf_count, challenged_bf_count, check_received_count, check_apex_received_count, challenged_received_count,total_check, total_check_apex, total_challenged, check_analyzed_count_in_month, check_apex_analyzed_count_in_month, challenged_analyzed_count_in_month,  carry_check, carry_check_apex, carry_challenged, counts,report_date FROM temp_reportico_dol_monthly_carry_brought_fwd WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Head Office') {
				$query = ReportCustomComponent::getHoMonthlyCarryBroughtForward($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, category_name, lab_name, check_bf_count, check_apex_bf_count, challenged_bf_count, check_received_count, check_apex_received_count, challenged_received_count,total_check, total_check_apex, total_challenged, check_analyzed_count_in_month, check_apex_analyzed_count_in_month, challenged_analyzed_count_in_month,  carry_check, carry_check_apex, carry_challenged, counts,report_date FROM temp_reportico_ho_monthly_carry_brought_fwd WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminMonthlyCarryBroughtForward($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, category_name, lab_name, check_bf_count, check_apex_bf_count, challenged_bf_count, check_received_count, check_apex_received_count, challenged_received_count,total_check, total_check_apex, total_challenged, check_analyzed_count_in_month, check_apex_analyzed_count_in_month, challenged_analyzed_count_in_month,  carry_check, carry_check_apex, carry_challenged, counts,report_date FROM temp_reportico_admin_monthly_carry_brought_fwd WHERE user_id = '$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("category_name")->justify("center")->label("Division")
				->column("check_bf_count")->justify("center")->label("Brought Forward Check")
				->column("check_apex_bf_count")->justify("center")->label("Brought Forward Check(APEX)")
				->column("challenged_bf_count")->justify("center")->label(" Brought Forward Challenged")
				->column("check_received_count")->justify("center")->label("Received Check")
				->column("check_apex_received_count")->justify("center")->label("Received Check(APEX)")
				->column("challenged_received_count")->justify("center")->label("Received Challenged")
				->column("total_check")->justify("center")->label("Total  Check")
				->column("total_check_apex")->justify("center")->label("Total Check(APEX)")
				->column("total_challenged")->justify("center")->label("Total Challenged")
				->column("check_analyzed_count_in_month")->justify("center")->label("Analyze Check")
				->column("check_apex_analyzed_count_in_month")->justify("center")->label("Analyze Check(APEX)")
				->column("challenged_analyzed_count_in_month")->justify("center")->label("Analyze Challenged")
				->column("carry_check")->justify("center")->label("Carry Forward Check")
				->column("carry_check_apex")->justify("center")->label("Carry Forward Check(APEX)")
				->column("carry_challenged")->justify("center")->label("Carry Forward Challenged")
				->column("counts")->hide()

				//->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customTrailer("Total Number of Samples : {counts} ", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				// ->customTrailer("{$name} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 10px;margin-bottom:10px;")
				// ->customTrailer("({$email}) ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 25px;margin-bottom:10px;")
				// ->customTrailer("{$role} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 45px; margin-bottom:10px;")

				->group("lab_name")
				->header("lab_name")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				// ->footer("Time: date('Y-m-d H:i:s')", "font-size: 8pt; text-align: right; font-style: italic; width: 100%; margin-top: 60px; padding-bottom:60px;")
				->execute();
		}
	}
	
	// informationOfAnnexureEAlongWithMprDivisionWise replace name
	//chemistwisedetailsforthesampleanalysedandcarryforward 30-08-22
	public function chemistWiseDetailsForTheSampleAnalysedAndCarryForward()
	{

		$this->autoRender = false;

		if ($this->request->is('post')) {
			$month = $this->request->getData('month');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user_id = $_SESSION['user_code'];
			$year = $this->request->getData('year');
			$month_name = date("F", mktime(0, 0, 0, $month, 10));

			$report_name = "Chemist Wise Details For The Sample Analysed And Carry Forward for the month of " . $month_name . ' , ' . $year;

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'DOL') {
				// getDolInfoAnnMprDivisionWise
				$query = ReportCustomComponent::getDolChemAnnMprDivisionWise($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no,category_name, commodity_name,bf_count,allotment_count,check_bf_count ,check_apex_bf_count ,challenged_bf_count ,check_received_count ,check_apex_received_count ,challenged_received_count ,check_analyzed_count_in_month ,check_apex_analyzed_count_in_month ,challenged_analyzed_count_in_month ,carry_check ,carry_check_apex ,carry_challenged ,lab_name, name_chemist, no_of_param,no_of_para_analys, remark, counts,report_date FROM temp_reportico_dol_info_mpr_division WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Head Office') {
				$query = ReportCustomComponent::getHoChemAnnMprDivisionWise($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, sample_type_desc ,allotment_count,category_name,commodity_name,bf_count,lab_name, name_chemist, no_of_param,no_of_para_analys, remark, counts,report_date FROM temp_reportico_ho_info_mpr_division WHERE user_id = '$user_id'";
				}
			}




			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminChemAnnMprDivisionWise($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, commodity_name,bf_count,allotment_count,check_bf_count ,check_apex_bf_count ,challenged_bf_count ,check_received_count ,check_apex_received_count ,challenged_received_count ,check_analyzed_count_in_month ,check_apex_analyzed_count_in_month ,challenged_analyzed_count_in_month ,carry_check ,carry_check_apex ,carry_challenged ,lab_name, name_chemist, no_of_param,no_of_para_analys, remark, counts,report_date FROM temp_reportico_admin_info_mpr_division WHERE user_id = '$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("category_name")->justify("center")->label("Division")
				->column("commodity_name")->justify("center")->label("AGMARK Commodity")
				->column("bf_count")->justify("center")->label("Brought Forward")
																							 
				->column("no_of_para_analys")->justify("center")->label("Analyzed")
																							 
																										
																									   
																														   
																																	  
																																	 
				->column("no_of_param")->justify("center")->label("No. of Parameters in Commodity")
				->column("remark")->justify("center")->label("Carry Forward")
				->column("allotment_count")->justify("center")->label("Allotted")
				
				// ->column("name_chemist")->justify("center")->label("Name of Chemist")
				->column("sample_type_desc")->justify("center")->label("Sample Type")
				// ->column("allotment_count")->justify("center")->label("Alotted")
			    // ->column("check_bf_count")->justify("center")->label("No. of Check Sample BF")
				// ->column("check_apex_bf_count")->justify("center")->label("No. of Check(APEX) Sample BF")
				// ->column("challenged_bf_count")->justify("center")->label("No. of Challenged Sample BF")
				// ->column("check_received_count")->justify("center")->label("No. of Check Sample Alloted")
				// ->column("check_apex_received_count")->justify("center")->label("No. of Check(APEX) Sample Alloted")
				// ->column("challenged_received_count")->justify("center")->label("No. of Challenged Sample Alloted")
				// ->column("check_analyzed_count_in_month")->justify("center")->label("No. of Check Sample Analyzed in (commodity-wise)")
				// ->column("check_apex_analyzed_count_in_month")->justify("center")->label("No. of Check(APEX) Sample Analyzed in (commodity-wise)")
				// ->column("challenged_analyzed_count_in_month")->justify("center")->label("No. of Challenged Sample Analyzed in (commodity-wise)")
				// ->column("carry_check")->justify("center")->label("No. of Check Sample Pending CF")
				// ->column("carry_check_apex")->justify("center")->label("No. of Check(APEX) Sample Pending CF")
				// ->column("carry_challenged")->justify("center")->label("No. of Challenged Sample Pending CF")
				
				->column("counts")->hide()

				//->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customTrailer("Total Number of Samples : {counts} ", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				// ->customTrailer("{$name} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 10px;margin-bottom:10px;")
				// ->customTrailer("({$email}) ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 25px;margin-bottom:10px;")
				// ->customTrailer("{$role} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 45px; margin-bottom:10px;")


				->group("lab_name")
				->header("lab_name")

				->group("name_chemist")
				->header("name_chemist")

				->group("sample_type_desc")
				 ->header("sample_type_desc")

				// ->expression("total1")->sum("check_analyzed_count_in_month", "commodity_name")
				// ->expression("total2")->sum("check_apex_analyzed_count_in_month", "commodity_name")
				// ->expression("total3")->sum("challenged_analyzed_count_in_month", "commodity_name")

				->group("commodity_name")
				// ->header("commodity_name")

				// ->group("sample_type_desc")
				//  ->header("sample_type_desc")

				// ->trailer("total1")->below("check_analyzed_count_in_month")->label("Total : ")
				// ->trailer("total2")->below("check_apex_analyzed_count_in_month")->label("Total : ")
				// ->trailer("total3")->below("challenged_analyzed_count_in_month")->label("Total : ")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")
				// ->footer("Time: date('Y-m-d H:i:s')", "font-size: 8pt; text-align: right; font-style: italic; width: 100%; margin-top: 55px; padding-bottom:60px;")
				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}

	public function detailsOfSamplesAnalyzedByRalsAnnexureB()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$month = $this->request->getData('month');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user_id = $_SESSION['user_code'];
			$year = $this->request->getData('year');
			$month_name = date("F", mktime(0, 0, 0, $month, 10));

			$report_name = "Details of Sample Analyzed by RAL for the month of " . $month_name . ' , ' . $year;

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDolDetailsSampleAnalyzedByRal($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, commodity_name, lab_name, commodity_counts, org_sample_code, sample_type_desc, inter_lab_compare, pvt_sample, inter_check, proj_sample, repeat_sample, pt_samp, report_date, counts FROM temp_reportico_dol_details_sample_analyzed_ral WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Head Office') {
				$query = ReportCustomComponent::getHoDetailsSampleAnalyzedByRal($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, commodity_name, lab_name, commodity_counts, org_sample_code, sample_type_desc, inter_lab_compare, pvt_sample, inter_check, proj_sample, repeat_sample, pt_samp, report_date, counts FROM temp_reportico_ho_details_sample_analyzed_ral WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminDetailsSampleAnalyzedByRal($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, commodity_name, lab_name, commodity_counts, org_sample_code, sample_type_desc, inter_lab_compare, pvt_sample, inter_check, proj_sample, repeat_sample, pt_samp, report_date, counts FROM temp_reportico_admin_details_sample_analyzed_ral WHERE user_id = '$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("commodity_name")->justify("center")->label("Commodity Name")
				// ->column("check_sample")->justify("center")->label("Code no. of Check Samples")
				// ->column("check_apex")->justify("center")->label("Code no. of Check apex")
				// ->column("challenge_sample")->justify("center")->label("Code no. of Challenge Samples")
				// ->column("ilc_sample")->justify("center")->label("Code no. of ILC Samples")
				->column("commodity_counts")->justify("center")->label("No. of Sample Analyzed")
				->column("org_sample_code")->justify("center")->label("Code no. of Sample Analyzed")
				->column("inter_lab_compare")->justify("center")->label("Inter Lab Comparasion")
				->column("pvt_sample")->justify("center")->label("Private Sample")
				->column("inter_check")->justify("center")->label("Internal Check Sample")
				->column("proj_sample")->justify("center")->label("Project Sample")
				->column("repeat_sample")->justify("center")->label("Repeat Sample")
				->column("pt_samp")->justify("center")->label("PT Samples")
				->column("counts")->hide()
				->column("sample_type_desc")->hide()

				//->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customTrailer("Total Number of Samples : {counts} ", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				// ->customTrailer("{$name} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 10px;margin-bottom:10px;")
				// ->customTrailer("({$email}) ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 25px;margin-bottom:10px;")
				// ->customTrailer("{$role} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 45px; margin-bottom:10px;")

				->expression("total1")->sum("commodity_counts", "lab_name")
				->expression("total2")->sum("inter_lab_compare", "lab_name")
				->expression("total3")->sum("pvt_sample", "lab_name")
				->expression("total4")->sum("inter_check", "lab_name")
				->expression("total5")->sum("proj_sample", "lab_name")
				->expression("total6")->sum("repeat_sample", "lab_name")
				->expression("total7")->sum("pt_samp", "lab_name")

				->group("lab_name")
				->header("lab_name")

				->trailer("total1")->below("commodity_counts")->label("Total : ")
				->trailer("total2")->below("inter_lab_compare")->label("Total : ")
				->trailer("total3")->below("pvt_sample")->label("Total : ")
				->trailer("total4")->below("inter_check")->label("Total : ")
				->trailer("total5")->below("proj_sample")->label("Total : ")
				->trailer("total6")->below("repeat_sample")->label("Total : ")
				->trailer("total7")->below("pt_samp")->label("Total : ")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				// ->footer("Time: date('Y-m-d H:i:s')", "font-size: 8pt; text-align: right; font-style: italic; width: 100%; margin-top: 60px; padding-bottom:60px;")
				->execute();
		}
	}


	public function bifercationOfSamplesAnalyzedByRal()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$month = $this->request->getData('month');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user_id = $_SESSION['user_code'];
			$year = $this->request->getData('year');
			$month_name = date("F", mktime(0, 0, 0, $month, 10));

			$report_name = "No. of Sample Analyzed in the month  of " . $month_name . ' , ' . $year;

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDolBifercationRal($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, name_chemist, check_count, pvt_count, sample_frm_cal, research_count, ilc_count, internal_check_count, total, working_days, other, norms,report_date, counts FROM temp_reportico_dol_bifercation_ral WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Head Office') {
				$query = ReportCustomComponent::getHoBifercationRal($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, name_chemist, check_count, pvt_count, sample_frm_cal, research_count, ilc_count, internal_check_count, total, working_days, other, norms,report_date, counts FROM temp_reportico_ho_bifercation_ral WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminBifercationRal($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, name_chemist, check_count, pvt_count, sample_frm_cal, research_count, ilc_count, internal_check_count, total, working_days, other, norms,report_date, counts FROM temp_reportico_admin_bifercation_ral WHERE user_id = '$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("name_chemist")->justify("center")->label("Name of Chemist")
				->column("check_count")->justify("center")->label("Check")
				->column("pvt_count")->justify("center")->label("Private Sample")
				->column("sample_frm_cal")->justify("center")->label("Sample from CAL")
				->column("research_count")->justify("center")->label("Research")
				->column("ilc_count")->justify("center")->label("Proficiency/ILC")
				->column("internal_check_count")->justify("center")->label("Internal Check")
				->column("total")->justify("center")->label("Total")
				->column("working_days")->justify("center")->label("No. of Working Days")
				->column("other")->justify("center")->label("Any other work attended")
				->column("norms")->justify("center")->label("Whether sample analyzed as per norms")
				->column("counts")->hide()

				//->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customTrailer("Total Number of Samples : {counts} ", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				// ->customTrailer("{$name} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 10px;margin-bottom:10px;")
				// ->customTrailer("({$email}) ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 25px;margin-bottom:10px;")
				// ->customTrailer("{$role} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 45px; margin-bottom:10px;")

				->group("lab_name")
				->header("lab_name")

				// ->group("name_chemist")
				// ->header("name_chemist")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				// ->footer("Time: date('Y-m-d H:i:s')", "font-size: 8pt; text-align: right; font-style: italic; width: 100%; margin-top: 60px; padding-bottom:60px;")
				->execute();
		}
	}

	public function monthlyStatusOfAnalyzedOfCheckSamplesAndPendingSamplesOfRalAnnexureE()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$month = $this->request->getData('month');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user_id = $_SESSION['user_code'];
			$year = $this->request->getData('year');
			$month_name = date("F", mktime(0, 0, 0, $month, 10));

			$report_name = "Monthly status of analyzed of Check samples and pending samples of RAL for month of " . $month_name . ' , ' . $year;

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDolMonthChekPendRal($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, name_chemist, bf_count, allotment_count, commodity_name, check_analyze_commodity, no_of_parameter,  total,  pending_count, remark,  reason, report_date, counts FROM temp_reportico_dol_month_chk_pend_ral WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Head Office') {
				$query = ReportCustomComponent::getHoMonthChekPendRal($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, name_chemist, bf_count, allotment_count, commodity_name, check_analyze_commodity, no_of_parameter,  total,  pending_count, remark,  reason, report_date, counts FROM temp_reportico_ho_month_chk_pend_ral WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminMonthChekPendRal($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, name_chemist, bf_count, allotment_count, commodity_name, check_analyze_commodity, no_of_parameter,  total,  pending_count, remark,  reason, report_date, counts FROM temp_reportico_admin_month_chk_pend_ral WHERE user_id = '$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("name_chemist")->justify("center")->label("Name of Chemist")
				->column("bf_count")->justify("center")->label("No. of check sample BF")
				->column("allotment_count")->justify("center")->label("No. of check samples alloted")
				->column("commodity_name")->justify("center")->label("Name of Commodity")
				->column("check_analyze_commodity")->justify("center")->label("No. of check sample analyzed commodity_wise")
				->column("no_of_parameter")->justify("center")->label("No. of parameter analyzed")
				->column("total")->justify("center")->label("Total")
				->column("pending_count")->justify("center")->label("No. of samples pendinf(CF)")
				->column("remark")->justify("center")->label("Remark")
				->column("reason")->justify("center")->label("Reason of CF")
				->column("counts")->hide()

				// ->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customTrailer("Total Number of Samples : {counts} ", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				// ->customTrailer("{$name} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 10px;margin-bottom:10px;")
				// ->customTrailer("({$email}) ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 25px;margin-bottom:10px;")
				// ->customTrailer("{$role} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 45px; margin-bottom:10px;")

				->expression("total1")->sum("bf_count", "lab_name")
				->expression("total2")->sum("allotment_count", "lab_name")
				->expression("total3")->sum("check_analyze_commodity", "lab_name")
				->expression("total4")->sum("pending_count", "lab_name")

				->group("lab_name")
				->header("lab_name")

				->trailer("total1")->below("bf_count")->label("Total : ")
				->trailer("total2")->below("allotment_count")->label("Total : ")
				->trailer("total3")->below("check_analyze_commodity")->label("Total : ")
				->trailer("total4")->below("pending_count")->label("Total : ")

				->group("name_chemist")
				->header("name_chemist")

				->group("commodity_name")
				->header("commodity_name")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				// ->footer("Time: date('Y-m-d H:i:s')", "font-size: 8pt; text-align: right; font-style: italic; width: 100%; margin-top: 60px; padding-bottom:60px;")

				->execute();
		}
	}

	public function commodityWiseDetailsOfSamplesAnalyzedByRalAnnexureE()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$month = $this->request->getData('month');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user_id = $_SESSION['user_code'];
			$years = $this->request->getData('year');
			$month_name = date("F", mktime(0, 0, 0, $month, 10));

			if ($month == 02) {
				$month_one = 12;
				$month_two = $month - 1;
			} else if ($month == 01) {
				$month_one = 11;
				$month_two = 12;
			} else {
				$month_one = $month - 2;
				$month_two = $month - 1;
			}

			$month_name_one = date("F", mktime(0, 0, 0, $month_one, 10));
			$month_name_two = date("F", mktime(0, 0, 0, $month_two, 10));

			$report_name = "Commodity wise details of samples analyzed by RAL Annexure E for the Month of " . $month_name . ' , ' . $years;

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDolCommodityWiseSampleRalAnnxeure($month, $years, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, commodity_name, bf_count, received_count, total, sample_analyze, conformed_std, misgrade, cf_month, report_date, counts FROM temp_reportico_dol_commo_wise_sample_ral_annexure WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Head Office') {
				$query = ReportCustomComponent::getHoCommodityWiseSampleRalAnnxeure($month, $years, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, commodity_name, bf_count, received_count, total, sample_analyze, conformed_std, misgrade, cf_month, report_date, counts FROM temp_reportico_ho_commo_wise_sample_ral_annexure WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminCommodityWiseSampleRalAnnxeure($month, $years, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, commodity_name, bf_count, received_count, total, sample_analyze, conformed_std, misgrade, cf_month, report_date, counts FROM temp_reportico_admin_commo_wise_sample_ral_annexure WHERE user_id = '$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("commodity_name")->justify("center")->label("Name of Commodity")
				->column("bf_count")->justify("center")->label("BF from month $month_name_one")
				->column("received_count")->justify("center")->label("Sample received for month $month_name_two")
				->column("total")->justify("center")->label("Total Sample Received")
				->column("sample_analyze")->justify("center")->label("Sample Analyzed")
				->column("conformed_std")->justify("center")->label("Conformed to Standard")
				->column("misgrade")->justify("center")->label("Misgraded")
				->column("cf_month")->justify("center")->label("CF for the month $month_name")
				->column("counts")->hide()

				//->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customTrailer("Total Number of Samples : {counts} ", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				// ->customTrailer("{$name} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 10px;margin-bottom:10px;")
				// ->customTrailer("({$email}) ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 25px;margin-bottom:10px;")
				// ->customTrailer("{$role} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 45px; margin-bottom:10px;")

				->expression("total1")->sum("bf_count", "lab_name")
				->expression("total2")->sum("received_count", "lab_name")
				->expression("total3")->sum("total", "lab_name")
				->expression("total4")->sum("sample_analyze", "lab_name")
				->expression("total5")->sum("cf_month", "lab_name")
				->group("lab_name")
				->header("lab_name")
				->trailer("total1")->below("bf_count")->label("Total : ")
				->trailer("total2")->below("received_count")->label("Total : ")
				->trailer("total3")->below("total")->label("Total : ")
				->trailer("total4")->below("sample_analyze")->label("Total : ")
				->trailer("total5")->below("cf_month")->label("Total : ")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				// ->footer("Time: date('Y-m-d H:i:s')", "font-size: 8pt; text-align: right; font-style: italic; width: 100%; margin-top: 60px; padding-bottom:60px;")
				->execute();
		}
	}

	public function statementOfCheckSamplesBroughtForwardCarryForwardAnnexureI()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$month = $this->request->getData('month');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user_id = $_SESSION['user_code'];
			$year = $this->request->getData('year');

			$month_name = date("F", mktime(0, 0, 0, $month, 10));

			$report_name = "Statement of Check Samples Brought forward/ Carry forward for the Month of " . $month_name . ' , ' . $year;

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDolStatementChkBfCfSample($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, role, sancationed_strength, staff_strength, bf_count, received_count, total, analyzed_count_original, analyzed_count_duplicate, analyzed_count_repeat, carry_forward, remark, report_date, counts FROM temp_reportico_dol_smt_chk_bf_cf_sample WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Head Office') {
				$query = ReportCustomComponent::getHoStatementChkBfCfSample($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, role, sancationed_strength, staff_strength, bf_count, received_count, total, analyzed_count_original, analyzed_count_duplicate, analyzed_count_repeat, carry_forward, remark, report_date, counts FROM temp_reportico_ho_smt_chk_bf_cf_sample WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminStatementChkBfCfSample($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, role, sancationed_strength, staff_strength, bf_count, received_count, total, analyzed_count_original, analyzed_count_duplicate, analyzed_count_repeat, carry_forward, remark, report_date, counts FROM temp_reportico_admin_smt_chk_bf_cf_sample WHERE user_id = '$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("role")->justify("center")->label("Name of Post")
				->column("sancationed_strength")->justify("center")->label("Sanctioned Strength")
				->column("staff_strength")->justify("center")->label("Staff Strength")
				->column("bf_count")->justify("center")->label("BF")
				->column("received_count")->justify("center")->label("Received during month")
				->column("total")->justify("center")->label("Total")
				->column("analyzed_count_original")->justify("center")->label("Analyzed during month original")
				// ->column("analyzed_count_duplicate")->justify("center")->label("Duplicate")
				// ->column("analyzed_count_repeat")->justify("center")->label("Repeat (Retest)")
				/* remove this two column 29-08-2022 by shreeya*/
				->column("carry_forward")->justify("center")->label("Carried Forwards")
				->column("remark")->justify("center")->label("Remark")
				->column("lab_name")->justify("center")->label("Name of RAL")
				->column("counts")->hide()

				//->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customTrailer("Total Number of Samples : {counts} ", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				// ->customTrailer("{$name} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 10px;margin-bottom:10px;")
				// ->customTrailer("({$email}) ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 25px;margin-bottom:10px;")
				// ->customTrailer("{$role} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 45px; margin-bottom:10px;")

				->group("lab_name")
				->header("lab_name")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				// ->footer("Time: date('Y-m-d H:i:s')", "font-size: 8pt; text-align: right; font-style: italic; width: 100%; margin-top: 60px; padding-bottom:60px;")
				->execute();
		}
	}

	public function timeTakenReport()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$month = $this->request->getData('month');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user_id = $_SESSION['user_code'];
			$year = $this->request->getData('year');

			$month_name = date("F", mktime(0, 0, 0, $month, 10));

			$report_name = "Time Taken Report for the Month of " . $month_name . ' , ' . $year;

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDolTimeTakenReport($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, commodity_name, sample_count, received_date, dispatch_date, time_taken, reason, remark,  report_date, counts FROM temp_reportico_dol_time_taken_report WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Head Office') {
				$query = ReportCustomComponent::getHoTimeTakenReport($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, commodity_name, sample_count, received_date, dispatch_date, time_taken, reason, remark,  report_date, counts FROM temp_reportico_ho_time_taken_report WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminTimeTakenReport($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, lab_name, commodity_name, sample_count, received_date, dispatch_date, time_taken, reason, remark,  report_date, counts FROM temp_reportico_admin_time_taken_report WHERE user_id = '$user_id'";
				}
			}

			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("sample_count")->justify("center")->label("Sample Code No.")/* replace heading no of sample -> to sample code no 29-08-2022 by shreeya*/
				->column("received_date")->justify("center")->label("Dt. of receipt of sample at RAL")
				->column("dispatch_date")->justify("center")->label("Dt. of Submission of results")
				->column("time_taken")->justify("center")->label("Time Taken for Ananlysis/Submission")
				->column("reason")->justify("center")->label("Reason for any delay")
				->column("remark")->justify("center")->label("Remark")
				->column("lab_name")->justify("center")->label("Name of RAL")
				->column("commodity_name")->justify("center")->label("Name of Commodity")
				->column("counts")->hide()

				//->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customTrailer("Total Number of Samples : {counts} ", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")
				// ->customTrailer("{$name} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 10px;margin-bottom:10px;")
				// ->customTrailer("({$email}) ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 25px;margin-bottom:10px;")
				// ->customTrailer("{$role} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 45px; margin-bottom:10px;")

				->group("lab_name")
				->header("lab_name")

				->group("commodity_name")
				->header("commodity_name")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				// ->footer("Time: date('Y-m-d H:i:s')", "font-size: 8pt; text-align: right; font-style: italic; width: 100%; margin-top: 60px; padding-bottom:60px;")
				->execute();
		}
	}

	public function sampleAllotmentSheetOfCodingSectionToTheICAnalyticalSectionOfCalNagpur()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$month = $this->request->getData('month');
			$year = $this->request->getData('year');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user_id = $_SESSION['user_code'];


			if ($lab == "HO") {
				$report1 =  $lab . ',' . $_SESSION['ro_office'];
			} else if (isset($_SESSION['ro_office'])) {
				$report1 =  $lab . ',' . $_SESSION['ro_office'];
			} else {
				$report1 =  $lab . ',' . $_SESSION['ro_office'];
			}

			$con = ConnectionManager::get('default');

			$query = $con->execute("SELECT phone FROM dmi_users WHERE id = '$user_id'");
			$records = $query->fetchAll('assoc');
			$query->closeCursor();
			foreach ($records as $record) {
				$phone = $record['phone'];
			}

			$report_name = "Sample allotment sheet of coding section to the I/C Analytical section ";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5 . '<br> Email :' . $email . ' <br>  Mobile : ' . $phone;


			$customHeader = "<p> प्रति,</p>
			<p >प्रभारी रसायन,मसाला,तेल,सूक्ष्मजीवशास्त्र/विषविद्या /खाद्यान्न अनुभाग |</p>
			<p>कृपया निन्म उल्लिखित कोडेड नमुने का विश्लेषण कर निर्धारित समय के भीतर अधोहस्ताक्षरी को रिपोर्ट भेज जाए |</p>
			<p>नमुने का ब्योरा:</p>";


			$customTrailer1 = "
			<p>	
			एसओ/आईईसी/ 17025-2005 के तहत प्रबंधकीय आवश्यकताओं के खंड 4.4 के संबंध में सौंपे गए 							
				कार्य करने की उचित व्यकवस्था है/ नहीं है।
			</p>							
			<p>
				1. सौंपे गए कार्य को करने के लिए आवश्य क कार्मिक, सूचना और उपयुक्त  संसाधनों, जिसमें रसायन, 							
				रीएजेन्ट्सं, ग्ला सवेयर, प्रमाणित संदर्भ सामग्री, संयंत्र, उपकरण, मान्य ता प्राप्तट पद्धतियों आदि का समावेश है, 							
				केन्द्रीय एगमार्क प्रयोगशाला, नागपुर में उपलब्ध् है। 	
			</p>						
			<p>
				2. कर्मियों में वह कौशल और निपुणता है जो प्रश्नुगत परीक्षण के प्रदर्शन के लिए आवश्यक है और वे माप 							
				की अनिश्चिंतता और सीमा का संसूचन आदि करने में भी सक्षम हैं। 
			</p>							
			<p>
				3. सौंपे गए कार्य के लिए जिम्मेदार कार्मिक द्वारा प्रयोग की जाने वाली विधि को पर्याप्त रूप से परिभाषित 							
				किया जाता है, उसका दस्ताकवेजीकरण किया जाता है एवं समझा जाता है।
			</p>							
			<p>4. जिन परीक्षण/पद्धतियों का चयन किया जाता है उनसे ग्राहकों की आवश्याकता पूर्ण होती है।</p> 							
			<p>5. जो अनुरोध/अनुबंध बनाया गया उसमें कोई मतभेद नहीं है और वह प्रयोगशाला और ग्राहक दोनों को मान्य  है। </p>";

			$customTrailer2 = "";
			$customTrailer3 = "<p class = 'text-right'>कोडींग अधिकारी</p>";

			$sql = "";

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDolSmplAllotCodingSection($month, $year, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT  sr_no, commodity_name, code_number, quantity, sample_type_desc, parameter, stage_sample_code, report_date FROM temp_reportico_dol_smpl_coding_section WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Head Office') {
				$query = ReportCustomComponent::getHoSmplAllotCodingSection($month, $year, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT  sr_no, commodity_name, code_number, quantity, sample_type_desc, parameter, stage_sample_code, report_date FROM temp_reportico_ho_smpl_coding_section WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminSmplAllotCodingSection($month, $year, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT  sr_no, commodity_name, code_number, quantity, sample_type_desc, parameter, stage_sample_code, report_date FROM temp_reportico_admin_smpl_coding_section WHERE user_id = '$user_id'";
				}
			}
			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("commodity_name")->justify("center")->label("Commodity")
				->column("code_number")->justify("center")->label("CODE Number")
				->column("quantity")->justify("center")->label("Quantity")
				->column("sample_type_desc")->justify("center")->label("Type of Sample")
				->column("parameter")->justify("center")->label("Parameters")
				->column("stage_sample_code")->justify("center")->label("LIMS Number")

				//->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customHeader($customHeader, "")

				->customTrailer($customTrailer1, "")
				->customTrailer($customTrailer2, "")
				->customTrailer($customTrailer3, "")

				// ->customTrailer("{$customTrailer2} ", "font-size: 10pt; text-align: left; font-weight: bold; width: 100%; margin-top: 10px;margin-bottom:10px;")
				// ->customTrailer("{$customTrailer1} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; padding-top:330px;margin-bottom:10px;")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				// ->footer("Time: date('Y-m-d H:i:s')", "font-size: 8pt; text-align: right; font-style: italic; width: 100%; margin-top: 60px; padding-bottom:60px;")
				->execute();
		}
	}

	public function sampleAllotmentSheetOfICAnalyticalSectionIssuedToTheChemistForAnalysis()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$month = $this->request->getData('month');
			$year = $this->request->getData('year');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user_id = $_SESSION['user_code'];

			$report_name = "Sample allotment sheet of I/C Analytical section issued to the Chemist for analysis";

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$con = ConnectionManager::get('default');

			$query = $con->execute("SELECT CONCAT(u.f_name,' ',u.l_name,', ', u.role) AS name_chemist, sa.recby_ch_date, sa.commencement_date,mst.sample_type_desc
			FROM sample_inward AS si
			INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
			INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
			INNER JOIN workflow AS w ON w.org_sample_code=si.org_sample_code
			INNER JOIN m_sample_type mst ON si.sample_type_code = mst.sample_type_code
			WHERE EXTRACT(MONTH
			FROM si.received_date):: INTEGER = '$month' AND EXTRACT(YEAR
			FROM si.received_date):: INTEGER = '$year' AND  w.dst_loc_id='$ral_lab_no' AND u.role IN ('Jr Chemist','Sr Chemist')");

			$records = $query->fetchAll('assoc');
			$query->closeCursor();
			$sample_type_desc = "";
			$name_chemist = "";
			$working_days = "";
			foreach ($records as $record) {
				$name_chemist = $record['name_chemist'];
				$recby_ch_date = $record['recby_ch_date'];
				$commencement_date = $record['commencement_date'];
				$sample_type_desc = $record['sample_type_desc'];

				if ($recby_ch_date == '' || $commencement_date == '') {
					$working_days = '0';
				} else {
					$diff = abs(strtotime($commencement_date) - strtotime($recby_ch_date));
					$years = floor($diff / (365 * 60 * 60 * 24));
					$months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
					$working_days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
				}
			}


			$customHeader1 = "<p>निम्नलिखित $sample_type_desc नमूने श्री/श्रीमती  $name_chemist  को पूर्ण/आंशिक विश्लेषण के लिए प्राथमिकता के आधार पर आवंटित किए जाते हैं। </p> <p>विश्लेषण $working_days कार्य दिवसों में पूरा किया जा सकता है। परिणाम प्रस्तुत करने में देरी, यदि कोई हो, कृपया उचित औचित्य के साथ समझाया जाए</p>";

			$customHeader2 = "<p>The following $sample_type_desc samples are alloted to Mr/Mrs. $name_chemist  for complete/partial analysis on priortiy basis.</p> <p>The analysis may be completed in $working_days working days. Delay in submission of results, if any, may kindly be explained with the proper justification</p>";

			$customHeader = $customHeader1 . '<br>' . $customHeader2 . '<br><br>';


			$customTrailer1 = "<p class = 'text-right'>केमिस्ट के हस्ताक्षर <br> Signature of Chemist</p>";

			$customTrailer2 = "<p class= 'text-center'>अनुभाग प्रभारी के हस्ताक्षर <br> Signature Incharge of Section </p>";

			$customTrailer3 = "<p class= 'text-left'>दिनांक <br> Date :  </p>";

			$sql = "";

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDolSmplAnalyticalSectionChemistAnalysis($month, $year, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT  sr_no, commodity_name, code_number, parameter, remark, report_date FROM temp_reportico_dol_smpl_analytical_section_chemist_analysis WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Head Office') {
				$query = ReportCustomComponent::getHoSmplAnalyticalSectionChemistAnalysis($month, $year, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT  sr_no, commodity_name, code_number, parameter, remark, report_date FROM temp_reportico_ho_smpl_analytical_section_chemist_analysis WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminSmplAnalyticalSectionChemistAnalysis($month, $year, $ral_lab_no);
				if ($query == 1) {
					$sql = "SELECT  sr_no, commodity_name, code_number, parameter, remark, report_date FROM temp_reportico_admin_smpl_analytical_section_chemist_analysis WHERE user_id = '$user_id'";
				}
			}
			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("commodity_name")->justify("center")->label("Commodity")
				->column("code_number")->justify("center")->label("CODE Number")
				->column("parameter")->justify("center")->label("Parameters")
				->column("remark")->justify("center")->label("Remark")

				//->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customHeader($customHeader, "")

				->customTrailer($customTrailer1, "")
				->customTrailer($customTrailer2, "")

				// ->customTrailer("{$customTrailer1} ", "font-size: 10pt; text-align: center; font-weight: bold; width: 100%; margin-top: 25px;margin-bottom:10px;")
				// ->customTrailer("{$customTrailer2} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 25px;margin-bottom:10px;")
				// ->customTrailer("{$customTrailer3} ", "font-size: 10pt; text-align: left; font-weight: bold; width: 100%; margin-top: 25px;margin-bottom:10px;")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				// ->footer("Time: date('Y-m-d H:i:s')", "font-size: 8pt; text-align: right; font-style: italic; width: 100%; margin-top: 60px; padding-bottom:60px;")
				->execute();
		}
	}

	public function perticularsOfSamplesReceivedAndAnalyzedByRalAnnexureD()
	{
		$this->autoRender = false;

		if ($this->request->is('post')) {
			$month = $this->request->getData('month');
			$year = $this->request->getData('year');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user_id = $_SESSION['user_code'];
			$month_name = date("F", mktime(0, 0, 0, $month, 10));

			$report_name = "Particulars of samples received and analyzed by RAL Annexure D for the month of " . $month_name . ' , ' . $year;;

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDolParticularSampleAnanlyzeReceiveRal($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT  sr_no, lab_name, commodity_name, bf_count, received_count, total, analyzed_count_original, analyzed_count_duplicate, received_count_year, analyzed_count_year, carry_forward, remark, sample_type_desc, report_date, counts FROM temp_reportico_dol_particular_analyze_receive WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Head Office') {
				$query = ReportCustomComponent::getHoParticularSampleAnanlyzeReceiveRal($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT  sr_no, lab_name, commodity_name, bf_count, received_count, total, analyzed_count_original, analyzed_count_duplicate, received_count_year, analyzed_count_year, carry_forward, remark, sample_type_desc, report_date, counts FROM temp_reportico_ho_particular_analyze_receive WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminParticularSampleAnanlyzeReceiveRal($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT  sr_no, lab_name, commodity_name, bf_count, received_count, total, analyzed_count_original, analyzed_count_duplicate, received_count_year, analyzed_count_year, carry_forward, remark, sample_type_desc, report_date, counts FROM temp_reportico_admin_particular_analyze_receive WHERE user_id = '$user_id'";
				}
			}
			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("commodity_name")->justify("center")->label("Commodity")
				->column("bf_count")->justify("center")->label("Brought Forward")
				->column("received_count")->justify("center")->label("Samples Received during the Month")
				->column("total")->justify("center")->label("Total")
				->column("analyzed_count_original")->justify("center")->label("Original sample analyzed during the month")
				->column("analyzed_count_duplicate")->justify("center")->label("Duplicate sample analyzed during the month")
				->column("received_count_year")->justify("center")->label("Progressive Total of Sample Received duirng the year")
				->column("analyzed_count_year")->justify("center")->label("Progressive Total of Sample Analyzed duirng the year")
				->column("carry_forward")->justify("center")->label("Sample Carried Forward")
				->column("remark")->justify("center")->label("Remarks")
				->column("sample_type_desc")->justify("center")->label("Sample Type")
				->column("counts")->hide()

				//->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customTrailer("Total Number of Samples : {counts} ", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				// ->customTrailer("{$name} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 10px;margin-bottom:10px;")
				// ->customTrailer("({$email}) ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 25px;margin-bottom:10px;")
				// ->customTrailer("{$role} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 45px; margin-bottom:10px;")

				->expression("total1")->sum("bf_count", "lab_name")
				->expression("total2")->sum("received_count", "lab_name")
				->expression("total3")->sum("total", "lab_name")
				->expression("total4")->sum("analyzed_count_original", "lab_name")
				->expression("total5")->sum("analyzed_count_duplicate", "lab_name")
				->expression("total6")->sum("received_count_year", "lab_name")
				->expression("total7")->sum("analyzed_count_year", "lab_name")
				->expression("total8")->sum("carry_forward", "lab_name")

				->group("lab_name")
				->header("lab_name")

				->trailer("total1")->below("bf_count")->label("Total : ")
				->trailer("total2")->below("received_count")->label("Total : ")
				->trailer("total3")->below("total")->label("Total : ")
				->trailer("total4")->below("analyzed_count_original")->label("Total : ")
				->trailer("total5")->below("analyzed_count_duplicate")->label("Total : ")
				->trailer("total6")->below("received_count_year")->label("Total : ")
				->trailer("total7")->below("analyzed_count_year")->label("Total : ")
				->trailer("total8")->below("carry_forward")->label("Total : ")

				->group("sample_type_desc")
				->header("sample_type_desc")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")

				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				// ->footer("Time: date('Y-m-d H:i:s')", "font-size: 8pt; text-align: right; font-style: italic; width: 100%; margin-top: 60px; padding-bottom:60px;")
				->execute();
		}
	}

	public function commonReport()
	{
		if ($this->request->is('post')) {
			$month = $this->request->getData('month');
			$year = $this->request->getData('year');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
			$ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
			$role = $this->request->getData('role');
			$user_id = $_SESSION['user_code'];
			$sample = $this->request->getData('sample_type');
			$sample = implode(',', $sample);
			$month_name = date("F", mktime(0, 0, 0, $month, 10));

			$this->viewBuilder()->setLayout('pdf_layout');

			$con = ConnectionManager::get('default');

			$sql = "SELECT sa.alloc_to_user_code, si.sample_type_code
			FROM sample_inward AS si
			INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
			INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
			WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
			FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
			FROM sa.alloc_date):: INTEGER = '$year' AND u.role IN ('Jr Chemist','Sr Chemist','Cheif Chemist')
			GROUP BY sa.alloc_to_user_code, si.sample_type_code";

			$sql = $con->execute($sql);
			$recordNames = $sql->fetchAll('assoc');
			$sql->closeCursor();

			foreach ($recordNames as $recordName) {
				$user_code = $recordName['alloc_to_user_code'];
				// $sample_type = $recordName['sample_type_code'];

				$query = $con->execute("SELECT sa.alloc_to_user_code,sa.recby_ch_date, sa.commencement_date,'NA' AS other,'Yes' AS norms, CONCAT(u.f_name,' ',u.l_name,' (',u.role,') ') AS name_chemist,
					(
                    SELECT COUNT(si.sample_type_code)
                    FROM sample_inward AS si
                    INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code IN ($sample)
                    INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                    WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                    ) AS check_count,
                    -- (
                    -- SELECT COUNT(si.sample_type_code)
                    -- FROM sample_inward AS si
                    -- INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 0
                    -- INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    -- INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                    -- WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    -- FROM sa.alloc_date):: INTEGER = '$month'  AND EXTRACT(YEAR
                    -- FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                    -- ) AS pvt_count,
                    -- (
                    -- SELECT COUNT(si.sample_type_code)
                    -- FROM sample_inward AS si
                    -- INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 2
                    -- INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    -- INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                    -- WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    -- FROM sa.alloc_date):: INTEGER = '$month'  AND EXTRACT(YEAR
                    -- FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                    -- ) AS research_count,
                    -- (
                    -- SELECT COUNT(si.sample_type_code)
                    -- FROM sample_inward AS si
                    -- INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 7
                    -- INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    -- INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                    -- WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    -- FROM sa.alloc_date):: INTEGER = '$month'  AND EXTRACT(YEAR
                    -- FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                    -- ) AS ilc_count,
                    -- (
                    -- SELECT COUNT(si.sample_type_code)
                    -- FROM sample_inward AS si
                    -- INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code AND si.sample_type_code = 0
                    -- INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
                    -- INNER JOIN dmi_users AS du ON sa.alloc_to_user_code = du.id
                    -- WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
                    -- FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
                    -- FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
                    -- ) AS internal_check_count,
					(
					SELECT count(w.org_sample_code) 
					FROM workflow w 
					WHERE w.dst_loc_id = $ral_lab_no AND EXTRACT(MONTH
					FROM w.tran_date):: INTEGER = '$month'  AND EXTRACT(YEAR
					FROM w.tran_date):: INTEGER = '$year' AND w.user_code = '$user_code'
					) AS sample_frm_cal,
					(
					SELECT CONCAT(r.user_flag,', ',o.ro_office) AS ral_lab
					FROM dmi_users AS u
					INNER JOIN dmi_ro_offices AS o ON u.posted_ro_office=o.id
					INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email AND r.user_flag='$ral_lab_name'
					WHERE u.status = 'active' AND o.id = '$ral_lab_no'
					GROUP BY ral_lab
					) AS lab_name

					FROM sample_inward AS si
					INNER JOIN m_sample_type AS mst ON si.sample_type_code = mst.sample_type_code
					INNER JOIN m_sample_allocate AS sa ON sa.org_sample_code=si.org_sample_code
					INNER JOIN m_commodity AS mc ON mc.commodity_code=si.commodity_code
					INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code
					INNER JOIN dmi_ro_offices AS ml ON ml.id=sa.lab_code
					INNER JOIN dmi_user_roles AS r ON r.user_email_id=u.email
					WHERE sa.lab_code='$ral_lab_no' AND EXTRACT(MONTH
					FROM sa.alloc_date):: INTEGER = '$month' AND EXTRACT(YEAR
					FROM sa.alloc_date):: INTEGER = '$year' AND sa.alloc_to_user_code = '$user_code'
					GROUP BY u.f_name,u.l_name, u.role, sa.alloc_to_user_code,sa.recby_ch_date, sa.commencement_date");
				// pr($query);
				$records = $query->fetchAll('assoc');
				$query->closeCursor();
				$this->set('records', $records);
				$this->set('month_name', $month_name);
				$this->set('year', $year);
				$this->callTcpdf($this->render(), 'I');
			}
		}
	}
	
	
	//added on separate report of sample type 26-08-2022 by shreeya 
	// sample type
	
	public function detailsOfSamplesAnalysedCarryForwardForSampleType(){
		
		$this->autoRender = false;

		if ($this->request->is('post')) {

			$month = $this->request->getData('month');
			$year = $this->request->getData('year');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
		    $ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
		
			$role = $this->request->getData('role');
		
			$user_id = $_SESSION['user_code'];
		
		
			$month_name = date("F", mktime(0, 0, 0, $month, 10));
			
			$report_name = "Details Of Samples Analysed Carry Forward For The Month Of" . $month_name . ' , ' . $year;

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDoldetailsOfSamplesAnalysedCarryForwardForSampleType($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no, months,commodity_name,working_days,name_chemist,project_sample,check_count,
					check_apex_count,challenged_count,ilc_count,research_count,retesting_count,other,other_work,norm,sample_type_desc,total_no,counts,report_date,lab_name,no_of_param FROM temp_dol_details_of_samples_analysed_carry_forward_for_sample WHERE user_id = '$user_id'";
				
				}
			}

			if ($role == 'Head Office') {
				$query = ReportCustomComponent::getHodetailsOfSamplesAnalysedCarryForwardForSampleType($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
				 $sql = "SELECT sr_no, months,commodity_name,working_days,name_chemist,project_sample,check_count,
				 check_apex_count,challenged_count,ilc_count,research_count,retesting_count,other,other_work,norm,sample_type_desc,total_no,counts,report_date,lab_name,no_of_param FROM temp_details_of_samples_analysed_carry_forward_for_sample_type WHERE user_id = '$user_id'";
				}
			}
			
			
			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdmindetailsOfSamplesAnalysedCarryForwardForSampleType($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					"SELECT sr_no, months,commodity_name,working_days,name_chemist,project_sample,check_count,
				 check_apex_count,challenged_count,ilc_count,research_count,retesting_count,other,other_work,norm,sample_type_desc,total_no,counts,report_date,lab_name,no_of_param FROM temp_admin_details_of_samples_analysed_carry_forward_for_sample WHERE user_id = '$user_id'";
				}
			}
			
			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				->column("lab_name")->justify("center")->label("Lab Name.")
				->column("name_chemist")->justify("center")->label("Name of Chemist")
				->column("sample_type_desc")->justify("center")->label("Sample Type")
				->column("commodity_name")->justify("center")->label("Name of Commodity")
				->column("project_sample")->justify("center")->label("Project Samples")
				->column("check_count")->justify("center")->label("Check")
				->column("check_apex_count")->justify("center")->label("Check(APEX)")
				->column("challenged_count")->justify("center")->label(" Challenged")
				->column("ilc_count")->justify("center")->label("ILC")
				->column("research_count")->justify("center")->label("Research")
				->column("retesting_count")->justify("center")->label("Retesting")
				->column("working_days")->justify("center")->label("No. of working days")
				->column("no_of_param")->justify("center")->label("No. of parameters")
				->column("other")->justify("center")->label("Other")
				->column("other_work")->justify("center")->label("Other Work")
				->column("norm")->justify("center")->label("Whether analyzed as per norm")
				->column("total_no")->justify("center")->label("Total Nos.")
				->column("counts")->hide()

 
				//->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customTrailer("Total Number of Samples : {counts} ", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				// ->customTrailer("{$name} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 10px;margin-bottom:10px;")
				// ->customTrailer("({$email}) ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 25px;margin-bottom:10px;")
				// ->customTrailer("{$role} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 45px; margin-bottom:10px;")


				->group("lab_name")
				->header("lab_name")

				->group("name_chemist")
				->header("name_chemist")

				->group("sample_type_desc")
				->header("sample_type_desc")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")


				// ->footer("Time: date('Y-m-d H:i:s')", "font-size: 8pt; text-align: right; font-style: italic; width: 100%; margin-top: 55px; padding-bottom:60px;")
				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}
	
	
	
	//added for consolidated report on 22-08-2022 by shreeya
	public function consolidatedReportAnalysedByChemist()
	{
		
		$this->autoRender = false;

		if ($this->request->is('post')) {

			$month = $this->request->getData('month');
			$year = $this->request->getData('year');
			$lab = $this->request->getData('lab');
			$ral_lab = $this->request->getData('ral_lab');
		    $ral_lab = explode('~', $ral_lab);
			$ral_lab_no = $ral_lab[0];
			$ral_lab_name = $ral_lab[1];
			$posted_ro_office = $this->request->getData('posted_ro_office');
			
			$fname = $this->request->getData('fname');
			$lname = $this->request->getData('lname');
			
			$name = $fname . ' ' . $lname;
			$email = base64_decode($this->request->getData('email'));
		
			$role = $this->request->getData('role');
		
			$user_id = $_SESSION['user_code'];
		
		
			$month_name = date("F", mktime(0, 0, 0, $month, 10));
			
			$report_name = "Consolidated Report Analyzed By Chemist For The Month Of " . $month_name . ' , ' . $year;

			$header1 = "भारत सरकार/Goverment of India";
			$header2 = "कृषि और किसान कल्याण मंत्रालय /Ministry of Agriculture & Farmers Welfare";
			$header3 = "कृषि, सहकारिता एवं किसान कल्याण विभाग / Department of Agriculture & Farmers Welfare";
			$header4 = "विपणन और निरीक्षण निदेशालय / Directorate of Marketing and Inspection";

			if ($lab == 'RAL') {
				$header5 = "प्रादेशिक एगमार्क प्रयोगशाला / Regional Agmark Laboratory , " . $_SESSION['ro_office'];
			} else if ($lab == 'CAL') {
				$header5 = "केंद्रीय एगमार्क प्रयोगशाला / Central Agmark Laboratory <br> उत्तर अम्बज़री मार्ग / North Ambazari Road Nagpur 440010";
			} else if ($lab == 'RO') {
				$header5 = "प्रादेशिक कार्यालय / Regional Office , " . $_SESSION['ro_office'];
			} else if ($lab == 'SO') {
				$header5 = "उप-कार्यालय / Sub Office , " . $_SESSION['ro_office'];
			}

			$headerone = $header1 . '<br>' . $header2 . '<br>' . $header3 . '<br>' . $header4;
			$header = $headerone . '<br>' . $header5;

			$sql = "";

			if ($role == 'DOL') {
				$query = ReportCustomComponent::getDolConsolidatedReporteAnalyzedByChemist($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no,lab_name, name_chemist, sample_type_desc, project_sample, check_count ,counts , check_apex_count, challenged_count, ilc_count, research_count, retesting_count,other_private_sample, smpl_analysed_instrn, report_date 
					FROM temp_dol_consolidated_reporte_analyzed_by_chemists WHERE user_id = '$user_id'";
				}
			}

			if ($role == 'Head Office') {
				$query = ReportCustomComponent::getHoConsolidatedReporteAnalyzedByChemist($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no,lab_name, name_chemist, sample_type_desc, project_sample, check_count, counts, check_apex_count, challenged_count, ilc_count, research_count, retesting_count,other_private_sample, smpl_analysed_instrn, report_date 
					FROM temp_consolidated_reporte_analyzed_by_chemists WHERE user_id = '$user_id'";
				
				}
				
				
			}
			
			if ($role == 'Admin') {
				$query = ReportCustomComponent::getAdminConsolidatedReporteAnalyzedByChemist($month, $year, $ral_lab_no, $ral_lab_name);
				if ($query == 1) {
					$sql = "SELECT sr_no,lab_name, name_chemist, sample_type_desc, project_sample, check_count, counts,check_apex_count, challenged_count, ilc_count, research_count, retesting_count,other_private_sample, smpl_analysed_instrn, report_date 
					FROM temp_admin_consolidated_reporte_analyzed_by_chemists WHERE user_id = '$user_id'";
				}
			}
			
			if ($sql == "") {
				return $this->redirect("/report/index");
			}

			ini_set("include_path", reporticoReport);
			require_once("vendor/autoload.php");
			require_once("vendor/reportico-web/reportico/src/Reportico.php");

			Builder::build()
				->properties(["bootstrap_preloaded" => true])
				->datasource()->database("pgsql:host=" . ForReportsConnection . "; dbname=" . ForReportsDB)->user(ForReportsUserName)->password(ForReportsPassword)
				->title($report_name)

				->sql($sql)

				->column("sr_no")->justify("center")->label("Sr. No.")
				
				->column("check_count")->justify("center")->label("Check Samples")
				->column("check_apex_count")->justify("center")->label("Check(APEX) Sample")
				->column("challenged_count")->justify("center")->label(" Challenged Sample")
				->column("ilc_count")->justify("center")->label("ILC")
				->column("research_count")->justify("center")->label("Research")
				->column("retesting_count")->justify("center")->label("Retesting")
				->column("smpl_analysed_instrn")->justify("center")->label("Analysed")
				->column("project_sample")->justify("center")->label("Project Samples")
				->column("other_private_sample")->justify("center")->label("Other")
				//  ->column("total_no")->justify("center")->label("Total Nos.")
				->column("report_date")->justify("center")->label("Report Date.")
				->column("counts")->hide()

				//->to('CSV') //Auto download excel file	

				->group("report_date")
				->header("report_date")
				->customTrailer("Total Number of Samples : {counts} ", "")
				->customTrailer("{$name} ", "")
				->customTrailer("({$email}) ", "")
				->customTrailer("{$role} ", "")

				// ->customTrailer("{$name} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 10px;margin-bottom:10px;")
				// ->customTrailer("({$email}) ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 25px;margin-bottom:10px;")
				// ->customTrailer("{$role} ", "font-size: 10pt; text-align: right; font-weight: bold; width: 100%; margin-top: 45px; margin-bottom:10px;")


				->group("lab_name")
				->header("lab_name")

				->group("name_chemist")
				->header("name_chemist")

				->group("sample_type_desc")
				->header("sample_type_desc")

				->page()
				->pagetitledisplay("TopOfFirstPage")

				->header($header, "")


				// ->footer("Time: date('Y-m-d H:i:s')", "font-size: 8pt; text-align: right; font-style: italic; width: 100%; margin-top: 55px; padding-bottom:60px;")
				->footer("Page: {PAGE} of {PAGETOTAL} & Time: date('Y-m-d H:i:s')", "")
				->execute();
		}
	}
	
	
	
}

?>
