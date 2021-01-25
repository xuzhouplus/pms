<?php


namespace app\components\oauth2\gateway;


abstract class Gateway extends \yii\base\BaseObject
{
    public abstract function getScope($scope): string;

    public abstract function getGrantType($grantType): string;

    public abstract function getAuthorizeUrl($scope, $redirect, $state): string;

    public abstract function getUserInfo($grantType): AuthorizeUser;
}