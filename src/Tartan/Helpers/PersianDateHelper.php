<?php

namespace Tartan\Helpers;

use Tartan\IntlDatetime;

class PersianDateHelper
{
	/**
	 * Gregorian to Persian
	 * @param $date
	 * @param string $format
	 *
	 * @return string
	 */
	public function gTop($date, $format = 'yyyy/MM/dd H:m:s')
	{
		$date = new IntlDatetime($date);
		$date->setCalendar('persian');
		$date->setLocale('fa');
		return $date->format($format);
	}

	/**
	 * Persian to Gregorian
	 * @param $date
	 * @param string $format
	 * @param string $inputLocale
	 *
	 * @return string
	 */
	public function pTog($date, $format = 'yyyy/MM/dd H:m:s', $inputLocale = 'fa')
	{
		$date = new IntlDatetime($date, 'Asia/Tehran', 'persian', $inputLocale);

		$date->setCalendar('Gregorian');
		$date->setLocale('en');
		return $date->format($format);
	}
}