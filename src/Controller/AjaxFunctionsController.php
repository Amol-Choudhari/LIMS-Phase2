<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controller;
use Cake\Network\Session\DatabaseSession;
use App\Network\Request\Request;
use App\Network\Response\Response;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
/**
 * Description of AjaxFunctionsController
 *
 * @author Acer
 */
class AjaxFunctionsController extends AppController{
    var $name = 'AjaxFunctions';
   // var $components= array('Customfunctions');

    public function initialize(): void {

        parent::initialize();
        $this->loadComponent('Customfunctions');
        //Changes by Shweta Apale 22-10-2021
        $this->SampleInward = $this->getTableLocator()->get('SampleInward');
        $this->MCommodityCategory = $this->getTableLocator()->get('MCommodityCategory');
        $this->MSampleAllocate = $this->getTableLocator()->get('MSampleAllocate');


    }

/*******************************************************************************************************************************************************************************************************************************/

    public function showCommodityDropdown(){

        $this->autoRender = false;
        $this->loadModel('MCommodity');
        $category_id = $_POST['commodity'];
        $commodities = $this->MCommodity->find('all', array('fields'=>array('commodity_code','commodity_name'), 'conditions'=>array('category_code IS'=>$category_id)))->toArray();
        ?>
        <option value=""><?php echo "Select Commodity";?></option>
        <?php foreach($commodities as $commodity){ ?>
                <option value="<?php echo $commodity['commodity_code'];?>"><?php echo $commodity['commodity_name'];?></option>
        <?php }
    }


/*******************************************************************************************************************************************************************************************************************************/

    //library function to show districts dropdown on state select by ajax
    public function showDistrictDropdown(){

        $this->autoRender = false;
        $this->loadModel('DmiDistricts');
        $state_id = $_POST['state'];
        // Apply "Order by" clause to get state list by order wise (Done By Pravin 10-01-2018)
        $districts = $this->DmiDistricts->find('all', array('fields'=>array('id','district_name'), 'conditions'=>array('state_id IS'=>$state_id, 'delete_status IS NULL'),'order'=>array('district_name')))->toArray();

        foreach($districts as $district){ ?>
                <option value="<?php echo $district['id']?>"><?php echo $district['district_name']?></option>
        <?php	}

    }

/*******************************************************************************************************************************************************************************************************************************/


    //Ajax function to show charges on add firm on certificate select
    public function showCharge(){

        $this->autoRender = false;
        $this->loadModel('DmiApplicationCharges');
        $get_charges = $this->DmiApplicationCharges->find('all',array('conditions'=>array('certificate_type_id IS'=>$this->request->getData('certification_type'))))->first();
        $total_charges = $get_charges['charge'];

        ?><input type="text" id="total_charge" name="total_charge" value="<?php echo $total_charges; ?>" readonly /><?php

    }


/*******************************************************************************************************************************************************************************************************************************/


    public function calculateCategoryWiseCharge(){
        $this->autoRender = false;
        $this->loadModel('MCommodity');
        $this->loadModel('DmiApplicationCharges');
        $selected_commodity_ids = explode(',',$this->request->getData('selected_sub_commodities'));

        $get_category_ids = $this->MCommodity->find('list',array('valueField'=>'category_code','conditions'=>array('commodity_code IN'=>$selected_commodity_ids)))->toList();

        $get_charges = $this->DmiApplicationCharges->find('all',array('conditions'=>array('certificate_type_id IS'=>$this->request->getData('certification_type'))))->first();
        $default_charges = $get_charges['charge'];

        $total_charges = $default_charges * count(array_unique($get_category_ids));//added array_unique()

        ?><input type="text" id="total_charge" name="total_charge" value="<?php echo $total_charges; ?>" readonly /><?php
    }


/*******************************************************************************************************************************************************************************************************************************/


    // get sample details for selected sample code,
    public function getSampleCatCommTypeDetails(){

        $this->autoRender = false;
        $this->loadModel('SampleInward');
        $this->loadModel('Workflow');
        $conn = ConnectionManager::get('default');

        $sampledetails = array();
        $sample_code = $_POST['sample_code'];
        // Apply empty field validation,
        if(!empty($sample_code)){

            $get_org_sample_code = $this->Workflow->find('all',array('fields'=>array('org_sample_code'),'conditions'=>array('stage_smpl_cd IS'=>$sample_code)))->first();
            $org_sample_code = $get_org_sample_code['org_sample_code'];

                if(!empty($get_org_sample_code)){
                
                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                /*$sampledetails = $this->SampleInward->find('all',array('joins'=>array(array('table' => 'm_commodity_category','alias' => 'mcc','type' => 'INNER','conditions' => array( 'mcc.category_code = SampleInward.category_code')),array('table' => 'm_commodity','alias' => 'mc','type' => 'INNER','conditions' => array( 'mc.commodity_code = SampleInward.commodity_code')),                                      /// 
                //    array('table' => 'm_sample_type','alias' => 'mst','type' => 'INNER','conditions' => array( 'mst.sample_type_code = SampleInward.sample_type_code'))),'fields'=>array('mst.sample_type_desc','mc.commodity_name','mcc.category_name','mst.sample_type_code','mc.commodity_code','mcc.category_code'),'conditions'=>array('org_sample_code'=>$get_org_sample_code['org_sample_code'])))->first();*/         //
                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                
                $query = $conn->execute("SELECT mst.sample_type_desc,
                                    mc.commodity_name,
                                    mcc.category_name,
                                    mst.sample_type_code,
                                    mc.commodity_code,
                                    mcc.category_code 
                            FROM sample_inward AS si 
                            INNER JOIN m_commodity_category AS mcc ON mcc.category_code = si.category_code 
                            INNER JOIN m_commodity AS mc ON mc.commodity_code = si.commodity_code 
                            INNER JOIN m_sample_type AS mst ON mst.sample_type_code = si.sample_type_code 
                            WHERE org_sample_code = '$org_sample_code'");

                $sampledetails = $query->fetchAll('assoc');
            }
        }

        echo '~'.json_encode($sampledetails).'~';
        exit;
    }

/*******************************************************************************************************************************************************************************************************************************/

    public function checkUniqueTransIdForAppl(){
            
        //initialize model in component
        $this->loadModel('DmiGrantCertificatesPdfs');
        $this->loadModel('DmiFlowWiseTablesLists');
        $this->loadModel('DmiFirms');
        print_r($this->loadModel('LimsSamplePaymentDetails')); exit;

        $trans_id = $_POST['trans_id'];
        
        $new_customer_id = $this->Session->read('username');//currently applying applicant				
        $allow_id = 'yes';
        
        //temp static array, will be replaced by query result in phase 2
        //$payment_tables_array = array('Dmi_applicant_payment_detail','Dmi_renewal_applicant_payment_detail');
        
        //get all payment tables flow wise
        $payment_tables_array = $this->DmiFlowWiseTablesLists->find('all',array('fields'=>'payment','conditions'=>array('payment IS NOT'=>null)))->toArray();
        
        foreach($payment_tables_array as $each_table){
            
            $each_table = $each_table['payment'];
            $this->loadModel($each_table);
            
            //check new app if trans id already exist
            $check_trans_id = $this->$each_table->find('all',array('conditions'=>array('transaction_id IS'=>$trans_id),'order'=>'id desc'))->first();
            
            
            //for new 
            if(!empty($check_trans_id)){
                
                $old_customer_id = $check_trans_id['customer_id'];//applicant which already used this trans id.
                
                //check if appl with that trans id is granted already
                $check_grant = $this->DmiGrantCertificatesPdfs->find('all',
                    array('conditions'=>
                        array('OR'=>
                            array('customer_id IS'=>$old_customer_id)
                            ),'order'=>'id desc'))->first();
                            
                if(!empty($check_grant)){
                    
                    $allow_id = 'no';
                    break;
                
                }else{
                    
                    //if not grant then check primary id, district and firm type of the applicant
                    // The old applicant and current applicant should match the above 3 details.
                    //old details
                    $get_old_details = $this->DmiFirms->find('all',array('conditions'=>array('customer_id IS'=>$old_customer_id)))->first();
                    $old_primary_id = $get_old_details['customer_primary_id'];
                    $old_firm_type = $get_old_details['certification_type'];
                    $old_district_id = $get_old_details['district'];
                    
                    //new details
                    $get_new_details = $this->DmiFirms->find('all',array('conditions'=>array('customer_id IS'=>$new_customer_id)))->first();
                    $new_primary_id = $get_new_details['customer_primary_id'];
                    $new_firm_type = $get_new_details['certification_type'];
                    $new_district_id = $get_new_details['district'];
                    
                    if(($old_primary_id==$new_primary_id) && ($old_firm_type==$new_firm_type) && ($old_district_id==$new_district_id)){
                        
                        $allow_id = 'yes';
                    
                    }else{
                        $allow_id = 'no';
                        break;
                    }
                    
                }
                
            }
        }

        echo '~'.$allow_id.'~';
        exit;
    }

    /*******************************************************************************************************************************************************************************************************************************/
    /***
     * Made by Shweta Apale 22-10-2021
     * To get Category by From date & To date
     *  */ 
    public function getCategoryByDateArray()
    {
        $this->autoRender = false;

		$to_date = $_POST['to_date'];
		$from_date = $_POST['from_date'];
        $categories = $this->SampleInward->getCategoryByDate($to_date,$from_date);
		if (!empty($categories)) {
			echo "<option value = ''>Select </option>";
			foreach ($categories as $key => $each) {
				echo "<option value = '" . $key . "'>" . $each . "</option>";
			}
		}
        else{
            echo "<option value = ''> No Category Avaiable </option>";
        } 
    }

    /*******************************************************************************************************************************************************************************************************************************/
    /***
     * Made by Shweta Apale 22-10-2021
     * To get Commodity by Category
     *  */ 
    public function getCommodityByCategoryArray()
    {
        $this->autoRender = false;

		$category = $_POST['Category'];
        $commodities = $this->MCommodityCategory->getCommodityByCategory($category);
		if (!empty($commodities)) {
			echo "<option value = ''>Select </option>";
			foreach ($commodities as $key => $each) {
				echo "<option value = '" . $key . "'>" . $each . "</option>";
			}
		}
        else{
            echo "<option value = ''> No Commodity Avaiable </option>";
        } 
    }

    /*******************************************************************************************************************************************************************************************************************************/
    /***
     * Made by Shweta Apale 29-10-2021
     * To get Commodity by Date
     *  */ 
    public function getCommodityByDateArray()
    {
        $this->autoRender = false;

		$from_date = $_POST['from_date'];
        $to_date = $_POST['to_date'];
        $commodities = $this->MCommodityCategory->getCommodityByDate($from_date,$to_date);
		if (!empty($commodities)) {
			echo "<option value = ''>Select </option>";
			foreach ($commodities as $key => $each) {
				echo "<option value = '" . $key . "'>" . $each . "</option>";
			}
		}
        else{
            echo "<option value = ''> No Commodity Avaiable </option>";
        } 
    }

    /*******************************************************************************************************************************************************************************************************************************/
    /***
     * Made by Shweta Apale 02-11-2021
     * To get Sample Code by Commodity & Date
     *  */ 
    public function getSampleCodeByCommodityDateArray()
    {
        $this->autoRender = false;

		$from_date = $_POST['from_date'];
        $to_date = $_POST['to_date'];
        $commodity = $_POST['Commodity'];
        $ral_lab_list = $_POST['ral_lab_list'];
        $ral_lab = explode('~',$ral_lab_list);
        $ral_lab_no = $ral_lab[0];
        $ral_lab_name = $ral_lab[1];

        $sample_code = $this->SampleInward->getSampleCodeByCommodityDate($from_date,$to_date,$commodity,$ral_lab_no);
		if (!empty($sample_code)) {
			echo "<option value = ''>Select </option>";
			foreach ($sample_code as $key => $each) {
				echo "<option value = '" . $key . "'>" . $each . "</option>";
			}
		}else{
            echo "<option value = ''> No Sample Code Avaiable </option>";
        }  
    }

    /*******************************************************************************************************************************************************************************************************************************/
    /***
     * Made by Shweta Apale 10-11-2021
     * To get Ral Lab by Lab
     *  */ 
    public function getRallabByLabArray()
    {
        $this->autoRender = false;

		$lab = $_POST['lab'];
        $ral_lab = $this->DmiUsers->getRallabByLab($lab);
		if (!empty($ral_lab)) {
			echo "<option value = ''>Select </option>";
			foreach ($ral_lab as $key => $each) {
				echo "<option value = '" . $key . "' selected>" . $each . "</option>";
			}
		}
    }
	
    /*******************************************************************************************************************************************************************************************************************************/
    /***
     * Made by Shweta Apale 13-11-2021
     * To get Chemist Code by Date & Ral Lab
     *  */ 

    public function getChemistCodeByDateRalLabArray()
    {
        $this->autoRender = false;

		$from_date = $_POST['from_date'];
        $to_date = $_POST['to_date'];
        $ral_lab_list = $_POST['ral_lab_list'];
        $ral_lab = explode('~',$ral_lab_list);
        $ral_lab_no = $ral_lab[0];

        $chemist_code = $this->MSampleAllocate->getChemistCodeByDateRalLab($from_date, $to_date, $ral_lab_no);
		if (!empty($chemist_code)) {
			echo "<option value = ''>Select </option>";
			foreach ($chemist_code as $key => $each) {
				echo "<option value = '" . $key . "'>" . $each . "</option>";
			}
		}
        else{
            echo "<option value = ''> No Chemist Code Avaiable </option>";
        }
    }

    /*******************************************************************************************************************************************************************************************************************************/
    /***
     * Made by Shweta Apale 13-11-2021
     * To get Sample Code by Date & Chemist Code
     *  */ 

    public function getSampleCodeByDateChemistArray()
    {
        $this->autoRender = false;

		$from_date = $_POST['from_date'];
        $to_date = $_POST['to_date'];
        $chemist_code = $_POST['chemist_code'];

        $sample_code = $this->MSampleAllocate->getSampleCodeByDateChemist($from_date, $to_date, $chemist_code);
		if (!empty($sample_code)) {
			echo "<option value = ''>Select </option>";
			foreach ($sample_code as $key => $each) {
				echo "<option value = '" . $key . "'>" . $each . "</option>";
			}
		}
        else{
            echo "<option value = ''> No Sample Code Avaiable </option>";
        }
    }

    /*******************************************************************************************************************************************************************************************************************************/
    /***
     * Made by Shweta Apale 18-11-2021
     * To get User by Ral Lab
     *  */ 

    public function getUserByRalLabArray()
    {
        $this->autoRender = false;

		$lab = $_POST['lab'];
        $ral_lab_list = $_POST['ral_lab_list'];
        $ral_lab = explode('~',$ral_lab_list);
        $ral_lab_no = $ral_lab[0];

        $user = $this->DmiUsers->getUserByRalLab($lab, $ral_lab_no);
		if (!empty($user)) {
			echo "<option value = ''>Select </option>";
			foreach ($user as $key => $each) {
				echo "<option value = '" . $key . "'>" . $each . "</option>";
			}
		}
        else{
            echo "<option value = ''> No Users Avaiable </option>";
        }
  
    }
	


/*******************************************************************************************************************************************************************************************************************************/

	//user dashboard search Sample ajax function
	//added on 05-11-2021 by Amol
	public function searchSample(){

		$username = $this->Session->read('username');
		$user_code = $this->Session->read('user_code');
		$sample_code = $_POST['sample_code'];
		$this->loadModel('DmiUserRoles');
		$this->loadModel('Workflow');
		$check_user_role = $this->DmiUserRoles->find('all',array('conditions'=>array('user_email_id IS'=>$username)))->first();

		$show_details ='no';
		//check if the user is super admin
		if($check_user_role['super_admin']=='yes'){			
			$show_details ='yes';
		}else{
			//check if the sample code belongs to the user
			$mapUserSample = $this->Workflow->find('all',array('fields'=>'org_sample_code','conditions'=>array('dst_usr_cd'=>$user_code,'stage_smpl_cd'=>$sample_code)))->first();
		
            if(!empty($mapUserSample)){
				$show_details ='yes';
			}
		}
		
		$sampleInward = array();
		$location = '';
		$commodity = '';
		$SampleType = '';
		$lastAction = '';
		
		//check sample details
		if($show_details=='yes'){
           
			$org_smpl_cd = $mapUserSample['org_sample_code'];
			$this->loadModel('SampleInward');
			$sampleInward = $this->SampleInward->find('all',array('fields'=>array('loc_id','commodity_code','sample_type_code')))->first();
			
			if(!empty($sampleInward)){
				//get location from loc id
				$this->loadModel('DmiRoOffices');
				$office = $this->DmiRoOffices->find('all',array('fields'=>'ro_office','conditions'=>array('id'=>$sampleInward['loc_id'])))->first();
				$location = $office['ro_office'];
				
				//get commodity name
				$this->loadModel('MCommodity');
				$getCommodity = $this->MCommodity->find('all',array('fields'=>'commodity_name','conditions'=>array('commodity_code'=>$sampleInward['commodity_code'])))->first();
				$commodity = $getCommodity['commodity_name'];
				
				//get Sample Type
				$this->loadModel('MSampleType');
				$getSampleType = $this->MSampleType->find('all',array('fields'=>'sample_type_desc','conditions'=>array('sample_type_code'=>$sampleInward['sample_type_code'])))->first();
				$SampleType = $getSampleType['sample_type_desc'];
				
				//get last action of the sample
				$stageFlagArray = array('SI'=>'Sample Inward',
										'SD'=>'Sample Details',
										'OF'=>'Forwarded to Inward Officer',
										'Accepted by Inward Officer',
										'IF'=>'Forwarded to Inward Officer',
										'HF'=>'Head office to Inward Officer',
										'HS'=>'Accepted by HO',
										'LI'=>'Allocated by Lab_Incharge',
										'RIF'=>'Forwarded for Retest',
										'R'=>'Marked for Retest',
										'FR'=>'Forwarded to RAL',
										'AR'=>'Approved Results by Inward Officer',
										'FO'=>'Forwarded to OIC',
										'FG'=>'Final Graded by OIC',
										'TA'=>'Allocated to Chemist',
										'FS'=>'Forward back to RAL',
										'FC'=>'Forward back to CAL',
										'FGIO'=>'Final Graded by Inward Officer',
										'VC'=>'Sample Verified',
										'VS'=>'Sample Verified');
									
				$getlastFlag = $this->Workflow->find('all',array('fields'=>'stage_smpl_flag','conditions'=>array('org_sample_code'=>$org_smpl_cd),'order'=>'id desc'))->first();
				$lastAction = $stageFlagArray[trim($getlastFlag['stage_smpl_flag'])];
			}
			
		}else{
			
		}
		

		if(!empty($sampleInward)){

			echo "<table class='table' border='1'>
				<thead class='tablehead'>
					<tr>
						<th>Sample Code</th>
						<th>Sample Type</th>
						<th>Commodity</th>
						<th>Location</th>
						<th>Last Action</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>".$sample_code."</td>
						<td>".$SampleType."</td>
						<td>".$commodity."</td>
						<td>".$location."</td>
						<td>".$lastAction."</td>
					</tr>
				</tbody>
			</table>";

		}else{
			echo "<p class='alert alert-danger'>The Sample code does not exist OR The Sample does not belongs to you</p>";
		}
		
		exit;
	}


/*******************************************************************************************************************************************************************************************************************************/

    public function checkOldPassword()
    {
        $this->autoRender = false;
		$this->loadModel('DmiUsers');

        $username = $this->Session->read('username');
		$get_password = $_POST['Oldpassword'];
        $oldPassword = hash('sha512',$get_password);

		$checkDatabasePassword = $this->DmiUsers->find()->select(['password'])->where(['email IS' => $username])->first();
        
        if (!empty($checkDatabasePassword)) {

            $existedPassword = $checkDatabasePassword['password'];

            if ($oldPassword != $existedPassword) {
                echo 'yes';
            } else {
                echo 'no';
            }
        }

		exit;

    }


}
?>