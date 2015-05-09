<?php

class SimplePie_Restriction
{
    var $relationship;
    var $type;
    var $value;

    // Constructor, used to input the data
    function SimplePie_Restriction($relationship = null, $type = null, $value = null)
    {
        $this->relationship = $relationship;
        $this->type = $type;
        $this->value = $value;
    }

    function __toString()
    {
        // There is no $this->data here
        return md5(serialize($this));
    }

    function get_relationship()
    {
        if ($this->relationship !== null) {
            return $this->relationship;
        } else {
            return null;
        }
    }

    function get_type()
    {
        if ($this->type !== null) {
            return $this->type;
        } else {
            return null;
        }
    }

    function get_value()
    {
        if ($this->value !== null) {
            return $this->value;
        } else {
            return null;
        }
    }
}

?>