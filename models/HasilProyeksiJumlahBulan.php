<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "hasil_proyeksi_jumlah_bulan".
 *
 * @property int $no_proyeksi_jumlah
 * @property string $id_wilayah
 * @property int $tahun_proyeksi
 * @property int $jumlah_proyeksi
 */
class HasilProyeksiJumlahBulan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hasil_proyeksi_jumlah_bulan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_wilayah', 'tahun_proyeksi', 'jenis_kelamin', 'jumlah_proyeksi'], 'required'],
            [['tahun_proyeksi', 'jumlah_proyeksi'], 'integer'],
            [['id_wilayah'], 'string', 'max' => 4],
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
            'id_wilayah' => 'Id Wilayah',
            'tahun_proyeksi' => 'Tahun Proyeksi',
            'jenis_kelamin' => 'Jenis Kelamin',
            'jumlah_proyeksi' => 'Jumlah Proyeksi',
        ];
    }
}
