<?php
require(__DIR__ . '/../src/Validators/CustomValidator.php');

use Tartan\Validators\CustomValidator;

class ValidatorsTest extends PHPUnit_Framework_TestCase
{
	function testCalendars()
	{
		$validator = new CustomValidator();
		$result = $validator->validateNationalId (null, '3801245144', null, null);
		$this->assertTrue($result);

		$result = $validator->validateNationalId (null, '0081668791', null, null);
		$this->assertTrue($result);

		$result = $validator->validateNationalId (null, '3360120760', null, null);
		$this->assertTrue($result);

		$result = $validator->validateNationalId (null, '3801245145', null, null);
		$this->assertFalse($result);
	}
}
