<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "data_kabkota_lp".
 *
 * @property int $no_data
 * @property string $id_kabkota
 * @property int $tahun_dasar
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
class DataKabkotaLp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'data_kabkota_lp';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_wilayah', 'tahun_dasar', 'jenis_kelamin', 'ku_5', 'ku_10', 'ku_15', 'ku_20', 'ku_25', 'ku_30', 'ku_35', 'ku_40', 'ku_45', 'ku_50', 'ku_55', 'ku_60', 'ku_65', 'ku_70', 'ku_75', 'ku_80'], 'required'],
            [['tahun_dasar', 'ku_5', 'ku_10', 'ku_15', 'ku_20', 'ku_25', 'ku_30', 'ku_35', 'ku_40', 'ku_45', 'ku_50', 'ku_55', 'ku_60', 'ku_65', 'ku_70', 'ku_75', 'ku_80'], 'integer'],
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
            'no_data' => 'No Data',
            'id_wilayah' => 'Kode Kabupaten/kota',
            'tahun_dasar' => 'Tahun Data',
            'jenis_kelamin' => 'Jenis Kelamin',
            'ku_5' => 'Kelompok Umur 0-4 ',
            'ku_10' => 'Kelompok Umur 5-9 ',
            'ku_15' => 'Kelompok Umur 10-14 ',
            'ku_20' => 'Kelompok Umur 15-19 ',
            'ku_25' => 'Kelompok Umur 20-24 ',
            'ku_30' => 'Kelompok Umur 25-29 ',
            'ku_35' => 'Kelompok Umur 30-34 ',
            'ku_40' => 'Kelompok Umur 35-39 ',
            'ku_45' => 'Kelompok Umur 40-44 ',
            'ku_50' => 'Kelompok Umur 45-49 ',
            'ku_55' => 'Kelompok Umur 50-54 ',
            'ku_60' => 'Kelompok Umur 55-59 ',
            'ku_65' => 'Kelompok Umur 60-64 ',
            'ku_70' => 'Kelompok Umur 65-69 ',
            'ku_75' => 'Kelompok Umur 70-74 ',
            'ku_80' => 'Kelompok Umur 75+ ',
        ];
    }

    public function beforeSave($insert)
    {
        parent::beforeSave($insert);
        if(Yii::$app->user->identity->role == 'provinsi'){
            if ($this->isNewRecord){
                //Simpan data di monitoring
                    include "koneksi.php";
                    $sql_tambah = "INSERT INTO monitoring (nama_wilayah, kegiatan, tanggal, waktu) 
                                   VALUE('".Yii::$app->user->identity->username."', 
                                         'Menambah kabupaten/kota baru',
                                         '".date('Y-m-d')."', 
                                         '".date('H:i:s')."')";
                    $query_tambah = mysqli_query($host,$sql_tambah) or die(mysqli_error());
            }
            else{
                //Simpan data di monitoring
                    include "koneksi.php";
                    $sql_tambah = "INSERT INTO monitoring (nama_wilayah, kegiatan, tanggal, waktu) 
                                   VALUE('".Yii::$app->user->identity->username."', 
                                         'Mengubah data kabupaten/kota',
                                         '".date('Y-m-d')."', 
                                         '".date('H:i:s')."')";
                    $query_tambah = mysqli_query($host,$sql_tambah) or die(mysqli_error());

                //Hapus hasil proyeksi yang sudah pernah dibuat-->
                    $id_provinsi = substr(Yii::$app->user->identity->wilayah_id, 0, 2);
                    //Buat koneksi ke DB
                        include "koneksi.php";
                        $sql_hapusHasil = "DELETE hasil_proyeksi_jumlah
                                           FROM hasil_proyeksi_jumlah, master_wilayah
                                           WHERE hasil_proyeksi_jumlah.id_wilayah = master_wilayah.id_wilayah
                                           AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'";
                        $query_hapusHasil = mysqli_query($host,$sql_hapusHasil) or die(mysqli_error());
                        $sql_hapusHasil3 = "DELETE hasil_proyeksi_jumlah_bulan
                                           FROM hasil_proyeksi_jumlah_bulan, master_wilayah
                                           WHERE hasil_proyeksi_jumlah_bulan.id_wilayah = master_wilayah.id_wilayah
                                           AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'";
                        $query_hapusHasil3 = mysqli_query($host,$sql_hapusHasil3) or die(mysqli_error());
                        $sql_hapusHasil2 = "DELETE hasil_proyeksi_lp
                                           FROM hasil_proyeksi_lp, master_wilayah
                                           WHERE hasil_proyeksi_lp.id_wilayah = master_wilayah.id_wilayah
                                           AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'";
                        $query_hapusHasil2 = mysqli_query($host,$sql_hapusHasil2) or die(mysqli_error());
                        $sql_hapusPengiriman = "DELETE FROM laporan WHERE perihal = 'Cek hasil proyeksi' AND asal = '" . Yii::$app->user->identity->username . "'";
                        $query_hapusPengiriman = mysqli_query($host,$sql_hapusPengiriman) or die(mysqli_error());
            };

            
        };
        
        return true;
    }

}
