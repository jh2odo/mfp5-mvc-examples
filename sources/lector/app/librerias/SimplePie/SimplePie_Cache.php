<?php

class SimplePie_Cache
{
    /**
     * Don't call the constructor. Please.
     *
     * @access private
     */
    function SimplePie_Cache()
    {
        trigger_error('Please call SimplePie_Cache::create() instead of the constructor', E_USER_ERROR);
    }

    /**
     * Create a new SimplePie_Cache object
     *
     * @static
     * @access public
     */
    function create($location, $filename, $extension)
    {
        return new SimplePie_Cache_File($location, $filename, $extension);
    }
}

?>