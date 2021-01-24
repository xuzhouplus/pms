<?php


namespace app\components\oauth2\gateway;


class AuthorizeUser extends \yii\base\BaseObject
{
	public string $open_id;//用户唯一id
	public string $union_id;//微信union_id
	public string $channel;//登录类型请查看 \\tinymeng\\OAuth2\\Helper\\ConstCode
	public string $nickname;//昵称
	public string $gender;//0=>未知 1=>男 2=>女   twitter和line不会返回性别，所以这里是0，Facebook根据你的权限，可能也不会返回，所以也可能是0
	public string $avatar;//头像
	public string $type;//授权类型
}