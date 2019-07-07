<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "master_wilayah".
 *
 * @property string $id_wilayah
 * @property string $nama_wilayah
 */
class MasterWilayah extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'master_wilayah';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_wilayah', 'nama_wilayah'], 'required'],
            [['id_wilayah'], 'string', 'max' => 10],
            [['nama_wilayah'], 'string', 'max' => 100],
            [['id_wilayah'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_wilayah' => 'Kode Wilayah',
            'nama_wilayah' => 'Nama Wilayah',
        ];
    }
}
