# yii2-wechat
WeChat SDK for yii2 , based on [overtrue/wechat](https://github.com/overtrue/wechat).     
This extension helps you access `overtrue/wechat` application in a simple & familiar way:   `Yii::$app->wechat`.   

## Installation
```
composer require elim051/yii2-wechat
或者将下边这个行代码加入到你的composer.json的require中
"elim051/yii2-wechat": "*"
```

## Configuration

Add the SDK as a yii2 application `component` in the `config/main.php`:

```php

'components' => [
	// ...
	'wechat' => [
        'class' => 'maxwen\easywechat\Wechat',
		'config' => [  // easywechat configurations
			'app_id' => 'wx3cf0f39249eb0exx',
		    'secret' => 'f1c242f4f28f735d4687abb469072axx',

		    // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
		    'response_type' => 'array',

		    'log' => [
		        'level' => 'debug',
		        'file' => __DIR__.'/wechat.log',
		    ],
		]
		// 'sessionParam' => '' # wechat user info will be stored in session under this key
		// 'returnUrlParam' => '' # returnUrl param stored in session
	],
	// ...
]
```

## Usage
```php

// here are two representative examples that will help you:

// 微信网页授权:
if(Yii::$app->wechat->isWechat && !Yii::$app->wechat->isAuthorized()) {
	return Yii::$app->wechat->authorizeRequired()->send();
}

// 自定义scopes
if(Yii::$app->wechat->isWechat && !Yii::$app->wechat->isAuthorized()) {
	Yii::$app->wechat->scopes = ['snsapi_userinfo'];
	return Yii::$app->wechat->authorizeRequired()->send();
}

// 微信支付(JsApi):
$orderData = [ 
	'openid' => '.. '
	// ... etc. 
];
$order = new WechatOrder($orderData);
$payment = Yii::$app->wechat->payment;
$prepayRequest = $payment->prepare($order);
if($prepayRequest->return_code = 'SUCCESS' && $prepayRequest->result_code == 'SUCCESS') {
	$prepayId = $prepayRequest->prepay_id;
}else{
	throw new yii\base\ErrorException('微信支付异常, 请稍后再试');
}

$jsApiConfig = $payment->configForPayment($prepayId);

return $this->render('wxpay', [
	'jsApiConfig' => $jsApiConfig,
	'orderData'   => $orderData
]);

```


[Wechat options configure help docs.](https://easywechat.org/zh-cn/docs/configuration.html)


### More documentation
see [EasyWeChat Docs](https://easywechat.org/zh-cn/docs/index.html).

Thanks to `overtrue/wechat` , realy a easy way to play with wechat SDK 😁.
Thanks to `maxwen/yii2-easy-wechat` 😁.