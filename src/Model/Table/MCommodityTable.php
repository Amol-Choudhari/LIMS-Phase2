<?php
namespace app\Model\Table;
	use Cake\ORM\Table;
	use App\Model\Model;
	use Cake\ORM\TableRegistry;

class MCommodityTable extends Table{

	var string $name = "MCommodity";

    
    //getCommodity
    //Description : This is function will return the commodity name by id.
    //Author : Akash Thakre
    //Date : 03-06-2022

    public function getCommodity($id) {
        if (!empty($id)) {
            $getData = $this->find('all')->select(['commodity_name'])->where(['commodity_code' => $id])->first();
            $detail = $getData['commodity_name'];
        } else {
            $detail = '';
        }
        return $detail;
    }
}

?>
