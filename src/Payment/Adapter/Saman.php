<?php
namespace Tartan\Payment\Adapter;

use Illuminate\Support\Facades\Lang;
use SoapClient;
use SoapFault;

class Saman extends AdapterAbstract
{

	protected $_WSDL             = 'http://acquirer.sb24.com/ref-payment/ws/ReferencePayment?WSDL';
	protected $_SECURE_WSDL      = 'https://acquirer.sb24.com/ref-payment/ws/ReferencePayment?WSDL';
	protected $_END_POINT        = 'https://acquirer.sb24.com/CardServices/controller';
	protected $_MOBILE_END_POINT = 'https://macquirer.samanepay.com/pay.php';

    protected $_TEST_WSDL        = 'http://banktest.ir/gateway/saman/ws?wsdl';
    protected $_TEST_END_POINT   = 'http://banktest.ir/gateway/saman/gate';
    protected $_TEST_MOBILE_END_POINT = 'http://banktest.ir/gateway/saman/gate';

    public $reverseSupport = true;

    public $validateReturnsAmount = true;

    public function setOptions(array $options = array())
    {
        parent::setOptions($options);
        foreach ($this->_config as $name => $value) {
            switch ($name) {
            case 'resnum':
                if (preg_match('/^[a-z0-9]+$/', $value))
                    $this->reservationNumber = $value;
                break;
            case 'refnum':
                if (strlen($value) === 20)
                    $this->refId = $value;
                break;
            }
        }
    }

    public function getInvoiceId()
    {
        if (!isset($this->_config['reservation_number'])) {
            return null;
        }
        return $this->_config['reservation_number'];
    }

    public function getReferenceId()
    {
        if (!isset($this->_config['ref_id'])) {
            return null;
        }
        return $this->_config['ref_id'];
    }

    public function getStatus()
    {
        if (!isset($this->_config['state'])) {
            return null;
        }
        return $this->_config['state'];
    }

    public function doGenerateForm(array $options = array())
    {
	    if (isset($this->_config['with_token']) && $this->_config['with_token']) {
		    return $this->doGenerateFormWithToken($options);
	    } else {
		    return $this->doGenerateFormWithoutToken($options); // default
	    }
    }
    public function doGenerateFormWithoutToken(array $options = array())
    {
        $this->setOptions($options);
        $this->_checkRequiredOptions(['amount', 'terminal_id', 'reservation_number', 'redirect_url']);

        if (isset($this->_config['isMobile']) && $this->_config['is_mobile']) {
	        $action = $this->getEndPoint(true);
        } else {
	        $action = $this->getEndPoint();
        }

        $form  = sprintf('<form id="goto-bank-form" method="post" action="%s" class="form-horizontal">', $action );
        $form .= sprintf('<input name="Amount" value="%d">', $this->_config['amount']);
        $form .= sprintf('<input name="MID" value="%s">', $this->_config['terminal_id']);
        $form .= sprintf('<input name="ResNum" value="%s">', $this->_config['reservation_number']);
        $form .= sprintf('<input name="RedirectURL" value="%s">', $this->_config['redirect_url']);

        if (isset($this->_config['logo_uri'])) {
            $form .= sprintf('<input name="LogoURI" value="%s">', $this->_config['logo_uri']);
        }

        $label = isset($this->_config['submitLabel']) ? $this->_config['submitLabel'] : Lang::trans("global.go_to_gateway");

        $form .= sprintf('<div class="control-group"><div class="controls"><input type="submit" class="btn btn-success" value="%s"></div></div>', $label);

        $form .= '</form>';

        return $form;
    }

	public function doGenerateFormWithToken(array $options = array())
	{
		$this->setOptions($options);
		$this->_checkRequiredOptions(['amount', 'terminal_id', 'reservation_number', 'redirect_url']);

		if (isset($this->_config['is_mobile']) && $this->_config['is_mobile']) {
			$action = $this->getEndPoint(true);
		} else {
			$action = $this->getEndPoint();
		}

		try {
			$this->_log($this->getWSDL());
			$soapClient = new SoapClient($this->getWSDL());

			$sendParams = array(
				'pin'         => $this->_config['terminal_id'],
				'amount'      => $this->_config['amount'],
				'orderId'     => $this->_config['order_id']
			);

			$res = $soapClient->__soapCall('PinPaymentRequest', $sendParams);

		} catch (SoapFault $e) {
			$this->log($e->getMessage());
			throw new Exception('SOAP Exception: ' . $e->getMessage());
		}

		$form  = sprintf('<form id="goto-bank-form" method="post" action="%s" class="form-horizontal">', $action );
		$form .= sprintf('<input name="Amount" value="%d">', $this->_config['amount']);
		$form .= sprintf('<input name="MID" value="%s">', $this->_config['terminal_id']);
		$form .= sprintf('<input name="ResNum" value="%s">', $this->_config['reservation_number']);
		$form .= sprintf('<input name="RedirectURL" value="%s">', $this->_config['redirect_url']);

		if (isset($this->_config['logo_uri'])) {
			$form .= sprintf('<input name="LogoURI" value="%s">', $this->_config['logo_uri']);
		}

		$label = isset($this->_config['submit_label']) ? $this->_config['submit_label'] : Lang::trans("global.go_to_gateway");

		$form .= sprintf('<div class="control-group"><div class="controls"><input type="submit" class="btn btn-success" value="%s"></div></div>', $label);

		$form .= '</form>';

		return $form;
	}

    public function doVerifyTransaction(array $options = array())
    {
        $this->setOptions($options);
        $this->_checkRequiredOptions(['ref_id', 'terminal_id', 'state']);

        if ($this->_config['ref_id'] == '') {
	        throw new Exception('Error: ' . $this->_config['state']);
        }

        try {
            if (isset($this->_config['useHttps']) && $this->_config['useHttps'] === false) {
                $soapClient = new SoapClient($this->getWSDL());
            } else {
                $soapClient = new SoapClient($this->getWSDL(true));
            }

            $res = $soapClient->VerifyTransaction(
                $this->_config['ref_id'], $this->_config['terminal_id']
            );
        } catch (SoapFault $e) {
            $this->_log($e->getMessage());
            throw new Exception('SOAP Exception: ' . $e->getMessage());
        }

        return (int) $res;
    }

    public function doReverseTransaction(array $options = array())
    {
        $this->setOptions($options);
        $this->_checkRequiredOptions(['ref_id', 'terminal_id', 'password', 'amount']);

        try {
            if (isset($this->_config['use_https']) && $this->_config['use_https'] === false) {
                $soapClient = new SoapClient($this->getWSDL());
            } else {
                $soapClient = new SoapClient($this->getWSDL(true));
            }

            $res = $soapClient->reverseTransaction(
                $this->_config['ref_id'],
                $this->_config['terminal_id'],
                $this->_config['password'],
                $this->_config['amount']
            );
        } catch (SoapFault $e) {
            $this->_log($e->getMessage());
            throw new Exception('SOAP Exception: ' . $e->getMessage());
        }

        return (int) $res;
    }
}
