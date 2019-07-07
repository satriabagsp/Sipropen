<?php 
	
use yii\helpers\Html;
use app\models\DataKabKota;
use app\models\HasilProyeksi;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Url

?>

<div class="site-simpan">

    <?php if ($status == 'buat_baru'): ?>
        <?php
            //Mulai lakukan penghitungan proyeksi (SEMENTARA HANYA TOTAL TAHUNAN DULU)-->
            //Buat koneksi ke DB
                include "koneksi.php";
            //inisiasi variabel
                $i = 0;
                $sql_query = "SELECT *
                              FROM data_kabkota
                              WHERE provinsi = '" . Yii::$app->user->identity->username . "'";
                $query_mysql = mysqli_query($host,$sql_query) or die(mysqli_error());
                                            
            //Memasukan data dari database dalam bentuk ARRAY
                while($row = mysqli_fetch_array($query_mysql)){ 
                    $i++;
                    $id_provinsi[] = $row['id_prov'];
                    $provinsi[] = $row['provinsi'];
                    $id_kabkota[] = $row['id_kabkota'];
                    $kabkota[] = $row['kabkota'];
                    $p2010[] = $row['tot_jumlah_2010'];
                    $p2015[] = $row['tot_jumlah_2015'];
                    $r[] = round(pow($row['tot_jumlah_2015'] / $row['tot_jumlah_2010'] , 12/60) - 1 , 4); //Perhitungan LPP
                };
            
            //Perhitungan proyeksi untuk 2016 sampai 2035 (belum diprorata) dari data yang udah dijadiin Array di atas.
                for($tahun=2010;$tahun<=2035;$tahun++){ //Ini untuk inisiasi variabel total proyeksi tahunan.
                    ${'totproy_'.$tahun} = 0;
                };

                for($i=0;$i<count($kabkota);$i++){
                    for($tahun=2010;$tahun<=2035;$tahun++){
                        ${'proykab_'.$tahun}[$i] = round($p2015[$i]*pow((1+$r[$i]),$tahun-2015),0); //Ini penhitungan proyeksi per kabupaten per tahun.
                    };
                    
                    //Hitung jumlah proyeksi untuk semua kabupaten tiap tahun.
                    for($tahun=2010;$tahun<=2035;$tahun++){
                        ${'totproy_'.$tahun} = ${'proykab_'.$tahun}[$i]+${'totproy_'.$tahun}; //Ini hitung total proyeksi tahunan semua kabkot.
                    };
                };

            //Simpan hasil proyeksi ke database.
                //Buat nama2 kolom di tabel hasil proyeksi jadi 1 array
                    $kolomhasil = array();
                    $kolomhasil[0] = 'provinsi';
                    $kolomhasil[1] = 'kab_kota';
                    for($k=2;$k<13;$k++){
                        $thn = 2013+$k;
                        $kolomhasil[$k] = 'p'.$thn;
                    };
                            
                //Buat isi2 tabel hasil proyeksi jadi 1 array
                    $datahasil = array();
                    for($k=0;$k<$i;$k++){
                        $datahasil[$k] = [
                            Yii::$app->user->identity->username,
                            $kabkota[$k],
                            $proykab_2015[$k],
                            $proykab_2016[$k],
                            $proykab_2017[$k],
                            $proykab_2018[$k],
                            $proykab_2019[$k],
                            $proykab_2020[$k],
                            $proykab_2021[$k],
                            $proykab_2022[$k],
                            $proykab_2023[$k],
                            $proykab_2024[$k],
                            $proykab_2025[$k]
                        ];
                    };                             
                
                //disimpan ke database
                    Yii::$app->db->createCommand()->batchInsert('hasil_proyeksi', $kolomhasil, $datahasil)->execute();

                //Langsung loncat ke halaman hasil proyeksi
                    return Yii::$app->response->redirect(Url::to(['/hasil-proyeksi/index']));

        ?>

    <?php elseif ($status == 'buat_ulang'): ?>
        <?php
            //Hapus hasil proyeksi yang sudah pernah dibuat-->
            //Buat koneksi ke DB
                    include "koneksi.php";
                    $sql_hapusHasil = "DELETE FROM hasil_proyeksi WHERE provinsi = '" . Yii::$app->user->identity->username . "'";
                    $query_hapusHasil = mysqli_query($host,$sql_hapusHasil) or die(mysqli_error());
                    $sql_hapusPengiriman = "DELETE FROM laporan WHERE perihal = 'Cek hasil proyeksi' AND asal = '" . Yii::$app->user->identity->username . "'";
                    $query_hapusPengiriman = mysqli_query($host,$sql_hapusPengiriman) or die(mysqli_error());

            //Mulai lakukan penghitungan proyeksi (SEMENTARA HANYA TOTAL TAHUNAN DULU)-->
            //Buat koneksi ke DB
                include "koneksi.php";
            //inisiasi variabel
                $i = 0;
                $sql_query = "SELECT *
                              FROM data_kabkota
                              WHERE provinsi = '" . Yii::$app->user->identity->username . "'";
                $query_mysql = mysqli_query($host,$sql_query) or die(mysqli_error());
                                            
            //Memasukan data dari database dalam bentuk ARRAY
                while($row = mysqli_fetch_array($query_mysql)){ 
                    $i++;
                    $id_provinsi[] = $row['id_prov'];
                    $provinsi[] = $row['provinsi'];
                    $id_kabkota[] = $row['id_kabkota'];
                    $kabkota[] = $row['kabkota'];
                    $p2010[] = $row['tot_jumlah_2010'];
                    $p2015[] = $row['tot_jumlah_2015'];
                    $r[] = round(pow($row['tot_jumlah_2015'] / $row['tot_jumlah_2010'] , 12/60) - 1 , 4); //Perhitungan LPP
                };
            
            //Perhitungan proyeksi untuk 2016 sampai 2035 (belum diprorata) dari data yang udah dijadiin Array di atas.
                for($tahun=2010;$tahun<=2035;$tahun++){ //Ini untuk inisiasi variabel total proyeksi tahunan.
                    ${'totproy_'.$tahun} = 0;
                };

                for($i=0;$i<count($kabkota);$i++){
                    for($tahun=2010;$tahun<=2035;$tahun++){
                        ${'proykab_'.$tahun}[$i] = round($p2015[$i]*pow((1+$r[$i]),$tahun-2015),0); //Ini penhitungan proyeksi per kabupaten per tahun.
                    };
                    
                    //Hitung jumlah proyeksi untuk semua kabupaten tiap tahun.
                    for($tahun=2010;$tahun<=2035;$tahun++){
                        ${'totproy_'.$tahun} = ${'proykab_'.$tahun}[$i]+${'totproy_'.$tahun}; //Ini hitung total proyeksi tahunan semua kabkot.
                    };
                };

            //Simpan hasil proyeksi ke database.
                //Buat nama2 kolom di tabel hasil proyeksi jadi 1 array
                    $kolomhasil = array();
                    $kolomhasil[0] = 'provinsi';
                    $kolomhasil[1] = 'kab_kota';
                    for($k=2;$k<13;$k++){
                        $thn = 2013+$k;
                        $kolomhasil[$k] = 'p'.$thn;
                    };
                            
                //Buat isi2 tabel hasil proyeksi jadi 1 array
                    $datahasil = array();
                    for($k=0;$k<$i;$k++){
                        $datahasil[$k] = [
                            Yii::$app->user->identity->username,
                            $kabkota[$k],
                            $proykab_2015[$k],
                            $proykab_2016[$k],
                            $proykab_2017[$k],
                            $proykab_2018[$k],
                            $proykab_2019[$k],
                            $proykab_2020[$k],
                            $proykab_2021[$k],
                            $proykab_2022[$k],
                            $proykab_2023[$k],
                            $proykab_2024[$k],
                            $proykab_2025[$k]
                        ];
                    };                             
                
                //disimpan ke database
                    Yii::$app->db->createCommand()->batchInsert('hasil_proyeksi', $kolomhasil, $datahasil)->execute();

                //Langsung loncat ke halaman hasil proyeksi
                    return Yii::$app->response->redirect(Url::to(['/hasil-proyeksi/index']));

                echo "Ini buat ulang";

        ?>

    <?php elseif ($status == 'hapus'): ?>
        <?php
            //Hapus hasil proyeksi yang sudah pernah dibuat-->
                //Buat koneksi ke DB
                    include "koneksi.php";
                    $sql_hapusHasil = "DELETE FROM hasil_proyeksi WHERE provinsi = '" . Yii::$app->user->identity->username . "'";
                    $query_hapusHasil = mysqli_query($host,$sql_hapusHasil) or die(mysqli_error());
                    $sql_hapusPengiriman = "DELETE FROM laporan WHERE perihal = 'Cek hasil proyeksi' AND asal = '" . Yii::$app->user->identity->username . "'";
                    $query_hapusPengiriman = mysqli_query($host,$sql_hapusPengiriman) or die(mysqli_error());

            //Langsung loncat ke halaman hasil proyeksi
                    return Yii::$app->response->redirect(Url::to(['/hasil-proyeksi/index']));
        ?>

    <?php endif; ?>
    	

</div>


