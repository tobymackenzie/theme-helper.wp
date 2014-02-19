<?php
/*
Class: DataHelper
Provides place to store data
*/
namespace TJM\WPThemeHelper;

class DataHelper{
	/*==========
	==data
	==========*/
	/*
	Attribute: data
	Array containing generic data to be accessed by anything with reference to an instance of this class.
	*/
	protected $data = Array();
	/*
	Method: get
	Access a data value by key
	Parameters:
		key(String): Key of data value
	Return:
		Value of data stored at key, or null if it is not set.
	*/
	public function get($key){
		return ($this->has($key)) ? $this->data[$key] : null;
	}
	/*
	Method: has
	See if a data key is set
	Parameters:
		key(String): Key of data value
	*/
	public function has($key){
		return array_key_exists($key, $this->data);
	}
	/*
	Method: set
	Set a data value by key
	Parameters:
		key(String): Key of data value
		value(Mixed): Value to store in key
	Return:
		this
	*/
	public function set($key, $value){
		$this->data[$key] = $value;
		return $this;
	}
}
