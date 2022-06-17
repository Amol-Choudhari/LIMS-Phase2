<?php
namespace App\Controller;

use Cake\Event\Event;
use App\Network\Email\Email;
use Cake\ORM\Entity;
use Cake\Datasource\ConnectionManager;
use Cake\Http\Client\Request;
use Cake\View;
use Cake\ORM\TableRegistry;
use PHPStan\PhpDocParser\Ast\Type\ThisTypeNode;

class MasterController extends AppController {
	
	var $name = 'Master';

	//initialize components and models
	public function initialize(): void{
		parent::initialize();

		//Initialize Layouts & Helpers
		$this->viewBuilder()->setHelpers(['Form','Html']);
		$this->viewBuilder()->setLayout('admin_dashboard');

		//Load Components
		$this->loadComponent('Customfunctions');
		$this->loadComponent('Inputvalidation');

		//Load Models
		$this->loadModel('MCommodityCategory');
		$this->loadModel('MCommodity');
		$this->loadModel('CommodityTest');
		$this->loadModel('SampleInward');
		$this->loadModel('MasterModules');
		$this->loadModel('TestFields');
		$this->loadModel('TestFormula');
		$this->loadModel('MSampleObs');
		$this->loadModel('MSampleObsType');
	}

/******************************************************************************************************************************************************************************************************************************************************/	
	

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


/******************************************************************************************************************************************************************************************************************************************************/	

	public function codeMasterHome(){
		//for display
	}

/******************************************************************************************************************************************************************************************************************************************************/	

	public function referenceMasterHome(){
		//for display
	}

/******************************************************************************************************************************************************************************************************************************************************/	

	//List All Categories
	public function savedCategory() {

		$this->authenticateUser();
		$this->loadModel('MCommodityCategory');
		$categoryArray = $this->MCommodityCategory->find('all', array('fields'=> array('category_code', 'category_name', 'l_category_name', 'min_quantity'), 'order'=> array('category_name'=>'ASC'), 'conditions'=> array('display'=>'Y')));
		$this->set('categoryArray', $categoryArray);

	}

/******************************************************************************************************************************************************************************************************************************************************/	

	//Adding New Category
	public function newCategory(){

		// clear session category variables
		$this->Session->delete('category_code');
		$this->Session->delete('category_data');
		$this->redirect('/Master/category');

	}

/******************************************************************************************************************************************************************************************************************************************************/	


	//Edit Category Record
	public function fetchCategory($id){
		
		$this->loadModel('MCommodityCategory');
		$category_data = $this->MCommodityCategory->find('all', array('fields'=> array('category_code', 'category_name', 'l_category_name', 'min_quantity'), 'conditions'=> array('category_code'=>$id)))->first();
		$this->Session->write('category_code', $id);
		$this->Session->write('category_data', $category_data);
		$this->redirect('/Master/category');

	}


/******************************************************************************************************************************************************************************************************************************************************/	

	//Add / Update Commodity Category
	public function category(){

		$this->authenticateUser();
		
		// load records if session is set
		if ($this->Session->read('category_data')!=null) {

			$this->set('category_data', $this->Session->read('category_data'));
		}

		if ($this->request->is('post')) {

			$postData = $this->request->getData();
			
			// html encode the each post inputs
			
			foreach ($postData as $key => $value) {

				$postData[$key] = htmlentities($postData[$key], ENT_QUOTES);
			}
			
			// check post data validation
			$validate_err = $this->Inputvalidation->categoryPostValidations($this->request->getData());

			if ($validate_err != '') {

				$this->set('validate_err', $validate_err);
				return null;
			}

			// saving new record
			if (null !==($this->request->getData('save'))) {

				// check duplicate category validation
				$category_name_upper = strtoupper(trim($this->request->getData('category_name')));

				$isExist = $this->MCommodityCategory->find('all', array('conditions'=> array('UPPER(TRIM(category_name))'=>$category_name_upper, 'display'=>'Y')))->count();
				
					if ($isExist!='0') {

						$category_nm = $this->request->getData('category_name');
						$this->set('message',  $category_nm . ' record already exist, Please contact administrator to delete it!');
						$this->set('redirect_to', 'category');
						return null;
					}

					$categoryInputData = array('category_name'=>$postData['category_name'],'l_category_name'=>$postData['l_category_name'],'min_quantity'=>$postData['min_quantity']);
					$categoryEntity = $this->MCommodityCategory->newEntity($categoryInputData);
					$recordPush = $this->MCommodityCategory->save($categoryEntity);

					if ($recordPush) {

						$message = 'Successfully added new category!';
						$redirect_to = 'saved_category';

					} else {

						$message = 'Problem in saving new category, try again later!';
						$redirect_to = 'saved_category';
					
					}

				// set variables to show popup messages FROM view file
				$this->set('message', $message);
				$this->set('redirect_to', $redirect_to);
			}

			// updating record
			if (null !==($this->request->getData('update'))) {

				// check duplicate category validation
				$category_name_upper = strtoupper(trim($this->request->getData('category_name')));
				$category_code_post = $this->request->getData('category_code');
				$isExist = $this->MCommodityCategory->find('all', array('conditions'=> array('UPPER(TRIM(category_name))'=>$category_name_upper, 'category_code !='=>$category_code_post, 'display'=>'Y')))->count();
				
				if ($isExist!='0') {

					$category_nm = $this->request->getData('category_name');
					//$this->view = '/Element/message_boxes';
					$this->set('message',  $category_nm . ' record already exist, Please contact administrator to delete it!');
					$this->set('redirect_to', 'category');
					return null;
				}

				$categoryModifiedData = array(
					'category_code'=>$postData['category_code'],
					'category_name'=>$postData['category_name'],
					'l_category_name'=>$postData['l_category_name'],
					'min_quantity'=>$postData['min_quantity']
				);

				$categoryEntity = $this->MCommodityCategory->newEntity($categoryModifiedData);
				$recordUpdate = $this->MCommodityCategory->save($categoryEntity);

				if ($recordUpdate) {

					$message = 'Successfully update category!';
					$redirect_to = 'saved_category';
				
				} else {

					$message = 'Problem in updation, try again later!';
					$redirect_to = 'saved_category';
				}

			// set variables to show popup messages FROM view file
			$this->set('message', $message);
			$this->set('redirect_to', $redirect_to);
			}

		}

	}


/******************************************************************************************************************************************************************************************************************************************************/	


	// delete commodity category record
	public function deleteCategory($id){

		$this->autoRender = false;		
		// checking category code already in use or not
		$inUsed = $this->MCommodity->find('all', array('conditions'=> array('category_code IS'=>$id, 'display'=>'Y')))->count();

		if ($inUsed!='0') {
				$message = 'Already in use, could not deleted!';
				$redirect_to = '../saved_category';
		} else {

			$categoryRecord = array('category_code'=>$id, 'display'=>'N');
			$categoryEntity = $this->MCommodityCategory->newEntity($categoryRecord);
			$recordDelete = $this->MCommodityCategory->save($categoryEntity);

			if ($recordDelete) {

				$message = 'Successfully delete category!';
				$message_theme = 'success';
				$redirect_to = '../saved_category';
			
			} else {

				$message = 'Problem in deleting, try again later!';
				$message_theme = 'failed';
				$redirect_to = '../saved_category';
			}
		}

			// set variables to show popup messages FROM view file
			$this->set('message', $message);
			$this->set('message_theme',$message_theme);
			$this->set('redirect_to', $redirect_to);
	}


/******************************************************************************************************************************************************************************************************************************************************/	

	// list all commodities
	public function savedCommodity(){

		$this->authenticateUser();

		$conn = ConnectionManager::get('default');
		$query = $conn->execute("SELECT com.commodity_name AS commodity_name, com.l_commodity_name AS l_commodity_name, cat.category_name AS category_name, com.commodity_code AS commodity_code FROM m_commodity_category AS cat, m_commodity AS com WHERE cat.category_code=com.category_code AND com.display='Y' ORDER BY com.commodity_name ASC");
		$query_result = $query->fetchAll('assoc');
		$this->set('commodityArray', $query_result);

	}


/******************************************************************************************************************************************************************************************************************************************************/	

	// add new commodity
	public function newCommodity(){

		// clear session category variables
		$this->Session->delete('commodity_code');
		$this->Session->delete('commodity_data');
		$this->redirect('/Master/commodity');

	}


/******************************************************************************************************************************************************************************************************************************************************/	

	// fetch commodity record on update page
	public function fetchCommodity($id){

		$commodity_data = $this->MCommodity->find('all', array('conditions'=> array('commodity_code IS'=>$id)))->first();
		//print_r($commodity_data); exit;

		$this->Session->write('commodity_code', $id);
		$this->Session->write('commodity_data', $commodity_data);
		$this->redirect('/Master/commodity');

	}


/******************************************************************************************************************************************************************************************************************************************************/	

	// add / update commodity
	public function commodity(){

		$this->authenticateUser();

		// load records if session is set
		if ($this->Session->read('commodity_data')!=null) {

			$this->set('commodity_data', $this->Session->read('commodity_data'));
		}

		// list of commodity categories
		$categoryArray = $this->MCommodityCategory->find('all', array('conditions'=> array('display'=>'Y'), 'order'=> array('category_name'=>'ASC')));
		$this->set('categoryArray', $categoryArray);

		if ($this->request->is('post')) {
				
			$postData = $this->request->getData();
			
			// html encode the each post inputs
			foreach ($postData as $key => $value) {

				$postData[$key] = htmlentities($postData[$key], ENT_QUOTES);
			}

			// check post data validation
			$validate_err = $this->Inputvalidation->commodityPostValidations($postData);
			
			if ($validate_err != '') {

				$this->set('validate_err', $validate_err);
				return null;
			}

			
			// saving new record
			if (null !==($this->request->getData('save'))) {

				// check duplicate commodity validation
				$commodity_name_upper = strtoupper(trim($postData['commodity_name']));
				$isExist = $this->MCommodity->find('all', array('conditions'=> array('UPPER(TRIM(commodity_name))'=>$commodity_name_upper, 'display'=>'Y')))->count();
				
				if ($isExist!='0') {

					$commodity_nm = $postData['commodity_name'];
					//$this->view = '/Element/message_boxes';
					$this->set('message',  $commodity_nm . ' record for this category already exists! Please Contact to Administrator');
					$this->set('redirect_to', 'commodity');
					return null;
				}

				$commodityRecord = array(
					'category_code'=>$postData['category_code'],
					'commodity_name'=>$postData['commodity_name'],
					'l_commodity_name'=>$postData['l_commodity_name']
				);
				
				$commodityEntity = $this->MCommodity->newEntity($commodityRecord);
				$recordPush = $this->MCommodity->save($commodityEntity);

				if ($recordPush) {

					$message = 'Successfully added new commodity!';
					$redirect_to = 'saved_commodity';
					//$this->view = '/Element/message_boxes';
				} else {
					
					$message = 'Problem in saving new commodity, try again later!';
					$redirect_to = 'saved_commodity';
					//$this->view = '/Element/message_boxes';
				}

			// set variables to show popup messages FROM view file
			$this->set('message', $message);
			$this->set('redirect_to', $redirect_to);
			}

			// update record
			if (null!==($this->request->getData('update'))) {

				// check duplicate commodity validation
				$commodity_name_upper = strtoupper(trim($postData['commodity_name']));
				$commodity_code_post = $postData['commodity_code'];
				$isExist = $this->MCommodity->find('all', array('conditions'=> array('UPPER(TRIM(commodity_name))'=>$commodity_name_upper, 'commodity_code !='=>$commodity_code_post, 'display'=>'Y')))->count();
				
				if ($isExist!='0') {

					$commodity_nm = $postData['commodity_name'];
					$this->set('message',  $commodity_nm . ' already exists !');
					$this->set('redirect_to', 'commodity');
					return null;
				}

				$commodityModifiedData = array(
					'commodity_code'=>$postData['commodity_code'],
					'category_code'=>$postData['category_code'],
					'commodity_name'=>$postData['commodity_name'],
					'l_commodity_name'=>$postData['l_commodity_name']
				);

				$commodityEntity = $this->MCommodity->newEntity($commodityModifiedData);
				$recordUpdate = $this->MCommodity->save($commodityEntity);

				if ($recordUpdate) {

					$message = 'Successfully update commodity!';
					$redirect_to = 'saved_commodity';
				} else {
					
					$message = 'Problem in updation, try again later!';
					$redirect_to = 'saved_commodity';
				}

			// set variables to show popup messages FROM view file
			$this->set('message', $message);
			$this->set('redirect_to', $redirect_to);
			}

		}

	}


/******************************************************************************************************************************************************************************************************************************************************/	

	// delete commodity record
	public function deleteCommodity($id){

		// check commodity code already in use
		$inUsedTest = $this->CommodityTest->find('all', array('conditions'=> array('commodity_code IS'=>$id, 'display'=>'Y')))->count();
		$inUsedSampleInward = $this->SampleInward->find('all', array('conditions'=> array('commodity_code IS'=>$id, 'display'=>'Y')))->count();
		
		if ($inUsedTest!='0' || $inUsedSampleInward!='0') {
			
			$message = 'Commodity already in used, could not be deleted';
			$redirect_to = '../saved_commodity';
		
		} else {

			$commodityRecord = array('commodity_code'=>$id, 'display'=>'N');
			$commodityEntity = $this->MCommodity->newEntity($commodityRecord);
			$recordDeleted = $this->MCommodity->save($commodityEntity);

			if ($recordDeleted) {

				$message = 'Successfully deleted commodity!';
				$redirect_to = '../saved_commodity';
			
			} else {

				$message = 'Problem in deleting, try again later!';
				$redirect_to = '../saved_commodity';
			}

		}

	// set variables to show popup messages FROM view file
	$this->set('message', $message);
	$this->set('redirect_to', $redirect_to);
	
	}


/******************************************************************************************************************************************************************************************************************************************************/	


	// list all phy appear modules
	public function savedPhyAppear($module){

		$this->authenticateUser();
		// current phy appear module
		$phy_appear_record = $this->MasterModules->find('all', array('conditions'=> array('action'=>$module)))->first();
		$this->set('phyAppear', $phy_appear_record);

		$textbox_1 = $phy_appear_record['textbox_1'];
		$table = $phy_appear_record['table_name'];
		//print_r($table); exit;

		// all records under SELECTed module
		$this->loadModel($table);
		$phyAppearArray = $this->$table->find('all', array('conditions'=> array('display'=>'Y'), 'order'=> array($textbox_1=>'ASC')));
	
		$this->set('phyAppearArray', $phyAppearArray);
		
	}


/******************************************************************************************************************************************************************************************************************************************************/	

	// render add new page for phy appear
	public function newPhyAppear($module){

		// clear phy appear session variables
		$this->Session->delete('phy_appear_module');
		$this->Session->delete('phy_appear_code');
		$this->Session->delete('phy_appear_data');

		$this->redirect('/Master/phy-appear/' . $module);

	}


/******************************************************************************************************************************************************************************************************************************************************/	

	// fetch single record on update page
	public function fetchPhyAppear($code_name, $table_name, $code){

		$module_data = $this->MasterModules->find('all', array('conditions'=> array('table_name'=>$table_name)))->first();
		$action = $module_data['action'];

		$this->loadModel($table_name);
		$phy_appear_data = $this->$table_name->find('all', array('conditions'=> array($code_name=>$code)))->first();

		$this->Session->write('phy_appear_module', $module_data);
		$this->Session->write('phy_appear_code', $code);
		$this->Session->write('phy_appear_data', $phy_appear_data);

		$this->redirect('/Master/phy-appear/' . $action);

	}


/******************************************************************************************************************************************************************************************************************************************************/	

	// add / update all phy appear modules
	public function phyAppear($module){

		$this->authenticateUser();
		// load records if session is set
		if ($this->Session->read('phy_appear_data')!=null) {

			$this->set('phy_appear_data', $this->Session->read('phy_appear_data'));
			$this->set('phy_appear_module', $this->Session->read('phy_appear_module'));
		}

		// current phy appear module
		$phy_appear_record = $this->MasterModules->find('all', array('conditions'=> array('action'=>$module)))->first();
		$this->set('phyAppear', $phy_appear_record);

		if ($this->request->is('post')) {
			
			$postData = $this->request->getData();

			// html encode the each post inputs
			foreach ($postData as $key => $value) {

				$postData[$key] = htmlentities($postData[$key], ENT_QUOTES);
			}

			// check post data validation
			$validate_err = $this->Inputvalidation->phyAppearPostValidations($this->request->getData());
			
			if ($validate_err != '') {

				$this->set('validate_err', $validate_err);
				return null;
			}

			$postData = $this->request->getData();
			$textbox_1 = $postData['textbox_1'];
			$textbox_2 = $postData['textbox_2'];
			$table = $postData['table_name'];
			$action = $postData['action'];
			$title = $postData['title'];
			$phy_appear_code = $postData['phy_appear_code'];
			$phy_appear_code_name = $postData['phy_appear_code_name'];
			$user_code = $this->Session->read('user_code');

			$this->loadModel($table);
			// save new record
			if (null!==($this->request->getData('save'))) {

				// check duplicated phy appear name
				$textbox_one_upper = strtoupper(trim($postData[$textbox_1]));
				$isExist = $this->$table->find('all', array('conditions'=> array('UPPER(TRIM('.$textbox_1.'))'=>$textbox_one_upper, 'display'=>'Y')))->count();
				
				if ($isExist!='0') {

					$textbox_one_nm = $postData[$textbox_1];
					$this->set('message', 'Record for this ' . $textbox_one_nm . ' already exists');
					$this->set('redirect_to', '../phy-appear/' .  $action);
					return null;
				}

					$phyAppearData = array(
					$textbox_1=>$postData[$textbox_1],
					$textbox_2=>$postData[$textbox_2],
					'user_code'=>$user_code
				);

				$phyAppearEntity = $this->$table->newEntity($phyAppearData);
				$recordPush = $this->$table->save($phyAppearEntity);

				if ($recordPush) {

					$message = 'Successfully added new ' . $title . ' record!';
					$redirect_to = '../saved-phy-appear/' . $action;
				
				} else {

					$message = 'Problem in saving ' . $title . ', try again later!';
					$redirect_to = '../saved-phy-appear/' . $action;
				
				}

			// set variables to show popup messages FROM view file
			$this->set('message', $message);
			$this->set('redirect_to', $redirect_to);
			
			}

			// update record
			if (null!==($postData['update'])) {

				// check isEditable status of current record
				if ($table=='m_test_type') { 
					
					$isEditableStatus = $this->$table->find('all', array('conditions'=> array($phy_appear_code_name=>$phy_appear_code, 'iseditable'=>'N')))->count();
					
					if ($isEditableStatus!='0') {

						$textbox_one_nm = $postData[$textbox_1];
						$this->set('message', 'Not allow to modify ' . $textbox_one_nm . ' record');
						$this->set('redirect_to', '../saved-phy-appear/' .  $action);
						return null;
					}
				}

				// check duplicate entry for  phy appear name
				$textbox_one_upper = strtoupper(trim($postData[$textbox_1]));
				
				$isExist = $this->$table->find('all', array('conditions'=> array('UPPER(TRIM('.$textbox_1.'))'=>$textbox_one_upper, $phy_appear_code_name.' !='=>$phy_appear_code, 'display'=>'Y')))->count();
				
				if ($isExist!='0') {

					$textbox_one_nm = $postData[$textbox_1];
					$this->set('message', 'Record for this ' . $textbox_one_nm . ' already exists');
					$this->set('redirect_to', '../phy-appear/' .  $action);
					return null;
				}

				$phyAppearModifiedData = array(
					$phy_appear_code_name=>$phy_appear_code,
					$textbox_1=>$postData[$textbox_1],
					$textbox_2=>$postData[$textbox_2],
					'user_code'=>$user_code
				);

				$phyAppearEntity = $this->$table->newEntity($phyAppearModifiedData);
				$recordUpdate = $this->$table->save($phyAppearEntity);

				if ($recordUpdate) {

					$message = 'Successfully update ' . $title . '!';
					$redirect_to = '../saved-phy-appear/' . $action;
				
				} else {

					$message = 'Problem in updation, try again later!';
					$redirect_to = '../saved-phy-appear/' . $action;
				}

			// set variables to show popup messages from view file
			$this->set('message', $message);
			$this->set('redirect_to', $redirect_to);
			}

		}

	}


/******************************************************************************************************************************************************************************************************************************************************/	


	// delete phy appear modules record
	public function deletePhyAppear($code_name, $table, $action, $code){

		$this->autoRender=false;
		// check phy appear already in use or not
		$inUsed = '0';
		$this->loadModel($table);
		
		if ($table == 'm_fields') {

			$inUsed = $this->TestFields->find('all', array('conditions'=> array('field_code'=>$code)))->count();
		
		} elseif ($table == 'm_MTestMethod') {

			$inUsed = $this->TestFormula->find('all', array('conditions'=> array('method_code'=>$code, 'display'=>'Y')))->count();
		}

		if ($inUsed!='0') {

			$message = 'Record already in use, could not be deleted!';
			$redirect_to = '../../../../saved-phy-appear/' . $action;

		} else {

			$phyAppearRecord = array($code_name=>$code, 'display'=>'N');

			$phyAppearEntity = $this->$table->newEntity($phyAppearRecord);
			$recordDeleted = $this->$table->save($phyAppearEntity);

			if ($recordDeleted) {

				$message = 'Successfully delete record!';
				$redirect_to = '../../../../saved-phy-appear/' . $action;

			} else {

				$message = 'Problem in deleting, try again later!';
				$redirect_to = '../../../../saved-phy-appear/' . $action;

			}

		}

		// set variables to show popup messages from view file
		$this->set('message', $message);
		$this->set('redirect_to', $redirect_to);
	}


/******************************************************************************************************************************************************************************************************************************************************/	

	// list all homogenization values
	public function savedHomoValue(){

		$this->authenticateUser();

		$conn = ConnectionManager::get('default');
		$homo_value_data = $conn->execute("SELECT so.m_sample_obs_code, 
												  so.m_sample_obs_type_code, 
												  obs.m_sample_obs_desc, 
												  so.m_sample_obs_type_value 
										   FROM m_sample_obs_type AS so 
										   INNER JOIN m_sample_obs AS obs ON so.m_sample_obs_code=obs.m_sample_obs_code 
										   WHERE so.display='Y'");
		
		$homoValueArray = $homo_value_data->fetchAll('assoc');

		$this->set('homoValueArray', $homoValueArray);

	}

/******************************************************************************************************************************************************************************************************************************************************/	


	// add new homogenization
	public function newHomoValue(){

		/* clear previous homo value session variables */
		$this->Session->delete('homo_value_code'); 
		$this->Session->delete('homo_value_data');
		$this->redirect('/Master/homo-value');

	}


/******************************************************************************************************************************************************************************************************************************************************/	


	// fetch homo value record on update page
	public function fetchHomoValue($code){

		$conn = ConnectionManager::get('default');
		
		$homo_value_record = $conn->execute("SELECT so.m_sample_obs_code, 
													so.m_sample_obs_type_code, 
													so.m_sample_obs_type_value, 
													obs.m_sample_obs_desc 
											 FROM m_sample_obs_type AS so, m_sample_obs AS obs 
											 WHERE so.m_sample_obs_code=obs.m_sample_obs_code 
											 AND so.m_sample_obs_type_code='$code' 
											 AND so.display='Y' 
											 LIMIT 1");
		
		$homo_value_data = $homo_value_record->fetch('assoc');

		$this->Session->write('homo_value_code', $code);
		$this->Session->write('homo_value_data', $homo_value_data);
		$this->redirect('/Master/homo-value');

	}



/******************************************************************************************************************************************************************************************************************************************************/	



	// add / update homogenization values
	public function homoValue(){

		$this->authenticateUser();
		error_reporting('0'); // need to uncomment in production mode

		// load records if session is set
		if ($this->Session->read('homo_value_data')!=null) {
			
			$this->set('homo_value_code', $this->Session->read('homo_value_code'));
			$this->set('homo_value_data', $this->Session->read('homo_value_data'));

		}

		// list of all homogenization values
		$homoValueArray = $this->MSampleObs->find('all', array('conditions'=> array('display'=>'Y'), 'order'=> array('m_sample_obs_code'=>'DESC')));
		$this->set('homoValueArray', $homoValueArray);

		if ($this->request->is('post')) {

				$postData = $this->request->getData();

				// html encode the each post inputs
				foreach ($postData as $key => $value) {

					$postData[$key] = htmlentities($postData[$key], ENT_QUOTES);
				}


			// check post data validation
			$validate_err = $this->Inputvalidation->homoValuePostValidations($postData);

				if ($validate_err != '') {
					
					$this->set('validate_err', $validate_err);
					return null;
				}

				$user_code = $this->Session->read('user_code');
				$val_type = $postData['val_type'];
				$old_val_type = $postData['old_val_type'];
				$m_sample_obs_type_code = $postData['m_sample_obs_type_code'];
				$m_sample_obs_code = $postData['m_sample_obs_code'];
				$m_sample_obs_type_value = $postData['m_sample_obs_type_value'];

				// saving new record
			if (null!==($postData['save'])) {

					//to check if same homo with same value exist, if yes then validate
					$existCount = $this->MSampleObsType->find('all', array('conditions'=> array('m_sample_obs_code'=>$m_sample_obs_code, 'm_sample_obs_type_value'=>$m_sample_obs_type_value, 'display'=>'Y')))->count();

					if ($existCount == '0') {

						// get count record exist for the SELECTed Homo
						$for_value_type = $this->MSampleObsType->find('all', array('conditions'=> array('m_sample_obs_code IS'=>$m_sample_obs_code, 'display'=>'Y')))->first();

						if ($for_value_type['m_sample_obs_type_value']=='Yes') {

							$existed_value_type = 'yesno';
						
						} else {

							$existed_value_type = 'single';
						}

						if ($val_type != $existed_value_type) {

							$this->set('message',  'Sorry. The Selected value type do not matched with previous records.');
							$this->set('redirect_to', 'saved-homo-value');
							return null;
						}

							if ($val_type=='single') {

								$homoValueRecord = array(
									'm_sample_obs_code'=>$m_sample_obs_code,
									'm_sample_obs_type_value'=>$m_sample_obs_type_value,
									'user_code'=>$user_code,
									'login_timestamp'=>$postData['login_timestamp']
								);

								$homoValueEntity = $this->MSampleObsType->newEntity($homoValueRecord);
								$recordPush = $this->MSampleObsType->save($homoValueEntity);

							} else {

								$homoValueRecordOne = array(
									'm_sample_obs_code'=>$m_sample_obs_code,
									'm_sample_obs_type_value'=>'Yes',
									'user_code'=>$user_code,
									'login_timestamp'=>$postData['login_timestamp']
								);

								$homoValueRecordTwo= array(
									'm_sample_obs_code'=>$m_sample_obs_code,
									'm_sample_obs_type_value'=>'No',
									'user_code'=>$user_code,
									'login_timestamp'=>$postData['login_timestamp']
								);

								$homoValueEntityOne = $this->MSampleObsType->newEntity($homoValueRecordOne);
								$homoValueEntityTwo = $this->MSampleObsType->newEntity($homoValueRecordTwo);
								$recordPush = $this->MSampleObsType->save($homoValueEntityOne);
								$recordPushTwo = $this->MSampleObsType->save($homoValueEntityTwo);

							}

							if ($recordPush) {
								
								$message = 'Successfully added new homogenization value!';
								$redirect_to = 'saved-homo-value';
							} else {
								$message = 'Problem in saving homogenization value, try again later!';
								$redirect_to = 'saved-homo-value';
				
							}
					} else {

							$message = 'Record Already Exists';
							$redirect_to = 'saved-homo-value';
					

					}

				// set variables to show popup messages from view file
				$this->set('message', $message);
				$this->set('redirect_to', $redirect_to);
				
			}

			// update record
			if (null!==($postData['update'])) {

				// check duplicate homo values in same homogenization field
				$homo_value_upper = strtoupper(trim($m_sample_obs_type_value));

				$isExist = $this->MSampleObsType->find('all', array('conditions'=> array('UPPER(TRIM(m_sample_obs_type_value))'=>$homo_value_upper, 'm_sample_obs_code'=>$m_sample_obs_code, 'm_sample_obs_type_code !='=>$m_sample_obs_type_code, 'display'=>'Y')))->count();
				
				if ($isExist!='0') {
		
					$this->set('message',  'Homogenization value already exists !');
					$this->set('redirect_to', 'homo-value');
					return null;
				}

					if ($old_val_type != $val_type) {

						// disable previous value type under same homo field, added on 17-09-2020 by Aniket
						$MSampleObsType = TableRegistry::getTableLocator()->get('MSampleObsType');
						$deactivePreviousValType = $MSampleObsType->query();
						$deactivePreviousValType->update()->set(array('display'=>'N'))->where(array('m_sample_obs_code'=>$m_sample_obs_code))->execute();

						if ($val_type=='yesno') {

							$homoValueRecordOne = array(
								'm_sample_obs_code'=>$m_sample_obs_code,
								'm_sample_obs_type_value'=>'Yes',
								'user_code'=>$user_code,
								'login_timestamp'=>$postData['login_timestamp']
							);

							$homoValueRecordTwo = array(
								'm_sample_obs_code'=>$m_sample_obs_code,
								'm_sample_obs_type_value'=>'No',
								'user_code'=>$user_code,
								'login_timestamp'=>$postData['login_timestamp']
							);

							$homoValueEntityOne = $this->MSampleObsType->newEntity($homoValueRecordOne);
							$homoValueEntityTwo = $this->MSampleObsType->newEntity($homoValueRecordTwo);
							$recordUpdated = $this->MSampleObsType->save($homoValueEntityOne);
							$recordUpdatedTwo = $this->MSampleObsType->save($homoValueEntityTwo);

						} else {

							$homoValueRecord = array(
								'm_sample_obs_code'=>$m_sample_obs_code,
								'm_sample_obs_type_value'=>$m_sample_obs_type_value,
								'user_code'=>$user_code,
								'login_timestamp'=>$postData['login_timestamp']
							);

							$homoValueEntity = $this->MSampleObsType->newEntity($homoValueRecord);
							$recordUpdated = $this->MSampleObsType->save($homoValueEntity);

						}

					} else {

						$homoValueModifiedData = array(
							'm_sample_obs_type_code'=>$m_sample_obs_type_code,
							'm_sample_obs_type_value'=>$m_sample_obs_type_value
						);

						$homoValueEntity = $this->MSampleObsType->newEntity($homoValueModifiedData);
						$recordUpdated = $this->MSampleObsType->save($homoValueEntity);

					}

				if ($recordUpdated) {
					
					$message = 'Successfully update homogenization value!';
					$redirect_to = 'saved-homo-value';
				
				} else {
					
					$message = 'Problem in updation of homogenization value, try again later!';
					$redirect_to = 'saved-homo-value';
				}

				// set variables to show popup messages from view file
				$this->set('message', $message);
				$this->set('redirect_to', $redirect_to);
			
			}

		}

	}


/******************************************************************************************************************************************************************************************************************************************************/	

	// delete homo value record
	public function deleteHomoValue($homo_type_code, $homo_code, $val_type, $homo_value){

		if ($val_type=='yesno') {

			// delete one of yes/no combo record
			$homoValueRecordOne = array('m_sample_obs_type_code'=>$homo_type_code, 'display'=>'N');
			$homoValueEntityOne = $this->MSampleObsType->newEntity($homoValueRecordOne);
			$recordDeleted = $this->MSampleObsType->save($homoValueEntityOne);

			// delete second of yes/no combo record
			$getSecondHomoCode = $this->MSampleObsType->find('all', array('conditions'=> array('m_sample_obs_type_value !='=>$homo_value, 'm_sample_obs_code'=>$homo_code, 'display'=>'Y')))->first();
			$homoValueRecordTwo = array('m_sample_obs_type_code'=>$getSecondHomoCode['m_sample_obs_type_code'], 'display'=>'N');
			$homoValueEntityTwo = $this->MSampleObsType->newEntity($homoValueRecordTwo);
			$recordDeletedTwo = $this->MSampleObsType->save($homoValueEntityTwo);

		} else {

			$homoValueRecord = array('m_sample_obs_type_code'=>$homo_type_code, 'display'=>'N');
			$homoValueEntity = $this->MSampleObsType->newEntity($homoValueRecord);
			$recordDeleted = $this->MSampleObsType->save($homoValueEntity);

		}

		if ($recordDeleted) {
			$message = 'Successfully deleted homogenization value!';
			$redirect_to = '../../../../saved-homo-value';
			//$this->view = '/Element/message_boxes';
		} else {
			$message = 'Problem in deleting, try again later!';
			$redirect_to = '../../../../saved-homo-value';
			//$this->view = '/Element/message_boxes';
		}

	// set variables to show popup messages from view file
	$this->set('message', $message);
	$this->set('redirect_to', $redirect_to);
	
	}


/******************************************************************************************************************************************************************************************************************************************************/	


	// list all assigned homogenization
	public function savedAssignHomo(){

		$this->authenticateUser();

		$conn = ConnectionManager::get('default');

		$assign_homos = $conn->execute("SELECT cat.category_name, com.commodity_name, so.m_sample_obs_desc,com.commodity_code
	 									FROM m_commodity_obs AS a 
										INNER JOIN m_commodity_category AS cat ON a.category_code=cat.category_code 
										INNER JOIN m_commodity AS com ON com.commodity_code=a.commodity_code 
										INNER JOIN m_sample_obs AS so ON so.m_sample_obs_code=a.m_sample_obs_code 
										WHERE a.display='Y'");

		$assign_homo_result = $assign_homos->fetchAll('assoc');
		//print_r($assign_homo_result); exit;
		$this->set('assignHomosArray', $assign_homo_result);

	}


/******************************************************************************************************************************************************************************************************************************************************/	

	// view page as add page
	public function newAssignHomo(){

		// clear session assign homo variables
		$this->Session->delete('assign_homo_code');
		$this->Session->delete('assign_homo_data');

		$this->redirect('/Master/assign-homo');

	}


/******************************************************************************************************************************************************************************************************************************************************/	

	// save or update assign homo
	public function assignHomo(){

		$this->authenticateUser();
		error_reporting('0');

		// getting categories
		$categories = $this->MCommodityCategory->find('all', array('conditions'=> array('display'=>'Y'), 'order'=> array('category_name'=>'ASC')));
		$this->set(compact('categories'));

		$homo_fields = $this->MSampleObs->find('all', array('conditions'=> array('display'=>'Y'), 'order'=> array('m_sample_obs_desc'=>'ASC')));
		$this->set(compact('homo_fields'));

	}


/******************************************************************************************************************************************************************************************************************************************************/	

	// getting commodities as per category code
	public function getCommodityFields(){

		$this->autoRender = false;
		$str="";
		$conn = ConnectionManager::get('default');
		$category_code = $_POST['category_code'];
		$invalid_option = "<option value=''>Wrong Category Code</option>";

		if (!is_numeric($category_code) || $category_code=='') {
			
			echo $invalid_option;
			exit;
		}

		if ($category_code==0) {

			$commodity_data = $conn->execute("SELECT mc.commodity_code, mc.commodity_name 
											  FROM m_commodity AS mc 
											  INNER JOIN sample_inward AS si ON si.commodity_code=mc.commodity_code 
											  INNER JOIN workflow AS w ON w.org_sample_code=si.org_sample_code 
											  GROUP BY mc.commodity_code, mc.commodity_name");
		} else {

			$commodity_data = $conn->execute("SELECT mc.commodity_code, mc.commodity_name 
											  FROM m_commodity AS mc 
											  WHERE mc.category_code=$category_code 
											  GROUP BY mc.commodity_code, mc.commodity_name");
		}

		$commodity_array = $commodity_data->fetchAll('assoc');

		foreach ($commodity_array as $commodity) {

			$commodity_code = $commodity['commodity_code'];

			$commodity_count = $conn->execute("SELECT cat.category_name, com.commodity_name,so.m_sample_obs_desc 
											   FROM m_commodity_obs a
											   INNER JOIN m_commodity_category AS cat on  a.category_code=cat.category_code
											   INNER JOIN m_commodity AS com on com.commodity_code=a.commodity_code
											   INNER JOIN m_sample_obs AS so on so.m_sample_obs_code=a.m_sample_obs_code
											   WHERE a.category_code='$category_code' 
											   AND a.commodity_code='$commodity_code'");

			$exist_commodity = $commodity_count->fetchAll('assoc');
			
			if ((count($exist_commodity)) == 0) {

				$status = '';
				$class = '';
			} else {

				$status = 'disabled';
				$class = 'opt-disable';
			}

			$str = $str . "<option value='" . $commodity['commodity_code'] . "' " . $status . " class='" . $class ."'>" . $commodity['commodity_name'] . "</option>";

		}

		echo $str;
		exit;

	}


/******************************************************************************************************************************************************************************************************************************************************/	


	public function fetchTest($id){

		$this->loadModel('MTest');
		$category_data = $this->MTest->find('all', array('fields'=> array('test_name', 'l_test_name'), 'conditions'=> array('test_code'=>$id)))->first();

		$this->Session->write('test_code', $id);
		$this->Session->write('category_data', $category_data);

		$this->redirect('/Master/saved_test_type');

	}


/******************************************************************************************************************************************************************************************************************************************************/	


	 public function savedTestType(){

		$this->authenticateUser();
		// current phy appear module
		$this->loadModel('MTest');
		$tests = $this->MTest->find('all', array('fields'=> array('test_name', 'l_test_name'), 'conditions'=> array('display'=>'Y')));
		//print_r($tests); exit;
		$this->set(compact('tests', $tests));
	 }


/******************************************************************************************************************************************************************************************************************************************************/	


	// delete commodity category record
	public function deleteTest($id){

		// checking category code already in use or not
		$inUsed = $this->Mtest->find('all', array('conditions'=> array('test_code IS'=>$id, 'display'=>'Y')))->count();
		
		if ($inUsed!='0') {

				$message = 'Already in use, could not deleted!';
				$redirect_to = '../saved_test_type';
		
		} else {

			$categoryRecord = array('test_code'=>$id, 'display'=>'N');

			$categoryEntity = $this->Mtest->newEntity($categoryRecord);
			$recordDelete = $this->Mtest->save($categoryEntity);

			if ($recordDelete) {

				$message = 'Successfully delete category!';
				$redirect_to = '../saved_test_type';

			} else {
		
				$message = 'Problem in deleting, try again later!';
				$redirect_to = '../saved_category';

			}
		}
	}


/******************************************************************************************************************************************************************************************************************************************************/	


	public function addtestFields(){

		// clear session category variables
		$this->Session->delete('category_code');
		$this->Session->delete('category_data');

		$this->redirect('/Master/category');

	}


/******************************************************************************************************************************************************************************************************************************************************/	


	public function testFields() {

		$this->authenticateUser();
		$this->loadModel('MFields');
		$testFields = $this->MFields->find('all', array('fields'=> array('field_name', 'l_field_name'), 'conditions'=> array('display'=>'Y')));
		//print_r($testFields); exit;
		$this->set(compact('testFields', $testFields));
	 }


/******************************************************************************************************************************************************************************************************************************************************/	

	 	public function commodityGrade(){

			$str1="";
			$this->loadModel('MCommodityCategory');
			$this->loadModel('MGradeDesc');
			$this->loadModel('CommGrade');
			$this->loadModel('MGradeStandard');
			$this->loadModel('MTestMethod');
			
		 	$commodity_category=$this->MCommodityCategory->find('all',array('order' => array('category_name' => 'ASC'),'conditions' => array('display' => 'Y')))->toArray();
			$this->set('commodity_category',$commodity_category);
			
			$grades=$this->MGradeDesc->find('all',array('order' => array('grade_desc' => 'ASC'),'conditions' => array('display' => 'Y')))->toArray();
		 	$this->set('grades',$grades);				

			$grades_strd=$this->MGradeStandard->find('all',array('order' => array('grade_strd_desc' => 'ASC'),'conditions' => array('display' => 'Y')))->toArray();
		 	$this->set('grades_strd',$grades_strd);
		 
			$methods=$this->MTestMethod->find('all',array('order' => array('method_name' => 'ASC'),'conditions' => array('display' => 'Y')))->toArray();
			$this->set('methods',$methods);
		 
		 
			if ($this->request->is('post')) {

				$postData = $this->request->getData();
				
				if ($postData['button']=='add') {
					
					$report_code = explode("~", $postData['field_arr']);
					$test = $postData['field_arr']; 
					$fields = explode("-",$test);
					
						for ($i=0;$i<count(array_filter($fields));$i++) {

								$field=explode("~",$fields[$i]);
								$grade_code=$field[0];
								$order_code=$field[1];
								
								$postData['grade_code'] = $field[0];
							 	$postData['grade_order'] = $field[1];

						 		if ($postData['min_max']=="-1") {

						 			$grade_data = array("category_code"=>$postData['category_code'], 
										 				"commodity_code"=>$postData['commodity_code'],
										 				"test_code"=>$postData['test_code'],
										 				"method_code"=>$postData['method_code'],
										 				"grd_standrd"=>$postData['grd_standrd'],
										 				"grade_code"=>$field[0],
										 				"grade_value"=>$postData['grade_value'],
										 				"min_max"=>$postData['min_max'], 
										 				"grade_order"=>$field[1],
										 				"user_code"=>$postData['user_code']);
							
								} elseif ($postData['min_max']=="Min") {

							 		$grade_data = array("category_code"=>$postData['category_code'], 
							 							"commodity_code"=>$postData['commodity_code'], 
							 							"test_code"=>$postData['test_code'],
							 							"method_code"=>$postData['method_code'],
							 							"grd_standrd"=>$postData['grd_standrd'],
							 							"grade_code"=>$field[0],
							 							"grade_value"=>$postData['grade_value'],
							 							"min_max"=>$postData['min_max'],  
							 							"grade_order"=>$field[1],
							 							"user_code"=>$postData['user_code']);
						 		
								} elseif ($postData['min_max']=="Max") {

							 		$grade_data = array("category_code"=>$postData['category_code'], 
							 							"commodity_code"=>$postData['commodity_code'], 
							 							"test_code"=>$postData['test_code'],
							 							"method_code"=>$postData['method_code'],
							 							"grd_standrd"=>$postData['grd_standrd'],
							 							"grade_code"=>$field[0],
							 							"max_grade_value"=>$postData['max_grade_value'],
							 							"min_max"=>$postData['min_max'],  
							 							"grade_order"=>$field[1],
							 							"user_code"=>$postData['user_code']);
								
								} elseif ($postData['min_max']=="Range") {
							
									$grade_data = array("category_code"=>$postData['category_code'], 
														"commodity_code"=>$postData['commodity_code'], 
														"test_code"=>$postData['test_code'],
														"method_code"=>$postData['method_code'],
														"grd_standrd"=>$postData['grd_standrd'],
														"grade_code"=>$field[0],
														"grade_value"=>$postData['grade_value'],
														'max_grade_value'=>$postData['max_grade_value'],
														"min_max"=>$postData['min_max'],  
														"grade_order"=>$field[1],
														"user_code"=>$postData['user_code']);
						 		
								}
						 
							// check the conditions for test code is already define test method or not, done by pravin bhakare, 17-12-2019
							$is_already_present = $this->CommGrade->find('all',array('fields'=>array('method_code'),'conditions'=>array('category_code'=>$postData['category_code'],'commodity_code'=>$postData['commodity_code'],'test_code'=>$postData['test_code'],'grade_code'=>$postData['grade_code']),'group'=>array('method_code')));
																							//added new condition on 06-05-2020 by pravin
							 if (!empty($is_already_present)) {	
 
							 		$count_result =  count($is_already_present);

							 		if ($count_result == 1) {
										 
										$test_method = $is_already_present[0]['Comm_Grade']['method_code'];
										
										if ($test_method == $postData['method_code']) {
 
											$this->loadModel('MTestMethod');
											$method_code = $this->MTestMethod->find('first',array('fields'=>array('method_name'),'conditions'=>array('method_code'=>$test_method)));
											$this->Session->setFlash(__('Test method defined for this test is '.$method_code['MTestMethod']['method_name'].'. So, The test method can not be changed.'));
											return $this->redirect(array('action' => 'commodity_grade'));
								 		
										} else {
 
											$this->loadModel('MTestMethod');
											$method_code = $this->MTestMethod->find('first',array('fields'=>array('method_name'),'conditions'=>array('method_code'=>$test_method)));
											$this->Session->setFlash(__('Test method defined for this test is '.$method_code['MTestMethod']['method_name'].'. So, The test method can not be changed.'));
											return $this->redirect(array('action' => 'commodity_grade'));
										}
									
									} else {
 
										$this->Session->setFlash(__('More than one methods are defined for this test. So, The test method can not be changed.'));
										return $this->redirect(array('action' => 'commodity_grade'));
									 
									}
 
						 	} else {
 
								 if ($this->CommGrade->saveAll($grade_data)) {

										$this->Session->setFlash(__('Records has been Saved!'));
										return $this->redirect(array('action' => 'commodity_grade'));
							 
							   } else {
 
								 	$errors =$this->CommGrade->validationErrors;
								 	$this->set('errors',$errors);
								
								}
							
							}
												 
						 
						}
				}
		 
				if ($postData['button']=='update') {
				
					$category_code=$postData['category_code'];
					$commodity_code=$postData['commodity_code'];
					$tests=$postData['test_code'];
			
						$sr_no=$this->CommGrade->find('all',array('fields' => array('sr_no',	'sr_no'),'conditions' => array('category_code' => $category_code,'commodity_code'=>$commodity_code,'test_code'=>$tests)));
						
						$postData['sr_no']=$sr_no['0']['Comm_Grade']['sr_no'];
						//pr($postData);  exit;
						
						if (!$this->CommGrade->save($postData)) {

								$errors =$this->CommGrade->validationErrors;
								$this->set('errors',$errors);
						} else {

							//$this->Flash->set('Records has been Updated!');	
							$this->Session->setFlash(__('Records has been Updated!'));
							return $this->redirect(array('action' => 'commodity_grade'));
						}
				}

				if ($postData['button']=='delete') {

						$category_code=$postData['category_code'];
						$commodity_code=$postData['commodity_code'];
						$tests=$postData['test_code'];
					
						if ($this->CommGrade->updateAll(array('display' => "'N'"),array('category_code' => $category_code,'commodity_code'=>$commodity_code,'test_code'=>$tests))) {

							//$this->Flash->set('Records has been Deleted!');
							$this->Session->setFlash(__('Records has been Deleted!'));
							return $this->redirect(array('action' => 'commodity_grade'));

						} else {

								$errors =$this->CommGrade->validationErrors;
								$this->set('errors',$errors);
								
						}
				}
		 
	 	}
			
	}


/******************************************************************************************************************************************************************************************************************************************************/	

	 
	function createFormula(){
		 
		$this->loadModel('MTest');
		$this->loadModel('MTestType');
		$this->loadModel('CommodityTest');
        
		$flag = false;
		$conn = ConnectionManager::get('default');

		
        /*$Test = $this->MTest->find("all", array('order' => array('MTest.test_name' => 'ASC'),'group' => array('MTest.test_code,MTest.test_name'),
											'joins' => array(array('table' => 'm_test_type','alias' => 'a','type' => 'INNER','conditions' => array('a.test_type_code = MTest.test_type_code')),array('table' => 'commodity_test','alias' => 'b','type' => 'INNER','conditions' => array('b.test_code = MTest.test_code'))),
											'fields' => array('MTest.test_code','MTest.test_name'),
            					'conditions' => array('a.test_type_name' => array("Formula","SV","PN","YN","RT","PA"),'MTest.display'=>'Y')));*/
				
			/*$Test = $this->MTest->find("all")->select(['MTest.test_code','MTest.test_name'])
									->join(['a' => ['table' => 'm_test_type', 'type' => 'INNER',
									'conditions' => ['a.test_type_code = MTest.test_type_code']],
									'b' => ['table' => 'commodity_test', 'type' => 'INNER',
									'conditions' => ['b.test_code = MTest.test_code']]])
									->where(['a.test_type_name' => ["Formula", "SV", "PN", "YN", "RT", "PA"], 'MTest.display' => 'Y'])
									->group(['MTest.test_code','MTest.test_name'])
									->order(['MTest.test_name' => 'ASC'])->toArray();
									
									print_r($Test); exit;*/

		$Tests = $conn->execute("SELECT mt.test_code, mt.test_name 
								 FROM m_test AS mt 
								 INNER JOIN m_test_type AS mtt ON mtt.test_type_code = mt.test_type_code
							  	 INNER JOIN commodity_test AS ct ON ct.test_code = mt.test_code 
								 WHERE mtt.test_type_name IN('Formula','SV','PN','YN','RT','PA') 
								 AND mt.display='Y' 
								 GROUP BY mt.test_code, mt.test_name 
								 ORDER BY mt.test_name ASC")->fetchAll('assoc');
										 
			$result = array();
			$i = 1;
			
			foreach ($Tests as $Test) {

				$result[$i] = $Test['test_name'];
				$i++;
			}

			$this->set('result', $result);
		
			$this->loadModel('MTestMethod');
		
			$method = $this->MTestMethod->find('all',array('order' => array('method_name' => 'ASC'),'conditions' => array('display' => 'Y')))->toArray();
			//print_r($method); exit;
			$this->set('method',$method);
		
      			if ($this->request->is('post')) {
		
					if (isset($_POST['unit'])) {

						$unit=$_POST['unit'];
					
					} else {

						$unit='';
					}
			
         $test_type = $_POST['type'];
			
        	 	if ($test_type == "f") {
 
					$test_code = $_POST['test_code'];
					$method_code = $_POST['method_code'];
					
					$dStart = new \DateTime(date('Y-m-d H:i:s'));

					$date = $dStart->createFromFormat('d/m/Y', $_POST['start_date']);
					$start_date = $date->format('Y/m/d');
					$start_date = date('Y-m-d',strtotime($start_date));
					
					$formula = $_POST['formula'];
					
						if (isset($formula) && $formula!="") {
						
						} else {

							$this->Session->setFlash('Formula Can not be Blank');
							return;
						}
				
						$patternb ="/^[\(\[]+[A-z ]+[+*/-][A-z ]+[\]\)]$";			
						$patternb ='/^[0-9]{1}$/';
				 		
						$field_validation = $_POST['field_validation_range'];
				 		
						$rttttttv1 = preg_match($patternb,$_POST['field_validation_range']);

				 			if ($rttttttv1==0) {	

								$this->Session->setFlash(__('Please enter a validation range in proper format'));
								return $this->redirect(array('action' => 'create_formula'));
							}
					
				 			
							$resl = $this->Customfunctions->hasMatchedParenthesis($formula);
                		$formula1 = $formula;
                		
							 $final_form = array();
                		
							 preg_match_all(" /[*%+-].[*%+-]/", $formula, $matches1);

		                preg_match_all("/\[[^\]]*\]/", $formula, $matches);

							for ($i = 0; $i < count($matches[0]); $i++) {

								$final_form[$i] = trim($matches[0][$i], "[]");
									
								$str = $conn->execute("SELECT field_value 
													   FROM test_fields 
													   WHERE field_code=(SELECT field_code FROM m_fields WHERE  field_name='$final_form[$i]') 
													   AND test_code=$test_code 
													   ORDER BY field_value ASC")->fetchAll('assoc');
							
								$formula1 = str_replace($matches[0][$i], $str[0][0]['field_value'], $formula1);
							}
				
					 			$formula1 = str_replace("[", "", $formula1);
               					$formula1 = str_replace("]", "", $formula1);
				
                				$resl2 = $this->Customfunctions->multiexplode(array("+", "-", "*", "/"), $formula1);

				
								for ($k = 0; $k < count($resl2); $k++) {

									$string = str_replace("(", "", $resl2[$k]);
									$string = str_replace(")", "", $string);

									if (!is_numeric($string)) {

										if (strlen($string) > 1) {

											$flag = true;
										}
									}
								}
				
                				if (!$flag && $resl) {
					
                					$data = $this->MTest->query("SELECT test_code 
                												 FROM  test_formula 
                												 WHERE test_code='$test_code' 
                												 AND method_code='$method_code'");
				 	
					 				$data1 = $this->MTest->query("SELECT test_code,method_code 
					 											  FROM test_formula 
					 											  WHERE  test_code='$test_code' 
					 											  AND end_date is null");
				
			
                				if (count($data) > 0) {

									if (count($data1) > 0) {

										if (isset($_POST['unit'])) {

											$unit=$_POST['unit'];
										
										} else {

											$unit='';
										}
						
										$str = "UPDATE test_formula 
												SET end_date='$start_date' ,  unit='$unit' 
										 		WHERE  test_code=$test_code 
										  		AND method_code=".$data1[0][0]['method_code']." AND end_date is null";
					
					   					if (!$this->MTest->query($str)) {

						   					// $this->Session->setFlash('The formula has been updated!');
										} else {
						   					// $this->Session->setFlash('The formula has not been updated!');
							
										}
					
								$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range,unit)
													 VALUES($test_code,$method_code,'$start_date',
													 Null,'$formula1','$formula','$field_validation','$unit')");
                        	
                        		$this->Session->setFlash('The formula has been added!');
								
								return $this->redirect(array('action' => 'create_formula'));

							} else {

								$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range,unit)
													VALUES($test_code,$method_code,'$start_date',
													Null,'$formula1','$formula','$field_validation','$unit')");
										
								$this->Session->setFlash('The formula has been added!');
								
								return $this->redirect(array('action' => 'create_formula'));
							}

							/* $str="update test_formula set test_formulae='$formula1',test_formula1='$formula',start_date='$start_date',end_date='$end_date',res_validation_range='$field_validation' WHERE  test_code=$test_code AND method_code='$method_code'";
								
							if (!$this->MTest->query($str)) {
										$this->Session->setFlash('The formula has been updated!');
								} else {
										$this->Session->setFlash('The formula has not been updated!');
								} */
						
						} else {

							$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range,unit)
												 VALUES($test_code,$method_code,'$start_date',
												 Null,'$formula1','$formula','$field_validation','$unit')");
							$this->Session->setFlash('The formula has been added!');
							return $this->redirect(array('action' => 'create_formula'));
					
						}

               } else {

                    $this->Session->setFlash('Incorrect formula,Please check formula for operANDs AND operator precedence');
						  return $this->redirect(array('action' => 'create_formula'));
                
				}

				} elseif ($test_type == "s") {

   	             	$test_code = $_POST['test_code'];
                	$formula = $_POST['formula'];
					$method_code = $_POST['method_code'];
				    

					$dStart = new \DateTime(date('Y-m-d H:i:s'));

					$date = $dStart->createFromFormat('d/m/Y', $_POST['start_date']);
					$start_date=$date->format('Y/m/d');
					$start_date=date('Y-m-d',strtotime($start_date));

				
					$formula1="SV";
					$field_validation = 0;
				 
					$data1 = $this->MTest->query("SELECT test_code 
												  FROM test_formula
												  WHERE  test_code='$test_code' 
												  AND method_code='$method_code'");
					
					$data2 = $this->MTest->query("SELECT test_code,method_code 
												  FROM test_formula
												  WHERE  test_code='$test_code' 
												  AND end_date is null  ");
			
               $data = $this->MTest->query("SELECT field_code 
               	                            FROM test_fields 
               	                            WHERE test_code='$test_code'");
                
					if (count($data) > 0) {
                   
                   $id = $data[0][0]['field_code'];
		
                    $data = $this->MTest->query("UPDATE m_fields 
                    							 SET field_name='$formula' 
                    							 WHERE field_code=$id");
					
						if (count($data1) > 0) {

							if (count($data2) > 0) { 
								
								//$str="update test_formula SET end_date='$start_date' WHERE  test_code=$test_code AND method_code='$method_code'";
								
								if (!$this->MTest->query("UPDATE test_formula 
														  SET end_date='$start_date' 
														  WHERE method_code=".$data2[0][0]['method_code']."  AND test_code=$test_code AND end_date is null")) {
																//$this->Session->setFlash('The formula has been updated!');
								
								} else {

									//$this->Session->setFlash('The formula has not been updated!');
								}

								$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)
													 VALUES($test_code,$method_code,'$start_date',
													 Null,'$formula1','$formula','$field_validation')");
                        		$this->Session->setFlash('The formula has been added!');
								return $this->redirect(array('action' => 'create_formula'));
							
							} else {

								$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)
													 VALUES($test_code,$method_code,'$start_date'
													 Null,'$formula1','$formula','$field_validation')");
                        
                        		$this->Session->setFlash('The formula has been added!');
								return $this->redirect(array('action' => 'create_formula'));
							}
						
						} else {

								$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)
													 VALUES($test_code,$method_code,'$start_date',
													 Null,'$formula1','$formula','$field_validation')");
                        		
                        		$this->Session->setFlash('The formula has been added!');
								return $this->redirect(array('action' => 'create_formula'));
						}
					
					
               } else {

                    $this->loadModel('MCommodityCategory');
                    $data = $this->MCommodityCategory->query("SELECT * FROM m_fields ORDER BY field_code DESC limit 1");
                    $alphabet = $data[0][0]['field_value'];

					 	  	if (count($data)>0) {
								
								$last_alphabet = ++$alphabet;
							} else {

						   	$last_alphabet = 'a'; 
					 		}

                   	// $this->MTest->query("INSERT INTO m_fields (field_name,field_value)values('$formula','$last_alphabet')");
                    $res = $this->MTest->query("SELECT max(field_code) AS id FROM m_fields ");
                    $last_id = $res[0][0]['id'];
                    $this->MTest->query("INSERT INTO test_fields (test_code,field_code)values($test_code,$last_id)");
						  //$this->Session->setFlash('Field label is updated');
					
							//For adding value in test formula
					
					}
            } elseif ($test_type == "r") {

                $test_code = $_POST['test_code'];
                $formula = $_POST['formula'];
				    $method_code = $_POST['method_code'];
			
					 $dStart = new \DateTime(date('Y-m-d H:i:s'));

					 $date = $dStart->createFromFormat('d/m/Y', $_POST['start_date']);
					 $start_date=$date->format('Y/m/d');
					 $start_date=date('Y-m-d',strtotime($start_date));

				
					 $formula1="RT";
				
					 // $formula = "SV";
				 
					 $field_validation = 0;
				 
				 
					 $data1 = $this->MTest->query("SELECT test_code FROM test_formula WHERE  test_code='$test_code' AND method_code='$method_code'");
				
		    		 $data2 = $this->MTest->query("SELECT test_code,method_code FROM test_formula WHERE  test_code='$test_code' AND end_date is null  ");
								 
                $data = $this->MTest->query("SELECT field_code FROM test_fields WHERE  test_code='$test_code'");

                if (count($data) > 0) {
                   
                   $id = $data[0][0]['field_code'];

                   $data = $this->MTest->query("UPDATE m_fields SET  field_name='$formula' WHERE field_code=$id");
					
					
						if (count($data1) > 0) {

							if(count($data2) > 0) { 
								//$str="UPDATE test_formula SET end_date='$start_date' WHERE  test_code=$test_code AND method_code='$method_code'";
								
								if (!$this->MTest->query("UPDATE test_formula SET end_date='$start_date' 
																  WHERE method_code=".$data2[0][0]['method_code']."  AND test_code=$test_code AND end_date is null")) {
													
													//$this->Session->setFlash('The formula has been updated!');
								} else {
							
									//$this->Session->setFlash('The formula has not been updated!');
								}

								$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)values($test_code,$method_code,'$start_date',Null,'$formula1','$formula','$field_validation')");
								$this->Session->setFlash('The formula has been added!');
								return $this->redirect(array('action' => 'create_formula'));
							
							} else {

								$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)values($test_code,$method_code,'$start_date',Null,'$formula1','$formula','$field_validation')");
                        $this->Session->setFlash('The formula has been added!');
								return $this->redirect(array('action' => 'create_formula'));
							}
						
						} else {

								$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)values($test_code,$method_code,'$start_date',Null,'$formula1','$formula','$field_validation')");
                        $this->Session->setFlash('The formula has been added!');
								return $this->redirect(array('action' => 'create_formula'));
					
						}
					
					
					 //$this->Session->setFlash('Field label is updated');
               } else {

                    $this->loadModel('MCommodityCategory');
                    $data = $this->MCommodityCategory->query("SELECT * FROM m_fields ORDER BY field_code DESC limit 1");
                    $alphabet = $data[0][0]['field_value'];

					 		if(count($data)>0) {

                    		$last_alphabet = ++$alphabet;
					 		} else {

						   	$last_alphabet = 'a'; 
					 		}
                   
							 // $this->MTest->query("INSERT INTO m_fields (field_name,field_value)values('$formula','$last_alphabet')");
                    
							 $res = $this->MTest->query("SELECT max(field_code) AS id FROM m_fields ");
                    
							 $last_id = $res[0][0]['id'];
                    
							 $this->MTest->query("INSERT INTO test_fields (test_code,field_code)values($test_code,$last_id)");
					
							 //$this->Session->setFlash('Field label is updated');
					
							//For adding value in test formula
					
					}

				} else {

					if ($test_type == "p") {

				 		$test_code = $_POST['test_code'];
						$method_code = $_POST['method_code'];
				
						$dStart = new \DateTime(date('Y-m-d H:i:s'));

						$date = $dStart->createFromFormat('d/m/Y', $_POST['start_date']);
						$start_date=$date->format('Y/m/d');
						$start_date=date('Y-m-d',strtotime($start_date));
  
				
						//$end_date = $_POST['end_date'];
				
						$formula1="PN";
						$formula = "PN";
						$field_validation = 0;
				
						$data = $this->MTest->query("SELECT test_code FROM test_formula WHERE  test_code='$test_code' AND method_code='$method_code'");
				
				 		$data1 = $this->MTest->query("SELECT test_code,method_code FROM test_formula WHERE  test_code='$test_code' AND end_date is null  AND test_formulae='PN'");
				
                   if (count($data) > 0) {

							if(count($data1) > 0) { 
								//$str="UPDATE test_formula set end_date='$start_date' WHERE  test_code=$test_code AND method_code='$method_code'";
						
								if (!$this->MTest->query("UPDATE test_formula SET end_date='$start_date' WHERE  method_code=".$data1[0][0]['method_code']." AND test_code=$test_code AND end_date is null")) {
							
									//$this->Session->setFlash('The formula has been updated!');
								} else {
									//$this->Session->setFlash('The formula has not been updated!');
								}
								
								$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)values($test_code,$method_code,'$start_date',Null,'$formula1','$formula','$field_validation')");
                        $this->Session->setFlash('The formula has been added!');
								return $this->redirect(array('action' => 'create_formula'));
							
							} else {

								$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)values($test_code,$method_code,'$start_date',Null,'$formula1','$formula','$field_validation')");
                        $this->Session->setFlash('The formula has been added!');
								return $this->redirect(array('action' => 'create_formula'));
							}
						
						} else {

								$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)values($test_code,$method_code,'$start_date',Null,'$formula1','$formula','$field_validation')");
                        $this->Session->setFlash('The formula has been added!');
								return $this->redirect(array('action' => 'create_formula'));
						}
					
					} elseif ($test_type == "y") {

							$test_code = $_POST['test_code'];
							$method_code = $_POST['method_code'];

							$dStart = new \DateTime(date('Y-m-d H:i:s'));

							$date = $dStart->createFromFormat('d/m/Y', $_POST['start_date']);
							$start_date=$date->format('Y/m/d');
							$start_date=date('Y-m-d',strtotime($start_date));

							
							$formula1="YN";
							$formula = "YN";
							$field_validation = 0;

							$data = $this->MTest->query("SELECT test_code FROM test_formula WHERE  test_code='$test_code' AND method_code='$method_code' AND test_formulae='YN'");
							
							$data1 = $this->MTest->query("SELECT test_code,method_code FROM test_formula WHERE  test_code='$test_code' AND end_date is null  ");
		
                			if (count($data) > 0) {

									if (count($data1) > 0) { 
										//$str="UPDATE test_formula set end_date='$start_date' WHERE  test_code=$test_code AND method_code='$method_code'";
										
										if (!$this->MTest->query("UPDATE test_formula SET end_date='$start_date' WHERE  test_code=$test_code AND end_date is null")) {
												//$this->Session->setFlash('The formula has been updated!');
										} else {
											//$this->Session->setFlash('The formula has not been updated!');
										}

										$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)values($test_code,$method_code,'$start_date',Null,'$formula1','$formula','$field_validation')");
										$this->Session->setFlash('The formula has been added!');
										return $this->redirect(array('action' => 'create_formula'));
									
									} else {

										$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)values($test_code,$method_code,'$start_date',Null,'$formula1','$formula','$field_validation')");
												$this->Session->setFlash('The formula has been added!');
										return $this->redirect(array('action' => 'create_formula'));
									}
									
								} else {

									$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)values($test_code,$method_code,'$start_date',Null,'$formula1','$formula','$field_validation')");
									$this->Session->setFlash('The formula has been added!');
									return $this->redirect(array('action' => 'create_formula'));
										
								} 
					
					} elseif ($test_type == "PA") {

								$test_code = $_POST['test_code'];
								$method_code = $_POST['method_code'];

								$dStart = new \DateTime(date('Y-m-d H:i:s'));

								$date = $dStart->createFromFormat('d/m/Y', $_POST['start_date']);
								$start_date=$date->format('Y/m/d');
								$start_date=date('Y-m-d',strtotime($start_date));

								$formula1="PA";
								$formula = "PA";
								$field_validation = 0;
								$data = $this->MTest->query("SELECT test_code FROM test_formula WHERE  test_code='$test_code' AND method_code='$method_code' AND test_formulae='PA'");
								
								$data1 = $this->MTest->query("SELECT test_code,method_code FROM test_formula WHERE  test_code='$test_code' AND end_date is null  ");
				
                			if (count($data) > 0) {

									if (count($data1) > 0) { 
										//$str="UPDATE test_formula set end_date='$start_date' WHERE  test_code=$test_code AND method_code='$method_code'";
										
										if (!$this->MTest->query("UPDATE test_formula SET end_date='$start_date' WHERE  test_code=$test_code AND end_date is null")) {
											//$this->Session->setFlash('The formula has been updated!');
										} else {
											//$this->Session->setFlash('The formula has not been updated!');
										}

										$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)values($test_code,$method_code,'$start_date',Null,'$formula1','$formula','$field_validation')");
												$this->Session->setFlash('The formula has been added!');
										return $this->redirect(array('action' => 'create_formula'));
									
									} else {

										$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)values($test_code,$method_code,'$start_date',Null,'$formula1','$formula','$field_validation')");
										$this->Session->setFlash('The formula has been added!');
										return $this->redirect(array('action' => 'create_formula'));
									}
								
								} else {

									$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)values($test_code,$method_code,'$start_date',Null,'$formula1','$formula','$field_validation')");
												$this->Session->setFlash('The formula has been added!');
										return $this->redirect(array('action' => 'create_formula'));
								} 
					
					}

					/* }else {
							//$this->Session->setFlash(__('The application not saved.'));
						$this->redirect(array('controller'=>'users', 'action'=>'error'));
						// header('Location:../../../mferror.html');
							exit;
						} */
				}
			}
	
	}


/****************************************************************************************************************************************************************************************************************************************************/	
//																																																													//		
//		// edit category record                                                                                                                                                                                                                     //          
//		/*public function fetchCommodityIdForAssignTest($id){                                                                                                                                                                                       //         
//																																																													//		
//			$this->loadModel('MCommodityCategory');																																																	//		
//			$category_data = $this->MCommodityCategory->find('all', array('fields'=> array('category_code', 'category_name', 'l_category_name', 'min_quantity'), 'conditions'=> array('category_code'=>$id)))->first();								//
//																																																													//	
//			$this->Session->write('category_code', $id);																																															//	
//			$this->Session->write('category_data', $category_data);																																													//		
//																																																													//	
//			$this->redirect('/Master/category');																																																	//					
//																																																													//		
//	}*/																																																												//	
//																																																													//	
//																																																													//			
/****************************************************************************************************************************************************************************************************************************************************/	


	// commodity test method starts this funtion name changed FROM commodity_test to assignTestToCommodity on 2021 (listing)
	public function assignTestToCommodity() {
		
	  	$this->loadModel('CommodityTest');
		$this->loadModel('MTest');
		$this->loadModel('TestFields');
		$this->loadModel('MCommodity');
		$conn = ConnectionManager::get('default');

		//tests list
		$tests = $conn->execute("SELECT t.test_code,t.test_name,t.l_test_name FROM m_test AS t WHERE t.display='Y' group by t.test_code,t.test_name ORDER BY test_name ASC")->fetchAll('assoc');

		//print_r($test); exit;
		$result = array();

		$i = 0;
		foreach ($tests as $test) {

			$result[$i] = $test['test_name'];
			$i = $i+1;
		}

		$this->set('result', $result);

	

		 //list commodities 
		//$commodity = $conn->execute("SELECT * FROM m_commodity WHERE display='Y' ORDER BY commodity_name ASC")->fetchAll('assoc');

		$commodity = $this->MCommodity->find('list',array('valueField'=>'commodity_name','conditions'=>array('display'=>'Y'),'order'=>'commodity_name'))->toList();
		$this->set('commodity', $commodity);

	
		if ($this->request->is('post')) {
			
			$test_code = $this->request->getData('test_code');
			$commodity_code = $this->request->getData('commodity_code');		
			
			$count = $this->CommodityTest->find('count', array( 'conditions' => array('test_code' => $test_code,'commodity_code' => $commodity_code)));
				
			$dep_test_code = $this->TestFields->find('all', array( 'conditions' => array('test_code' => $test_code,'field_type' => 'D')));
				
				if (count($dep_test_code)>0) {

					for ($i=0;$i<count($dep_test_code);$i++) {
				
						$test_code1 = $dep_test_code[$i]['dep_test_code'];
						$count1 = $this->CommodityTest->find('count', array( 'conditions' => array('test_code' => $test_code1,'commodity_code' => $commodity_code)));
					if($count1<1)
					{
						if (!$this->CommodityTest->query("INSERT INTO commodity_test(commodity_code,test_code)values($commodity_code,$test_code1)")) 
						{
								$this->Session->setFlash('The Test has been saved!');
								return $this->redirect(array('action' => 'commodity_test'));	
						} 
						else
						{
							$errors = $this->CommodityTest->validationErrors;									
							exit;
						}
						
					}else{
							$test_name = $this->Test->query("SELECT t.test_name FROM m_test  AS t 
															WHERE  t.display='Y' AND t.test_code=$test_code group by t.test_code,t.test_name ORDER BY test_name ASC");
					
							$commodity = $this->Test->query("SELECT commodity_name FROM m_commodity WHERE commodity_code=$commodity_code AND display='Y' ORDER BY commodity_name ASC");

						// $this->Session->setFlash
							$this->Session->setFlash($test_name[0][0]['test_name'].'Test already added to '.$commodity[0][0]['commodity_name'].'!');
							return $this->redirect(array('action' => 'commodity_test'));
					}
					}											
				}
			if($count<1)
				{
					//echo "INSERT INTO commodity_test(commodity_code,test_code)values($commodity_code,$test_code)";exit;
					if (!$this->CommodityTest->query("INSERT INTO commodity_test(commodity_code,test_code)values($commodity_code,$test_code)")) 
					{
							$this->Session->setFlash('The Test has been saved!');
							return $this->redirect(array('action' => 'commodity_test'));	
					} 
					else
					{
						$errors = $this->CommodityTest->validationErrors;
						
						exit;
					}
				}
				else{
					
						$test_name = $this->Test->query("SELECT test_name FROM m_test WHERE  display='Y' AND test_code=$test_code ");						
						$commodity = $this->Test->query("SELECT commodity_name FROM m_commodity WHERE commodity_code=$commodity_code AND display='Y' ORDER BY commodity_name ASC");

						$this->Session->setFlash($test_name[0][0]['test_name'].' is already added to '.$commodity[0][0]['commodity_name'].'!');	
					return $this->redirect(array('action' => 'commodity_test'));
				}
		
	
			}
	}

	
}

?>