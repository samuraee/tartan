<?php

namespace Tartan\Models;

use ArrayAccess;
//use Tartan\Exception;

class generic implements ArrayAccess
{

	var $data;

	//constructor
	function __construct ($array)
	{
		$this->load($array);
	}

	// gets a value
	function __get ($var)
	{
		return $this->data[$var];
	}
	// sets a key => value
	function __set ($key, $value)
	{
		$this->data[$key] = $value;
	}

	function __isset($key) {
		return isset($this->data[$key]);
	}

	function __unset ($key)
	{
		unset($this->data[$key]);
	}

	// loads a key => value array into the class
	function load ($array)
	{
		if (is_array($array) || ($array instanceof \stdClass)) {
			foreach ($array as $key => $value) {
				$this->data[$key] = $value;
			}
		}
	}

	// empties a specified setting or all of them
	function unload ($data = '')
	{
		if ($data) {
			if (is_array($data)) {
				foreach ($data as $var) {
					unset($this->data[$var]);
				}
			}
			else {
				unset($this->data[$data]);
			}
		}
		else {
			$this->data = array();
		}
	}

	/* return the object as an array */
	function get_all ()
	{
		return $this->data;
	}

	public function offsetExists($offset)
	{
        return isset($this->data[$offset]);
    }

	public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

	public function offsetUnset($offset)
	{
        unset($this->data[$offset]);
    }

	public function offsetGet($offset)
	{
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }
}