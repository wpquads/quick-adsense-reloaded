<?php
namespace quads;

use quads;

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
        return ( $this->renderFile ) ? quads_local_file_get_contents( $this->renderFile ) : $this->prepareOutput();
    }
}