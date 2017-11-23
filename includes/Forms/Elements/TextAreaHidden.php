<?php
namespace wpquads;

use wpquads;

/**
 * Class TextArea
 * @package WPStaging\Forms\Elements
 */
class TextAreaHidden extends Elements
{

    /**
     * @return string
     */
    protected function prepareOutput()
    {
        return "<textarea style='display:none;' id='{$this->getId()}' name='{$this->getName()}' {$this->prepareAttributes()}>{$this->default}</textarea>";
    }

    /**
     * @return string
     */
    public function render()
    {
        return ($this->renderFile) ? @file_get_contents($this->renderFile) : $this->prepareOutput();
    }
}