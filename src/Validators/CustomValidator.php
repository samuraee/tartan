<?php

namespace Tartan\Validators;

class CustomValidator
{

	/**
	 * Validate Password Strength level
	 * @param $attribute
	 * @param $value
	 * @param $parameters
	 * @param $validator
	 *
	 * @return bool
	 */
	public function validateStrength($attribute, $value, $parameters, $validator)
	{
		if( preg_match('/(?=^.{8,}$)(?=.*\d)(?=.*[!@#$%^&*]+)(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/', $value) )
			return true;

		return false;
	}

	/**
	 * Validate Iran Billing Ids
	 * @param $attribute
	 * @param $value
	 * @param $parameters
	 * @param $validator
	 *
	 * @return bool
	 */
	public function validateIranBillingId($attribute, $value, $parameters, $validator)
	{
		$factor = 2;
		$computedCheckDigit = 0;
		$code = $value;
		$givenCheckDigit = substr($code,-1);

		for ($i = strlen($code) - 2 ; $i >= 0; --$i,++$digit)
		{
			$digit = $code[$i];
			$computedCheckDigit += $digit * $factor;
			$factor = ($factor == 7) ? 2 : ++$factor;
		}

		$computedCheckDigit %= 11;
		$computedCheckDigit = ($computedCheckDigit <= 1) ? 0 : 11 - $computedCheckDigit;
		return ($computedCheckDigit == $givenCheckDigit);
	}

	/**
	 * Validate Iran Shetab Card Numbers
	 * @param $attribute
	 * @param $value
	 * @param $parameters
	 * @param $validator
	 *
	 * @return bool
	 */
	public function validateShetabCard($attribute, $value, $parameters, $validator)
	{
		if(empty($value) || !is_numeric($value))
		{
			return false;
		}

		settype($value, 'string');

		if (preg_match('/^(627353|505801)/', $value)) {
			if (strlen($value) != 16) {
				return false;
			}
		}
		else {
			$sumTable = array(
				array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9),
				array(0, 2, 4, 6, 8, 1, 3, 5, 7, 9));
			$sum      = 0;
			$flip     = 0;

			for ($i = strlen($value) - 1; $i >= 0; $i--) {
				$sum += $sumTable[$flip++ & 0x1][$value[$i]];
			}

			if (!($sum % 10 === 0)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Validate UUID format
	 * @param $attribute
	 * @param $value
	 * @param $parameters
	 * @param $validator
	 *
	 * @return bool
	 */
	public function validateUuid($attribute, $value, $parameters, $validator)
	{
		return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $value) === 1;
	}

	/**
	 * validating Iranian national id code
	 * @reference http://www.aliarash.com/article/codemeli/codemeli.htm
	 * @param $attribute
	 * @param $value
	 * @param $parameters
	 * @param $validator
	 *
	 * @return bool
	 */
	public function validateNationalId ($attribute, $value, $parameters, $validator)
	{
		if(empty($value) || !is_numeric($value))
		{
			return false;
		}

		$value = str_pad($value, 10, '0', STR_PAD_LEFT); // pad to 10 digits

		$value = str_split($value);

		$sum = 0;
		for ($i=0; $i<=8; $i++) {
			$sum += (10-$i) * $value[$i];
		}

		$m = $sum%11;

		if ($m <= 2) {
			return $m == $value[9];
		} else {
			return (11-$m) == $value[9];
		}
	}
}