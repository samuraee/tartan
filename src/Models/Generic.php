<?php

namespace Tartan\Models;

use ArrayAccess;
//use Tartan\Exception;

class generic implements ArrayAccess
{

	var $vars;

	//constructor
	function __construct ($array)
	{
		if (is_array($array) || ($array instanceof \stdClass)) {
			$this->vars = $array;
		}
	}

	// gets a value
	function get ($var)
	{
		return $this->vars[$var];
	}

	// sets a key => value
	function set ($key, $value)
	{
		$this->vars[$key] = $value;
	}

	// loads a key => value array into the class
	function load ($array)
	{
		if (is_array($array) || ($array instanceof \stdClass)) {
			foreach ($array as $key => $value) {
				$this->vars[$key] = $value;
			}
		}
	}

	// empties a specified setting or all of them
	function unload ($vars = '')
	{
		if ($vars) {
			if (is_array($vars)) {
				foreach ($vars as $var) {
					unset($this->vars[$var]);
				}
			}
			else {
				unset($this->vars[$vars]);
			}
		}
		else {
			$this->vars = array();
		}
	}

	/* return the object as an array */
	function get_all ()
	{
		return $this->vars;
	}

	public function offsetExists($offset)
	{
        return isset($this->vars[$offset]);
    }

	public function offsetSet($offset, $value)
    {
        $this->vars[$offset] = $value;
    }

	public function offsetUnset($offset)
	{
        unset($this->vars[$offset]);
    }

	public function offsetGet($offset)
	{
        return isset($this->vars[$offset]) ? $this->vars[$offset] : null;
    }
}