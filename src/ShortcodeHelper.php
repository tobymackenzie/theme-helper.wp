<?php
/*
Class: ShortcodeHelper
Manages adding shortcodes and provides simple interface to add many at once.
*/
namespace TJM\WPThemeHelper;

class ShortcodeHelper{
	public function __construct($shortcodes = null){
		if($shortcodes){
			$this->add($shortcodes);
		}
	}
	protected $shortcodes = Array();

	/*
	Method: add
	Add one or more shortcodes
	Parameters:
		codes(String|Array): If string, adds shortcode for said string.  If array, adds a shortcode for each key as the code and value as callable
		method(callable): function/method to call for shortcode
	*/
	public function add($codes, $method = null){
		if(is_array($codes)){
			foreach($codes as $name=> $method){
				$this->add($name, $method);
			}
		}else{
			$this->shortcodes[$codes] = $method;
			add_shortcode($codes, $method);
		}
		return $this;
	}
	/*
	Method: get
	Get callable(s) for shortcode(s), or all shortcodes if no parameter is passed.
	*/
	public function get($codes = null){
		if(is_array($codes)){
			$return = Array();
			foreach($codes as $code){
				$return[$code] = $this->get($code);
			}
			return $return;
		}elseif(is_string($codes)){
			return $this->shortcodes[$codes];
		}else{
			return $this->shortcodes;
		}
	}
}
