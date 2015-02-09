<?php

namespace app\models;

use Yii;

/**
 * User model
 */
class User extends \pavlinter\adm\models\User
{
    const STATUS_NOT_APPROVED = 1;
    /**
     * @param null $key
     * @param null $default
     * @return array|null
     */
    public static function status($key = null, $default = null)
    {
        $status = [
            self::STATUS_ACTIVE => Yii::t('adm/user', 'Active Status', ['dot' => false]),
            self::STATUS_NOT_APPROVED => Yii::t('adm/user', 'Not Approved Status', ['dot' => false]),
            self::STATUS_DELETED => Yii::t('adm/user', 'Deleted Status', ['dot' => false]),
        ];
        if ($key !== null) {
            if (isset($status[$key])) {
                return $status[$key];
            }
            return $default;
        }

        return $status;
    }
}
