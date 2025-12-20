<?php
namespace quads;

/**
 * Class Numerical
 * @package WPStaging\Forms\Elements
 */
class Numerical extends Elements
{

    /**
     * @return string
     */
    protected function prepareOutput()
    {
        return "<input id='{$this->getId()}' name='{$this->getName()}' type='number' {$this->prepareAttributes()} value='{$this->default}' />";
    }

    /**
     * @return string
     */
    public function render()
    {
        return ( $this->renderFile ) ? quads_local_file_get_contents( $this->renderFile ) : $this->prepareOutput();
    }
}