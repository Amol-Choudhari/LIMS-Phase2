<?php

//Note: All $this are converted to $this->Controller in this component. on 11-07-2017 by Amol
//To access the properties of main controller used initialize function.
	namespace app\Controller\Component;
	use Cake\Controller\Controller;
	use Cake\Controller\Component;

	use Cake\Controller\ComponentRegistry;
	use Cake\ORM\Table;
	use Cake\ORM\TableRegistry;
	use Cake\Datasource\EntityInterface;

	class BeforepageloadComponent extends Component {


		public $components= array('Session');
		public $controller = null;
		public $session = null;

		public function initialize(array $config):void{
			parent::initialize($config);
			$this->Controller = $this->_registry->getController();
			$this->Session = $this->getController()->getRequest()->getSession();
		}

/***************************************************************************************************************************************************************************************************/		

	//This method is used to update logout time in dmi_customer_logs or dmi_user_logs table on every request for current logged in person.
	public function setLogoutTime() {
		
		//Initialize Model in Component
		$DmiUserLogs =TableRegistry::getTableLocator()->get('DmiUserLogs');
		$DmiCustomerLogs =TableRegistry::getTableLocator()->get('DmiCustomerLogs');//initialize model in component

		if ($this->Session->read('username') != null) {

			$username_id = $this->Session->read('username');

			//$proper_email = Validation::email($username_id);// cake email validation

			if (filter_var($username_id, FILTER_VALIDATE_EMAIL)) {

				//Update user logs table
				$find_id_list = $DmiUserLogs->find('list', array('valueField'=>'id','conditions'=>array('email_id IS'=>$username_id)))->toList();
				
				if (!empty($find_id_list)) {

					$find_max_id = $DmiUserLogs->find('all', array('fields'=>'id','conditions'=>array('id'=>max($find_id_list))))->first();	
					$max_id = $find_max_id['id'];
					$DmiUserLogsEntity = $DmiUserLogs->newEntity(array('id'=>$max_id,'time_out'=>date('H:i:s')));
					$DmiUserLogs->save($DmiUserLogsEntity);
				}

			} else {

				//Update customer logs table
				$find_id_list = $DmiCustomerLogs->find('list', array('valueField'=>'id','conditions'=>array('customer_id IS'=>$username_id)))->toList();

				if (!empty($find_id_list)) {

					$find_max_id = $DmiCustomerLogs->find('all', array('fields'=>'id','conditions'=>array('id IS'=>max($find_id_list))))->first();
					$max_id = $find_max_id['id'];
					$DmiCustomerLogsEntity = $DmiCustomerLogs->newEntity(array('id'=>$max_id,'time_out'=>date('H:i:s')));
					$DmiCustomerLogs->save($DmiCustomerLogsEntity);

				}
			}

		}

	}

/***************************************************************************************************************************************************************************************************/		

	//Display Footer content from database
	public function getFooterContent(){
		
		//initialize model in component
		$Dmi_page =TableRegistry::getTableLocator()->get('DmiPages');
		$footer_content = $Dmi_page->find('all',array('fields'=>'content', 'conditions'=>array('id'=>17)))->first()->toArray();
		$this->Controller->set('footer_content',$footer_content['content']);
	}

/***************************************************************************************************************************************************************************************************/		

	//Check the user login time
	public function currentSessionStatus(){


		// compare user current session id, Done by Pravin Bhakare 12-11-2020
		$username = $this->Controller->Session->read('username');
		$countspecialchar = substr_count($username,"/");
		if($countspecialchar == 1){ $userType = 'dp'; }
		if($countspecialchar == 3){ $userType = 'df'; }
		if($countspecialchar == 0){ $userType = 'du'; }
		
		$Dmi_login_status = TableRegistry::getTableLocator()->get('DmiLoginStatuses'); //initialize model in component
		
		$currLoggedin = $Dmi_login_status->find('all',array('conditions'=>array('user_id IS'=>$username,'user_type'=>$userType),'order'=>'id'))->first();
		
		if(!empty($currLoggedin)){
			$browser_session_d = $currLoggedin['sessionid'];
			if($this->Controller->Session->read('browser_session_d') !=''){
				if($browser_session_d != $this->Controller->Session->read('browser_session_d')){
				//temp commented on 10-05-2022, for testing purpose.	
					//$this->Controller->Session->destroy();						
				}
			}
		}

		$login_time = $this->Controller->Session->read('last_login_time_value');

		if (!empty($login_time)) {

			if (time() - $login_time > 1200) {

				$this->Controller->Session->destroy();
				echo "Your session has timed out due to inactivity";?><a href="<?php echo $this->getController()->getRequest()->getAttribute('webroot');?>"> Please Login</a><?php "Again";
				exit;

			} else {

				$current_time = time();
				$this->Controller->Session->write('last_login_time_value',$current_time);
			}

		}

	}

/***************************************************************************************************************************************************************************************************/		

		//Added to get Home page contents
		public function homePageContent(){

			$Dmi_home_page_content =TableRegistry::getTableLocator()->get('DmiHomePageContents');//initialize model in component
			$fetch_home_page_content = $Dmi_home_page_content->find('all',array('order'=>'id'))->toArray();
			$this->Controller->set('home_page_content',$fetch_home_page_content);
		}
		
/***************************************************************************************************************************************************************************************************/		
		
		public function checkValidRequest(){
				
			//commented on 02-04-2021 as esign services was blocked on response
			
			/*	$validHostName = array('agmarkonline.dmi.gov.in','esignservice.cdac.in');
				$hostName = $_SERVER['HTTP_HOST'];
				if(!in_array($hostName,$validHostName)){
					$this->Controller->Session->destroy();
					echo "Something went wrong. ";?><a href="<?php echo $this->webroot;?>"> Please Login</a><?php "Again";					
					exit;
				}else{
					
					//new condition added on 27-03-2021 by Amol to bypass esign on response
					if(isset($_SERVER['HTTP_REFERER']) &&
						($_SERVER['HTTP_REFERER'] == 'https://esignservice.cdac.in/esign2.1' || 
						$_SERVER['HTTP_REFERER'] == 'https://esignservice.cdac.in/esign2.1/OTP')){
							
							//do nothing
					}else{
						// validated referere, Done by Pravin Bhakare 10-02-2021
						if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],$hostName) == null){
							$this->Controller->Session->destroy();
							echo "Something went wrong. ";?><a href="<?php echo $this->webroot;?>"> Please Login</a><?php "Again";					
							exit;
						}
						
					}
					
				}*/
			
		}

	}


?>
