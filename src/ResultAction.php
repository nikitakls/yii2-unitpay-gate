<?php
/**
 * ResultAction.php
 * User: nikitakls
 * Date: 22.01.18
 * Time: 9:59
 */

namespace nikitakls\unitpay;

use yii\base\Action;

class ResultAction extends Action
{
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
     * @var string $unitpay
     */
    protected $unitpay = 'unitpay';
    /**
     * @var Merchant $merchant
     */
    protected $merchant;

    /**
     * Runs the action.
     */
    public function run()
    {
        if (!isset($_REQUEST['method'], $_REQUEST['params'])) {
            throw new \InvalidArgumentException('Invalid params');
        }
        $method = $_REQUEST['method'];
        $params = new ResultParam($_REQUEST['params']);

        $this->merchant->checkHandlerRequest();
        if ($this->merchant->orderCurrency != $params->getOrderCurrency()) {
            throw new \InvalidArgumentException('Currency not equal');
        }

        switch ($method) {
            case 'check':
                return $this->callback($this->checkCallback, $params);
            case 'pay':
                return $this->callback($this->payCallback, $params);
            case 'error':
                return $this->callback($this->failCallback, $params);
            default:
                throw new \InvalidArgumentException('Invalid method.');
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