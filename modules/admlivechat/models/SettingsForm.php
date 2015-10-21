<?php

namespace app\modules\admlivechat\models;

use app\modules\admlivechat\Module;
use pavlinter\admparams\Module as ParamsManager;
use Yii;
use yii\base\Model;

/**
 * SettingsForm
 */
class SettingsForm extends Model
{
    public $analytic;

    public $webtools;

    public $active = 1;

    public function init()
    {
        if (isset(Yii::$app->params[Module::getInstance()->settingsKey])) {
            $params = Yii::$app->params[Module::getInstance()->settingsKey];
            foreach ($params as $name => $value) {
                if ($this->hasProperty($name)) {
                    $this->$name = $value;
                }
            }

        }
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['analytic'], 'filter', 'filter' => function ($value) {
                $value = preg_replace('#<script(.*?)>(.*?)</script>#is', '$2', $value);
                return trim($value);
            },],
            [['analytic', 'webtools'], 'string'],
            [['active'], 'boolean'],
        ];
    }



    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'analytic' => Yii::t('modelAdm/admlivechat', 'Analytic Code'),
            'webtools' => Yii::t('modelAdm/admlivechat', 'Webmaster Tools'),
            'active' => Yii::t('modelAdm/admlivechat', 'Active'),
        ];
    }

    /**
     * @return bool
     */
    public function save()
    {
        return ParamsManager::getInstance()->manager->staticParams('change', Module::getInstance()->settingsKey, $this->getAttributes());
    }
}


