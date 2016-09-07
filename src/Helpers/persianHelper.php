<?php

if (! function_exists('random_string')) {

	function random_string($length, $type = '') {
		// Select which type of characters you want in your random string
		switch($type) {
			case 'num':
				// Use only numbers
				$salt = '1234567890';
				break;
			case 'lower':
				// Use only lowercase letters
				$salt = 'abcdefghijklmnopqrstuvwxyz';
				break;
			case 'upper':
				// Use only uppercase letters
				$salt = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
			default:
				// Use uppercase, lowercase, numbers, and symbols
				$salt = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
				break;
		}
		$rand = '';
		$i = 0;
		while ($i < $length) { // Loop until you have met the length
			$num = rand() % strlen($salt);
			$tmp = substr($salt, $num, 1);
			$rand = $rand . $tmp;
			$i++;
		}
		return $rand; // Return the random string
	}
}

if (! function_exists('persian')) {
	function persian ($string, $digits = true)
	{
		$farsiArray      = array("۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹");
		$arabicArray     = array("٠", "١", "٢", "٣", "٤", "٥", "٦", "٧", "٨", "٩");
		$englishArray    = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");

		$nonPersianArray = array("ى", "ي", "ك", "ئ", "إ", "أ", "ٱ", "ة", "ؤ", "ء");
		$persianArray    = array("ی", "ی", "ک", "ی", "ا", "ا", "ﺍ", "ه", "و", "");

		if ($digits) {
			$string = str_replace($englishArray, $farsiArray, $string);
			$string = str_replace($arabicArray, $farsiArray, $string);
		}
		$string = str_replace($nonPersianArray, $persianArray, $string);

		return $string;
	}
}

if (! function_exists('english')) {
	function english ($string)
	{
		$farsiArray      = array("۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹");
		$arabicArray     = array("٠", "١", "٢", "٣", "٤", "٥", "٦", "٧", "٨", "٩");
		$englishArray    = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");

		$string = str_replace($farsiArray, $englishArray, $string);
		$string = str_replace($arabicArray, $englishArray, $string);

		return $string;
	}
}


if (! function_exists('englishFilter')) {
	function englishFilter (array $object)
	{
		foreach ($object as $key => $val) {
			$object [$key] = english ($val);
		}

		return $object;
	}
}

if (! function_exists('persianMoney')) {
	function persianMoney ($string, $currency = false, $locale = 'fa')
	{
		$money = ($locale == 'fa') ? persian(number_format($string)) : number_format($string);

		return $money . ($currency ? ' ریال' : '');
	}
}