<?php
/**
 * Merchant.php
 * User: nikitakls
 * Date: 22.01.18
 * Time: 10:17
 */

namespace nikitakls\unitpay;

use nikitakls\unitpay\lib\UnitPay;
use yii\base\BaseObject;

class Merchant extends BaseObject
{
    public $secretKey = '';
    public $publicKey = '';

    public $backUrl = '';


    public $orderCurrency = 'RUB';

    public $locale = 'ru';

    private $_provider = null;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->_provider = new UnitPay($this->secretKey);
    }

    /**
     * Return link to redirect to payment via unitpay gate
     * @param float|int $orderSum
     * @param integer $orderId
     * @param string $orderDesc
     * @param string $email
     * @param string $phone
     * @return string redirect url
     */
    public function payment($orderSum, $orderId, $orderDesc, $email = '', $phone = '')
    {
        $pay = $this->_provider;
        $pay->setBackUrl($this->backUrl);
        if (!empty($email)) {
            $pay->setCustomerEmail($email);
        }
        if (!empty($phone)) {
            $pay->setCustomerPhone($phone);
        }
        $redirectUrl = $pay->form(
            $this->publicKey,
            $orderSum,
            $orderId,
            $orderDesc,
            $this->orderCurrency,
            $this->locale
        );
        return $redirectUrl;
    }

    /**
     * @param string $message
     * @return string
     */
    public function getSuccessResponse($message)
    {
        return $this->_provider->getSuccessHandlerResponse($message);
    }

    /**
     * @param string $message
     * @return string
     */
    public function getErrorResponse($message)
    {
        return $this->_provider->getErrorHandlerResponse($message);
    }

    /**
     * @return bool
     */
    public function checkHandlerRequest()
    {
        return $this->_provider->checkHandlerRequest();
    }

}