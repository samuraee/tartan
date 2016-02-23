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
                    $this->referenceId = $value;
                break;
            }
        }
    }

    public function getInvoiceId()
    {
        if (!isset($this->_config['reservationNumber'])) {
            return null;
        }
        return $this->_config['reservationNumber'];
    }

    public function getReferenceId()
    {
        if (!isset($this->_config['referenceId'])) {
            return null;
        }
        return $this->_config['referenceId'];
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
	    if (isset($this->_config['withToken']) && $this->_config['withToken']) {
		    return $this->doGenerateFormWithToken($options);
	    } else {
		    return $this->doGenerateFormWithoutToken($options); // default
	    }
    }
    public function doGenerateFormWithoutToken(array $options = array())
    {
        $this->setOptions($options);
        $this->_checkRequiredOptions(['amount', 'merchantCode', 'reservationNumber', 'redirectAddress']);

        if (isset($this->_config['isMobile']) && $this->_config['isMobile']) {
	        $action = $this->getEndPoint(true);
        } else {
	        $action = $this->getEndPoint();
        }

        $form  = sprintf('<form id="goto-bank-form" method="post" action="%s" class="form-horizontal">', $action );
        $form .= sprintf('<input name="Amount" value="%d">', $this->_config['amount']);
        $form .= sprintf('<input name="MID" value="%s">', $this->_config['merchantCode']);
        $form .= sprintf('<input name="ResNum" value="%s">', $this->_config['reservationNumber']);
        $form .= sprintf('<input name="RedirectURL" value="%s">', $this->_config['redirectAddress']);

        if (isset($this->_config['logoUri'])) {
            $form .= sprintf('<input name="LogoURI" value="%s">', $this->_config['logoUri']);
        }

        $label = isset($this->_config['submitLabel']) ? $this->_config['submitLabel'] : Lang::trans("global.go_to_gateway");

        $form .= sprintf('<div class="control-group"><div class="controls"><input type="submit" class="btn btn-success" value="%s"></div></div>', $label);

        $form .= '</form>';

        return $form;
    }

	public function doGenerateFormWithToken(array $options = array())
	{
		$this->setOptions($options);
		$this->_checkRequiredOptions(['amount', 'merchantCode', 'reservationNumber', 'redirectAddress']);

		if (isset($this->_config['isMobile']) && $this->_config['isMobile']) {
			$action = $this->getEndPoint(true);
		} else {
			$action = $this->getEndPoint();
		}

		try {
			$this->_log($this->getWSDL());
			$soapClient = new SoapClient($this->getWSDL());

			$sendParams = array(
				'pin'         => $this->_config['merchantCode'],
				'amount'      => $this->_config['amount'],
				'orderId'     => $this->_config['orderId']
			);

			$res = $soapClient->__soapCall('PinPaymentRequest', $sendParams);

		} catch (SoapFault $e) {
			$this->log($e->getMessage());
			throw new Exception('SOAP Exception: ' . $e->getMessage());
		}

		$form  = sprintf('<form id="goto-bank-form" method="post" action="%s" class="form-horizontal">', $action );
		$form .= sprintf('<input name="Amount" value="%d">', $this->_config['amount']);
		$form .= sprintf('<input name="MID" value="%s">', $this->_config['merchantCode']);
		$form .= sprintf('<input name="ResNum" value="%s">', $this->_config['reservationNumber']);
		$form .= sprintf('<input name="RedirectURL" value="%s">', $this->_config['redirectAddress']);

		if (isset($this->_config['logoUri'])) {
			$form .= sprintf('<input name="LogoURI" value="%s">', $this->_config['logoUri']);
		}

		$label = isset($this->_config['submitLabel']) ? $this->_config['submitLabel'] : Lang::trans("global.go_to_gateway");

		$form .= sprintf('<div class="control-group"><div class="controls"><input type="submit" class="btn btn-success" value="%s"></div></div>', $label);

		$form .= '</form>';

		return $form;
	}

    public function doVerifyTransaction(array $options = array())
    {
        $this->setOptions($options);
        $this->_checkRequiredOptions(['referenceId', 'merchantCode', 'state']);

        if ($this->_config['referenceId'] == '') {
	        throw new Exception('Error: ' . $this->_config['state']);
        }

        try {
            if (isset($this->_config['useHttps']) && $this->_config['useHttps'] === false) {
                $soapClient = new SoapClient($this->getWSDL());
            } else {
                $soapClient = new SoapClient($this->getWSDL(true));
            }

            $res = $soapClient->VerifyTransaction(
                $this->_config['referenceId'], $this->_config['merchantCode']
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
        $this->_checkRequiredOptions(['referenceId', 'merchantCode', 'password', 'amount']);

        try {
            if (isset($this->_config['useHttps']) && $this->_config['useHttps'] === false) {
                $soapClient = new SoapClient($this->getWSDL());
            } else {
                $soapClient = new SoapClient($this->getWSDL(true));
            }

            $res = $soapClient->reverseTransaction(
                $this->_config['referenceId'],
                $this->_config['merchantCode'],
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
