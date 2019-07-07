<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "hasil_proyeksi_jumlah".
 *
 * @property int $no_proyeksi_jumlah
 * @property string $id_kabkota
 * @property int $tahun_proyeksi
 * @property int $jumlah_proyeksi
 */
class HasilProyeksiJumlah extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hasil_proyeksi_jumlah';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_kabkota', 'tahun_proyeksi', 'jenis_kelamin', 'jumlah_proyeksi'], 'required'],
            [['tahun_proyeksi', 'jumlah_proyeksi'], 'integer'],
            [['id_kabkota'], 'string', 'max' => 4],
            [['jenis_kelamin'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'no_proyeksi_jumlah' => 'No Proyeksi Jumlah',
            'id_kabkota' => 'Id Kabkota',
            'tahun_proyeksi' => 'Tahun Proyeksi',
            'jenis_kelamin' => 'Jenis Kelamin',
            'jumlah_proyeksi' => 'Jumlah Proyeksi',
        ];
    }
}
