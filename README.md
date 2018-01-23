nikitakls/yii2-unitpay-gate
=============================
Unitpay gate for Yii2

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require --prefer-dist nikitakls/yii2-unitpay-gate "*"
```

or add

```
"nikitakls/yii2-unitpay-gate": "*"
```

to the require section of your `composer.json` file.

Configuration
-----

```
'components' => [
    'unitpay' => [
        'class' => '\nikitakls\unitpay\Merchant',
        'secretKey' => '',
        'publicKey' => '',
        'orderCurrency' => 'RUB', # 'EUR', 'UAH', 'BYR', 'USD', 'RUB'
        'locale' => 'ru',
    ]
    ...
]

```

Usage
-----

Once the extension is installed, simply use it in your code by  :



You can use result action in you controller:
```php
class PaymentController extends Controller
{
    
    public $enableCsrfValidation = false;
    
    public $unitpay = 'unitpay';
    
    
    public function actionInvoice()
    {
        $model = new Invoice();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            /** @var \nikitakls\unitpay\Merchant $merchant */
            $merchant = Yii::$app->get($this->unitpay);
            return $this->redirect($merchant->payment($model->sum, $model->id, 'Пополнение счета', Yii::$app->user->identity->email, $model->phone));
        } else {
            return $this->render('invoice', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {

        return [
            'result' => [
                'class' => ResultAction::class,
                'payCallback' => [$this, 'payCallback'],
                'checkCallback' => [$this, 'checkCallback'],
                'failCallback' => [$this, 'failCallback'],
            ],
        ];
    }
    
    public function payCallback(ResultParam $param)
    {
                $this->loadModel($nInvId)->updateAttributes(['status' => Invoice::STATUS_ACCEPTED]);
                return Yii::$app->get('unitpay')->getSuccessResponse('Pay Success');

    }
    
    public function checkCallback(ResultParam $param)
    {
                if($this->loadModel($nInvId)){
                    return Yii::$app->get('unitpay')->getSuccessResponse('Check Success. Ready to pay.');
                };
                return Yii::$app->get('unitpay')->getErrorResponse('Message about error');
    }
    
    public function failCallback(ResultParam $param)
    {
                $this->loadModel($nInvId)->updateAttributes(['status' => Invoice::STATUS_FAIL]);
                Yii::$app->errorHandler->logException($param->getErrorMessage());
                return Yii::$app->get('unitpay')->getSuccessHandlerResponse('Error logged');
    }
}
```