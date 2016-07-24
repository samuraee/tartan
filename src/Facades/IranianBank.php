<?php

namespace Tartan\Facades;
use Illuminate\Support\Facades\Facade;

/**
 * Class IranBank
 * @package Tartan\Facades
 * @author Tartan <iamtartan@gmail.com>
 */
class IranianBank extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'Tartan\Helpers\IranianBankHelper';
	}
}