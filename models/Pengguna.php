<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "masuk".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $role
 * @property string $wilayah_id
 * @property string $email
 */
class Pengguna extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'masuk';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username'], 'string', 'max' => 30],
            [['password'], 'string', 'max' => 50],
            [['role', 'wilayah_id'], 'string', 'max' => 10],
            [['email'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'role' => 'Role',
            'wilayah_id' => 'Wilayah ID',
            'email' => 'Kontak Email',
        ];
    }
}
