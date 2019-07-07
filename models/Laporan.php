<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "laporan".
 *
 * @property int $id_laporan
 * @property string $tanggal
 * @property string $waktu
 * @property string $asal
 * @property string $tujuan
 * @property string $perihal
 * @property string $status
 * @property string $deskripsi
 */
class Laporan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tanggal', 'waktu', 'asal', 'tujuan', 'perihal', 'status'], 'required'],
            [['tanggal', 'waktu'], 'string', 'max' => 20],
            [['asal', 'tujuan', 'status', 'deskripsi'], 'string', 'max' => 50],
            [['perihal'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_laporan' => 'Id Laporan',
            'tanggal' => 'Tanggal',
            'waktu' => 'Waktu',
            'asal' => 'Asal',
            'tujuan' => 'Tujuan',
            'perihal' => 'Perihal',
            'status' => 'Status',
            'deskripsi' => 'Deskripsi',
        ];
    }

    //Fungsi untuk isi provinsi dan kabupaten/kota tabel ubahdata secara otomatis
    public function beforeSave($insert)
    {
        parent::beforeSave($insert);
        $this->tanggal = date('Y-m-d');
        $this->waktu = date('H:i:s');
        $this->asal = Yii::$app->user->identity->username; 
        if(Yii::$app->user->identity->role == 'provinsi'){
            $this->tujuan = 'pusat';
            $this->perihal = 'Cek hasil proyeksi';

            //Kirim email ke pusat bahwa hasil proyeksi dikirim
                Yii::$app->mailer->compose()
                    ->setTo('satriabgsp22@gmail.com')
                    ->setFrom('server@gmail.com')
                    ->setSubject('Hasil Proyeksi '.Yii::$app->user->identity->username.' (TESTING)')
                    ->setTextBody(Yii::$app->user->identity->username.' baru saja mengirim hasil proyeksi.')
                    ->send();

        }else if(Yii::$app->user->identity->role == 'kabkota'){
            //Ambil provinsi dari kabkota terkait.
                //Buat koneksi
                include "koneksi.php";

                //ambil nama wilayah dari id yang ada
                $id_wilayah = substr(Yii::$app->user->identity->wilayah_id, 0, 2);
                $sql_wil = "SELECT username, wilayah_id, email
                            FROM masuk
                            WHERE wilayah_id = '" . $id_wilayah . "00'";
                $query_wil = mysqli_query($host,$sql_wil) or die(mysqli_error());
                $tahun='';
                while($row = mysqli_fetch_array($query_wil)){ 
                    $nama_provinsi = $row['username'];
                    $id_provinsi = $row['wilayah_id'];
                    $email_provinsi = $row['email'];
                };
            $this->tujuan = $nama_provinsi;
            $this->perihal = 'Permintaan ubah data';

            //Kirim email ke provinsi bahwa hasil proyeksi dikirim
                Yii::$app->mailer->compose()
                    ->setTo($email_provinsi)
                    ->setFrom('server@gmail.com')
                    ->setSubject('Permintaan Ubah Data '.Yii::$app->user->identity->username.' (TESTING)')
                    ->setTextBody(Yii::$app->user->identity->username.' baru saja mengirim permintaan ubah data.')
                    ->send();
        };
        $this->status = 'BELUM DIPERIKSA';

        return true;
    }
}
