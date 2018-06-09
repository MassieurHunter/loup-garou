<?php

class Themes
{
    protected $arrThemes = [];

    public function initThemes()
    {

        if ($cssHandle = opendir(CSS_PATH)) {
        	
            while (false !== ($line = readdir($cssHandle))) {
            	
                if ($line !== '.' && $line !== '..' && is_dir(CSS_PATH . $line)) {
                	
                    $this->arrThemes[$line] = ucwords($line);
                    
                }
            }
            
            asort($this->arrThemes);

        }
    }

    public function getThemes(): array
    {
        if (empty($this->arrThemes)) {
            $this->initThemes();
        }

        return $this->arrThemes;
    }

}