<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Laporan;
use app\models\HasilProyeksi;
use app\models\Monitoring;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use app\controllers\HasilProyeksiController; 

//Inisiasi variabel untuk menampilkan tabel laporan.
    //Buat ngefilter data yang ditampilin di tabel, harus sesuai dengan daerahnya.
        if (Yii::$app->user->identity->role == 'provinsi'){ //buat nampilin permintaan ubah data aja.
            $role = Laporan::find()->where(['tujuan' => Yii::$app->user->identity->username]);
        } else if (Yii::$app->user->identity->role == 'kabkota'){
            $role = Laporan::find()->where(['asal' => Yii::$app->user->identity->username]);
        } else {
            $role = Laporan::find()->where(['tujuan' => Yii::$app->user->identity->username]);
        };

        $dataProvider = new ActiveDataProvider([
            'query' => $role
            ,'sort' => [
                'defaultOrder' => [
                'tanggal' => SORT_ASC
                ]
            ]
        ]);

    //Buat ngefilter laporan permintaan perubahan untuk provinsi aja.
        $dataProvider2 = new ActiveDataProvider([
            'query' => Laporan::find()->where(['asal' => Yii::$app->user->identity->username])
            ,'sort' => [
                'defaultOrder' => [
                'tanggal' => SORT_ASC
                ]
            ]
        ]);

    //Buat nampilin tabel monitoring
        $dataMonitoring = new ActiveDataProvider([
            'query' => Monitoring::find()
            ,'sort' => [
                'defaultOrder' => [
                'tanggal' => SORT_ASC
                ]
            ]
        ]);

$this->title = 'Sistem Proyeksi Penduduk';
?>
<div class="site-index">
    <div class="panel panel-info">
     	<div class="panel-heading"><h1>Dashboard</h1></div>
        <div class="panel-body">
            <div class="container-fixed">

         		<?php if (Yii::$app->user->identity->role == 'provinsi'): ?>

                    <?php
                        $tahun = date('Y');
                        //buat tabel untuk ngeliatin data.
                            //Buat koneksi ke DB
                            //Ambil data dari tabel kab/kota
                                include "koneksi.php";
                                $id_provinsi = substr(Yii::$app->user->identity->wilayah_id, 0, 2);
                                $sql_AmbilData = "SELECT id_wilayah
                                                  FROM data_kabkota_lp
                                                  WHERE SUBSTRING(id_wilayah,1,2) = '" . $id_provinsi . "'
                                                  AND jenis_kelamin = 'l'
                                                  AND tahun_dasar = '2015'";
                                $query_AmbilData = mysqli_query($host,$sql_AmbilData) or die(mysqli_error());
                                $jumlah_kabkota='';
                                while($row = mysqli_fetch_array($query_AmbilData)){ 
                                    $jumlah_kabkota++;
                                };

                            //Ambil data dari tabel laporan
                                $sql_status = "SELECT status
                                              FROM laporan
                                              WHERE perihal = 'Cek hasil proyeksi' 
                                              AND asal = '".Yii::$app->user->identity->username."'";
                                $query_status = mysqli_query($host,$sql_status) or die(mysqli_error());
                                                    
                                //Memasukan data dari database ke variabel
                                    $status = mysqli_fetch_array($query_status);

                            //Cek apakah sudah membuat proyeksi atau belum
                                $id_provinsi = substr(Yii::$app->user->identity->wilayah_id, 0, 2);
                                //Ambil data dari tabel hasil_proyeksi
                                //Ambil per kabupaten/kota tahun saat ini
                                $sql_dataProyeksi = "SELECT *
                                                    FROM hasil_proyeksi_jumlah, master_wilayah
                                                    WHERE hasil_proyeksi_jumlah.id_wilayah = master_wilayah.id_wilayah
                                                    AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'";  
                                $query_dataProyeksi = mysqli_query($host,$sql_dataProyeksi) or die(mysqli_error());
                                $cek='';
                                while($row = mysqli_fetch_array($query_dataProyeksi)){ 
                                    $cek++;
                                };

                            if($cek){
                            //Ambil data dari tabel hasil_proyeksi
                                $id_provinsi = substr(Yii::$app->user->identity->wilayah_id, 0, 2);
                                //Ambil data dari tabel hasil_proyeksi
                                //Ambil per kabupaten/kota tahun saat ini
                                $sql_dataProyeksi = "SELECT *
                                                    FROM hasil_proyeksi_jumlah, master_wilayah
                                                    WHERE hasil_proyeksi_jumlah.id_wilayah = master_wilayah.id_wilayah
                                                    AND hasil_proyeksi_jumlah.tahun_proyeksi = '" . $tahun . "'
                                                    AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'";  
                                $query_dataProyeksi = mysqli_query($host,$sql_dataProyeksi) or die(mysqli_error());
                                while($row = mysqli_fetch_array($query_dataProyeksi)){ 
                                    $data_proyeksi[] = $row['jumlah_proyeksi'];
                                };
                                $jumlah_nilai_proyeksi = array_sum($data_proyeksi);
                            } elseif(!$cek){
                                $jumlah_nilai_proyeksi = 0;
                            };

                                $sql_AmbilTerbesar = "SELECT * 
                                                     FROM hasil_proyeksi_jumlah, master_wilayah
                                                     WHERE SUBSTRING(hasil_proyeksi_jumlah.id_wilayah,1,2) = '" . $id_provinsi . "'
                                                     AND hasil_proyeksi_jumlah.id_wilayah = master_wilayah.id_wilayah
                                                     AND tahun_proyeksi = '".$tahun."'
                                                     ORDER BY jumlah_proyeksi DESC LIMIT 1"; //Ambil data terbesar
                                $query_AmbilTerbesar = mysqli_query($host,$sql_AmbilTerbesar) or die(mysqli_error());

                                $sql_AmbilTerkecil = "SELECT * 
                                                     FROM hasil_proyeksi_jumlah, master_wilayah
                                                     WHERE SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'
                                                     AND hasil_proyeksi_jumlah.id_wilayah = master_wilayah.id_wilayah
                                                     AND tahun_proyeksi = '".$tahun."'
                                                     ORDER BY jumlah_proyeksi ASC LIMIT 1"; //Ambil data terbesar
                                $query_AmbilTerkecil = mysqli_query($host,$sql_AmbilTerkecil) or die(mysqli_error());

                                //Memasukan data dari database ke variabel
                                    $terbesar = mysqli_fetch_array($query_AmbilTerbesar);
                                    $terkecil = mysqli_fetch_array($query_AmbilTerkecil);
                        ?>

                    <div class="kiri_main">
                        <div class="row">        
                            <div class="col-lg-4 col-md-4">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-12 text-right">
                                                <div><font size=6px><?php echo number_format($terbesar['jumlah_proyeksi']) ?></font></div>
                                                <div><font size=2px><b><?php echo $terbesar['nama_wilayah'] ?></b></font></div>
                                                <div>-----------------------------</div>
                                                <div><b>Penduduk Terbanyak <?php echo $tahun ?></b></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="panel panel-danger">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-12 text-right">
                                                <div><font size=6px><?php echo number_format($jumlah_nilai_proyeksi) ?></font></div>
                                                <div><font size=2px><b><?php echo Yii::$app->user->identity->username ?></b></font></div>
                                                <div>-----------------------------</div>
                                                <div><b>Jumlah Penduduk <?php echo $tahun ?></b></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <div class="panel panel-success">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-12 text-right">
                                                <div><font size=6px><?php echo number_format($terkecil['jumlah_proyeksi']) ?></font></div>
                                                <div><font size=2px><b><?php echo $terkecil['nama_wilayah'] ?></b></font></div>
                                                <div>-----------------------------</div>
                                                <div><b>Penduduk Tersedikit <?php echo $tahun ?></b></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p><b>Berikut adalah laporan status permintaan ubah data master kabupaten/kota:</b></p>
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'options' => ['style' => 'font-size:12px;'],
                            'showFooter' => true,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],
                            
                                'tanggal',
                                'waktu',
                                'asal',
                                //'tujuan',
                                'perihal',
                                'status',
                                'deskripsi',
                                [
                                    'label' => 'Pilihan',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        return Html::a('Lihat', ['ubah-data-kabkota/view', 'nama_wilayah' => $model->asal, 'desk' => $model->deskripsi]);
                                    },
                                ],
                                ],
                            ]); ?>
                    </div>
                    <div class="kanan_main" style="background: #efe5b6; margin-top: 0px; margin-left: 0px; padding-top: 10px; padding-bottom: 10px; border:2px solid gold; color:black;"> 
                        <p><b><center><font size="5px"><?php echo Yii::$app->user->identity->username ?></font></center></b></p>
                        <br>

                        <center><font size="2px"><table class="table">
                            <tr>
                                <td style="text-align:left;"><b>Jumlah kab/kota:</b></td>
                                <td style="text-align:left;"><b><?php echo $jumlah_kabkota ?></b></td>
                            </tr>
                            <tr>
                                
                            </tr>
                            <tr>
                                <td style="text-align:left;"><b>Status proyeksi:</b></td>
                                <?php if ($status['status'] == 'DISETUJUI'): ?>
                                    <td style="text-align:left;"><b><font color=green><?php echo $status['status'] ?></font></b></td>
                                <?php elseif ($status['status'] == 'BELUM DIPERIKSA' || $status['status'] == 'PERIKSA KEMBALI'): ?>
                                    <td style="text-align:left;"><b><font color=red><?php echo $status['status'] ?></font></b></td>
                                <?php elseif (!$status['status']): ?>
                                    <td style="text-align:left;"><b>Belum kirim proyeksi</b></td>
                                <?php endif; ?>
                            </tr>
                            <tr>
                                <td style="text-align:left;"></td><td style="text-align:left;"></td>
                        </table></font></center>
                    </div>

                <?php elseif (Yii::$app->user->identity->role == 'kabkota'): ?>

                    <?php
                        $tahun = date('Y');
                        //buat tabel untuk ngeliatin data.
                            //Buat koneksi ke DB
                            //Ambil nama provinsi
                                include "koneksi.php";
                                $id_provinsi = substr(Yii::$app->user->identity->wilayah_id, 0, 2);
                                $sql_AmbilData = "SELECT nama_wilayah
                                                  FROM master_wilayah
                                                  WHERE id_wilayah = '".$id_provinsi."00'";
                                $query_AmbilData = mysqli_query($host,$sql_AmbilData) or die(mysqli_error());
                                $jumlah_kabkota='';
                                while($row = mysqli_fetch_array($query_AmbilData)){ 
                                    $nama_provinsi = $row['nama_wilayah'];
                                };

                            //Ambil data dari tabel laporan
                                $sql_status = "SELECT status
                                              FROM laporan
                                              WHERE perihal = 'Cek hasil proyeksi' 
                                              AND asal = '".$nama_provinsi."'";
                                $query_status = mysqli_query($host,$sql_status) or die(mysqli_error());
                                                    
                                //Memasukan data dari database ke variabel
                                    $status = mysqli_fetch_array($query_status);

                            //Cek apakah sudah membuat proyeksi atau belum
                                //Ambil data dari tabel hasil_proyeksi
                                $sql_dataProyeksi = "SELECT *
                                                    FROM hasil_proyeksi_jumlah
                                                    WHERE id_wilayah = '" . Yii::$app->user->identity->wilayah_id . "'";  
                                $query_dataProyeksi = mysqli_query($host,$sql_dataProyeksi) or die(mysqli_error());
                                $cek='';
                                while($row = mysqli_fetch_array($query_dataProyeksi)){ 
                                    $cek++;
                                };

                            if($cek){
                            //Ambil data dari tabel hasil_proyeksi
                                $id_provinsi = substr(Yii::$app->user->identity->wilayah_id, 0, 2);
                                //Ambil data laki + perempuan dari tabel hasil_proyeksi
                                $sql_dataProyeksi = "SELECT *
                                                    FROM hasil_proyeksi_jumlah
                                                    WHERE id_wilayah = '" . Yii::$app->user->identity->wilayah_id . "'
                                                    AND jenis_kelamin = 'l+p'
                                                    AND tahun_proyeksi = '".$tahun."'";  
                                $query_dataProyeksi = mysqli_query($host,$sql_dataProyeksi) or die(mysqli_error());
                                while($row = mysqli_fetch_array($query_dataProyeksi)){ 
                                    $data_lp = $row['jumlah_proyeksi'];
                                };
                                //Ambil data laki dari tabel hasil_proyeksi
                                $sql_dataProyeksi = "SELECT *
                                                    FROM hasil_proyeksi_jumlah
                                                    WHERE id_wilayah = '" . Yii::$app->user->identity->wilayah_id . "'
                                                    AND jenis_kelamin = 'l'
                                                    AND tahun_proyeksi = '".$tahun."'";  
                                $query_dataProyeksi = mysqli_query($host,$sql_dataProyeksi) or die(mysqli_error());
                                while($row = mysqli_fetch_array($query_dataProyeksi)){ 
                                    $data_l = $row['jumlah_proyeksi'];
                                };
                                //Ambil data perempuan dari tabel hasil_proyeksi
                                $sql_dataProyeksi = "SELECT *
                                                    FROM hasil_proyeksi_jumlah
                                                    WHERE id_wilayah = '" . Yii::$app->user->identity->wilayah_id . "'
                                                    AND jenis_kelamin = 'p'
                                                    AND tahun_proyeksi = '".$tahun."'";  
                                $query_dataProyeksi = mysqli_query($host,$sql_dataProyeksi) or die(mysqli_error());
                                while($row = mysqli_fetch_array($query_dataProyeksi)){ 
                                    $data_p = $row['jumlah_proyeksi'];
                                };
                            } elseif(!$cek){
                                $data_lp = 0;
                                $data_l = 0;
                                $data_p = 0;
                            };

                            //Ambil data dari tabel laporan
                                $id_wilayah = substr(Yii::$app->user->identity->wilayah_id, 0, 2);
                                $sql_status = "SELECT status
                                              FROM laporan, master_wilayah
                                              WHERE laporan.perihal = 'Cek hasil proyeksi' 
                                              AND laporan.asal = master_wilayah.nama_wilayah
                                              AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_wilayah . "'";
                                $query_status = mysqli_query($host,$sql_status) or die(mysqli_error());
                                                    
                                //Memasukan data dari database ke variabel
                                    $status = mysqli_fetch_array($query_status);
                        ?>

                    <div class="kiri_main">      

                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-12 text-right">
                                                <div><font size=6px><?php echo number_format($data_l + $data_p) ?></font></div>
                                                <div><font size=3px><b>Jumlah Penduduk <?php echo $tahun ?></b></font></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="panel panel-success">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-12 text-right">
                                                <div><font size=6px><?php echo number_format($data_l) ?></font></div>
                                                <div><font size=3px><b>Jumlah Laki-laki <?php echo $tahun ?></b></font></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="panel panel-warning">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-12 text-right">
                                                <div><font size=6px><?php echo number_format($data_p) ?></font></div>
                                                <div><font size=3px><b>Jumlah Perempuan <?php echo $tahun ?></b></font></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <p>Berikut adalah laporan status permintaan ubah data master:</p>
                            <?= GridView::widget([
                                'dataProvider' => $dataProvider,
                                'options' => ['style' => 'font-size:12px;'],
                                'showFooter' => true,
                                'columns' => [
                                    ['class' => 'yii\grid\SerialColumn'],
                                
                                    'tanggal',
                                    'waktu',
                                    //'asal',
                                    'tujuan',
                                    'perihal',
                                    'status',
                                    'deskripsi',

                                    [
                                    'label' => 'Pilihan',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        return Html::a('Lihat', ['ubah-data-kabkota/view', 'nama_wilayah' => $model->asal, 'desk' => $model->deskripsi]);
                                    },
                                ],

                                ],
                            ]); ?>  
         		    </div>
                    <div class="kanan_main" style="background: #efe5b6; margin-top: 0px; margin-left: 0px; padding-top: 10px; padding-bottom: 10px; border:2px solid gold; color:black;"> 
                        <p><b><center><font size="5px"><?php echo Yii::$app->user->identity->username ?></font></center></b></p>
                        <br>

                        <center><font size="2px"><table class="table">
                            <tr>
                                <td style="text-align:left;"><b>Provinsi:</b></td>
                                <td style="text-align:left;"><b><?php echo $nama_provinsi ?></b></td>
                            </tr>
                            <tr>
                                
                            </tr>
                            <tr>
                                <td style="text-align:left;"><b>Status proyeksi:</b></td>
                                <?php if ($status['status'] == 'DISETUJUI'): ?>
                                    <td style="text-align:left;"><b><font color=green><?php echo $status['status'] ?></font></b></td>
                                <?php elseif ($status['status'] == 'BELUM DIPERIKSA' || $status['status'] == 'PERIKSA KEMBALI'): ?>
                                    <td style="text-align:left;"><b><font color=red><?php echo $status['status'] ?></font></b></td>
                                <?php elseif (!$status['status']): ?>
                                    <td style="text-align:left;"><b>Belum kirim proyeksi</b></td>
                                <?php endif; ?>
                            </tr>
                            <tr>
                                <td style="text-align:left;"></td><td style="text-align:left;"></td>
                            </tr>
                        </table></font></center>
                    </div>

                <?php elseif (Yii::$app->user->identity->role == 'pusat'): ?>
                    <div> 
                        <p>Berikut adalah laporan status hasil proyeksi:</p>
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'options' => ['style' => 'font-size:12px;'],
                            'showFooter' => true,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],
                                
                                'tanggal',
                                'waktu',
                                'asal',
                                //'tujuan',
                                'perihal',
                                'status',
                                'deskripsi',

                                [
                                    'label' => 'Pilihan',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        return Html::a('Lihat', ['hasil-proyeksi-jumlah/lihat', 'provinsi_terpilih' => $model->asal, 'id_provinsi_terpilih' => '']);
                                    },
                                ],

                            ],
                        ]); ?>
                    </div>

                    <div> 
                        <p>Berikut adalah aktivitas yang dilakukan pengguna:</p>
                        <?= GridView::widget([
                            'dataProvider' => $dataMonitoring,
                            'options' => ['style' => 'font-size:12px;'],
                            'showFooter' => true,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],
                                
                                [
                                    'attribute' => 'nama_wilayah',
                                    'label' => 'Satuan Kerja',
                                ],
                                [
                                    'attribute' => 'kegiatan',
                                    'label' => 'Kegiatan',
                                ],
                                [
                                    'attribute' => 'tanggal',
                                    'label' => 'Tanggal',
                                ],
                                [
                                    'attribute' => 'waktu',
                                    'label' => 'Waktu',
                                ],

                            ],
                        ]); ?>
                    </div>

                <?php elseif (Yii::$app->user->identity->role == 'admin'): ?>

                    <div> 
                        <p>Berikut adalah aktivitas yang dilakukan pengguna:</p>
                        <?= GridView::widget([
                            'dataProvider' => $dataMonitoring,
                            'options' => ['style' => 'font-size:12px;'],
                            'showFooter' => true,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],
                                
                                [
                                    'attribute' => 'nama_wilayah',
                                    'label' => 'Satuan Kerja',
                                ],
                                [
                                    'attribute' => 'kegiatan',
                                    'label' => 'Kegiatan',
                                ],
                                [
                                    'attribute' => 'tanggal',
                                    'label' => 'Tanggal',
                                ],
                                [
                                    'attribute' => 'waktu',
                                    'label' => 'Waktu',
                                ],

                            ],
                        ]); ?>
                    </div>

                <?php endif; ?>

         	</div>
        </div>
    </div>   
</div>
