<?php
namespace app\Model\Table;
	use Cake\ORM\Table;
	use App\Model\Model;
	use Cake\ORM\TableRegistry;
	use Cake\Core\Configure;
	
class WorkflowTable extends Table {
	 var $useTable = 'workflow';
	  public $primaryKey='id';
	  
	  
	  public $validate = array(
		'org_sample_code' => array(
			'onlyLetterSp' => array(
				'rule' => array('custom', '/^[0-9]+$/'),
				'message' => 'Enter a proper sample code'
			)
		),
		'src_loc_id' => array(
			'onlyLetterSp' => array(
				'rule' => array('custom', '/^[0-9]+$/'),
				'message' => 'Enter a proper Source Location Code'
			)
		),
		'src_usr_cd' => array(
			'onlyLetterSp' => array(
				'rule' => array('custom', '/^[0-9]+$/'),
				'message' => 'Enter a proper Source User Code'
			)
		),
		'dst_loc_id' => array(
			'onlyLetterSp' => array(
				'rule' => array('custom', '/^[0-9]+$/'),
				'message' => 'Enter a Destination Location Code'
			)
		),
		'dst_usr_cd' => array(
			'onlyLetterSp' => array(
				'rule' => array('custom', '/^[0-9]+$/'),
				'message' => 'Enter a Destination User Code'
			)
		),
		'stage_smpl_cd' => array(
			'onlyLetterSp' => array(
				'rule' => array('custom', '/^[a-zA-z0-9]+$/'),
				'message' => 'Enter a proper sample code'
			)
		),
		 'tran_date'=> array(
			
				'rule' =>  'date',
				'message' => 'Enter a transaction date'	
			
		) 
		
	);
	  
	  
	 }
	 ?>