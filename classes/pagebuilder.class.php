<?php
class PageBuilder{
	private $page;
	public function __construct($page = false){
		if($page !== false){
			$this->page = $page;		
		}
	}
	public function build($site, $returnFilePath = true){
		$template = $this->page->getTemplate();
		$filename = $template->getFilename();
		$spots = $template->getSpots();

		//loop spots and set assetvalues
		foreach($spots as $tplSpotId => $spot){
			$assetname = 'asset'.$tplSpotId;
			$spotname = $spot->systemName();
			if($spot->uservalue()){
				try{
					$$assetname = $this->page->getValue($tplSpotId);
				}catch(DataException $e){
					//TODO: may want to do something here, so we dont publish pages that lacks information
					$$assetname = $spot->getName().'error';
				}
			}else{
				$$assetname = $this->createStaticSpotFile($site, $spot, $tplSpotId);
			}
		}
		$filename = 'cache/templates/'.$site->getId().'/'.$filename;
		$path = str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']).$filename;
		if(!is_file($path)){
			throw new PathException('no file at '.$path);
		}
		ob_start();
		include_once($path);
		$html= ob_get_clean();
		return ($returnFilePath) ? $this->createFile($html, $site) : $html;

	}
	private function createStaticSpotFile($site, $spot, $tplSpotId){
		$staticspotPath = 'cache/templates/'.$site->getId().'/staticspots/';

		$filename = $spot->systemName().'_'.$tplSpotId.'.php';
		if(!is_dir($staticspotPath)){
			$fm = new FileMaker();
			$fm->makeDirectory($staticspotPath);
		}
		$filepath = $staticspotPath.$filename;
		$fm = new FileMaker();
		$fm->makeFile($filepath, $spot->getContent());

		$serverpath = 'dynamic/includes';
		$uploader = new FTPUploader($site);
		$uploader->upload($serverpath, $filepath, $filename);
		return $this->serverSideInclude($serverpath.$filename);
	}
	private function createFile($html, $site){
		$fileName = 'cache/pages/'.$site->getId().'/'.$this->page->getId().'_'.$this->page->currentRevision()->getId().'.php';
		$fm = new FileMaker();
		return $fm->makeFile($fileName, $html);
	}
	private function serverSideInclude($path){
		return '<?php include("dynamic/'.$path.'");?>';
	}
}

?>