<?php


namespace app\module\v1\models;


use app\models\Identity;
use Yii;
use yii\filters\RateLimitInterface;

class TokenIdentity extends Identity implements RateLimitInterface
{
    public $rateWindowSize = 3600;

    public function getCacheKey($key)
    {
        return [__CLASS__, $this->getId(), $key];
    }

    public function getRateLimit($request, $action)
    {
        return [5000, $this->rateWindowSize];
    }

    public function loadAllowance($request, $action)
    {
        $allowance = Yii::$app->cache->get($this->getCacheKey('api_rate_allowance'));
        $timestamp = Yii::$app->cache->get($this->getCacheKey('api_rate_timestamp'));
        return [$allowance, $timestamp];
    }

    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        Yii::$app->cache->set($this->getCacheKey('api_rate_allowance'), $allowance, $this->rateWindowSize);
        Yii::$app->cache->set($this->getCacheKey('api_rate_timestamp'), $timestamp, $this->rateWindowSize);
    }
}