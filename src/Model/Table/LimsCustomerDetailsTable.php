<?php

namespace app\Model\Table;
use Cake\ORM\Table;
use App\Model\Model;
use Cake\ORM\TableRegistry;

class LimsCustomerDetailsTable extends Table{
	
	var $name = "LimsCustomerDetails";


    // Save details 
	// Description : Save the details of the customer
	// Author : Akash Thakre
	// Date : 22-07-2022

	public function saveCustomerDetails($org_sample_code,$postData){

        if (!empty($postData['customer_name']) && 
            !empty($postData['customer_email_id']) && 
            !empty($postData['customer_mobile_no']) &&
            !empty($postData['street_address']) && 
            !empty($postData['state']) && 
            !empty($postData['district']) && 
            !empty($postData['postal_code'])) 
        { 

            $org_sample_code = htmlentities($postData['org_sample_code'], ENT_QUOTES);
            $customer_name	= htmlentities($postData['customer_name'], ENT_QUOTES);
            $customer_email_id	= htmlentities($postData['customer_email_id'], ENT_QUOTES);
            $customer_mobile_no	= htmlentities($postData['customer_mobile_no'], ENT_QUOTES);
            $customer_fax_no	= htmlentities($postData['customer_fax_no'], ENT_QUOTES);
            $street_address	= htmlentities($postData['street_address'], ENT_QUOTES);
            $state	= htmlentities($postData['state'], ENT_QUOTES);
            $district = htmlentities($postData['district'], ENT_QUOTES);
            $postal_code = htmlentities($postData['postal_code'], ENT_QUOTES);
            $sample_type_code = htmlentities($postData['sample_type_code'], ENT_QUOTES);

            $record_id = $this->find('all')->select(['id'])->where(['org_sample_code IS' => $org_sample_code])->first();

            //edit array
            if ($record_id != null) {
        
                $data_array = array(

                    'id'=>$record_id['id'],
                    'customer_name'=>$customer_name,
                    'customer_email_id'=>base64_encode($customer_email_id),
                    'customer_mobile_no'=>base64_encode($customer_mobile_no),
                    'customer_fax_no'=>$customer_fax_no,
                    'street_address'=>$street_address,
                    'state'=>$state,
                    'district'=>$district,
                    'postal_code'=>$postal_code,
                    'sample_type_code'=>$sample_type_code,
                    'modified'=>date('Y-m-d H:i:s')
                );
    
            } else {
                //add array
                $data_array = array(

                    'org_sample_code'=>$org_sample_code,
                    'customer_name'=>$customer_name,
                    'customer_email_id'=>base64_encode($customer_email_id),
                    'customer_mobile_no'=>base64_encode($customer_mobile_no),
                    'customer_fax_no'=>$customer_fax_no,
                    'street_address'=>$street_address,
                    'state'=>$state,
                    'district'=>$district,
                    'postal_code'=>$postal_code,
                    'sample_type_code'=>$sample_type_code,
                    'created'=>date('Y-m-d H:i:s'),
                    'modified'=>date('Y-m-d H:i:s')
                );
            }
            
            $saveEntity = $this->newEntity($data_array);
    
            if ($this->save($saveEntity)) {
    
                return 1;
            }
        
        } else {
            return 0;
        }

    }





}

?>