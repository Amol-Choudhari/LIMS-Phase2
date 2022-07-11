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


		


    }


    ?>

