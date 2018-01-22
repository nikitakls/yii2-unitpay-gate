<?php
/**
 * ResultParam.php
 * User: nikitakls
 * Date: 22.01.18
 * Time: 13:05
 */

namespace nikitakls\unitpay;


use yii\helpers\ArrayHelper;

class ResultParam
{
    protected $_param;

    public function __construct(array $data)
    {
        $this->_param = $data;
    }

    public function getProfit(){
        return ArrayHelper::getValue($this->_param, 'profit');
    }

    public function getPayerSum(){
        return ArrayHelper::getValue($this->_param, 'payerSum');
    }

    public function getOrderId(){
        return ArrayHelper::getValue($this->_param, 'account');
    }

    public function getOrderSum(){
        return ArrayHelper::getValue($this->_param, 'orderSum');
    }

    public function getOrderCurrency(){
        return ArrayHelper::getValue($this->_param, 'orderCurrency');
    }

    public function getErrorMessage(){
        return ArrayHelper::getValue($this->_param, 'errorMessage');
    }

}