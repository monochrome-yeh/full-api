<?php

namespace common\modules\monochrome\members\models;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class AdminUser extends User
{
    private static $_admin_user_list;

    public static function collectionName()
    {
        return 'admin_user';
    }

    public static function getAdminUserList()
    {
        if (self::$_admin_user_list == null) {
            $result = [];

            foreach (static::find()->asArray()->all() as $adminUser) {
                $result[(string)$adminUser['_id']] = $adminUser['username'];
            }

            self::$_admin_user_list = $result;
        }

		return self::$_admin_user_list;
    }
}
