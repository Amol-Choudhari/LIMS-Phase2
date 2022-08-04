<?php
	namespace app\Controller\Component;
	use Cake\Controller\Controller;
	use Cake\Controller\Component;
	use Cake\Controller\ComponentRegistry;
	use Cake\ORM\Table;
	use Cake\ORM\TableRegistry;
	use Cake\Datasource\EntityInterface;
	use Cake\Datasource\ConnectionManager;
	use app\Controller\SampleForwardController;

	class IlcComponent extends Component {

		public $components= array('Session','Customfunctions');
		public $controller = null;
		public $session = null;
	
		public function initialize(array $config):void{
			parent::initialize($config);
			$this->Controller = $this->_registry->getController();
			$this->Session = $this->getController()->getRequest()->getSession();
		}

		// added for selected  record save or not check done 24/06/2022 by shreeya
		public function checkSavedornot($forw_sample_cd) {

			$IlcSelectedRals = TableRegistry::getTableLocator()->get('IlcSelectedRals');
			$getSavedList = $IlcSelectedRals->find('all')->select()->where(['status IS' => '1','stage_sample_code'=>$forw_sample_cd])->toArray();

			if(empty($getSavedList)){

				return false;
			}
			return true;
		}
				
		/******************************************************************************************************************************************************************/ 
		//added for save selected record  03/06/2022 by shreeya  
		public function getSavedSelectedRALs($sampleTypeCode) {

			$forw_sample_cd = $this->Session->read('forw_sample_cd');
			// load model
			$conn = ConnectionManager::get('default');
			//$Workflow = TableRegistry::getTableLocator()->get('Workflow');
			$SampleInward = TableRegistry::getTableLocator()->get('SampleInward');
			$IlcSelectedRals = TableRegistry::getTableLocator()->get('IlcSelectedRals');
		
			// featch saved list on template done 14/06/2022 by shreeya
			$getSavedList = $IlcSelectedRals->find('all')->select()->where(['status IS' => '1','stage_sample_code'=>$forw_sample_cd,'sample_type'=>$sampleTypeCode])->toArray();
		
			$query = $conn->execute("SELECT u.id as urid,ro.id as rid ,u.f_name,u.l_name,ro.ro_office FROM ilc_selected_rals AS sr
									INNER JOIN dmi_users AS u ON u.id = sr.inwd_off_val
									INNER JOIN dmi_ro_offices AS ro ON ro.id = sr.ral_name_val
									WHERE sr.stage_sample_code='$forw_sample_cd'AND sr.sample_type='$sampleTypeCode'AND sr.status=1 order by sr.id");
						
			
			$getSavedList = $query->fetchAll('assoc');
			$this->Controller->set('getSavedList',$getSavedList);
		}

		/******************************************************************************************************************************************************************/ 

		//added for save new mapping code on new table db done  17/06/2022 by shreeya  
		public function SelectSavedMapping($sampleTypeCode) {

			$forw_sample_cd = $this->Session->read('forw_sample_cd');
			// load model
			$IlcSelectedRals = TableRegistry::getTableLocator()->get('IlcSelectedRals');
			$IlcOrgSmplcdMaps = TableRegistry::getTableLocator()->get('IlcOrgSmplcdMaps');
			$date = date('Y-m-d H:i:s');

			//checking entry with status 1 to avoid resaving of entries
			$checkmapentry=	$IlcOrgSmplcdMaps->find('all')->select()->where(['status IS' => '1','org_sample_code'=>$forw_sample_cd,'sample_type'=>$sampleTypeCode])->first();
			
			if(empty($checkmapentry)){

				// added for save record in mapping table done 17/06/2022 by shreeya
				$getSavedList = $IlcSelectedRals->find('all')->select()->where(['status IS' => '1','stage_sample_code'=>$forw_sample_cd,'sample_type'=>$sampleTypeCode])->toArray();
				$i=1;
				$savelist=array();
				foreach($getSavedList as $eachLab)
				{
					//added for new generating mapping code using Customfunctions component 17/06/2022
					$new_mapping_code[$i] = $this->Customfunctions->createStageSampleCode();
					$savelist[] = array(

						'org_sample_code'=>$forw_sample_cd,
						'sample_type'=>$eachLab['sample_type'],
						'ilc_org_sample_cd'=>$new_mapping_code[$i],
						'ral_name_val'=>$eachLab['ral_name_val'],
						'inwd_off_val'=>$eachLab['inwd_off_val'],
						'status'=>1,
						'created'=>$date,
						"modified"=>$date
					
					);
				$i++;	
				}

				foreach($savelist as $eachLab)
		
				{
					$ilcEntity = $IlcOrgSmplcdMaps->newEntity($eachLab);
					$IlcOrgSmplcdMaps->save($ilcEntity);
				}
			}
			

				
		}

		/******************************************************************************************************************************************************************/ 

		// save selected 5 RAL on sample inward table done 27/06/2022 by shreeya
		public function SavedToSampleInward($sampleTypeCode) {

			$forw_sample_cd = $this->Session->read('forw_sample_cd');

			// load model
			$SampleInward = TableRegistry::getTableLocator()->get('SampleInward');
			$IlcOrgSmplcdMaps = TableRegistry::getTableLocator()->get('IlcOrgSmplcdMaps');
			$Workflow = TableRegistry::getTableLocator()->get('Workflow');
			$date = date('Y-m-d H:i:s');
			
			// added for save record in mapping table done 17/06/2022 by shreeya
			$getSavedList = $IlcOrgSmplcdMaps->find('all')->select()->where(['status IS' => '1','org_sample_code'=>$forw_sample_cd,'sample_type'=>$sampleTypeCode])->toArray();

			//get featch all sample inward record done 28/06/2022 by shreeya
			$sample_inward_data = $SampleInward->find('all',array('conditions'=>array('org_sample_code IS'=>$forw_sample_cd)))->first();
		
			//select only inward_id in desc
			$get_last_id = $SampleInward->find('all',array('fields'=>'inward_id','order'=>'inward_id desc'))->first();

			$inward_id		 = $get_last_id['inward_id'];
			$src_loc_id      = $sample_inward_data["loc_id"];
			$fin_year		 = $sample_inward_data['fin_year'];
			$letr_date       = date('Y-m-d');
			
			$i=1;
			$savelist=array();
			foreach($getSavedList as $eachLab)
			{
				$checkInward = $SampleInward->find('all',array('conditions'=>array('org_sample_code'=>$eachLab['ilc_org_sample_cd'])))->first();
				if(empty($checkInward)){
					
					$savelist[] = array(

						'inward_id'				=>	$inward_id+$i,
						'loc_id'				=>	$src_loc_id,
						'fin_year'				=>	$fin_year,
						'stage_sample_code'     =>  $eachLab['ilc_org_sample_cd'],
						'sample_type_code'      =>  $eachLab['sample_type'],
						'org_sample_code'       =>  $eachLab['ilc_org_sample_cd'],
						'designation'		    =>	$sample_inward_data['designation'],
						'users'				    =>	$sample_inward_data['users'],
						'letr_ref_no'		    =>	$sample_inward_data['letr_ref_no'],
						'letr_date'			    =>	$letr_date,
						'received_date'		    =>	$letr_date,
						'container_code'		=>	$sample_inward_data['container_code'],
						'par_condition_code'	=>	$sample_inward_data['par_condition_code'],
						'parcel_size'		    =>	$sample_inward_data['parcel_size'],
						'sam_condition_code'	=>	$sample_inward_data['sam_condition_code'],
						'sample_total_qnt'   	=>	$sample_inward_data['sample_total_qnt'],
						'category_code'		    =>	$sample_inward_data['category_code'],
						'commodity_code'		=>	$sample_inward_data['commodity_code'],
						'ref_src_code'		    =>	$sample_inward_data['ref_src_code'],
						'expiry_month'		    =>	$sample_inward_data['expiry_month'],
						'expiry_year'		    =>	$sample_inward_data['expiry_year'],
						'acc_rej_flg'		    =>	$sample_inward_data['acc_rej_flg'],
						'rej_code'			    =>	$sample_inward_data['rej_code'],
						'name'				    =>	$sample_inward_data['name'],
						'address'			    =>	$sample_inward_data['address'],
						'rej_reason'			=>	$sample_inward_data['rej_reason'],
						'user_code'			    =>	$sample_inward_data['user_code'],
						'entry_flag'			=>	$sample_inward_data['entry_flag'],
						'status_flag'		    =>	$sample_inward_data['status_flag'],
						'entry_type'		    =>	'sub_sample',
						'created'			    =>	date('Y-m-d H:i:s')
						
					);
				}
			
			$i++;
			}
			//creating entities for array
			$Saveselect = $SampleInward->newEntities($savelist);
			//saving data in loop
			foreach($Saveselect as $select){
				$SampleInward->save($select);
			}

			/****************************************************************************************/
			// save selected 5 RAL on workflow table with SI flag done 29-06-2022 by shreeya
			$tran_date		= date('Y-m-d');
			$saveList=array();
			foreach($getSavedList as $eachLab)
			{
				$forwardedList = $Workflow->find('all',array('conditions'=>array('org_sample_code'=>$eachLab['ilc_org_sample_cd'],'stage_smpl_flag IS'=>'SI')))->first();
				if(empty($forwardedList)){

					$saveList[] = array(

						'stage_smpl_cd'     	=>  $eachLab['ilc_org_sample_cd'],
						'org_sample_code'       =>  $eachLab['ilc_org_sample_cd'],
						'dst_loc_id'            =>  $eachLab['ral_name_val'],
						'dst_usr_cd'            =>  $eachLab['inwd_off_val'],
						'src_loc_id'            =>  $_SESSION['posted_ro_office'],
						'src_usr_cd'            =>  $_SESSION['user_code'],
						'user_code'             =>  $_SESSION['user_code'],
						'tran_date'             =>  $tran_date,
						'stage'				    =>  '1',
						'stage_smpl_flag'       =>  'SI'
						
					);
				}
				
					
			}
			//creating entities for array
			$Saveselect = $Workflow->newEntities($saveList);
			//saving data in loop
			foreach($Saveselect as $select){
				$Workflow->save($select);
			}

			/****************************************************************************************/
			// save selected 5 RAL on workflow table with SD flag done 29-06-2022 by shreeya
			$SaveList=array();
			foreach($getSavedList as $eachLab)
			{
				$forwardedList = $Workflow->find('all',array('conditions'=>array('org_sample_code'=>$eachLab['ilc_org_sample_cd'],'stage_smpl_flag IS'=>'SD')))->first();
				if(empty($forwardedList)){
					$SaveList[] = array(

						'stage_smpl_cd'     	=>  $eachLab['ilc_org_sample_cd'],
						'org_sample_code'       =>  $eachLab['ilc_org_sample_cd'],
						'dst_loc_id'            =>  $eachLab['ral_name_val'],
						'dst_usr_cd'            =>  $eachLab['inwd_off_val'],
						'src_loc_id'            =>  $_SESSION['posted_ro_office'],
						'src_usr_cd'            =>  $_SESSION['user_code'],
						'user_code'             =>  $_SESSION['user_code'],
						'tran_date'             =>  $tran_date,
						'stage'				    =>  '2',
						'stage_smpl_flag'       =>  'SD'
					);
				}
			}
			//creating entities for array
			$SaveSelect = $Workflow->newEntities($SaveList);
			//saving data in loop
			foreach($SaveSelect as $select){
				$Workflow->save($select);
			}
				
		}

		/******************************************************************************************************************************************************************/ 
		
		// forward to workflow table with OF flag min 5 RAL done 29-06-2022 by shreeya
		public function ilcsampleForward($sampleTypeCode) {

			$forw_sample_cd = $this->Session->read('forw_sample_cd');
			$conn = ConnectionManager::get('default');
			
			// load model
			$Workflow = TableRegistry::getTableLocator()->get('Workflow');
			$DmiUsers = TableRegistry::getTableLocator()->get('DmiUsers');
			$SampleInward = TableRegistry::getTableLocator()->get('SampleInward');
			$IlcOrgSmplcdMaps = TableRegistry::getTableLocator()->get('IlcOrgSmplcdMaps');

			$ogrsample1	= $SampleInward->find('all', array('conditions'=> array('stage_sample_code IS' => $forw_sample_cd)))->first();
			$ogrsample	= $ogrsample1['org_sample_code'];
			
			$office_code	= $this->getController()->getRequest()->getData('ral_cal');
			
			$tran_date		= date('Y-m-d');
			$dispatch_date	= date("Y/m/d");

			
			if ($office_code == 'HO') {
			
				$flag = "HF";

			} else {

				$flag = "OF";
			}
			
			$getSavedList = $IlcOrgSmplcdMaps->find('all')->select()->where(['status IS' => '1','org_sample_code'=>$forw_sample_cd])->toArray();

			$savedlist=array();
			foreach ($getSavedList as $each) {
				
				//Checks if the Sample is Already Forwarded.
				$forwardedList = $Workflow->find('all',array('conditions'=>array('org_sample_code'=>$each['ilc_org_sample_cd'],'stage_smpl_flag IS'=>'OF')))->first();
				if(empty($forwardedList)){
					$new_sample_code	= $this->Customfunctions->createStageSampleCode();

					$savedlist[] = array(
							
						'org_sample_code'       =>$each['ilc_org_sample_cd'],
						'stage_smpl_cd'     	=>$new_sample_code,
						'dst_loc_id'            =>$each['ral_name_val'],
						'dst_usr_cd'            =>$each['inwd_off_val'],
						'src_loc_id'            =>$_SESSION['posted_ro_office'],
						'src_usr_cd'            =>$_SESSION['user_code'],
						'tran_date'            =>$tran_date,
						'user_code'             =>$_SESSION["user_code"],
						'stage'                =>'4',
						'stage_smpl_flag'      =>$flag

					);
				}
				
			}
			//saved record
			$workflowEntity = $Workflow->newEntities($savedlist);
			
			foreach($workflowEntity as $each)
			{	
				if ($Workflow->save($each)) {
					
					if ($office_code=='HO') {
				
						$str="UPDATE sample_inward SET status_flag='H',dispatch_date='$dispatch_date' WHERE stage_sample_code='".$each['org_sample_code']."' ";
						
					} elseif ($office_code=='CAL') {
						
						$str="UPDATE sample_inward SET status_flag='F',chlng_smpl_disptch_cal_dt='$tran_date',dispatch_date='$dispatch_date'   WHERE stage_sample_code='".$each['org_sample_code']."' ";

					} else {
						
						$str="UPDATE sample_inward SET status_flag='F',dispatch_date='$dispatch_date' WHERE stage_sample_code='".$each['org_sample_code']."' ";
						
					}

				}	
				$conn->execute($str);
			}
			
 			
			//all process updating records sample inward table with  main sample code

			if ($office_code=='HO') {
					
				$str="UPDATE sample_inward SET status_flag='H',dispatch_date='$dispatch_date' WHERE stage_sample_code='$forw_sample_cd' ";
				
			} elseif ($office_code=='CAL') {
				
				$str="UPDATE sample_inward SET status_flag='F',chlng_smpl_disptch_cal_dt='$tran_date',dispatch_date='$dispatch_date'   WHERE stage_sample_code='$forw_sample_cd' ";

			} else {
				
				$str="UPDATE sample_inward SET status_flag='F',dispatch_date='$dispatch_date' WHERE stage_sample_code='$forw_sample_cd' ";
				
			}
			$conn->execute($str);

			//all process updating records workflow table with OF flag
			$workflowentity = $Workflow->newEntity(array(
				'org_sample_code'       =>$forw_sample_cd,
				'stage_smpl_cd'     	=>$forw_sample_cd,
				'dst_loc_id'            =>$_SESSION['posted_ro_office'],//same as source loc id for ILC to manage forwarded list
				'dst_usr_cd'            =>$_SESSION['user_code'], //same as source loc id for ILC to manage forwarded list
				'src_loc_id'            =>$_SESSION['posted_ro_office'],
				'src_usr_cd'            =>$_SESSION['user_code'],
				'tran_date'            =>$tran_date,
				'user_code'             =>$_SESSION["user_code"],
				'stage'                =>'4',
				'stage_smpl_flag'      =>$flag
			));
			$Workflow->save($workflowentity);
			
		return true;		
			
		}


		//check wheather sample type ilc final grading 11-07-2022
		public function ilcFinalGradeAvaiIf($sampleTypeCode) {

			$user_id = $_SESSION['user_code'];
			$conn = ConnectionManager::get('default');
			// load model
			$Workflow = TableRegistry::getTableLocator()->get('Workflow');

			$query1 = $conn->execute("SELECT ft.sample_code,ft.sample_code
									 FROM Final_Test_Result AS ft
									 INNER JOIN workflow AS w ON ft.org_sample_code=w.org_sample_code
									 INNER JOIN m_sample_allocate sa ON ft.org_sample_code=sa.org_sample_code
									 INNER JOIN sample_inward AS si ON ft.org_sample_code=si.org_sample_code
									 WHERE  si.sample_type_code = 9 AND ft.display='Y' AND w.dst_usr_cd='$user_id' AND w.stage_smpl_flag IN ('AR','FO','FC','FG','FS','VS','FGIO') AND  si.status_flag IN('VS','FG','FC','FO','FS')
									 GROUP BY ft.sample_code ");

			$final_result_details1 = $query1->fetchAll('assoc');

			//Conditions to check wheather stage sample code is final graded or not.
			$final_result1 = array();
			if(!empty($final_result_details1)){

				foreach($final_result_details1 as $stage_sample_code){

					$final_grading1 = $Workflow->find('all',array('conditions'=>array('stage_smpl_flag'=>'FG','stage_smpl_cd'=>$stage_sample_code['sample_code'],'src_usr_cd'=>$user_id)))->first();

					if(empty($final_grading1)){
						$final_result1[]= $stage_sample_code;
					}
				}
			}
			return $final_result1;

		}

		// check wheather stage sample code is final graded or not
		public  Function ilcFinalGradeAvaiElse()
		{
			$user_id = $_SESSION['user_code'];
			$conn = ConnectionManager::get('default');
			// load model
			$Workflow = TableRegistry::getTableLocator()->get('Workflow');

			$query1 = $conn->execute("SELECT ft.sample_code,ft.sample_code
									 FROM Final_Test_Result AS ft
									 INNER JOIN workflow AS w ON ft.org_sample_code=w.org_sample_code
									 INNER JOIN m_sample_allocate sa ON ft.org_sample_code=sa.org_sample_code
									 INNER JOIN sample_inward AS si ON ft.org_sample_code=si.org_sample_code
									 WHERE ft.display='Y'
									 AND w.dst_usr_cd='$user_id'
									 AND w.stage_smpl_flag IN('AR','FO','FC','FR')
									 AND  si.status_flag IN('VS','FO','FC','FR')
									 GROUP BY ft.sample_code");

			$final_result_details1 = $query1->fetchAll('assoc');
            
			/* Conditions to check wheather stage sample code is final graded or not.*/
			$final_result1 = array();
			if (!empty($final_result_details1)) {

				foreach ($final_result_details1 as $stage_sample_code) {

					$final_grading_details1 = $Workflow->find('all',array('conditions'=>array('stage_smpl_cd'=>$stage_sample_code['sample_code']),'order'=>array('id desc')))->first();

					if (!empty($final_grading_details1)) {

						$final_grading1 = $Workflow->find('all',array('conditions'=>array('dst_usr_cd'=>$user_id,'id'=>$final_grading_details['id'],'stage_smpl_flag !='=>'FG')))->first();

						if (!empty($final_grading1)) {
							$final_result1[]= $stage_sample_code;
						}
					}
				}

			}

			return $final_result1;

		}

		public  Function finalgradingresult($final_result1)
		{
			$conn = ConnectionManager::get('default');
			
			//to be used in below core query format, that's why
			$arr = "IN(";
			foreach ($final_result1 as $each) {
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

			$result1 = $query->fetchAll('assoc');
			return $result1;
			
		}	
		

		//added for save calculate zscore on 04-08-2022 by shreeya 

		// public function SaveCalculateZscore (){


		// 	$sample_code = $this->Session->read('org_sample_code');
			
		// 	$IlcOrgSmplcdMaps = TableRegistry::getTableLocator()->get('IlcOrgSmplcdMaps');
		
		// 	$date = date('Y-m-d H:i:s');

		// 	// $postData = $this->request->getData();

			
		// 		// $get = $this->IlcOrgSmplcdMaps->find('all',array('fields'=>'ilc_org_sample_cd'))->toArray();
		// 		// $sample_code = $get['ilc_org_sample_cd'];

		// 		$getSavedList = $IlcOrgSmplcdMaps->find('all')->select()->where(['status IS' => '1','org_sample_code IS'=>$sample_code])->toArray();
				
				

		// 		// $org_sample_code  	= $postData['org_sample_code'];
		// 		// $sample_code	  	= $postData['sample_code'];
		// 		// $lab_name 		  	= $postData['lab_name'];
		// 		// $test_code 		  	= $postData['test_data'];
		// 		// $commodity_code   	= $postData['commodity_code'];
		// 		// $calculate_zscore 	= $postData['calculate_zscore'];
		// 		// $put_final_zscore 	= $postData['put_final_zscore'];
		// 		$date 			  	= date('Y-m-d H:i:s');

		// 		$zscore= array(

		// 			'org_sample_code' 	=> $sample_code,
		// 			'sample_code'     	=> 258521,
		// 			'lab_name'		  	=> 'kolkata',
		// 			'test_code'		  	=> 1,
		// 			'commodity_code'  	=> 4,
		// 			'calculate_zscore'	=> 2,
		// 			'put_final_zscore'	=> 2,
		// 			'created'			=> $date,
		// 			'modified'			=> $date	
					
		// 		);

		// 		$ZscorEntity = $IlcCalculateZscore->newEntity($zscore);
		// 		$IlcCalculateZscore->save($ZscorEntity);


		// }

		


    }


    ?>

