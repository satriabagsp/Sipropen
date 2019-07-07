<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "data_provinsi_lp".
 *
 * @property int $id_nomor
 * @property string $id_wilayah
 * @property string $tahun_data
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
class DataProvinsiLp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'data_provinsi_lp';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_wilayah', 'tahun_data', 'jenis_kelamin', 'ku_5', 'ku_10', 'ku_15', 'ku_20', 'ku_25', 'ku_30', 'ku_35', 'ku_40', 'ku_45', 'ku_50', 'ku_55', 'ku_60', 'ku_65', 'ku_70', 'ku_75', 'ku_80'], 'required'],
            [['ku_5', 'ku_10', 'ku_15', 'ku_20', 'ku_25', 'ku_30', 'ku_35', 'ku_40', 'ku_45', 'ku_50', 'ku_55', 'ku_60', 'ku_65', 'ku_70', 'ku_75', 'ku_80'], 'integer'],
            [['id_wilayah'], 'string', 'max' => 10],
            [['tahun_data', 'jenis_kelamin'], 'string', 'max' => 4],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_nomor' => 'Id Nomor',
            'id_wilayah' => 'Id Wilayah',
            'tahun_data' => 'Tahun Data',
            'jenis_kelamin' => 'Jenis Kelamin',
            'ku_5' => 'Ku 5',
            'ku_10' => 'Ku 10',
            'ku_15' => 'Ku 15',
            'ku_20' => 'Ku 20',
            'ku_25' => 'Ku 25',
            'ku_30' => 'Ku 30',
            'ku_35' => 'Ku 35',
            'ku_40' => 'Ku 40',
            'ku_45' => 'Ku 45',
            'ku_50' => 'Ku 50',
            'ku_55' => 'Ku 55',
            'ku_60' => 'Ku 60',
            'ku_65' => 'Ku 65',
            'ku_70' => 'Ku 70',
            'ku_75' => 'Ku 75',
            'ku_80' => 'Ku 80',
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
                                         'Menambah tahun proyeksi baru',
                                         '".date('Y-m-d')."', 
                                         '".date('H:i:s')."')";
                    $query_tambah = mysqli_query($host,$sql_tambah) or die(mysqli_error());
            }
            else{
                //Simpan data di monitoring
                    include "koneksi.php";
                    $sql_tambah = "INSERT INTO monitoring (nama_wilayah, kegiatan, tanggal, waktu) 
                                   VALUE('".Yii::$app->user->identity->username."', 
                                         'Mengubah data tahun proyeksi',
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
