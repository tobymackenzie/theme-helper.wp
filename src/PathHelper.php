<?php
/*
Class: PathHelper
Access point for common paths
*/
namespace TJM\WPThemeHelper;

class PathHelper{
	/*==========
	==files
	==========*/
	/*
	Method: getThemeFilePath
	Get the file path to a file inside a theme.  Takes into account child themes, allowing them to override parent theme files.
	Parameters:
		name(String): name of file.  Can have relative or absolute path attached
		container(String): containing folder or path to use within theme folder
		extension(String): optional file extension to add to name
	Return:
		(String): path to file that exists, or null.
	*/
	static public function getThemeFilePath($name, $container = '', $extension = ''){
		if(substr($name, 0, 1) == '/'){
			$templatePath = $name;
		}else{
			$relativePath = self::getRelativePath($name, $container, $extension);
			$templatePath = self::getChildThemeFilePath($name, $container, $extension);
			//--
			if(!file_exists($templatePath)){
				$templatePath = self::getParentThemeFilePath($name, $container, $extension);
			}
		}
		return (file_exists($templatePath)) ? $templatePath : null;
	}

	/*
	Method: getChildThemeFilePath
	Get the file path to a file inside a child theme.
	Parameters:
		{see `getRelativePath()`}
	Return:
		(String): path to file.
	*/
	static public function getChildThemeFilePath($name, $container = '', $extension = ''){
		return get_stylesheet_directory() . self::getRelativePath($name, $container, $extension);
	}

	/*
	Method: getParentThemeFilePath
	Get the file path to a file inside a parent theme.
	Parameters:
		{see `getRelativePath()`}
	Return:
		(String): path to file.
	*/
	static public function getParentThemeFilePath($name, $container = '', $extension = ''){
		return get_template_directory() . self::getRelativePath($name, $container, $extension);
	}

	/*=====
	==helpers
	=====*/

	/*
	Method: getRelativePath
	Get relative file path for three pieces: container, name, and extension.
	Parameters:
		name(String): name of file
		container(String): containing folder(s)
		extension(String): optional file extension to add to name
	Return:
		(String): relative file name constructed from parameters
	*/
	static public function getRelativePath($name, $container = '', $extension = ''){
		$relativePath = DIRECTORY_SEPARATOR;
		if($container != ''){
			$relativePath .= $container . DIRECTORY_SEPARATOR;
		}
		$relativePath .= $name;
		if($extension != ''){
			$relativePath .= ".{$extension}";
		}
		return $relativePath;
	}
}
