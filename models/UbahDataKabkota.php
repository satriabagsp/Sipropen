<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ubah_data_kabkota".
 *
 * @property int $no_ubah_data
 * @property string $id_wilayah
 * @property int $tahun_data
 * @property string $jenis_kelamin
 * @property int $ku_5
 * @property int $ku_10
 * @property int $ku_15
 * @property int $ku_20
 * @property int $ku_25
 * @property int $ku_30
 * @property int $ku_35
 * @property int $ku_40
 * @property int $ku_45
 * @property int $ku_50
 * @property int $ku_55
 * @property int $ku_60
 * @property int $ku_65
 * @property int $ku_70
 * @property int $ku_75
 * @property int $ku_80
 */
class UbahDataKabkota extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ubah_data_kabkota';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_wilayah', 'tahun_data', 'jenis_kelamin', 'ku_5', 'ku_10', 'ku_15', 'ku_20', 'ku_25', 'ku_30', 'ku_35', 'ku_40', 'ku_45', 'ku_50', 'ku_55', 'ku_60', 'ku_65', 'ku_70', 'ku_75', 'ku_80'], 'required'],
            [['tahun_data', 'ku_5', 'ku_10', 'ku_15', 'ku_20', 'ku_25', 'ku_30', 'ku_35', 'ku_40', 'ku_45', 'ku_50', 'ku_55', 'ku_60', 'ku_65', 'ku_70', 'ku_75', 'ku_80'], 'integer'],
            [['id_wilayah'], 'string', 'max' => 4],
            [['jenis_kelamin'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'no_ubah_data' => 'No Ubah Data',
            'id_wilayah' => 'Kode Kabupaten/kota',
            'tahun_data' => 'Tahun Data',
            'jenis_kelamin' => 'Jenis Kelamin',
            'ku_5' => '(ubahan) Kelompok Umur 0-4 ',
            'ku_10' => '(ubahan) Kelompok Umur 5-9 ',
            'ku_15' => '(ubahan) Kelompok Umur 10-14 ',
            'ku_20' => '(ubahan) Kelompok Umur 15-19 ',
            'ku_25' => '(ubahan) Kelompok Umur 20-24 ',
            'ku_30' => '(ubahan) Kelompok Umur 25-29 ',
            'ku_35' => '(ubahan) Kelompok Umur 30-34 ',
            'ku_40' => '(ubahan) Kelompok Umur 35-39 ',
            'ku_45' => '(ubahan) Kelompok Umur 40-44 ',
            'ku_50' => '(ubahan) Kelompok Umur 45-49 ',
            'ku_55' => '(ubahan) Kelompok Umur 50-54 ',
            'ku_60' => '(ubahan) Kelompok Umur 55-59 ',
            'ku_65' => '(ubahan) Kelompok Umur 60-64 ',
            'ku_70' => '(ubahan) Kelompok Umur 65-69 ',
            'ku_75' => '(ubahan) Kelompok Umur 70-74 ',
            'ku_80' => '(ubahan) Kelompok Umur 75+ ',
        ];
    }

    public function beforeSave($insert)
    {
        parent::beforeSave($insert);
        if ($this->isNewRecord)
        {
            //Simpan data di monitoring
                include "koneksi.php";
                $sql_tambah = "INSERT INTO monitoring (nama_wilayah, kegiatan, tanggal, waktu) 
                               VALUE('".Yii::$app->user->identity->username."', 
                                     'Membuat permintaan ubah data',
                                     '".date('Y-m-d')."', 
                                     '".date('H:i:s')."')";
                $query_tambah = mysqli_query($host,$sql_tambah) or die(mysqli_error());
        }
        else
        {
            //Simpan data di monitoring
                include "koneksi.php";
                $sql_tambah = "INSERT INTO monitoring (nama_wilayah, kegiatan, tanggal, waktu) 
                               VALUE('".Yii::$app->user->identity->username."', 
                                     'Mengubah permintaan ubah data',
                                     '".date('Y-m-d')."', 
                                     '".date('H:i:s')."')";
                $query_tambah = mysqli_query($host,$sql_tambah) or die(mysqli_error());
        };
        
        return true;
    }
}
