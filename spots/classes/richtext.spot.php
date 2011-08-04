<?php
class Richtext extends AdminSpot{
	public function giveAdmin($spotTplId, $value = ''){
		global $dRep;
		$menu = new FolderList(array());
		$spotvalue = $value;
		$spotId = $spotTplId;
		include('spots/adminviews/richtext.admin.php');
	}
	public function getContent($value){
		return $value;
	}

}
?>
