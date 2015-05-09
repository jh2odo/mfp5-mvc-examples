<?php

/**
 * Parses the XML Declaration
 *
 * @package SimplePie
 */
class SimplePie_XML_Declaration_Parser
{
    /**
     * XML Version
     *
     * @access public
     * @var string
     */
    var $version = '1.0';

    /**
     * Encoding
     *
     * @access public
     * @var string
     */
    var $encoding = 'UTF-8';

    /**
     * Standalone
     *
     * @access public
     * @var bool
     */
    var $standalone = false;

    /**
     * Current state of the state machine
     *
     * @access private
     * @var string
     */
    var $state = 'before_version_name';

    /**
     * Input data
     *
     * @access private
     * @var string
     */
    var $data = '';

    /**
     * Input data length (to avoid calling strlen() everytime this is needed)
     *
     * @access private
     * @var int
     */
    var $data_length = 0;

    /**
     * Current position of the pointer
     *
     * @var int
     * @access private
     */
    var $position = 0;

    /**
     * Create an instance of the class with the input data
     *
     * @access public
     * @param string $data Input data
     */
    function SimplePie_XML_Declaration_Parser($data)
    {
        $this->data = $data;
        $this->data_length = strlen($this->data);
    }

    /**
     * Parse the input data
     *
     * @access public
     * @return bool true on success, false on failure
     */
    function parse()
    {
        while ($this->state && $this->state !== 'emit' && $this->has_data()) {
            $state = $this->state;
            $this->$state();
        }
        $this->data = '';
        if ($this->state === 'emit') {
            return true;
        } else {
            $this->version = '';
            $this->encoding = '';
            $this->standalone = '';
            return false;
        }
    }

    /**
     * Check whether there is data beyond the pointer
     *
     * @access private
     * @return bool true if there is further data, false if not
     */
    function has_data()
    {
        return (bool)($this->position < $this->data_length);
    }

    /**
     * Advance past any whitespace
     *
     * @return int Number of whitespace characters passed
     */
    function skip_whitespace()
    {
        $whitespace = strspn($this->data, "\x09\x0A\x0D\x20", $this->position);
        $this->position += $whitespace;
        return $whitespace;
    }

    /**
     * Read value
     */
    function get_value()
    {
        $quote = substr($this->data, $this->position, 1);
        if ($quote === '"' || $quote === "'") {
            $this->position++;
            $len = strcspn($this->data, $quote, $this->position);
            if ($this->has_data()) {
                $value = substr($this->data, $this->position, $len);
                $this->position += $len + 1;
                return $value;
            }
        }
        return false;
    }

    function before_version_name()
    {
        if ($this->skip_whitespace()) {
            $this->state = 'version_name';
        } else {
            $this->state = false;
        }
    }

    function version_name()
    {
        if (substr($this->data, $this->position, 7) === 'version') {
            $this->position += 7;
            $this->skip_whitespace();
            $this->state = 'version_equals';
        } else {
            $this->state = false;
        }
    }

    function version_equals()
    {
        if (substr($this->data, $this->position, 1) === '=') {
            $this->position++;
            $this->skip_whitespace();
            $this->state = 'version_value';
        } else {
            $this->state = false;
        }
    }

    function version_value()
    {
        if ($this->version = $this->get_value()) {
            $this->skip_whitespace();
            if ($this->has_data()) {
                $this->state = 'encoding_name';
            } else {
                $this->state = 'emit';
            }
        } else {
            $this->state = 'standalone_name';
        }
    }

    function encoding_name()
    {
        if (substr($this->data, $this->position, 8) === 'encoding') {
            $this->position += 8;
            $this->skip_whitespace();
            $this->state = 'encoding_equals';
        } else {
            $this->state = false;
        }
    }

    function encoding_equals()
    {
        if (substr($this->data, $this->position, 1) === '=') {
            $this->position++;
            $this->skip_whitespace();
            $this->state = 'encoding_value';
        } else {
            $this->state = false;
        }
    }

    function encoding_value()
    {
        if ($this->encoding = $this->get_value()) {
            $this->skip_whitespace();
            if ($this->has_data()) {
                $this->state = 'standalone_name';
            } else {
                $this->state = 'emit';
            }
        } else {
            $this->state = false;
        }
    }

    function standalone_name()
    {
        if (substr($this->data, $this->position, 10) === 'standalone') {
            $this->position += 10;
            $this->skip_whitespace();
            $this->state = 'standalone_equals';
        } else {
            $this->state = false;
        }
    }

    function standalone_equals()
    {
        if (substr($this->data, $this->position, 1) === '=') {
            $this->position++;
            $this->skip_whitespace();
            $this->state = 'standalone_value';
        } else {
            $this->state = false;
        }
    }

    function standalone_value()
    {
        if ($standalone = $this->get_value()) {
            switch ($standalone) {
                case 'yes':
                    $this->standalone = true;
                    break;

                case 'no':
                    $this->standalone = false;
                    break;

                default:
                    $this->state = false;
                    return;
            }

            $this->skip_whitespace();
            if ($this->has_data()) {
                $this->state = false;
            } else {
                $this->state = 'emit';
            }
        } else {
            $this->state = false;
        }
    }
}

?>