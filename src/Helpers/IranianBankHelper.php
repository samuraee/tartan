<?php

namespace Tartan\Helpers;

use Tartan\Exception;

class IranianBankHelper
{
	protected static $_cardBinNumber = [
		['code' => 60, 'bin' => 606373 , 'slug' => 'gharzolhasaneh_mehr', 'name' => 'قرض الحسنه مهر'],
		['code' => 54, 'bin' => 622106 , 'slug' => 'parsian', 'name' => 'پارسیان'],
		['code' => 62, 'bin' => 636214 , 'slug' => 'ayande', 'name' => 'آینده'],
		['code' => 16, 'bin' => 603770 , 'slug' => 'keshavarzi', 'name' => 'کشاورزی'],
		['code' => 16, 'bin' => 639217 , 'slug' => 'keshavarzi', 'name' => 'کشاورزی'],
		['code' => 14, 'bin' => 628023 , 'slug' => 'maskan', 'name' => 'مسکن'],
		['code' => 22, 'bin' => 502908 , 'slug' => 'tose_taavon', 'name' => 'توسعه تعاون'],
		['code' => 13, 'bin' => 589463 , 'slug' => 'refah', 'name' => 'رفاه'],
		['code' => 12, 'bin' => 610433 , 'slug' => 'mellat', 'name' => 'ملت'],
		['code' => 52, 'bin' => 621986 , 'slug' => 'saman', 'name' => 'سامان'],
		['code' => 56, 'bin' => 621986 , 'slug' => 'saman', 'name' => 'سامان'],
		['code' => 18, 'bin' => 627353 , 'slug' => 'tejarat', 'name' => 'تجارت'],
		['code' => 55, 'bin' => 627412 , 'slug' => 'eghtesad_novin', 'name' => 'اقتصاد نوین'],
		['code' => 21, 'bin' => 627760 , 'slug' => 'post', 'name' => 'پست'],
		['code' => 57, 'bin' => 639347 , 'slug' => 'pasargad', 'name' => 'پاسارگاد'],
		['code' => 57, 'bin' => 502229 , 'slug' => 'pasargad', 'name' => 'پاسارگاد'],
		['code' => 58, 'bin' => 639607 , 'slug' => 'sarmaye', 'name' => 'سرمایه'],
		['code' => 59, 'bin' => 639346 , 'slug' => 'sina', 'name' => ' سینا'],
		['code' => 63, 'bin' => 627381 , 'slug' => 'ansar', 'name' => 'انصار'],
		['code' => 19, 'bin' => 603769 , 'slug' => 'saderat', 'name' => 'صادرات'],
		['code' => 17, 'bin' => 603799 , 'slug' => 'melli', 'name' => 'ملی'],
		['code' => 20, 'bin' => 627648 , 'slug' => 'tose_saderat', 'name' => 'توسعه صادرات'],
		['code' => 11, 'bin' => 627961 , 'slug' => 'sanat_madan', 'name' => 'صنعت و معدن'],
		['code' => 53, 'bin' => 627488 , 'slug' => 'kar_afarin', 'name' => 'کار آفرین'],
		['code' => 53, 'bin' => 502910 , 'slug' => 'kar_afarin', 'name' => 'کار آفرین'],
		['code' => 15, 'bin' => 589210 , 'slug' => 'sepah', 'name' => 'سپه'],
		['code' => 51, 'bin' => 628157 , 'slug' => 'moaseseh_etebari', 'name' => 'موسسه اعتباری'],
		['code' => 61, 'bin' => 502806 , 'slug' => 'shahr', 'name' => 'شهر'],
		['code' => 61, 'bin' => 504706 , 'slug' => 'shahr', 'name' => 'شهر'],
		['code' => 66, 'bin' => 502938 , 'slug' => 'dey', 'name' => 'دی'],
		['code' => 64, 'bin' => 505416 , 'slug' => 'gardeshgari', 'name' => 'گردشگری'],
		['code' => 65, 'bin' => 636949 , 'slug' => 'hekmat_iranian', 'name' => 'حکمت ایرانیان'],
	];

	/**
	 * @var array
	 */
	protected static $_billTypes = [
		1 => ['slug' => 'ab', 'name' => 'آب'],
		2 => ['slug' => 'bargh', 'name' => 'برق'],
		3 => ['slug' => 'gaz', 'name' => 'گاز'],
		4 => ['slug' => 'tel', 'name' => 'تلفن ثابت'],
		5 => ['slug' => 'mobile' , 'name' => 'تلفن همراه'],
		6 => ['slug' => 'shahrdari', 'name' => 'شهرداری'],
		9 => ['slug' => 'rahnamai_ranandegi', 'name' => 'راهنمایی و رانندگی']
	];

	/**
	 * @param $cardNumber
	 *
	 * @return string
	 * @throws Exception
	 */
	public function cardBankName ($cardNumber)
	{
		if (!preg_match('/^\d{16}$/', $cardNumber)) {
			throw new Exception('Invalid card number format!');
		}
		$bin = substr($cardNumber, 0, 6);

		foreach (static::$_cardBinNumber as $row) {
			if ($row['bin'] == $bin) {
				return $row;
				break;
			}
		}

		throw new Exception('could not detect card`s vendor name!');
	}

	/**
	 * @param $shebaNumber
	 *
	 * @return string
	 * @throws Exception
	 * @internal param $cardNumber
	 *
	 */
	public function shebaBankName ($shebaNumber)
	{
		if (preg_match('/^ir\d{24}$/i', $shebaNumber)) {
			$code = intval(substr($shebaNumber, 4, 3));
		} else if (preg_match('/^\d{24}$/i', $shebaNumber)){
			$code = intval(substr($shebaNumber, 2, 3));
		} else {
			throw new Exception('Invalid sheba number format!');
		}

		foreach (static::$_cardBinNumber as $row) {
			if ($row['code'] == $code) {
				return $row;
				break;
			}
		}

		throw new Exception('could not detect sheba account`s vendor name!');
	}

	/**
	 * @param $account
	 * @param bool $formatted
	 *
	 * @return string
	 */
	public function generateShebaNumber ($account, $formatted = false)
	{
		$section = 98 - bcmod('62000000' . $account . '182700', 97);
		$sheba   = sprintf('IR%s%s%s', str_pad($section, 2, '0', STR_PAD_LEFT), '062000000', $account);
		if ($formatted)
			return substr(chunk_split($sheba, 4, '-'), 0, -1);
		else
			return $sheba;
	}

	/**
	 * @param $billId
	 *
	 * @return array
	 * @throws Exception
	 */
	public function getBillType ($billId)
	{
		if (isset(static::$_billTypes[substr($billId, -2, -1)])) {
			return static::$_billTypes[substr($billId, -2, -1)];
		}

		throw new Exception('could not detect bill`s vendor name!');
	}

	/**
	 * @param $billId
	 *
	 * @return string
	 */
	public function getBillID ($billId)
	{
		return substr($billId, -2, -1);
	}

	/**
	 * @param $payId
	 * @param string $billId
	 *
	 * @return bool
	 */
	public static function isValidBillInfo ($payId, $billId = "")
	{
		$factor             = 2;
		$computedCheckDigit = 0;
		$code               = $billId . $payId;
		$givenCheckDigit    = substr($code, -1);

		for ($i = strlen($code) - 2; $i >= 0; --$i, ++$digit) {
			$digit = $code[$i];
			$computedCheckDigit += $digit * $factor;
			$factor = ($factor == 7) ? 2 : ++$factor;
		}

		$computedCheckDigit %= 11;
		$computedCheckDigit = ($computedCheckDigit <= 1) ? 0 : 11 - $computedCheckDigit;

		return ($computedCheckDigit == $givenCheckDigit);
	}

	public function billInfo ($billId, $payId)
	{
		$bill                = new \stdClass();
		$bill->amount        = intval(substr($payId, 0, -5)) * 1000;
		$bill->company       = $this->getBillType($billId);
		$bill->billIdType    = $this->getBillID($billId);
		$bill->period        = intval(substr($payId, -4, -2));
		$bill->isValidBillId = $this->isValidBillInfo($billId) && !((strlen($billId) < 6) || (strlen($billId) > 13));
		$bill->isValidPayId  = $this->isValidBillInfo(substr($payId, 0, strlen($payId) - 1)) && !((strlen($payId) < 6) || (strlen($payId) > 13));
		//$bill->year          = intval(substr($data['payId'], -5, -4));

		$bill->isValid = $this->isValidBillInfo($payId, $billId) && $bill->isValidPayId && $bill->isValidBillId;


		return $bill;
	}
}


