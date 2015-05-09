<?php

class SimplePie_Cache_File
{
    var $location;
    var $filename;
    var $extension;
    var $name;

    function SimplePie_Cache_File($location, $filename, $extension)
    {
        $this->location = $location;
        $this->filename = rawurlencode($filename);
        $this->extension = rawurlencode($extension);
        $this->name = "$location/$this->filename.$this->extension";
    }

    function save($data)
    {
        if (file_exists($this->name) && is_writeable($this->name) || file_exists($this->location) && is_writeable($this->location)) {
            if (is_a($data, 'SimplePie')) {
                $data = $data->data;
            }

            $data = serialize($data);

            if (function_exists('file_put_contents')) {
                return (bool)file_put_contents($this->name, $data);
            } else {
                $fp = fopen($this->name, 'wb');
                if ($fp) {
                    fwrite($fp, $data);
                    fclose($fp);
                    return true;
                }
            }
        }
        return false;
    }

    function load()
    {
        if (file_exists($this->name) && is_readable($this->name)) {
            return unserialize(file_get_contents($this->name));
        }
        return false;
    }

    function mtime()
    {
        if (file_exists($this->name)) {
            return filemtime($this->name);
        }
        return false;
    }

    function touch()
    {
        if (file_exists($this->name)) {
            return touch($this->name);
        }
        return false;
    }

    function unlink()
    {
        if (file_exists($this->name)) {
            return unlink($this->name);
        }
        return false;
    }
}

?>