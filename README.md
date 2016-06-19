Tartan
============



License
-------
MIT license.


VTartan Custom Validators
-------------------------
Add the following line to boot method of `app/Providers/AppServiceProvider.php`
 
 Validator::extend('strength', 'Tartan\Validators\CustomValidator@validateStrength');
 Validator::extend('iran_billing_id', 'Tartan\Validators\CustomValidator@validateIranBillingId');

Add following lines to `resources/lang/en/validation.php` in `Custom Validation Language Lines` part

```
 'strength' => 'The password :attribute is too weak and must contain one or more uppercase, lowercase, numeric, and special character (!@#$%^&*).',
 'iran_billing_id' => 'The billing Id :attribute is not a valid Billing Id.'
```