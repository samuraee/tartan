<?php

namespace Tartan\Validators;

class CustomValidator
{

	public function validateStrength($attribute, $value, $parameters, $validator)
	{
		if( preg_match('/(?=^.{8,}$)(?=.*\d)(?=.*[!@#$%^&*]+)(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/', $value) )
			return true;

		return false;
	}

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

}