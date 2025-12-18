<?php
namespace quads;


use quads\ElementsWithOptions;

/**
 * Class Radio
 * @package WPStaging\Forms\Elements
 */
//class Radio extends Elements
class Radio extends ElementsWithOptions
{

    /**
     * @return string
     */
    protected function prepareOutput()
    {
        $output = '';

        foreach ($this->options as $id => $value)
        {
            $checked = ($this->default && $this->default === $value) ? " checked=''" : '';

            $attributeId = $this->getId($id);
            
            $sanitize = str_replace (array('][', '[', ']'), '-', $this->getId()) ;

            $output .= "<input type='radio' name='{$this->getId()}' id='{$sanitize}{$attributeId}' value='{$id}' {$checked}/>";
            $output .= "<label for='{$sanitize}{$attributeId}' id='{$sanitize}{$attributeId}-label'>{$value}</label>";
        }

        return $output;
    }

    /**
     * @return string
     */
    public function render()
    {
        return ( $this->renderFile ) ? quads_local_file_get_contents( $this->renderFile ) : $this->prepareOutput();
    }
}