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

class NablAccreditationController extends AppController{
	var $name = 'NablAccreditation';
	
	public function beforeFilter($event) {
		parent::beforeFilter($event);

		$username = $this->Session->read('username');
		$this->loadModel('DmiUserRoles');
		$user_access = $this->DmiUserRoles->find('all',array('conditions'=>array('user_email_id IS'=>$username)))->first();

		//check masters role given
		if (empty($user_access)) {
			$this->customAlertPage("Sorry You are not authorized to view this page..");
			exit;
		}
	}

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
		$this->loadModel('LimsLabNablDetails');
		$this->loadModel('LimsLabNablCommTestDetails');
	}

/*==============================================================================================================*/
//to show list of saved sample by current user
	public function nabldetailList(){
		$user_flag = $this->Session->read('user_flag');

		$sampleArray = $this->getSavedSamplesList();
		
		$this->set(compact('sampleArray','user_flag'));
	}

/*============================== Get NABL detail List===========================*/
	
	//created common function to fetch list , to be used for dashboard counts also, on 28-04-2021 by Amol
	public function getSavedSamplesList(){
		
		$conn = ConnectionManager::get('default');
		$user_cd = $this->Session->read('user_code');
		$loc_id = $this->Session->read('posted_ro_office');
		$user_flag = $this->Session->read('user_flag');
		
		/*$query = $conn->execute("SELECT lnd.id, lnd.lab_id, lnd.accreditation_cert_no,lnd.valid_upto_date, lnd.user_id, lnd.category_id, dro.ro_office, mcc.category_name
			FROM lims_lab_nabl_details AS lnd
			LEFT JOIN dmi_ro_offices AS dro ON lnd.lab_id::integer = dro.id
			LEFT JOIN m_commodity_category AS mcc ON lnd.category_id::INTEGER =mcc.category_code
			ORDER BY lnd.id DESC;");*/
			
			$query = $conn->execute("SELECT lnd.id, lnd.lab_id,dro.ro_office, mc.commodity_name
			FROM lims_lab_nabl_comm_test_details AS lnd
			INNER JOIN lims_lab_nabl_details AS mnt ON lnd.lab_id = mnt.lab_id
			INNER JOIN dmi_ro_offices AS dro ON lnd.lab_id::integer = dro.id
			INNER JOIN m_commodity AS mc ON lnd.commodity::INTEGER =mc.commodity_code
			ORDER BY lnd.id DESC;");
			
		$sampleArray = $query ->fetchAll('assoc');
		return $sampleArray;
	}


// ============================================================================================================
	
//to open sample nabl form in edit mode
public function fetchNablId($nabl_id){

	$this->Session->write('nabl_edit_id',$nabl_id);

	$this->redirect('/NablAccreditation/add-nabl');
}

// ============================================================================================================

public function showParametersDropdown(){  

        $this->autoRender = false;
        $this->loadModel('CommodityTest');
        $this->loadModel('MTest');
        $commodity_code_id = $_POST['commodity_code'];

        $Parameters = $this->CommodityTest->find('all', array('fields'=>array('commodity_code','test_code'),'conditions'=> array('commodity_code IS'=>$commodity_code_id, 'display'=>'Y')))->toArray();
        ?>

		
        <?php
		if(!empty($Parameters)){
			?> <ul style="column-gap: 0px;"> <?php
			$i=1;
			foreach($Parameters as $Parameter){ 
				$test = $this->MTest->find('all', array('fields'=>array('test_code','test_name'),'conditions'=> array('test_code IS'=>$Parameter['test_code'], 'display'=>'Y')))->first();
				?>				
					<li class="">
						<label for="ms-opt-<?php echo $i; ?>" class="">																															
							<input value="<?php echo $Parameter['test_code']; ?>" title="<?php echo $test['test_name']; ?>" id="ms-opt-<?php echo $i; ?>" type="checkbox" >  <?php echo $test['test_name']; ?>
						</label>
					</li>
			<?php $i=$i+1;
			}
			?> </ul> <?php
		}else{
			echo "No test parameters available for selected commodity";
		}
		exit;

}


public function	testParameterOption() {

	$this->autoRender = false;
	$this->loadModel('CommodityTest');
	$this->loadModel('MTest');
	$commodity_code_id = $_POST['commodity_code'];

	$Parameters = $this->CommodityTest->find('all', array('fields'=>array('commodity_code','test_code'),'conditions'=> array('commodity_code IS'=>$commodity_code_id, 'display'=>'Y')))->toArray();
	?>

	
	<?php
	if(!empty($Parameters)){
		foreach($Parameters as $Parameter){ 
			$test = $this->MTest->find('all', array('fields'=>array('test_code','test_name'),'conditions'=> array('test_code IS'=>$Parameter['test_code'], 'display'=>'Y')))->first();
			?>				
				<option value="<?php echo $test['test_code']; ?>">  <?php echo $test['test_name']; ?></option>
		<?php 
		}
	}else{
		echo "No test parameters available for selected commodity";
	}
	exit;
}


public function addNabl(){

	//$this->authenticateUser();
	$this->viewBuilder()->setLayout('admin_dashboard');		
	$this->loadModel('MLabel');
	$this->loadModel('MReport');
	$this->loadModel('DmiRoOffices');
	$this->loadModel('LimsLabNablCommTestDetails');
	$this->loadModel('MCommodity');

	$commodity_category = $this->MCommodityCategory->find('list',array('valueField'=>'category_name','conditions'=>array('display'=>'Y'),'order'=>'category_name'))->toArray();

	$office = $this->DmiRoOffices->find('list',array('valueField'=>'ro_office','conditions'=>array('office_type IN'=>array('RAL','CAL'),'delete_status IS NULL'),'order'=>'ro_office'))->toArray();

	$this->set('office',$office);
	

	$commodity_list = array();
	$test_list = array();

	$message = '';
	$message_theme = '';
	$redirect_to = '';

	$label = $this->MLabel->find('list',array('keyField'=>'label_code','valueField'=>'label_desc','conditions'=>array('display'=>'Y')))->toArray();
	$this->set('reportlebel',$label);
	
	$get_nabl_details = array();
	$get_nabl_details['accreditation_cert_no']='';
	$get_nabl_details['valid_upto_date']='';
	$get_nabl_details['category_id']='';
	$get_nabl_details['commodity']='';
	$get_nabl_details['tests']='';
	$get_nabl_details['lab_id']='';
	
	$nabl_certificate = '';
	$date_validity = '';
	$category_code = '';
	$commodity_code = '';
	$test_parameters = '';
	$office = '';
	$nabl_edit_id = $this->Session->read('nabl_edit_id');
	if(!empty($nabl_edit_id)){
		$this->loadModel('LimsLabNablDetails');		

		//get details from NABL commodity and test table
		$get_nabl_details = $this->LimsLabNablCommTestDetails->find('all',array('fields'=>array('lab_id','category_id','commodity','tests'),'conditions'=>array('id IS'=>$nabl_edit_id)))->first();
		
		//get cert no and validity date
		$get_cert_no = $this->LimsLabNablDetails->find('all',array('fields'=>array('accreditation_cert_no','valid_upto_date'), 'conditions'=>array('lab_id IS'=>$get_nabl_details['lab_id']),'order'=>'id desc'))->first();
	
		$get_nabl_details['accreditation_cert_no'] = $get_cert_no['accreditation_cert_no'];
		$get_nabl_details['valid_upto_date'] = $get_cert_no['valid_upto_date'];
		$get_nabl_details['tests'] = explode(',',$get_nabl_details['tests']);
		
		//get selected category_code
		$commodity_category = $this->MCommodityCategory->find('list',array('keyField'=>'category_code','valueField'=>'category_name','conditions'=>array('category_code IS'=>$get_nabl_details['category_id'])))->toArray();
		//get commodity for selected category_code
		$commodity_list = $this->MCommodity->find('list',array('keyField'=>'commodity_code','valueField'=>'commodity_name','conditions'=>array('commodity_code IS'=>$get_nabl_details['commodity'])))->toArray();
	
		//get tests for selected commodity_code
		$this->loadModel('CommodityTest');
		$this->loadModel('MTest');
		$get_test_list = $this->CommodityTest->find('list', array('valueField'=>'test_code','conditions'=> array('commodity_code IS'=>$get_nabl_details['commodity'], 'display'=>'Y')))->toArray();
		$test_list = $this->MTest->find('list', array('keyField'=>'test_code','valueField'=>'test_name','conditions'=> array('test_code IN'=>$get_test_list, 'display'=>'Y')))->toArray();
		
		$this->Session->delete('nabl_edit_id');
	}
	
	$this->set('commodity_list',$commodity_list);
	$this->set('commodity_category',$commodity_category);
	$this->set('test_list',$test_list);
	$this->set('get_nabl_details',$get_nabl_details);
	

	if ($this->request->is('post')) {
			
		$nabl_certificate = htmlentities($this->request->getData("nabl_certificate"),ENT_QUOTES);
		$date_validity = htmlentities($this->request->getData("date_validity"),ENT_QUOTES);
		$category_code = $this->request->getData("category_code");
		$commodity_code = $this->request->getData("commodity_code");
		$test_parameters = implode(',',$this->request->getData("test_parameters"));//converting with , sepearated value
		$office = htmlentities($this->request->getData("office"),ENT_QUOTES);
		
		//get NABL details if exist
		$get_details = $this->LimsLabNablDetails->find('all',array('fields'=>'id','conditions'=>array('lab_id IS'=>$office,/*'accreditation_cert_no'=>$nabl_certificate*/),'order'=>'id desc'))->first();

		$record_id = '';
		if(!empty($get_details)){$record_id = $get_details['id']; }//to update record if exist, or create new
		
		//set date format
		$dStart = new \DateTime(date('Y-m-d H:i:s'));

		$date = $dStart->createFromFormat('d/m/Y', $date_validity);
		$date_validity = $date->format('Y/m/d');
		$date_validity = date('Y-m-d',strtotime($date_validity));
		
		$NablDetailsEntity = $this->LimsLabNablDetails->newEntity(array(
			'id'=>$record_id,
			'lab_id'=>$office,
			'accreditation_cert_no'=>$nabl_certificate,
			'valid_upto_date'=>$date_validity,
			'user_id'=>$_SESSION['username'],
			'created'=>date('Y-m-d H:i:s'),
			'modified'=>date('Y-m-d H:i:s'),
		));
			
		$this->LimsLabNablDetails->save($NablDetailsEntity);

		//get NABL commosity and test details if exist
		$get_commo_details = $this->LimsLabNablCommTestDetails->find('all',array('fields'=>'id','conditions'=>array('lab_id IS'=>$office,'category_id IS'=>$category_code,'commodity IS'=>$commodity_code),'order'=>'id desc'))->first();

		$record_id = '';
		if(!empty($get_commo_details)){$record_id = $get_commo_details['id']; }//to update record if exist, or create new
		
		$NablCommTestEntity = $this->LimsLabNablCommTestDetails->newEntity(array(
			'id'=>$record_id,
			'lab_id'=>$office,
			'category_id'=>$category_code,
			'commodity'=>$commodity_code,
			'tests'=>$test_parameters,
		//	'nabl_details_id' => $last_id,
			'user_id'=>$_SESSION['username'],
			'created'=>date('Y-m-d H:i:s'),
			'modified'=>date('Y-m-d H:i:s'),
		));
		$this->LimsLabNablCommTestDetails->save($NablCommTestEntity);
		
		//all NABL details logs table
		$this->loadModel('LimsLabNablLogs');
		$NablLogsEntity = $this->LimsLabNablLogs->newEntity(array(
			'lab_id'=>$office,
			'category_id'=>$category_code,
			'commodity'=>$commodity_code,
			'cert_no'=>$nabl_certificate,
			'valid_upto'=>$date_validity,
			'tests'=>$test_parameters,
			'user_id'=>$_SESSION['username'],
			'created'=>date('Y-m-d H:i:s'),
			'modified'=>date('Y-m-d H:i:s'),
		));
		$this->LimsLabNablLogs->save($NablLogsEntity);
		
		$this->LimsUserActionLogs->saveActionLog('NABL Accreditation Add','Success');
		$message = 'The NABL Accreditation is added successfully.';
		$message_theme = 'success';
		$redirect_to = 'nabldetailList';
	}

	$this->set('message',$message);
	$this->set('message_theme',$message_theme);
	$this->set('redirect_to',$redirect_to);

}



}

?>
