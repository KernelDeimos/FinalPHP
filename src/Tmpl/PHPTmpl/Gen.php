<?php

namespace FinalPHP\Tmpl\PHPTmpl;

class Gen {
	static function CSS($src, $antiCache = false) {
		if ($antiCache) {
			$src = $src.'?'.time();
		}
		$ats = array(
			'rel' => "stylesheet",
			'type' => "test/css",
			'href' => $src,
		);
		return new Tag("link", $ats);
	}
	static function JS($src, $antiCache = false) {
		if ($antiCache) {
			$src = $src.'?'.time();
		}
		return new Tag("script", array('src' => $src));
	}
	static function JQ() {
		return new Tag("script", array(
			'src' => "http://code.jquery.com/jquery-latest.js",
		));
	}

	static function Pre($text) {
		return new Tag("pre", NULL, $text);
	}
	static function Dump($text) {
		ob_start();
		var_dump($text);
		$result = ob_get_clean();
		return self::Pre($result);
	}

	static function LF() {
		return "\n";
	}

	static function Document() {
		return new Tag("html", NULL, array(
			new Tag("head"),
			new Tag("body"),
		));
	}

	static function __callStatic($name, $args) {
		if (count($args) < 2) {
			$args[] = NULL;
		}
		return new Tag($name, ...array_reverse($args));
	}
}
?>
