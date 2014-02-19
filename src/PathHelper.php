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
	public function getThemeFilePath($name, $container = '', $extension = ''){
		if(substr($name, 0, 1) == '/'){
			$templatePath = $name;
		}else{
			$relativePath = DIRECTORY_SEPARATOR;
			if($container != ''){
				$relativePath .= $container . DIRECTORY_SEPARATOR;
			}
			$relativePath .= $name;
			if($extension != ''){
				$relativePath .= ".{$extension}";
			}
			$templatePath = get_stylesheet_directory() . $relativePath;
			//--
			if(!file_exists($templatePath)){
				$templatePath = get_template_directory() . $relativePath;
			}
		}
		return (file_exists($templatePath)) ? $templatePath : null;
	}

}
