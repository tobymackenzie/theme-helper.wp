<?php
/*
Class: ConfigHelper
Helps with working with wp-config.  Done statically to not require instantiation.
*/
namespace TJM\WPThemeHelper;

class ConfigHelper{
	/*
	Method: init
	Init config helper.
	*/
	public static function init($opts = Array()){
		self::loadVars((isset($opts['vars'])) ? $opts['vars'] : Array());
		self::initConstants($opts);
		self::initGlobals($opts);
		self::postInit($opts);
	}

	protected static function initConstants($opts = Array()){
		$constants = self::getDefaultConstants();
		if(isset($opts['constants'])){
			$constants = array_merge($constants, $opts['constants']);
		}
		self::setConstants($constants);
	}
	protected static function initGlobals($opts = Array()){
		$globals = self::getDefaultGlobals();
		if(isset($opts['globals'])){
			$globals = array_merge($globals, $opts['globals']);
		}
		self::setGlobals($globals);
	}
	protected static function postInit($opts = Array()){
		//--debug stuff
		if(WP_DEBUG){
			// error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
		}
	}

	/*=====
	==variables/constants
	=====*/

	//==constants //

	/*
	Static Property: defaultConfigConstants
	Defaults for configConstants property.
	*/
	public static function getDefaultConstants(){
		return Array(
			//--security
			'AUTH_KEY'=> '1234567890poiuytrewq'
			,'SECURE_AUTH_KEY'=> '0987654321zxcvbnmlkjh'
			,'LOGGED_IN_KEY'=> 'asdfghjklpoiuytrewq'
			,'NONCE_KEY'=> 'zxcvbnmlkjhgfdsa'
			,'AUTH_SALT'=> 'asdfghjklmnbvcxz'
			,'SECURE_AUTH_SALT'=> 'mnbvcxzasdfghjkl'
			,'LOGGED_IN_SALT'=> '1234567890qwertyuiop'
			,'NONCE_SALT'=> '1234567890plkjhgfdsazxc'
		);
	}

	/*
	Method: setConstant
	Set a constant
	Arguments:
		name(String): name of constant to set
		value(Mixed): value to set constant to
	Returns:
		(Boolean): whether constant was set
	*/
	public static function setConstant($name, $value){
		if(!defined($name)){
			define($name, self::fillTokens($value));
			if(self::getVar('debug')){
				echo "setConstant: {$name}=> " . constant($name) . "<br />\n";
			}
			return true;
		}else{
			return false;
		}
	}

	/*
	Method: setConstants
	Set PHP constants with keys of array to values of array.  Can pass multiple arrays as arguments, with later array values taking precedence.
	Arguments
		{n}(Array): Constant (key) to be set to (value).  Items in later arguments overload those in earlier arguments.
	*/
	public static function setConstants(){
		$constants = Array();
		foreach(func_get_args() as $arg){
			$constants = array_merge($constants, $arg);
		}
		foreach($constants as $name=> $value){
			self::setConstant($name, $value);
		}
	}

	//==globals //

	/*
	Method: getDefaultGlobals
	Get default globals array.
	Returns:
		(Array): default globals
	*/
	public static function getDefaultGlobals(){
		return Array(
		);
	}

	/*
	Method: setGlobal
	Set a global variable
	Arguments:
		name(String): name of variable to set
		value(Mixed): value to set variable to
	*/
	public static function setGlobal($name, $value){
		$GLOBALS[$name] = self::fillTokens($value);
		if(self::getVar('debug')){
			echo "setGlobal: {$name}=> <pre>";
			var_dump($GLOBALS[$name]);
			echo "</pre><br />\n";
		}
	}

	/*
	Method: setGlobals
	Arguments
		{n}(Array): Constant (key) to be set to (value).  Items in later arguments overload those in earlier arguments.
	*/
	public static function setGlobals(){
		$constants = Array();
		foreach(func_get_args() as $arg){
			$constants = array_merge($constants, $arg);
		}
		foreach($constants as $name=> $value){
			self::setGlobal($name, $value);
		}
	}

	//==vars //

	/*
	Property: vars
	Variables used internally by ConfigHelper.
	*/
	protected static $vars;
	public static function getVar($name){
		return (isset(self::$vars[$name])) ? self::$vars[$name] : null;
	}

	/*
	Method: getDefaultVars
	Get default config vars array.
	Returns:
		(Array): default vars
	*/
	public static function getDefaultVars(){
		return Array(
			//--whether to output debug messages for this class
			'debug'=> false
			,'contentRelativePath'=> '_content'
			,'host'=> ($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['PWD']
			,'webRoot'=> ($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : $_SERVER['PWD']
			,'wpRelativePath'=> '_wp'
			,'protocol'=> (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 'https' : 'http'
		);
	}

	/*
	Method: loadVars
	Load values into `vars` property, including defaults.  Any arrays passed as arguments will be merged into array, latter arguments taking precedence.
	*/
	public static function loadVars(){
		if(!self::$vars){
			self::$vars = self::getDefaultVars();
		}
		foreach(func_get_args() as $arg){
			self::$vars = array_merge(self::$vars, $arg);
		}
		return self::$vars;
	}


	/*=====
	==helpers
	=====*/
	/*
	Method: fillTokens
	This class uses tokens within strings to allow values to use other values.  This method will replace all tokens with their appropriate values.  Values can come from constants, variables, or WPApp::vars.  Tokens are wrapped in double curly brackets.  They would look like

	- constants: '{{CONSTANT_NAME}}'
	- globals: '{{globalVariableName}}'
	- vars: '{{ConfigHelper::vars.varName}}'
	Arguments:
		string(String): string to replace tokens in
	Returns:
		(String): string with tokens replaced
	*/
	public static function fillTokens($string){
		preg_match_all("/\{\{([\w\.\-:]+)\}\}/", $string, $matches);
		$tokens = $matches[0];
		$names = $matches[1];

		foreach($names as $key=> $name){
			$token = $tokens[$key];
			if(substr($name, 0, 21) === 'ConfigHelper::vars.'){
				$value = self::getVar(substr($name, 21));
			}elseif(substr($name, 0, 1) === '$'){
				$value = $GLOBALS[$name];
			}else{
				$value = constant($name);
			}
			$string = str_replace($token, $value, $string);
		}

		return $string;
	}
}
