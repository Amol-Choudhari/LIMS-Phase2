<?php

namespace app\Model\Table;
use Cake\ORM\Table;
use App\Model\Model;
use Cake\ORM\TableRegistry;
use App\Controller\InwardDetailsController;


class LimsSamplePaymentDetailsTable extends Table{

	var string $name = "LimsSamplePaymentDetails";
    var string $useTable = 'lims_sample_payment_details';


    public function getPaymentDetails(){

        $org_sample_code = $_SESSION['org_sample_code'];
    
        return $this->find('all')->where(['sample_code IS' => $org_sample_code])->first();
    }



    
    

}

?>
