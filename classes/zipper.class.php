<?php
class Zipper extends FileUploader{
	public function __construct(){
		$this->setbasePath();
	}
	public function IsZipFile($filename){
		$ext = $this->getExtension($filename);
		switch($ext){
			case 'rar' : 
			case 'zip' : return true;break;
			default : return false;
		}
	}
	public function Decompress($file, $location){
		$filename = $this->getFilename($file);
	
		if(!rename($file, $location.$filename)){
			throw new DataException('cantrename');
		}
		
		switch($this->getExtension($filename)){
			case 'rar' : $result = $this->Unrar($location.$filename, $location);break;
			case 'zip' : $result = $this->Unzip($location.$filename, $location);break;
			default: throw new DataException('UnpackError');break;			
		}
		$filelist = $this->getFilelist($location);
		rename($location.$filename, $file);
		return $filelist;
	}
	private function Unzip($filepath, $location){
		$zip = new ZipArchive;
		$res = $zip->open($filepath);
		if ($res === TRUE) {
			$zip->extractTo($location);
			$zip->close();
			return true;
		}
		$errmessage = $this->getErrorMessage($res);
		throw new Exception('zipfailed_'.$errmessage.$filepath);	
	}
	private function getErrorMessage($res){
		switch($res){
			case ZIPARCHIVE::ER_EXISTS : return 'er_exists';break;
			case ZIPARCHIVE::ER_INCONS : return 'er_incons';break;
			case ZIPARCHIVE::ER_INVAL  : return 'er_inval';break;
			case ZIPARCHIVE::ER_MEMORY : return 'er_memory';break;
			case ZIPARCHIVE::ER_NOENT  : return 'er_noent';break;
			case ZIPARCHIVE::ER_NOZIP  : return 'er_nozip';break;
			case ZIPARCHIVE::ER_OPEN   : return 'er_open';break;
			case ZIPARCHIVE::ER_READ   : return 'er_read';break;
			case ZIPARCHIVE::ER_SEEK   : return 'er_seek';break;
			default: return $res;break;
		}
	}
	private function Unrar($filepath, $location){
		$rar = new RarArchive;
		$res = $rar->open($filepath);
		if ($res === TRUE) {
			$entries = $rar->getEntries();
			foreach($entries as $entry){
				$entry->extract($location);
			}
			$rar->close();
			return true;
		}
		throw new Exception('rarfailed');
		
	}
	private function getFilelist($folder){
		$list = array();
		$handler = opendir($folder);
		while ($file = readdir($handler)){
			if(is_file($folder.$file)){
				$list[] = $file;
			}
	    }
		return $list;
	}
}
?>