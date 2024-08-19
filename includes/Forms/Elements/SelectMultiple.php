<?php
namespace wpquads;


/**
 * Class Select
 * @package WPStaging\Forms\Elements
 */
class SelectMultiple extends ElementsWithOptions
{

    /**
     * @return string
     */
    protected function prepareOutput()
    {
        $output = "<select multiple id='{$this->getId()}' name='{$this->name}[]' {$this->prepareAttributes()}>";

            foreach ($this->options as $id => $value)
            {
                $selected = ($this->isSelected($id)) ? " selected=''" : '';

                $output .= "<option value='{$id}'{$selected}>{$value}</option>";
            }

        $output.= "</select>";

        return $output;
    }

    /**
     * @param string $value
     * @return bool
     */
    private function isSelected($value)
    {
        if (
            $this->default &&
            (
                (is_string($this->default) && $this->default === $value) ||
                (is_array($this->default) && in_array($value, $this->default))
            )
        )
        {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function render()
    {
        return ( $this->renderFile ) ? quads_local_file_get_contents( $this->renderFile ) : $this->prepareOutput();
    }
}