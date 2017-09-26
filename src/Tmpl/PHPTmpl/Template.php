<?php

namespace FinalPHP\Tmpl\PHPTmpl;

class Template {
	private $vars = array();
	private $templateFile = '';

	private function __construct($filename)
	{
		if ($filename !== NULL) {
			$this->templateFile = $filename;
		}
	}

	public static function NewWithFile($filename)
	{
		return new Template($filename);
	}

	public static function New($filename)
	{
		return new Template(NULL);
	}

	function SetFile($filename)
	{
		$this->templateFile = $filename;
	}

	function Render() {
		$this->loadInclude($this->templateFile);
	}

	function __set($index, $value)
	{
		$this->vars[$index] = $value;
	}

	function loadInclude($file) {
		foreach ($this->vars as $key => $value) $$key = $value;
		require($file);
	}
}
