<?php
/*
Class: Renderer
Handles rendering of files
*/
namespace TJM\WPThemeHelper;

use TJM\Component\BufferManager\BufferManager;

class Renderer{
	protected $bufferManager;
	protected $pathManager;

	/*
	Method: __construct
	Parameters:
		opts(Array):
			bufferManager(BufferManager): object that manages output buffers
			pathManager(PathHelper): object that manages paths
	*/
	public function __construct($opts = Array()){
		$this->bufferManager =
			(isset($opts['bufferManager']))
			? $opts['bufferManager']
			: new BufferManager()
		;
		$this->pathManager =
			(isset($opts['pathManager']))
			? $opts['pathManager']
			: new PathHelper()
		;
	}

	/*=====
	==render
	=====*/

	/*
	Property: renderStack
	Stack of templates currently being rendered.  Used by renderParent to be able to render the parent of the currently rendering template.
	*/
	protected $renderStack = Array();

	/*
	Method: getTemplateContent
	Render a template into a string.
	Parameters:
		templateFile(String): file name of template to use, path relative to theme directory or absolute.
		data(Array): data to pass to template
	Return:
		(String|null): Rendered content of template, or null if it doesn't exist
	*/
	public function getTemplateContent($templateFile, $data = Array()){
		$templatePath = $this->pathManager->getThemeFilePath($templateFile);
		if($templatePath){
			$this->bufferManager->start();
			//--ensure same interface when loading skeleton through this function as not
			extract($GLOBALS);

			//--make data available directly to template
			extract($data);

			require($templatePath);

			return $this->bufferManager->end();
		}else{
			return null;
		}
	}

	/*
	Method: render
	Virtual alias for $this->renderTemplate(), with a default template of skeleton.php
	Parameters:
		templateFile(String): {see $this->outputTemplate()}
		data(Array): {see $this->outputTemplate()}
	*/
	public function render($templateFile = 'skeleton.php', $data = Array()){
		return $this->renderTemplate($templateFile, $data);
	}

	/*
	Method: renderParent
	Renders the parent theme template file.  Useful if a child theme overrides a template but you still want to be able to render the parent version, such as from within the child template.
	Parameters:
		templateFile(String): {see $this->outputTemplate()}
		data(Array): {see $this->outputTemplate()}
	*/
	public function renderParent($templateFile = null, $data = null){
		if($templateFile === null){
			$stackData = $this->renderStack[count($this->renderStack) - 1];
			$templateFile = $stackData['template'];
			$data = $stackData['data'];
		}
		if($data === null){
			$data = Array();
		}
		$templatePath = $this->pathManager->getParentThemeFilePath($templateFile);
		return $this->renderTemplate($templatePath, $data);
	}

	/*
	Method: renderPiece
	Virtual alias for $this->renderTemplate(), but setting template file to be in pieces folder
	Parameters:
		templateName(String): Name of piece, a file in the pieces directory minus the '.php' from the name
		data(Array): {see $this->outputTemplate()}
	*/
	public function renderPiece($templateName = 'skeleton.php', $data = Array()){
		$templatePath = substr($this->pathManager->getRelativePath($templateName, 'pieces', 'php'), 1);
		return $this->renderTemplate($templatePath, $data);
	}

	/*
	Method: renderTemplate
	Renders a template
	Parameters:
		templateFile(String): {see $this->outputTemplate()}
		data(Array): {see $this->outputTemplate()}
	*/
	public function renderTemplate($templateFile, $data = Array()){
		$this->renderStack[] = Array(
			'template'=> $templateFile
			,'data'=> $data
		);
		$return = $this->getTemplateContent($templateFile, $data);
		array_pop($this->renderStack);
		return $return;
	}

	/*
	Method: outputCommentPiece
	Special version of $this->renderPiece to be passed as a callback to Wordpress's 'wp_list_comments' function.
	See: http://codex.wordpress.org/Function_Reference/wp_list_comments
	Parameters:
		comment(Comment): comment object holding comment data
		args(Array): arguments passed to wp_list_comments
		depth(Integer): how deep to the comment is in nesting
	*/
	public function outputCommentPiece($comment, $args, $depth){
		$data = Array(
			'args'=> $args
			,'comment'=> $comment
			,'depth'=> $depth
		);
		$output = $this->renderPiece('comment', $data);
		echo $output;
	}
}
