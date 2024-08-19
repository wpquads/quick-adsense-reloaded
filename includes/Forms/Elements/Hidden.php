<?php
namespace wpquads;


use wpquads\ElementsWithOptions;

/**
 * Class Hidden
 * @package WPStaging\Forms\Elements
 */
class Hidden extends Elements
{

    /**
     * @return string
     */
    protected function prepareOutput()
    {
        return "<input id='{$this->getId()}' name='{$this->getName()}' type='hidden' {$this->prepareAttributes()} value='{$this->default}' />";
    }

    /**
     * @return string
     */
    public function render()
    {
        return ( $this->renderFile ) ? quads_local_file_get_contents( $this->renderFile ) : $this->prepareOutput();
    }
}