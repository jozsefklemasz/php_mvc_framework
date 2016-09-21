<?php
final class View{

	private $blocks_folder, $block_pattern, $current_theme;

	function __construct(){
		$this->current_theme = 'main';
		$this->blocks_folder = 'mvc/view/theme/' . $this->current_theme . '/blocks/';
	}

	public function SetTitle($title){
		$this->title = $title;
	}

	public function GetTitle(){
		return $this->title;
	}

	public function Parse($view_file){
		$input = file_get_contents($view_file);
		$blocks = array_diff(scandir($this->blocks_folder), array('..', '.'));
		
		foreach ($blocks as $block) {
			$block_name = pathinfo($this->blocks_folder . $block, PATHINFO_FILENAME);
			$tpl_block = '{' . $block_name . '}';

			if (strpos($input, $tpl_block) !== false) {
				$block_content = $this->GetBlockContent($block);
				$input = str_replace($tpl_block, $block_content, $input);
			}
		}

		return $this->CreateOutput($input);
	}

	private function GetBlockContent($block_name){
		$block_path = 'mvc/view/theme/' . $this->current_theme . '/blocks/' . $block_name;
		$block_content = file_get_contents($block_path);
		
		return $block_content;
	}

	private function CreateOutput($content){
		$output_file_path = 'system/temp/temp_output' . $this->GenerateSession() . '.php';
		
		try {
			$this->WriteThemeFile($output_file_path, $content);
		} catch (Exception $e) {
			echo $e->getmessage();
			return false;
		}

		return $output_file_path;
	}

	private function WriteThemeFile($path, $content){
		if($output_file = fopen($path,'w')){
			fwrite($output_file,$content);
			fclose($output_file);	
		} else {
			throw new Exception("Can't create theme file. Check the permissions of the system/temp folder!");
		}
		
	}

	private function GenerateSession(){
		$time = time();
		$random_sequence = rand(10000000,99999999);
		return $time * $random_sequence;
	}
}