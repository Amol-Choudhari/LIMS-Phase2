<?php
namespace App\Controller;

use Cake\Event\Event;
use App\Network\Email\Email;
use Cake\ORM\Entity;
use Cake\Datasource\ConnectionManager;
use Cake\Http\Client\Request;
use Cake\View;

 class SampleAllocateController extends AppController {

	var $name = 'SampleAllocate';

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/********************************************************************************************************************************************************************************************************************************/

	public function initialize(): void {
		parent::initialize();
		$this->viewBuilder()->setLayout('admin_dashboard');
		$this->viewBuilder()->setHelpers(['Form','Html']);
		$this->loadComponent('Customfunctions');
	}

/********************************************************************************************************************************************************************************************************************************/

	//To Validate User
	public function authenticateUser(){

		$this->loadModel('DmiUserRoles');
		$user_access = $this->DmiUserRoles->find('all',array('conditions'=>array('user_email_id IS'=>$this->Session->read('username'))))->first();

		if(!empty($user_access)){
			//proceed
		}else{
			echo "Sorry.. You don't have permission to view this page";
			exit();
		}
	}

/********************************************************************************************************************************************************************************************************************************/

	//To Get Sample Code and Redirect To Sample Allocate Window
	public function redirectToAllocate($allocate_sample_cd){

		$this->Session->write('allocate_sample_cd',$allocate_sample_cd);
		$this->redirect(array('controller'=>'SampleAllocate','action'=>'sample_allocate'));

	}

/********************************************************************************************************************************************************************************************************************************/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>[Sample Allocate Method Starts]>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

	//SAMPLE ALLOCATE
	public function sampleAllocate() {

		$this->authenticateUser();
		$this->viewBuilder()->setLayout('admin_dashboard');
		$conn = ConnectionManager::get('default');

		//Set Variables For Pop Up Messages
		$message = '';
		$message_theme = '';
		$redirect_to = '';

		$allocate_sample_cd = trim($this->Session->read('allocate_sample_cd'));//trim added on 03-06-2022 by Shreeya

			if (!empty($allocate_sample_cd)) {

				//Load Models
				$this->loadModel('CodeDecode');
				$this->loadModel('MSampleAllocate');
				$this->loadModel('DmiUsers');
				$this->loadModel('ActualTestData');
				$this->loadModel('Workflow');
				$this->loadModel('MUnitWeight');

				$this->set('allocate_sample_cd',array($allocate_sample_cd=>$allocate_sample_cd));

				$user_type = $this->DmiUsers->find('list',array('order' => array('role' => 'ASC'),'keyField' => 'role','valueField'=>'role','conditions'=>array('role IN' =>array('Jr Chemist','Sr Chemist'), 'status'=>'active')))->toArray();

				$this->set('user_type',$user_type);

				//Change variable name grade_desc to unit_desc
				$this->loadModel('MUnitWeight');
				$unit_desc = $this->MUnitWeight->find('list',array('order' => array('unit_weight' => 'ASC'),'fields'=>array('unit_id','unit_weight'),'conditions' => array('display' => 'Y')))->toArray();
				$this->set('unit_desc',$unit_desc);

					if ($this->request->is('post')) {
						$postData = $this->request->getData();

						//Post Data Validations
						$validate_err = $this->allocatePostValidations($this->request->getData());

						if ($validate_err != '') {

							$this->set('validate_err',$validate_err);
							return null;

						}

						//HTML Encoding
						foreach ($postData as $key => $value) {

							$postData[$key] = htmlentities($postData[$key], ENT_QUOTES);

						}

							//Date & Time Format Check
							$dStart = new \DateTime(date('Y-m-d H:i:s'));

							$date = $dStart->createFromFormat('d/m/Y',$postData["rec_from_dt"]);
							$from_dt = $date->format('Y-m-d');
							$from_dt = date('Y-m-d',strtotime($from_dt));

							$date1 = $dStart->createFromFormat('d/m/Y', $postData['rec_to_dt']);
							$to_dt=$date1->format('Y/m/d');
							$to_dt = date('Y-m-d',strtotime($to_dt));

							if ($postData['category_code']==0) {

								$query = $conn->execute("SELECT category_code FROM m_commodity WHERE commodity_code='".$postData['commodity_code']."'");
								$category_code = $query->fetchAll('assoc');
								$category_code = $category_code[0];
							}

						//Save Records
						if (isset($postData['save'])) {

							$sample_code = trim($postData['stage_sample_code']);
							$postData['sample_code'] = $sample_code;

							$query = $conn->execute("SELECT si.org_sample_code
														FROM sample_inward AS si
														INNER JOIN workflow AS w ON w.org_sample_code = si.org_sample_code
														WHERE w.stage_smpl_cd = '$sample_code'");

							$ogrsample1 = $query->fetchAll('assoc');

							$ogrsample	= $ogrsample1[0]['org_sample_code'];
							$postData['org_sample_code'] = $ogrsample;
							$postData['rec_from_dt'] = $from_dt;
							$postData['rec_to_dt'] = $to_dt;
							$postData['lab_code'] = $_SESSION['posted_ro_office'];

							//Date & Time Format Method
							$dStart = new \DateTime(date('Y-m-d H:i:s'));

							$expect_complt = $dStart->createFromFormat('d/m/Y', $postData['expect_complt']);
							$expect_complt1	= $expect_complt->format('Y/m/d');
							$expect_complt1 = date('Y-m-d',strtotime($expect_complt1));

							$postData['expect_complt']	= $expect_complt1;

							if ($postData['category_code']==0) {

								$postData['category_code']	= $category_code['category_code'];

								$dStart = new \DateTime(date('Y-m-d H:i:s'));

								$expect_complt = $dStart->createFromFormat('d/m/Y', $postData['expect_complt']);
								$expect_complt1	= $expect_complt->format('Y/m/d');
								$expect_complt1 = date('Y-m-d',strtotime($expect_complt1));

								$postData['expect_complt']	= $expect_complt1;
							}

							$sampleAllocateEntity = $this->MSampleAllocate->newEntity($postData);

							$allocateResult = $this->MSampleAllocate->save($sampleAllocateEntity);

								if ($allocateResult) {

									if ($_SESSION['role']=='Lab Incharge') {

										$conn->execute("UPDATE workflow SET stage_smpl_flag='LI' WHERE org_sample_code='$ogrsample' AND stage_smpl_flag='OF'");

										$conn->execute("UPDATE sample_inward SET status_flag='LA' WHERE org_sample_code='$ogrsample'");

									} else {

										$conn->execute("UPDATE sample_inward SET status_flag='A' WHERE org_sample_code='$ogrsample'");

									}


									$alloc_to_user_code	= $postData['alloc_to_user_code'];

									$stage_smpl_cd = $postData['chemist_code'];

									$user_data1 = $this->DmiUsers->find('all', array('conditions'=> array('id' =>$alloc_to_user_code)))->first();

									$role_code = $user_data1['posted_ro_office'];

									$tran_date = $postData['tran_date'];

									$data = $this->Workflow->find('all', array('conditions'=> array('stage_smpl_cd' => $sample_code)))->toArray();

									$stage = $data[0]['stage']+1;

									$workflow_data = array("org_sample_code"=>$ogrsample,
															"src_loc_id"=>$_SESSION["posted_ro_office"],
															"src_usr_cd"=>$_SESSION["user_code"],
															"dst_loc_id"=>$role_code,
															"dst_usr_cd"=>$alloc_to_user_code,
															"stage_smpl_cd"=>$stage_smpl_cd,
															"user_code"=>$_SESSION["user_code"],
															"tran_date"=>$tran_date,
															"stage"=>$stage,"stage_smpl_flag"=>"TA");

									$_SESSION["sample"] = $postData['stage_sample_code'];

									$codeDecodeEntity = $this->CodeDecode->newEntity($postData);

										if (!$this->CodeDecode->save($codeDecodeEntity)) {

											$message = 'Sorry.. There is some technical issues. please check';
											$message_theme = 'failed';
											$redirect_to = 'sample_allocate';

										}

									$workflowEntity = $this->Workflow->newEntity($workflow_data);

									$this->Workflow->save($workflowEntity);

									$_SESSION["posted_ro_office"] = $_SESSION["posted_ro_office"];
									$_SESSION["loc_user_id"] = $_SESSION["user_code"];


									$test = explode(",",$postData['tests']);

									$test = array_unique($test);

									for ($i=0;$i<count($test);$i++) {

										$postData['test_code']= $test[$i];
										$test_alloc[] = $postData;

									}

									$ActualTestDataEntity = $this->ActualTestData->newEntities($test_alloc);

									foreach ($ActualTestDataEntity as $eachData) {

										if (!$this->ActualTestData->save($eachData)) {

											$message = 'Sorry.. There is some technical issues. please check';
											$message_theme = 'failed';
											$redirect_to = 'sample_allocate';
										}
									}

									$get_id = $this->MSampleAllocate->find('all',array('fields'=>'sr_no','conditions'=>array('sample_code'=>$sample_code),'order'=>'sr_no desc'))->first();
									$lastInsertedId = $get_id['sr_no'];

									$query = $conn->execute("SELECT chemist_code,f_name ,l_name, role
																FROM m_sample_allocate AS s
																INNER JOIN dmi_users AS u ON s.alloc_to_user_code=u.id
																WHERE sr_no='$lastInsertedId'");

									$chemist_code = $query->fetchAll('assoc');

									//Load Model For User Role
									$this->loadModel('Workflow');

									//Get Source User Role
									$get_source_user_role = $this->Workflow->find()->select(['src_usr_cd'])->where(['stage_smpl_cd IS' => $chemist_code[0]['chemist_code']])->first();
									$sourceusercode = $get_source_user_role['src_usr_cd'];
									$source_user_role = $this->DmiUsers->find('all',array('fields'=>array('role'),'conditions'=>array('id IS'=>$sourceusercode)))->first();

									//Get Destination User Role
									$get_destination_user_role = $this->Workflow->find()->select(['dst_usr_cd'])->where(['stage_smpl_cd IS' => $chemist_code[0]['chemist_code']])->first();
									$destinationusercode = $get_destination_user_role['dst_usr_cd'];
									//$destination_user_role = $this->DmiUsers->find('all',array('fields'=>array('role'),'conditions'=>array('id IS'=>$usercode)))->first();

									//Call To the Common SMS/Email Sending Method
									$this->loadModel('DmiSmsEmailTemplates');
									//print_r($chemist_code); exit;
									if ($source_user_role['role'] == 'Inward Officer') {

										//When Sample Is Allocated by Inward Officer
										//$this->DmiSmsEmailTemplates->sendMessage(2023,$chemist_code[0]['chemist_code'],$sourceusercode);
										//Sample Is Allocated to Chemist
										//$this->DmiSmsEmailTemplates->sendMessage(2024,$chemist_code[0]['chemist_code'],$destinationusercode);
									
									} elseif ($source_user_role['role'] == 'RAL/CAL OIC') {


									}


									$message = 'Sample Code '.$chemist_code[0]['chemist_code'].' is allocated to  '.$chemist_code[0]['f_name'].' '.$chemist_code[0]['l_name'].'('.$chemist_code[0]['role'].'). ';
									$message_theme = 'success';
									$redirect_to = 'available_to_allocate';

								} else {

										$message = 'Sorry.. There is some technical issues. please check';
										$message_theme = 'failed';
										$redirect_to = 'sample_allocate';

								}

						} elseif (isset($postData['update'])) {


						}

					}
			}

			//Set Variables To Show Popup Messages From View File
			$this->set('message',$message);
			$this->set('message_theme',$message_theme);
			$this->set('redirect_to',$redirect_to);

	}


/********************************************************************************************************************************************************************************************************************************/

	//To Get Sample Code & Redirect To Forwarding To Lab Incharge Window
	public function redirectToForward($forward_sample_cd){

		$this->Session->write('forward_sample_cd',$forward_sample_cd);
		$this->redirect(array('controller'=>'SampleAllocate','action'=>'sample_forward'));

	}

/********************************************************************************************************************************************************************************************************************************/

/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>[Sample Forward Method Starts]>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

	public function sampleForward(){

		$this->authenticateUser();
		$this->viewBuilder()->setLayout('admin_dashboard');
		$conn = ConnectionManager::get('default');

		//Set Variables To Show Popup Messages From View File
		$message = '';
		$message_theme = '';
		$redirect_to = '';

		$forward_sample_cd = trim($this->Session->read('forward_sample_cd')); //added trim() on 24-06-2022 by Amol;

		if (!empty($forward_sample_cd)) {

			$this->loadModel('CodeDecode');
			$this->loadModel('MSampleAllocate');
			$this->loadModel('DmiUsers');
			$this->loadModel('ActualTestData');
			$this->loadModel('Workflow');
			$this->loadModel('MUnitWeight');

			$this->set('forward_sample_cd',array($forward_sample_cd=>$forward_sample_cd));

			$user_type=$this->DmiUsers->find('list',array('order' => array('role' => 'ASC'),'keyField' => 'role','valueField'=>'role','conditions'=>array('role' =>'Lab Incharge', 'status'=>'active')))->toArray();
			
			$this->set('user_type',$user_type);

			if ($this->request->is('post')) {

				$postdata = $this->request->getData();

				//Post Data Validations
				$validate_err = $this->allocatePostValidations($this->request->getData());

				if ($validate_err != '') {

					$this->set('validate_err',$validate_err);
					return null;
				}

				//HTML Encoding
				foreach ($postdata as $key => $value) {

					$postdata[$key] = htmlentities($postdata[$key], ENT_QUOTES);
				}

				if (null !==($this->request->getData('save'))) {

					$sample_code = trim($this->request->getData('stage_sample_code'));

					$query = $conn->execute("SELECT si.org_sample_code
												FROM sample_inward AS si
												INNER JOIN workflow as w on w.org_sample_code = si.org_sample_code
												WHERE w.stage_smpl_cd = '$sample_code'");

					$ogrsample1 = $query->fetchAll('assoc');

					$ogrsample = $ogrsample1[0]['org_sample_code'];
					$alloc_to_user_code	= $this->request->getData('alloc_to_user_code');

					$user_data1	= $this->DmiUsers->find('all', array('conditions'=> array('id IS' =>$alloc_to_user_code)))->first();
					$role_code = $user_data1['posted_ro_office'];

					$tran_date = $this->request->getData('tran_date');

					$data = $this->Workflow->find('all', array('conditions'=> array('stage_smpl_cd IS' => $sample_code)))->toArray();
					$stage = $data[0]['stage']+1;

					//Generate Stage Sample Code
					$stage_smpl_cd = $this->Customfunctions->createStageSampleCode();

					$workflow_data = array("org_sample_code"=>$ogrsample,
											"src_loc_id"=>$_SESSION["posted_ro_office"],
											"src_usr_cd"=>$_SESSION["user_code"],
											"dst_loc_id"=>$role_code,
											"dst_usr_cd"=>$alloc_to_user_code,
											"stage_smpl_cd"=>$stage_smpl_cd,
											"user_code"=>$_SESSION["user_code"],
											"tran_date"=>$tran_date,
											"stage"=>$stage,
											"stage_smpl_flag"=>"IF");

					$workflowEntity = $this->Workflow->newEntity($workflow_data);

					$this->Workflow->save($workflowEntity);

					$conn->execute("UPDATE sample_inward SET status_flag='IF' WHERE org_sample_code='$ogrsample'");

					//Call To the Common SMS/Email Sending Method
					$this->loadModel('DmiSmsEmailTemplates');
					//$this->DmiSmsEmailTemplates->sendMessage(2029,$stage_smpl_cd,$_SESSION["user_code"]);
					//$this->DmiSmsEmailTemplates->sendMessage(2032,$stage_smpl_cd,$alloc_to_user_code);
					$message = 'The Sample is Forwarded to Lab Incharge with '.$stage_smpl_cd.' code!';
					$message_theme = 'success';
					$redirect_to = 'available_to_allocate';

				}

			}

		}

		//Set Variables To Show Popup Messages From View File
		$this->set('message',$message);
		$this->set('message_theme',$message_theme);
		$this->set('redirect_to',$redirect_to);

	}


/********************************************************************************************************************************************************************************************************************************************/

/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>[Get Total Quantity]>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

	public function getTtlQnt() {

		$this->autoRender=false;
		$conn = ConnectionManager::get('default');

		$this->loadModel('SampleInward');
		$this->loadModel('MSampleAllocate');
		$this->loadModel('MCommodity');


		$commodity_code=trim($_POST['commodity_code']);
		$category_code=trim($_POST['category_code']);
		$sample_code=trim($_POST['sample_code']);
		$type=$_POST['type'];

		if (!is_numeric($sample_code) || $sample_code=='') {

			echo '#[error]~Invalid Sample Code#';
			exit;
		}

		if (!is_numeric($commodity_code)) {

			echo '#[error]~Invalid Commodity Code#';
			exit;
		}

		if (!is_numeric($category_code) || $category_code=='') {

			echo '#[error]~Invalid Category Code#';
			exit;
		}

		if ($type=="A" || $type=="F") {

		} else {

			echo '#[error]~Invalid Type#';
			exit;
		}

		$total = $conn->execute("SELECT count(*) AS total FROM m_sample_allocate AS s
									INNER JOIN workflow AS w ON s.org_sample_code=w.org_sample_code
									WHERE s.sample_code='$sample_code' AND w.dst_usr_cd=".$_SESSION['user_code']." AND s.display='Y' AND alloc_cncl_flag='N'");

		$total = $total->fetchAll('assoc');


		$total=$total[0]['total'];

			if ($total<=0) {
				//updated this query on 04-05-2022 by Amol, changed "si.sample_total_qnt AS total" to "si.actual_received_qty AS total"
				$str= "SELECT w.stage_smpl_cd,w.stage_smpl_cd,si.actual_received_qty AS total,si.parcel_size,mnw.unit_weight
							FROM sample_inward AS si
							INNER JOIN workflow AS w ON si.org_sample_code=w.org_sample_code
							INNER JOIN m_unit_weight AS mnw ON mnw.unit_id=si.parcel_size AND si.commodity_code='$commodity_code' AND si.category_code='$category_code' AND w.stage_smpl_flag IN('OF','IF','AS') AND w.src_loc_id=".$_SESSION['posted_ro_office']."AND w.stage_smpl_cd='$sample_code' AND w.dst_usr_cd= ".$_SESSION['user_code']." GROUP BY si.stage_sample_code,w.stage_smpl_cd,si.sample_total_qnt,si.parcel_size,mnw.unit_weight ORDER BY si.stage_sample_code asc";

			} else {

				//updated this query on 04-05-2022 by Amol, changed sum(s.sample_qnt) to sum(s.sample_qnt)/2, bcoz it was doubling the final sum value
				//made this query same as from function "getttlQnt()" on 04-05-2022, replaced "si.sample_total_qnt" with "si.actual_received_qty"
				$str= "SELECT w.stage_smpl_cd,w.stage_smpl_cd,si.actual_received_qty,si.parcel_size,sum(s.sample_qnt)/2 AS qty,si.actual_received_qty - sum(s.sample_qnt)/2 AS total,mnw.unit_weight
						FROM sample_inward AS si
						INNER JOIN workflow AS w ON si.org_sample_code=w.org_sample_code
						INNER JOIN m_sample_allocate AS s ON si.org_sample_code=s.org_sample_code
						INNER JOIN m_unit_weight AS mnw ON mnw.unit_id=si.parcel_size AND si.commodity_code='$commodity_code'AND si.category_code='$category_code'AND w.stage_smpl_flag IN('OF','IF','AS') AND w.src_loc_id=".$_SESSION['posted_ro_office']."AND s.sample_code='$sample_code' AND w.dst_usr_cd=".$_SESSION['user_code']." AND  s.display='Y'
						GROUP BY si.stage_sample_code,w.stage_smpl_cd,si.actual_received_qty,si.parcel_size,mnw.unit_weight
						ORDER BY si.stage_sample_code ASC";
			}
			
			$test2 = $conn->execute($str);
			$test2 = $test2->fetchAll('assoc');

			if ($test2) {

				echo '#'.json_encode($test2[0]['total']).'#';
				exit;

			} else {

				echo'#0#';
				exit;
			}

	}

/********************************************************************************************************************************************************************************************************************************************/

	//GET CHEMIST CODE BY AJAX
	public function getChemLiCode() {

		if (null !==($_POST['user_type'])) {

			$user_code = $_POST['user_type'];

		} else {

			echo '#[error]~Invalid Type1!#';
			exit;
		}

		if ($user_code=="Lab Incharge" || $user_code=="Jr Chemist" || $user_code=="Sr Chemist" || $user_code=="Cheif Chemist" ) {

		} else {
			echo '#[error]~Invalid Type2!#';
			exit;
		}

		//Generate Stage Sample Code
		$random_code = $this->Customfunctions->createChemistCode();

		if ($_POST['user_type']=='Jr Chemist' || $_POST['user_type']=='Sr Chemist' || $_POST['user_type']=='Cheif Chemist') {

			$chemist_code	= $random_code;
			$li_code		= '-';
			echo '#'.$chemist_code."~".$li_code.'#';

		} elseif ($_POST['user_type']=='Lab Incharge') {

			$li_code		= $random_code;
			$chemist_code	= '-';
			echo '#'.$chemist_code."~".$li_code.'#';
		}

		exit;
	}

/********************************************************************************************************************************************************************************************************************************************/

/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>[Get Users]>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

	//GET USERS
	public function getUsers() {

		$this->autoRender=false;
		$conn = ConnectionManager::get('default');
		$str = "";
		$location_code = $_SESSION['posted_ro_office'];
		$user_code = $_POST['user_type'];
		$role = $_SESSION['role'];

		if (null !==($_POST['user_type'])) {

		} else {

			echo '[error]~Invalid Type1!';
			exit;

		}

		if ($user_code == "Lab Incharge" || $user_code == "Jr Chemist" || $user_code == "Sr Chemist" || $user_code == "Cheif Chemist" ) {

		} else {

			echo '[error]~Invalid Type2!';
			exit;
		}

		$patternb='/^[A-Z a-z]/';
		$rttttttv=preg_match($patternb,$user_code);

		if ($rttttttv==0) {

			echo '[error]~Invalid User Type3!';
			exit;
		}


		if ($user_code=='Lab Incharge') {

			$users_name	= $conn->execute("SELECT * FROM dmi_users WHERE  role IN(SELECT role FROM dmi_users WHERE role='Lab Incharge' AND status != 'disactive' ) AND status != 'disactive' ");

		} else {

			$users_name	= $conn->execute("SELECT * FROM dmi_users WHERE posted_ro_office=$location_code AND role IN(SELECT role FROM dmi_users WHERE role='$user_code' AND status != 'disactive' ) AND status != 'disactive' ");
		}

		$users_name = $users_name->fetchAll('assoc');

			for ($i=0;$i<count($users_name);$i++) {

				$str.="<option value='".$users_name[$i]['id']."'>".$users_name[$i]['f_name'].' '.$users_name[$i]['l_name']."</option>";

			}

		echo $str;

	}

/********************************************************************************************************************************************************************************************************************************************/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>[Get Quantity]>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

	//GET QUANTITY BY AJAX
	public function getQty() {

		$this->autoRender=false;
		$conn = ConnectionManager::get('default');

		$this->loadModel('SampleInward');
		$this->loadModel('MSampleAllocate');

		$commodity_code	= trim($_POST['commodity_code']);
		$category_code	= trim($_POST['category_code']);
		$sample_code = trim($_POST['sample_code']);

		if (!is_numeric($commodity_code)) {
			echo '#[error]#';
			exit;
		}

		if (!is_numeric($category_code) || $category_code=='') {
			echo '#[error]#';
			exit;
		}

		if (!is_numeric($sample_code) || $sample_code=='') {
			echo '#[error]~Invalid Sample Code#';
			exit;
		}

		$total= $conn->execute("SELECT count(*) AS total
									FROM m_sample_allocate AS s
									INNER JOIN workflow AS w ON s.org_sample_code=w.org_sample_code
									WHERE s.sample_code='$sample_code' AND w.dst_usr_cd=".$_SESSION['user_code']." AND s.display='Y' AND alloc_cncl_flag='N'");

		$total = $total->fetchAll('assoc');

		$total=$total[0]['total'];

		if ($total<=0) {
			//updated this query on 04-05-2022 by Amol, changed "si.sample_total_qnt AS total" to "si.actual_received_qty AS total"
			$str= "SELECT w.stage_smpl_cd,w.stage_smpl_cd,si.actual_received_qty,si.actual_received_qty AS total,si.parcel_size,mnw.unit_weight
						FROM sample_inward AS si
						INNER JOIN workflow AS w ON si.org_sample_code=w.org_sample_code
						INNER JOIN m_unit_weight AS mnw ON mnw.unit_id=si.parcel_size AND si.commodity_code='$commodity_code' AND si.category_code='$category_code'AND w.stage_smpl_flag IN('OF','IF','AS') AND w.src_loc_id=".$_SESSION['posted_ro_office']."AND w.stage_smpl_cd='$sample_code' AND w.dst_usr_cd=".$_SESSION['user_code']."GROUP BY si.sample_total_qnt,si.stage_sample_code,w.stage_smpl_cd,si.actual_received_qty,si.parcel_size,mnw.unit_weight ORDER BY si.stage_sample_code asc";

		} else {
			//updated this query on 04-05-2022 by Amol, changed sum(s.sample_qnt) to sum(s.sample_qnt)/2, bcoz it was doubling the final sum value
			//made this query same as from function "getttlQnt()" on 04-05-2022, replaced "si.sample_total_qnt" with "si.actual_received_qty"
			$str= "SELECT w.stage_smpl_cd,w.stage_smpl_cd,si.actual_received_qty,si.parcel_size,sum(s.sample_qnt)/2 AS qty,si.actual_received_qty - sum(s.sample_qnt)/2 AS total,mnw.unit_weight
						FROM sample_inward AS si
						INNER JOIN workflow AS w ON si.org_sample_code=w.org_sample_code
						INNER JOIN m_sample_allocate AS s ON si.org_sample_code=s.org_sample_code
						INNER JOIN m_unit_weight AS mnw ON mnw.unit_id=si.parcel_size AND si.commodity_code='$commodity_code'AND si.category_code='$category_code'AND w.stage_smpl_flag IN('OF','IF','AS') AND w.src_loc_id=".$_SESSION['posted_ro_office']."AND s.sample_code='$sample_code' AND w.dst_usr_cd=".$_SESSION['user_code']." AND  s.display='Y'
						GROUP BY si.stage_sample_code,w.stage_smpl_cd,si.actual_received_qty,si.parcel_size,mnw.unit_weight
						ORDER BY si.stage_sample_code ASC";

		}

	//commented as not in use below
	/*	$str2 = "SELECT w.stage_smpl_cd,w.stage_smpl_cd,si.sample_total_qnt,si.parcel_size,mnw.unit_weight
					FROM sample_inward AS si
					INNER JOIN workflow AS w ON si.org_sample_code=w.org_sample_code
					INNER JOIN m_unit_weight AS mnw ON mnw.unit_id=si.parcel_size AND si.commodity_code='$commodity_code' AND si.category_code='$category_code' AND w.stage_smpl_flag IN('SD','IF','AS','OF') AND w.dst_loc_id=".$_SESSION['posted_ro_office']." AND w.stage_smpl_cd=trim($sample_code) AND w.dst_usr_cd=".$_SESSION['user_code']."";
*/

		$test2	= $conn->execute($str);
		$test2 = $test2->fetchAll('assoc');

		if ($test2) {

			echo '#'.json_encode($test2).'#';
			exit;

		} else {

			echo '#'.json_encode($test2).'#';
			exit;
		}


	}

/********************************************************************************************************************************************************************************************************************************************/

	//GET DETAILS
	public function getDetails(){

		$this->autoRender=false;
		$conn = ConnectionManager::get('default');

		$sam_code1		= trim($_POST['sample_code']);
		$user_code1		= trim($_POST['alloc_to_user_code'] );

		if (!is_numeric($user_code1) || $user_code1=='') {

			echo '#[error]~Invalid user Code#';
			exit;
		}

		if (!is_numeric($sam_code1) || $sam_code1=='') {

			echo '#[error]~Invalid Sample Code#';
			exit;
		}

		$detail	= $conn->execute("SELECT  a.sample_qnt,a.sample_unit,a.alloc_to_user_code,a.test_n_r,a.expect_complt
									FROM m_sample_allocate AS a
									WHERE a.sample_code='$sam_code1' AND a.alloc_to_user_code='$user_code1'AND a.display='Y'AND alloc_cncl_flag='N'");

		$detail = $detail->fetchAll('assoc');

		if ((count($detail))!=0) {

			echo '#'.json_encode($detail[0]).'#';
		} else {

			echo "#NO_DATA#";
		}

		exit;
	}


/********************************************************************************************************************************************************************************************************************************************/

/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>[Update Details]>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

	//UPDATE DETAILS
	public function updateDetails() {

		$this->loadModel('SamAllocate');
		$this->loadModel('Workflow1');

		if (null !==($_POST['sample_code']) && null !== ($_POST['alloc_to_user_code'])) {

			if (null !==($_POST['arrtestalloc'])) {

				$test_code=$_POST['arrtestalloc'];

			} else {

				$test_code="";
			}

			$sample_code=trim($_POST['sample_code']);
			$user_type=$_POST['user_type'];
			$allocate_to=$_POST['alloc_to_user_code'];
			$com_code=$_POST['commodity_name'];
			$com_cat=$_POST['commodity_category'];
			$alloc_cncl_flag=$_POST['alloc_cncl_flag'];
			$fin_year=$_POST['fin_year'];
			$test_n_r=$_POST['test_n_r'];
			$rec_from_dt=$_POST['rec_from_dt'];
			$rec_to_dt=$_POST['rec_to_dt'];
			$expect_complt1=$_POST['expect_complt'];
			$sample_qnt=$_POST['sample_qnt'];
			$sample_unit=$_POST['sample_unit'];
			$lab_code=$_SESSION['posted_ro_office'];

			if (null !==($_POST['re_test'])) {

				$re_test=$_POST['re_test'];

			} else {

				$re_test="";
			}

			$today=date('Y-m-d');
			$chemist_code_new=$this->SamAllocate->query("SELECT chemist_code FROM m_sample_allocate WHERE sample_code='$sample_code' AND alloc_to_user_code='$allocate_to'");

			$chemist_code=$chemist_code_new[0][0]['chemist_code'];

			if ($test_n_r=='R') {

				$test_cnt="SELECT max(test_n_r_no) FROM m_sample_allocate WHERE sample_code='$sample_code' AND alloc_to_user_code='$allocate_to' AND test_n_r='R'";
				$test_n_r_no=$test_cnt['0']['0']['max']+1;

			} else {

				$test_n_r_no=1;
			}

			//Check Date Format
			$dStart = new \DateTime(date('Y-m-d H:i:s'));

			//From Date
			$date = $dStart->createFromFormat('d/m/Y',$rec_from_dt);
			$from_dt=$date->format('Y-m-d');
			$from_dt = date('Y-m-d',strtotime($from_dt));

			//To Date
			$date1 = $dStart->createFromFormat('d/m/Y', $rec_to_dt);
			$to_dt=$date1->format('Y-m-d');
			$to_dt = date('Y-m-d',strtotime($to_dt));


			//Expect Completion Date
			$date2 = $dStart->createFromFormat('d/m/Y',$expect_complt1);
			$expect_complt=$date2->format('Y-m-d');
			$expect_complt = date('Y-m-d',strtotime($expect_complt));

			$ogrsample1= $this->SampleInward->find('first', array('joins' => array(array('table' => 'workflow','alias' => 'w','type' => 'INNER','conditions' => array('w.org_sample_code = SampleInward.org_sample_code'))),
																  'fields' => array('SampleInward.org_sample_code','SampleInward.org_sample_code'),
																  'conditions'=> array('w.stage_smpl_cd' => $sample_code)));

			$ogrsample	= $ogrsample1['org_sample_code'];

			$this->SamAllocate->query("UPDATE m_sample_allocate SET acptnce_flag='N',
												rec_from_dt='$from_dt',
												rec_to_dt='$to_dt',
												chemist_code='$chemist_code',
												lab_code=$lab_code,
												sample_qnt='$sample_qnt',
												sample_unit=$sample_unit,
												test_n_r='$test_n_r',
												test_n_r_no='$test_n_r_no',
												expect_complt='$expect_complt',
												alloc_cncl_flag='$alloc_cncl_flag',
												org_sample_code='$ogrsample'
									   WHERE sample_code='$sample_code'AND alloc_to_user_code=$allocate_to AND chemist_code='$chemist_code'");


			$this->SamAllocate->query("UPDATE code_decode SET  li_code='-',chemist_code='$chemist_code',lab_code=$lab_code WHERE sample_code='$sample_code'AND alloc_to_user_code=$allocate_to AND chemist_code='$chemist_code'");


			$this->SamAllocate->query("UPDATE actual_test_data SET display='Y',lab_code='$lab_code',org_sample_code='$ogrsample' WHERE sample_code='$sample_code'AND  alloc_to_user_code=$allocate_to AND chemist_code='$chemist_code' ");


			foreach ($test_code as $testcd) {

				$this->SamAllocate->query("INSERT INTO actual_test_data( fin_year,sample_code, alloc_to_user_code,chemist_code, test_code, org_sample_code, user_code) VALUES ('$fin_year','$sample_code','$allocate_to','$chemist_code',$testcd,'$ogrsample',2);") ;
			}

			 echo "1";

		}
		exit;
	}

/********************************************************************************************************************************************************************************************************************************************/

	//GET TEST NR NO
	public function gettest_n_r_no() {

		$this->loadModel('MSampleAllocate');

		$allocate_to=$_POST['alloc_to_user_code'];

		$sample_code=$_POST['sample_code'];

			if ($_POST['test_n_r']=='R') {

				$test_cnt=$this->SamAllocate->query("SELECT max(test_n_r_no) FROM m_sample_allocate WHERE sample_code='$sample_code' AND alloc_to_user_code='$allocate_to' AND test_n_r='R' ");

				$re_test=$_POST['re_test'];

					if ($re_test=='P') {


						$test_n_r_no=$test_cnt['0']['0']['max'];

						if ($test_n_r_no=='') {

							$test_n_r_no=1;

						}

						echo $test_n_r_no; exit;

					} else {

						$test_n_r_no=$test_cnt['0']['0']['max']+1;

						echo $test_n_r_no; exit;

					}

				}

		}

/********************************************************************************************************************************************************************************************************************************************/

	//GET ALLOCATED TESTS
	public function getAllocTest() {

		$this->autoRender=false;
		$conn = ConnectionManager::get('default');

		$this->loadModel('ActualTestData');
		$this->loadModel('SampleInward');
		$sample_code1 = trim($_POST['sample_code']);
		$alloc_by_user_code11 = trim($_POST['alloc_to_user_code']);


		if (!is_numeric($alloc_by_user_code11) || $alloc_by_user_code11=='') {
			echo '#[error]~Invalid alloc_by_user_code#';
			exit;
		}

		if (!is_numeric($sample_code1) || $sample_code1=='') {

			echo '#[error]~Invalid Sample Code#';
			exit;
		}

		$dup = $conn->execute("SELECT result_dupl_flag FROM sample_inward AS si
							   INNER JOIN  workflow AS w ON si.org_sample_code=w.org_sample_code
							   WHERE w.stage_smpl_cd='$sample_code1' ");

		$dup = $dup->fetchAll('assoc');

		$this->set('dup',$dup);

		$res=$dup[0]['result_dupl_flag'];

		if (trim($res)=="D") {

		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//																																																											  //
		//	/*$testalloc	= $this->actual_test_data->find('list', array('joins' => array(array('table' => 'm_test','alias' => 'a','type' => 'INNER','conditions' => array('a.test_code = actual_test_data.test_code')),							  //
		//																					  array('table' => 'sample_inward','alias' => 'si','type' => 'INNER','conditions' => array('actual_test_data.org_sample_code = si.org_sample_code'))),	  //
		//																								'fields' => array('a.test_code','a.test_name'),																								  //
		//																								'conditions'=>array('actual_test_data.sample_code'=>$sample_code1,'actual_test_data.display'=>'Y')));*/                                       //
		//																																																											  //
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

			$query = $conn->execute("SELECT a.test_code,a.test_name
										FROM actual_test_data AS atd
										INNER JOIN m_test AS a ON a.test_code = atd.test_code
										INNER JOIN sample_inward AS si ON atd.org_sample_code = si.org_sample_code
										WHERE atd.sample_code='$sample_code1' AND atd.display='Y'");

		} else {

		 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 //																																																	 //
		 //					/*$testalloc	= $this->actual_test_data->find('list', array('joins' => array(																									 //
		 //					array('table' => 'm_test','alias' => 'a','type' => 'INNER','conditions' => array('a.test_code = actual_test_data.test_code'))),													 //
		 //							'fields' => array('a.test_code','a.test_name'),																															 //
		 //							'conditions'=>array('actual_test_data.sample_code'=>$sample_code1,'actual_test_data.alloc_to_user_code'=>$alloc_by_user_code11 ,'actual_test_data.display'=>'Y')));*/    //
         //																																																	 //
		 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

			$query = $conn->execute("SELECT a.test_code,a.test_name
										FROM actual_test_data AS atd
										INNER JOIN m_test AS a ON a.test_code = atd.test_code
										WHERE atd.sample_code='$sample_code1' AND atd.alloc_to_user_code='$alloc_by_user_code11' AND atd.display='Y'");
		}

		$testalloc = $query->fetchAll('assoc');

		echo '#'.json_encode($testalloc).'#';
		exit;
	}


/********************************************************************************************************************************************************************************************************************************************/

	//GET ALLOCATED TESTS 1
	public function getAllocTest1() {

		$this->autoRender=false;
		$conn = ConnectionManager::get('default');
		$sample_code1 = trim($_POST['sample_code']);

		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	/*$testalloc = $this->actual_test_data->find('list',																										 //
		//		array(joins' => array(array('table' => 'm_test','alias' => 'a','type' => 'INNER','conditions' => array('a.test_code = actual_test_data.test_code')),	 //
		//		array('table' => 'sample_inward','alias' => 'si','type' => 'INNER','conditions' => array('actual_test_data.org_sample_code = si.org_sample_code'))),     //
		//		'fields' => array('a.test_code','a.test_name'),																											 //
		//		'conditions'=>array('actual_test_data.sample_code'=>$sample_code1,'actual_test_data.display'=>'Y','si.result_dupl_flag'=>'D')));*/                	     //
		//																																								 //
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$query = $conn->execute("SELECT a.test_code,a.test_name FROM actual_test_data AS atd
									INNER JOIN m_test AS a ON a.test_code = atd.test_code
									INNER JOIN sample_inward si ON atd.org_sample_code = si.org_sample_code
									WHERE atd.sample_code='$sample_code1' AND atd.display='Y' AND si.result_dupl_flag='D'");

		$testalloc = $query->fetchAll('assoc');

		echo '#'.json_encode($testalloc).'#';
		exit;
	}

/********************************************************************************************************************************************************************************************************************************************/

	//GET USER DETAIL
	public function getuserdetail() {

		$this->autoRender=false;
		$conn = ConnectionManager::get('default');
		$sample_code1=$_POST['sample_code'];
		$alloc_by_user_code=$_POST['alloc_to_user_code'];
		$data = $conn->execute("SELECT * FROM workflow WHERE dst_usr_cd=$alloc_by_user_code AND org_sample_code IN(SELECT org_sample_code FROM workflow WHERE stage_smpl_cd='$sample_code1' ) AND stage_smpl_flag IN('OF','IF','AS')");
		$data = $data->fetchAll('assoc');
		echo '#'.count($data).'#';
		exit;
	}

/********************************************************************************************************************************************************************************************************************************************/

	//GET FLAG BY AJAX
	public function getFlag(){

		$this->autoRender=false;
		$conn = ConnectionManager::get('default');
		$sample_code1=$_POST['sample_code'];

		$flg = $conn->execute("SELECT CASE WHEN result_dupl_flag='S' THEN 'Single Analysis' ELSE 'Duplicate Analysis' END AS flg1
							   FROM sample_inward AS si
							   INNER JOIN  workflow AS w ON si.org_sample_code = w.org_sample_code
							   WHERE w.stage_smpl_cd='$sample_code1' AND  stage_smpl_flag IN('AS','IF')  ");

		$flg = $flg->fetchAll('assoc');

		echo '#'.json_encode($flg).'#';
		exit;

	}

/********************************************************************************************************************************************************************************************************************************************/

	//GET USER DETAILS NEW BY AJAX
	public function getuserdetailNew() {

		$this->autoRender=false;
		$conn =ConnectionManager::get('default');
		$sample_code1 = $_POST['sample_code'];

		$data = $conn->execute("SELECT * FROM workflow
								INNER JOIN sample_inward AS si ON si.org_sample_code=workflow.org_sample_code
								WHERE si.status_flag='IF'AND workflow.org_sample_code IN(SELECT org_sample_code FROM workflow WHERE stage_smpl_cd='$sample_code1') AND stage_smpl_flag IN('OF','IF')");

		$data = $data->fetchAll('assoc');
		echo '#'.count($data).'#';
		exit;
	}


/********************************************************************************************************************************************************************************************************************************************/

	//GET TEST BY COMMODITY ID BY AJAX
	public function getTestByCommodityId() {

		$this->autoRender = false;
		$conn = ConnectionManager::get('default');
		$this->loadModel('ActualTestData');
		$this->loadModel('CommodityTest');
		$commodity_code=$_POST['commodity_code'];
		$sample_code1=trim($_POST['sample_code']);
		$alloc_by_user_code11=$_POST['alloc_to_user_code'];

		if (!is_numeric($sample_code1) || $sample_code1=='' ){

			echo '#[error]~Invalid Sample Code#';
			exit;
		}
		if (!is_numeric($commodity_code)) {

			echo '#[error]~Invalid Commodity Code#';
			exit;
		}

		if (!is_numeric($alloc_by_user_code11) || $alloc_by_user_code11=='') {

			echo '#[error]~Invalid User Code#';
			exit;
		}

		$query = $conn->execute("SELECT a.test_code,a.test_name
								 FROM actual_test_data AS atd
								 INNER JOIN m_test AS a ON a.test_code = atd.test_code
								 INNER JOIN sample_inward AS si ON atd.org_sample_code = si.org_sample_code
								 WHERE atd.sample_code='$sample_code1'AND atd.display='Y'AND si.result_dupl_flag='D'
								 GROUP BY a.test_code,a.test_name");

		$testalloc2 = $query->fetchAll('assoc');

		$testalloc = $this->ActualTestData->find('list', array('keyField'=>'test_code','valueField'=>'test_code','conditions'=>array('sample_code IS'=>$sample_code1,'alloc_to_user_code IS'=>$alloc_by_user_code11 ,'display'=>'Y')))->toList();

		if ((count($testalloc)!=0)) {

			if (count($testalloc2)>0) {

				$category="";

			} else {

				//To Be Used In Below Core Query Format
				$arr = "NOT IN(";
				foreach($testalloc as $key => $value){
					$arr .= "'";
					$arr .= $value;
					$arr .= "',";
				}
				$arr .= "'00')";//00 is intensionally given to put last value in string.

				$category = $conn->execute("SELECT a.test_code,a.test_name
											FROM commodity_test AS ct
											INNER JOIN m_test AS a ON a.test_code = ct.test_code
											INNER JOIN test_formula AS tf ON a.test_code = tf.test_code
											WHERE ct.commodity_code='$commodity_code' AND a.test_code ".$arr." GROUP BY a.test_code,a.test_name");
			}

		} else {

			if (count($testalloc2)>0) {

				$category="";

			} else {

				$category = $conn->execute("SELECT a.test_code,a.test_name
											FROM commodity_test AS ct
											INNER JOIN m_test AS a ON a.test_code = ct.test_code
											INNER JOIN test_formula AS tf ON a.test_code = tf.test_code
											WHERE ct.commodity_code='$commodity_code'
											GROUP BY a.test_code,a.test_name");
			}

		}

		$category = $category->fetchAll('assoc');

		echo '#'.json_encode($category).'#';
		exit;
	}

/********************************************************************************************************************************************************************************************************************************************/

	//Check Commodity Grading Before Allocated The Test To Any Chemist
	public function checkCommodityGradingForTest(){

		$this->autoRender=false;
		$commodity_code_id = trim($_POST['commodity_code_id']);
		$test_code_id = trim($_POST['test_code_id']);

		if (!is_numeric($commodity_code_id) || $commodity_code_id=='') {

			echo '#[error]~Invalid commodity code#';
			exit;
		}

		if (!is_numeric($test_code_id) || $test_code_id=='') {

			echo '#[error]~Invalid test code#';
			exit;
		}

		$this->loadModel('CommGrade');

		$result = $this->CommGrade->find('all',array('conditions'=>array('commodity_code IS'=>$commodity_code_id,'test_code IS'=>$test_code_id)))->first();

		if (empty($result)) {

			echo '#0#';

		} else {

			echo '#1#';
		}

		exit;

	}

/********************************************************************************************************************************************************************************************************************************************/

	//To List Sample Code Ready To Allocate
	public function readyToAllocateSamplesList() {

		$conn = ConnectionManager::get('default');
		$user_cd=$this->Session->read('user_code');
		$this->loadModel('SampleInward');

		if ($_SESSION["role"]=="Lab Incharge") {

			$str = "SELECT w.stage_smpl_cd,w.stage_smpl_cd
					FROM sample_inward AS si
					INNER JOIN workflow AS w ON si.org_sample_code=w.org_sample_code AND w.stage_smpl_flag IN('SD','AS','IF','HF') AND w.dst_loc_id=".$_SESSION['posted_ro_office']." AND w.dst_usr_cd=".$_SESSION['user_code']." AND acceptstatus_flag='A' AND si.status_flag NOT IN('AR','FR','SR','G') AND si.status_flag in('IF','LA','T') AND  w.stage_smpl_flag!='R'
					GROUP BY si.stage_sample_code,w.stage_smpl_cd
					ORDER BY si.stage_sample_code asc";

		} else {

			$str = "SELECT w.stage_smpl_cd,w.stage_smpl_cd
					FROM sample_inward as si
					INNER JOIN workflow as w on si.org_sample_code=w.org_sample_code AND w.stage_smpl_flag In ('AS','IF','HF') AND w.dst_loc_id=".$_SESSION['posted_ro_office']." AND w.dst_usr_cd=".$_SESSION['user_code']." AND acceptstatus_flag='A' AND si.status_flag NOT IN('AR','FR','SR','IF','LA','G') AND w.stage_smpl_flag!='R,	'
					GROUP BY si.stage_sample_code,w.stage_smpl_cd
					ORDER BY si.stage_sample_code asc";
		}

			$query = $conn->execute($str);
			$stage_sample_code_result = $query->fetchAll('assoc');

			//Conditions to Check Wheather Stage Sample Code is Final Graded or not.
			$stage_sample_code_list = array();

			if (!empty($stage_sample_code_result)) {

				$this->loadModel('Workflow');


				foreach ($stage_sample_code_result as $stage_sample_code) {

					$final_grading = $this->Workflow->find('all',array('conditions'=>array('stage_smpl_flag IN'=>array('FG','FGIO'),'stage_smpl_cd IS'=>$stage_sample_code['stage_smpl_cd'])))->first();

					if (empty($final_grading)) {

						//Condtions To Check Wheather Stage Sample Code Is Sent For Mark For Already Test BY Akash 08/07/2021.
						$get_original_sample_code = $this->Workflow->find()->select(['org_sample_code'])->where(['stage_smpl_cd IS' => $stage_sample_code['stage_smpl_cd']])->first();

						$already_sent_for_test = $this->Workflow->find('all',array('conditions'=>array('stage_smpl_flag'=>'TA','org_sample_code IS'=>$get_original_sample_code['org_sample_code'])))->first();

						//Condtions To Check Wheather Stage Sample Code Is Mark For Retest or Not.
						$sample_for_retest = $this->Workflow->find('all',array('conditions'=>array('stage_smpl_flag'=>'R','stage_smpl_cd IS'=>$stage_sample_code['stage_smpl_cd'])))->first();

						//check if sample is for single or duplicate analysis
						//added below query and new condition on 30-05-2022 by Amol, to show Duplicate analysis sample for allocation to multiple chemists
						$get_analysis_flg = $this->SampleInward->find('all',array('fields'=>'result_dupl_flag','conditions'=>array('org_sample_code'=>$get_original_sample_code['org_sample_code'])))->first();
						
						if ((empty($sample_for_retest) && empty($already_sent_for_test)) || trim($get_analysis_flg['result_dupl_flag'])=='D') {
							$stage_sample_code_list[]= $stage_sample_code['stage_smpl_cd'];
						}
					}
				}
			}

		return $stage_sample_code_list;
	}

/********************************************************************************************************************************************************************************************************************************************/

	//To List Sample Code Ready To Forward
	public function readyToForwardSamplesList() {

		$conn = ConnectionManager::get('default');
		$user_cd=$this->Session->read('user_code');
		$this->loadModel('SampleInward');

		if ($_SESSION["role"]=="Lab Incharge") {
			$str = "SELECT w.stage_smpl_cd,w.stage_smpl_cd FROM sample_inward AS si INNER JOIN workflow AS w ON si.org_sample_code=w.org_sample_code AND w.stage_smpl_flag!='R' AND w.stage_smpl_flag !='IF' AND w.stage_smpl_flag IN('SD','AS') AND si.status_flag NOT IN('IF','A','T','SR','LA','G') AND acceptstatus_flag='A' AND w.dst_loc_id=".$_SESSION['posted_ro_office']." AND w.dst_usr_cd=".$_SESSION['user_code']." AND si.org_sample_code NOT IN(SELECT ftr.org_sample_code FROM final_test_result AS ftr INNER JOIN sample_inward AS si ON si.org_sample_code=ftr.org_sample_code) GROUP BY si.stage_sample_code,w.stage_smpl_cd ORDER BY si.stage_sample_code ASC";
		} else {
			$str = "SELECT w.stage_smpl_cd,w.stage_smpl_cd FROM sample_inward AS si INNER JOIN workflow AS w ON si.org_sample_code=w.org_sample_code AND w.stage_smpl_flag!='R'AND w.stage_smpl_flag !='IF'AND w.stage_smpl_flag IN('SD','AS') AND si.status_flag NOT IN('IF','A','T','SR','LA','G') AND acceptstatus_flag='A' AND w.dst_loc_id=".$_SESSION['posted_ro_office']." AND w.dst_usr_cd=".$_SESSION['user_code']." AND si.org_sample_code NOT IN(SELECT ftr.org_sample_code FROM final_test_result AS ftr INNER JOIN sample_inward AS si ON si.org_sample_code=ftr.org_sample_code) GROUP BY si.stage_sample_code,w.stage_smpl_cd ORDER BY si.stage_sample_code ASC";
		}

		$query = $conn->execute($str);
		$stage_sample_code_result = $query->fetchAll('assoc');

		//Conditions To Check Wheather Stage Sample Code is Final Graded or Not.
		$stage_sample_code_list = array();

		if (!empty($stage_sample_code_result)) {

			$this->loadModel('Workflow');

			foreach ($stage_sample_code_result as $stage_sample_code) {

				$final_grading = $this->Workflow->find('all',array('conditions'=>array('stage_smpl_flag IN'=>array('FG','FGIO'),'stage_smpl_cd IS'=>$stage_sample_code['stage_smpl_cd'])))->first();

				if (empty($final_grading)) {

					//Conditions To Check Wheather Stage Sample Code is Mark For Retest or Not.
					$sample_for_retest = $this->Workflow->find('all',array('conditions'=>array('stage_smpl_flag'=>'R','stage_smpl_cd IS'=>$stage_sample_code['stage_smpl_cd'])))->first();

					if (empty($sample_for_retest)) {

						$stage_sample_code_list[]= $stage_sample_code['stage_smpl_cd'];
					}
				}
			}
		}

		return $stage_sample_code_list;

	}

/********************************************************************************************************************************************************************************************************************************************/

	//To List Of Sample Available Samples To Allocate For Test Or Forward To Lab Incharge
	public function availableToAllocate(){
		
		//by default
		$to_dt = date('Y-m-d');
		$from_dt = date('Y-m-d',strtotime('-1 month'));
		
		if ($this->request->is('post')) {

			//on search
			$to_dt = 	$this->request->getData('to_dt');
			$from_dt = $this->request->getData('from_dt');

			if (empty($from_dt) || empty($to_dt)) {

				echo "<script>alert('Please Select Proper Dates');</script>";
			}
			$this->set(compact('to_dt','from_dt'));
		}

		if (!empty($from_dt) || !empty($to_dt)) {

			//to be allocate to chemist
			$avail_to_allocate = $this->getSampleToAllocate($from_dt,$to_dt);
			$this->set('avail_to_allocate',$avail_to_allocate);

			//to be forwarded to Lab Incharge
			$avail_to_forward = $this->getSampleToForwardInchrg($from_dt,$to_dt);
			$this->set('avail_to_forward',$avail_to_forward);

			/*get the list of send back sample by chemist,*/
			$sendback = $this->getSampleReturnedByChemist($from_dt,$to_dt);
			$this->set('sendback',$sendback);
			
		}

	}

/********************************************************************************************************************************************************************************************************************************************/


	//created common function to fetch list , to be used for dashboard counts also, on 28-04-2021 by Amol
	public function getSampleToAllocate($from_dt=null,$to_dt=null){

		$conn = ConnectionManager::get('default');
		$user_cd=$this->Session->read('user_code');

		//to list sample for allocation
		$sample_list_to_allocate = $this->readyToAllocateSamplesList();

		//set array format
		$cus_string= '';
		foreach($sample_list_to_allocate as $each){

			$cus_string .= $each."','";
		}
		if(!empty($from_dt) && !empty($to_dt)){
			$dateCondition = " AND date(si.created) >= '$from_dt' AND date(si.created) <= '$to_dt'";
		}else{
			$dateCondition = "";
		}

		//to get extra parameters for listing
		//added new condition of stage sample code above on 07-05-2021 by Akash to distinct records
		$query = $conn->execute("SELECT DISTINCT ON (w.stage_smpl_cd) si.inward_id,w.stage_smpl_cd,si.received_date,st.sample_type_desc,mcc.category_name,mc.commodity_name,ml.ro_office,w.modified AS accepted_on
								 FROM sample_inward AS si
								 INNER JOIN m_sample_type AS st ON si.sample_type_code=st.sample_type_code
								 INNER JOIN m_commodity_category AS mcc ON si.category_code=mcc.category_code
								 INNER JOIN dmi_ro_offices AS ml ON ml.id=si.loc_id
								 INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
								 INNER JOIN workflow AS w ON w.org_sample_code = si.org_sample_code
								 WHERE w.stage_smpl_cd IN ('$cus_string')".$dateCondition);

		$avail_to_allocate = $query ->fetchAll('assoc');
		return $avail_to_allocate;
	}


/********************************************************************************************************************************************************************************************************************************************/


	//created common function to fetch list , to be used for dashboard counts also, on 28-04-2021 by Amol
	public function getSampleToForwardInchrg($from_dt=null,$to_dt=null){

		$conn = ConnectionManager::get('default');

		//to list samples for forwarding to lab incharge
		$sample_list_to_forward = $this->readyToForwardSamplesList();

		//set array format
		$cus_string= '';
		foreach($sample_list_to_forward as $each){

			$cus_string .= $each."','";
		}
		if(!empty($from_dt) && !empty($to_dt)){
			$dateCondition = " AND date(si.created) >= '$from_dt' AND date(si.created) <= '$to_dt'";
		}else{
			$dateCondition = "";
		}

		//to get extra parameters for listing
		$query = $conn->execute("SELECT DISTINCT ON (w.stage_smpl_cd) si.inward_id,w.stage_smpl_cd,si.received_date,st.sample_type_desc,mcc.category_name,mc.commodity_name,ml.ro_office,w.modified AS accepted_on
								 FROM sample_inward AS si
								 INNER JOIN m_sample_type AS st ON si.sample_type_code=st.sample_type_code
								 INNER JOIN m_commodity_category AS mcc ON si.category_code=mcc.category_code
								 INNER JOIN dmi_ro_offices AS ml ON ml.id=si.loc_id
								 INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
								 INNER JOIN workflow AS w ON w.org_sample_code = si.org_sample_code
								 WHERE w.stage_smpl_cd IN ('$cus_string')".$dateCondition);

		$avail_to_forward = $query ->fetchAll('assoc');

		return $avail_to_forward;
	}


/********************************************************************************************************************************************************************************************************************************************/

	//created common function to fetch list , to be used for dashboard counts also, on 28-04-2021 by Amol
	public function getSampleReturnedByChemist($from_dt=null,$to_dt=null){

		$conn = ConnectionManager::get('default');
		$send_back_applications = array();
		$this->loadModel('Workflow');
		if(!empty($from_dt) && !empty($to_dt)){
			$dateCondition = " AND date(a.created) >= '$from_dt' AND date(a.created) <= '$to_dt'";
		}else{
			$dateCondition = "";
		}

		$sendback = $conn->execute("SELECT  DISTINCT ON (s.sample_code) s.sample_code,b.commodity_name,s.chemist_code,s.sendback_remark,s.recby_ch_date,s.org_sample_code
									FROM sample_inward AS a
									INNER JOIN m_sample_allocate AS s ON a.org_sample_code=s.org_sample_code
									INNER JOIN workflow AS wf ON wf.org_sample_code=a.org_sample_code
									INNER JOIN m_commodity AS b ON a.commodity_code=b.commodity_code
									WHERE s.acptnce_flag='NABC'AND a.status_flag!='SR'AND s.user_code='".$_SESSION['user_code']."' ".$dateCondition)->fetchAll('assoc');


		if (count($sendback)>0) {

			foreach ($sendback as $each_reord) {

				$final_granted_sample = $this->Workflow->find('all',array('conditions'=>array('stage_smpl_flag'=>'FG','org_sample_code'=>$each_reord['org_sample_code'])))->first();

				if (empty($final_granted_sample)) {

					$sample_for_retest = $this->Workflow->find('all',array('conditions'=>array('stage_smpl_flag'=>'R','org_sample_code IS'=>$each_reord['org_sample_code'])))->first();

					if (empty($sample_for_retest)) {
						$send_back_applications[] = $each_reord;
					}
				}
			}

		}
		return $send_back_applications;

	}


/********************************************************************************************************************************************************************************************************************************************/

	//to show the list of allocated/forwarded samples
	public function allocatedList(){

		$conn = ConnectionManager::get('default');
		$user_cd=$this->Session->read('user_code');

		//by default
		$to_dt = date('Y-m-d');
		$from_dt = date('Y-m-d',strtotime('-1 month'));

		if ($this->request->is('post')) {

			//on search
			$to_dt = 	$this->request->getData('to_dt');
			$from_dt = $this->request->getData('from_dt');


			if (empty($from_dt) || empty($to_dt)) {

				echo "<script>alert('Please Select Proper Dates');</script>";
			}
			$this->set(compact('to_dt','from_dt'));
		}

		if (!empty($from_dt) || !empty($to_dt)) {

			// add new join condition to show allocated chemist name,
			$allRes = $conn->execute("SELECT a.sample_code,cd.chemist_code,alloc_date,u.f_name || ' ' || u.l_name AS f_name,a.alloc_to_user_code,cun.f_name || ' ' || cun.l_name AS cun_f_name
									  FROM actual_test_data AS a
									  INNER JOIN code_decode AS cd ON cd.chemist_code=a.chemist_code AND a.alloc_to_user_code=cd.alloc_to_user_code AND cd.display='Y' AND a.sample_code=cd.sample_code
									  INNER JOIN m_sample_allocate AS sa ON sa.chemist_code=cd.chemist_code AND sa.alloc_to_user_code=cd.alloc_to_user_code AND sa.display='Y' AND sa.sample_code=cd.sample_code AND sa.test_n_r='N'
									  INNER JOIN dmi_users AS cun ON cun.id=sa.alloc_to_user_code
									  INNER JOIN dmi_users AS u ON u.id=sa.user_code AND u.role='".$_SESSION['role']."' AND a.display='Y' AND cd.chemist_code!='-' AND cd.user_code='".$_SESSION['user_code']."' AND date(a.created) >= '$from_dt' AND date(a.created) <= '$to_dt' GROUP BY a.sample_code,u.f_name,cun.f_name,cd.chemist_code,sa.test_n_r,sa.alloc_date,u.l_name,cun.l_name,a.alloc_to_user_code ORDER BY sa.alloc_date DESC");

		$allRes = $allRes->fetchAll('assoc');

		$this->set('allRes',$allRes);

		$allres3=array();

		foreach ($allRes as $allRes2) {

			$user_code_new=$allRes2['alloc_to_user_code'];
			$sample_code_new=$allRes2['sample_code'];
			$chemist_code=$allRes2['chemist_code'];

			$allRes1 = $conn->execute("SELECT a.chemist_code,b.test_name
									   FROM actual_test_data AS a
									   INNER JOIN code_decode AS cd ON cd.chemist_code=a.chemist_code AND a.alloc_to_user_code=cd.alloc_to_user_code AND cd.display='Y' AND a.sample_code=cd.sample_code
									   INNER JOIN m_sample_allocate AS sa ON sa.chemist_code=cd.chemist_code AND sa.alloc_to_user_code=cd.alloc_to_user_code AND sa.display='Y' AND sa.sample_code=cd.sample_code
									   INNER JOIN m_test AS b ON a.test_code=b.test_code
									   INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code AND  a.display='Y' AND cd.chemist_code!='-' AND a.alloc_to_user_code='$user_code_new' AND a.chemist_code='$chemist_code'
									   GROUP BY a.chemist_code,b.test_name");

			$allRes1 = $allRes1->fetchAll('assoc');

			foreach ($allRes1 as $res2){

				array_push($allres3,$res2);
			}
		}

		$this->set('allRes1',$allres3);

			//To Get Forwarded Lab In-charge Samples List
			$res = $conn->execute("SELECT DISTINCT w.stage_smpl_cd,u.f_name || ' ' || u.l_name AS f_name,u.role
									FROM workflow AS w
									INNER JOIN dmi_users AS u ON w.dst_usr_cd=u.id AND w.stage_smpl_flag='IF'AND u.role='Lab Incharge'AND w.src_usr_cd ='".$_SESSION['user_code']."'GROUP BY u.f_name,u.l_name,w.stage_smpl_cd,u.role");

			$res = $res->fetchAll('assoc');

			$this->set('res',$res);
		}
	}


/********************************************************************************************************************************************************************************************************************************************/

	//to redirect on sample slip window
	public function redirectToSampleSlip($sample_slip_cd){

		$this->Session->write('sample_slip_cd',$sample_slip_cd);
		$this->redirect(array('controller'=>'SampleAllocate','action'=>'sample_slip'));
	}

/********************************************************************************************************************************************************************************************************************************************/

/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>[Sample Slip]>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

	//to generate sample slip pdf
	public function sampleSlip(){

		$this->authenticateUser();
		$this->viewBuilder()->setLayout('admin_dashboard');

		$conn = ConnectionManager::get('default');

		$sample_slip_cd = $this->Session->read('sample_slip_cd');

		if(!empty($sample_slip_cd)){

			$user_type = $conn->execute("SELECT DISTINCT role_code,role FROM dmi_users AS u
											INNER JOIN user_role AS r ON r.role_name=u.role
											WHERE u.role IN('Jr Chemist','Sr Chemist','Cheif Chemist','Lab Incharge') AND u.status != 'disactive'
											ORDER BY role ASC");

			$user_type = $user_type->fetchAll('assoc');

			$this->set('user_type',$user_type);

			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//																																																    //
			//  /*$sample = $conn->execute("select sa.sample_code,sa.sample_code from sample_inward as si 																									    //
			//		INNER JOIN m_sample_allocate as sa on si.org_sample_code=sa.org_sample_code																												    //
			//		INNER JOIN workflow as w on si.org_sample_code=w.org_sample_code and sa.alloc_cncl_flag='N' and w.dst_loc_id=".$_SESSION["posted_ro_office"]." and  w.dst_usr_cd=".$_SESSION["user_code"]."	//
			//		GROUP BY si.stage_sample_code,sa.sample_code ORDER BY si.stage_sample_code asc");*/																										    //
			//																																																    //
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

			$this->set('sample',array($sample_slip_cd=>$sample_slip_cd));

			if ($this->request->is('post')){

        //print_r($this->request->getData()); exit;

				$this->viewBuilder()->setLayout('pdf_layout');

				if (array_key_exists('alloc_to_user_code',$_POST) && null !==($_POST['alloc_to_user_code'])) {

					$postdata = $this->request->getData();
					$alloc_to_user_code = explode("~", $postdata['alloc_to_user_code']);

				} else {

					$alloc_to_user_code="";
				}

				if (null !==($_POST['sample_code'])) {

					$chemist_code = $_POST['sample_code'];

				} else {

					$chemist_code="";
				}

				if (null !==($_POST['sample'])) {

					$sample = $_POST['sample'];

				} else {

					$sample="";
				}

					$testalloc1 = $conn->execute("SELECT cd.chemist_code AS chemist_code,b.commodity_name,c.f_name,c.l_name,a.received_date
													FROM sample_inward AS a
													INNER JOIN m_sample_allocate AS s ON a.org_sample_code=s.org_sample_code
													INNER JOIN code_decode AS cd ON a.org_sample_code=cd.org_sample_code
													INNER JOIN m_commodity AS b ON a.commodity_code=b.commodity_code
													INNER JOIN dmi_users AS c ON a.user_code=c.id
													WHERE s.alloc_cncl_flag='N'AND s.sample_code='$sample' AND cd.chemist_code='$chemist_code' AND a.display='Y' AND s.display='Y'
													GROUP BY cd.chemist_code,b.commodity_name,c.f_name,c.l_name,a.received_date");


				if ($testalloc1) {

					$testalloc = $conn->execute("SELECT cd.chemist_code AS chemist_code,b.commodity_name,c.f_name,c.l_name,a.received_date
													FROM sample_inward AS a
													INNER JOIN m_sample_allocate AS s ON a.org_sample_code=s.org_sample_code
													INNER JOIN code_decode AS cd ON a.org_sample_code=cd.org_sample_code
													INNER JOIN m_commodity AS b ON a.commodity_code=b.commodity_code
													INNER JOIN dmi_users AS c ON cd.alloc_to_user_code=c.id
													WHERE s.alloc_cncl_flag='N' AND s.sample_code='$sample' AND cd.chemist_code='$chemist_code' AND a.display='Y' AND s.display='Y'
													GROUP BY cd.chemist_code,b.commodity_name,c.f_name,c.l_name,a.received_date");

					$testalloc = $testalloc->fetchAll('assoc');

					$subTestResult = $conn->execute("SELECT mt.test_name
														FROM sample_inward AS a
														INNER JOIN m_sample_allocate AS s ON a.org_sample_code=s.org_sample_code
														INNER JOIN actual_test_data AS atd ON a.org_sample_code=atd.org_sample_code
														INNER JOIN m_test mt ON mt.test_code = atd.test_code
														WHERE s.alloc_cncl_flag='N' AND s.sample_code='$sample' AND atd.chemist_code='$chemist_code' AND a.display='Y' AND s.display='Y'
														GROUP BY mt.test_name");

					$subTestResult = $subTestResult->fetchAll('assoc');

				} else {

					$testalloc = $conn->execute("SELECT cd.li_code AS chemist_code,b.commodity_name,c.f_name,c.l_name,a.received_date
													FROM sample_inward AS a
													INNER JOIN m_sample_allocate AS s ON a.org_sample_code=s.org_sample_code
													INNER JOIN code_decode AS cd ON a.org_sample_code=cd.org_sample_code
													INNER JOIN m_commodity AS b ON a.commodity_code=b.commodity_code
													INNER JOIN dmi_users AS c ON cd.alloc_to_user_code=c.id
													WHERE s.alloc_cncl_flag='N' AND s.sample_code='$sample' AND cd.li_code='$chemist_code' AND a.display='Y' AND s.display='Y'
													GROUP BY cd.li_code,cd.chemist_code,b.commodity_name,c.f_name,c.l_name,a.received_date");

					$testalloc = $testalloc->fetchAll('assoc');

					$subTestResult = $conn->execute("SELECT mt.test_name
														FROM sample_inward AS a
														INNER JOIN m_sample_allocate AS s ON a.org_sample_code=s.org_sample_code
														INNER JOIN actual_test_data AS atd ON a.org_sample_code=atd.org_sample_code
														INNER JOIN m_test mt ON mt.test_code = atd.test_code
														WHERE s.alloc_cncl_flag='N' AND s.sample_code='$sample' AND atd.chemist_code='$chemist_code' AND a.display='Y' AND s.display='Y'
														GROUP BY mt.test_name");

					$subTestResult = $subTestResult->fetchAll('assoc');
				}

				if(count($testalloc)>0){

					$testName	= '';

					for($i=0;$i<count($subTestResult);$i++){
						$testName	= $testName.$subTestResult[$i]['test_name'].",";
					}

					$testName = (substr($testName,-1) == ',') ? substr($testName, 0, -1) : $testName;

					$testalloc[0]['tests']	= $testName;

					$this->set('testalloc',$testalloc);
				}


				//call to the pdf creaation common method
				$this->callTcpdf($this->render(),'I');

			}

		}


	}

/********************************************************************************************************************************************************************************************************************************************/

	//to get chemist/division sample code list while creating sample slip
	public function getSample(){

		$this->autoRender = false;
		$conn = ConnectionManager::get('default');

		 $sample = trim($_POST['sample']);

		$str="SELECT c.chemist_code AS chemist_code
				FROM code_decode AS c
				INNER JOIN m_sample_allocate AS s ON s.chemist_code=c.chemist_code
				WHERE s.sample_code='$sample' AND  c.display='Y'
				GROUP BY c.chemist_code";

		$sample_code1 = $conn->execute($str);
		$sample_code1 = $sample_code1->fetchAll('assoc');

		if($sample_code1[0]['chemist_code']=='-'){

			$str1="SELECT c.li_code AS chemist_code
					 FROM code_decode AS c
					 INNER JOIN m_sample_allocate AS s ON s.chemist_code=c.chemist_code
					 WHERE c.sample_code='$sample' AND  s.alloc_cncl_flag='N' AND c.display='Y'
					 GROUP BY c.li_code";
			$sample_code = $conn->execute($str1);
		}
		else{

			$str="SELECT c.chemist_code AS chemist_code
				   FROM code_decode AS c
					INNER JOIN m_sample_allocate AS s ON s.chemist_code=c.chemist_code
					WHERE c.sample_code='$sample' AND s.alloc_cncl_flag='N' AND  c.display='Y'
					GROUP BY c.chemist_code";
			$sample_code = $conn->execute($str);

		}

		$sample_code = $conn->execute($str);

		$sample_code = $sample_code->fetchAll('assoc');

		echo '~'.json_encode($sample_code).'~';

		exit;
	}


/********************************************************************************************************************************************************************************************************************************************/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>[Allocate Post Validations]>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

	//function to take post data and validate each field.
	public function allocatePostValidations($postData){

		$validation_status = '';

		if($postData["type"]=='A'){//only in allocation window

			if($postData["user_type"]=="Lab Incharge" || $postData["user_type"]=="Jr Chemist" || $postData["user_type"]=="Sr Chemist" || $postData["user_type"]=="Cheif Chemist"){

			}else{
				$validation_status = 'Invalid User Type';
			}

			$patternb = '/^[0-9 ,]+$/';
			$rttttttv = preg_match($patternb,$postData['tests']);
			if(empty($postData["tests"]) || $postData["tests"]!=''){
				if ($rttttttv==0){
					$validation_status = 'Invalid Test code';
				}
			}

			$patternb = '/^[0-9]+$/';
			$rttttttv = preg_match($patternb,$postData['sample_qnt']);
			if($postData["sample_qnt"]!=''){
					if ($rttttttv==0){
					$validation_status = 'Invalid Quantity';
				}
			}

			if($postData["alloc_cncl_flag"]=='N' || $postData["alloc_cncl_flag"]=='C'){

			}else{
				$validation_status = 'Invalid Alloc. cncl flag';
			}

			$patternb1='/^([0-9]{1,2})\\/([0-9]{1,2})\\/([0-9]{4})$/';
			$rttttttv=preg_match($patternb1,$postData["rec_from_dt"]);
			if ($rttttttv==0){
				$validation_status = 'Invalid rec from date';
			}

			$rttttttv2=preg_match($patternb1,$postData["rec_to_dt"]);
			if ($rttttttv2==0){
				$validation_status = 'Invalid rec to date';
			}

			$rttttttv1=preg_match($patternb1,$postData["expect_complt"]);
			if (empty($postData["expect_complt"]) || $rttttttv1==0){
				$validation_status = 'Invalid expected date';
			}

			$patternb		= '/^[0-9]+$/';
			$rttttttv		= preg_match($patternb,$postData['test_n_r_no']);
			if($postData["test_n_r_no"]!=''){
				if ($rttttttv==0){
					$validation_status = 'Invalid test n r no.';
				}
			}

		}elseif($postData["type"]=='F'){//only when forwarding

			if($postData["user_type"]=="Lab Incharge"){

			}else{
				$validation_status = 'Invalid User Type';
			}


		}else{

			$validation_status = 'Invalid Type';
		}

		//other common fields

		$rttttttv = preg_match('/^[0-9]+$/',$postData['user_code']);
		if($postData["user_code"]!=''){
			if ($rttttttv==0){
				$validation_status = 'Invalid login user';
			}
		}

		$rttttttv		= preg_match('/^[0-9]+$/',$postData['category_code']);
		if($postData["category_code"]!=''){
			if ($rttttttv==0){
				$validation_status = 'Invalid Category';
			}
		}

		$rttttttv		= preg_match('/^[0-9]+$/',$postData['alloc_to_user_code']);
		if($postData["alloc_to_user_code"]!=''){
			if ($rttttttv==0){
				$validation_status = 'Invalid User Selected';
			}
		}

		$patternb		= '/^[0-9]+$/';
		$rttttttv		= preg_match($patternb,$postData['alloc_by_user_code']);
		if($postData["alloc_by_user_code"]!=''){
			if ($rttttttv==0){
				$validation_status = 'Invalid Allocate by user';
			}
		}

		$patternb		= '/^[0-9 -]+$/';
		$rttttttv		= preg_match($patternb,$postData['fin_year']);
		if($postData["fin_year"]!=''){
			if ($rttttttv==0){
				$validation_status = 'Invalid Financial year';
			}
		}

		$patternb		= '/^[0-9]+$/';
		$rttttttv		= preg_match($patternb,$postData['stage_sample_code']);
		if($postData["stage_sample_code"]!=''){
			if ($rttttttv==0){
				$validation_status = 'Invalid Sample code';
			}
		}

		$patternb		= '/^[0-9]+$/';
		$rttttttv		= preg_match($patternb,$postData['commodity_code']);
		if($postData["commodity_code"]!=''){
			if ($rttttttv==0){
				$validation_status = 'Invalid Commodity';
			}
		}


		$conn = ConnectionManager::get('default');
		/* if user_type is lab incharge then change the conditions for user list in*/
		if($postData["user_type"]=="Lab Incharge"){
			$users_name	= $conn->execute("SELECT role,posted_ro_office FROM dmi_users WHERE status != 'disactive' AND id='".$postData['alloc_to_user_code']."'");
		}else{
			$users_name	= $conn->execute("SELECT role,posted_ro_office FROM dmi_users WHERE status != 'disactive' AND posted_ro_office='".$_SESSION['posted_ro_office']."' AND id='".$postData['alloc_to_user_code']."'");
		}

		$users_name = $users_name->fetchAll('assoc');

		if(empty($users_name))
		{
			$validation_status = 'Invalid Allocated user';
		}

		return $validation_status;

	}

/********************************************************************************************************************************************************************************************************************************************/

	//below code is for allocation for retest
	public function gettestNRNo(){

		$this->loadModel('MSampleAllocate');
		$allocate_to=$_POST['alloc_to_user_code'];
		$sample_code=$_POST['sample_code'];
		$conn = ConnectionManager::get('default');

		if($_POST['test_n_r']=='R') {

			$test_cnt = $conn->execute("SELECT max(test_n_r_no) FROM m_sample_allocate WHERE sample_code='$sample_code' AND alloc_to_user_code='$allocate_to' AND test_n_r='R' ");

			$test_cnt = $test_cnt->fetchAll('assoc');

			$re_test=$_POST['re_test'];

			if ($re_test=='P') {

				$test_n_r_no=$test_cnt['0']['max'];

				if($test_n_r_no=='') {

					$test_n_r_no=1;
				}

				echo '#'.$test_n_r_no.'#'; exit;
			}
			else{

				$test_n_r_no=$test_cnt['0']['max']+1;
				echo '#'.$test_n_r_no.'#'; exit;
			}
		}

	 }

/********************************************************************************************************************************************************************************************************************************************/

	//to list sample code ready to allocate to chemist
	public function readyToAllocateRetestSamplesList(){

		$conn = ConnectionManager::get('default');
		$user_cd=$this->Session->read('user_code');
		$this->loadModel('SampleInward');

		if ($_SESSION["role"]=="Lab Incharge") {

			$str = "SELECT DISTINCT w.stage_smpl_cd,w.stage_smpl_cd
					  FROM m_sample_allocate AS sa
					  INNER JOIN workflow AS w ON sa.org_sample_code=w.org_sample_code
					  INNER JOIN code_decode AS cd ON sa.org_sample_code=cd.org_sample_code
					  INNER JOIN sample_inward AS si ON w.org_sample_code=si.org_sample_code
					  WHERE si.status_flag IN('SR','LA','R','A') AND w.stage_smpl_flag='R' AND si.acceptstatus_flag='A' AND w.dst_usr_cd=".$_SESSION['user_code']." AND w.dst_loc_id=".$_SESSION['posted_ro_office']."";

		} else {

			$str = "SELECT DISTINCT w.stage_smpl_cd,w.stage_smpl_cd
					  FROM m_sample_allocate AS sa
					  INNER JOIN workflow as w on sa.org_sample_code=w.org_sample_code
					  INNER JOIN code_decode as cd on sa.org_sample_code=cd.org_sample_code
					  INNER JOIN sample_inward as si on w.org_sample_code=si.org_sample_code
					  WHERE si.status_flag IN('SR','LA','R','A') AND w.stage_smpl_flag='R' AND si.acceptstatus_flag='A' AND w.dst_usr_cd=".$_SESSION['user_code']." AND w.dst_loc_id=".$_SESSION['posted_ro_office']."";

		}

		$query = $conn->execute($str);
		$result = $query->fetchAll('assoc');
		$stage_sample_code_list = array();

		//creating array format requird for listing in view
		foreach ($result as $esch) {

			$stage_sample_code_list[$esch['stage_smpl_cd']] = $esch['stage_smpl_cd'];
		}

		return $stage_sample_code_list;

	}


/********************************************************************************************************************************************************************************************************************************************/

	//to list sample code ready to Forward to lab incharge
	public function readyToForwardRetestSamplesList(){

		$conn = ConnectionManager::get('default');
		$user_cd=$this->Session->read('user_code');
		$this->loadModel('SampleInward');

		if($_SESSION["role"]=="Lab Incharge")
		{
			$str="SELECT DISTINCT w.stage_smpl_cd,w.stage_smpl_cd
				   FROM m_sample_allocate AS sa
					INNER JOIN workflow AS w on sa.org_sample_code=w.org_sample_code
					INNER JOIN code_decode AS cd on sa.org_sample_code=cd.org_sample_code
					INNER JOIN sample_inward AS si on w.org_sample_code=si.org_sample_code
					WHERE si.status_flag NOT IN('SR','A','IF','LA') AND acceptstatus_flag='A' AND stage_smpl_flag='R' AND w.dst_usr_cd=".$_SESSION['user_code']." AND w.dst_loc_id=".$_SESSION['posted_ro_office']."";

		}else{

			$str="SELECT DISTINCT w.stage_smpl_cd,w.stage_smpl_cd
				   FROM m_sample_allocate as sa
					INNER JOIN workflow as w on sa.org_sample_code=w.org_sample_code
					INNER JOIN code_decode as cd on sa.org_sample_code=cd.org_sample_code
					INNER JOIN sample_inward as si on w.org_sample_code=si.org_sample_code
					WHERE si.status_flag='SR' AND si.status_flag NOT IN('IF','A','SR','LA') AND acceptstatus_flag='A' AND stage_smpl_flag='R' AND w.dst_usr_cd=".$_SESSION['user_code']." AND w.dst_loc_id=".$_SESSION['posted_ro_office']."";
		}

		$query = $conn->execute($str);
		$result = $query->fetchAll('assoc');
		$stage_sample_code_list = array();

		//creating array format requird for listing in view
		foreach($result as $esch){
			$stage_sample_code_list[$esch['stage_smpl_cd']] = $esch['stage_smpl_cd'];
		}

		return $stage_sample_code_list;

	}

/********************************************************************************************************************************************************************************************************************************************/


	//list of sample available samples to Allocate for test or forward to lab incharge
	public function availableToAllocateRetest(){
		
		//by default
		$to_dt = date('Y-m-d');
		$from_dt = date('Y-m-d',strtotime('-1 month'));
		
		if ($this->request->is('post')) {

			//on search
			$to_dt = 	$this->request->getData('to_dt');
			$from_dt = $this->request->getData('from_dt');

								   
															  
												   

			if (empty($from_dt) || empty($to_dt)) {

				echo "<script>alert('Please Select Proper Dates');</script>";
			}
			$this->set(compact('to_dt','from_dt'));
		}

		if (!empty($from_dt) || !empty($to_dt)) {

			//to be allocate to chemist
			$avail_to_allocate = $this->getSampleToAllocateRetest($from_dt,$to_dt);
			$this->set('avail_to_allocate',$avail_to_allocate);

			//to be forwarded to Lab Incharge
			$avail_to_forward = $this->getSampleToForwardInchrgRetest($from_dt,$to_dt);
			$this->set('avail_to_forward',$avail_to_forward);

			/*get the list of send back sample by chemist,*/
			$sendback = $this->getSampleReturnedByChemistRetest($from_dt,$to_dt);
			$this->set('sendback',$sendback);
			
		}

	}

/********************************************************************************************************************************************************************************************************************************************/

/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>[getSampleToAllocateRetest]>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

	//created common function to fetch list , to be used for dashboard counts also, on 28-04-2021 by Amol
	public function getSampleToAllocateRetest($from_dt=null,$to_dt=null){

		$conn = ConnectionManager::get('default');
		$user_cd=$this->Session->read('user_code');

		//to list sample for allocation
		$sample_list_to_allocate = $this->readyToAllocateRetestSamplesList();

		//set array format
		$cus_string= '';
		foreach($sample_list_to_allocate as $each){

			$cus_string .= $each."','";
		}
		if(!empty($from_dt) && !empty($to_dt)){
			$dateCondition = " AND date(si.created) >= '$from_dt' AND date(si.created) <= '$to_dt'";
		}else{
			$dateCondition = "";
		}

		//to get extra parameters for listing
		//added new condition of stage sample code above on 07-05-2021 by Akash to distinct records

		$query = $conn->execute("SELECT DISTINCT ON (w.stage_smpl_cd) si.inward_id, w.stage_smpl_cd, si.received_date, st.sample_type_desc, mcc.category_name, mc.commodity_name, ml.ro_office, w.modified AS requested_on
									FROM sample_inward AS si
									INNER JOIN m_sample_type AS st ON si.sample_type_code=st.sample_type_code
									INNER JOIN m_commodity_category AS mcc ON si.category_code=mcc.category_code
									INNER JOIN dmi_ro_offices AS ml ON ml.id=si.loc_id
									INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
									INNER JOIN workflow AS w ON w.org_sample_code = si.org_sample_code
									WHERE w.stage_smpl_cd IN ('$cus_string')".$dateCondition);

		$avail_to_allocate = $query ->fetchAll('assoc');
		$this->set('avail_to_allocate',$avail_to_allocate);

		return $avail_to_allocate;
	}

/********************************************************************************************************************************************************************************************************************************************/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>[Get Sample To Forward Incharge Retest]>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

	//created common function to fetch list , to be used for dashboard counts also, on 28-04-2021 by Amol
	public function getSampleToForwardInchrgRetest($from_dt=null,$to_dt=null){

		$conn = ConnectionManager::get('default');

		//to list samples for forwarding to lab incharge
		$sample_list_to_forward = $this->readyToForwardRetestSamplesList();

		//set array format
		$cus_string= '';
		foreach($sample_list_to_forward as $each){

			$cus_string .= $each."','";
		}
		if(!empty($from_dt) && !empty($to_dt)){
			$dateCondition = " AND date(si.created) >= '$from_dt' AND date(si.created) <= '$to_dt'";
		}else{
			$dateCondition = "";
		}

		//to get extra parameters for listing
		//added new condition of stage sample code above on 07-05-2021 by Akash to distinct records

		$query = $conn->execute("SELECT DISTINCT ON (w.stage_smpl_cd) si.inward_id, w.stage_smpl_cd, si.received_date, st.sample_type_desc, mcc.category_name, mc.commodity_name, ml.ro_office, w.modified AS requested_on
									FROM sample_inward AS si
									INNER JOIN m_sample_type AS st ON si.sample_type_code=st.sample_type_code
									INNER JOIN m_commodity_category AS mcc ON si.category_code=mcc.category_code
									INNER JOIN dmi_ro_offices AS ml ON ml.id=si.loc_id
									INNER JOIN m_commodity AS mc ON si.commodity_code=mc.commodity_code
									INNER JOIN workflow AS w ON w.org_sample_code = si.org_sample_code
									WHERE w.stage_smpl_cd IN ('$cus_string')".$dateCondition);

		$avail_to_forward = $query ->fetchAll('assoc');
		$this->set('avail_to_forward',$avail_to_forward);

		return $avail_to_forward;
	}


/********************************************************************************************************************************************************************************************************************************************/

	//created common function to fetch list , to be used for dashboard counts also, on 28-04-2021 by Amol
	public function getSampleReturnedByChemistRetest($from_dt=null,$to_dt=null){

		$conn = ConnectionManager::get('default');
		if(!empty($from_dt) && !empty($to_dt)){
			$dateCondition = " AND date(a.created) >= '$from_dt' AND date(a.created) <= '$to_dt'";
		}else{
			$dateCondition = "";
		}

		/*get the list of send back sample by chemist,*/
		$sendback = $conn->execute("SELECT DISTINCT s.sample_code, b.commodity_name, s.chemist_code, s.sendback_remark, s.recby_ch_date, s.org_sample_code
										FROM sample_inward AS a
										INNER JOIN m_sample_allocate AS s ON a.org_sample_code=s.org_sample_code
										INNER JOIN workflow AS wf ON wf.org_sample_code=a.org_sample_code
										INNER JOIN m_commodity AS b ON a.commodity_code=b.commodity_code
										WHERE s.acptnce_flag='NABC' AND s.test_n_r='R' AND s.user_code='".$_SESSION['user_code']."'".$dateCondition);

		$sendback = $sendback ->fetchAll('assoc');

		return $sendback;

	}

/********************************************************************************************************************************************************************************************************************************************/

	//to get sample code and redirect to sample allocate window
	public function redirectToAllocateRetest($allocate_sample_cd){

		$this->Session->write('allocate_sample_cd',$allocate_sample_cd);
		$this->redirect(array('controller'=>'SampleAllocate','action'=>'sample_allocate_retest'));

	}

/********************************************************************************************************************************************************************************************************************************************/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>[sampleAllocateRetest]>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/


	public function sampleAllocateRetest(){

		$this->authenticateUser();
		$this->viewBuilder()->setLayout('admin_dashboard');
		$conn = ConnectionManager::get('default');

		// set variables to show popup messages from view file
		$message = '';
		$message_theme = '';
		$redirect_to = '';

		$allocate_sample_cd = $this->Session->read('allocate_sample_cd');

		if (!empty($allocate_sample_cd)) {

			$this->loadModel('CodeDecode');
			$this->loadModel('MSampleAllocate');
			$this->loadModel('DmiUsers');
			$this->loadModel('ActualTestData');
			$this->loadModel('Workflow');
			$this->loadModel('MUnitWeight');

			$this->set('allocate_sample_cd',array($allocate_sample_cd=>$allocate_sample_cd));

			$user_type=$this->DmiUsers->find('list',array('order' => array('role' => 'ASC'),'keyField' => 'role','valueField'=>'role','conditions'=>array('role IN' =>array('Jr Chemist','Sr Chemist','Cheif Chemist'), 'status'=>'active')))->toArray();
			$this->set('user_type',$user_type);

			/* Change variable name grade_desc to unit_desc, */
			$this->loadModel('MUnitWeight');
			$unit_desc=$this->MUnitWeight->find('list',array('order' => array('unit_weight' => 'ASC'),'fields'=>array('unit_id','unit_weight'),'conditions' => array('display' => 'Y')))->toArray();
			$this->set('unit_desc',$unit_desc);

			if ($this->request->is('post')) {

				//check post data validations
				$validate_err = $this->allocatePostValidations($this->request->getData());

				if ($validate_err != '') {

					$this->set('validate_err',$validate_err);
					return null;
				}

				//html encode the each post inputs
				$postData = $this->request->getData();

				foreach ($postData as $key => $value) {

					$postData[$key] = htmlentities($postData[$key], ENT_QUOTES);
				}

				//chnage date format
				$dStart = new \DateTime(date('Y-m-d H:i:s'));

				// for format from date
				$date = $dStart->createFromFormat('d/m/Y',$postData["rec_from_dt"]);
				$from_date = $date->format('Y-m-d');
				$from_dt=date('Y-m-d',strtotime($from_date));

				//for format to date
				$date1 = $dStart->createFromFormat('d/m/Y', $postData['rec_to_dt']);
				$to_date = $date1->format('Y/m/d');
				$to_dt = date('Y-m-d',strtotime($to_date));

					if ($postData['category_code']==0) {

						$query = $conn->execute("SELECT category_code FROM m_commodity WHERE commodity_code='".$postData['commodity_code']."'");

						$category_code = $query->fetchAll('assoc');

						$category_code = $category_code[0];
					}


					if (null !==($this->request->getData('save'))) {

						$sample_code = trim($postData['stage_sample_code']);
						$postData['sample_code'] = $sample_code;


						$query = $conn->execute("SELECT si.org_sample_code FROM sample_inward AS si INNER JOIN workflow AS w ON w.org_sample_code = si.org_sample_code WHERE w.stage_smpl_cd = '$sample_code'");

						$ogrsample1 = $query->fetchAll('assoc');

						$ogrsample = $ogrsample1[0]['org_sample_code'];
						$postData['org_sample_code'] = $ogrsample;
						$postData['rec_from_dt'] = $from_dt;
						$postData['rec_to_dt'] = $to_dt;
						$postData['lab_code'] = $_SESSION['posted_ro_office'];

						$test_n_r = $postData['test_n_r'];
						
						$expect_complt = $dStart->createFromFormat('d/m/Y', $postData['expect_complt']);
						$expect_complt1	= $expect_complt->format('Y/m/d');
						$expect_complt1 = date('Y-m-d',strtotime($expect_complt1));

						$postData['expect_complt']	= $expect_complt1;

						if ($postData['category_code']==0) {

							$postData['category_code']	= $category_code['category_code'];

							//chnage date format
							$dStart = new \DateTime(date('Y-m-d H:i:s'));

							// for expect completion date

							$expect_complt = $dStart->createFromFormat('d/m/Y',$postData["expect_complt"]);
							$expect_complt1 = $expect_complt->format('Y-m-d');
							$expect_complt1=date('Y-m-d',strtotime($expect_complt1));

							$postData['expect_complt']	= $expect_complt1;

						}

						if ($test_n_r=='R') {

							$test_cnt = $conn->execute("SELECT max(test_n_r_no) FROM m_sample_allocate WHERE sample_code='$sample_code' AND test_n_r='R'");
							$test_cnt = $test_cnt->fetchAll('assoc');

							$test_n_r_no = $test_cnt['0']['max']+1;
							$postData['test_n_r_no'] = $test_n_r_no;
							$postData['test_n_r']='R';

						} else {

							$test_n_r_no=1;
							$postData['test_n_r_no'] = $test_n_r_no;
							$postData['test_n_r']='R';
						}

						$sampleAllocateEntity = $this->MSampleAllocate->newEntity($postData);
						$allocateResult = $this->MSampleAllocate->save($sampleAllocateEntity);
						
						if ($allocateResult) {

							if ($_SESSION['role']=='Lab Incharge') {

								$conn->execute("UPDATE workflow SET stage_smpl_flag='LI' WHERE org_sample_code='$ogrsample' AND stage_smpl_flag='OF'");

								$conn->execute("UPDATE sample_inward SET status_flag='LA' WHERE org_sample_code='$ogrsample'");

							} else {

								$conn->execute("UPDATE sample_inward SET status_flag='A' WHERE org_sample_code='$ogrsample'");
							}

							$alloc_to_user_code = $postData['alloc_to_user_code'];
							$stage_smpl_cd = $postData['chemist_code'];

							$user_data1 = $this->DmiUsers->find('all', array('conditions'=> array('id IS' =>$alloc_to_user_code)))->first();

							$role_code = $user_data1['posted_ro_office'];
							$tran_date = $postData['tran_date'];

							$data = $this->Workflow->find('all', array('conditions'=> array('stage_smpl_cd IS' => $sample_code)))->toArray();

							$stage = $data[0]['stage']+1;

							$workflow_data = array("org_sample_code"=>$ogrsample,
													"src_loc_id"=>$_SESSION["posted_ro_office"],
													"src_usr_cd"=>$_SESSION["user_code"],
													"dst_loc_id"=>$role_code,
													"dst_usr_cd"=>$alloc_to_user_code,
													"stage_smpl_cd"=>$stage_smpl_cd,
													"user_code"=>$_SESSION["user_code"],
													"tran_date"=>$tran_date,
													"stage"=>$stage,
													"stage_smpl_flag"=>"TA");

							$_SESSION["sample"] = $postData['stage_sample_code'];


							$codeDecodeEntity = $this->CodeDecode->newEntity($postData);

							if (!$this->CodeDecode->save($codeDecodeEntity)) {

								$message = 'Sorry.. There is some technical issues. please check';
								$message_theme = 'failed';
								$redirect_to = 'sample_allocate';
							}

							$workflowEntity = $this->Workflow->newEntity($workflow_data);

							$this->Workflow->save($workflowEntity);

							$_SESSION["posted_ro_office"] = $_SESSION["posted_ro_office"];
							$_SESSION["loc_user_id"] = $_SESSION["user_code"];


							$test = explode(",",$postData['tests']);

							$test = array_unique($test);

							for ($i=0;$i<count($test);$i++) {

								$postData['test_code']= $test[$i];
								$test_alloc[] = $postData;

							}

							$ActualTestDataEntity  = $this->ActualTestData->newEntities($test_alloc);

							foreach ($ActualTestDataEntity as $eachData) {

								if (!$this->ActualTestData->save($eachData)) {

									$message = 'Sorry.. There is some technical issues. please check';
									$message_theme = 'failed';
									$redirect_to = 'sample_allocate';
								}
							}

							$get_id = $this->MSampleAllocate->find('all',array('fields'=>'sr_no','conditions'=>array('sample_code IS'=>$sample_code),'order'=>'sr_no desc'))->first();
							$lastInsertedId = $get_id['sr_no'];

							$query	= $conn->execute("SELECT chemist_code, f_name ,l_name,role
													  FROM m_sample_allocate AS s
													  INNER JOIN dmi_users AS u ON s.alloc_to_user_code=u.id
													  WHERE sr_no='$lastInsertedId'");

							$chemist_code = $query->fetchAll('assoc');

							//call to the common SMS/Email sending method
							$this->loadModel('DmiSmsEmailTemplates');
							//$this->DmiSmsEmailTemplates->sendMessage(2008,$sample_code);

							$message = 'Sample Code '.$chemist_code[0]['chemist_code'].' is allocated to  '.$chemist_code[0]['f_name'].' '.$chemist_code[0]['l_name'].'('.$chemist_code[0]['role'].'). ';
							$message_theme = 'success';
							$redirect_to = 'available_to_allocate';

						} else {

							$message = 'Sorry.. There is some technical issues. please check';
							$message_theme = 'failed';
							$redirect_to = 'sample_allocate';

						}

					} elseif (null !==($this->request->getData('update'))){


					}

			}

		}

		// set variables to show popup messages from view file
			$this->set('message',$message);
			$this->set('message_theme',$message_theme);
			$this->set('redirect_to',$redirect_to);
	}


/********************************************************************************************************************************************************************************************************************************************/

	//to get sample code and redirect to forwarding to lab incharge window
	public function redirectToForwardRetest($forward_sample_cd){

		$this->Session->write('forward_sample_cd',$forward_sample_cd);
		$this->redirect(array('controller'=>'SampleAllocate','action'=>'sample_forward_retest'));

	}

/********************************************************************************************************************************************************************************************************************************************/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>[sampleForwardRetest]>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/

	public function sampleForwardRetest(){

		$this->authenticateUser();
		$this->viewBuilder()->setLayout('admin_dashboard');
		$conn = ConnectionManager::get('default');

		// set variables to show popup messages from view file
		$message = '';
		$message_theme = '';
		$redirect_to = '';

		$forward_sample_cd = $this->Session->read('forward_sample_cd');

		if (!empty($forward_sample_cd)) {

			$this->loadModel('CodeDecode');
			$this->loadModel('MSampleAllocate');
			$this->loadModel('DmiUsers');
			$this->loadModel('ActualTestData');
			$this->loadModel('Workflow');
			$this->loadModel('MUnitWeight');

			$this->set('forward_sample_cd',array($forward_sample_cd=>$forward_sample_cd));

			$user_type=$this->DmiUsers->find('list',array('order' => array('role' => 'ASC'),'keyField' => 'role','valueField'=>'role','conditions'=>array('role' =>'Lab Incharge', 'status'=>'active')))->toArray();
			$this->set('user_type',$user_type);

			if ($this->request->is('post')) {

				//check post data validations
				$validate_err = $this->allocatePostValidations($this->request->getData());

				if ($validate_err != '') {

					$this->set('validate_err',$validate_err);
					return null;
				}

				//html encode the each post inputs
				$postData = $this->request->getData();

				foreach ($postData as $key => $value) {

					$postData[$key] = htmlentities($postData[$key], ENT_QUOTES);
				}

				if (null !==($this->request->getData('save'))) {

					$sample_code = trim($this->request->getData('stage_sample_code'));

					$query = $conn->execute("SELECT si.org_sample_code FROM sample_inward AS si INNER JOIN workflow as w on w.org_sample_code = si.org_sample_code WHERE w.stage_smpl_cd = '$sample_code'");

					$ogrsample1 = $query->fetchAll('assoc');

					$ogrsample = $ogrsample1[0]['org_sample_code'];
					$alloc_to_user_code	= $this->request->getData('alloc_to_user_code');

					$user_data1	= $this->DmiUsers->find('all', array('conditions'=> array('id IS' =>$alloc_to_user_code)))->first();
					$role_code = $user_data1['posted_ro_office'];

					$tran_date = $this->request->getData('tran_date');

					$data	= $this->Workflow->find('all', array('conditions'=> array('stage_smpl_cd IS' => $sample_code)))->toArray();
					$stage = $data[0]['stage']+1;

					// genrate stage sample code,
					$stage_smpl_cd	= $this->Customfunctions->createStageSampleCode();

					$workflow_data	= array("org_sample_code"=>$ogrsample,
														"src_loc_id"=>$_SESSION["posted_ro_office"],
														"src_usr_cd"=>$_SESSION["user_code"],
														"dst_loc_id"=>$role_code,
														"dst_usr_cd"=>$alloc_to_user_code,
														"stage_smpl_cd"=>$stage_smpl_cd,
														"user_code"=>$_SESSION["user_code"],
														"tran_date"=>$tran_date,
														"stage"=>$stage,
														"stage_smpl_flag"=>"RIF");

					$workflowEntity = $this->Workflow->newEntity($workflow_data);
					$this->Workflow->save($workflowEntity);

					$conn->execute("UPDATE sample_inward SET status_flag='RIF' WHERE org_sample_code='$ogrsample'");

					//call to the common SMS/Email sending method
					$this->loadModel('DmiSmsEmailTemplates');
					//$this->DmiSmsEmailTemplates->sendMessage(2009,$sample_code);

					$message = 'The Sample is Forwarded to Lab Incharge with '.$stage_smpl_cd.' code!';
					$message_theme = 'success';
					$redirect_to = 'available_to_allocate_retest';

				}

			}

		}

		// set variables to show popup messages from view file
			$this->set('message',$message);
			$this->set('message_theme',$message_theme);
			$this->set('redirect_to',$redirect_to);

	}


/********************************************************************************************************************************************************************************************************************************************/


/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>[allocatedRetestList]>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/


	//to show the list of Retest allocated/forwarded samples
	public function allocatedRetestList(){

		$conn = ConnectionManager::get('default');
		$user_cd=$this->Session->read('user_code');

		// add new join condition to show allocated chemist name,
		 $allRes = $conn->execute("SELECT a.sample_code,cd.chemist_code, alloc_date,u.f_name || ' ' || u.l_name AS f_name,a.alloc_to_user_code,cun.f_name || ' ' || cun.l_name AS cun_f_name
									FROM actual_test_data AS a
									INNER JOIN code_decode AS cd ON cd.chemist_code=a.chemist_code AND a.alloc_to_user_code=cd.alloc_to_user_code AND cd.display='Y' AND a.sample_code=cd.sample_code
									INNER JOIN m_sample_allocate AS sa ON sa.chemist_code=cd.chemist_code AND sa.alloc_to_user_code=cd.alloc_to_user_code AND sa.display='Y' AND sa.sample_code=cd.sample_code AND sa.test_n_r='R'
									INNER JOIN dmi_users AS cun ON cun.id=sa.alloc_to_user_code
									INNER JOIN dmi_users AS u ON u.id=sa.user_code  AND u.role='".$_SESSION['role']."' AND a.display='Y' AND cd.chemist_code!='-'  AND cd.user_code='".$_SESSION['user_code']."' GROUP BY a.sample_code,u.f_name,cun.f_name,cd.chemist_code,sa.test_n_r,sa.alloc_date,u.l_name,cun.l_name,a.alloc_to_user_code ORDER BY alloc_date DESC");

		$allRes = $allRes->fetchAll('assoc');

		$this->set('allRes',$allRes);

		$allres3=array();

		foreach ($allRes as $allRes2) {

			$user_code_new=$allRes2['alloc_to_user_code'];
			$sample_code_new=$allRes2['sample_code'];
			$chemist_code=$allRes2['chemist_code'];

			$allRes1 = $conn->execute("SELECT a.chemist_code,b.test_name
										FROM actual_test_data AS a
										INNER JOIN code_decode AS cd ON cd.chemist_code=a.chemist_code AND a.alloc_to_user_code=cd.alloc_to_user_code  AND cd.display='Y' AND a.sample_code=cd.sample_code
										INNER JOIN m_sample_allocate AS sa ON sa.chemist_code=cd.chemist_code AND sa.alloc_to_user_code=cd.alloc_to_user_code AND sa.display='Y' AND sa.sample_code=cd.sample_code
										INNER JOIN m_test AS b ON a.test_code=b.test_code
										INNER JOIN dmi_users AS u ON u.id=sa.alloc_to_user_code  AND  a.display='Y' AND cd.chemist_code!='-'  AND a.alloc_to_user_code='$user_code_new' AND a.chemist_code='$chemist_code'
										GROUP BY a.chemist_code,b.test_name");

			$allRes1 = $allRes1->fetchAll('assoc');

			foreach ($allRes1 as $res2) {

				array_push($allres3,$res2);
			}
		}

		$this->set('allRes1',$allres3);

		//to get forwarded lab incharge samples list
		$res = $conn->execute("SELECT DISTINCT w.stage_smpl_cd,u.f_name || ' ' || u.l_name AS f_name,u.role FROM workflow AS w
								INNER JOIN m_sample_allocate AS s ON s.org_sample_code=w.org_sample_code
								INNER JOIN dmi_users AS u ON w.dst_usr_cd=u.id AND stage_smpl_flag='IF' AND s.test_n_r='R' AND role='Lab Incharge' AND w.src_usr_cd ='".$_SESSION['user_code']."'GROUP BY u.f_name,u.l_name,w.stage_smpl_cd,u.role");

		$res = $res->fetchAll('assoc');

		$this->set('res',$res);
	}

}
?>
