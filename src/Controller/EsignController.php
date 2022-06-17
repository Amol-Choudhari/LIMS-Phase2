<?php

namespace App\Controller;
use Cake\Event\Event;
use Cake\Network\Session\DatabaseSession;
use App\Network\Email\Email;
use App\Network\Request\Request;
use App\Network\Response\Response;
use Cake\ORM\TableRegistry;
use App\Network\Http\HttpSocket;
use Cake\Utility\Xml;
use FR3D;
use Applicationformspdfs;//importing another controller class here
/**
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class EsignController extends AppController {
	
	var $name = 'Esign';


	public function beforeFilter($event) {
		parent::beforeFilter($event);	

					$this->viewBuilder()->setHelpers(['Form','Html']);
					$this->loadComponent('Customfunctions');
	}

	
	//This is for new applications esign call
	public function requestEsign(){ 

		$this->autoRender = false;
		
		$grading_sample_code = $this->Session->read('grading_sample_code');
		$current_level = $this->Session->read('current_level');
		$pdf_file_name = $this->Session->read('pdf_file_name');	

		//if response from ESP for esign request
		if($this->request->is('post')){
			
			//to get FORM base method response POST and convert into associative array
			////updated on 31-05-2021 for Form Based Esign method by Amol
		/*	$eSignResponse = simplexml_load_string($this->request->getdata('eSignResponse'));
			$getRespInJson = json_encode($eSignResponse);
			$getRespAssoArray = json_decode($getRespInJson,TRUE);
																			  
			//calling to set response signature on existing pdf.
			$esign_status = $this->signTheDoc($getRespAssoArray,$pdf_file_name);
		*/
			$esign_status = 1;
			if($esign_status == 1){

				$this->Session->delete('pdf_file_name');//added to clear pdf file name from session, after esign					
					
				//calling final submit process now after signature appended in pdf.
				
				$this->redirect('https://10.153.72.52/LIMS/FinalGrading/saveFinalGrading');
	
			//added this else part  to show esign failed message	
			}else{
				
				$this->redirect('https://10.153.72.52/LIMS/esign/esign_issue');//updated on 31-05-2021 for Form Based Esign method by Amol
			}
			
		}
		
	}

	
	//this function is created to create XML with signature to request esign OTP, called through ajax
	//if ajax call of this function properly responded with OTP on mobile, 
	//then it will redirect to CDAC server with CORS(Cross-Origin-Resourse-Sharing) functionality to validate OTP.
	//if OTP is successfull, then CDAC will redirected to our provided URL with proper session by CORS.
	public function createEsignXmlAjax(){
		
		$this->autoRender = false;

		$current_level = 'RAL/CAL OIC';
		$this->Session->write('current_level',$current_level);
		$pdf_file_name = $this->Session->read('pdf_file_name');
		$grading_sample_code = $this->Session->read('grading_sample_code');


		//removed tcpdf code from here to create pdf using imagik images, on 24-01-2020
		//Now created common TCPDF function 'call_tcpdf' in Appcontroller and replaced with Mpdf code
		//Now implementing signature content at the time of first pdf creation, fetch that pdf here to create hash for Xml.
		
		//get generated pdf to create hash
		$doc_path = $_SERVER["DOCUMENT_ROOT"].'/writereaddata/LIMS/reports/'.$pdf_file_name;		
		
		$get_date = date('Y-m-d');
		$get_time = date('H:i:s'.'.000');
		$time_stamp = $get_date.'T'.$get_time; //formatting timestamp as required
		$txn_id = rand().time();
		$asp_id = 'DMIC-001';
		$document_hashed = hash_file('sha256',$doc_path);//create pdf hash		
		$response_url = 'https://10.153.72.52/LIMS/esign/request_esign';

		$doc_info = 'Sample Grade Report';	

		require_once(ROOT . DS . 'vendor' . DS . 'xmldsign' . DS . 'src' . DS . 'Adapter' . DS . 'XmlseclibsAdapter.php');

		// "Create" the document.
		$xml = new \DOMDocument( "1.0", "ISO-8859-15" );

		// Create some elements.
		$xml_esign = $xml->createElement( "Esign" );
		$xml_docs = $xml->createElement( "Docs" );
		$xml_docs_input = $xml->createElement( "InputHash", $document_hashed );
		
		// Set the attributes for Esign tag
		$xml_esign->setAttribute( "ver", "2.1" );
		$xml_esign->setAttribute( "sc", "Y" );
		$xml_esign->setAttribute( "ts", $time_stamp );
		$xml_esign->setAttribute( "txn", $txn_id );
		//$xml_esign->setAttribute( "ekycMode", "U" );
		$xml_esign->setAttribute( "ekycIdType", "A" );
		$xml_esign->setAttribute( "ekycId", "" );
		$xml_esign->setAttribute( "aspId", $asp_id );
		$xml_esign->setAttribute( "AuthMode", "1" );				
		$xml_esign->setAttribute( "responseSigType", "pkcs7" );
		//$xml_esign->setAttribute( "preVerified", "n" );
		//$xml_esign->setAttribute( "organizationFlag", "n" );
		$xml_esign->setAttribute( "responseUrl", $response_url ); 
		
		// Set the attributes for InputHash tag
		$xml_docs_input->setAttribute( "id", "1" );
		$xml_docs_input->setAttribute( "hashAlgorithm", "SHA256" );
		$xml_docs_input->setAttribute( "docInfo", $doc_info );
		
		
		// Append the whole bunch inside
		$xml_docs->appendChild( $xml_docs_input );
		$xml_esign->appendChild( $xml_docs );

		$xml->appendChild( $xml_esign );

		$xmlTool = new FR3D\XmlDSig\Adapter\XmlseclibsAdapter();
		$xmlTool->setPrivateKey(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/agmarkonline.key'));
		$xmlTool->addTransform(FR3D\XmlDSig\Adapter\XmlseclibsAdapter::ENVELOPED);

		$xmlTool->sign($xml);	
		$xml_string = $xml->saveXML(); 
		
		//save details in logs table
		$this->saveRequestLog('',$grading_sample_code,$pdf_file_name,$current_level,$time_stamp,$txn_id,$asp_id,
											$document_hashed,$response_url,null,null);
		
		//updated on 31-05-2021 for Form Based Esign method
		$result_arr = array('xml'=>$xml_string,'txnid'=>$txn_id);
		
		echo json_encode($result_arr);
		exit;
	}
	
	
	
//This function is created to append response signature on existing pdf doc.
	public function signTheDoc($resp_array,$pdf_file_name){

		$resp_status = $resp_array['@attributes']['status'];//updated on 31-05-2021 for Form Based Esign method

		if($resp_status == 1){
			//Set signature on pdf process Starts here....				
			//file path to get existing pdf, signed it and write on the same place
			$pdf_path = $_SERVER["DOCUMENT_ROOT"].'/writereaddata/LIMS/reports/'.$pdf_file_name;
			$cer_value = $resp_array['UserX509Certificate'];//updated on 31-05-2021 for Form Based Esign method
			$pkcs7_value = $resp_array['Signatures']['DocSignature'];//updated on 31-05-2021 for Form Based Esign method
			
			//to verify response called custom function
			$verify_cdac_response = $this->verifyCdacResponse($resp_array);
			if($verify_cdac_response == true) {

				require_once(ROOT . DS .'vendor' . DS . 'tcpdf' . DS . 'tcpdf.php');
				$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
				
				$pdf->my_output($pdf_path,'F',$pdf_path,$cer_value,$pkcs7_value,true);
				
			}else{
				
				$resp_status = false;
			}
				
		}

		//update this response array to DB for log
		$last_insert_id = $this->Session->read('log_last_insert_id');									
		$this->updateResponseLog($last_insert_id,null,$resp_array);
		
		return $resp_status;
	}
	
	
	
//this function is created to save request log in db
	public function saveRequestLog($id,$sample_code,$pdf_file_name,$current_level,$ts,$txn_id,$asp_id,
											$doc_hash,$response_url,$response_one,$response_two){		
			
		$this->loadModel('LimsEsignRequestResponseLogs');
		$user_id = $this->Session->read('user_code');
		
			$dataEntity = $this->LimsEsignRequestResponseLogs->newEntity(array(		
				'id'=>$id,'request_by_user_id'=>$user_id,'pdf_file_name'=>$pdf_file_name,
				'current_level'=>$current_level,'time_stamp'=>$ts,'txn_id'=>$txn_id,'asp_id'=>$asp_id,
				'doc_hash_value'=>$doc_hash,'response_url'=>$response_url,'response_one'=>$response_one,
				'response_two'=>$response_two,'created'=>date('Y-m-d H:i:s'),'modified'=>date('Y-m-d H:i:s')
			)); 
			
			$this->LimsEsignRequestResponseLogs->save($dataEntity);

		//get last insert id from table
		$get_last_id = $this->LimsEsignRequestResponseLogs->find('all',array('fields'=>'id',array('order'=>'id desc')))->first();
		$log_last_insert_id = $get_last_id['id'];
		$this->Session->write('log_last_insert_id',$log_last_insert_id);
		
	}
	

//this function is created to update response(first & second) log in db
	public function updateResponseLog($id,$response_one,$response_two){
		
		$this->loadModel('LimsEsignRequestResponseLogs');
					
		if($response_one != null){
			
			//string representation of array
			$response_one = json_encode($response_one);
			
			$responseEntity = $this->LimsEsignRequestResponseLogs->newEntity(array(		
				'id'=>$id,'response_one'=>$response_one,'modified'=>date('Y-m-d H:i:s')
			));
			$this->LimsEsignRequestResponseLogs->save($responseEntity);
			
		}elseif($response_two != null){
			
			//string representation of array
			$response_two = json_encode($response_two); 
			
			$responseEntity = $this->LimsEsignRequestResponseLogs->newEntity(array(		
				'id'=>$id,'response_two'=>$response_two,'modified'=>date('Y-m-d H:i:s')
			));
			$this->LimsEsignRequestResponseLogs->save($responseEntity);
			
			$this->Session->delete('log_last_insert_id');
		}
											
	}

//to fetch first response from ajax post data and update in DB record
	public function update1stReponseAjax(){
		$this->autoRender = false;
		$resp_arr = $_POST['resp1_arr'];		
		$uid_token = $resp_arr['info'];
		
		//update response one in DB
		$last_insert_id = $this->Session->read('log_last_insert_id');									
		$this->updateResponseLog($last_insert_id,$resp_arr,null);

	}
	
	
	//created this function to fetch cdac response array and verify the signature
	public function verifyCdacResponse($resp_array){
		
		//certificate file provided by CDAC
		$get_cdac_cert = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/cdac_ssl_cert.pem');
		$split_string = split('-----',$get_cdac_cert);//split string and get cert key string from it
		$cert_key_string = $split_string[2];
		 
		//signature attached with response 
		$resp_cdac_signature = $resp_array['Signature']['SignatureValue']; //updated on 31-05-2021 for Form Based Esign method
		//Certificate details attached with response
		$resp_cdac_cert = $resp_array['EsignResp']['Signature']['KeyInfo']['X509Data']['X509Certificate'];
		
		//remove white spaces and compare in condition
		if(preg_replace('/\s+/', '', $cert_key_string) == preg_replace('/\s+/', '', $resp_cdac_cert)){			
			return true;
		}else{
			return false;
		}

	}

//created this function to show esign failed message redirect to home page	
	public function esignIssue(){
		$message = '';
		$redirect_to = '';
		
		$message = 'Sorry.. Esign Failed, Please login again and try.';
		$redirect_to = '/';
		$this->view = '/Element/message_boxes';
		
		$this->set('message',$message);
		$this->set('redirect_to',$redirect_to);
	}
	

	
}
