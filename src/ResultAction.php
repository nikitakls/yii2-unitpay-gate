<?php
/**
 * ResultAction.php
 * User: nikitakls
 * Date: 22.01.18
 * Time: 9:59
 */

namespace nikitakls\unitpay;

use nikitakls\unitpay\lib\UnitPay;
use yii\base\Action;

class ResultAction extends Action
{
    /**
     * @var string $unitpay
     */
    protected $unitpay = 'unitpay';

    /**
     * @var Merchant $merchant
     */
    protected $merchant;

    /**
     * @var callable callback when gate perform payment
     */
    public $payCallback;

    /**
     * @var callable callback before perform payment
     */
    public $checkCallback;

    /**
     * @var callable callback if fail payment
     */
    public $failCallback;

    /**
     * Runs the action.
     */
    public function run()
    {
        if (!isset($_REQUEST['method'], $_REQUEST['params'])) {
            throw new InvalidConfigException;
        }
        $method = $_REQUEST['method'];
        $params = new ResultParam($_REQUEST['params']);

        $this->merchant->checkHandlerRequest();

        switch ($method) {
            case 'check':
                return $this->callback($this->checkCallback, $params);
            case 'pay':
                return $this->callback($this->payCallback, $params);
            case 'error':
                return $this->callback($this->failCallback, $params);
            // Method Refund means that the money returned to the client
            default:
                // Please cancel the order
                throw new InvalidConfigException('Invalid method.');
        }
    }

    public function callback($callback, $params)
    {
        if (!is_callable($callback)) {
            throw new InvalidConfigException('"' . get_class($this) . '::callback" should be a valid callback.');
        }

        $response = call_user_func($callback, $params);
        return $response;
    }


    protected function beforeRun()
    {
        if (is_null($this->controller->merchant)) {
            throw new InvalidConfigException('Merchant not configurated.');
        }
        $this->merchant = \Yii::$container->get($this->unitpay);

        return parent::beforeRun();
    }

}