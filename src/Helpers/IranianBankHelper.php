<?php

namespace Tartan\Helpers;

class IranianBankHelpers
{
	public function cardBankName($cardnumber)
	{
		switch (substr($cardnumber,0,6))
		{
			case "603799": //1
				return "بانک ملی";
				break;
			case "589210"://2
				return "بانک سپه";
				break;
			case "627648"://3
				return "بانک توسعه صادرات";
				break;
			case "627961"://4
				return "بانک صنعت و معدن";
				break;
			case "603770"://5
				return "بانک کشاورزي";
				break;
			case "628023"://6
				return "بانک مسکن";
				break;
			case "627760"://7
				return "پست بانک ایران";
				break;
			case "502908"://8
				return "بانک توسعه تعاون";
				break;
			case "627412"://9
				return "بانک اقتصاد نوین";
				break;
			case "622106"://10
				return "بانک پارسیان";
				break;
			case "639347" :
			case "502229"://11
				return "بانک پاسارگاد";
				break;
			case "627488"://12
				return "بانک کارآفرین";
				break;
			case "621986"://13
				return "بانک سامان";
				break;
			case "639346"://14
				return "بانک سینا";
				break;
			case "639607"://15
				return "بانک سرمایه";
				break;
			case "502806"://16
				return "بانک شهر";
				break;
			case "502938"://17
				return "بانک دی";
				break;
			case "603769"://18
				return "بانک صادرات";
				break;
			case "610433"://19
				return "بانک ملت";
				break;
			case "627353"://20
				return "بانک تجارت";
				break;
			case "589463"://21
				return "بانک رفاه";
				break;
			case "627381"://22
				return "بانک انصار";
				break;
			case "636214"://23
				return "بانک آینده";
				break;
			case "606737"://24
				return "موسسه مالی اعتباری مهر";
				break;
			case "628157"://25
			case "504706"://25
				return "موسسه اعتباری توسعه";
				break;
			case "936450"://26
				return "بانک مرکزی";
				break;
			case "505785"://27
				return "بانک ایران زمین";
				break;
			case "606373"://28
				return "بانک مهر";
				break;
			case "505416"://29
				return "بانگ گردشگری";
				break;
			case "504172":
				return "بانک رسالت";
				break;
			case "639599":
				return "بانک قوامین";
				break;

		}

	}

	function generateShebaNumber($account, $formated = false)
	{
		$section = 98 - bcmod('62000000' . $account . '182700', 97);
		$sheba = sprintf('IR%s%s%s', str_pad($section, 2, '0', STR_PAD_LEFT), '062000000', $account);
		if($formated)
			return substr(chunk_split($sheba, 4, '-'), 0, -1);
		else
			return $sheba;
	}
}


