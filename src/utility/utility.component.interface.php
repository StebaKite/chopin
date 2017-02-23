<?php

interface UtilityComponentInterface {	
	
	public static function getInstance();
	
	public function setLanguageReplace($languageReplace);
	public function setConfiguration($configuration);
	
	public function getConfiguration();
	public function getLanguageReplace();
	
	public function tailFile($template, $replacement);
	public function tailTemplate($template);
	public function getTemplate($fileName);
	public function getConfig();
	public function getConfigInBatchMode($project_root);
	
}

?>