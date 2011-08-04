<?php

class templateParser
{
	// member definition
	var $output;
	function templateParser($templateFile='template.htm')
	{
		// constructor setting up class initialization
		(file_exists($templateFile))?$this->output = file_get_contents($templateFile):die('Error:Template file '.$templateFile.' not found');
	}

	function parseTemplate($tags=array())
	{
		// code for parsing template files
/*		if(count($tags)>0)
		{*/
			foreach($tags as $tag=>$data)
			{
				/*
					checks if data sent is a file, means we cant pass filepath as data
					$data=(@file_exists($data))?$this->parseFile($data):$data;
				*/
				$this->output=str_replace('{'.$tag.'}',$data,$this->output);
			}
/*		}
		else 
		{
			die('Error: No tags were provided for replacement');
		}*/
	}
	function parseFile($file)
	{
		ob_start();
		include($file);
		$content=ob_get_contents();
		ob_end_clean();
		return $content;
	}
	function display()
	{
		// code for displaying the finished parsed page
		return $this->output;
	}
}
?>