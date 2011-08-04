<?php
class TemplateMaker{
	private $path;
	private $templatefile;
	public function __construct(){
		
	}
	public function createTemplate($path, $name, $template){
		$this->path = $path;
		$content = $this->readFile();
		$spots = $this->findSpots($content);
		$spotMaker = new SpotMaker();
		$spots = $spotMaker->createSpots($spots, $template);
		return $this->makeTemplate($spots, $name, $template);
	}
	private function makeTemplate($spots, $name, $template){
		$customer = unserialize($_SESSION['customer']);
		$name = ($name == '') ? 'Unnamed' : $name;
		$filename = substr(strrchr($this->templateFile, '/'), 1);
		$properties = array('name' => $name, 'customer' => $customer->getId(), 'spots' => $spots, 'filename' => $filename, 'id' => $template->getId());
		$template->setProperties($properties);
		return $template;
	}
	private function writeFile($string, $createPhp = true){
		$myFile = $this->path;
		
		$customer = unserialize($_SESSION['customer']);	
		$site = $customer->getSite();		
		$ext = substr($myFile, strrpos($myFile, '.'));
		if($createPhp){
			$this->templateFile = str_replace($ext, '_'.$site->getId().'.php', $myFile);
		}else{
			$this->templateFile = $this->path;
		}
		/*write content*/
		$filehandler = new Filemaker();
		$filehandler->writeFile($this->templateFile, $string);
	}
	private function rstrpos ($haystack, $needle, $offset){
		$size = strlen ($haystack);
		$pos = strpos (strrev($haystack), $needle, $size - $offset);
		
		if ($pos === false)
			return false;
		
		return $size - $pos;
	}
	private function readFile(){
		$filehandler = new Filemaker();
		return $filehandler->getFileContent($this->path);
	}
	public function updateTemplate($template){
		$customer = unserialize($_SESSION['customer']);	
		$site = $customer->getSite();		
		$spots = $template->getSpots();
		$this->path = 'cache/templates/'.$site->getId().'/'.$template->getFilename();
		$content = $this->readFile();
		$strings = array();
		$counter = 0;
		foreach($spots as $tplSpotId => $spot){
			$subStr = '<?php echo $asset'.$spot->order().'; ?>';
			$content = str_replace($subStr, '<?php echo $asset'.$tplSpotId.';?>', $content); 
			$counter++;
		}
		$this->writeFile($content, false);
	}
	public function findSpots($content){
		$offset = 0;
		$spotsConfigs = array();
		$count = 0;
		while(($position = stripos($content, 'edit="')) !== false){
			//we have a position, now we need to find what type of edit it is
				
			$startEdit= $position + 6;
			$endEdit = stripos($content, '"', $startEdit + 1);
			//get config string by looking for first " after the edit= attribute
			$editString = substr($content, $startEdit, ($endEdit - $startEdit));
			
			//insert asset config arrays into an array so we can go through and create templates later
			$spotsConfigs[$startEdit] = explode('-', $editString);
			
			//remove the edit attribute
			$content =  preg_replace('/edit="'.$editString.'"/i', '', $content, 1);

			//find the tag start and end - for now you cant have same tag nested inside the edit tag
			$tagStart = $this->rstrpos($content, '<', $position);
			$tagNameEnd = strpos($content, ' ', $tagStart);
			$tagName = substr($content, $tagStart, ($tagNameEnd - $tagStart));
			$tagEnd = strpos($content, '>', $tagNameEnd);
			$tag = substr($content, $tagStart, ($tagEnd - $tagStart));
			$closeTagPos = strpos($content, '</'.$tagName.'>', $tagEnd);
			
			//create the php tag we are going to use later
			$content = substr_replace($content, '<?php echo $asset'.$count.'; ?>', $tagEnd + 1, ($closeTagPos - $tagEnd - 1));
			
			$count++;
		}
		$this->writeFile($content);
		return $spotsConfigs;
	}	
}
?>

