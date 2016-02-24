<?php
namespace Tartan\Payment\Adapter;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

abstract class AdapterAbstract
{
	const BEFORE_CALL = 'in';

	const AFTER_CALL = 'out';

	protected $_END_POINT = null;

	protected $_MOBILE_END_POINT = null;

	protected $_WSDL = null;

	protected $_SECURE_WSDL = null;

	protected $_TEST_WSDL = null;

	protected $_TEST_END_POINT = null;

	protected $_TEST_MOBILE_END_POINT = null;

	protected $_config = array();

	public $reverseSupport = false;

	public $validateReturnsAmount = false;

	public function __construct ($config)
	{
		$this->setOptions($config);
		$this->init();
	}

	public function __set ($key, $val)
	{
		$key = strtolower($key);
		$this->setOptions(array($key => $val));
	}

	public function __get ($key)
	{
		return isset($this->_config[$key]) ? $this->_config[$key] : null;
	}

	public function __call ($name, array $arguments = [])
	{
		$arguments ['caller_ips'] = self::getClientIpAddress();

		$this->_log($name, $arguments, 'info');
		call_user_func_array([$this, 'setOptions'], $arguments);
		$exception = false;
		$return    = null;

		try {
			$return = call_user_func_array([$this, 'do' . $name], []);
		} catch (Exception $e) {
			$this->_log($name, [
				'message' => $e->getMessage(),
				'code'    => $e->getCode(),
				'file'    => $e->getFile() . ':' . $e->getLine()
			],'critical');
			$exception = true;
		}
		if (!$exception) {
			if (!is_array($return)) {
				$this->_log($name, ['response' => $return]);
			} else {
				$this->_log($name, $return);
			}
		}

		return $return;
	}

	protected function _log ($message, $arguments = [], $logLevel = 'debug')
	{
		if (!is_array($arguments)) {
			$arguments = (array) $arguments;
		}

		$arguments ['tag'] = str_replace('\\', '_' , get_class($this));

		Log::$logLevel($message, $arguments);
	}

	protected function _checkRequiredOptions (array $options)
	{
		foreach ($options as $option) {
			if (!array_key_exists($option, $this->_config)) {
				throw new Exception(
					"Configuration array must have a key for '$option'"
				);
			}
		}
	}

	public function setOptions (array $options = [])
	{
		foreach ($options as $key => $value) {
			$key                 = strtolower($key);
			$this->_config[$key] = $value;
		}
	}

	public function getOptions ()
	{
		return $this->_config;
	}

	public function getWSDL ($secure = false)
	{
		if (config('app.env') == 'production')
		{
			if ($secure == true) {
				return $this->_SECURE_WSDL;
			}
			else {
				return $this->_WSDL;
			}
		}
		else {
			return $this->_TEST_WSDL;
		}
	}

	public function getEndPoint ($mobile = false)
	{
		if (config('app.env') == 'production')
		{
			if ($mobile == true) {
				return $this->_MOBILE_END_POINT;
			}
			else {
				return $this->_END_POINT;
			}
		}
		else {
			if ($mobile == true) {
				return $this->_TEST_MOBILE_END_POINT;
			}
			else {
				return $this->_TEST_END_POINT;
			}
		}
	}

	public static function getClientIpAddress() {
		$ipAddress = '';
		if ($_SERVER['HTTP_CLIENT_IP'])
			$ipAddress = $_SERVER['HTTP_CLIENT_IP'];
		else if($_SERVER['HTTP_X_FORWARDED_FOR'])
			$ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if($_SERVER['HTTP_X_FORWARDED'])
			$ipAddress = $_SERVER['HTTP_X_FORWARDED'];
		else if($_SERVER['HTTP_FORWARDED_FOR'])
			$ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if($_SERVER['HTTP_FORWARDED'])
			$ipAddress = $_SERVER['HTTP_FORWARDED'];
		else if($_SERVER['REMOTE_ADDR'])
			$ipAddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipAddress = 'UNKNOWN';

		return $ipAddress;
	}

	public function init () {}

	abstract public function getInvoiceId ();

	abstract public function getReferenceId ();

	abstract public function getStatus ();

	abstract public function doGenerateForm (array $options = []);

	abstract public function doVerifyTransaction (array $options = []);

	abstract public function doReverseTransaction (array $options = []);
}
