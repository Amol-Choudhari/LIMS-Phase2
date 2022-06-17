<?php

namespace app\Model\Table;

use Cake\ORM\Table;
use App\Model\Model;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;

class SampleInwardTable extends Table
{

	var $name = "SampleInward";
	public $useTable = 'sample_inward';
	public $primaryKey = 'inward_id';

	public $validate = array(

		/* 'loc_id' => array(
						'onlyLetterSp' => array('rule' => array('custom', '/^[0-9]+$/'),'message' => 'Enter a proper location id')),*/

		'letr_ref_no' => array(
			'notBlank' => array('rule' => array('notBlank'), 'message' => 'Letter Reference Number Can not be blank'),
			'minLength' => array('rule' => array('minLength', 1), 'message' => 'Minimum length for Reference Number should be of 1 characters'),
			'maxLength' => array('rule' => array('maxLength', 50), 'message' => 'Maximum length for Reference Number should be of 50 characters'),
			'onlyLetterSp' => array('rule' => array('custom', '/^[0-9 a-z A-Z \/\-]+$/'), 'message' => 'Letter Reference Number should be aplanumeric')
		),

		'letr_date' => array(
			'notBlank' => array('rule' => array('notBlank'), 'message' => 'Letter Date Can not be blank')
		),
		'received_date' => array(
			'notBlank' => array('rule' => array('notBlank'), 'message' => 'Received Date Can not be blank')
		),

		'fin_year' => array(
			'onlyLetterSp' => array('rule' => array('custom', '/^[0-9 -]+$/'), 'message' => 'Enter a Financial Year')
		),

		/* 'sample_code'=>array(
						'onlyLetterSp' => array('rule' => array('custom', '/^[0-9]+$/'),'message' => 'Enter a proper sample code')), 
		'inward_id'=>array(
						'onlyLetterSp' => array('rule' => array('custom', '/^[0-9]+$/'),'message' => 'Enter a proper inward_id')),  */

		'container_code' => array(
			'notBlank' => array('rule' => array('notBlank'), 'message' => 'Container Code can not be blank ;Please enter Container Code'),
			'onlyLetterSp' => array('rule' => array('custom', '/^[0-9]+$/'), 'message' => 'Container Code should be numeric')
		),

		'entry_flag' => array(
			'notBlank' => array('rule' => array('notBlank'), 'message' => 'Physical Appearance can not be blank ;Please enter Physical Appearance'),
			'onlyLetterSp' => array('rule' => array('custom', '/^[0-9]+$/'), 'message' => 'Physical Appearance should be numeric')
		),

		'par_condition_code' => array(
			'notBlank' => array('rule' => array('notBlank'), 'message' => 'Package Condition can not be blank ;Please enter Package Condition'),
			'onlyLetterSp' => array('rule' => array('custom', '/^[0-9]+$/'), 'message' => 'Package Condition should be numeric')
		),

		'sam_condition_code' => array(
			'notBlank' => array('rule' => array('notBlank'), 'message' => 'Sample Condition can not be blank ;Please enter sample condition'),
			'onlyLetterSp' => array('rule' => array('custom', '/^[0-9]+$/'), 'message' => 'Sample condition should be numeric')
		),

		'sample_type_code' => array(
			'notBlank' => array('rule' => array('notBlank'), 'message' => 'Sample type can not be blank ;Please enter sample type'),
			'onlyLetterSp' => array('rule' => array('custom', '/^[0-9]+$/'), 'message' => 'Sample Type should be numeric')
		),

		'sample_total_qnt' => array(
			'notBlank' => array('rule' => array('notBlank'), 'message' => 'Total Quantity can not be blank ;Please enter quantity'),
			'numeric' => array('rule' => array('custom', '/^[0-9]+$/'), 'message' => 'Quantity should be numeric')
		),

		'parcel_size' => array(
			'notBlank' => array('rule' => array('notBlank'), 'message' => 'Size can not be blank ;Please enter Size'),
			'onlyLetterSp' => array('rule' => array('custom', '/^[0-9]+$/'), 'message' => 'Unit Description should be only alphabets with space')
		),

		'category_code' => array(
			'notBlank' => array('rule' => array('notBlank'), 'message' => 'Commodity Category can not be blank ;Please enter commodity category'),
			'onlyLetterSp' => array('rule' => array('custom', '/^[0-9]+$/'), 'message' => 'Commodity Category should be only alphabets with space')
		),

		'commodity_code' => array(
			'notBlank' => array('rule' => array('notBlank'), 'message' => 'Commodity can not be blank ;Please enter commodity'),
			'onlyLetterSp' => array('rule' => array('custom', '/^[0-9]+$/'), 'message' => 'Commodity should be only alphabets with space')
		),

		'ref_src_code' => array(
			'notBlank' => array('rule' => array('notBlank'), 'message' => 'Reference source code can not be blank ;Please enter Unit Description'),
			'minLength' => array('rule' => array('minLength', 1), 'message' => 'Minimum length for Reference source code should be of 1 characters'),
			'maxLength' => array('rule' => array('maxLength', 11), 'message' => 'Maximum length for Reference source code should be of 11 characters'),
			'onlyLetterSp' => array('rule' => array('custom', '/^[0-9a-zA-Z]+$/'), 'message' => 'Reference source code is numeric and minimum 6 numbers')
		),

		'rej_reason' => array(
			'minLength' => array('rule' => array('minLength', 1), 'message' => 'Minimum length for Reject Reason should be of 1 characters', 'allowEmpty' => true),
			'maxLength' => array('rule' => array('maxLength', 100), 'message' => 'Maximum length for Reject Reason should be of 100 characters'),
			'onlyLetterSp' => array('rule' => array('custom', '/^[a-z A-Z]+$/'), 'message' => 'Reason should be only alphabets with space')
		),

		'expiry_month' => array(
			'notBlank' => array('rule' => array('notBlank'), 'message' => 'Expiry month can not be blank ;Please enter expiry month'),
			'onlyLetterSp' => array('rule' => array('custom', '/^[0-9]+$/'), 'message' => 'Expiry month should be numeric')
		),

		'expiry_year' => array(
			'notBlank' => array('rule' => array('notBlank'), 'message' => 'Expiry year can not be blank ;Please enter expiry year'),
			'onlyLetterSp' => array('rule' => array('custom', '/^[0-9]+$/'), 'message' => 'Expiry year should be numeric')
		),

		'remark' => array(
			'notBlank' => array('rule' => array('notBlank'), 'message' => 'Remark can not be blank ;Please enter expiry year'),

			'onlyLetterSp' => array('rule' => array('custom', '/^[a-zA-Z0-9]+$/'), 'message' => 'remark should be alphanumeric')
		)



	);


	public function inward_sample($fin_year, $lab_code, $cat_code, $com_code, $status, $sample_code)
	{

		/***********************************************************************************************************************************************/
		/*$this->query("INSERT INTO sample_inward(lab_code, fin_year, category_code, commodity_code, 												  //
		//							sample_code, status_flag, display, user_code, login_timestamp)													 //
		//							VALUES ($lab_code,'$fin_year', $cat_code ,$com_code ,'$sample_code' ,'$status','Y',4,current_timestamp)");*/	//
		//																																		   //
		//****************************************************************************************************************************************//			

		return true;
	}

	//*********************************************************************************************************************************************************************************************************************************************** */ 
	/*  public function edit_sample($sample_inward){
			
			$abc=$this->query("select m_par_condition.par_condition_desc,m_par_condition.par_condition_code, m_sample_condition.sam_condition_desc,
						m_sample_condition.sam_condition_code, m_sample_type.sample_type_desc,m_sample_type.sample_type_code,
						sample_inward.org_sample_code,sample_inward.users,sample_inward.ref_src_code,m_commodity_category.category_name
						,m_commodity_category.category_code, m_commodity.commodity_name,m_commodity.commodity_code,sample_inward.fin_year,sample_inward.loc_id,
						sample_inward.inward_id,sample_inward.stage_sample_code,sample_inward.rej_code,sample_inward.rej_reason,
						sample_inward.letr_ref_no,sample_inward.letr_date,sample_inward.received_date,sample_inward.designation,m_container_type.container_desc,m_container_type.container_code,sample_inward.parcel_size, sample_inward.sample_total_qnt
						,sample_inward.stage_sample_code, case WHEN sample_inward.expiry_month='1' THEN 'January'  WHEN sample_inward.expiry_month='2' THEN 'February' 
						WHEN sample_inward.expiry_month='3' THEN 'March' WHEN sample_inward.expiry_month='4' THEN 'April' WHEN sample_inward.expiry_month='5' THEN 'May' 
						WHEN sample_inward.expiry_month='6' THEN 'June' WHEN sample_inward.expiry_month='7' THEN 'July' WHEN sample_inward.expiry_month='8' THEN 'Augest' WHEN sample_inward.expiry_month='9' 
						THEN 'September' WHEN sample_inward.expiry_month='10' THEN 'Octomber' 
						WHEN sample_inward.expiry_month='11' THEN 'November'  WHEN sample_inward.expiry_month='12' THEN 'December' end  expiry_month,sample_inward.expiry_year,sample_inward.acc_rej_flg,
						sample_inward.entry_flag, sample_inward.dispatch_date,sample_inward.user_code,sample_inward.display,sample_inward.login_timestamp,
						sample_inward.name,sample_inward.address,dmi_ro_offices.ro_office from sample_inward 
						INNER JOIN m_sample_condition ON (m_sample_condition.sam_condition_code=sample_inward.sam_condition_code) 
						INNER JOIN m_par_condition On (m_par_condition.par_condition_code=sample_inward.par_condition_code)
						INNER JOIN m_sample_type On (m_sample_type.sample_type_code=sample_inward.sample_type_code) 
						INNER JOIN dmi_ro_offices on (sample_inward.loc_id=dmi_ro_offices.id) 
						INNER JOIN m_container_type on sample_inward.container_code=m_container_type.container_code 
						INNER JOIN m_commodity_category On (m_commodity_category.category_code=sample_inward.category_code) 
						INNER JOIN m_commodity On (m_commodity.commodity_code=sample_inward.commodity_code) and sample_inward.inward_id='$sample_inward'");

									return $abc;
			
		} */

	/************************************************************************************************************************************************************************************************************************************************************* */

	public function edit_sample($sample_inward)
	{

		// Updated the select query for getting the propery value of Expiry Month. Done By Pravin Bhakare, on 12-06-2019 

		$abc = $this->query("SELECT m_par_condition.par_condition_desc, m_par_condition.par_condition_code, m_sample_condition.sam_condition_desc,
								  m_sample_condition.sam_condition_code, m_sample_type.sample_type_desc, m_sample_type.sample_type_code,
								  sample_inward.org_sample_code, sample_inward.users, sample_inward.ref_src_code,
								  m_commodity_category.category_name, m_commodity_category.category_code, m_commodity.commodity_name,
								  m_commodity.commodity_code, sample_inward.fin_year, sample_inward.loc_id, sample_inward.inward_id,
								  sample_inward.stage_sample_code, sample_inward.rej_code, sample_inward.rej_reason,
								  sample_inward.letr_ref_no, sample_inward.letr_date, sample_inward.received_date,
								  sample_inward.designation, m_container_type.container_desc, m_container_type.container_code,
								  sample_inward.parcel_size, sample_inward.sample_total_qnt, sample_inward.stage_sample_code, 
								  sample_inward.expiry_month, sample_inward.expiry_year, sample_inward.acc_rej_flg,
								  sample_inward.entry_flag, sample_inward.dispatch_date, sample_inward.user_code,
								  sample_inward.display, sample_inward.login_timestamp, sample_inward.name,
								  sample_inward.address, dmi_ro_offices.ro_office, m_unit_weight.unit_weight 
							FROM sample_inward 
							INNER JOIN m_sample_condition ON (m_sample_condition.sam_condition_code=sample_inward.sam_condition_code) 
							INNER JOIN m_par_condition ON (m_par_condition.par_condition_code=sample_inward.par_condition_code)
							INNER JOIN m_sample_type ON (m_sample_type.sample_type_code=sample_inward.sample_type_code) 
							INNER JOIN dmi_ro_offices ON (sample_inward.loc_id=dmi_ro_offices.id) 
							INNER JOIN m_unit_weight ON (m_unit_weight.unit_id=sample_inward.parcel_size) 														 
							INNER JOIN m_container_type ON sample_inward.container_code=m_container_type.container_code 
							INNER JOIN m_commodity_category ON (m_commodity_category.category_code=sample_inward.category_code) 
							INNER JOIN m_commodity ON (m_commodity.commodity_code=sample_inward.commodity_code) 
							AND sample_inward.inward_id='$sample_inward'");

		return $abc;
	}

	/*************************************************************************************************************************************************************************************************************************************************************
		/* public function edit_sample_new($stage_sample_code){
			
					$abc=$this->query("sample_inward.dispatch_date,sample_inward.user_code,sample_inward.display,sample_inward.login_timestamp,
			sample_inward.name,sample_inward.address,dmi_ro_offices.ro_office,
			smpl_drwl_dt,drawal_loc,shop_name,shop_address,mnfctr_nm,mnfctr_addr,pckr_nm,pckr_addr,sample_inward_details.remark,replica_serial_no,no_of_packets,pack_size
			,lot_no,tbl ,sample_inward_details.grade,collected_by
			from sample_inward 
			INNER JOIN m_sample_condition ON (m_sample_condition.sam_condition_code=sample_inward.sam_condition_code) 
			INNER JOIN m_par_condition On (m_par_condition.par_condition_code=sample_inward.par_condition_code)
			INNER JOIN m_sample_type On (m_sample_type.sample_type_code=sample_inward.sample_type_code)
			INNER JOIN dmi_ro_offices on (sample_inward.loc_id=dmi_ro_offices.id) 
			INNER JOIN m_container_type on sample_inward.container_code=m_container_type.container_code
			INNER JOIN m_commodity_category On (m_commodity_category.category_code=sample_inward.category_code)

			INNER JOIN m_commodity On (m_commodity.commodity_code=sample_inward.commodity_code) and sample_inward.stage_sample_code='$stage_sample_code'");

			return $abc;
			
		} */


	/*************************************************************************************************************************************************************************************************************************************************************/


	public function edit_sample_new($stage_sample_code)
	{

		$abc = $this->query("SELECT m_par_condition.par_condition_desc, m_par_condition.par_condition_code, m_sample_condition.sam_condition_desc,
								  m_sample_condition.sam_condition_code, m_sample_type.sample_type_desc, m_sample_type.sample_type_code,
								  sample_inward.org_sample_code, sample_inward.users, sample_inward.ref_src_code,
								  m_commodity_category.category_name, m_commodity_category.category_code, m_commodity.commodity_name,
								  m_commodity.commodity_code, sample_inward.fin_year, sample_inward.loc_id,
								  sample_inward.inward_id, sample_inward.stage_sample_code, sample_inward.rej_code,
								  sample_inward.rej_reason, sample_inward.letr_ref_no, sample_inward.letr_date,
								  sample_inward.received_date, sample_inward.designation, m_container_type.container_desc,
								  m_container_type.container_code, sample_inward.parcel_size, sample_inward.sample_total_qnt, sample_inward.stage_sample_code,
								  CASE  WHEN sample_inward.expiry_month='1'  THEN 'January' 
										WHEN sample_inward.expiry_month='2'  THEN 'February' 
										WHEN sample_inward.expiry_month='3'  THEN 'March' 
										WHEN sample_inward.expiry_month='4'  THEN 'April' 
										WHEN sample_inward.expiry_month='5'  THEN 'May' 
										WHEN sample_inward.expiry_month='6'  THEN 'June' 
										WHEN sample_inward.expiry_month='7'  THEN 'July' 
										WHEN sample_inward.expiry_month='8'  THEN 'Augest' 
										WHEN sample_inward.expiry_month='9'  THEN 'September' 
										WHEN sample_inward.expiry_month='10' THEN 'Octomber' 
										WHEN sample_inward.expiry_month='11' THEN 'November'  
										WHEN sample_inward.expiry_month='12' THEN 'December' 
								  END expiry_month,
									sample_inward.expiry_year, sample_inward.acc_rej_flg, sample_inward.entry_flag,
									sample_inward.dispatch_date, sample_inward.user_code, sample_inward.display,
									sample_inward.login_timestamp, sample_inward.name, sample_inward.address, 
									dmi_ro_offices.ro_office, m_unit_weight.unit_weight
							FROM sample_inward 
							INNER JOIN m_sample_condition ON (m_sample_condition.sam_condition_code=sample_inward.sam_condition_code) 
							INNER JOIN m_par_condition ON (m_par_condition.par_condition_code=sample_inward.par_condition_code)
							INNER JOIN m_sample_type ON (m_sample_type.sample_type_code=sample_inward.sample_type_code)
							INNER JOIN dmi_ro_offices ON (sample_inward.loc_id=dmi_ro_offices.id) 
							INNER JOIN m_unit_weight ON (m_unit_weight.unit_id=sample_inward.parcel_size)																			 
							INNER JOIN m_container_type ON sample_inward.container_code=m_container_type.container_code
							INNER JOIN m_commodity_category ON (m_commodity_category.category_code=sample_inward.category_code)
							INNER JOIN m_commodity ON (m_commodity.commodity_code=sample_inward.commodity_code) 
							AND sample_inward.org_sample_code='$stage_sample_code'");

		return $abc;
	}

	/*************************************************************************************************************************************************************************************************************************************************************/


	public function accept_sample_info($stage_sample_code)
	{

		$abc = $this->query("SELECT DISTINCT m_par_condition.par_condition_desc, m_par_condition.par_condition_code, m_sample_condition.sam_condition_desc,
											   m_sample_condition.sam_condition_code, m_sample_type.sample_type_desc, m_sample_type.sample_type_code,
											   sample_inward.org_sample_code, sample_inward.users, sample_inward.ref_src_code,
											   m_commodity_category.category_name, m_commodity_category.category_code, m_commodity.commodity_name,
											   m_commodity.commodity_code, sample_inward.fin_year, sample_inward.loc_id,
											   sample_inward.inward_id, sample_inward.stage_sample_code, sample_inward.rej_code,
											   sample_inward.rej_reason, sample_inward.letr_ref_no, sample_inward.letr_date,
											   sample_inward.received_date, sample_inward.designation, m_container_type.container_desc,
											   m_container_type.container_code, sample_inward.parcel_size, sample_inward.sample_total_qnt,
											   sample_inward.stage_sample_code, sample_inward.expiry_month, sample_inward.expiry_year,
											   sample_inward.acc_rej_flg, sample_inward.entry_flag, sample_inward.dispatch_date, 
											   sample_inward.user_code, sample_inward.display, sample_inward.login_timestamp,
											   sample_inward.name, sample_inward.address, dmi_ro_offices.ro_office,
									           w.stage_smpl_cd, sd.smpl_drwl_dt, sd.drawal_loc, sd.shop_name, sd.shop_address, sd.mnfctr_nm,
											   sd.mnfctr_addr, sd.pckr_nm, sd.pckr_addr, sd.remark, sd.replica_serial_no, 
											   sd.no_of_packets, sd.pack_size, sd.lot_no, sd.tbl, sd.grade, sd.collected_by
								FROM sample_inward 
								INNER JOIN m_sample_condition ON (m_sample_condition.sam_condition_code=sample_inward.sam_condition_code) 
								INNER JOIN m_par_condition ON (m_par_condition.par_condition_code=sample_inward.par_condition_code)
								INNER JOIN m_sample_type ON (m_sample_type.sample_type_code=sample_inward.sample_type_code)
								INNER JOIN dmi_ro_offices ON (sample_inward.loc_id=dmi_ro_offices.id) 
								INNER JOIN m_container_type ON sample_inward.container_code=m_container_type.container_code
								INNER JOIN m_commodity_category ON (m_commodity_category.category_code=sample_inward.category_code)
								INNER JOIN workflow AS w ON w.org_sample_code=sample_inward.org_sample_code
								INNER JOIN sample_inward_details AS sd ON sd.org_sample_code=sample_inward.org_sample_code
								INNER JOIN m_commodity ON (m_commodity.commodity_code=sample_inward.commodity_code) 
								WHERE stage_smpl_cd='$stage_sample_code'");

		return $abc;
	}

	/*************************************************************************************************************************************************************************************************************************************************************/


	public function edit_sample_ro($stage_sample_code)
	{

		$abc = $this->query("SELECT  m_par_condition.par_condition_desc, m_par_condition.par_condition_code, m_sample_condition.sam_condition_desc,
								   m_sample_condition.sam_condition_code, m_sample_type.sample_type_desc, m_sample_type.sample_type_code,
								   sample_inward.org_sample_code, sample_inward.users, sample_inward.ref_src_code,
								   m_commodity_category.category_name, m_commodity_category.category_code, m_commodity.commodity_name,
								   m_commodity.commodity_code, sample_inward.fin_year, sample_inward.loc_id,
								   sample_inward.inward_id, sample_inward.stage_sample_code, sample_inward.rej_code,
								   sample_inward.rej_reason, sample_inward.letr_ref_no, sample_inward.letr_date,
								   sample_inward.received_date, sample_inward.designation, m_container_type.container_desc,
								   m_container_type.container_code, sample_inward.parcel_size,
								   sample_inward.sample_total_qnt, sample_inward.stage_sample_code,
									CASE WHEN sample_inward.expiry_month='1'  THEN 'January'  
										 WHEN sample_inward.expiry_month='2'  THEN 'February' 
										 WHEN sample_inward.expiry_month='3'  THEN 'March' 
										 WHEN sample_inward.expiry_month='4'  THEN 'April' 
										 WHEN sample_inward.expiry_month='5'  THEN 'May' 
										 WHEN sample_inward.expiry_month='6'  THEN 'June' 
										 WHEN sample_inward.expiry_month='7'  THEN 'July' 
										 WHEN sample_inward.expiry_month='8'  THEN 'Augest' 
										 WHEN sample_inward.expiry_month='9'  THEN 'September' 
										 WHEN sample_inward.expiry_month='10' THEN 'Octomber' 
										 WHEN sample_inward.expiry_month='11' THEN 'November'  
										 WHEN sample_inward.expiry_month='12' THEN 'December' 
									END   expiry_month,
							      sample_inward.expiry_year, sample_inward.acc_rej_flg, sample_inward.entry_flag, 
								  sample_inward.dispatch_date, sample_inward.user_code, sample_inward.display, 
								  sample_inward.login_timestamp, sample_inward.name, sample_inward.address, 
								  dmi_ro_offices.ro_office, 
								  sample_inward_details.smpl_drwl_dt, sample_inward_details.drawal_loc,
								  sample_inward_details.shop_name, sample_inward_details.shop_address,
								  sample_inward_details.mnfctr_nm, sample_inward_details.mnfctr_addr,
								  sample_inward_details.pckr_nm, sample_inward_details.pckr_addr,
								  sample_inward_details.remark, sample_inward_details.replica_serial_no,
								  sample_inward_details.no_of_packets,sample_inward_details.pack_size,
								  sample_inward_details.lot_no, sample_inward_details.tbl,
								  sample_inward_details.grade, m_unit_weight.unit_weight,
								  sample_inward_details.collected_by
							FROM sample_inward 
							INNER JOIN m_sample_condition ON (m_sample_condition.sam_condition_code=sample_inward.sam_condition_code) 
							INNER JOIN m_par_condition ON (m_par_condition.par_condition_code=sample_inward.par_condition_code)
							INNER JOIN m_sample_type ON (m_sample_type.sample_type_code=sample_inward.sample_type_code)
							INNER JOIN dmi_ro_offices ON (sample_inward.loc_id=dmi_ro_offices.id) 
							INNER JOIN m_container_type ON sample_inward.container_code=m_container_type.container_code
							INNER JOIN m_commodity_category ON (m_commodity_category.category_code=sample_inward.category_code)
							INNER JOIN sample_inward_details ON (sample_inward_details.org_sample_code=sample_inward.org_sample_code)
							INNER JOIN m_commodity ON (m_commodity.commodity_code=sample_inward.commodity_code) 
							AND sample_inward.stage_sample_code='$stage_sample_code'");

		return $abc;
	}

	/*******************************************************************************************************************************************************************************************************************************/
	/***
	 * Made by Shweta Apale 22-10-2021
	 * To get Category by From date & To date
	 *  */

	public function getCategoryByDate($to_date,$from_date)
	{
		$conn = ConnectionManager::get('default');
		$q = $conn->execute("SELECT si.category_code,mcc.category_name
		FROM sample_inward si
		INNER JOIN m_commodity_category mcc ON si.category_code = mcc.category_code
		WHERE DATE(si.received_date) BETWEEN '$from_date' AND '$to_date'
		GROUP BY si.category_code,mcc.category_name");
		$records = $q->fetchAll('assoc');

		$data = array();
		$i = 0;
		foreach ($records as $result) {
			$code = $result['category_code'];
			$data[$code] = $result['category_name'];
			$i++;
		}

		return $data;
	}

	/*******************************************************************************************************************************************************************************************************************************/
	/***
	 * Made by Shweta Apale 02-11-2021
	 * To get Sample Code by Commodity, From date & To date
	 *  */

	public function getSampleCodeByCommodityDate($from_date,$to_date,$commodity,$ral_lab_no)
	{
		$conn = ConnectionManager::get('default');
		$q = $conn->execute("SELECT DISTINCT(wf.org_sample_code), si.org_sample_Code,si.commodity_code,si.received_date,wf.tran_date
		FROM sample_inward si
		INNER JOIN workflow wf ON si.org_sample_code = wf.org_sample_code AND (wf.dst_loc_id = '$ral_lab_no' OR wf.src_loc_id = '$ral_lab_no')  AND wf.stage_smpl_flag = 'FG'
		WHERE si.commodity_code = '$commodity' AND wf.tran_date BETWEEN '$from_date' AND '$to_date'");
		//pr($q);
		$records = $q->fetchAll('assoc');

		$data = array();
		$i = 0;
		foreach ($records as $result) {
			$code = $result['org_sample_code'];
			$data[$code] = $result['org_sample_code'];
			$i++;
		}
		return $data;
	}

}
