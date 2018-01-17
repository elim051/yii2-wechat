<?php
/**
 * Project: WeChat.
 * Author: James
 * Date: <2017/07/12 - 21:31>
 */

namespace elim051\easywechat;

use yii;
use EasyWeChat\Factory;
use yii\base\Component;

/**
 * Class Wechat
 * @package common\components
 *
 * @property Factory    $app
 * @property bool       $isWechat
 * @property string     $returnUrl
 */
class Wechat extends Component
{
    /**
     * wechat config
     * @var array
     */
    public $config = [];
	/**
	 * user identity class params
	 * @var array
	 */
	public $userOptions = [];
	/**
	 * wechat user info will be stored in session under this key
	 * @var string
	 */
	public $sessionParam = '_wechatUser';
	/**
	 * returnUrl param stored in session
	 * @var string
	 */
	public $returnUrlParam = '_wechatReturnUrl';

    /**
     * User info scope. ['snsapi_userinfo', 'snsapi_base'],  Default is snsapi_base
     * @var array
     */
	public $scopes = [];
	/**
	 * @var Factory
	 */
	private static $_app;
	/**
	 * @var \Overtrue\Socialite\User
	 */
	private static $_user;

	/**
	 * @return yii\web\Response
	 */
	public function authorizeRequired()
	{
		if(Yii::$app->request->get('code')) {
			// callback and authorize
			return $this->authorize($this->app->oauth->user());
		}else{
			// redirect to wechat authorize page
			$this->setReturnUrl(Yii::$app->request->url);
			return Yii::$app->response->redirect($this->app->oauth->redirect()->getTargetUrl());
		}
	}
	
	/**
	 * @param \Overtrue\Socialite\User $user
	 * @return yii\web\Response
	 */
	public function authorize(\Overtrue\Socialite\User $user)
	{
		Yii::$app->session->set($this->sessionParam, $user);
		return Yii::$app->response->redirect($this->getReturnUrl());
	}

	/**
	 * check if current user authorized
	 * @return bool
	 */
	public function isAuthorized()
	{
		$sessionVal = Yii::$app->session->get($this->sessionParam);
		return (!empty($sessionVal));
	}

	/**
	 * @param string|array $url
	 */
	public function setReturnUrl($url)
	{
		Yii::$app->session->set($this->returnUrlParam, $url);
	}

	/**
	 * @param null $defaultUrl
	 * @return mixed|null|string
	 */
	public function getReturnUrl($defaultUrl = null)
	{
		$url = Yii::$app->session->get($this->returnUrlParam, $defaultUrl);
		if (is_array($url)) {
			if (isset($url[0])) {
				return Yii::$app->getUrlManager()->createUrl($url);
			} else {
				$url = null;
			}
		}

		return $url === null ? Yii::$app->homeUrl : $url;
	}

	/**
	 * single instance of \EasyWeChat\OfficialAccount\Application
	 * @return \EasyWeChat\OfficialAccount\Application
	 */
	public function getApp()
	{
		if(! self::$_app){
		    if ($this->scopes) $this->config['oauth']['scopes'] = $this->scopes;
			self::$_app = Factory::officialAccount($this->config);
		}
		return self::$_app;
	}

	/**
	 * @return \Overtrue\Socialite\User|null
	 */
	public function getUser()
	{
        self::$_user = Yii::$app->session->get($this->sessionParam);
        return self::$_user;
	}

	/**
	 * overwrite the getter in order to be compatible with this component
	 * @param $name
	 * @return mixed
	 * @throws \Exception
	 */
	public function __get($name)
	{
		try {
			return parent::__get($name);
		}catch (\Exception $e) {
			if($this->getApp()->$name) {
				return $this->app->$name;
			}else{
				throw $e->getPrevious();
			}
		}
	}

	/**
	 * check if client is wechat
	 * @return bool
	 */
	public function getIsWechat()
	{
		return strpos($_SERVER["HTTP_USER_AGENT"], "MicroMessenger") !== false;
	}
}
