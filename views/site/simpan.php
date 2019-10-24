<?php 
  
use yii\helpers\Html;
use app\models\DataKabKota;
use app\models\HasilProyeksi;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Url

?>

<div class="site-simpan">

    <?php if (Yii::$app->user->identity->role == 'provinsi'): ?>
        <!-- Menghapus hasil proyeksi provinsi terkait beserta laporan pengirimannya. -->
            <?php if ($stat == 'hapus_proyeksi'): //jika setuju maka mengubah status menjadi "Disetujui"?>
            <?php
              //Buat koneksi ke DB
                        include "koneksi.php";
                        $sql_hapusHasil = "DELETE FROM hasil_proyeksi WHERE provinsi = '" . Yii::$app->user->identity->username . "'";
                        $query_hapusHasil = mysqli_query($host,$sql_hapusHasil) or die(mysqli_error());
                        $sql_hapusPengiriman = "DELETE FROM laporan WHERE perihal = 'Cek hasil proyeksi' AND asal = '" . Yii::$app->user->identity->username . "'";
                        $query_hapusPengiriman = mysqli_query($host,$sql_hapusPengiriman) or die(mysqli_error());

                    //Langsung loncat ke halaman hasil proyeksi
                        return Yii::$app->response->redirect(Url::to(['/hasil-proyeksi/index']));
                ?>

        <!-- Melakukan pengelolaan permintaan ubah data, apakah disetujui, dihapus, atau buat baru. -->
          <?php
            //ambil email wilayah asal yang ada
              $sql_wil = "SELECT email
                          FROM pengguna
                          WHERE username = '" . $asal . "'";
              $query_wil = mysqli_query($host,$sql_wil) or die(mysqli_error());
              $tahun='';
              while($row = mysqli_fetch_array($query_wil)){ 
                $email_kabkota = $row['email'];
              };
          ?>

            <?php elseif ($stat == 'setuju'): //jika setuju maka mengubah status menjadi "Disetujui"?>
                <?php
                    //Buat koneksi ke DB dan run SQL
                        include "koneksi.php";
                        $sql_statusPeriksa = "UPDATE laporan
                                              SET status = 'DISETUJUI' 
                                              WHERE perihal = 'Permintaan ubah data' 
                                              AND asal = '" . $asal . "'
                                              AND deskripsi = '" . $jenis_kelamin . '-' . $tahun_dasar . "'";
                        $query_statusPeriksa = mysqli_query($host,$sql_statusPeriksa) or die(mysqli_error());

                    //Ambil data ubahan dari kabkota, tahun, dan jk terkait.
                        $sql_dataUbahan = "SELECT *
                                          FROM ubah_data_kabkota
                                          WHERE id_wilayah = '". $id_wilayah ."'
                                          AND jenis_kelamin = '". $jenis_kelamin ."'
                                          AND tahun_data = '" . $tahun_dasar . "'";
                        $query_dataUbahan = mysqli_query($host,$sql_dataUbahan) or die(mysqli_error());
                        while($row = mysqli_fetch_array($query_dataUbahan)){ 
                            for($umur=5;$umur<81;$umur=$umur+5){
                                ${'ku_'.$umur.'_ubahan'} = $row['ku_'.$umur];
                            }; //Ini variabel jumlah penduduk perempuan per kelompok umur per kabupaten tahun dasar dan tahun target
                        };

                    //ubah data kabkota sesuai data ubahan yang telahtersimpan di $ku_umur_ubahan
                        $sql_ubah = "UPDATE data_kabkota_lp
                                    SET ku_5 = '" . $ku_5_ubahan . "',
                                        ku_10 = '" . $ku_10_ubahan . "',
                                        ku_15 = '" . $ku_15_ubahan . "',
                                        ku_20 = '" . $ku_20_ubahan . "',
                                        ku_25 = '" . $ku_25_ubahan . "',
                                        ku_30 = '" . $ku_30_ubahan . "',
                                        ku_35 = '" . $ku_35_ubahan . "',
                                        ku_40 = '" . $ku_40_ubahan . "',
                                        ku_45 = '" . $ku_45_ubahan . "',
                                        ku_50 = '" . $ku_50_ubahan . "',
                                        ku_55 = '" . $ku_55_ubahan . "',
                                        ku_60 = '" . $ku_60_ubahan . "',
                                        ku_65 = '" . $ku_65_ubahan . "',
                                        ku_70 = '" . $ku_70_ubahan . "',
                                        ku_75 = '" . $ku_75_ubahan . "',
                                        ku_80 = '" . $ku_80_ubahan . "'
                                    WHERE id_wilayah = '". $id_wilayah ."' 
                                    AND jenis_kelamin = '". $jenis_kelamin ."'
                                    AND tahun_dasar = '" . $tahun_dasar . "'";
                        $query_ubah = mysqli_query($host,$sql_ubah) or die(mysqli_error());

                    //Simpan data di monitoring
                      $string = "Menyetujui permintaan ubah data {$id_wilayah} tahun {$tahun_dasar}";
                      $sql_tambah = "INSERT INTO monitoring (nama_wilayah, kegiatan, tanggal, waktu) 
                                     VALUE('".Yii::$app->user->identity->username."', 
                                           '".$string."',
                                           '".date('Y-m-d')."', 
                                           '".date('H:i:s')."')";
                      $query_tambah = mysqli_query($host,$sql_tambah) or die(mysqli_error());

                    //Kirim email ke kabkota terkait bahwa ubah data disetujui
                      Yii::$app->mailer->compose()
                          ->setTo($email_kabkota)
                          ->setFrom('server@gmail.com')
                          ->setSubject('Hasil Proyeksi '.$asal.' (TESTING)')
                          ->setTextBody('Permintaan ubah data '.$asal.' telah dsetujui.')
                          ->send();

                    //Langsung loncat ke halaman hasil proyeksi
                        return Yii::$app->response->redirect(Url::to(['/site/index']));
                    ?>

            <?php elseif ($stat == 'tolak'): //jika tolak maka mengubah status menjadi "Periksa kembali"?>
                <?php
                    //Buat koneksi ke DB dan run SQL
                        include "koneksi.php";
                        $sql_statusPeriksa = "UPDATE laporan
                                             SET status = 'PERIKSA KEMBALI' 
                                             WHERE perihal = 'Permintaan ubah data' 
                                             AND asal = '" . $asal . "'
                                             AND deskripsi = '" . $jenis_kelamin . '-' . $tahun_dasar . "'";
                        $query_statusPeriksa = mysqli_query($host,$sql_statusPeriksa) or die(mysqli_error());

                    //Simpan data di monitoring
                      $string = "Menolak permintaan ubah data {$id_wilayah} tahun {$tahun_dasar}";
                      $sql_tambah = "INSERT INTO monitoring (nama_wilayah, kegiatan, tanggal, waktu) 
                                     VALUE('".Yii::$app->user->identity->username."', 
                                           '".$string."',
                                           '".date('Y-m-d')."', 
                                           '".date('H:i:s')."')";
                      $query_tambah = mysqli_query($host,$sql_tambah) or die(mysqli_error());

                    //Kirim email ke kabkota terkait bahwa ubah data ditolak
                      Yii::$app->mailer->compose()
                          ->setTo($email_kabkota)
                          ->setFrom('server@gmail.com')
                          ->setSubject('Hasil Proyeksi '.$asal.' (TESTING)')
                          ->setTextBody('Permintaan ubah data '.$asal.' telah ditolak. Mohon periksa kembali data ubahan yang dibuat.')
                          ->send();

                    //Langsung loncat ke halaman hasil proyeksi
                        return Yii::$app->response->redirect(Url::to(['/site/index']));
                    ?>

            <?php elseif ($stat == 'batal'): //jika batal maka mengubah status menjadi "Belum diperiksa"?>
                <?php
                    //Buat koneksi ke DB dan run SQL
                        include "koneksi.php";
                        $sql_statusPeriksa = "UPDATE laporan
                                             SET status = 'BELUM DIPERIKSA' 
                                             WHERE perihal = 'Permintaan ubah data' 
                                             AND asal = '" . $asal . "'
                                             AND deskripsi = '" . $jenis_kelamin . '-' . $tahun_dasar . "'";
                        $query_statusPeriksa = mysqli_query($host,$sql_statusPeriksa) or die(mysqli_error());

                    //Simpan data di monitoring
                      $string = "Batal menolak permintaan ubah data {$id_wilayah} tahun {$tahun_dasar}";
                      $sql_tambah = "INSERT INTO monitoring (nama_wilayah, kegiatan, tanggal, waktu) 
                                     VALUE('".Yii::$app->user->identity->username."', 
                                           '".$string."',
                                           '".date('Y-m-d')."', 
                                           '".date('H:i:s')."')";
                      $query_tambah = mysqli_query($host,$sql_tambah) or die(mysqli_error());

                    //Kirim email ke kabkota terkait bahwa ubah data ditolak
                      Yii::$app->mailer->compose()
                          ->setTo($email_kabkota)
                          ->setFrom('server@gmail.com')
                          ->setSubject('Hasil Proyeksi '.$asal.' (TESTING)')
                          ->setTextBody('Status terakhir permintaan ubah data '.$asal.' telah dibatalkan. Mohon tunggu updating status selanjutnya.')
                          ->send();

                    //Langsung loncat ke halaman hasil proyeksi
                        return Yii::$app->response->redirect(Url::to(['/site/index']));
                    ?>

            <?php endif; ?>


    <?php elseif (Yii::$app->user->identity->role == 'kabkota'): ?>

        <?php if ($stat == 'hapus_laporan'): ?>
        <!-- Menghapus permintaan ubah data kabupaten/kota terkait beserta laporan permintaannya. -->
            <?php
                //Buat koneksi ke DB
                    include "koneksi.php";
                    $sql_hapusHasil = "DELETE 
                                       FROM ubah_data_kabkota 
                                       WHERE id_wilayah = '" . Yii::$app->user->identity->wilayah_id . "'
                                       AND jenis_kelamin = '" . $jenis_kelamin . "'
                                       AND tahun_data = '" . $tahun_dasar . "'";
                    $query_hapusHasil = mysqli_query($host,$sql_hapusHasil) or die(mysqli_error());
                    $sql_hapusPengiriman = "DELETE 
                                            FROM laporan 
                                            WHERE perihal = 'Permintaan ubah data' 
                                            AND asal = '" . Yii::$app->user->identity->username . "'
                                            AND deskripsi = '" . $jenis_kelamin.'-'.$tahun_dasar . "'";
                    $query_hapusPengiriman = mysqli_query($host,$sql_hapusPengiriman) or die(mysqli_error());

                //Simpan data di monitoring
                      $string = "Menghapus permintaan ubah data tahun {$tahun_dasar}";
                      $sql_tambah = "INSERT INTO monitoring (nama_wilayah, kegiatan, tanggal, waktu) 
                                     VALUE('".Yii::$app->user->identity->username."', 
                                           '".$string."',
                                           '".date('Y-m-d')."', 
                                           '".date('H:i:s')."')";
                      $query_tambah = mysqli_query($host,$sql_tambah) or die(mysqli_error());

                //Langsung loncat ke data penduduk kab/kota
                    return Yii::$app->response->redirect(Url::to(['/data-kabkota-lp/view', 'id' => $id_wilayah]));
            ?>

        <?php elseif ($stat == 'kirim_ulang'): //jika kirim_ulang maka mengubah status menjadi "Belum diperiksa"?>
            <?php
                //Buat koneksi ke DB dan run SQL
                    include "koneksi.php";
                    $sql_statusPeriksa = "UPDATE laporan
                                          SET status = 'BELUM DIPERIKSA' 
                                          WHERE perihal = 'Permintaan ubah data' 
                                          AND asal = '" . $asal . "'
                                          AND deskripsi = '" . $jenis_kelamin . '-' . $tahun_dasar . "'";
                    $query_statusPeriksa = mysqli_query($host,$sql_statusPeriksa) or die(mysqli_error());

                //Simpan data di monitoring
                      $string = "Kirim ulang permintaan ubah data tahun {$tahun_dasar}";
                      $sql_tambah = "INSERT INTO monitoring (nama_wilayah, kegiatan, tanggal, waktu) 
                                     VALUE('".Yii::$app->user->identity->username."', 
                                           '".$string."',
                                           '".date('Y-m-d')."', 
                                           '".date('H:i:s')."')";
                      $query_tambah = mysqli_query($host,$sql_tambah) or die(mysqli_error());

                //Langsung loncat ke halaman hasil proyeksi
                    return Yii::$app->response->redirect(Url::to(['/site/index']));
            ?>

        <?php endif; ?>



    <?php elseif (Yii::$app->user->identity->role == 'pusat'): ?>
        <!-- Melakukan pengelolaan data laporan hasil proyeksi, apakah disetujui, dihapus, atau buat baru. -->
          <?php
          //ambil email wilayah asal yang ada
            include "koneksi.php";
            $sql_wil = "SELECT email
                        FROM pengguna
                        WHERE username = '" . $asal . "'";
            $query_wil = mysqli_query($host,$sql_wil) or die(mysqli_error());
            while($row = mysqli_fetch_array($query_wil)){ 
              $email_provinsi = $row['email'];
            };
          ?>

            <?php if ($stat == 'setuju'): //jika setuju maka mengubah status menjadi "Disetujui"?>
                <?php
                    //Buat koneksi ke DB dan run SQL
                        $sql_statusPeriksa = "UPDATE laporan
                                          SET status = 'DISETUJUI' 
                                          WHERE perihal = 'Cek hasil proyeksi' 
                                          AND asal = '" . $asal . "'";
                        $query_statusPeriksa = mysqli_query($host,$sql_statusPeriksa) or die(mysqli_error());

                    //Simpan data di monitoring
                      $string = "Menyetujui hasil proyeksi {$asal}";
                      $sql_tambah = "INSERT INTO monitoring (nama_wilayah, kegiatan, tanggal, waktu) 
                                     VALUE('".Yii::$app->user->identity->username."', 
                                           '".$string."',
                                           '".date('Y-m-d')."', 
                                           '".date('H:i:s')."')";
                      $query_tambah = mysqli_query($host,$sql_tambah) or die(mysqli_error());

                    //Kirim email ke provinsi terkait bahwa hasil proyeksi disetujui
                      Yii::$app->mailer->compose()
                          ->setTo($email_provinsi)
                          ->setFrom('server@gmail.com')
                          ->setSubject('Hasil Proyeksi '.$asal.' (TESTING)')
                          ->setTextBody('Hasil proyeksi penduduk kabupaten/kota di '.$asal.' telah disetujui.')
                          ->send();

                    //Langsung loncat ke halaman hasil proyeksi
                        return Yii::$app->response->redirect(Url::to(['index']));
                    ?>

            <?php elseif ($stat == 'tolak'): //jika tolak maka mengubah status menjadi "Periksa kembali"?>
                <?php
                    //Buat koneksi ke DB dan run SQL
                        include "koneksi.php";
                        $sql_statusPeriksa = "UPDATE laporan
                                             SET status = 'PERIKSA KEMBALI' 
                                             WHERE perihal = 'Cek hasil proyeksi' 
                                             AND asal = '" . $asal . "'";
                        $query_statusPeriksa = mysqli_query($host,$sql_statusPeriksa) or die(mysqli_error());

                    //Simpan data di monitoring
                      $string = "Menolak hasil proyeksi {$asal}";
                      $sql_tambah = "INSERT INTO monitoring (nama_wilayah, kegiatan, tanggal, waktu) 
                                     VALUE('".Yii::$app->user->identity->username."', 
                                           '".$string."',
                                           '".date('Y-m-d')."', 
                                           '".date('H:i:s')."')";
                      $query_tambah = mysqli_query($host,$sql_tambah) or die(mysqli_error());

                    //Kirim email ke provinsi terkait bahwa hasil proyeksi ditolak
                      Yii::$app->mailer->compose()
                          ->setTo($email_provinsi)
                          ->setFrom('server@gmail.com')
                          ->setSubject('Hasil Proyeksi '.$asal.' (TESTING)')
                          ->setTextBody('Hasil proyeksi penduduk kabupaten/kota di '.$asal.' telah ditolak. Mohon periksa kembali penghitungan proyeksi yang dibuat.')
                          ->send();

                    //Langsung loncat ke halaman hasil proyeksi
                        return Yii::$app->response->redirect(Url::to(['index']));
                    ?>

            <?php elseif ($stat == 'batal'): //jika batal maka mengubah status menjadi "Belum diperiksa"?>
                <?php
                    //Buat koneksi ke DB dan run SQL
                        include "koneksi.php";
                        $sql_statusPeriksa = "UPDATE laporan
                                             SET status = 'BELUM DIPERIKSA' 
                                             WHERE perihal = 'Cek hasil proyeksi' 
                                             AND asal = '" . $asal . "'";
                        $query_statusPeriksa = mysqli_query($host,$sql_statusPeriksa) or die(mysqli_error());

                    //Simpan data di monitoring
                      $string = "Batal menolak hasil proyeksi {$asal}";
                      $sql_tambah = "INSERT INTO monitoring (nama_wilayah, kegiatan, tanggal, waktu) 
                                     VALUE('".Yii::$app->user->identity->username."', 
                                           '".$string."',
                                           '".date('Y-m-d')."', 
                                           '".date('H:i:s')."')";
                      $query_tambah = mysqli_query($host,$sql_tambah) or die(mysqli_error());

                    //Kirim email ke provinsi terkait bahwa hasil proyeksi batal ditolak
                      Yii::$app->mailer->compose()
                          ->setTo($email_provinsi)
                          ->setFrom('server@gmail.com')
                          ->setSubject('Hasil Proyeksi '.$asal.' (TESTING)')
                          ->setTextBody('Status tolak hasil proyeksi penduduk kabupaten/kota di '.$asal.' telah dibatalkan. Silakan tunggu update status berikutnya.')
                          ->send();

                    //Langsung loncat ke halaman hasil proyeksi
                        return Yii::$app->response->redirect(Url::to(['index']));
                    ?>

            <?php endif; ?>

    <?php endif; ?>

</div>


