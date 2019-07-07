<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "monitoring".
 *
 * @property int $id_monitoring
 * @property string $nama_wilayah
 * @property string $kegiatan
 * @property string $tanggal
 * @property string $waktu
 */
class Monitoring extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'monitoring';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nama_wilayah', 'kegiatan', 'tanggal', 'waktu'], 'required'],
            [['nama_wilayah', 'kegiatan'], 'string', 'max' => 100],
            [['tanggal', 'waktu'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_monitoring' => 'Id Monitoring',
            'nama_wilayah' => 'Nama Wilayah',
            'kegiatan' => 'Kegiatan',
            'tanggal' => 'Tanggal',
            'waktu' => 'Waktu',
        ];
    }
}
