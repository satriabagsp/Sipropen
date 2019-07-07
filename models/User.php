<?php

namespace app\models;
use app\models\Pengguna; //mendifinisikan model class Masuk yang telah kita generate tadi.

class User extends \yii\base\Object implements \yii\web\IdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;
    public $role;
    public $wilayah_id;
    public $email;


    public static function findIdentity($id)
    {
        //mencari user login berdasarkan IDnya dan hanya dicari 1.
        $user = Pengguna::findOne($id); 
        if(count($user)){
            return new static($user);
        }
        return null;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        //mencari user login berdasarkan accessToken dan hanya dicari 1.
        $user = Pengguna::find()->where(['accessToken'=>$token])->one(); 
        if(count($user)){
            return new static($user);
        }
        return null;
    }

    public static function findByUsername($username)
    {
        //mencari user login berdasarkan username dan hanya dicari 1.
        $user = Pengguna::find()->where(['username'=>$username])->one(); 
        if(count($user)){
            return new static($user);
        }
        return null;
    }

    public static function findByRole($role)
    {
        //mencari user login berdasarkan username dan hanya dicari 1.
        $user = Pengguna::find()->where(['role'=>$role])->one(); 
        if(count($user)){
            return new static($user);
        }
        return null;
    }

    public static function findByWilayah_id($wilayah_id)
    {
        //mencari user login berdasarkan username dan hanya dicari 1.
        $user = Pengguna::find()->where(['wilayah_id'=>$wilayah_id])->one(); 
        if(count($user)){
            return new static($user);
        }
        return null;
    }

    public static function findByEmail($email)
    {
        //mencari user login berdasarkan username dan hanya dicari 1.
        $user = Pengguna::find()->where(['email'=>$email])->one(); 
        if(count($user)){
            return new static($user);
        }
        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    public function validatePassword($password)
    {
        return $this->password === $password;
    }
}

