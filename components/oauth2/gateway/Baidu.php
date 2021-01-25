<?php


namespace app\components\oauth2\gateway;


use app\models\BaiduSetting;
use app\models\Connect;
use tinymeng\tools\HttpRequest;

class Baidu extends Gateway
{
    public string $apiKey;
    public string $secretKey;

    const SCOPE_DEFAULT = 'basic,netdisk';

    const GRANT_TYPE_AUTHORIZE_CODE = 'authorization_code';

    const AUTHORIZE_URL = 'http://openapi.baidu.com/oauth/2.0/authorize';
    const AUTHORIZE_DISPLAY = 'page';

    const TOKEN_URL = 'https://openapi.baidu.com/oauth/2.0/token';

    const USER_URL = 'https://openapi.baidu.com/rest/2.0/passport/users/getInfo';

    public function init()
    {
        $this->apiKey = \Yii::$app->app->setting(BaiduSetting::SETTING_KEY_API_KEY);
        $this->secretKey = BaiduSetting::decrypt(\Yii::$app->app->setting(BaiduSetting::SETTING_KEY_SECRET_KEY), 'yii');
    }

    public function getScope($scope): string
    {
        return self::SCOPE_DEFAULT;
    }

    public function getGrantType($grantType): string
    {
        return self::GRANT_TYPE_AUTHORIZE_CODE;
    }

    public function getAuthorizeUrl($scope, $redirect, $state): string
    {
        \Yii::$app->cache->set('baidu_authorize:' . $state, $redirect);
        $query = [
            'response_type' => 'code',
            'client_id' => $this->apiKey,
            'redirect_uri' => $redirect,
            'scope' => $scope,
            'display' => self::AUTHORIZE_DISPLAY,
            'force_login' => 1,
            'qrcode' => 1
        ];
        return self::AUTHORIZE_URL . '?' . http_build_query($query);
    }

    public function getUserInfo($grantType): AuthorizeUser
    {
        $request = \Yii::$app->request;
        $redirect = \Yii::$app->cache->get('baidu_authorize:' . $request->getQueryParam('state'));
        if (empty($redirect)) {
            throw new \Exception('授权无效');
        }
        $code = $request->getQueryParam('code');
        $response = HttpRequest::httpGet(self::TOKEN_URL, [
            'grant_type' => $grantType,
            'code' => $code,
            'client_id' => $this->apiKey,
            'client_secret' => $this->secretKey,
            'redirect_uri' => $redirect
        ]);
        $responseData = json_decode($response, true);
        if (empty($responseData['token'])) {
            throw new \Exception($responseData['error_description'] ?? '换取access_token失败');
        }
        $userResponse = HttpRequest::httpGet(self::USER_URL, [
            'access_token' => $responseData['token'],
            'get_unionid' => 1
        ]);
        $userResponseData = json_decode($userResponse, true);
        if (!empty($userResponseData['error'])) {
            throw new \Exception($userResponseData['error_description'] ?? '获取用户信息失败');
        }
        $authorizeUser = new AuthorizeUser();
        $authorizeUser->type = Connect::CONNECT_TYPE_BAIDU;
        $authorizeUser->open_id = $userResponseData['openid'];
        $authorizeUser->union_id = $userResponseData['unionid'] ?? '';
        $authorizeUser->nickname = $userResponseData['username'] ?? '';
        $authorizeUser->avatar = $userResponseData['portrait'] ?? '';
        $authorizeUser->channel = 0;
        $authorizeUser->gender = $userResponseData['sex'];
        return $authorizeUser;
    }
}