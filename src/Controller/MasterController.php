<?php
namespace App\Controller;

use Cake\Event\Event;
use App\Network\Email\Email;
use Cake\ORM\Entity;
use Cake\Datasource\ConnectionManager;
use Cake\Http\Client\Request;
use Cake\View;
use Cake\ORM\TableRegistry;
use mysql_xdevapi\CollectionModify;
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
		
		$message = '';
		$message_theme = '';
		$redirect_to = '';

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

					$this->LimsUserActionLogs->saveActionLog('Master Save','Failed'); #Action
					$category_nm = $this->request->getData('category_name');
					$this->set('message',  $category_nm . ' record already exist, Please contact administrator to delete it!');
					$this->set('message_theme','failed');
					$this->set('redirect_to', 'category');
					return null;
				}

				$categoryInputData = array('category_name'=>$postData['category_name'],'l_category_name'=>$postData['l_category_name'],'min_quantity'=>$postData['min_quantity']);
				$categoryEntity = $this->MCommodityCategory->newEntity($categoryInputData);
				$recordPush = $this->MCommodityCategory->save($categoryEntity);

				if ($recordPush) {

					$this->LimsUserActionLogs->saveActionLog('Master Save','Success'); #Action
					$message = 'Successfully added new category!';
					$message_theme = 'success';
					$redirect_to = 'saved_category';

				} else {

					$this->LimsUserActionLogs->saveActionLog('Master Save','Failed'); #Action
					$message = 'Problem in saving new category, try again later!';
					$message_theme = 'failed';
					$redirect_to = 'saved_category';
				}

				// set variables to show popup messages FROM view file
				$this->set('message', $message);
                $this->set('message_theme',$message_theme);
				$this->set('redirect_to', $redirect_to);
			}

			// updating record
			if (null !==($this->request->getData('update'))) {

				// check duplicate category validation
				$category_name_upper = strtoupper(trim($this->request->getData('category_name')));
				$category_code_post = $this->request->getData('category_code');
				$isExist = $this->MCommodityCategory->find('all', array('conditions'=> array('UPPER(TRIM(category_name))'=>$category_name_upper, 'category_code !='=>$category_code_post, 'display'=>'Y')))->count();

				if ($isExist!='0') {
					
					$this->LimsUserActionLogs->saveActionLog('Master Update','Failed'); #Action
					$category_nm = $this->request->getData('category_name');
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
					
					$this->LimsUserActionLogs->saveActionLog('Master Update','Success'); #Action
					$message = 'Successfully update category!';
					$message_theme = 'success';
					$redirect_to = 'saved_category';

				} else {
					
					$this->LimsUserActionLogs->saveActionLog('Master Update','Failed'); #Action
					$message = 'Problem in updation, try again later!';
					$message_theme = 'failed';
					$redirect_to = 'saved_category';
				}

				// set variables to show popup messages FROM view file
				$this->set('message', $message);
				$this->set('message_theme',$message_theme);
				$this->set('redirect_to', $redirect_to);
			}

		}

	}


/******************************************************************************************************************************************************************************************************************************************************/


	// delete commodity category record
	public function deleteCategory($id){

		//$this->autoRender = false;

		// checking category code already in use or not
		$inUsed = $this->MCommodity->find('all', array('conditions'=> array('category_code IS'=>$id, 'display'=>'Y')))->count();

		if ($inUsed!='0') {
			
			$this->LimsUserActionLogs->saveActionLog('Master Delete','Failed'); #Action
            $this->set('message',  'Already in use, could not deleted!');
            $this->set('message_theme','failed');
            $this->set('redirect_to', '../saved_category');
            return null;

		} else {

			$categoryRecord = array('category_code'=>$id, 'display'=>'N');
			$categoryEntity = $this->MCommodityCategory->newEntity($categoryRecord);
			$recordDelete = $this->MCommodityCategory->save($categoryEntity);

			if ($recordDelete) {
				
				$this->LimsUserActionLogs->saveActionLog('Master Delete','Success'); #Action
				$message = 'Successfully delete category!';
				$message_theme = 'success';
				$redirect_to = '../saved_category';

			} else {
				
				$this->LimsUserActionLogs->saveActionLog('Master Delete','Failed'); #Action
				$message = 'Problem in deleting, try again later!';
				$message_theme = 'failed';
				$redirect_to = '../saved_category';
			}
		}

		$this->redirect("/master/delete_category");	// This line added by shankhpal shende on 05/09/2022
		
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
		$this->Session->write('commodity_code', $id);
		$this->Session->write('commodity_data', $commodity_data);
		$this->redirect('/Master/commodity');
	}


/******************************************************************************************************************************************************************************************************************************************************/

	// add / update commodity
	public function commodity(){

		$this->authenticateUser();
		
		$message = '';
		$message_theme = '';
		$redirect_to = '';
		
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
					
					$this->LimsUserActionLogs->saveActionLog('Master Save','Failed'); #Action
					$this->set('message',  $commodity_nm . ' record for this category already exists! Please Contact to Administrator');
					$this->set('message_theme','failed');
					$this->set('redirect_to', 'commodity');
					return null;
				}

				// get last record commodity code record to solve the composite primary keys issue, Done by Pravin bhakare 01-11-2021
				$getLastRecordId = $this->MCommodity->find('all',array('fields'=>'commodity_code','order'=>'commodity_code desc'))->first();
				$currentRecordId = $getLastRecordId['commodity_code'] +1;

				$commodityRecord = array(
					'category_code'=>$postData['category_code'],
					'commodity_code'=>$currentRecordId,
					'commodity_name'=>$postData['commodity_name'],
					'l_commodity_name'=>$postData['l_commodity_name']
				);

				$commodityEntity = $this->MCommodity->newEntity($commodityRecord);
				$recordPush = $this->MCommodity->save($commodityEntity);

				if ($recordPush) {
					
					$this->LimsUserActionLogs->saveActionLog('Master Save','Success'); #Action
					$message = 'Successfully added new commodity!';
                    $message_theme = 'success';
					$redirect_to = 'saved_commodity';
					
				} else {
					$this->LimsUserActionLogs->saveActionLog('Master Save','Failed'); #Action
					$message = 'Problem in saving new commodity, try again later!';
                    $message_theme = 'failed';
					$redirect_to = 'saved_commodity';
					
				}

				// set variables to show popup messages FROM view file
				$this->set('message', $message);
				$this->set('message_theme',$message_theme);
				$this->set('redirect_to', $redirect_to);
			}

			// update record
			if (null!==($this->request->getData('update'))) {

				// check duplicate commodity validation
				$commodity_name_upper = strtoupper(trim($postData['commodity_name']));
				$commodity_code_post = $postData['commodity_code'];
				$isExist = $this->MCommodity->find('all', array('conditions'=> array('UPPER(TRIM(commodity_name))'=>$commodity_name_upper, 'commodity_code !='=>$commodity_code_post, 'display'=>'Y')))->count();

				if ($isExist!='0') {
					$this->LimsUserActionLogs->saveActionLog('Master Update','Failed'); #Action
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
					$this->LimsUserActionLogs->saveActionLog('Master Update','Success'); #Action
					$message = 'Successfully update commodity!';
					$message_theme = 'success';
					$redirect_to = 'saved_commodity';
				} else {
					$this->LimsUserActionLogs->saveActionLog('Master Update','Failed'); #Action
					$message = 'Problem in updation, try again later!';
					$message_theme = 'failed';
					$redirect_to = 'saved_commodity';
				}

				// set variables to show popup messages FROM view file
				$this->set('message', $message);
				$this->set('message_theme',$message_theme);
				$this->set('redirect_to', $redirect_to);
			}

		}

	}


/******************************************************************************************************************************************************************************************************************************************************/

	// delete commodity record
	public function deleteCommodity($id){

		$this->autoRender = false;

		// check commodity code already in use
		$inUsedTest = $this->CommodityTest->find('all', array('conditions'=> array('commodity_code IS'=>$id, 'display'=>'Y')))->count();
		$inUsedSampleInward = $this->SampleInward->find('all', array('conditions'=> array('commodity_code IS'=>$id, 'display'=>'Y')))->count();

		if ($inUsedTest!='0' || $inUsedSampleInward!='0') {
			$this->LimsUserActionLogs->saveActionLog('Master Delete','Failed'); #Action
			$message = 'Commodity already in used, could not be deleted';
			$redirect_to = '../saved_commodity';

		} else {

			// get the category code value to resolve the issue of composite primary key issue, done by pravin bhakare, 01-11-2021
			$category_code = $this->MCommodity->find("all",array("fields"=>'category_code','conditions'=>array('commodity_code'=>$id)))->first();
			$categorycode = $category_code['category_code'];

			$commodityRecord = array('category_code'=>$categorycode,'commodity_code'=>$id, 'display'=>'N');
			$commodityEntity = $this->MCommodity->newEntity($commodityRecord);
			$recordDeleted = $this->MCommodity->save($commodityEntity);

			if ($recordDeleted) {
				$this->LimsUserActionLogs->saveActionLog('Master Delete','Success'); #Action
				$message = 'Successfully deleted commodity!';
				$redirect_to = '../saved_commodity';

			} else {
				$this->LimsUserActionLogs->saveActionLog('Master Delete','Failed'); #Action
				$message = 'Problem in deleting, try again later!';
				$redirect_to = '../saved_commodity';
			}

		}

		$this->redirect("/master/saved_commodity");
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

		// all records under SELECTed module
		$this->loadModel($table);
		$phyAppearArray = $this->$table->find('all', array('conditions'=> array('display'=>'Y'), 'order'=> array($textbox_1=>'ASC')))->toArray();
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
					$this->LimsUserActionLogs->saveActionLog('Master Save','Failed'); #Action
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

					$this->LimsUserActionLogs->saveActionLog('Master Save','Success'); #Action
					$message = 'Successfully added new ' . $title . ' record!';
					$message_theme = 'success';
					$redirect_to = '../saved-phy-appear/' . $action;

				} else {
					$this->LimsUserActionLogs->saveActionLog('Master Save','Failed'); #Action
					$message = 'Problem in saving ' . $title . ', try again later!';
					$message_theme = 'failed';
					$redirect_to = '../saved-phy-appear/' . $action;

				}

				// set variables to show popup messages FROM view file
				$this->set('message', $message);
				$this->set('message_theme',$message_theme);
				$this->set('redirect_to', $redirect_to);

			}

			// update record
			if (null!==($this->request->getData('update'))) {

				// check isEditable status of current record
				if ($table=='m_test_type') {

					$isEditableStatus = $this->$table->find('all', array('conditions'=> array($phy_appear_code_name=>$phy_appear_code, 'iseditable'=>'N')))->count();

					if ($isEditableStatus!='0') {
						$this->LimsUserActionLogs->saveActionLog('Master Update','Failed'); #Action
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
					$this->LimsUserActionLogs->saveActionLog('Master Update','Failed'); #Action
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
					$this->LimsUserActionLogs->saveActionLog('Master Update','Success'); #Action
					$message = 'Successfully update ' . $title . '!';
					$message_theme = 'success';
					$redirect_to = '../saved-phy-appear/' . $action;

				} else {
					$this->LimsUserActionLogs->saveActionLog('Master Update','Failed'); #Action
					$message = 'Problem in updation, try again later!';
					$message_theme = 'failed';
					$redirect_to = '../saved-phy-appear/' . $action;
				}

				// set variables to show popup messages from view file
				$this->set('message', $message);
				$this->set('message_theme', $message_theme);
				$this->set('redirect_to', $redirect_to);
			}

		}

	}


/******************************************************************************************************************************************************************************************************************************************************/


	// delete phy appear modules record
	public function deletePhyAppear($code_name, $table, $action, $code){

		// $this->autoRender=false;
		// check phy appear already in use or not
		$inUsed = '0';
		$this->loadModel($table);

		if ($table == 'm_fields') {

			$inUsed = $this->TestFields->find('all', array('conditions'=> array('field_code'=>$code)))->count();

		} elseif ($table == 'm_MTestMethod') {

			$inUsed = $this->TestFormula->find('all', array('conditions'=> array('method_code'=>$code, 'display'=>'Y')))->count();
		}

		if ($inUsed!='0') {
			$this->LimsUserActionLogs->saveActionLog('Master Delete','Failed'); #Action
			$message = 'Record already in use, could not be deleted!';
			$message_theme = 'failed';
			$redirect_to = '../../../../saved-phy-appear/' . $action;

		} else {

			$phyAppearRecord = array($code_name=>$code, 'display'=>'N');

			$phyAppearEntity = $this->$table->newEntity($phyAppearRecord);
			$recordDeleted = $this->$table->save($phyAppearEntity);

			if ($recordDeleted) {
				$this->LimsUserActionLogs->saveActionLog('Master Delete','Success'); #Action
				$message = 'Successfully delete record!';
				$message_theme = 'success';
				$redirect_to = '../../../../saved-phy-appear/' . $action;

			} else {
				$this->LimsUserActionLogs->saveActionLog('Master Delete','Failed'); #Action
				$message = 'Problem in deleting, try again later!';
				$message_theme = 'failed';
				$redirect_to = '../../../../saved-phy-appear/' . $action;

			}
           // This line commented by shankhpal shende on 05/09/2022 for displaying error message 
			//$this->redirect('/master/saved-phy-appear/' . $action);

		}

		// set variables to show popup messages from view file
		$this->set('message', $message);
		$this->set('message_theme', $message_theme);
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

		$sampleObsCode = $this->MSampleObsType->find('all',array('fields'=>'m_sample_obs_type_code','order'=>'m_sample_obs_type_code desc'))->first();
		$sampleObsCodeCurr =  $sampleObsCode['m_sample_obs_type_code']+1;

		// load records if session is set
		if ($this->Session->read('homo_value_data')!=null) {

			$this->set('homo_value_code', $this->Session->read('homo_value_code'));
			$this->set('homo_value_data', $this->Session->read('homo_value_data'));
			$homoValueData = $this->Session->read('homo_value_data');
			$updateMSampleObsCode = $homoValueData['m_sample_obs_code'];
			$updateMSampleObsTypeCode = $homoValueData['m_sample_obs_type_code'];
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
						$this->LimsUserActionLogs->saveActionLog('Master Save','Failed'); #Action
						$this->set('message',  'Sorry. The Selected value type do not matched with previous records.');
						$this->set('redirect_to', 'saved-homo-value');
						return null;
					}

					if ($val_type=='single') {

						$homoValueRecord = array(
							'm_sample_obs_type_code'=>$sampleObsCodeCurr,
							'm_sample_obs_code'=>$m_sample_obs_code,
							'm_sample_obs_type_value'=>$m_sample_obs_type_value,
							'user_code'=>$user_code,
							'login_timestamp'=>$postData['login_timestamp']
						);

						$homoValueEntity = $this->MSampleObsType->newEntity($homoValueRecord);
						$recordPush = $this->MSampleObsType->save($homoValueEntity);

					} else {

						$homoValueRecordOne = array(
							'm_sample_obs_type_code'=>$sampleObsCodeCurr,
							'm_sample_obs_code'=>$m_sample_obs_code,
							'm_sample_obs_type_value'=>'Yes',
							'user_code'=>$user_code,
							'login_timestamp'=>$postData['login_timestamp']
						);

						$sampleObsCodeCurrent = $sampleObsCodeCurr + 1;
						$homoValueRecordTwo= array(
							'm_sample_obs_type_code'=>$sampleObsCodeCurrent,
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
						$this->LimsUserActionLogs->saveActionLog('Master Save','Success'); #Action
						$message = 'Successfully added new homogenization value!';
						$message_theme = 'success';
						$redirect_to = 'saved-homo-value';
					} else {
						$this->LimsUserActionLogs->saveActionLog('Master Save','Failed'); #Action
						$message = 'Problem in saving homogenization value, try again later!';
						$message_theme = 'failed';
						$redirect_to = 'saved-homo-value';

					}

				} else {
					$this->LimsUserActionLogs->saveActionLog('Master Save','Failed'); #Action
					$message = 'Record Already Exists';
					$message_theme = 'failed';
					$redirect_to = 'saved-homo-value';
				}

				// set variables to show popup messages from view file
				$this->set('message', $message);
				$this->set('message_theme', $message_theme);
				$this->set('redirect_to', $redirect_to);

			}

			// update record
			if (null!==($postData['update'])) {

				// check duplicate homo values in same homogenization field
				$homo_value_upper = strtoupper(trim($m_sample_obs_type_value));

				$isExist = $this->MSampleObsType->find('all', array('conditions'=> array('UPPER(TRIM(m_sample_obs_type_value))'=>$homo_value_upper, 'm_sample_obs_code'=>$m_sample_obs_code, 'm_sample_obs_type_code !='=>$m_sample_obs_type_code, 'display'=>'Y')))->count();

				if ($isExist!='0') {
					$this->LimsUserActionLogs->saveActionLog('Master Update','Failed'); #Action
					$this->set('message',  'Homogenization value already exists !');
					$this->set('redirect_to', 'homo-value');
					return null;
				}

				$this->MSampleObsType->find('all', array('conditions'=> array('UPPER(TRIM(m_sample_obs_type_value))'=>$homo_value_upper, 'm_sample_obs_code'=>$m_sample_obs_code, 'm_sample_obs_type_code !='=>$m_sample_obs_type_code, 'display'=>'Y')))->count();

				if ($old_val_type != $val_type) {

					// disable previous value type under same homo field, added on 17-09-2020 by Aniket
					$MSampleObsType = TableRegistry::getTableLocator()->get('MSampleObsType');
					$deactivePreviousValType = $MSampleObsType->query();
					$deactivePreviousValType->update()->set(array('display'=>'N'))->where(array('m_sample_obs_code'=>$m_sample_obs_code))->execute();

					if ($val_type=='yesno') {

						$homoValueRecordOne = array(
							'm_sample_obs_type_code'=>$updateMSampleObsTypeCode,
							'm_sample_obs_code'=>$m_sample_obs_code,
							'm_sample_obs_type_value'=>'Yes',
							'user_code'=>$user_code,
							'login_timestamp'=>$postData['login_timestamp']
						);

						$homoValueRecordTwo = array(
							'm_sample_obs_type_code'=>$updateMSampleObsTypeCode,
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
							'm_sample_obs_type_code'=>$updateMSampleObsTypeCode,
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
					$this->LimsUserActionLogs->saveActionLog('Master Update','Success'); #Action
					$message = 'Successfully update homogenization value!';
					$message_theme = 'success';
					$redirect_to = 'saved-homo-value';

				} else {
					$this->LimsUserActionLogs->saveActionLog('Master Update','Failed'); #Action
					$message = 'Problem in updation of homogenization value, try again later!';
					$message_theme = 'failed';
					$redirect_to = 'saved-homo-value';
				}

				// set variables to show popup messages from view file
				$this->set('message', $message);
				$this->set('message_theme', $message_theme);
				$this->set('redirect_to', $redirect_to);

			}

		}

	}


/******************************************************************************************************************************************************************************************************************************************************/

	// delete homo value record
	public function deleteHomoValue($homo_type_code, $homo_code, $val_type, $homo_value){

		if ($val_type=='yesno') {

			// delete one of yes/no combo record
			$homoValueRecordOne = array('m_sample_obs_type_code'=>$homo_type_code, 'm_sample_obs_code'=>$homo_code,'display'=>'N');
			$homoValueEntityOne = $this->MSampleObsType->newEntity($homoValueRecordOne);
			$recordDeleted = $this->MSampleObsType->save($homoValueEntityOne);

			// delete second of yes/no combo record
			$getSecondHomoCode = $this->MSampleObsType->find('all', array('conditions'=> array('m_sample_obs_type_value !='=>$homo_value, 'm_sample_obs_code'=>$homo_code, 'display'=>'Y')))->first();
			$homoValueRecordTwo = array('m_sample_obs_type_code'=>$getSecondHomoCode['m_sample_obs_type_code'], 'm_sample_obs_code'=>$getSecondHomoCode['m_sample_obs_code'],'display'=>'N');
			$homoValueEntityTwo = $this->MSampleObsType->newEntity($homoValueRecordTwo);
			$recordDeletedTwo = $this->MSampleObsType->save($homoValueEntityTwo);

		} else {

			$homoValueRecord = array('m_sample_obs_type_code'=>$homo_type_code, 'm_sample_obs_code'=>$homo_code,'display'=>'N');
			$homoValueEntity = $this->MSampleObsType->newEntity($homoValueRecord);
			$recordDeleted = $this->MSampleObsType->save($homoValueEntity);

		}

		if ($recordDeleted) {
			$message = 'Successfully deleted homogenization value!';
			$redirect_to = '../../../../saved-homo-value';
		} else {
			$message = 'Problem in deleting, try again later!';
			$redirect_to = '../../../../saved-homo-value';
		}

		//$this->redirect("/master/saved-homo-value");  // commented by shankhpal shende on 05/09/2022

		// set variables to show popup messages from view file
		$this->set('message', $message);
		$this->set('redirect_to', $redirect_to);

	}


/******************************************************************************************************************************************************************************************************************************************************/


	// list all assigned homogenization
	public function savedAssignHomo(){

		$this->authenticateUser();

		$conn = ConnectionManager::get('default');

		$assign_homos = $conn->execute("SELECT cat.category_code,cat.category_name, com.commodity_name, so.m_sample_obs_desc,com.commodity_code
	 									FROM m_commodity_obs AS a
										INNER JOIN m_commodity_category AS cat ON a.category_code=cat.category_code
										INNER JOIN m_commodity AS com ON com.commodity_code=a.commodity_code
										INNER JOIN m_sample_obs AS so ON so.m_sample_obs_code=a.m_sample_obs_code
										WHERE a.display='Y'");

		$assign_homo_result = $assign_homos->fetchAll('assoc');
		//print_r($assign_homo_result); exit;
		$this->set('assignHomosArray', $assign_homo_result);
		$this->Session->delete('homocategory');
		$this->Session->delete('homocommodity');

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

	public function edithomogenization($category,$commodity){
		$this->Session->write('homocategory', $category);
		$this->Session->write('homocommodity', $commodity);
		$this->redirect('/master/assign-homo');
	}
	// save or update assign homo
	public function assignHomo(){

		$this->authenticateUser();
		error_reporting('0');

		$sessionArray = $this->Session->read();

	  if (array_key_exists("homocategory",$sessionArray) && array_key_exists("homocommodity",$sessionArray))
	  {
			$homocategory = 	$this->Session->read('homocategory');
			$homocommodity = 	$this->Session->read('homocommodity');
			$categories = $this->MCommodityCategory->find('all', array('conditions'=> array('category_code'=>$homocategory,'display'=>'Y'), 'order'=> array('category_name'=>'ASC')));
			$mcommodity = $this->MCommodity->find('all', array('conditions'=> array('category_code'=>$homocategory,'commodity_code'=>$homocommodity,'display'=>'Y'), 'order'=> array('commodity_name'=>'ASC')));

		}else{
			$homocategory = 	'';
			$homocommodity = 	'';
			$categories = $this->MCommodityCategory->find('all', array('conditions'=> array('display'=>'Y'), 'order'=> array('category_name'=>'ASC')));
			$mcommodity = array();
		}
		$this->set(compact('categories'));
		$this->set(compact('mcommodity'));
		$this->set(compact('homocategory'));
		$this->set(compact('homocommodity'));

		$homo_fields = $this->MSampleObs->find('all', array('conditions'=> array('display'=>'Y'), 'order'=> array('m_sample_obs_desc'=>'ASC')));
		$this->set(compact('homo_fields'));



		if(null!==$this->request->getData("save")) {

			$this->loadModel('MCommodityObs');

			$commodity_code = htmlentities($this->request->getData("commodity_code"),ENT_QUOTES);
			$category_code = htmlentities($this->request->getData("category_code"),ENT_QUOTES);
			$homochecks = $this->request->getData("homocheck");

			foreach ($homochecks as $key => $value) {

					$homovalue = htmlentities($value);

					$ModelEntity = $this->MCommodityObs->newEntity(array(
						'm_sample_obs_code'=>$homovalue,
						'category_code'=>$category_code,
						'commodity_code'=>$commodity_code,
						'user_code'=>$_SESSION['user_code'],
						'display'=>'Y',
						'created'=>date('Y-m-d H:i:s'),
						'modified'=>date('Y-m-d H:i:s'),
					));

					$this->MCommodityObs->save($ModelEntity);
			}

			$message = 'Assign the homogenization values Successfully !';
			$redirect_to = 'saved-assign-homo';

			$this->set('message', $message);
			$this->set('redirect_to', $redirect_to);

		}
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


	 public function savedTestType(){

	 	$this->Session->delete('testcodeid');

		$this->authenticateUser();
		
		$this->loadModel('MTest');

		$tests = $this->MTest->find('all', array('join'=>array(array('table'=>'m_test_type','alias'=>'mtt','type'=>'INNER','conditions'=>array('MTest.test_type_code = mtt.test_type_code'))),
																							'fields'=> array('MTest.test_name', 'MTest.test_code','MTest.l_test_name','mtt.test_type_name'), 'conditions'=> array('MTest.display'=>'Y')))->toArray();
		
		$this->set(compact('tests', $tests));
	 }


/******************************************************************************************************************************************************************************************************************************************************/
	
	// for test edit functionality, Done by Pravin Bhakare 09-11-2021
	public function editTest($id){

		$this->Session->write('testcodeid', $id);
		$this->redirect('/master/add-test');
	}


/******************************************************************************************************************************************************************************************************************************************************/

	// updated the test saving function, Done by Pravin Bhakare 09-11-2021
	public function addTest(){


		$this->loadModel('MTestType');
		$this->loadModel('MTest');

		$title = "Add Test";
		$test_name = '';
		$l_test_name = '';
		$test_type_code = '';
		$testcodeid = '';
		$message = '';
		$redirect_to = '';



		$sessionArray = $this->Session->read();

	  	if (array_key_exists("testcodeid",$sessionArray)){

	  		$testcodeid = $this->Session->read('testcodeid');
	  		$test_data = $this->MTest->find('all', array('conditions'=> array('test_code'=>$testcodeid)))->first();
	  		$test_name = $test_data['test_name'];
			$l_test_name = $test_data['l_test_name'];
			$test_type_code = $test_data['test_type_code'];
			$title = "Edit Test";
	  	}
		

		$test_types  = $this->MTestType->find('list',array('keyField'=>'test_type_code','valueField'=>'test_type_name'
																	,'conditions' => array('display' => 'Y'),'order' => array('test_type_name' => 'ASC')))->toArray();
        
		
		if (null!==($this->request->getData('save'))){

			$test_type_value = htmlentities($this->request->getData('test_type'),ENT_QUOTES);
			$test_name_value = htmlentities($this->request->getData('test_name'),ENT_QUOTES);
			$l_test_name_value = htmlentities($this->request->getData('l_test_name'),ENT_QUOTES);

			if(empty($testcodeid)){

				$duplicate_recored = $this->MTest->find('all', array('conditions'=> array('lower(trim(test_name)) IS'=>strtolower((trim($test_name_value))))))->first();
			
			}else{
				
				$duplicate_recored = $this->MTest->find('all', array('conditions'=> array('lower(trim(test_name)) IS'=>strtolower(trim($test_name_value)),'test_code !='=>$testcodeid)))->first();
			}
	
			if( $test_type_value != ""
			    && $test_name_value != ""
			    && $l_test_name_value != ""
			    && array_key_exists($test_type_value,$test_types)
			    && empty($duplicate_recored))
			{
				
				
				$entityRecord = $this->MTest->newEntity(array(
					'test_code'=>$testcodeid,
					'test_name'=>$test_name_value,
					'l_test_name'=>$l_test_name_value,
					'test_type_code'=>$test_type_value,
					'user_code'=>$_SESSION['user_code'],
					'display'=>'Y'
				));
					
				if($this->MTest->save($entityRecord)){

					$message = 'Test record saved Successfully';
					$redirect_to = '../master/saved-test-type';
			
				}else{

					$message = 'Test record not saved, something went wrong';
					$redirect_to = '../master/saved-test-type';
				}	

			}else{

				$message = 'Something went wrong in form data, please check and resubmit again';
				$redirect_to = '../master/add-test';
			}

		}

		

		$this->set('test_types', $test_types);
        $this->set('test_name', $test_name);
        $this->set('l_test_name', $l_test_name);
        $this->set('test_type_code', $test_type_code);
        $this->set('title',$title);
        $this->set('message',$message);
        $this->set('redirect_to',$redirect_to);
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

	// function updated as new flow, Done by Pravin Bhakare 09-11-2021
	public function testFields() {

		$this->Session->delete('assigntestcode');
		$this->Session->delete('assigntestcodestatus');

		$this->authenticateUser();
		$this->loadModel('MFields');
		$conn = ConnectionManager::get('default');

		$assignTest = $conn->execute("select mt.test_code,mt.test_name,
										case when at.status_flag = 'F' then 'Finalize' else 'Not Finalize' end status from m_test mt
										inner join ( select test_code,status_flag from test_fields where display='Y' group by test_code,status_flag) as at ON at.test_code = mt.test_code
										");

		$assignTestResult = $assignTest->fetchAll('assoc');
		$this->set('testFields', $assignTestResult);

	 }

	 public function editAssignTestFields($test_code,$status){

	 	$this->authenticateUser();
	 	$this->Session->write('assigntestcode',$test_code);
	 	$this->Session->write('assigntestcodestatus',$status);
	 	$this->redirect('/master/assign-test-fields');

	 }

	 public function assignTestFields(){

	 	$this->authenticateUser();
	 	$this->loadModel('m_fields');

	 	$conn = ConnectionManager::get('default');
	 	$test_code = null;
	 	$sessionArray =  $this->Session->read();

	 	if (array_key_exists("assigntestcode",$sessionArray))
	 	{
	 		$test_code = $this->Session->read('assigntestcode');
	 		$test_query = $conn->execute("select mt.test_code,mt.test_name from m_test mt WHERE test_code = '$test_code'");

	 	}else{

	 		$test_query = $conn->execute("select mt.test_code,mt.test_name from m_test mt WHERE test_code NOT IN ( select test_code from test_fields where display='Y' group by test_code) order by mt.test_name");
	 	}
	 	
	 	$test_names = $test_query->fetchAll('assoc');
	 	//print_r($test_names); exit;
	 	

        $test_fields = $this->m_fields->find('all', array('order' => array('field_name' => 'ASC'),
            'fields' => array(
                'field_code',
				 'l_field_name',
                'field_name',
				
            ) ,
			'conditions' => array('display' =>'Y')
        ))->toArray();


        $this->set('test_names',$test_names);
        $this->set('test_fields',$test_fields);
        $this->set('test_code',$test_code);
	 }


	 public function savedAssignedTestFields() {

	 	$conn = ConnectionManager::get('default');
	 	$this->loadModel('TestFields');
	 	$this->loadModel('m_fields');

	 	$test_code = $this->request->getData("test_code");
	 	

	 	if(!isset($test_code) || !is_numeric($test_code)){
			echo "0";
			exit;
		}else{

			$test = $this->request->getData("field_arr");
			$fields = explode("-",$test);
			$field_code_data = "";


			$data1 = $this->TestFields->find('all', array( 'conditions' => array(
										'test_code' => $test_code),'order' => array('field_value' => 'desc')))->toArray();

		
			if(strlen($test)>0)
			{

				for($i=0;$i<count($fields);$i++)
				{

					$field = explode("~",$fields[$i]);
					$field_code = $field[0];
					$field_code_data.=$field_code.",";
					$field_range = $field[1];
					$field_type = $field[2];
					$unit = $field[3];
					 
					$data = $this->TestFields->find('all', array( 'conditions' => array(
					'test_code' => $test_code,'field_code' => $field_code)))->count();

					$b=array();
					
					if($field_type==true)
					{
						$test1 = $this->m_fields->find('all', array('join' => array(
									 array(
										'table' => 'm_test',
										'alias' => 'b',
										'type' => 'INNER',
										'conditions' => array(
											'b.test_name = m_fields.field_name'
										)
									)
								),
								'fields' => array(
									'b.test_code',
								),
								'conditions' => array('m_fields.field_code' => $field_code)))->toArray();

						
						if($test1[0]['b']['test_code']!=''){

							$b['dep_test_code']	= $test1[0]['b']['test_code'];
							$dep_test_code	=	$b['dep_test_code'];
							
							$b['field_type'] = "D";
							$pqr = $b['field_type'];

						}else{

							$b['dep_test_code'] = 0;
							$dep_test_code = $b['dep_test_code'];
							
							$b['field_type'] = "N";
							$pqr = $b['field_type'];
						}


					}else{

						$b['field_type'] = "N";
						$pqr = $b['field_type'];
						
						$b['dep_test_code'] = 0;
						$dep_test_code = $b['dep_test_code'];

					}



					$data2 = $this->TestFields->find('all', array( 'conditions' => array(
										'test_code' => $test_code),'order' => array('field_value' => 'desc')))->toArray();

					$a = array();

					if($data<1)
					{

						if(count($data2)<1)
						{
							$a['field_value'] = 'a';
							$abc = $a['field_value'];
						}
						else{

							$a['field_value'] =++$data2[0]['field_value'];
							$abc = $a['field_value'];
						}

						$conn->execute("insert into test_fields (field_validation,field_type,dep_test_code,field_value,field_code,test_code,field_unit)values('$field[1]','$pqr','$dep_test_code','$abc','$field[0]',$test_code,'$field[3]')");

					}else{
						
						$conn->update('test_fields',['field_validation'=>$field[1],'dep_test_code'=>$dep_test_code,'field_type'=>$field[2],'field_unit'=>$field[3]],['field_code'=>$field[0],'test_code'=>$test_code]);

					}

				}

				$field_code_data=trim($field_code_data,",");
				$conn->execute("delete from test_fields where test_code=$test_code and field_code NOT IN ($field_code_data)");

			}
			else
			{
				$field_code_data=trim($field_code_data,",");
				$conn->execute("delete from test_fields where test_code=$test_code");
			}

			echo 1;
			exit;

		}


	 }



	public function finalizeAssignTestFields() {
		
		$conn = ConnectionManager::get('default');        

		$test_code = $this->request->getData("test_code");

	 	if(!isset($test_code) || !is_numeric($test_code)){
			echo "0";
			exit;
		}else{

			$conn->execute("update test_fields set status_flag='F' where test_code=$test_code");     		
		 	echo "1";exit;
		 }
		
    }


	public function getTestFieldsData() {
		
		$test_code = $this->request->getData("test_code");

		$this->loadModel('TestFields');

        $test_fields = $this->TestFields->find('all', array(
		'joins' => array(
                array(
                    'table' => 'm_fields',
                    'alias' => 'a',
                    'type' => 'INNER',
                    'conditions' => array(
                        'a.field_code = TestFields.field_code'
                    )
                )
            ),
            'fields' => array(
                           
				'field_validation',
				'field_type',
				'field_code',
				'field_unit',
            ),
            'conditions' => array( 'TestFields.test_code' =>$test_code )))->toArray();
			 
		echo json_encode($test_fields);
	  	exit;
    }


/******************************************************************************************************************************************************************************************************************************************************/

	public function commodityGrade(){

		$this->authenticateUser();

		$str1="";
		$message = '';
		$redirect_to = '';

		$this->loadModel('MCommodityCategory');
		$this->loadModel('MGradeDesc');
		$this->loadModel('CommGrade');
		$this->loadModel('MGradeStandard');
		$this->loadModel('MTestMethod');

	 	$commodity_category = $this->MCommodityCategory->find('list',array('keyField'=>'category_code','valueField'=>'category_name','order' => array('category_name' => 'ASC'),'conditions' => array('display' => 'Y')))->toArray();
		$this->set('commodity_category',$commodity_category);

		$grades=$this->MGradeDesc->find('all',array('order' => array('grade_desc' => 'ASC'),'conditions' => array('display' => 'Y')))->toArray();
	 	$this->set('grades',$grades);

		$grades_strd=$this->MGradeStandard->find('list',array('keyField'=>array('grd_standrd'),'valueField'=>array('grade_strd_desc'),'order' => array('grade_strd_desc' => 'ASC'),'conditions' => array('display' => 'Y')))->toArray();
	 	$this->set('grades_strd',$grades_strd);

		$methods=$this->MTestMethod->find('all',array('order' => array('method_name' => 'ASC'),'conditions' => array('display' => 'Y')))->toArray();
		$this->set('methods',$methods);

		$minmax =  array('Range'=>'Range','Min'=>'Min','Max'=>'Max');
		$this->set('minmax',$minmax);


		if ($this->request->is('post')) {

			$postData = $this->request->getData();

			$modifiedData = 'false';
			$message = '';
			$newGradeCode = array();


			if(array_key_exists("grade_code",$postData) && array_key_exists("grade_order",$postData))
			{

				foreach($postData as $key => $eachField)
				{

					if($key == 'grade_code' || $key == 'grade_order')
					{
						foreach($eachField as $eachValue){

							if(!is_numeric($eachValue)){
								
								$modifiedData = 'true';
							}
						}

						if(count($postData['grade_code']) != count($postData['grade_order'])){
							
							$modifiedData = 'true';
						}

					}elseif($key == 'min_max' && isset($postData['grade_value']) && ( $postData['grade_value'] != 'Positive' || $postData['grade_value'] != 'Negative')){

						if(!in_array($eachField,$minmax)){
							
							$modifiedData = 'true';
						}

					}elseif($key == 'min_max'){

						if(!in_array($eachField,$minmax)){

							$modifiedData = 'true';
						}

					}elseif($key == 'grade_value' && $postData['min_max'] == ''){

						if(!in_array($eachField,array('Positive','Negative'))){
							
							$modifiedData = 'true';
						}
					}
					else{

						if($key != 'save' && !is_numeric($eachField)){
							$modifiedData = 'true';
						}
					}

				}

				$message = 'Something went wrong, please checked properly forms value and then resubmit again';

			}else{
				$message = "Grade and grade order not selected";
				$modifiedData = 'true';
			}

			
			if($modifiedData == 'false'){

				foreach($postData['grade_code'] as $grade_code){

					// check the conditions for test code is already define test method or not, done by pravin bhakare, 17-12-2019
					$is_already_present = $this->CommGrade->find('all',array('fields'=>array('method_code'),'conditions'=>array('category_code'=>$postData['category_code'],'commodity_code'=>$postData['commodity_code'],'test_code'=>$postData['test_code'],'grade_code'=>$grade_code),'group'=>array('method_code')))->toArray();

					if (!empty($is_already_present)) {


						$count_result =  count($is_already_present);

				 		if ($count_result == 1) {

							$test_method = $is_already_present[0]['method_code'];

							$this->loadModel('MTestMethod');
							$method_name = $this->MTestMethod->find('all',array('fields'=>array('method_name'),'conditions'=>array('method_code'=>$test_method)))->first();
							$methodName = $method_name['method_name'];
							$message = 'Test method defined for this test is '.$methodName.'. So, The test method can not be changed.';
							break;

						} else {

							$message = 'More than one methods are defined for this test. So, The test method can not be changed.';
							break;

						}

					}else{

						$newGradeCode[] =  $grade_code;
					}

				}


				if(!empty($newGradeCode)){

					if($postData['min_max'] == ''){

						$postData['min_max'] = '-1';
						$postData['max_grade_value'] = NULL;

					}elseif($postData['min_max'] == 'Min'){

						$postData['max_grade_value'] = NULL;

					}elseif($postData['min_max'] == 'Max'){

						$postData['grade_value'] = NULL;
					}

					foreach($newGradeCode as $newCode){


						$newEntity = $this->CommGrade->newEntity(array(

								'category_code'	=>	$postData['category_code'],
								'commodity_code' => $postData['commodity_code'],
								'test_code' => $postData['test_code'],
								'method_code' => $postData['method_code'],
								'grd_standrd' => $postData['grd_standrd'],
								'grade_code' => $newCode,
								'grade_value' => $postData['grade_value'],
								'max_grade_value' => $postData['max_grade_value'],
								'min_max' => $postData['min_max'],
								'grade_order' => $postData['grade_order'][$newCode],
								'user_code' => $postData['user_code'],
								'display' => 'Y',
								'login_timestamp' => date('Y-m-d H:i:s'),
								'created' => date('Y-m-d H:i:s'),
								'modified' => date('Y-m-d H:i:s'),
						   ));

						$this->CommGrade->save($newEntity);

					}

					$message = 'Records has been Saved!';
				}

			}

 		}

 		$redirect_to = 'commodityGrade';
 		$this->set('message',$message);
 		$this->set('redirect_to',$redirect_to);

	}



	public function getCommodity(){

		$conn = ConnectionManager::get('default');

		$category_code = $this->request->getData('category_code');


		if(!isset($category_code) || !is_numeric($category_code)){
			echo "0";
			exit;

		}else{

			$str="";
		
			$commodity=	$conn->execute("select * from m_commodity where category_code=$category_code  and display='Y' order by commodity_name asc")->fetchAll('assoc');
		 
			
			if(count($commodity)==0)
			{
				echo 0;

			}else{

				for($i=0;$i<count($commodity);$i++)
				{
					$str.="<option value='".$commodity[$i]['commodity_code']."'>".$commodity[$i]['commodity_name']."</option>";					
				}
				echo $str;
			}


		}
		

		exit;
	}


	public function getTestByCommodityId()
	{
			
		$conn = ConnectionManager::get('default');

		$commodity_code = $this->request->getData('commodity_code');
		$category = array();

		if(!isset($commodity_code) || !is_numeric($commodity_code)){
			echo "0";
			exit;

		}else{

			
		$category = $conn->execute("select mt.test_code,mt.test_name from commodity_test ct
		  Inner join m_test as mt on mt.test_code = ct.test_code
		  where ct.commodity_code = $commodity_code
		  order by mt.test_name")->fetchAll('assoc');

		}
				
		if(count($category)==0){
			echo 0;
		}else{
			echo json_encode($category);
		}
				
		exit;
	}


	//below function is added on 21-12-2019 by Amol to get method list on test select
	//called through ajax code
	public function getTestMethods(){
					
		$test_code = $this->request->getData('test_code');

		if(!isset($test_code) || !is_numeric($test_code)){

			echo "0";

		}else{

			$this->loadModel('MTestMethod');
			$this->loadModel('TestFormula');
			
			$added_methods_with_test = $this->TestFormula->find('list',array('valueField' => 'method_code','conditions' => array('test_code'=>$test_code,'display' => 'Y')))->toArray();
			
			
			if(!empty($added_methods_with_test)){

				$methods = $this->MTestMethod->find('all',array('fields'=>array('method_code','method_name'),'order' => array('method_name' => 'ASC'),'conditions' => array('method_code IN'=>$added_methods_with_test,'display' => 'Y')))->toArray();

				$testMethods = array();

				if(!empty($methods)){

					foreach ($methods as $row1){

						$testMethods[] = $row1;
					}
				}

				echo json_encode($testMethods);
				
			
			}else{ echo '1'; }

		}

		exit;
		
	}


	public function getTestType(){
		
		$conn = ConnectionManager::get('default');

		$test_code = $this->request->getData('test_code');

		if(!isset($test_code) || !is_numeric($test_code)){

			echo "0";

		}else{

			$test_type = $conn->execute("Select mt.test_type_code,mtt.test_type_name from m_test as mt Inner join m_test_type as mtt On mtt.test_type_code=mt.test_type_code and mt.test_code=$test_code")->fetchAll('assoc');

			echo json_encode($test_type);
		}
		
		exit;
	}



	public function viewCommGradeList()
	{

		$conn = ConnectionManager::get('default');
	
		$str	= "select cat.category_name, com.commodity_name,t.test_name ,g.grade_desc,a.min_max, 
					(CASE WHEN (a. max_grade_value IS NULL OR a. max_grade_value = '') THEN a. max_grade_value ELSE a. grade_value END) as grade_value
					,a. max_grade_value ,
					(CASE WHEN (a. max_grade_value IS NULL OR a. max_grade_value = '') THEN a. grade_value ELSE NULL END)
					 as singleVal from comm_grade as a
					inner join m_grade_desc as g on g.grade_code=a.grade_code
					inner join m_commodity_category as cat on  a.category_code=cat.category_code
					inner join m_commodity as com on com.commodity_code=a.commodity_code
					inner join m_test as t on t.test_code=a.test_code where ";
		
		if($_POST['category_code']!=0)
			$str.="a.category_code=".$_POST['category_code']." and ";
		
		if($_POST['commodity_code']!=0)
			$str.="a.commodity_code=".$_POST['commodity_code']." and ";
		
		if($_POST['test_code']!=0)
			$str.="a.test_code=".$_POST['test_code']." and ";
		
		$str.=" a.display='Y' order by t.test_name asc";
				
		$res = $conn->execute($str)->fetchAll('assoc');
												
		echo json_encode($res);
		exit;
	}


/******************************************************************************************************************************************************************************************************************************************************/


public function createFormula(){

		$this->authenticateUser();
		$this->loadModel('MTest');
		$this->loadModel('MTestType');
		$this->loadModel('CommodityTest');
		$this->loadModel('MTestMethod');


		$flag = false;
		$conn = ConnectionManager::get('default');

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

			$test_names[$Test['test_code']] = $Test['test_name'];
			$i++;
		}

		$this->set('test_names', $test_names);

		$method = $this->MTestMethod->find('list',array('keyField'=>'method_code','valueField'=>'method_name','order' => array('method_name' => 'ASC'),'conditions' => array('display' => 'Y')))->toArray();			
		$this->set('method',$method);


      	if ($this->request->is('post')) {

      		$unit = '';

			/*if (isset($this->request->getData('unit')) {

				$unit = $this->request->getData('unit');
			} */

       		$test_type = $this->request->getData('type');


       		$test_code = $this->request->getData('test_code');
			$method_code = $this->request->getData('method_code');
			$startdate = $this->request->getData('start_date');


       		if(!is_numeric($test_code) || !is_numeric($method_code)
					||  $start_date == ''){

					$message = 'Invalid form data, please checked properly and resubmit';
					$redirect_to = 'create-formula';
			}else{

				$dStart = new DateTime(date('Y-m-d H:i:s'));

				$date = $dStart->createFromFormat('d/m/Y', $startdate);
				$start_date = $date->format('Y/m/d');
				$start_date = date('Y-m-d',strtotime($start_date));

				$rttttttv1 = preg_match('/^[0-9]{1}$/',$field_validation);

	 			if ($rttttttv1==0) {

					$message ='Please enter a validation range in proper format';
					$redirect_to = 'create-formula';
				}

			}


        	if($test_type == "f"){
				
				$field_validation = $this->request->getData('field_validation_range');
				$unit = $this->request->getData('unit');
				$formula = $this->request->getData('formula');


				if($field_validation == '' || $unit == '' || $formula == ''){

					$message = 'Invalid form data, please checked properly and resubmit';
					$redirect_to = 'create-formula';
				}
				

				if($message != ''){


					$resl = $this->Customfunctions->hasMatchedParenthesis($formula);
					$formula1 = $formula;
					$final_form = array();


					preg_match_all(" /[*%+-].[*%+-]/", $formula, $matches1);
					preg_match_all("/\[[^\]]*\]/", $formula, $matches);


					for ($i = 0; $i < count($matches[0]); $i++) {

						$final_form[$i] = trim($matches[0][$i], "[]");

						$str = $conn->execute("SELECT field_value
											   FROM test_fields
											   WHERE field_code=(SELECT field_code FROM m_fields WHERE  field_name = '$final_form[$i]')
											   AND test_code = $test_code
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

								$str = "UPDATE test_formula
										SET end_date='$start_date' ,  unit='$unit'
								 		WHERE  test_code=$test_code
								  		AND method_code=".$data1[0][0]['method_code']." AND end_date is null";

			   					if (!$this->MTest->query($str)) {

				   					// $this->Session->setFlash('The formula has been updated!');
								} else {
				   					// $this->Session->setFlash('The formula has not been updated!');

								}

								$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range,unit)VALUES($test_code,$method_code,'$start_date',
													 Null,'$formula1','$formula','$field_validation','$unit')");

		                		$message = 'The formula has been added!';
								$redirect_to = 'create-formula';

							} else {

								$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range,unit)
													VALUES($test_code,$method_code,'$start_date',
													Null,'$formula1','$formula','$field_validation','$unit')");
								$message = 'The formula has been added!';
								$redirect_to = 'create-formula';
							}

						}else{

							$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range,unit)VALUES($test_code,$method_code,'$start_date',Null,'$formula1','$formula','$field_validation','$unit')");

							$message = 'The formula has been added!';
							$redirect_to = 'create-formula';

						}

              		}else{

              			$message = 'Incorrect formula,Please check formula for operands AND operator precedence';
						$redirect_to = 'create-formula';
					}
				}

			}elseif ($test_type == "s") {
  	             	

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

							$message = 'The formula has been added!';
							$redirect_to = 'create-formula';

						} else {

								$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)
													 VALUES($test_code,$method_code,'$start_date'
													 Null,'$formula1','$formula','$field_validation')");

                        		$message = 'The formula has been added!';
								$redirect_to = 'create-formula';
						}

					} else {

						$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)
											 VALUES($test_code,$method_code,'$start_date',
											 Null,'$formula1','$formula','$field_validation')");

                		$message = 'The formula has been added!';
						$redirect_to = 'create-formula';
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

                   	
                    $res = $this->MTest->query("SELECT max(field_code) AS id FROM m_fields ");
                    $last_id = $res[0][0]['id'];
                    $this->MTest->query("INSERT INTO test_fields (test_code,field_code)values($test_code,$last_id)");

				}


            } elseif ($test_type == "r") {

				$formula1="RT";
				$field_validation = 0;

				$data1 = $this->MTest->query("SELECT test_code FROM test_formula WHERE  test_code='$test_code' AND method_code='$method_code'");

	    		$data2 = $this->MTest->query("SELECT test_code,method_code FROM test_formula WHERE  test_code='$test_code' AND end_date is null  ");

                $data = $this->MTest->query("SELECT field_code FROM test_fields WHERE  test_code='$test_code'");

                if (count($data) > 0) {

                   $id = $data[0][0]['field_code'];

                   $data = $this->MTest->query("UPDATE m_fields SET  field_name='$formula' WHERE field_code=$id");


					if (count($data1) > 0) {

						if(count($data2) > 0) {

							if (!$this->MTest->query("UPDATE test_formula SET end_date='$start_date'
															  WHERE method_code=".$data2[0][0]['method_code']."  AND test_code=$test_code AND end_date is null")) {

												//$this->Session->setFlash('The formula has been updated!');
							} else {

								//$this->Session->setFlash('The formula has not been updated!');
							}

							$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)values($test_code,$method_code,'$start_date',Null,'$formula1','$formula','$field_validation')");

							$message = 'The formula has been added!';
							$redirect_to = 'create-formula';

						} else {

							$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)values($test_code,$method_code,'$start_date',Null,'$formula1','$formula','$field_validation')");

							$message = 'The formula has been added!';
							$redirect_to = 'create-formula';
						}

					} else {

						$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)values($test_code,$method_code,'$start_date',Null,'$formula1','$formula','$field_validation')");

						$message = 'The formula has been added!';
						$redirect_to = 'create-formula';
					}
					
               	} else {

                    $this->loadModel('MCommodityCategory');
                    $data = $this->MCommodityCategory->query("SELECT * FROM m_fields ORDER BY field_code DESC limit 1");
                    $alphabet = $data[0][0]['field_value'];

			 		if(count($data)>0) {

            			$last_alphabet = ++$alphabet;

			 		} else {

				   		$last_alphabet = 'a';

			 		}

						

					$res = $this->MTest->query("SELECT max(field_code) AS id FROM m_fields ");
					$last_id = $res[0][0]['id'];

					$this->MTest->query("INSERT INTO test_fields (test_code,field_code)values($test_code,$last_id)");
				}

			}else{

				if($test_type == "p"){

					$formula1="PN";
					$formula = "PN";
					$field_validation = 0;

					$data = $this->MTest->query("SELECT test_code FROM test_formula WHERE  test_code='$test_code' AND method_code='$method_code'");

					$data1 = $this->MTest->query("SELECT test_code,method_code FROM test_formula WHERE  test_code='$test_code' AND end_date is null  AND test_formulae='PN'");

					if (count($data) > 0) {

						if(count($data1) > 0) {
							
							if (!$this->MTest->query("UPDATE test_formula SET end_date='$start_date' WHERE  method_code=".$data1[0][0]['method_code']." AND test_code=$test_code AND end_date is null")) {

								//$this->Session->setFlash('The formula has been updated!');
							} else {
								//$this->Session->setFlash('The formula has not been updated!');
							}

							$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)values($test_code,$method_code,'$start_date',Null,'$formula1','$formula','$field_validation')");
							
							$message = 'The formula has been added!';
							$redirect_to = 'create-formula';

						} else {

							$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)values($test_code,$method_code,'$start_date',Null,'$formula1','$formula','$field_validation')");

							$message = 'The formula has been added!';
							$redirect_to = 'create-formula';
						}

					} else {

						$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)values($test_code,$method_code,'$start_date',Null,'$formula1','$formula','$field_validation')");

						$message = 'The formula has been added!';
						$redirect_to = 'create-formula';
					}

				} elseif ($test_type == "y") {


					$formula1="YN";
					$formula = "YN";
					$field_validation = 0;

					$data = $this->MTest->query("SELECT test_code FROM test_formula WHERE  test_code='$test_code' AND method_code='$method_code' AND test_formulae='YN'");

					$data1 = $this->MTest->query("SELECT test_code,method_code FROM test_formula WHERE  test_code='$test_code' AND end_date is null  ");

					if (count($data) > 0) {

						if (count($data1) > 0) {
							
							if (!$this->MTest->query("UPDATE test_formula SET end_date='$start_date' WHERE  test_code=$test_code AND end_date is null")) {
									//$this->Session->setFlash('The formula has been updated!');
							} else {
								//$this->Session->setFlash('The formula has not been updated!');
							}

							$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)values($test_code,$method_code,'$start_date',Null,'$formula1','$formula','$field_validation')");
							
							$message = 'The formula has been added!';
							$redirect_to = 'create-formula';

						} else {

							$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)values($test_code,$method_code,'$start_date',Null,'$formula1','$formula','$field_validation')");
									
							$message = 'The formula has been added!';
							$redirect_to = 'create-formula';
						}

					} else {

						$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)values($test_code,$method_code,'$start_date',Null,'$formula1','$formula','$field_validation')");

						$message = 'The formula has been added!';
						$redirect_to = 'create-formula';

					}


				} elseif ($test_type == "PA") {
	
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

							$message = 'The formula has been added!';
							$redirect_to = 'create-formula';

						} else {

							$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)values($test_code,$method_code,'$start_date',Null,'$formula1','$formula','$field_validation')");

							$message = 'The formula has been added!';
							$redirect_to = 'create-formula';
						}

					} else {

						$this->MTest->query("INSERT INTO test_formula (test_code,method_code,start_date,end_date,test_formulae,test_formula1,res_validation_range)values($test_code,$method_code,'$start_date',Null,'$formula1','$formula','$field_validation')");
						$message = 'The formula has been added!';
						$redirect_to = 'create-formula';
					}
				}	
			}
		}
	}


	function getRecord(){

		$conn = ConnectionManager::get('default');

		$test = $this->request->getData('test');


		if(!isset($test) || !is_numeric($test)){
			echo "0";
			exit;

		}else{

			$fields = $conn->execute("select  distinct tf.id,mt.test_name,mtm.method_name,start_date,test_formula1,tf.status_flag,tf.test_code,tf.method_code,mtt.test_type_name from test_formula as tf
				Inner Join m_test as mt on mt.test_code=tf.test_code
				Inner Join m_test_method as mtm on mtm.method_code=tf.method_code
				Inner Join m_test_type as mtt on mtt.test_type_code=mt.test_type_code  where tf.test_code='$test' and tf.display='Y' and tf.end_date is null")->fetchAll('assoc');
			
			


			$fields1 = $conn->execute("select  mtt.test_type_name from m_test as mt
				Inner Join m_test_type as mtt on mtt.test_type_code=mt.test_type_code  where mt.test_code='$test' and mt.display='Y'")->fetchAll('assoc');
				
			if($fields){
				echo json_encode($fields);
			}else{
				echo json_encode($fields1);
			}

			exit;

		}
		 
	}



	public function getFormula() {

        $conn = ConnectionManager::get('default');

        $this->loadModel('MTest');

        $test_code = $this->request->getData('test_select');
        $method_code = $this->request->getData('method_code');

        		
		if(!isset($test_code) || !is_numeric($test_code)){
			echo "0";
			exit;
		}
		
		if(!isset($method_code) || !is_numeric($method_code)){
			echo "0";
			exit;
		}
		
		$test = $conn->execute("select b.test_type_name,a.test_formula1,a.res_validation_range 
								from m_test as mt
		 						LEFT Join test_formula as a ON a.test_code = mt.test_code and a.method_code = $method_code and a.end_date IS NULL
		 						Inner Join m_test_type as b ON b.test_type_code = mt.test_type_code
		 						where mt.test_code = $test_code")->fetchAll('assoc');
        /*print_r("select b.test_type_name,a.test_formula1,a.res_validation_range 
								from m_test as mt
		 						LEFT Join test_formula as a ON a.test_code = mt.test_code and a.method_code = $method_code and a.end_date IS NULL
		 						Inner Join m_test_type as b ON b.test_type_code = mt.test_type_code
		 						where mt.test_code = $test_code"); exit;*/
        echo json_encode($test);
        exit;
    }


    public function getFormulaStatus() {

        $this->loadModel('TestFormula');

		$test_code = $this->request->getData('test_select');
        $method_code = $this->request->getData('method_code');
		
        $count = $this->TestFormula->find('all', array(
            'conditions' =>
            array('test_code' => $test_code,'method_code'=>$method_code,'status_flag' =>'F','end_date IS NULL')
        ))->count();
		echo($count);exit;
    }



    public function getMethod() {

    	$conn = ConnectionManager::get('default');

        $test_code = $this->request->getData('test_select');
        $method_code = $this->request->getData('method_code');

        		
		if(!isset($test_code) || !is_numeric($test_code)){
			echo "0";
			exit;
		}
		
		if(!isset($method_code) || !is_numeric($method_code)){
			echo "0";
			exit;
		}

        $test = $conn->execute("select mtm.method_code,tf.start_date,tf.end_date,tf.unit from 								test_formula as tf
								Inner Join m_test_method as mtm on mtm.method_code=tf.method_code and tf.test_code=$test_code and tf.method_code=$method_code")->fetchAll('assoc');

		if(isset($test)){
			echo json_encode($test);
			exit;
		}
	
    }



    public function getTestParameter() {

        
		$conn = ConnectionManager::get('default');

        $test_code = $this->request->getData('test_select');
		
		if(!isset($test_code) || !is_numeric($test_code)){
			echo "0";
			exit;
		}

		
		$test = $conn->execute("select tf.id,mt.field_name from test_fields tf
		Inner Join m_fields as mt on mt.field_code = tf.field_code
		where tf.test_code = $test_code
		ORDER by mt.field_name")->fetchAll('assoc');

		foreach($test as $key => $value){
			$test_fields[$value['id']] = $value['field_name'];
		}

        echo json_encode($test_fields);
        exit;
    }



    public function testFormula() {

		 $conn = ConnectionManager::get('default');

		  if ($this->request->is('post')) {


                $formula 		= $this->request->getData('formula');
                $test_code 		= $this->request->getData('test_code');
				$patternb		= "/^[\(\[]+[A-z ]+[+*/-][A-z ]+[\]\)]$";
				$patternb		='/^[0-9]{1}$/';
				$resl			= $this->hasMatchedParenthesis($formula);
                $formula1 		= $formula;
                $final_form 	= array();
                preg_match_all(" /[*%+-].[*%+-]/", $formula, $matches1);
                preg_match_all("/\[[^\]]*\]/", $formula, $matches);
				$formulaFields	= '';
				$formulaAlfa	= '';

				if(!isset($test_code) || !is_numeric($test_code)){
					echo "0";
					exit;
				}

				for ($i = 0; $i < count($matches[0]); $i++) {

                    $final_form[$i] = trim($matches[0][$i], "[]");
                    
					$str			= $conn->execute("select field_value from test_fields where field_code=(select field_code from m_fields where  field_name='$final_form[$i]') and test_code=$test_code order by field_value asc")->fetchAll('assoc');
					
					$formulaFields	= $formulaFields."^".$matches[0][$i];
					$formulaAlfa	= $formulaAlfa."^".$str[0]['field_value'];
					$formula1 		= str_replace($matches[0][$i], $str[0]['field_value'], $formula1);
                }
				
                $formula1			= str_replace("[", "", $formula1);
                $formula1 			= str_replace("]", "", $formula1);
				
			 	$formulaFields		= str_replace("[", "", $formulaFields);
                $formulaFields 		= str_replace("]", "", $formulaFields);
				 
                $resl2 				= $this->multiexplode(array("+", "-", "*", "/"), $formula1);
				
                for ($k = 0; $k < count($resl2); $k++) {

                    $string			= str_replace("(", "", $resl2[$k]);
                    $string 		= str_replace(")", "", $string);

                    if (!is_numeric($string)) {
                        if (strlen($string) > 1) {
                            $flag = true;
                        }
                    }
                }
				
			echo $formula1.'~'.$k.'~'.$formulaFields.'~'.$formulaAlfa;//.'~'.$formulaFields
			exit;
		}
    }



    public function hasMatchedParenthesis($string) {
        $len = strlen($string);
        $stack = array();
        for ($i = 0; $i < $len; $i++) {
            switch ($string[$i]) {
                case '(': array_push($stack, 0);
                    break;
                case ')':
                    if (array_pop($stack) !== 0)
                        return false;
                    break;
                case '[': array_push($stack, 1);
                    break;
                case ']':
                    if (array_pop($stack) !== 1)
                        return false;
                    break;
                default: break;
            }
        }
        return (empty($stack));
    }


    public function multiexplode($delimiters, $string) {

        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return $launch;
    }







	// commodity test method starts this funtion name changed FROM commodity_test to assignTestToCommodity on 2021 (listing)
	public function assignTestToCommodity() {

		$this->authenticateUser();
	  	$this->loadModel('CommodityTest');
		$this->loadModel('MTest');
		$this->loadModel('TestFields');
		$this->loadModel('MCommodity');
		$conn = ConnectionManager::get('default');

		$message = '';
		$redirect_to = '';

		//tests list
		$tests = $conn->execute("SELECT t.test_code,t.test_name,t.l_test_name FROM m_test AS t WHERE t.display='Y' group by t.test_code,t.test_name ORDER BY test_name ASC")->fetchAll('assoc');

		
		$result = array();

		$i = 0;
		foreach ($tests as $test) {

			$result[$test['test_code']] = $test['test_name'];
			$i = $i+1;
		}

		$this->set('result', $result);



		 //list commodities
		//$commodity = $conn->execute("SELECT * FROM m_commodity WHERE display='Y' ORDER BY commodity_name ASC")->fetchAll('assoc');

		$commodity = $this->MCommodity->find('list',array('valueField'=>'commodity_name','conditions'=>array('display'=>'Y'),'order'=>'commodity_name'))->toList();
		$this->set('commodity', $commodity);


		if ($this->request->is('post')) {


			$commodity_code = $this->request->getData("commodity_code");
			$test_code = $this->request->getData("test_code");			

			if(!is_numeric($commodity_code) && !is_numeric($test_code)){
			
				$message = "Invalid selection of commodity or test, Please check";
				$redirect_to = "assign-test-to-commodity";

			}else{

				$dep_test_code = $this->TestFields->find('all', array( 'conditions' => array(
					 			 'test_code' => $test_code,'field_type' => 'D')))->toArray();
			

				if(count($dep_test_code)>0)
				{

					for($i=0;$i<count($dep_test_code);$i++)
					{
						$test_code1=$dep_test_code[$i]['dep_test_code'];
						$count1 = $this->CommodityTest->find('count', array( 'conditions' => array('test_code' => $test_code1,'commodity_code' => $commodity_code)));

						if($count1<1)
						{
							$conn->execute("insert into commodity_test(commodity_code,test_code)values($commodity_code,$test_code1)");
						}
					}

				}


				$conn->execute("insert into commodity_test(commodity_code,test_code)values($commodity_code,$test_code)");

				$message = "The Test has been saved successfully";
				$redirect_to = "assign-test-to-commodity";
			}

		}


		$this->set('message',$message);
		$this->set('redirect_to',$redirect_to);

	}

	public function getListUnassignedTestToComm(){

		$conn = ConnectionManager::get('default');

		$commodity_code = $this->request->getData("commodity_code");
	 	

	 	if(!isset($commodity_code) || !is_numeric($commodity_code)){
			echo "0";
			exit;
		}else{

			$unassignedTest = $conn->execute("SELECT t.test_code,t.test_name,t.l_test_name FROM m_test AS t WHERE test_code NOT IN ( SELECT test_code FROM commodity_test WHERE commodity_code = '$commodity_code') ORDER BY test_code")->fetchAll('assoc');
			
			echo json_encode($unassignedTest);
			exit;
		}
	}



	public function getListAssignedTestToComm(){

		$this->loadModel('CommodityTest');

		$commodity_code = $this->request->getData("commodity_code");

		if(!isset($commodity_code) || !is_numeric($commodity_code)){
			echo "0";
		}else{

				$test_fields = $this->CommodityTest->find('all', array(
				'join' => array(
		                array(
		                    'table' => 'm_test',
		                    'alias' => 'a',
		                    'type' => 'INNER',
		                    'conditions' => array(
		                        'a.test_code = CommodityTest.test_code'
		                    )
		                )
		            ),
		            'fields' => array(
		                'CommodityTest.test_code',
						'a.test_name'
		            ),
		            'conditions' => array( 'CommodityTest.commodity_code' =>$commodity_code)))->toArray(); 
			echo json_encode($test_fields);

		}

		exit;

	}


	// get assigned homogenization fields values, done by pravin bhakare 8-11-2021
	public function getHomogenizationFields(){

			$this->autoRender = false;

			$this->loadModel('MCommodityObs');

			$category = $this->request->getData('category_code');
			$commodity = $this->request->getData('commodity_code');

			$assignHomo = $this->MCommodityObs->find('list',array('keyField'=>'m_commodity_obs_code','valueField'=>'m_sample_obs_code',
																							'conditions'=>array('category_code IS'=>$category,'commodity_code IS'=>$commodity),'order'=>'m_commodity_obs_code'))->toArray();
			//print_r($assignHomo); exit;
			echo json_encode($assignHomo);

			exit;

	}



	//////////////////////////////////////////////////////Master Reports Management///////////////////////////////////////


	public function allReports() {
	
		$this->authenticateUser();
        $this->loadModel('MCommodity');
		$this->loadModel('MReport');
		$this->loadModel('MLabel');
		//Set the Layout
		$this->viewBuilder()->setLayout('admin_dashboard');
		
		$label = $this->MReport->find('all', array('order' => array('report_desc' => 'ASC'),'conditions' => array('display' => 'Y')))->toArray();
		
		$this->set('label',$label);
    }


	public function addReports(){

		$this->authenticateUser();
		$this->viewBuilder()->setLayout('admin_dashboard');
		$this->loadModel('MLabel');
		$this->loadModel('MReport');

		$message = '';
		$redirect_to = '';

		$label = $this->MLabel->find('list',array('keyField'=>'label_code','valueField'=>'label_desc','conditions'=>array('display'=>'Y')))->toArray();
		$this->set('reportlebel',$label);

		if ($this->request->is('post')) {

			$modifiedData = 'false';
			$report_label = htmlentities($this->request->getData("report_label"),ENT_QUOTES);
			$report_name = htmlentities($this->request->getData("report_name"),ENT_QUOTES);

			if(!isset($report_label) || !is_numeric($report_label)){
				$modifiedData = 'true';
				$message = 'Something went wrong, please checked properly forms value and then resubmit';
			}

			if($modifiedData == 'false'){

				$is_already_present = $this->MReport->find('all',array('fields'=>'report_desc','conditions'=>array('display'=>'Y','trim(lower(report_desc))'=>strtolower(trim($report_name)))))->first();
				
				if(empty($is_already_present)){

					$newEntity = $this->MReport->newEntity(array(
						'report_desc'=>$report_name,
						'user_code'=>$_SESSION['user_code'],
						'label_code' =>$report_label,
						'display'=>'Y',
						'login_timestamp'=>date('Y-m-d H:i:s'),
						'created'=>date('Y-m-d H:i:s'),
						'modified'=>date('Y-m-d H:i:s'),

					));

					if($this->MReport->save($newEntity)){

						$message = 'New Report Save Sucessfully';
						$redirect_to = 'all-reports';
					}else{
						$message = 'New Report Not Save Sucessfully';
						$redirect_to = 'add-reports';
					}

					

				}else{

					$message = 'Report already exists';
					$redirect_to = 'add-reports';
				}

			}

		}

		$this->set('message',$message);
		$this->set('redirect_to',$redirect_to);

	}


	public function deleteReport(){

		$conn = ConnectionManager::get('default');
		$reportid = $this->request->getData("reportid");

		if(!isset($reportid) || !is_numeric($reportid)){
				 echo 0;
		}else{

			$conn->execute("update m_report set display = 'N' where report_code = $reportid");
			echo 1;
		}

		exit;

	}


	//////////////////////////////////////////////////////Master Reports Management///////////////////////////////////////


	public function setReport(){

		$this->authenticateUser();
		$this->viewBuilder()->setLayout('admin_dashboard');
		$this->loadModel('MReportlabel');

		$message = '';
		$redirect_to = '';

		$conn = ConnectionManager::get('default');

		$userRoles = $conn->execute("select role_code,role_name from user_role where display='Y' order by role_name asc")->fetchAll('assoc');

		$user_roles = array();
		foreach($userRoles as $val){

			$user_roles[$val['role_code']] = $val['role_name'];
		}
		

		$reportCategory = 	$conn->execute("select r.label_code,r.label_desc from m_label as r where display='Y' group by r.label_code,r.label_desc order by label_code asc")->fetchAll('assoc');

		$report_category = array();
		foreach($reportCategory as $rval){

			$report_category[$rval['label_code']] = $rval['label_desc'];
		}


		$modifiedData = 'false';

		if ($this->request->is('post')) {

			$role_code = $this->request->getData('userrole');
			$label_code = $this->request->getData('reportcategory');
			$selreports = $this->request->getData('sreportname');

			if(!is_numeric($role_code)){
				$modifiedData = 'true';
			}

			if(!is_numeric($label_code)){
				$modifiedData = 'true';
			}

			//added new if condn to check no report selected from list 
			//on 15-09-2022 by shreeya while testing
			if(empty($selreports)){
				$modifiedData = 'true';
			}else{
				foreach($selreports as $val){

					if(!is_numeric($val)){
						$modifiedData = 'true';
					}
				}
			}
			
			$oldsetreports = array();

			if($modifiedData == 'false'){

				$selectedReports = $conn->execute("select report_label_code, report_code from m_reportlabel as r where role_code='".$role_code."' and label_code='".$label_code."'")->fetchAll('assoc');

				foreach($selectedReports as $sval){
					$oldsetreports[$sval['report_label_code']] = $sval['report_code'];
				}

				$tsetreports = array_unique(array_merge($selreports,$oldsetreports));
				$array_flip = array_flip($oldsetreports);

				foreach($tsetreports as $fval){

					if(in_array($fval,$selreports) && in_array($fval,$oldsetreports))
					{
						
						$recId = $array_flip[$fval];
						$recStatus = 'Y';
					
					}elseif(in_array($fval,$selreports) && !in_array($fval,$oldsetreports)){

						$recId = '';
						$recStatus = 'Y';

					}elseif(!in_array($fval,$selreports) && in_array($fval,$oldsetreports)){

						$recId = $array_flip[$fval];
						$recStatus = 'N';

					}


					$newEntity = $this->MReportlabel->newEntity(array(
						'report_label_code'=>$recId,
						'report_code'=>$fval,
						'label_code'=>$label_code,
						'role'=>$user_roles[$role_code],
						'role_code'=>$role_code,
						'user_code'=>$_SESSION['user_code'],
						'created'=>date('Y-m-d H:i:s'),
						'modified'=>date('Y-m-d H:i:s')
					));

					$this->MReportlabel->save($newEntity);
				}

				$message = 'Report Save Sucessfully';
				$redirect_to = 'set-report';

			}else{

				$message = 'Please select proper inputs';
				$redirect_to = 'set-report';
			}

		}

		$this->set('message',$message);
		$this->set('redirect_to',$redirect_to);
		$this->set('user_roles',$user_roles);
		$this->set('report_category',$report_category);
	}


	public function getSetReportNames(){

		$conn = ConnectionManager::get('default');
		$userrole = $this->request->getData("userrole");
		$reportid = $this->request->getData("reportid");

		if(!isset($userrole) || !is_numeric($userrole)){
			echo 0;
		}elseif(!isset($reportid) || !is_numeric($reportid)){
			echo 0;
		}else{
			
			$reportNames = 	$conn->execute("select report_code,report_desc from m_report where label_code='".$reportid."' and display='Y' order by report_desc asc")->fetchAll('assoc');


			$selectedReports = $conn->execute("select report_code from m_reportlabel as r where role_code='".$userrole."' and label_code='".$reportid."' and display='Y'")->fetchAll('assoc');

			$selectedReportsCode = array();
			foreach($selectedReports as $srval){

				$selectedReportsCode[] = $srval['report_code'];

			}



			$report_names = array();
			$rlist = '<ul>';
			$i= 1;

			
			foreach($reportNames as $rnval){

				$rlist .= "<li class=''> <label for='ms-opt-".$rnval['report_code']."' class='pl-4'>";
				$rlist .= "<input value='".$rnval['report_code']."' name='sreportname[]' title='".$rnval['report_desc']."' id='".$i."' type='checkbox' ";

				if(in_array($rnval['report_code'],$selectedReportsCode))
				{ 
					$rlist .= 'checked';
				}

				$rlist .= " >";
				$rlist .= $rnval['report_desc'];
				$rlist .= "</label> </li>";

				$i++;
			}

			$rlist .= "</ul>";
			echo $rlist;


		}

		exit;


	}


	function getAlreadySetReports() {

		$conn = ConnectionManager::get('default');
		$userrole = $this->request->getData("userrole");
		$reportid = $this->request->getData("reportid");

		$selectedReports =  array();

		
		if(!isset($userrole) || !is_numeric($userrole)){
			echo 0;
		}elseif(!isset($reportid) || !is_numeric($reportid)){
			echo 0;
		}else{

			$selectedReports = $conn->execute("select mr.report_desc from m_reportlabel as r 
				inner join m_report as mr on mr.report_code = r.report_code
				where r.role_code='".$userrole."' and r.label_code='".$reportid."'")->fetchAll('assoc');
		}
		
		echo json_encode($selectedReports);
		exit;
      
	}


    ///////////////////////////////////////////////// NEW MASTERS [DDO FOR LABS] //////////////////////////////////////////////////////

    //fetch_edit_id_for_ddo
    //Description : This function created to get the id fro ddo record
    //Author : Akash Thakre
    //Date : 03-06-2022

    public  function fetchEditIdForDdo($id){

        $this->Session->write('ddo_table_id', $id);
        $this->redirect('/Master/edit_ddo_to_ral_office');
    }





	public function addDdoToRalOffice(){

		$this->loadModel('DmiRoOffices');
        $this->loadModel('LimsDdoDetails');
        $this->loadModel('DmiPaoDetails');
        $this->loadModel('DmiUsers');

		$message = '';
		$message_theme = '';
		$redirect_to = '';	 

		$get_labs = $this->DmiRoOffices->getLabs();
		
		$getlistfromddo = $this->LimsDdoDetails->find('all')->select(['lab_id'])->combine('id','lab_id')->toArray();
		pr($getlistfromddo); exit;
		$ddolist = $this->DmiPaoDetails->getAllDdoList();

		

        $this->set(compact('get_labs','ddolist'));

	}




    //edit_ddo_to_ral_office
    //Description : This function created to serve the master for DDO that are used to decide PAO officer for LABS
    //Author : Akash Thakre
    //Date : 03-06-2022

    public  function editDdoToRalOffice(){

        $this->loadModel('DmiRoOffices');
        $this->loadModel('LimsDdoDetails');
        $this->loadModel('DmiPaoDetails');
        $this->loadModel('DmiUsers');

		$message = '';
		$message_theme = '';
		$redirect_to = '';	 
        
		if(isset($_SESSION['ddo_table_id'])){
			
		}

		$get_labs = $this->DmiRoOffices->getLabs();
        $getDdo = $this->LimsDdoDetails->getPaoDetails();

        $selectedLab = $this->LimsDdoDetails->getRecordById($_SESSION['ddo_table_id']);
        $lab_id = $selectedLab['lab_id'];
        $ddo_id = $selectedLab['dmi_user_id'];

        $get_posted_office_id = $this->DmiUsers->getUserDetailsById($ddo_id);
        $posted_office_details = $this->DmiRoOffices->getOfficeDetailsById($get_posted_office_id['posted_ro_office']);
        $posted_office = $posted_office_details[0];

        $ddolist = $this->DmiPaoDetails->getAllDdoList();

        $this->set(compact('get_labs','lab_id','ddolist','ddo_id','posted_office'));


		if ($this->request->is('post')) {

			
			$postData = $this->request->getData();

			$ddoname = $this->DmiUsers->getTableID($postData['ddo_id']);
		
			$ral_office = $this->DmiRoOffices->getOfficeDetailsById($postData['ral_office_id']);
			
			$savetheDetails = $this->LimsDdoDetails->saveDetails($postData);
		
			if($savetheDetails == true) {

				
				if (isset($_SESSION['ddo_table_id'])) {
					$message = 'You have edited the DDO <b>: '.base64_decode($ddoname).'</b> for the Labarotary : <b>'.$ral_office[0]."</b>";
				} else {
					$message = 'You have selected the DDO <b>: '.base64_decode($ddoname).'for the Labarotory : <b>'.$ral_office[0]."</b>";
				}

				$message_theme = 'success';
				$redirect_to = 'ddo_for_labs';
			
			} else {

				$message = 'Failed to save the Record';
				$message_theme = 'success';
				$redirect_to = 'ddo_for_labs';
			}
		
		}

		$this->set('message',$message);
		$this->set('message_theme',$message_theme);
		$this->set('redirect_to',$redirect_to);


    }

    //ddo_for_labs
    //Description : This function created to list all the DDO for labs
    //Author : Akash Thakre
    //Date : 03-06-2022

    public  function ddoForLabs(){

        //laod model
        $this->loadModel('LimsDdoDetails');
        $getDdo = $this->LimsDdoDetails->getPaoDetails();
        $this->set('getDdo',$getDdo);

    }






	//fetchEditIdForCharges
    //Description : This function created to list all the sample commercial charges
    //Author : Akash Thakre
    //Date : 22-06-2022

    public  function fetchEditIdForCharge($id){

        $this->Session->write('charge_id', $id);
        $this->redirect('/Master/edit_commercial_charges');
    }



	//delete_id_for_charge
    //Description : This function created to list all the sample commercial charges
    //Author : Akash Thakre
    //Date : 22-06-2022

    public  function deleteIdForCharge($id){

        $this->Session->write('charge_id', $id);
        $this->redirect('/Master/delete_commercial_charges');
    }



	//Commercial Sample Charges
    //Description : This function created to list all the sample commercial charges
    //Author : Akash Thakre
    //Date : 22-06-2022

    public  function commercialCharges(){

        //laod model
        $this->loadModel('LimsCommercialCharges');
        $getAllCharges = $this->LimsCommercialCharges->getAllCharges();
		$category = array();
		$commodity = array();

		if (!empty($getAllCharges)) {

			$i=0;

			foreach ($getAllCharges as $each) {

				//get category name
				$get_category = $this->MCommodityCategory->find('all',array('fields'=>'category_name','conditions'=>array('category_code IS'=>$each['category_code'],'display'=>'Y')))->first();
				//get commodity
				$get_commodity = $this->MCommodity->find('all',array('fields'=>'commodity_name','conditions'=>array('commodity_code IS'=>$each['commodity_code'],'display'=>'Y')))->first();

				if (!empty($get_category) && !empty($get_commodity)) {

					$category[$i] = $get_category['category_name'];
					$commodity[$i] = $get_commodity['commodity_name'];
				}

				$i=$i+1;

				$this->set('category',$category);
				$this->set('commodity',$commodity);
				$this->set('charge',$each['charges']);
			}
		}

        $this->set('getAllCharges',$getAllCharges);

    }



	//addCommercialCharges
    //Description : This function created to list all the sample commercial charges
    //Author : Akash Thakre
    //Date : 22-06-2022

	public function addCommercialCharges(){

		//Load Models
        $this->loadModel('LimsCommercialCharges');
		$this->loadModel('MCommodityCategory');
		$this->loadModel('MCommodity');

		$message = '';
		$message_theme = '';
		$redirect_to = '';	 

		$postData = $this->request->getData();
	
		//category lists
		$commodity_category = $this->MCommodityCategory->find('list',array('valueField'=>'category_name','conditions'=>array('display'=>'Y'),'order'=>'category_name'))->toArray();
		$this->set('commodity_category',$commodity_category);
		

		if ($this->request->is('post')) {

			$saveCharges = $this->LimsCommercialCharges->saveCharges($postData);

			if ($saveCharges == true) {
				$message = 'Commercial Charges Added successfully';
				$message_theme = 'success';
				$redirect_to = 'commercial_charges';
			} else {
				$message = 'Error Occured!!';
				$message_theme = 'failed';
				$redirect_to = 'commercial_charges';
			}

		}

		$this->set('message',$message);
		$this->set('message_theme',$message_theme);
		$this->set('redirect_to',$redirect_to);
	}



	//addCommercialCharges
    //Description : This function created to list all the sample commercial charges
    //Author : Akash Thakre
    //Date : 22-06-2022

	public function editCommercialCharges(){

		//Load Models
        $this->loadModel('LimsCommercialCharges');
		$this->loadModel('MCommodityCategory');
		$this->loadModel('MCommodity');
		
		$record_id = $_SESSION['charge_id'];
		if (!empty($record_id)) {


			$editChargesDetails = $this->LimsCommercialCharges->getChargeById($record_id);	
			$enteredcategorycode = $this->MCommodityCategory->getCategory($editChargesDetails['category_code']);
			$eneteredcommoditycode = $this->MCommodity->getCommodity($editChargesDetails['commodity_code']);

		} else {
			$editChargesDetails = '';
			$enteredcategorycode = '';
			$eneteredcommoditycode = '';
		}

		$this->set('editChargesDetails',$editChargesDetails);
		$this->set('enteredcategorycode',$enteredcategorycode);
		$this->set('eneteredcommoditycode',$eneteredcommoditycode);

		$message = '';
		$message_theme = '';
		$redirect_to = '';	 

		$postData = $this->request->getData();
		

		if ($this->request->is('post')) {

			$saveCharges = $this->LimsCommercialCharges->saveCharges($postData,$record_id);

			if ($saveCharges == true) {
				$message = 'Commercial Charges edited successfully';
				$message_theme = 'success';
				$redirect_to = 'commercial_charges';
			} else {
				$message = 'Error Occured !!';
				$message_theme = 'failed';
				$redirect_to = 'commercial_charges';
			}
		}

		$this->set('message',$message);
		$this->set('message_theme',$message_theme);
		$this->set('redirect_to',$redirect_to);
	}



	//DELETE MASTER RECORD CALL
	public function deleteCommercialCharges() {

		$record_id = $this->Session->read('charge_id');

		$message = '';
		$message_theme = '';
		$redirect_to = '';	 
		
        $this->loadModel('LimsCommercialCharges');

		if($this->LimsCommercialCharges->deleteChargeById($record_id) == true) {
			$message = 'Commercial Charges Deleted successfully';
			$message_theme = 'success';
			$redirect_to = 'commercial_charges';
		} else {
			$message = 'Error Occured !!';
			$message_theme = 'success';
			$redirect_to = 'commercial_charges';
		}

		$this->set('message',$message);
		$this->set('message_theme',$message_theme);
		$this->set('redirect_to',$redirect_to);
	}

}

?>
