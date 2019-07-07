<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "wilayah".
 *
 * @property string $id_provinsi
 * @property string $provinsi
 * @property string $id_kabkota
 * @property string $kabkota
 */
class Wilayah extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wilayah';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_provinsi', 'provinsi', 'id_kabkota', 'kabkota'], 'required'],
            [['id_provinsi'], 'string', 'max' => 2],
            [['provinsi', 'kabkota'], 'string', 'max' => 100],
            [['id_kabkota'], 'string', 'max' => 4],
            [['id_kabkota'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_provinsi' => 'Kode Provinsi',
            'provinsi' => 'Provinsi',
            'id_kabkota' => 'Kode Kabkota',
            'kabkota' => 'Kabkota',
        ];
    }
}
