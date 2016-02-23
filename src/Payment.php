<?php
namespace Tartan;

use Tartan\Payment\Adapter\AdapterInterface;
use Tartan\Payment\Exception;

class Payment
{
    var $gateway;
    var $options;

    public function __construct(AdapterInterface $gateway, $options = array()
    ) {
        $this->gateway = $gateway;
        $this->options = $options;
    }

    public function generateForm() {
        return $this->gateway->generateForm($this->options);
    }

    public function verifyTransaction() {
        return $this->gateway->verifyTransaction($this->options);
    }

    public function reverseTransaction() {
        return $this->gateway->reverseTransaction($this->options);
    }

    public static function factory($adapter, array $options = [], array $banks = [])
    {
        if (!is_array($options)) {
            throw new Exception(
                'Bank parameters must be in an array'
            );
        }

        if (!is_array($banks)) {
            throw new Exception(
                'Available banks must be in an array'
            );
        }

        if (!is_string($adapter) || empty($adapter)) {
            throw new Exception(
                'Bank name must be specified in a string'
            );
        }

        if (count($banks) > 0 && !in_array($adapter, $banks)) {
            throw new Exception(
                ucfirst($adapter) .
                    " bank adapter might exist, but is not listed among available banks"
            );
        }

        $adapterNamespace = 'Tartan\Payment\Adapter\\';
        $adapterName  = $adapterNamespace . $adapter;

        if (!class_exists($adapterName)) {
            throw new Exception(
                "Adapter class '$adapterName' does not exist"
            );
        }

        $bankAdapter = new $adapterName($options);

        if (!$bankAdapter instanceof Fox_EPayment_Adapter_Abstract) {
            throw new Exception(
                "Adapter class '$adapterName' does not extend Fox_EPayment_Adapter_Abstract"
            );
        }

        $bankAdapter->bank = $adapter;

        return $bankAdapter;
    }
}
