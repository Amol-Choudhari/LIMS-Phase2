<?php
namespace App\Controller;

use Cake\Event\Event;
use App\Network\Email\Email;
use Cake\ORM\Entity;
use Cake\Datasource\ConnectionManager;
use Cake\View;

class SamplePaymentController extends AppController{

	var $name = 'SamplePayment';
	
	public function initialize(): void {
		parent::initialize();
		$this->viewBuilder()->setLayout('admin_dashboard');
		$this->viewBuilder()->setHelpers(['Form','Html']);
		$this->loadComponent('Customfunctions');
        $this->loadComponent('PaymentDetails');
	}

    public function payment(){			
    
        $this->loadModel('DmiFirms');
        
        $sample_code = $this->Customfunctions->sessionCustomerID();

        if(!empty($customer_id)){
            
            // set variables to show popup messages from view file
            $message_theme = ''; // set message theme like error/success @by Aniket Ganvir dated 17th DEC 2020
            $message = '';
            $redirect_to = '';
            
            //check CA BEVO Applicant		
            $ca_bevo_applicant = $this->Customfunctions->checkCaBevo($customer_id);
            $oldapplication = $this->Customfunctions->isOldApplication($customer_id);
            $this->set('ca_bevo_applicant',$ca_bevo_applicant);
            $this->set('oldapplication',$oldapplication);
            
            $application_type = $this->Session->read('application_type');
            $office_type = $this->Customfunctions->getApplDistrictOffice($customer_id);			
            $firm_type = $this->Customfunctions->firmType($customer_id);		
            $firm_type_text = $this->Customfunctions->firmTypeText($customer_id);
            $form_type = $this->Customfunctions->checkApplicantFormType($customer_id);
            $this->set('form_type',$form_type);	
            if($form_type=='F' && $ca_bevo_applicant=='yes'){
                $form_type='E';
            }
            
            $this->loadModel('DmiCommonScrutinyFlowDetails');
            $this->loadModel('DmiFlowWiseTablesLists');
                            
            $section_details = $this->DmiCommonScrutinyFlowDetails->currentSectionDetails($application_type,$office_type,$firm_type,$form_type,1);
            
            $allSectionDetails = $this->DmiCommonScrutinyFlowDetails->allSectionList($application_type,$office_type,$firm_type,$form_type);
            
            // get previous and next button id 
            $previousBtn =	$this->Customfunctions->getNextPreSec($allSectionDetails);				
            $previous_button_url = 'application/section/'.$previousBtn[2];
            
            // For change flow
            $selectedSections = array();	
            if($application_type == 3){
                $this->loadModel('DmiChangeSelectedFields');				
                $selectedfields = $this->DmiChangeSelectedFields->selectedChangeFields();
                $selectedSections = $selectedfields[2];
            }
            $this->set('selectedSections',$selectedSections);
            
            // if return value 1 (all forms saved), return value 2 (all forms approved), return value 0 (all forms not saved or approved)
            $all_section_status = $this->Customfunctions->formStatusValue($allSectionDetails,$customer_id);
            
            $payment_table = $this->DmiFlowWiseTablesLists->getFlowWiseTableDetails($application_type,'payment');				
            
            $final_submit_details = $this->Customfunctions->finalSubmitDetails($customer_id,'application_form');			
            $this->set('final_submit_details',$final_submit_details);		
            
            $progress_bar_status = $this->Progressbar->formsProgressBarStatus($allSectionDetails,$customer_id);
            $this->set('progress_bar_status',$progress_bar_status);
        
            $firm_detail = $this->DmiChangeFirms->sectionFormDetails($customer_id);
            $firm_details = $firm_detail[0];
            $this->set('firm_details',$firm_details);
            
            // Fetch submitted Payment Details and show // Done By pravin 13/10/2017
            $this->Paymentdetails->applicantPaymentDetails($customer_id,$firm_details['district'],$payment_table);
            
            $this->loadModel('DmiApplicationCharges');
            $this->loadModel('MCommodity');
            $this->loadModel('MCommodityCategory');
            

            $application_charge = $this->Customfunctions->applicationCharges($application_type,$firm_type);
            $this->set('application_charge',$application_charge);
                            
            $this->loadModel($payment_table);
            $list_applicant_payment_id = $this->$payment_table->find('list', array('valueField'=>'id','conditions'=>array('customer_id IS'=>$customer_id)))->toArray();
            if(!empty($list_applicant_payment_id)){ $process_query = 'Updated'; }else{ $process_query = 'Saved'; }
                    
            $sub_commodity_array = explode(',',$firm_details['sub_commodity']);
            
            if(!empty($firm_details['sub_commodity'])){
                $i=0;
                foreach($sub_commodity_array as $sub_commodity_id)
                {				
                    $fetch_commodity_id = $this->MCommodity->find('all',array('conditions'=>array('commodity_code IS'=>$sub_commodity_id)))->first();
                    $commodity_id[$i] = $fetch_commodity_id['category_code'];						
                    $sub_commodity_data[$i] =  $fetch_commodity_id;						
                    $i=$i+1;
                }

                $unique_commodity_id = array_unique($commodity_id);
                
                $commodity_name_list = $this->MCommodityCategory->find('all',array('conditions'=>array('category_code IN'=>$unique_commodity_id, 'display'=>'Y')))->toArray();

                $this->set('commodity_name_list',$commodity_name_list);
                
                $this->set('sub_commodity_data',$sub_commodity_data);
            }
        
            if(!empty($firm_details['packaging_materials'])){			
                $this->loadModel('DmiPackingTypes');
                $packaging_materials = explode(',',$firm_details['packaging_materials']);				 
                $packaging_type = $this->DmiPackingTypes->find('list', array('valueField'=>'packing_type', 'conditions'=>array('id IN'=>$packaging_materials)));			 
                $this->set('packaging_type',$packaging_type);	
            }
            
            if(!empty($final_submit_details)){
                $final_submit_status = $final_submit_details['status'];
            }else{
                $final_submit_status = 'no_final_submit';
            }
            $this->set('final_submit_status',$final_submit_status);
            
            // set variables to show popup messages from view file
            $this->set('previous_button_url',$previous_button_url);
            $this->set('allSectionDetails',$allSectionDetails);
            $this->set('all_section_status',$all_section_status);
            $this->set('section_details',$section_details);
            
            if (null !== ($this->request->getData('final_submit'))) {
                
                //applied this condition on 26-03-2018 by Amol, with esign or without
                if(!empty($this->request->getData('once_no'))){
                    //calling common function for esigning//applied on 01-11-2017 by Amol
                    //$this->process_to_esign($customer_id);
                    //print_r("hi"); exit;
                }else{
                    //proceed without esign
                    $this->Session->write('with_esign','no');
                    $final_submit_call_result =  $this->Customfunctions->applicationFinalSubmitCall($customer_id,$all_section_status);

                    if($final_submit_call_result == true){	
                            $message_theme = 'success';
                            $message = $firm_type_text.' - Final submitted successfully ';
                            $redirect_to = '../applicationformspdfs/'.$section_details['forms_pdf'];
                            $this->viewBuilder()->setVar('message', $message);
                            $this->viewBuilder()->setVar('redirect_to', $redirect_to);								
                    }else{						
                            $message_theme = 'failed';
                            $message = $firm_type_text.' - All Sections not filled, Please fill all Section and then Final Submit ';
                            $redirect_to = '../application/application-for-certificate';
                            $this->viewBuilder()->setVar('message', $message);
                            $this->viewBuilder()->setVar('redirect_to', $redirect_to);									
                    }		
                                                    
                    //$this->view = '/Element/message_boxes';	
                    $this->render('/element/message_boxes');
                }
                                

            }elseif(null !== ($this->request->getData('save'))) {  // Save payment details by applicant (done by pravin 13/10/2017)
                        
                $get_payment_details = $this->Paymentdetails->saveApplicantPaymentDetails($this->request->getData(), $payment_table);
                
                if ($get_payment_details == true) {								
                    $message_theme = 'success';
                    $message = $firm_type_text.' - Payment Section, '.$process_query.' successfully';
                    $redirect_to = 'payment';
                    $this->viewBuilder()->setVar('message', $message);
                    $this->viewBuilder()->setVar('redirect_to', $redirect_to);	
                    $this->render('/element/message_boxes');
                }				
            
            }
                    
            
            $this->set('message_theme',$message_theme);
            $this->set('message',$message);
            $this->set('redirect_to',$redirect_to);			
        }

    }

}


?>