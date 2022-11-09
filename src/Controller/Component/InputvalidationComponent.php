<?php	
namespace app\Controller\Component;
use Cake\Controller\Controller;
use Cake\Controller\Component;	
use Cake\Controller\ComponentRegistry;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Datasource\EntityInterface;

class InputvalidationComponent extends Component {	
	
	public $components= array('Session');
	public $controller = null;
	public $session = null;

	public function initialize(array $config):void{
		parent::initialize($config);
		$this->Controller = $this->_registry->getController();
		$this->Session = $this->getController()->getRequest()->getSession();
	}

/***************************************************************************************************************************************************************************************************/		

	// validate category post data on server side
	public function categoryPostValidations($postData) {

		$validate_status = '';

		if (!empty($postData['category_name'])) {

			$res = preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $postData['category_name']);
			
				if ($res>'0') {

					$validate_status = 'Invalid Category Name';
				}
		}

		if (!empty($postData['l_category_name'])) {

			$res = preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $postData['l_category_name']);
			
				if ($res>'0') {

					$validate_status = 'Invalid Language Category Name';
				}
		}

		if (!empty($postData['min_quantity'])) {

			if (!is_numeric($postData['min_quantity'])) {

				$validate_status = 'Invalid Minimum Quantity';
			}

			$res = preg_match('/[.]/', $postData['min_quantity']);
			
			if ($res>'0') {

				$validate_status = 'Minium quantity should be numeric';
			}
		}

		return $validate_status;
	
	}

/***************************************************************************************************************************************************************************************************/		

	// validate commodity post data on server side
	public function commodityPostValidations($postData) {

		$validate_status = '';

		if (empty($postData['category_code'])) {
			
			$validate_status = 'Please select category';
		}

		if (!empty($postData['commodity_name'])) {

			$res = preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $postData['commodity_name']);
			
				if ($res>'0') {

					$validate_status = 'Invalid Commodity Name';
				}
				//added for (<=4) replace (< 4) while testing the validation not working  min. 4 characters
				//06-10-2022 by shreeya
				if (strlen($postData['commodity_name']) < 4) {
					
					$validate_status = 'Commodity name should be minimum 4 characters';
				}
				
				if (strlen($postData['commodity_name']) >= 50) {

					$validate_status = 'Commodity name should not greater than 50 characters';
				}
		}

		if (!empty($postData['l_commodity_name'])) {

			$res = preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $postData['l_commodity_name']);
			
				if ($res>'0') {

					$validate_status = 'Invalid Language Commodity Name';
				}

				if (strlen($postData['l_commodity_name']) >= 50) {

					$validate_status = 'Language commodity name should not greater than 50 characters';
				}
		}

		return $validate_status;
	
	}

/***************************************************************************************************************************************************************************************************/		

	// validate phy appear post data on server side
	public function phyAppearPostValidations($postData) {

		$validate_status = '';
		$textbox_1 = $postData['textbox_1'];
		$textbox_2 = $postData['textbox_2'];
		$table_nm = $postData['table_name'];

		if (empty($postData[$textbox_1])) {

			$validate_status = "Please enter the " . $postData['label_1'];
		}

		if (empty($postData[$textbox_2])) {

			$validate_status = "Please enter the " . $postData['label_2'];
		}

		if (!empty($postData[$textbox_1])) {

			if ($table_nm == 'm_fin_year') { // allow number and '-' in financial year module

				$res = preg_match('/[abcdefghijklmnopqrstuvwxyz\'^£$%&*()}{@#~?><>,|=_+¬]/', $postData[$textbox_1]);
				
					if ($res>'0') {

						$validate_status = "Invalid " . $postData['label_1'];
					}

			} else {

				$res = preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $postData[$textbox_1]);
				
					if ($res>'0') {

						$validate_status = "Invalid " . $postData['label_1'];
					}

			}

			if (strlen($postData[$textbox_1]) >= 70) {

				$validate_status = $postData['label_1'] . " should not greater than 70 characters";
			}
		
		}


		if (!empty($postData[$textbox_2])) {

			if ($table_nm == 'm_fin_year') { 
				
				// allow numbers and '-' in financial year module

				$res = preg_match('/[abcdefghijklmnopqrstuvwxyz\'^£$%&*()}{@#~?><>,|=_+¬]/', $postData[$textbox_2]);
				
					if($res>'0'){
					
						$validate_status = "Invalid " . $postData['label_2'];
						
					}

			} else {

				$res = preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $postData[$textbox_2]);
				
					if ($res>'0') {
					
						$validate_status = "Invalid " . $postData['label_2'];
					}

			}

			if (strlen($postData[$textbox_2]) >= 70) {

				$validate_status = $postData['label_2'] . " should not greater than 70 characters";
			}
		
		}

		return $validate_status;
	
	}

	
/***************************************************************************************************************************************************************************************************/		

	// validate homo value post data on server side
	public function homoValuePostValidations($postData) {

		$validate_status = '';

		if ($postData['val_type']=='single') {

			if (empty($postData['m_sample_obs_type_value'])) {

				$validate_status = 'Please enter Homogenization value';
			}

			if (strlen($postData['m_sample_obs_type_value']) >= 70) {

				$validate_status = 'Homogenization value should not greater than 70 characters';
			
			}
		}

		return $validate_status;
	
	}



}
?>