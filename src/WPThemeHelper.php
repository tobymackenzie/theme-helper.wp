<?php
/*
Class: WPThemeHelper
Used to contain basic theme functionality so it won't pollute the global namespace.  Will be instantiated into the variable $tjmThemeHelper.  Child themes may extend this class and instantiate their own $tjmThemeHelper to override class functionality.
*/
namespace TJM\WPThemeHelper;

use TJM\Component\BufferManager\BufferManager;

class WPThemeHelper{
	/*
	Method: __construct
	Initial setup of theme, including registering theme settings with WordPress.
	*/
	public function __construct($opts = Array()){
		$this->buffers =
			(isset($opts['buffers']))
			? $opts['buffers']
			: new BufferManager()
		;
		$this->data =
			(isset($opts['data']))
			? $opts['data']
			: new DataHelper()
		;
		$this->paths =
			(isset($opts['paths']))
			? $opts['paths']
			: new PathHelper()
		;
		$this->renderer =
			(isset($opts['renderer']))
			? $opts['renderer']
			: new Renderer(Array(
				'bufferManager'=> $this->buffers
				,'pathManager'=> $this->paths
			))
		;
		if(!(
			isset($opts['settings'])
			&& !$opts['settings']
			&& !is_array($opts['settings'])
		)){ //-# don't create settings helper at all if settings is set and falsey (not including an empty array)
			if(isset($opts['settings']) && is_object($opts['settings']) && !is_callable($opts['settings'])){
				$this->settings = $opts['settings'];
			}else{
				$settings = (isset($opts['settings'])) ? $opts['settings'] : null;
				$this->settings = new SettingHelper(Array(
					'settings'=> $settings
				));
			}
		}
		$this->shortcodes =
			(isset($opts['shortcodes']))
			? $opts['shortcodes']
			: new ShortcodeHelper()
		;
	}

	public $buffers;
	public $data;
	public $paths;
	public $renderer;
	public $shortcodes;
	public $settings;

	/*
	Method: dump
	var_dump with some html wrappings
	*/
	static public function dump(){
		$args = func_get_args();
		if(count($args) > 1){
			$heading = 'Debug: ' . array_shift($args) . ': ';
		}else{
			$heading = 'Debug: ';
		}
		echo "<h3>{$heading}</h3>";
		foreach($args as $arg){
			echo "<hr /><pre>"; var_dump($arg); echo "</pre>";
		}
	}

	/*==========
	==buffer
	==========*/
	/*
	Method: endBuffer
	Alias to $this->buffers->end()
	*/
	public function endBuffer(){
		return call_user_func_array(Array($this->buffers, 'end'), func_get_args());
	}

	/*
	Method: getBuffer
	Alias to $this->buffers->get()
	*/
	public function getBuffer(){
		return call_user_func_array(Array($this->buffers, 'get'), func_get_args());
	}

	/*
	Method: hasBuffer
	Alias to $this->buffers->has()
	*/
	public function hasBuffer(){
		return call_user_func_array(Array($this->buffers, 'has'), func_get_args());
	}

	/*
	Method: startBuffer
	Alias to $this->buffers->start()
	*/
	public function startBuffer(){
		return call_user_func_array(Array($this->buffers, 'start'), func_get_args());
	}

	/*==========
	==WordPress helpers
	==========*/
	/*
	Method: getMenuObject
	Get WordPress menu object for named location
		-@ http://wordpress.org/support/topic/display-title-custom-menu
	Parameters:
		name(String): name of location to get menu object for
	*/
	public function getMenuObject($name){
		$menuLocations = get_nav_menu_locations();
		if(isset($menuLocations[$name])){
			return wp_get_nav_menu_object($menuLocations[$name]);
		}else{
			return null;
		}
	}
	/*
	Method: hasParent
	Check if current post has ancestors
		-@ based on instructions from http://codex.wordpress.org/Conditional_Tags
	*/
	//
	public function hasParent($argParentID){
		global $post;

		if(is_page($argParentID)) return true;

		$fncAncestors = get_post_ancestors($post->ID);
		foreach($fncAncestors as $forAncestor)
			if(is_page() && $forAncestor == $argParentID) return true;

		return false;
	}
}
