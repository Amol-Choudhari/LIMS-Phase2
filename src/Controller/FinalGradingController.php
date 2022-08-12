<?php
namespace App\Controller;

use Cake\Event\Event;
use App\Network\Email\Email;
use Cake\ORM\Entity;
use Cake\Datasource\ConnectionManager;
use Cake\View;

class FinalGradingController extends AppController
{

	var $name 		= 'FinalGrading';

	public function beforeFilter($event) {
		parent::beforeFilter($event);

		$this->viewBuilder()->setLayout('admin_dashboard');
		$this->viewBuilder()->setHelpers(['Form','Html']);
		$this->loadComponent('Customfunctions');
	}

/********************************************************************************************************************************************************************************/

	//to validate login user
	public function authenticateUser() {

		$this->loadModel('DmiUserRoles');
		$user_access = $this->DmiUserRoles->find('all',array('conditions'=>array('user_email_id IS'=>$this->Session->read('username'))))->first();

		if (!empty($user_access)) {
			//proceed
		} else {
			echo "Sorry.. You don't have permission to view this page";
			exit();
		}
	}

/********************************************************************************************************************************************************************************/

	public function availableForGradingToInward() {

		$this->authenticateUser();

		$result = $this->getSampleToGradeByInward();
		$this->set('sample_codes',$result);
	}

/********************************************************************************************************************************************************************************/

/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--------<Get Sample To Grade By Inward>-------->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

	//created common function to fetch list , to be used for dashboard counts also, on 28-04-2021 by Amol
	public function getSampleToGradeByInward() {

		$conn = ConnectionManager::get('default');
		$user_code = $_SESSION['user_code'];
		$this->loadModel('Workflow');

        //Why : To show the finalized test sample to inward officer if sample forward by Lab incharge officer, Done by pravin bhakare 16-08-2019 */

		 $query = $conn->execute("SELECT ft.sample_code,ft.sample_code FROM Final_Test_Result AS ft
								  INNER JOIN workflow AS w ON ft.org_sample_code = w.org_sample_code
								  INNER JOIN m_sample_allocate sa ON ft.org_sample_code = sa.org_sample_code
								  INNER JOIN sample_inward AS si ON ft.org_sample_code = si.org_sample_code
								  WHERE ft.display ='Y' AND si.status_flag ='FR' AND w.stage_smpl_flag IN ('AR','FR') AND w.dst_usr_cd='$user_code'
								  GROUP BY ft.sample_code ");

		$final_result_details = $query->fetchAll('assoc');
		//print_r($final_result_details); exit;

		//Conditions to check wheather stage sample code is final graded or not.
		$final_result = array();

		if (!empty($final_result_details)) {

			foreach ($final_result_details as $stage_sample_code) {

				$final_grading = $this->Workflow->find('all',array('conditions'=>array('stage_smpl_flag'=>'FG','stage_smpl_cd IS'=>$stage_sample_code['sample_code'],'src_usr_cd'=>$user_code)))->first();

				if (empty($final_grading)) {

					$final_result[]= $stage_sample_code;
				}
			}
		}

		//to be used in below core query format, that's why
		$arr = "IN(";
		foreach ($final_result as $each) {
			$arr .= "'";
			$arr .= $each['sample_code'];
			$arr .= "',";
		}

		$arr .= "'00')";//00 is intensionally given to put last value in string.

		$query = $conn->execute("SELECT w.stage_smpl_cd,
										si.received_date,
										st.sample_type_desc,
										mcc.category_name,
										mc.commodity_name,
										ml.ro_office,
										w.modified AS submitted_on
										FROM sample_inward AS si
								 INNER JOIN m_sample_type AS st ON si.sample_type_code=st.sample_type_code
								 INNER JOIN m_commodity_category AS mcc ON si.category_code=mcc.category_code
								 INNER JOIN dmi_ro_offices AS ml ON ml.id=si.loc_id
								 INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
								 INNER JOIN workflow AS w ON w.org_sample_code = si.org_sample_code
								 WHERE w.stage_smpl_cd ".$arr." and w.stage_smpl_flag = 'AR' ORDER BY w.modified desc");

		$result = $query->fetchAll('assoc');

		return $result;

	}

/**************************************************************************************************************************************************************************/

	public function redirectToVerify($verify_sample_code){

		$this->Session->write('verify_sample_code',$verify_sample_code);
		$this->redirect(array('controller'=>'FinalGrading','action'=>'grading_by_inward'));
	}

/**************************************************************************************************************************************************************************/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--------<Grading By Inward>-------->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

	public function gradingByInward(){

		$this->authenticateUser();
		$this->viewBuilder()->setLayout('admin_dashboard');
		$str1		  = "";
		$this->loadModel('MCommodityCategory');
		$this->loadModel('DmiUsers');
		$this->loadModel('FinalTestResult');
		$this->loadModel('MGradeStandard');
		$this->loadModel('MTestMethod');
		$this->loadModel('SampleInward');
		$this->loadModel('Workflow');
		$this->loadModel('MSampleAllocate');
		$this->loadModel('MCommodity');
		$this->loadModel('MGradeDesc');
		$conn = ConnectionManager::get('default');

		$verify_sample_code = $this->Session->read('verify_sample_code');

		if (!empty($verify_sample_code)) {

			$this->set('samples_list',array($verify_sample_code=>$verify_sample_code));
			$this->set('stage_sample_code',$verify_sample_code);//for hidden field, to use common script

			$grades_strd=$this->MGradeStandard->find('list',array('keyField'=>'grd_standrd','valueField'=>'grade_strd_desc','order' => array('grade_strd_desc' => 'ASC')))->toArray();
			$this->set('grades_strd',$grades_strd);

			$grades=$this->MGradeDesc->find('list',array('keyField'=>'grade_code','valueField'=>'grade_desc','order' => array('grade_desc' => 'ASC'),'conditions' => array('display' => 'Y')))->toArray();
			$this->set('grades',$grades);

			if ($this->request->is('post')) {

				$postdata = $this->request->getData();
				//html encode the each post inputs
				foreach($postdata as $key => $value){
					$postdata[$key] = htmlentities($this->request->getData($key), ENT_QUOTES);
				}

				if ($this->request->getData('button')=='add') {

					// Add new filed to add subgrading value
					$subGradeChecked = $this->request->getData('subgrade');

					$sample_code=$this->request->getData('sample_code');
					$category_code=$this->request->getData('category_code');
					$commodity_code=$this->request->getData('commodity_code');
					$remark=$this->request->getData('remark');

					if (null !== ($this->request->getData('result_flg'))) {
						$result_flg	= $this->request->getData('result_flg');
					} else {
						$result_flg="";
					}

					$flagArr = array("P", "F", "M","R");

					$result_grade	=	'';
					$grade_code_vs=$this->request->getData('grade_code');

					$tran_date=$this->request->getData("tran_date");
					$ogrsample1= $this->Workflow->find('all', array('conditions'=> array('stage_smpl_cd IS' => $sample_code)))->first();
					$ogrsample=$ogrsample1['org_sample_code'];;

					$src_usr_cd = $conn->execute("SELECT src_usr_cd FROM workflow WHERE org_sample_code='$ogrsample' AND stage_smpl_flag='TA' ");
					$src_usr_cd = $src_usr_cd->fetchAll('assoc');
					$abc = $src_usr_cd[0]['src_usr_cd'];

					$test_n_r_no = $conn->execute("SELECT max(test_n_r_no) FROM m_sample_allocate WHERE sample_code='$sample_code' AND test_n_r='R' ");
					$test_n_r_no = $test_n_r_no->fetchAll('assoc');
					$abc1 = $test_n_r_no[0]['max']+1;

					if ($result_flg=='R') {

						$_SESSION["loc_id"] =$_SESSION["posted_ro_office"];
						$_SESSION["loc_user_id"] =$_SESSION["user_code"];

						$workflow_data = array("org_sample_code"=>$ogrsample,
												"src_loc_id"=>$_SESSION["posted_ro_office"],
												"src_usr_cd"=>$_SESSION["user_code"],
												"dst_loc_id"=>$_SESSION["posted_ro_office"],
												"dst_usr_cd"=>$abc,"stage_smpl_flag"=>"R",
												"tran_date"=>$tran_date,
												"user_code"=>$_SESSION["user_code"],
												"stage_smpl_cd"=>$sample_code,  "stage"=>"8");

						$workflowEntity =  $this->Workflow->newEntity($workflow_data);

						$this->Workflow->save($workflowEntity);


						$dst_usr_cd = $conn->execute("SELECT dst_usr_cd  FROM workflow WHERE org_sample_code='$ogrsample' AND stage_smpl_flag='R' ");
						$dst_usr_cd = $dst_usr_cd->fetchAll('assoc');

						$abcd = $dst_usr_cd[0]['dst_usr_cd'];

						$user_name = $conn->execute("SELECT DISTINCT role FROM dmi_users AS u
													 INNER JOIN workflow AS w ON u.id = w.dst_usr_cd
													 INNER JOIN user_role AS r ON u.role = r.role_name
													 WHERE dst_usr_cd ='$abcd'
													 AND org_sample_code='$ogrsample'
													 AND stage_smpl_flag='R'");

						$user_name = $user_name->fetchAll('assoc');

						$abc2 = $user_name[0]['role'];

						$_SESSION["loc_id"] =$_SESSION["posted_ro_office"];

						$_SESSION["loc_user_id"] =$_SESSION["user_code"];

						$date=date("Y/m/d");

						$sample_code=trim($this->request->getData('sample_code'));

						$query = $conn->execute("SELECT si.org_sample_code
												 FROM sample_inward AS si
												 INNER JOIN workflow AS w ON w.org_sample_code = si.org_sample_code
												 WHERE w.stage_smpl_cd = '$sample_code'");

						$ogrsample3 = $query->fetchAll('assoc');

						$ogrsample_code = $ogrsample3[0]['org_sample_code'];

						if ($result_flg =='F') {

							$result_flg='Fail';

						} elseif ($result_flg=='M') {

							$result_flg='Misgrade';

						} else {

							$result_flg='SR';
						}

						// Add two new fileds to add subgrading value and inward grading date ,

						$conn->execute("UPDATE sample_inward SET remark ='$remark', status_flag ='R', grade ='$grade_code_vs', grading_date ='$date', inward_grading_date = '$date', sub_grad_check_iwo = '$subGradeChecked', inward_grade = '$grade_code_vs'
										WHERE category_code = '$category_code' AND commodity_code = '$commodity_code' AND org_sample_code = '$ogrsample_code' AND display = 'Y' ");

						//call to the common SMS/Email sending method
						$this->loadModel('DmiSmsEmailTemplates');
						//$this->DmiSmsEmailTemplates->sendMessage(2016,$sample_code);

						echo '#The sample is marked for retest and re-sent to '.$abc2.'#';

						exit;

					} else {

						$dst_loc =$_SESSION["posted_ro_office"];

						if ($_SESSION['user_flag']=='RAL') {

							$data = $this->DmiUsers->find('all', array('conditions'=> array('role' =>'RAL/CAL OIC','posted_ro_office' => $dst_loc,'status !='=>'disactive')))->first();
							$dst_usr = $data['id'];

						} else {

							/* Change the conditions for to find destination user id, after test result approved by lab inward officer the application send to RAL/CAL OIC officer */
							$data = $this->DmiUsers->find('all', array('conditions'=> array('role' =>'RAL/CAL OIC','posted_ro_office' => $dst_loc,'status !='=>'disactive')))->first();
							$dst_usr = $data['id'];
						}


						if ($_SESSION['user_flag']=='RAL') {

							if (trim($result_flg)=='F') {

								$workflow_data = array("org_sample_code"=>$ogrsample,
														"src_loc_id"=>$_SESSION["posted_ro_office"],
														"src_usr_cd"=>$_SESSION["user_code"],
														"dst_loc_id"=>$_SESSION["posted_ro_office"],
														"dst_usr_cd"=>$dst_usr,
														"stage_smpl_flag"=>"FS",
														"tran_date"=>$tran_date,
														"user_code"=>$_SESSION["user_code"],
														"stage_smpl_cd"=>$sample_code,
														"stage"=>"8");
							} else {

								// Change the stage_smpl_flag value FG to FGIO to genreate the sample report after grading by OIC,
								$workflow_data = array("org_sample_code"=>$ogrsample,
													"src_loc_id"=>$_SESSION["posted_ro_office"],
													"src_usr_cd"=>$_SESSION["user_code"],
													"dst_loc_id"=>$_SESSION["posted_ro_office"],
													"dst_usr_cd"=>$dst_usr,
													"stage_smpl_flag"=>"FGIO",
													"tran_date"=>$tran_date,
													"user_code"=>$_SESSION["user_code"],
													"stage_smpl_cd"=>$sample_code,
													"stage"=>"8");
							}

						} elseif ($_SESSION['user_flag']=='CAL') {

							if (trim($result_flg)=='F') {

								$workflow_data =  array("org_sample_code"=>$ogrsample,
														"src_loc_id"=>$_SESSION["posted_ro_office"],
														"src_usr_cd"=>$_SESSION["user_code"],
														"dst_loc_id"=>$_SESSION["posted_ro_office"],
														"dst_usr_cd"=>$dst_usr,
														"stage_smpl_flag"=>"FC",
														"tran_date"=>$tran_date,
														"user_code"=>$_SESSION["user_code"],
														"stage_smpl_cd"=>$sample_code,
														"stage"=>"7");

							} else {

								$workflow_data =  array("org_sample_code"=>$ogrsample,
														"src_loc_id"=>$_SESSION["posted_ro_office"],
														"src_usr_cd"=>$_SESSION["user_code"],
														"dst_loc_id"=>$_SESSION["posted_ro_office"],
														"dst_usr_cd"=>$dst_usr,
														"stage_smpl_flag"=>"VS",
														"tran_date"=>$tran_date,
														"user_code"=>$_SESSION["user_code"],
														"stage_smpl_cd"=>$sample_code,
														"stage"=>"7");
							}
						}

						$workflowEntity = $this->Workflow->newEntity($workflow_data);

						$this->Workflow->save($workflowEntity);

						$_SESSION["loc_id"] = $_SESSION["posted_ro_office"];

						$_SESSION["loc_user_id"] = $_SESSION["user_code"];

						$date = date("Y/m/d");

						$sample_code = trim($this->request->getData('sample_code'));

						$query = $conn->execute("SELECT si.org_sample_code
												 FROM sample_inward AS si
												 INNER JOIN workflow AS w ON w.org_sample_code = si.org_sample_code
												 WHERE w.stage_smpl_cd = '$sample_code'");

						$ogrsample3 = $query->fetchAll('assoc');

						$ogrsample_code = $ogrsample3[0]['org_sample_code'];

						if ($_SESSION['user_flag']=='RAL') {

							if (trim($result_flg)=='F') {

								// Add two new fileds to add subgrading value and inward grading date
								$conn->execute("UPDATE sample_inward SET 
														status_flag='FS',
														remark ='$remark',
														grade='$grade_code_vs',
														grading_date='$date',
														inward_grading_date='$date',
														sub_grad_check_iwo='$subGradeChecked',
														inward_grade='$grade_code_vs',
														grade_user_cd=".$_SESSION['user_code'].",
														grade_user_flag='".$_SESSION['user_flag']."',
														grade_user_loc_id='".$_SESSION['posted_ro_office']."',
														ral_anltc_rslt_rcpt_dt='$tran_date'
														WHERE category_code= '$category_code'
														AND commodity_code = '$commodity_code'
														AND org_sample_code = '$ogrsample_code'
														AND display = 'Y' ");

							} else {

								// Add two new fileds to add subgrading value and inward grading date
								$conn->execute("UPDATE sample_inward SET 
														status_flag='FG',
														remark ='$remark',
														grade='$grade_code_vs',
														grading_date='$date',
														inward_grading_date='$date',
														sub_grad_check_iwo='$subGradeChecked',
														inward_grade='$grade_code_vs',
														grade_user_cd=".$_SESSION['user_code'].",
														grade_user_flag='".$_SESSION['user_flag']."',
														grade_user_loc_id='".$_SESSION['posted_ro_office']."',
														ral_anltc_rslt_rcpt_dt='$tran_date'
														WHERE category_code= '$category_code'
														AND commodity_code = '$commodity_code'
														AND org_sample_code = '$ogrsample_code'
														AND display = 'Y' ");
							}

						} elseif ($_SESSION['user_flag']=='CAL') {

							if ($result_flg=='F') {

								// Add two new fileds to add subgrading value and inward grading date
								$conn->execute("UPDATE sample_inward SET 
														status_flag='FC',
														remark ='$remark',
														grade='$grade_code_vs',
														grading_date='$date',
														inward_grading_date='$date',
														sub_grad_check_iwo='$subGradeChecked',
														inward_grade='$grade_code_vs',
														grade_user_cd=".$_SESSION['user_code'].",
														grade_user_flag='".$_SESSION['user_flag']."',
														grade_user_loc_id='".$_SESSION['posted_ro_office']."',
														ral_anltc_rslt_rcpt_dt='$tran_date'
														WHERE category_code= '$category_code'
														AND commodity_code = '$commodity_code'
														AND org_sample_code = '$ogrsample_code'
														AND display = 'Y' ");

							} else {

								// Add two new fileds to add subgrading value and inward grading date
								$conn->execute("UPDATE sample_inward SET 
														status_flag='VS',
														remark ='$remark',
														grade='$grade_code_vs',
														grading_date='$date',
														inward_grading_date='$date',
														sub_grad_check_iwo='$subGradeChecked',
														inward_grade='$grade_code_vs',
														grade_user_cd='".$_SESSION['user_code']."',
														grade_user_flag='".$_SESSION['user_flag']."',
														grade_user_loc_id='".$_SESSION['posted_ro_office']."',
														cal_anltc_rslt_rcpt_dt='$tran_date'
														WHERE category_code= '$category_code'
														AND commodity_code = '$commodity_code'
														AND org_sample_code = '$ogrsample_code'
														AND display = 'Y' ");
							}
						}

						//call to the common SMS/Email sending method
						$this->loadModel('DmiSmsEmailTemplates');
						//$this->DmiSmsEmailTemplates->sendMessage(2017,$sample_code);

						/* Change forward to RAL officer flash message,*/

						if ($_SESSION['user_flag']=='RAL') {

							echo '#The results have been finalized and forwarded to RAL,Office Incharge#';
							exit;

						} elseif ($_SESSION['user_flag']=='CAL') {

							echo '#The results have been finalized and forwarded to CAL,Office Incharge#';
							/* To disaply the message, after save the grading by inward officer.*/
							exit;

						} else {
							echo '#Record Save Sucessfully!#';
							exit;
						}
					}
				}
			}
		}
	
	}


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>--------<Get Final Result>-------->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

	// Inward Officer verify sample final grading please do not change, change let me know mandar
	public function getfinalResult(){

		$this->loadModel('CommGrade');
		$this->loadModel('MCommodity');
		$this->loadModel('FinalTestResult');
		$conn = ConnectionManager::get('default');

		$sample_code	= trim($_POST['sample_code']);
		$grd_standrd	= trim($_POST['grd_standrd']);
		$category_code	= trim($_POST['category_code']);
		$commodity_code	= trim($_POST['commodity_code']);

		if(!isset($sample_code) || !is_numeric($sample_code)){
			echo "#[error]~Invalid Sample code#";
			exit;
		}
		if(!isset($category_code) || !is_numeric($category_code)){
			echo "#[error]~Invalid Category code#";
			exit;
		}
		if(!isset($commodity_code) || !is_numeric($commodity_code)){
			echo "#[error]~Invalid Commodity code#";
			exit;
		}
		if(!isset($grd_standrd) || !is_numeric($grd_standrd)){
			echo "#[error]~Invalid Grading Standard#";
			exit;
		}

		$location_code	= $_SESSION['posted_ro_office'];
		$user_code		= $_SESSION['user_code'];
		$qry			= "SELECT t.test_code, t.test_name ,a.final_result
						   FROM final_test_result AS a
						   INNER JOIN m_test AS t ON t.test_code=a.test_code
						   WHERE a.display='Y' ";

		if ($_POST['sample_code']) {
			$qry.=	"and a.sample_code='$sample_code' ";
		}

		$res	= $conn->execute($qry);
		$res = $res->fetchAll('assoc');

		$i		= 0;
		$flag	= false;

		foreach ($res as $res1){

			$test = trim($res1['test_code']);
			$result = trim($res1['final_result']);

			$qry ="SELECT g.grade_desc ,t.grade_code,t.grade_value,t.max_grade_value,t.min_max,t.grade_order
					FROM comm_grade AS t
					INNER JOIN m_grade_desc AS g ON g.grade_code=t.grade_code
					WHERE t.test_code=$test AND  t.category_code=$category_code AND t.commodity_code=$commodity_code AND t.grd_standrd=$grd_standrd AND t.display='Y'
					ORDER BY t.grade_value";

			$grd_arr = $conn->execute($qry);
			$grd_arr = $grd_arr->fetchAll('assoc');

			if (!empty($grd_arr)) {

				foreach ($grd_arr as $grd_arr1){

					$grd_desc1			= '';
					$grade_value		= trim($grd_arr1['grade_value']);
					$max_grade_value	= trim($grd_arr1['max_grade_value']);
					$grade_desc			= trim($grd_arr1['grade_desc']);
					$min_max			= trim($grd_arr1['min_max']);
					$grade_order		= trim($grd_arr1['grade_order']);

					if (is_numeric($result)) {

						if ($min_max=='Max') {

							if ($grade_order==1) {

								if ($result<= $max_grade_value ) {

									$grd_desc1	= $grade_desc;
									break;

								} else {

									$grd_desc1	= 'Fail';
									break;
								}
							}
						}

						if ($min_max=='Min') {

							if($grade_order==1){

								if ( $result>= $grade_value ) {

										$grd_desc1	= $grade_desc;
										break;
								} else {
									$grd_desc1	= 'Fail';
									break;
								}

							}
						}

						if ($min_max=='Range' || $min_max=='') {

							if ($grade_order==1) {

								if ($result>=$grade_value && $result<=$max_grade_value) {

									$grd_desc1	= $grade_desc;
									break;
								} else {
									$grd_desc1	= 'Fail';
									break;
								}

							}
						}

					} else {

						if ($grade_order==1) {

							if (strcmp($grade_value,$result )==0) {

								$grd_desc1=$grade_desc;
								break;
							}else{
								$grd_desc1='Fail';
								break;
							}
						}
					}
				}

				if ($min_max=='Range') {

					$res[$i]['grd_val']	= $grade_value."-".$max_grade_value;

				} elseif ($min_max=='Min') {

					$res[$i]['grd_val']	= $grade_value." ".$min_max;

				} elseif ($min_max=='Max') {

					$res[$i]['grd_val']	= $max_grade_value." ".$min_max;

				} elseif ($min_max=='-1') {

					$res[$i]['grd_val']	= $grade_value;

				} else {

					$res[$i]['grd_val']	= "-";
					$res[$i]['grd_desc']	= $grd_desc1;
				}

				$res[$i]['grd_desc']	= $grd_desc1;
				$i++;

			} else {

				$flag=true;
			}
		}


		if ($flag==1) {
			echo "#~1#";
		} else {
			echo '#'.json_encode($res).'#';
		}

		exit;

	}

/***************************************************************************************************************************************************************************************/


	// get grade list commodity wise,
	public function getSampleCommodityGrads(){

		$this->autoRender = false;
		$this->loadModel('CommGrade');
		$this->loadModel('SampleInward');
		$this->loadModel('Workflow');

		$commodity_code = $_POST['commodity_code'];
		$sample_code = $_POST['sample_code'];
		$conn = ConnectionManager::get('default');

		//Fetch new field "sub_grad_check_iwo" data
		$query = $conn->execute("SELECT si.grade,si.sub_grad_check_iwo
								 FROM workflow AS w
								 INNER JOIN sample_inward AS si ON si.org_sample_code = w.org_sample_code
								 WHERE w.stage_smpl_cd='$sample_code'");

		$inward_grade = $query->fetchAll('assoc');

		
		$query = $conn->execute("SELECT gd.grade_desc,gd.grade_code
								 FROM comm_grade AS cg
								 INNER JOIN m_grade_desc AS gd ON gd.grade_code = cg.grade_code
								 WHERE cg.commodity_code='$commodity_code' AND cg.display='Y'");

		$commodity_code_grades = $query->fetchAll('assoc');

		$unique_result = array_unique($commodity_code_grades, SORT_REGULAR);

		$finalresult[0] = $inward_grade;
		$i=1;

		foreach($unique_result as $each){

			$finalresult[$i]['grade_code'] = $each['grade_code'];
			$finalresult[$i]['grade_desc'] = $each['grade_desc'];
			$i++;
		}

		// Add new option in grading drop down,
		$finalresult[$i] = Array ( 'grade_desc' => 'Fail' ,'grade_code' => 348 );

		echo '#'.json_encode($finalresult).'#';
		exit;
	}

/***************************************************************************************************************************************************************************************/

	public function availableForGradingToOic(){

		$this->authenticateUser();

		$result = $this->getSampleToGradeByOic();
		$this->set('sample_codes',$result);
	}

/***************************************************************************************************************************************************************************************/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>----------<Get Sample to Grade By OIC>---------->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

	//created common function to fetch list , to be used for dashboard counts also, on 28-04-2021 by Amol
	public function getSampleToGradeByOic(){

		$conn = ConnectionManager::get('default');
		$user_id = $_SESSION['user_code'];
		$this->loadModel('Workflow');

		if ($_SESSION['role']=='RAL/CAL OIC') {


			/* Add 'VS' flag options in stage_smpl_flag and status_flag conditions,
			Add 'FGIO' flag options in stage_smpl_flag and status_flag conditions //
			Why : To show the finalized test sample to inward officer if sample forward by Lab incharge officer, */

			$query = $conn->execute("SELECT ft.sample_code,ft.sample_code
										FROM Final_Test_Result AS ft
										INNER JOIN workflow AS w ON ft.org_sample_code=w.org_sample_code
										INNER JOIN m_sample_allocate sa ON ft.org_sample_code=sa.org_sample_code
										INNER JOIN sample_inward AS si ON ft.org_sample_code=si.org_sample_code
										WHERE ft.display='Y' AND w.dst_usr_cd='$user_id' AND w.stage_smpl_flag IN ('AR','FO','FC','FG','FS','VS','FGIO') AND  si.status_flag IN('VS','FG','FC','FO','FS')
										GROUP BY ft.sample_code ");

			$final_result_details = $query->fetchAll('assoc');

			//Conditions to check wheather stage sample code is final graded or not.
			$final_result = array();
			if(!empty($final_result_details)){

				foreach($final_result_details as $stage_sample_code){

					$final_grading = $this->Workflow->find('all',array('conditions'=>array('stage_smpl_flag'=>'FG','stage_smpl_cd'=>$stage_sample_code['sample_code'],'src_usr_cd'=>$user_id)))->first();

					if(empty($final_grading)){
						$final_result[]= $stage_sample_code;
					}
				}
			}

		} else {

			/* Add 'FR' flag options in stage_smpl_flag and status_flag conditions and destination user id conditions
			Why : To show the finalized test sample to OIC or Lab inward officer if sample forward by inward officer, */

			$query = $conn->execute("SELECT ft.sample_code,ft.sample_code
									 FROM Final_Test_Result AS ft
									 INNER JOIN workflow AS w ON ft.org_sample_code=w.org_sample_code
									 INNER JOIN m_sample_allocate sa ON ft.org_sample_code=sa.org_sample_code
									 INNER JOIN sample_inward AS si ON ft.org_sample_code=si.org_sample_code
									 WHERE ft.display='Y'
									 AND w.dst_usr_cd='$user_id'
									 AND w.stage_smpl_flag IN('AR','FO','FC','FR')
									 AND  si.status_flag IN('VS','FO','FC','FR')
									 GROUP BY ft.sample_code");

			$final_result_details = $query->fetchAll('assoc');

			/* Conditions to check wheather stage sample code is final graded or not.*/
			$final_result = array();
			if (!empty($final_result_details)) {

				foreach ($final_result_details as $stage_sample_code) {

					$final_grading_details = $this->Workflow->find('all',array('conditions'=>array('stage_smpl_cd'=>$stage_sample_code['sample_code']),'order'=>array('id desc')))->first();

					if (!empty($final_grading_details)) {

						$final_grading = $this->Workflow->find('all',array('conditions'=>array('dst_usr_cd'=>$user_id,'id'=>$final_grading_details['id'],'stage_smpl_flag !='=>'FG')))->first();

						if (!empty($final_grading)) {
							$final_result[]= $stage_sample_code;
						}
					}
				}
			}
		}

		//to be used in below core query format, that's why
		$arr = "IN(";
		foreach ($final_result as $each) {
			$arr .= "'";
			$arr .= $each['sample_code'];
			$arr .= "',";
		}
		$arr .= "'00')";//00 is intensionally given to put last value in string.

		//update the query to avoid duplicate entry in result, done by pravin bhakare 29-10-2021
		// NOTE : ADDED THE "VS" FLAG IN THIS QUERY TO GET THE VERFIED SAMPLE LIST AVALIBLE FOR GRADING AT THE OIC - 26-05-2022
		$query = $conn->execute("SELECT workflows.stage_smpl_cd,
		                si.received_date,
		                st.sample_type_desc,
		                mcc.category_name,
		                mc.commodity_name,
		                ml.ro_office,
		                workflows.modified AS submitted_on
		            FROM sample_inward AS si
		            INNER JOIN m_sample_type AS st ON si.sample_type_code=st.sample_type_code
		            INNER JOIN m_commodity_category AS mcc ON si.category_code=mcc.category_code
		            INNER JOIN dmi_ro_offices AS ml ON ml.id=si.loc_id
		            INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
		            INNER JOIN (select org_sample_code,stage_smpl_cd,modified from workflow where stage_smpl_flag IN('FGIO','FS','FC','VS') GROUP by org_sample_code,stage_smpl_cd,modified) as workflows
		                      on si.org_sample_code = workflows.org_sample_code
		            WHERE workflows.stage_smpl_cd ".$arr." ORDER BY workflows.modified desc "  );

		$result = $query->fetchAll('assoc');
		return $result;

	}

/*******************************************************************************************************************************************************************************************************/

	public function redirectToGrade($grading_sample_code){

		$this->Session->write('grading_sample_code',$grading_sample_code);
		$this->redirect(array('controller'=>'FinalGrading','action'=>'grading_by_oic'));
	}

/******************************************************************************************************************************************************************************************************/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>----------<GRADING BY OIC>---------->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

	public function gradingByOic(){

		$this->authenticateUser();
		$this->viewBuilder()->setLayout('admin_dashboard');
		$str1		  = "";
		$this->loadModel('MCommodityCategory');
		$this->loadModel('DmiUsers');
		$this->loadModel('FinalTestResult');
		$this->loadModel('MGradeStandard');
		$this->loadModel('MTestMethod');
		$this->loadModel('SampleInward');
		$this->loadModel('Workflow');
		$this->loadModel('MSampleAllocate');
		$this->loadModel('MCommodity');
		$this->loadModel('MGradeDesc');
		$conn = ConnectionManager::get('default');

		$grading_sample_code = $this->Session->read('grading_sample_code');

		if(!empty($grading_sample_code)){

			$this->set('samples_list',array($grading_sample_code=>$grading_sample_code));
			$this->set('stage_sample_code',$grading_sample_code);//for hidden field, to use common script

			$grades_strd=$this->MGradeStandard->find('list',array('keyField'=>'grd_standrd','valueField'=>'grade_strd_desc','order' => array('grade_strd_desc' => 'ASC')))->toArray();
			$this->set('grades_strd',$grades_strd);

			$grades=$this->MGradeDesc->find('list',array('keyField'=>'grade_code','valueField'=>'grade_desc','order' => array('grade_desc' => 'ASC'),'conditions' => array('display' => 'Y')))->toArray();
			$this->set('grades',$grades);


			//get org samle code
			$ogrsample1= $this->Workflow->find('all', array('conditions'=> array('stage_smpl_cd IS' => $grading_sample_code)))->first();
			$ogrsample = $ogrsample1['org_sample_code'];

			//to get commodity code for report pdf
			$getcommoditycd = $this->SampleInward->find('all',array('fields'=>'commodity_code','conditions'=>array('org_sample_code IS'=>$ogrsample),'order'=>'inward_id desc'))->first();
			$smple_commdity_code = $getcommoditycd['commodity_code'];
			$this->set('smple_commdity_code',$smple_commdity_code);

			if ($this->request->is('post')) {

				//html encode the each post inputs
				$postdata = $this->request->getData();

				foreach ($postdata as $key => $value) {

					$data[$key] = htmlentities($this->request->getData($key), ENT_QUOTES);
				}

				$sample_code = $this->request->getData('sample_code');

				if ($this->request->getData('button')=='add') {

					// Add new filed to add subgrading value
					$subGradeChecked = $this->request->getData('subgrade');

					$category_code=$this->request->getData('category_code');

					$commodity_code=$this->request->getData('commodity_code');

					$remark=$this->request->getData('remark');

					$remark_new=$this->request->getData('remark_new');


					if (null!==($this->request->getData('result_flg'))) {

						$result_flg	= $this->request->getData('result_flg');

					} else {

						$result_flg="";
					}

					$result_grade	=	'';
					$grade_code_vs=$this->request->getData('grade_code');

					$tran_date=$this->request->getData("tran_date");

					if ($result_flg=='R') {

						$src_usr_cd = $conn->execute("SELECT src_usr_cd  FROM workflow WHERE org_sample_code='$ogrsample' AND stage_smpl_flag='TA' ");
						$src_usr_cd = $src_usr_cd->fetchAll('assoc');
						$abc = $src_usr_cd[0]['src_usr_cd'];

						$_SESSION["loc_id"] = $_SESSION["posted_ro_office"];
						$_SESSION["loc_user_id"] = $_SESSION["user_code"];

						$workflow_data = array("org_sample_code"=>$ogrsample,
												"src_loc_id"=>$_SESSION["posted_ro_office"],
												"src_usr_cd"=>$_SESSION["user_code"],
												"dst_loc_id"=>$_SESSION["posted_ro_office"],
												"dst_usr_cd"=>$abc,
												"stage_smpl_flag"=>"R",
												"tran_date"=>$tran_date,
												"user_code"=>$_SESSION["user_code"],
												"stage_smpl_cd"=>$sample_code,
												"stage"=>"8");

						$workflowEntity = $this->Workflow->newEntity($workflow_data);
						$this->Workflow->save($workflowEntity);

						$dst_usr_cd = $conn->execute("SELECT dst_usr_cd  FROM workflow WHERE org_sample_code='$ogrsample' and stage_smpl_flag='R' ");

						$dst_usr_cd = $dst_usr_cd->fetchAll('assoc');
						$abcd = $dst_usr_cd[0]['dst_usr_cd'];

						$user_name = $conn->execute("SELECT DISTINCT role
													 FROM dmi_users AS u
													 INNER JOIN workflow AS w ON u.id=w.dst_usr_cd
											         INNER JOIN user_role AS r ON u.role=r.role_name
											         WHERE dst_usr_cd='$abcd' AND org_sample_code='$ogrsample' AND stage_smpl_flag='R' ");

						$user_name = $user_name->fetchAll('assoc');

						$abc2 = $user_name[0]['role'];

						$_SESSION["loc_id"] = $_SESSION["posted_ro_office"];
						$_SESSION["loc_user_id"] = $_SESSION["user_code"];
						$date=date("Y/m/d");
						$sample_code = trim($this->request->getData('sample_code'));

						$query = $conn->execute("SELECT si.org_sample_code
												 FROM sample_inward AS si
												 INNER JOIN workflow AS w ON w.org_sample_code = si.org_sample_code
												 WHERE w.stage_smpl_cd = '$sample_code'");

						$ogrsample3 = $query->fetchAll('assoc');

						$ogrsample_code = $ogrsample3[0]['org_sample_code'];

						if ($result_flg=='F') {

							$result_flg='Fail';
						} elseif ($result_flg=='M') {

							$result_flg='Misgrade';

						} else {
							$result_flg='Retest';
						}

						 // Add two new fileds to add subgrading value and oic grading date ,
						$conn->execute("UPDATE  sample_inward SET
											    remark ='$result_flg',
												remark_officeincharg ='$remark_new',
												status_flag='SR',grade='$grade_code_vs',
												grading_date='$date',
												oic_grading_date='$date',
												sub_grad_check_oic='$subGradeChecked'
										WHERE category_code= '$category_code'
										AND commodity_code = '$commodity_code'
										AND org_sample_code = '$ogrsample_code'
										AND display = 'Y' ");




						 //call to the common SMS/Email sending method
							$this->loadModel('DmiSmsEmailTemplates');
							//$this->DmiSmsEmailTemplates->sendMessage(2018,$sample_code);

						 echo '#0#';  // return 0 value to show conformation message
						 exit;

					} else {
						//code moved to below new function save grading
					}
				}
			}
		}

	}

/******************************************************************************************************************************************************************************************************/

	//to set post values in session to be used after redirecting from cdac
	public function setPostSessions(){

		$this->Session->write('post_remark',$_POST['remark']);//inward officer renark
		$this->Session->write('post_remark_new',$_POST['remark_new']);//Incharge remark
		$this->Session->write('post_grade_code_vs',$_POST['grade_code']);
		//get grade desc from table, added on 27-05-2022 by Amol
		//to show grade selected by OIC while final grading on report pdf
		$this->loadModel('MGradeDesc');
		$getGradedesc = $this->MGradeDesc->find('all',array('fields'=>'grade_desc','conditions'=>array('grade_code'=>$_POST['grade_code'])))->first();
		$this->Session->write('gradeDescFinalReport',$getGradedesc['grade_desc']);
		
		$this->Session->write('post_subGradeChecked',$_POST['subgrade']);
		$this->Session->write('post_category_code',$_POST['category_code']);
		$this->Session->write('post_commodity_code',$_POST['commodity_code']);

		echo '#1#';
		exit;
	}

/******************************************************************************************************************************************************************************************************/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>??>>>>>>>--------<Save Final Grading>-------->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

	//called after successful esigned by OIC
	public function saveFinalGrading(){

		// set variables to show popup messages from view file
		$message = '';
		$message_theme = '';
		$redirect_to = '';
		$this->loadModel('Workflow');
		$conn = ConnectionManager::get('default');

		$sample_code = $this->Session->read('grading_sample_code');

		//get org sample code
		$ogrsample1 = $this->Workflow->find('all', array('conditions'=> array('stage_smpl_cd IS' => $sample_code)))->first();
		$ogrsample = $ogrsample1['org_sample_code'];

		$src_usr_cd = $conn->execute("SELECT src_usr_cd,src_loc_id FROM workflow WHERE org_sample_code='$ogrsample' AND stage_smpl_flag IN ('OF','IF') ");

		$src_usr_cd = $src_usr_cd->fetchAll('assoc');
		$org_src_usr_cd = $src_usr_cd[0]['src_usr_cd'];
		$org_src_usr_id = $src_usr_cd[0]['src_loc_id'];

		$tran_date = date('Y-m-d');

		$workflow_data = array("org_sample_code"=>$ogrsample,
								"src_loc_id"=>$_SESSION["posted_ro_office"],
								"src_usr_cd"=>$_SESSION["user_code"],
								"dst_loc_id"=>$org_src_usr_id,
								"dst_usr_cd"=>$org_src_usr_cd,
								"stage_smpl_flag"=>"FG",
								"tran_date"=>$tran_date,
								"user_code"=>$_SESSION["user_code"],
								"stage_smpl_cd"=>$sample_code,
								"stage"=>"8");


		$workflowEntity = $this->Workflow->newEntity($workflow_data);

		$this->Workflow->save($workflowEntity);

		$_SESSION["loc_id"] = $_SESSION["posted_ro_office"];
		$_SESSION["loc_user_id"] = $_SESSION["user_code"];
		$date = date("Y/m/d");

		//get some post value from session after redirecting form cdac
		$remark = $this->Session->read('post_remark');//inward officer renark
		$remark_new = $this->Session->read('post_remark_new');//Incharge remark
		$grade_code_vs = $this->Session->read('post_grade_code_vs');
		$subGradeChecked = $this->Session->read('post_subGradeChecked');
		$category_code = $this->Session->read('post_category_code');
		$commodity_code = $this->Session->read('post_commodity_code');


		if ($_SESSION['user_flag']=='RAL' && $_SESSION['role']=='RAL/CAL OIC') {

			// Add two new fileds to add subgrading value and oic grading date ,
			$conn->execute("UPDATE sample_inward SET 
									status_flag='FG',
									remark ='$remark',
									grade='$grade_code_vs',
									remark_officeincharg ='$remark_new',
									remark_officeincharg_dt='$tran_date',
									grading_date='$date',
									oic_grading_date='$date',
									sub_grad_check_oic='$subGradeChecked',
									grade_user_cd=".$_SESSION['user_code'].",
									grade_user_flag='".$_SESSION['user_flag']."',
									grade_user_loc_id='".$_SESSION['posted_ro_office']."',
									ral_anltc_rslt_rcpt_dt='$tran_date'
								WHERE category_code= '$category_code'
								AND commodity_code = '$commodity_code'
								AND org_sample_code = '$ogrsample'
								AND display = 'Y' ");

		} elseif ($_SESSION['user_flag']=='CAL' && $_SESSION['role']=='DOL' || $_SESSION['role']=='RAL/CAL OIC') {

			// Add two new fileds to add subgrading value and oic grading date ,
			$conn->execute("UPDATE sample_inward SET 
									status_flag='G',
									remark ='$remark',
									grade='$grade_code_vs',
									remark_officeincharg ='$remark_new',
									remark_officeincharg_dt='$tran_date',
									grading_date='$date',
									oic_grading_date='$date',
									sub_grad_check_oic='$subGradeChecked',
									grade_user_cd='".$_SESSION['user_code']."',
									grade_user_flag='".$_SESSION['user_flag']."',
									grade_user_loc_id='".$_SESSION['posted_ro_office']."',
									cal_anltc_rslt_rcpt_dt='$tran_date'
							WHERE category_code= '$category_code'
							AND commodity_code = '$commodity_code'
							AND org_sample_code = '$ogrsample'
							AND display = 'Y' ");
		}

		//delete all used session to clear memory
		$this->Session->delete('grading_sample_code');
		$this->Session->delete('post_remark');
		$this->Session->delete('post_remark_new');
		$this->Session->delete('post_grade_code_vs');
		$this->Session->delete('post_subGradeChecked');
		$this->Session->delete('post_category_code');
		$this->Session->delete('post_commodity_code');

		//call to the common SMS/Email sending method
		$this->loadModel('DmiSmsEmailTemplates');
		//$this->DmiSmsEmailTemplates->sendMessage(2019,$sample_code);

		$message = 'Records has been Finalized and Sent to respective RO/SO/RAL!!';
		$message_theme = 'success';
		$redirect_to = 'available_for_grading_to_oic';
		//$this->view = '/Element/message_boxes';


		// set variables to show popup messages from view file
		$this->set('message',$message);
		$this->set('message_theme',$message_theme);
		$this->set('redirect_to',$redirect_to);
	
	}

/******************************************************************************************************************************************************************************************************/


	public function getRemark(){

		$this->loadModel('Users');
		$this->loadModel('DmiUsers');
		$conn = ConnectionManager::get('default');

		if($_POST['sample_code'])
		{
		 $sample_code=trim($_POST['sample_code']);
		}

		$user_data = $conn->execute("SELECT DISTINCT remark
									 FROM sample_inward AS si
									 INNER JOIN workflow AS w ON w.org_sample_code=si.org_sample_code
									 WHERE w.stage_smpl_cd='$sample_code' ");

		$user_data = $user_data->fetchAll('assoc');

		if (count($user_data)>0) {
			echo '#'.json_encode($user_data).'#';

		} else {

		   echo "#0#";
		}
		exit;
	}

/******************************************************************************************************************************************************************************************************/


	public function finalizedSamples(){

		$final_reports = $this->finalSampleTestReports();
		$this->set('final_sample_reports',$final_reports);
	}

/******************************************************************************************************************************************************************************************************/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>---------------<Final Sample Test Reports>--------------->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

	// create new menu for showing finalized test report result,
	public function finalSampleTestReports(){

		$this->viewBuilder()->setLayout('admin_dashboard');
		$this->loadModel('SampleInward');
		$this->loadModel('Workflow');
		$conn = ConnectionManager::get('default');

		$result = $this->Workflow->find('all',array('fields'=>array('modified','org_sample_code'),'conditions'=>array('src_usr_cd IS'=>$_SESSION['user_code']),'group'=>array('modified','org_sample_code'),'order'=>'modified desc'))->toArray();

		$final_reports = array();

		if (!empty($result)) {

			foreach ($result as $sample_code) {

				$org_smpl_cd = $sample_code['org_sample_code'];

				$query = $conn->execute("SELECT w.stage_smpl_cd, w.tran_date,mcc.category_name, mc.commodity_name, mst.sample_type_desc, mc.commodity_code, si.report_pdf
										 FROM workflow AS w
										 INNER JOIN sample_inward AS si ON si.org_sample_code = w.org_sample_code
										 INNER JOIN m_commodity_category AS mcc ON mcc.category_code = si.category_code
										 INNER JOIN m_commodity AS mc ON mc.commodity_code = si.commodity_code
										 INNER JOIN m_sample_type AS mst ON mst.sample_type_code = si.sample_type_code
										 WHERE w.stage_smpl_flag='FG' AND w.org_sample_code='$org_smpl_cd'");

				$final_grading = $query->fetchAll('assoc');


				if (!empty($final_grading)) {

					$final_reports[] = $final_grading[0];
				}
			}
		}

		$this->set('final_sample_reports',$final_reports);

		return $final_reports;
	}

/******************************************************************************************************************************************************************************************************/

	//to generate report pdf for preview and store on server
	public function sampleTestReportCode($sample_code,$sample_test_mc){

		$conn = ConnectionManager::get('default');

		$this->Session->write('sample_test_code',$sample_code);
		$this->Session->write('sample_test_mc',$sample_test_mc);
		
		// Added by AKASH on 10-08-2022
		$sd = $conn->execute("SELECT org_sample_code FROM workflow WHERE stage_smpl_cd = '$sample_code'")->fetch('assoc');
		$code2 = $sd['org_sample_code'];

		$grade = $conn->execute("SELECT gd.grade_desc
								 FROM sample_inward AS si
								 INNER JOIN m_grade_desc AS gd ON gd.grade_code = si.grade
								 WHERE si.org_sample_code = '$code2'")->fetchAll('assoc'); 

		$this->Session->write('gradeDescFinalReport',$grade[0]['grade_desc']);
		$this->redirect(array('controller'=>'FinalGrading','action'=>'sample_test_report'));
	}

/******************************************************************************************************************************************************************************************************/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>---------------<Sample Test Reports>--------------->>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

	public function sampleTestReport(){

		$this->viewBuilder()->setLayout('pdf_layout');

		$this->loadModel('SampleInward');
		$this->loadModel('FinalTestResult');
		$this->loadModel('ActualTestData');
		$this->loadModel('CommGrade');
		$this->loadModel('MSampleAllocate');
		$this->loadModel('Workflow');
		$this->loadModel('CommGrade');
		$conn = ConnectionManager::get('default');

		$commodity_code=$this->Session->read('sample_test_mc');
		$sample_code1=$this->Session->read('sample_test_code');

		$str1="SELECT org_sample_code FROM workflow WHERE display='Y' ";

		if ($sample_code1!='') {

			$str1.=" AND stage_smpl_cd='$sample_code1' GROUP BY org_sample_code"; /* remove trim fun on 01/05/2022 */
		}

		$sample_code2 = $conn->execute($str1);
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

		$query2 = "SELECT msr.m_sample_reg_obs_code, mso.m_sample_obs_code, mso.m_sample_obs_desc, mst.m_sample_obs_type_code,mst.m_sample_obs_type_value
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




		$test = $this->ActualTestData->find('all', array('fields' => array('test_code'=>'distinct(test_code)'),'conditions' =>array('org_sample_code IS' => $Sample_code, 'display' => 'Y')))->toArray();

		$test_string=array();
		$test_string_ext=array();

		$i=0;

		foreach ($test as $each) {

			$test_string[$i]=$each['test_code'];
			$i++;
		}
<<<<<<< HEAD

		//new queries and conditions added on 03-02-2022 by Amol
		//to print NABL logo and ULR no. on final test report
				
		$showNablLogo = ''; $urlNo='';		
		//get NABL commosity and test details if exist
		$this->loadModel('LimsLabNablCommTestDetails');
		$NablTests = $this->LimsLabNablCommTestDetails->find('all',array('fields'=>'tests','conditions'=>array('lab_id IS'=>$_SESSION['posted_ro_office'],'commodity IS'=>$commodity_code),'order'=>'id desc'))->first();		
	
		if(!empty($NablTests)){
			//get NABL certifcate details
			$this->loadModel('LimsLabNablDetails');
			$NablDetails = $this->LimsLabNablDetails->find('all',array('fields'=>array('accreditation_cert_no','valid_upto_date'), 'conditions'=>array('lab_id IS'=>$_SESSION['posted_ro_office']),'order'=>'id desc'))->first();
			//check validity
			$validUpto = strtotime($NablDetails['valid_upto_date']);
			$curDate = strtotime(date('d-m-Y'));
			
			if($validUpto > $curDate){
				
				$showNablLogo = 'yes';
				$certNo = $NablDetails['accreditation_cert_no'];
				$curYear = date('y');
				//Custom array for Lab no. 
				$labNoArr = array('55'=>'0','56'=>'1','45'=>'2','46'=>'3','47'=>'4','48'=>'5','49'=>'6','50'=>'7','51'=>'8','52'=>'9','53'=>'10','54'=>'11');
				$labNo = $labNoArr[$_SESSION['posted_ro_office']];
				
				//get total report for respective lab for current year
				$newDate = '01-01-'.date('Y');
				$getReportsCounts = $this->Workflow->find('all',array('fields'=>'id','conditions'=>array('src_loc_id'=>$_SESSION['posted_ro_office'],'stage_smpl_flag'=>'FG','date(tran_date) >=' =>$newDate,)))->toArray();
				$NoOfReport = '';
				for($i=0;$i<(8-(strlen(count($getReportsCounts))));$i++){
					$NoOfReport .= '0'; 
				}
				if(count($getReportsCounts)==0){
					$NoOfReport .= '1';
				}else{
					$NoOfReport .= count($getReportsCounts)+1;
				}
				
				
				$NablTests = explode(',',$NablTests['tests']);
				//compare tests arrays
				$result=array_diff($test_string,$NablTests);
				if(!empty($result)){$F_or_P = 'P';}else{$F_or_P = 'F';}

			//	$urlNo = 'ULR-'.$certNo.'/'.$curYear.'/'.$labNo.'/'.$NoOfReport.'/'.$F_or_P;
				$urlNo = 'ULR-'.$certNo.$curYear.$labNo.$NoOfReport.$F_or_P;

=======

		//new queries and conditions added on 03-02-2022 by Amol
		//to print NABL logo and ULR no. on final test report
				
		$showNablLogo = ''; $urlNo='';		
		//get NABL commosity and test details if exist
		$this->loadModel('LimsLabNablCommTestDetails');
		$NablTests = $this->LimsLabNablCommTestDetails->find('all',array('fields'=>'tests','conditions'=>array('lab_id IS'=>$_SESSION['posted_ro_office'],'commodity IS'=>$commodity_code),'order'=>'id desc'))->first();		
	
		if(!empty($NablTests)){
			//get NABL certifcate details
			$this->loadModel('LimsLabNablDetails');
			$NablDetails = $this->LimsLabNablDetails->find('all',array('fields'=>array('accreditation_cert_no','valid_upto_date'), 'conditions'=>array('lab_id IS'=>$_SESSION['posted_ro_office']),'order'=>'id desc'))->first();
			//check validity
			$validUpto = strtotime($NablDetails['valid_upto_date']);
			$curDate = strtotime(date('d-m-Y'));
			
			if($validUpto > $curDate){
				
				$showNablLogo = 'yes';
				$certNo = $NablDetails['accreditation_cert_no'];
				$curYear = date('y');
				//Custom array for Lab no. 
				$labNoArr = array('55'=>'0','56'=>'1','45'=>'2','46'=>'3','47'=>'4','48'=>'5','49'=>'6','50'=>'7','51'=>'8','52'=>'9','53'=>'10','54'=>'11');
				$labNo = $labNoArr[$_SESSION['posted_ro_office']];
				
				//get total report for respective lab for current year
				$newDate = '01-01-'.date('Y');
				$getReportsCounts = $this->Workflow->find('all',array('fields'=>'id','conditions'=>array('src_loc_id'=>$_SESSION['posted_ro_office'],'stage_smpl_flag'=>'FG','date(tran_date) >=' =>$newDate,)))->toArray();
				$NoOfReport = '';
				for($i=0;$i<(8-(strlen(count($getReportsCounts))));$i++){
					$NoOfReport .= '0'; 
				}
				if(count($getReportsCounts)==0){
					$NoOfReport .= '1';
				}else{
					$NoOfReport .= count($getReportsCounts)+1;
				}
				
				
				$NablTests = explode(',',$NablTests['tests']);
				//compare tests arrays
				$result=array_diff($test_string,$NablTests);
				if(!empty($result)){$F_or_P = 'P';}else{$F_or_P = 'F';}

				//$urlNo = 'ULR-'.$certNo.'/'.$curYear.'/'.$labNo.'/'.$NoOfReport.'/'.$F_or_P;
				$urlNo = 'ULR-'.$certNo.$curYear.$labNo.$NoOfReport.$F_or_P;

>>>>>>> daily-lims
				//to get tests with accreditation
				$accreditatedtest = $this->ActualTestData->find('all', array('fields' => array('test_code'=>'distinct(test_code)'),'conditions' =>array('org_sample_code IS' => $Sample_code, 'test_code IN'=>$NablTests, 'display' => 'Y')))->toArray();
				$test_string=array();
				$i=0;
				foreach ($accreditatedtest as $each) {

					$test_string[$i]=$each['test_code'];
					$i++;
				}

				//to get tests without accreditation
				$nonAccreditatedtest = $this->ActualTestData->find('all', array('fields' => array('test_code'=>'distinct(test_code)'),'conditions' =>array('org_sample_code IS' => $Sample_code, 'test_code NOT IN'=>$NablTests, 'display' => 'Y')))->toArray();
				$i=0;
				foreach ($nonAccreditatedtest as $each) {

					$test_string_ext[$i]=$each['test_code'];
					$i++;
				}
			}

		}
		$this->set(compact('showNablLogo','urlNo'));
<<<<<<< HEAD

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
=======

		foreach($test_string as $row1) {
>>>>>>> daily-lims

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


			//get al  allocated chemist if sample is for duplicate analysis
			if (isset($res2[0]['count'])>0) {

					$all_chemist_code = $conn->execute("SELECT ftr.chemist_code
														FROM m_sample_allocate AS ftr
														INNER JOIN sample_inward AS si ON si.org_sample_code=ftr.org_sample_code AND si.result_dupl_flag='D' AND ftr.sample_code='$sample_code1' ");

				$all_chemist_code= $all_chemist_code->fetchAll('assoc');

				$count_chemist = count($all_chemist_code);

			}

			//to get approved final result by Inward officer test wise
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


			//for duplicate anaylsis this is final approved result array
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
						}
						elseif (trim($data4['min_max'])=='Max') {
							$minMaxValue = "<br>(".$data4['min_max'].")";
						}
					}
				}

			}

			$str.="<tr><td>".$j."</td><td>".$data_test_name.$minMaxValue."</td>";
			$sampleTypeCode = $getSampleType['sample_type_code'];/*  check the count of max value added on 01/06/2022 */
			if($sampleTypeCode!=8){/* if sample type food safety parameter added on 01/06/2022  by shreeya */

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
				// start for max val according to food sefety parameter added on 01/06/2022 by shreeya
				$str.="<td>".$result."</td>";
				if($sampleTypeCode==8){
					$max_val = $data[0]['max_grade_value'];
					$str.="<td>".$max_val."</td>";
				}
			    // end 01/06/2022			   
			}
			$this->set('getSampleType',$getSampleType );

			$str.="<td>".$data_method_name."</td></tr>";
			$j++;
		}

		$this->set('table_str',$str );
		
		
		/* 
		Starts here
		to bifurcate accredited and non accredited test parameters on report
		The conditional non accredited tests logic starts here for NABL non accredited test results.
		The code is repitition of the logic from above code.
		on 09-08-2022 by Amol
		*/
		foreach($test_string_ext as $row1) {

			$query = $conn->execute("SELECT DISTINCT(grade.grade_desc),grade.grade_code,test_code
										FROM comm_grade AS cg
										INNER JOIN m_grade_desc AS grade ON grade.grade_code = cg.grade_code
										WHERE cg.commodity_code = '$commodity_code' AND cg.test_code = '$row1' AND cg.display = 'Y'");

			$commo_grade = $query->fetchAll('assoc');
			$str2="";

			$this->set('commo_grade',$commo_grade );
		}

		$j=1;

		foreach ($test_string_ext as $row) {

<<<<<<< HEAD
				$query = $conn->execute("SELECT cg.grade_code,cg.grade_value,cg.max_grade_value,cg.min_max
										 FROM comm_grade AS cg
										 INNER JOIN m_test_method AS tm ON tm.method_code = cg.method_code
										 INNER JOIN m_test AS t ON t.test_code = cg.test_code
										 WHERE cg.commodity_code = '$commodity_code' AND cg.test_code = '$row' AND cg.display = 'Y'
										 ORDER BY cg.grade_code ASC");


							$data = $query->fetchAll('assoc');


				$query = $conn->execute("SELECT t.test_name,tm.method_name
=======
			$query = $conn->execute("SELECT cg.grade_code,cg.grade_value,cg.max_grade_value,cg.min_max
										FROM comm_grade AS cg
										INNER JOIN m_test_method AS tm ON tm.method_code = cg.method_code
										INNER JOIN m_test AS t ON t.test_code = cg.test_code
										WHERE cg.commodity_code = '$commodity_code' AND cg.test_code = '$row' AND cg.display = 'Y'
										ORDER BY cg.grade_code ASC");
			
			$data = $query->fetchAll('assoc');


			$query = $conn->execute("SELECT t.test_name,tm.method_name
>>>>>>> daily-lims
										 FROM comm_grade AS cg
										 INNER JOIN m_test_method AS tm ON tm.method_code = cg.method_code
										 INNER JOIN m_test AS t ON t.test_code = cg.test_code
										 INNER JOIN test_formula AS tf ON tf.test_code = cg.test_code AND tm.method_code = cg.method_code
										 WHERE cg.commodity_code = '$commodity_code' AND cg.test_code = '$row' AND cg.display = 'Y'
										 ORDER BY t.test_name ASC");

<<<<<<< HEAD
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


			//get al  allocated chemist if sample is for duplicate analysis
				if (isset($res2[0]['count'])>0) {

					 $all_chemist_code = $conn->execute("SELECT ftr.chemist_code
					 									 FROM m_sample_allocate AS ftr
														 INNER JOIN sample_inward AS si ON si.org_sample_code=ftr.org_sample_code AND si.result_dupl_flag='D' AND ftr.sample_code='$sample_code1' ");

				   $all_chemist_code= $all_chemist_code->fetchAll('assoc');

					$count_chemist = count($all_chemist_code);

				}

			//to get approved final result by Inward officer test wise
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


			//for duplicate anaylsis this is final approved result array
			if (count($test_result)>0) {

=======
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


			//get al  allocated chemist if sample is for duplicate analysis
			if (isset($res2[0]['count'])>0) {

					$all_chemist_code = $conn->execute("SELECT ftr.chemist_code
														FROM m_sample_allocate AS ftr
														INNER JOIN sample_inward AS si ON si.org_sample_code=ftr.org_sample_code AND si.result_dupl_flag='D' AND ftr.sample_code='$sample_code1' ");

				$all_chemist_code= $all_chemist_code->fetchAll('assoc');

				$count_chemist = count($all_chemist_code);

			}

			//to get approved final result by Inward officer test wise
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


			//for duplicate anaylsis this is final approved result array
			if (count($test_result)>0) {

>>>>>>> daily-lims
				foreach ($test_result as $key=>$val) {
					$result_D= $val;
				}
			} else {
				$result_D="";
<<<<<<< HEAD
			}

			$commencement_date= $this->MSampleAllocate->find('all',array('order' => array('commencement_date' => 'asc'),'fields' => array('commencement_date'),'conditions' =>array('org_sample_code IS' => $Sample_code, 'display' => 'Y')))->toArray();
			$this->set('comm_date',$commencement_date[0]['commencement_date']);

			if (!empty($count_chemist)) {

				$count_chemist1 =  $count_chemist;
			} else {
				$count_chemist1 = '';
			}

=======
			}

			$commencement_date= $this->MSampleAllocate->find('all',array('order' => array('commencement_date' => 'asc'),'fields' => array('commencement_date'),'conditions' =>array('org_sample_code IS' => $Sample_code, 'display' => 'Y')))->toArray();
			$this->set('comm_date',$commencement_date[0]['commencement_date']);

			if (!empty($count_chemist)) {

				$count_chemist1 =  $count_chemist;
			} else {
				$count_chemist1 = '';
			}

>>>>>>> daily-lims
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
						}
						elseif (trim($data4['min_max'])=='Max') {
							$minMaxValue = "<br>(".$data4['min_max'].")";
						}
					}
				}

			}

			$str2.="<tr><td>".$j."</td><td>".$data_test_name.$minMaxValue."</td>";
			$sampleTypeCode = $getSampleType['sample_type_code'];/*  check the count of max value added on 01/06/2022 */
			if($sampleTypeCode!=8){/* if sample type food safety parameter added on 01/06/2022  by shreeya */

				// Draw tested test reading values,
				foreach ($commo_grade as $key=>$val) {

					$key = $val['grade_code'];

					$grade_code_match = 'no';

					foreach ($data as $data4) {

						$data_grade_code = $data4['grade_code'];

						if ($data_grade_code == $key) {

							$grade_code_match = 'yes';

							if (trim($data4['min_max'])=='Range') {

								$str2.="<td>".$data4['grade_value']."-".$data4['max_grade_value']."</td>";

							} elseif (trim($data4['min_max'])=='Min') {

								$str2.="<td>".$data4['grade_value']."</td>";

							} elseif (trim($data4['min_max'])=='Max') {

								$str2.="<td>".$data4['max_grade_value']."</td>";

							} elseif (trim($data4['min_max'])=='-1') {

								$str2.="<td>".$data4['grade_value']."</td>";

							}
						}
					}

					if ($grade_code_match == 'no') {
						$str2.="<td>---</td>";
					}

				}

			}
			//for duplicate analysis chemist wise results
			if ($count_chemist1>0) {

				for ($g=0;$g<$count_chemist;$g++) {
					$str2.="<td align='center'>".$result[$g]."</td>";
				}

				//for final result column
				$str2.="<td align='center'>".$result_D."</td>";

			//for single analysis final results
			} else {
				// start for max val according to food sefety parameter added on 01/06/2022 by shreeya
				$str2.="<td>".$result."</td>";
				if($sampleTypeCode==8){
					$max_val = $data[0]['max_grade_value'];
					$str2.="<td>".$max_val."</td>";
				}
			    // end 01/06/2022			   
			}
			$this->set('getSampleType',$getSampleType );

			$str2.="<td>".$data_method_name."</td></tr>";
			$j++;
		}

		$this->set('table_str2',$str2 );
		/* 
		Ends here
		The conditional non accredited tests logic ends here for NABL non accredited test results.
		The code is repitition of the logic from above code.
		on 09-08-2022 by Amol
		*/

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

			$query = $conn->execute("SELECT ur.user_flag,office.ro_office,usr.email
									 FROM workflow AS w
									 INNER JOIN dmi_ro_offices AS office ON office.id = w.src_loc_id
									 INNER JOIN dmi_users AS usr ON usr.id=w.src_usr_cd
									 INNER JOIN dmi_user_roles AS ur ON usr.email= ur.user_email_id
									 WHERE w.org_sample_code='$Sample_code'
									 AND stage_smpl_flag IN('OF','HF')");

			$sample_forwarded_office = $query->fetchAll('assoc');

			$sample_final_date = $this->Workflow->find('all',array('fields'=>'tran_date','conditions'=>array('stage_smpl_flag'=>'FG','org_sample_code IS'=>$Sample_code)))->first();
			$sample_final_date['tran_date'] = date('d/m/Y');//taking current date bcoz creating pdf before grading for preview.

			//Customer Details on 05-08-2022 by akash
			$this->loadModel('LimsCustomerDetails');
			$customerDetails = $this->LimsCustomerDetails->find('all')->where(['org_sample_code IS' => $Sample_code])->first();
			if (!empty($customerDetails)) {
				$customer_details = $customerDetails;

				$stateAndDistrict = $conn->execute("SELECT ds.state_name,dd.district_name
													FROM lims_customer_details AS lcd
													INNER JOIN dmi_states AS ds ON ds.id = lcd.state
													INNER JOIN dmi_districts AS dd ON dd.id = lcd.district
													WHERE lcd.org_sample_code = '$Sample_code'")->fetch('assoc');
				if (!empty($stateAndDistrict)) {
					$this->set('stateAndDistrict',$stateAndDistrict);
				} else {
					$stateAndDistrict = null;
				}

			} else {
				$customer_details = null;
			}
			
			$this->set('sample_final_date',$sample_final_date['tran_date']);
			$this->set('sample_forwarded_office',$sample_forwarded_office);
			$this->set('test_report',$test_report);
			$this->set('customer_details',$customer_details);
			// Call to function for generate pdf file,
			// change generate pdf file name,
			$current_date = date('d-m-Y');
			$test_report_name = 'grade_report_'.$sample_code1.'.pdf';

			//store pdf path to sample inward table to preview further
			$pdf_path = '/writereaddata/LIMS/reports/'.$test_report_name;
			$this->SampleInward->updateAll(array('report_pdf'=>"$pdf_path"),array('org_sample_code'=>$Sample_code));

			$this->Session->write('pdf_file_name',$test_report_name);

			//call to the pdf creaation common method
		
			if($this->request->is('ajax')){//on consent check box click
				$this->EsigncallTcpdf($this->render(),'F',$test_report_name);//to save and store
			
			}else{//on preview link click
				$this->EsigncallTcpdf($this->render(),'I',$test_report_name);//to preview
			}


		}

	}

}
?>
