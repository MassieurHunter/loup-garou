<?php

/**
 * HomeMade Template engine
 * Very light, you can only make echo / conditionnal structure or foreach loop
 *
 * @author Mâssieur Hunter
 */
class Template
{

	const PATTERN_ECHO				 = '#\{((\$[a-z_][a-z0-9_]*)(\[\s*\$*[\'"]*[a-z0-9_]*[\'"]*\s*\])*)\}#si';
	const REPLACE_ECHO				 = '<?php echo isset( $1 ) ? $1 : "";?>';
	const PATTERN_SVG				 = '#\{svg\(([a-z0-9 _.$-]+)(\,\s*([a-z0-9 _.-]+))?\)\}#si';
	const REPLACE_SVG				 = '<?php echo getSVG("$1", "$3"); ?>';
	const PATTERN_SET_VAR				 = '#\{((\$[a-z_][a-z0-9_]*)(\[\s*\$*[\'"]*[a-z0-9_]*[\'"]*\s*\])*)\s*=\s*(([\'"]*[a-z0-9_\/-]*[\'"]*)|(((\$[a-z_][a-z0-9_]*)(\[\s*\$*[\'"]*[a-z0-9_]*[\'"]*\s*\])*)))\s*([0-9+*\/ -]*\s*((\$[a-z_][a-z0-9_]*)(\[\s*\$*[\'"]*[a-z0-9_]*[\'"]*\s*\])*)?)\s*\}#si';
	const REPLACE_SET_VAR				 = '<?php $1 = $4 $10; ?>';
	const PATTERN_INCREMENT			 = '#\{((\$[a-z_][a-z0-9_]*)(\[\s*\$*[\'"]*[a-z0-9_]*[\'"]*\s*\])*)\s*([+-]{2})\s*\}#si';
	const REPLACE_INCREMENT			 = '<?php if(isset($1)){$1$4;} ?>';
	const PATTERN_IF					 = '#\{if\s*((\!)*(\$[a-z_][a-z0-9_]*)((\[\s*\$*[\'"]*[a-z0-9_]*[\'"]*\s*\])*))\s*(((&&|\|\||-|\+|\/|\*|%)?\s*(((\!)*(\$?[a-z_]?[a-z0-9_]*)(\[\s*\$*[\'"]*[a-z0-9_]*[\'"]*\s*\])*)\s*)*|((==|===|<|>|<=|>=|\!=|\!==)\s*(([\'"]*[a-z0-9_\/+ \#:-]*[\'"]*)|(((\$[a-z_][a-z0-9_]*)(\[\s*\$*[\'"]*[a-z0-9_]*[\'"]*\s*\])*))))*)*)\}#si';
	const REPLACE_IF					 = '<?php if(isset($3$4) && ($1 $6)){ ?>';
	const PATTERN_ELSEIF				 = '#\{elseif\s*((\!)*(\$[a-z_][a-z0-9_]*)((\[\s*\$*[\'"]*[a-z0-9_]*[\'"]*\s*\])*))\s*(((&&|\|\||-|\+|\/|\*|%)?\s*(((\!)*(\$?[a-z_]?[a-z0-9_]*)(\[\s*\$*[\'"]*[a-z0-9_]*[\'"]*\s*\])*)\s*)*|((==|===|<|>|<=|>=|\!=|\!==)\s*(([\'"]*[a-z0-9_\/+ \#:-]*[\'"]*)|(((\$[a-z_][a-z0-9_]*)(\[\s*\$*[\'"]*[a-z0-9_]*[\'"]*\s*\])*))))*)*)\}#si';
	const REPLACE_ELSEIF				 = '<?php }elseif(isset($3$4) && ($1 $6)){ ?>';
	const PATTERN_ELSE				 = '#\{else\}#si';
	const REPLACE_ELSE				 = '<?php }else{ ?>';
	const PATTERN_END_IF				 = '#\{/if\}#si';
	const REPLACE_END_IF				 = '<?php } ?>';
	const PATTERN_FOREACH				 = '#\{foreach\s*(\$[a-z_][a-z0-9_]*)((\[\s*\$*[\'"]*[a-z0-9_]*[\'"]*\s*\])*)\s*as\s*(\$[a-z_][a-z0-9_]+)\s*(=>)*\s*(\$[a-z_][a-z0-9_]+)*\}#si';
	const REPLACE_FOREACH				 = '<?php if(isset($1$2) && is_array($1$2)){foreach($1$2 as $4 $5 $6){ ?>';
	const PATTERN_END_FOREACH			 = '#\{/foreach\}#si';
	const REPLACE_END_FOREACH			 = '<?php }}?>';
	const PATTERN_FOR					 = '#\{for\s+(\$[a-z_][a-z0-9_]*)\s*=\s*(\$?[a-z0-9_][a-z0-9_]*)\s*;\s*(\$[a-z_][a-z0-9_]*+)\s*([<>=]{1,2})\s*(\$?[a-z0-9_][a-z0-9_]*)\s*;\s*(\$[a-z_][a-z0-9_]*)([+-]{2})\}#si';
	const REPLACE_FOR					 = '<?php for($1=$2;$3$4$5;$6$7){ ?>';
	const PATTERN_END_FOR				 = '#\{/for\}#si';
	const REPLACE_END_FOR				 = '<?php }?>';
	const PATTERN_WHILE					 = '#\{while\s*((\!)*(\$[a-z_][a-z0-9_]*)((\[\s*\$*[\'"]*[a-z0-9_]*[\'"]*\s*\])*))\s*(((&&|\|\||-|\+|\/|\*|%)?\s*(((\!)*(\$?[a-z_]?[a-z0-9_]*)(\[\s*\$*[\'"]*[a-z0-9_]*[\'"]*\s*\])*)\s*)*|((==|===|<|>|<=|>=|\!=|\!==)\s*(([\'"]*[a-z0-9_\/-]*[\'"]*)|(((\$[a-z_][a-z0-9_]*)(\[\s*\$*[\'"]*[a-z0-9_]*[\'"]*\s*\])*))))*)*)\}#si';
	const REPLACE_WHILE					 = '<?php while(isset($3$4) && ($1 $6)){ ?>';
	const PATTERN_END_WHILE				 = '#\{/while\}#si';
	const REPLACE_END_WHILE				 = '<?php } ?>';
	const PATTERN_PHP_TAG_SPACE		 = '#((\t|\r|\n)+)<\?php#';
	const REPLACE_PHP_TAG_SPACE		 = ' <?php';
	const PATTERN_FIN_PHP_TAG_SPACE	 = '#\?>((\t|\r|\n)+)#';
	const REPLACE_FIN_PHP_TAG_SPACE	 = '?> ';
	const PATTERN_PHP_TAG_MERGE		 = '#\?>\s+<\?php#';
	const REPLACE_PHP_TAG_MERGE		 = ' echo " "; ';
	const PATTERN_COMMENT_START		 = '#\{\*#si';
	const REPLACE_COMMENT_START		 = '<?php /*';
	const PATTERN_COMMENT_END			 = '#\*\}#si';
	const REPLACE_COMMENT_END			 = '*/ ?>';
	const PATERN_INDENTATION		 = '#(\r|\n)(\t)+#';
	const REPLACE_INDENTATION		 = '$1';
	const PATERN_TAG_SPACE		 = '#((\r|\n)+)<#';
	const REPLACE_TAG_SPACE		 = '<';
	const PATERN_TAG_SPACE_2		 = '#>\s*<#';
	const REPLACE_TAG_SPACE_2		 = '><';

	/**
	 * Dir to templates folder
	 *
	 * @var string
	 */
	private $templatesDir = '';

	/**
	 * Dir to cache
	 *
	 * @var string
	 */
	private $cacheDir = '';

	/**
	 * Template file location
	 * relative to template dir
	 *
	 * @var string
	 */
	private $filePath = '';

	/**
	 * template filename
	 *
	 * @var string
	 */
	private $fileName = '';

	/**
	 * Template File Extension
	 *
	 * @var string
	 */
	private $templateFileExtensions = '.tpl';

	/**
	 * Patterns arrray
	 *
	 * @var array
	 */
	private $arrPattern = array();

	/**
	 * Replaces array
	 *
	 * @var array
	 */
	private $arrReplace = array();

	/**
	 * Array containing current template variables
	 *
	 * @var array
	 */
	private $arrVars = array();

	public function __construct() {
		$this->templatesDir	 = VIEWPATH;
		$this->cacheDir		 = VIEWPATH . 'cache/';

		$this->arrPattern	 = array(
			self::PATTERN_ECHO,
			self::PATTERN_SVG,
			self::PATTERN_SET_VAR,
			self::PATTERN_INCREMENT,
			self::PATTERN_IF,
			self::PATTERN_ELSEIF,
			self::PATTERN_ELSE,
			self::PATTERN_END_IF,
			self::PATTERN_FOREACH,
			self::PATTERN_END_FOREACH,
			self::PATTERN_FOR,
			self::PATTERN_END_FOR,
			self::PATTERN_WHILE,
			self::PATTERN_END_WHILE,
//			self::PATTERN_PHP_TAG_SPACE,
//			self::PATTERN_FIN_PHP_TAG_SPACE,
//			self::PATTERN_PHP_TAG_MERGE,
			self::PATTERN_COMMENT_START,
			self::PATTERN_COMMENT_END,
//			self::PATERN_INDENTATION,
//			self::PATERN_TAG_SPACE,
//			self::PATERN_TAG_SPACE_2,
		);

		$this->arrReplace	 = array(
			self::REPLACE_ECHO,
			self::REPLACE_SVG,
			self::REPLACE_SET_VAR,
			self::REPLACE_INCREMENT,
			self::REPLACE_IF,
			self::REPLACE_ELSEIF,
			self::REPLACE_ELSE,
			self::REPLACE_END_IF,
			self::REPLACE_FOREACH,
			self::REPLACE_END_FOREACH,
			self::REPLACE_FOR,
			self::REPLACE_END_FOR,
			self::REPLACE_WHILE,
			self::REPLACE_END_WHILE,
//			self::REPLACE_PHP_TAG_SPACE,
//			self::REPLACE_FIN_PHP_TAG_SPACE,
//			self::REPLACE_PHP_TAG_MERGE,
			self::REPLACE_COMMENT_START,
			self::REPLACE_COMMENT_END,
//			self::REPLACE_INDENTATION,
//			self::REPLACE_TAG_SPACE,
//			self::REPLACE_TAG_SPACE_2,
		);
	}

	/*
	 *
	 * Getters
	 *
	 */

	/**
	 * Templates path getter
	 *
	 * @return string
	 */
	private function getTemplatesDir() {
		return $this->templatesDir;
	}

	/**
	 * Cache path getter
	 *
	 * @return string
	 */
	private function getCacheDir() {
		return $this->cacheDir;
	}

	/**
	 * Template file path
	 *
	 * @return string
	 */
	private function getFilePath() {
		return $this->filePath;
	}

	/**
	 * Path to template file getter
	 *
	 * @return string
	 */
	private function getFileName() {
		return $this->fileName;
	}

	/**
	 * Path to template cachefile getter
	 *
	 * @return string
	 */
	private function getCacheFile() {
		return $this->getCacheDir() . $this->getFileName() . '_' . substr(md5($this->getFilePath()), 5, 5) . '.php';
	}

	/**
	 * Returns patterns array
	 *
	 * @return array
	 */
	private function getPattern() {
		return $this->arrPattern;
	}

	/**
	 * Returns replaces array
	 * @return array
	 */
	private function getReplace() {
		return $this->arrReplace;
	}

	/**
	 * Returns all the template's variables
	 *
	 * @return array
	 */
	private function getAllVars() {
		return $this->arrVars;
	}

	/**
	 * Return a specific var
	 *
	 * @param string $name
	 * @return mixed
	 */
	private function getVar($name) {
		return $this->arrVars[$name];
	}

	/**
	 *
	 * @return string
	 */
	private function getTemplateFileExtentions() {
		return $this->templateFileExtensions;
	}

	/**
	 *
	 * @return string
	 */
	public function setTemplateFileExtentions($extension) {
		return $this->templateFileExtensions = '.' . $extension;
	}

	/**
	 * Allow us to change the template dir to check
	 *
	 * @param string $templatesDir
	 * @return \Template
	 */
	public function setTemplatesDir($templatesDir) {
		$this->templatesDir	 = $templatesDir;
		$this->cacheDir		 = $templatesDir . 'cache/';
		return $this;
	}

	/**
	 * Allow us to know if the cache file is up to date
	 *
	 * @return boolean
	 */
	public function isCacheOk() {
		return file_exists($this->getCacheFile()) && filemtime($this->getFilePath()) < filemtime($this->getCacheFile());
	}

	/**
	 * Display the parsed template
	 *
	 * @param string $file template file path (relative to templates dir)
	 * @param bool $showTemplateName Show the template name in HTML comments (default false)
	 */
	public function display($file, $showTemplateName = false) {
		$this->setFile($file);

		if (!file_exists($this->getFilePath())) {
			die($file . " template file doesn't exist");
		}

		if (!$this->isCacheOk()) {
			$this->rebuildCacheFile();
		}

		$arrVars = $this->getAllVars();
		foreach ($arrVars as $name => $value) {
			${$name} = $value;
		}

		if ($showTemplateName) {
			echo '<!-- Generated by ' . $file . ' -->';
		}

		include $this->getCacheFile();

		return $this;
	}

	/**
	 * Returns the parsed template in a php variable
	 *
	 * @param string $file template file path (relative to templates dir)
	 * @param bool $showTemplateName Show the template name in HTML comments (default false)
	 * @return string
	 */
	public function saveInVar($file, $showTemplateName = false) {
		ob_start();
		$this->display($file, $showTemplateName);

		$result = ob_get_contents();
		ob_end_clean();

		return $result;
	}

	/**
	 * Set the file path
	 *
	 * @param string $file
	 */
	public function setFile($file) {
		$this->filePath		 = $this->getTemplatesDir() . $file . $this->getTemplateFileExtentions();
		$splittedFileName	 = explode('/', $file);
		$this->fileName		 = end($splittedFileName);
	}

	/**
	 * Set a template variable
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return \Template
	 */
	public function setVar($name, $value) {
		$this->arrVars[$name] = $value;
		return $this;
	}

	/**
	 * Rebuild the cache file
	 */
	public function rebuildCacheFile() {
		$templateContent = file_get_contents($this->getFilePath());

		$cacheContent	 = preg_replace($this->getPattern(), $this->getReplace(), $templateContent);
		$fCache			 = fopen($this->getCacheFile(), 'w+');
		fwrite($fCache, $cacheContent);
		fclose($fCache);
		chmod($this->getCacheFile(), 0777);
	}

}