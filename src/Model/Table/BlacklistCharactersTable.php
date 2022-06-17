<?php
namespace app\Model\Table;
	use Cake\ORM\Table;
	use App\Model\Model;
	use Cake\ORM\TableRegistry;
	
class BlacklistCharactersTable extends Table{
	
	var $name = "BlacklistCharacters";
	var $useTable = 'blacklist_characters';
}

?>