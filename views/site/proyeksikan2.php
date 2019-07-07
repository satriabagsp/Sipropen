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
            //Buat koneksi ke DB
                include "koneksi.php";

            //INISIASI VARIABEL TAHUN2 BAHAN PROYEKSI DAN BULAN PROYEKSI.
                $tahun_terpilih[] = $tahun_dasar;
                $tahun_terpilih[] = $tahun_target; 
                $periode_proyeksi = $panjang_tahun;
                $periode_bulan = 7 + 6 + (12*(($panjang_tahun-$tahun_target)-1));

            //MENGAMBIL NILAI DATA KABUPATEN/KOTA L+P, L, P DARI TABEL DATA_KABKOTA
              $id_provinsi = substr(Yii::$app->user->identity->wilayah_id, 0, 2);
              for($t=0;$t<count($tahun_terpilih);$t++){
                //Ambil jumlah laki2 per kabupaten/kota per tahun dari tabel data_kabkota
                    $sql_dataLaki2 = "SELECT *
                              FROM data_kabkota_lp, master_wilayah
                              WHERE SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'
                              AND data_kabkota_lp.id_wilayah = master_wilayah.id_wilayah
                              AND data_kabkota_lp.jenis_kelamin = 'l'
                              AND data_kabkota_lp.tahun_dasar = '" . $tahun_terpilih[$t] . "'";  
                    $query_dataLaki2 = mysqli_query($host,$sql_dataLaki2) or die(mysqli_error());

                    $i='';
                    unset($nama_wilayah);
                    unset($id_wilayah);
                    while($row = mysqli_fetch_array($query_dataLaki2)){ 
                      $i++; //jumlah kabkota yang terdaftar di provinsi tersebut.
                      $nama_wilayah[] = $row['nama_wilayah'];
                      $id_wilayah[] = $row['id_wilayah'];
                      for($umur=5;$umur<81;$umur=$umur+5){
                        ${'ku_'.$umur.'_laki2_'.$tahun_terpilih[$t]}[] = $row['ku_'.$umur];
                      };
                    };
                    
                    for($j=0;$j<$i;$j++){
                        $b=0;
                        for($umur=5;$umur<81;$umur=$umur+5){
                            $b = ${'ku_'.$umur.'_laki2_'.$tahun_terpilih[$t]}[$j] + $b;
                            ${'jumlah_laki2_'.$tahun_terpilih[$t]}[$j] = $b;
                            ${'jumlah_ku_'.$umur.'_laki2_'.$tahun_terpilih[$t]} = array_sum(${'ku_'.$umur.'_laki2_'.$tahun_terpilih[$t]}); 
                        };
                    }; //Ini variabel jumlah penduduk laki-laki per provinsi tahun 2010 dan 2015

                //Ambil jumlah perempuan per kabupaten/kota per tahun dari tabel data_kabkota
                    $sql_dataPerempuan = "SELECT *
                                        FROM data_kabkota_lp, master_wilayah
                                        WHERE SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'
                                        AND data_kabkota_lp.id_wilayah = master_wilayah.id_wilayah
                                        AND data_kabkota_lp.jenis_kelamin = 'p'
                                        AND data_kabkota_lp.tahun_dasar = '" . $tahun_terpilih[$t] . "'"; 
                    $query_dataPerempuan = mysqli_query($host,$sql_dataPerempuan) or die(mysqli_error());

                    $i='';
                    while($row = mysqli_fetch_array($query_dataPerempuan)){ 
                        $i++;
                        for($umur=5;$umur<81;$umur=$umur+5){
                            ${'ku_'.$umur.'_perempuan_'.$tahun_terpilih[$t]}[] = $row['ku_'.$umur];
                        }; //Ini variabel jumlah penduduk perempuan per kelompok umur per kabupaten tahun 2010 dan 2015
                    };
                    
                    for($j=0;$j<$i;$j++){
                        $b=0;
                        for($umur=5;$umur<81;$umur=$umur+5){
                            $b = ${'ku_'.$umur.'_perempuan_'.$tahun_terpilih[$t]}[$j] + $b;
                            ${'jumlah_perempuan_'.$tahun_terpilih[$t]}[$j] = $b;
                            ${'jumlah_ku_'.$umur.'_perempuan_'.$tahun_terpilih[$t]} = array_sum(${'ku_'.$umur.'_perempuan_'.$tahun_terpilih[$t]});
                        }; //Ini variabel jumlah penduduk perempuan per kabupaten tahun 2010 dan 2015
                    };

                //Hitung jumlah penduduk per kabkota 2010 dan 2015
                  for($k=0;$k<$i;$k++){
                    ${'jumlah_penduduk_'.$tahun_terpilih[$t]}[$k] = ${'jumlah_laki2_'.$tahun_terpilih[$t]}[$k] + ${'jumlah_perempuan_'.$tahun_terpilih[$t]}[$k];
                  }
                
              };

            //Maka data jumlah penduduk per kabupaten/kota tahun 2010 ada di variabel array $jumlah_penduduk_2010, dan
            //data jumlah penduduk per kabupaten/kota tahun 2015 ada di variabel array $jumlah_penduduk_2015.
            //data kabupaten/kota di provinsi tersebut ada di array $nama_wilayah.


            //MENGAMBIL NILAI PROYEKSI TAHUNAN L+P, L, P DARI TABEL PROYEKSI_PROVINSI SEBAGAI KONTROL
                //Ambil tahun yang ada
                    $sql_tahun = "SELECT tahun_data
                                FROM data_provinsi_lp
                                GROUP BY tahun_data
                                having count(*)>1 ";
                    $query_tahun = mysqli_query($host,$sql_tahun) or die(mysqli_error());
                    $tahun='';
                    while($row = mysqli_fetch_array($query_tahun)){ 
                        $tahun++; //jumlah tahun yang ada.
                        $tahun_data[] = $row['tahun_data'];
                    };

                //Mulai ambil data
                    for($t=0;$t<count($tahun_data);$t++){
                        //Ambil jumlah laki2 per provinsi per tahun
                        $sql_dataLaki2 = "SELECT *
                                         FROM data_provinsi_lp
                                         WHERE id_wilayah = '" . Yii::$app->user->identity->wilayah_id . "'
                                         AND jenis_kelamin = 'l'
                                         AND tahun_data = '" . $tahun_data[$t] . "'"; 
                        $query_dataLaki2 = mysqli_query($host,$sql_dataLaki2) or die(mysqli_error());

                        while($row = mysqli_fetch_array($query_dataLaki2)){ 
                            for($umur=5;$umur<81;$umur=$umur+5){
                                ${'ku_'.$umur.'_prov_laki2_'.$tahun_data[$t]} = $row['ku_'.$umur];
                                ${'jumlah_prov_laki2_'.$tahun_data[$t]}[] = $row['ku_'.$umur];
                            }; //Ini variabel jumlah penduduk laki-laki per tahun per provinsi
                        };

                        //Ambil jumlah perempuan per kabupaten/kota per tahun
                        $sql_dataPerempuan = "SELECT *
                                                FROM data_provinsi_lp
                                                WHERE id_wilayah = '" . Yii::$app->user->identity->wilayah_id . "'
                                                AND jenis_kelamin = 'p'
                                                AND tahun_data = '" . $tahun_data[$t] . "'";  
                        $query_dataPerempuan = mysqli_query($host,$sql_dataPerempuan) or die(mysqli_error());

                        while($row = mysqli_fetch_array($query_dataPerempuan)){ 
                            for($umur=5;$umur<81;$umur=$umur+5){
                                ${'ku_'.$umur.'_prov_perempuan_'.$tahun_data[$t]} = $row['ku_'.$umur];
                                ${'jumlah_prov_perempuan_'.$tahun_data[$t]}[] = $row['ku_'.$umur];
                            }; //Ini variabel jumlah penduduk perempuan per tahun per provinsi
                        };
                                
                        //Hitung jumlah penduduk tiap tahunnya
                            ${'jumlah_provinsi_'.$tahun_data[$t]} = array_sum(${'jumlah_prov_laki2_'.$tahun_data[$t]}) + array_sum(${'jumlah_prov_perempuan_'.$tahun_data[$t]});
                            //Variabel Array $jumlah_provinsi_tahun berisi data provisi pertahun untuk wilayah dan tahun terkait.
                    };  
                        

                                        
            //PENGHITUNGAN LPP TAHUNAN, LPP BULANAN, DAN SEX RATIO.
                for($hit=0;$hit<count($id_wilayah);$hit++){
                    $lpp[] = pow(${'jumlah_penduduk_'.$tahun_target}[$hit] / ${'jumlah_penduduk_'.$tahun_dasar}[$hit] , 12/60) - 1; //Perhitungan LPP Tahunan.
                    $lpp_bulanan[] = pow(${'jumlah_penduduk_'.$tahun_target}[$hit] / ${'jumlah_penduduk_'.$tahun_dasar}[$hit] , 1/60) - 1; //Perhitungan LPP Bulanan.
                    $sr[] = ${'jumlah_laki2_'.$tahun_target}[$hit] / ${'jumlah_perempuan_'.$tahun_target}[$hit]; //Perhitungan lpp
                };
            
            //PENGHITUNGAN PROYEKSI TAHUNAN UNTUK L+P, L, P. (TANPA KELOMPOK UMUR)
                //Perhitungan proyeksi tahunan (belum diprorata) dari data yang udah dijadiin Array di atas.
                    for($tahun=$tahun_target;$tahun<=$periode_proyeksi;$tahun++){ //Ini untuk inisiasi variabel total proyeksi tahunan.
                        ${'totproy_'.$tahun} = 0;
                        ${'totproy_laki2_'.$tahun} = 0;
                        ${'totproy_perempuan_'.$tahun} = 0;
                    };

                    for($i=0;$i<count($id_wilayah);$i++){
                        //Hitung Tahunan Laki2+perempuan
                        $bulan = 3;
                        for($tahun=$tahun_target;$tahun<=$periode_proyeksi;$tahun++){
                            ${'proykab_'.$tahun}[$i] = floor(${'jumlah_penduduk_'.$tahun_target}[$i]*pow((1+$lpp[$i]),($bulan/24))); 
                            //Ini penghitungan proyeksi per kabupaten per tahun.
                            $bulan = $bulan + 24;
                        };

                        //Hitung Tahunan Laki2
                        for($tahun=$tahun_target;$tahun<=$periode_proyeksi;$tahun++){
                            ${'proykab_laki2_'.$tahun}[$i] = floor(( ${'proykab_'.$tahun}[$i] * $sr[$i] ) / (1 + $sr[$i])); 
                            //Ini penhitungan proyeksi laki2 per kabupaten per tahun -> Total dikali SR
                        };
                        
                        //Hitung Tahunan Perempuan
                        for($tahun=$tahun_target;$tahun<=$periode_proyeksi;$tahun++){
                            ${'proykab_perempuan_'.$tahun}[$i] = floor(${'proykab_'.$tahun}[$i] - ${'proykab_laki2_'.$tahun}[$i]); 
                            //Ini penhitungan proyeksi perempuan per kabupaten per tahun -> total dikurangi laki2
                        };

                        //Hitung jumlah proyeksi untuk semua kabupaten tiap tahun.
                        for($tahun=$tahun_target;$tahun<=$periode_proyeksi;$tahun++){
                            ${'totproy_'.$tahun} = ${'proykab_'.$tahun}[$i]+${'totproy_'.$tahun}; 
                            ${'totproy_laki2_'.$tahun} = ${'proykab_laki2_'.$tahun}[$i]+${'totproy_laki2_'.$tahun}; 
                            ${'totproy_perempuan_'.$tahun} = ${'proykab_perempuan_'.$tahun}[$i]+${'totproy_perempuan_'.$tahun}; 
                        };
                    };

                //Hitung Prorata Untuk Jumlah Penduduk seluruhnya (tanpa melibatkan kelompok umur).
                    //Hitung jumlah proyeksi untuk semua kabupaten tiap tahun.
                    for($tahun=$tahun_target;$tahun<=$periode_proyeksi;$tahun++){ //Ini untuk inisiasi variabel total prorata proyeksi tahunan.
                        ${'tot_prorata_'.$tahun} = 0;
                        ${'tot_prorata_laki2_'.$tahun} = 0;
                        ${'tot_prorata_perempuan_'.$tahun} = 0;
                    };  

                    for($i=0;$i<count($id_wilayah);$i++){
                        //Hitung prorata hasil proyeksi laki2+perempuan dan laki2 per kabupaten per tahun.
                        for($tahun=$tahun_target;$tahun<=$periode_proyeksi;$tahun++){
                            ${'prorata_'.$tahun}[$i] = floor((${'proykab_'.$tahun}[$i] / ${'totproy_'.$tahun}) * ${'jumlah_provinsi_'.$tahun});

                            ${'prorata_laki2_'.$tahun}[$i] = floor((${'proykab_laki2_'.$tahun}[$i] / ${'totproy_laki2_'.$tahun}) * array_sum(${'jumlah_prov_laki2_'.$tahun}));

                            ${'prorata_perempuan_'.$tahun}[$i] = floor((${'proykab_perempuan_'.$tahun}[$i] / ${'totproy_perempuan_'.$tahun}) * array_sum(${'jumlah_prov_perempuan_'.$tahun}));
                        };

                        //Hitung jumlah prorata proyeksi untuk semua kabupaten tiap tahun.
                        for($tahun=$tahun_target;$tahun<=$periode_proyeksi;$tahun++){
                            ${'tot_prorata_'.$tahun} = ${'prorata_'.$tahun}[$i]+${'tot_prorata_'.$tahun};
                            ${'tot_prorata_laki2_'.$tahun} = ${'prorata_laki2_'.$tahun}[$i]+${'tot_prorata_laki2_'.$tahun};
                            ${'tot_prorata_perempuan_'.$tahun} = ${'prorata_perempuan_'.$tahun}[$i]+${'tot_prorata_perempuan_'.$tahun}; 
                        };
                    };

                    //Hitung selisih total prorata tahunan dengan total provinsi (kontrol).
                    for($tahun=$tahun_target;$tahun<=$periode_proyeksi;$tahun++){
                        ${'selisih_tot_'.$tahun} = ${'jumlah_provinsi_'.$tahun} - ${'tot_prorata_'.$tahun}; 
                        ${'selisih_tot_laki2_'.$tahun} = array_sum(${'jumlah_prov_laki2_'.$tahun}) - ${'tot_prorata_laki2_'.$tahun}; 
                        ${'selisih_tot_perempuan_'.$tahun} = array_sum(${'jumlah_prov_perempuan_'.$tahun}) - ${'tot_prorata_perempuan_'.$tahun}; 
                    };

                    //Menambahkan selisih tiap tahun ke kabupaten/kota dengan jumlah penduduk tertinggi pada tahun target.
                    for($tahun=$tahun_target;$tahun<=$periode_proyeksi;$tahun++){
                        for($i=0;$i<count($id_wilayah);$i++){
                            if(${'jumlah_penduduk_'.$tahun_target}[$i] == max(${'jumlah_penduduk_'.$tahun_target})){
                                ${'prorata_'.$tahun}[$i] = ${'prorata_'.$tahun}[$i] + ${'selisih_tot_'.$tahun};
                                ${'prorata_laki2_'.$tahun}[$i] = ${'prorata_laki2_'.$tahun}[$i] + ${'selisih_tot_laki2_'.$tahun};
                                ${'prorata_perempuan_'.$tahun}[$i] = ${'prorata_perempuan_'.$tahun}[$i] + ${'selisih_tot_perempuan_'.$tahun};
                            }else{
                                ${'prorata_'.$tahun}[$i] = ${'prorata_'.$tahun}[$i];
                                ${'prorata_laki2_'.$tahun}[$i] = ${'prorata_laki2_'.$tahun}[$i];
                                ${'prorata_perempuan_'.$tahun}[$i] = ${'prorata_perempuan_'.$tahun}[$i];
                            };
                        };
                    }; 


            //PENGHITUNGAN PROYEKSI BULANAN UNTUK L+P, L, P. (TANPA KELOMPOK UMUR)
                //Perhitungan proyeksi bulanan (belum diprorata) dari data yang udah dijadiin Array di atas.
                    for($tahun=0;$tahun<$periode_bulan;$tahun++){ //Ini untuk inisiasi variabel total proyeksi tahunan.
                        ${'totproy_bulan_'.$tahun} = 0;
                        ${'totproy_laki2_bulan_'.$tahun} = 0;
                        ${'totproy_perempuan_bulan_'.$tahun} = 0;
                    };

                    for($i=0;$i<count($id_wilayah);$i++){
                        //Hitung Bulanan Laki2+perempuan
                        $bulan = 3;
                        for($tahun=0;$tahun<$periode_bulan;$tahun++){
                            ${'proykab_bulan_'.$tahun}[$i] = floor(${'jumlah_penduduk_'.$tahun_target}[$i]*pow((1+$lpp_bulanan[$i]),($bulan/2))); 
                            //Ini penghitungan proyeksi per kabupaten per tahun.
                            $bulan = $bulan + 2;
                        };

                        //Hitung Bulanan Laki2
                        for($tahun=0;$tahun<$periode_bulan;$tahun++){
                            ${'proykab_laki2_bulan_'.$tahun}[$i] = floor(( ${'proykab_bulan_'.$tahun}[$i] * $sr[$i] ) / (1 + $sr[$i])); 
                            //Ini penhitungan proyeksi laki2 per kabupaten per tahun -> Total dikali SR
                        };
                        
                        //Hitung Bulanan Perempuan
                        for($tahun=0;$tahun<$periode_bulan;$tahun++){
                            ${'proykab_perempuan_bulan_'.$tahun}[$i] = floor(${'proykab_bulan_'.$tahun}[$i] - ${'proykab_laki2_bulan_'.$tahun}[$i]); 
                            //Ini penhitungan proyeksi perempuan per kabupaten per tahun -> total dikurangi laki2
                        };

                        //Hitung jumlah proyeksi untuk semua kabupaten tiap tahun.
                        for($tahun=0;$tahun<$periode_bulan;$tahun++){
                            ${'totproy_bulan_'.$tahun} = ${'proykab_bulan_'.$tahun}[$i]+${'totproy_bulan_'.$tahun}; 
                            ${'totproy_laki2_bulan_'.$tahun} = ${'proykab_laki2_bulan_'.$tahun}[$i]+${'totproy_laki2_bulan_'.$tahun}; 
                            ${'totproy_perempuan_bulan_'.$tahun} = ${'proykab_perempuan_bulan_'.$tahun}[$i]+${'totproy_perempuan_bulan_'.$tahun}; 
                        };
                    };


            //Melakukan Prorata untuk kelompok umur
                //INISIASI VARIABEL
                for($tahun=$tahun_target;$tahun<=$periode_proyeksi;$tahun++){ //iterasi untuk semua tahun proyeksi
                    for($i=0;$i<count($id_wilayah);$i++){ //Iterasi untuk semua kabkota
                        for($umur=5;$umur<81;$umur=$umur+5){ //iterasi untuk semua kelompok umur

                            //Nilai total (L+P) penduduk per kelompok umur per wilayah pada tahun dasar.
                            ${'ku_'.$umur.'_lp_'.$tahun_target}[$i] = ${'ku_'.$umur.'_laki2_'.$tahun_target}[$i] + ${'ku_'.$umur.'_perempuan_'.$tahun_target}[$i]; 

                            //Nilai jumlah total (L+P) penduduk per kelompok umur semua kabkota pada tahun dasar.
                            ${'jumlah_ku_'.$umur.'_lp_'.$tahun_target} = ${'jumlah_ku_'.$umur.'_laki2_'.$tahun_target} + ${'jumlah_ku_'.$umur.'_perempuan_'.$tahun_target}; 

                            //Kontrol: Nilai jumlah total (L+P) per kelompok umur per tahun untuk provinsi.
                            ${'ku_'.$umur.'_prov_lp_'.$tahun} = ${'ku_'.$umur.'_prov_laki2_'.$tahun} + ${'ku_'.$umur.'_prov_perempuan_'.$tahun};

                        };
                    };
                };

                //ITERASI PERTAMA
                for($tahun=$tahun_target;$tahun<=$periode_proyeksi;$tahun++){
                    //Prorata kolom pertama
                    for($i=0;$i<count($id_wilayah);$i++){ //Iterasi untuk semua kabkota
                        for($umur=5;$umur<81;$umur=$umur+5){ //iterasi untuk semua kelompok umur
                            //Penghitungan prorata kolom awal L+P
                            ${'prorata1_ku_'.$umur.'_lp_'.$tahun}[$i] = round((${'ku_'.$umur.'_lp_'.$tahun_target}[$i] / ${'jumlah_ku_'.$umur.'_lp_'.$tahun_target}) * ${'ku_'.$umur.'_prov_lp_'.$tahun}, 0);
                            //Penghitungan prorata kolom awal L
                            ${'prorata1_ku_'.$umur.'_laki2_'.$tahun}[$i] = round((${'ku_'.$umur.'_laki2_'.$tahun_target}[$i] / ${'jumlah_ku_'.$umur.'_laki2_'.$tahun_target}) * ${'ku_'.$umur.'_prov_laki2_'.$tahun}, 0);
                        };
                    };

                    //Hitung nilai total baris (semua kelompok umur untuk tiap kabkota) dan total kolom (semua kabkota untuk tiap ku)
                    for($i=0;$i<count($id_wilayah);$i++){
                        $b=0;
                        $b1=0;
                        for($umur=5;$umur<81;$umur=$umur+5){
                            //Untuk L+P
                            $b = ${'prorata1_ku_'.$umur.'_lp_'.$tahun}[$i] + $b;
                            ${'tot_prorata_ku_lp_'.$tahun}[$i] = $b; //tot baris
                            ${'tot_prorata_ku_'.$umur.'_lp_'.$tahun} = array_sum(${'prorata1_ku_'.$umur.'_lp_'.$tahun}); //Tot kolom
                        };
                        for($umur=5;$umur<81;$umur=$umur+5){
                            //Untuk L
                            $b1 = ${'prorata1_ku_'.$umur.'_laki2_'.$tahun}[$i] + $b1;
                            ${'tot_prorata_ku_laki2_'.$tahun}[$i] = $b1; //tot baris
                            ${'tot_prorata_ku_'.$umur.'_laki2_'.$tahun} = array_sum(${'prorata1_ku_'.$umur.'_laki2_'.$tahun}); //Tot kolom
                        };
                    };

                    //Prorata baris
                        for($i=0;$i<count($id_wilayah);$i++){ //Iterasi untuk semua kabkota
                            for($umur=5;$umur<81;$umur=$umur+5){ //iterasi untuk semua kelompok umur
                                //Penghitungan prorata baris L+P
                                ${'prorata1_ku_'.$umur.'_lp_'.$tahun}[$i] = round((${'prorata1_ku_'.$umur.'_lp_'.$tahun}[$i] / ${'tot_prorata_ku_lp_'.$tahun}[$i]) * ${'prorata_'.$tahun}[$i], 0);
                                //Penghitungan prorata baris L
                                ${'prorata1_ku_'.$umur.'_laki2_'.$tahun}[$i] = round((${'prorata1_ku_'.$umur.'_laki2_'.$tahun}[$i] / ${'tot_prorata_ku_laki2_'.$tahun}[$i]) * ${'prorata_laki2_'.$tahun}[$i], 0);
                            };
                        };

                    //Hitung nilai total baris (semua kelompok umur untuk tiap kabkota) dan total kolom (semua kabkota untuk tiap ku)
                    for($i=0;$i<count($id_wilayah);$i++){
                        $b=0;
                        $b1=0;
                        for($umur=5;$umur<81;$umur=$umur+5){
                            //Untuk L+P
                            $b = ${'prorata1_ku_'.$umur.'_lp_'.$tahun}[$i] + $b;
                            ${'tot_prorata_ku_lp_'.$tahun}[$i] = $b; //tot baris
                            ${'tot_prorata_ku_'.$umur.'_lp_'.$tahun} = array_sum(${'prorata1_ku_'.$umur.'_lp_'.$tahun}); //Tot kolom
                            //Untuk L
                            $b1 = ${'prorata1_ku_'.$umur.'_laki2_'.$tahun}[$i] + $b1;
                            ${'tot_prorata_ku_laki2_'.$tahun}[$i] = $b1; //tot baris
                            ${'tot_prorata_ku_'.$umur.'_laki2_'.$tahun} = array_sum(${'prorata1_ku_'.$umur.'_laki2_'.$tahun}); //Tot kolom
                        };
                    };

                };

                //MULAI ITERASI PENGHITUNGAN SELANJUTNYA
                for($tahun=$tahun_target;$tahun<=$periode_proyeksi;$tahun++){
                    for($iterasi=0;$iterasi<=8;$iterasi++){
                        //Prorata kolom
                        for($i=0;$i<count($id_wilayah);$i++){ //Iterasi untuk semua kabkota
                            for($umur=5;$umur<81;$umur=$umur+5){ //iterasi untuk semua kelompok umur
                                //Penghitungan prorata kolom L+P
                                ${'prorata1_ku_'.$umur.'_lp_'.$tahun}[$i] = round((${'prorata1_ku_'.$umur.'_lp_'.$tahun}[$i] / ${'tot_prorata_ku_'.$umur.'_lp_'.$tahun}) * ${'ku_'.$umur.'_prov_lp_'.$tahun}, 0);
                                //Penghitungan prorata kolom L
                                ${'prorata1_ku_'.$umur.'_laki2_'.$tahun}[$i] = round((${'prorata1_ku_'.$umur.'_laki2_'.$tahun}[$i] / ${'tot_prorata_ku_'.$umur.'_laki2_'.$tahun}) * ${'ku_'.$umur.'_prov_laki2_'.$tahun}, 0);
                            };
                        };

                        //Hitung nilai total baris (semua kelompok umur untuk tiap kabkota) dan total kolom (semua kabkota untuk tiap ku)
                        for($i=0;$i<count($id_wilayah);$i++){
                            $b=0;
                            $b1=0;
                            for($umur=5;$umur<81;$umur=$umur+5){
                                //Untuk L+P
                                $b = ${'prorata1_ku_'.$umur.'_lp_'.$tahun}[$i] + $b;
                                ${'tot_prorata_ku_lp_'.$tahun}[$i] = $b; //tot baris
                                ${'tot_prorata_ku_'.$umur.'_lp_'.$tahun} = array_sum(${'prorata1_ku_'.$umur.'_lp_'.$tahun}); //Tot kolom
                                //Untuk L
                                $b1 = ${'prorata1_ku_'.$umur.'_laki2_'.$tahun}[$i] + $b1;
                                ${'tot_prorata_ku_laki2_'.$tahun}[$i] = $b1; //tot baris
                                ${'tot_prorata_ku_'.$umur.'_laki2_'.$tahun} = array_sum(${'prorata1_ku_'.$umur.'_laki2_'.$tahun}); //Tot kolom
                            };
                        };

                        //Prorata baris
                            for($i=0;$i<count($id_wilayah);$i++){ //Iterasi untuk semua kabkota
                                for($umur=5;$umur<81;$umur=$umur+5){ //iterasi untuk semua kelompok umur
                                    //Penghitungan prorata baris L+P
                                    ${'prorata1_ku_'.$umur.'_lp_'.$tahun}[$i] = round((${'prorata1_ku_'.$umur.'_lp_'.$tahun}[$i] / ${'tot_prorata_ku_lp_'.$tahun}[$i]) * ${'prorata_'.$tahun}[$i], 0);
                                    //Penghitungan prorata baris L
                                    ${'prorata1_ku_'.$umur.'_laki2_'.$tahun}[$i] = round((${'prorata1_ku_'.$umur.'_laki2_'.$tahun}[$i] / ${'tot_prorata_ku_laki2_'.$tahun}[$i]) * ${'prorata_laki2_'.$tahun}[$i], 0);
                                };
                            };

                        //Hitung nilai total baris (semua kelompok umur untuk tiap kabkota) dan total kolom (semua kabkota untuk tiap ku)
                        for($i=0;$i<count($id_wilayah);$i++){
                            $b=0;
                            $b1=0;
                            for($umur=5;$umur<81;$umur=$umur+5){
                            //Untuk L+P
                                $b = ${'prorata1_ku_'.$umur.'_lp_'.$tahun}[$i] + $b;
                                ${'tot_prorata_ku_lp_'.$tahun}[$i] = $b; //tot baris
                                ${'tot_prorata_ku_'.$umur.'_lp_'.$tahun} = array_sum(${'prorata1_ku_'.$umur.'_lp_'.$tahun}); //Tot kolom
                                //Untuk L
                                $b1 = ${'prorata1_ku_'.$umur.'_laki2_'.$tahun}[$i] + $b1;
                                ${'tot_prorata_ku_laki2_'.$tahun}[$i] = $b1; //tot baris
                                ${'tot_prorata_ku_'.$umur.'_laki2_'.$tahun} = array_sum(${'prorata1_ku_'.$umur.'_laki2_'.$tahun}); //Tot kolom
                            };
                        };
                    };

                    //Hitung Selisih antara jumlah seluruh ku  dari tiap kabkota dengan jumlah penduduk kabkota tersebut
                        for($i=0;$i<count($id_wilayah);$i++){
                            $selisih = ${'tot_prorata_ku_lp_'.$tahun}[$i] - ${'prorata_'.$tahun}[$i];
                            $selisih_laki2 = ${'tot_prorata_ku_laki2_'.$tahun}[$i] - ${'prorata_laki2_'.$tahun}[$i];

                            //Menambahkan selisih ke ku 75+
                            ${'prorata1_ku_80_lp_'.$tahun}[$i] = ${'prorata1_ku_80_lp_'.$tahun}[$i] - $selisih;
                            ${'prorata1_ku_80_laki2_'.$tahun}[$i] = ${'prorata1_ku_80_laki2_'.$tahun}[$i] - $selisih_laki2;
                        };

                    //Hitung nilai total kolom (semua kabkota untuk tiap ku) setelah penambahan selisih untuk ku 75+
                        for($i=0;$i<count($id_wilayah);$i++){
                            for($umur=5;$umur<81;$umur=$umur+5){
                                //Untuk L+P
                                ${'tot_prorata_ku_'.$umur.'_lp_'.$tahun} = array_sum(${'prorata1_ku_'.$umur.'_lp_'.$tahun}); //Tot kolom
                                //Untuk L
                                ${'tot_prorata_ku_'.$umur.'_laki2_'.$tahun} = array_sum(${'prorata1_ku_'.$umur.'_laki2_'.$tahun}); //Tot kolom
                            };
                        };

                    //Hitung Selisih antara jumlah ku seluruhnya dari tiap kabkota dengan jumlah penduduk kabkota tersebut
                        for($i=0;$i<count($id_wilayah);$i++){
                            if(${'jumlah_penduduk_'.$tahun_target}[$i] == max(${'jumlah_penduduk_'.$tahun_target})){
                                for($umur=5;$umur<81;$umur=$umur+5){
                                    $selisih = ${'tot_prorata_ku_'.$umur.'_lp_'.$tahun} - ${'ku_'.$umur.'_prov_lp_'.$tahun};
                                    $selisih_laki2 = ${'tot_prorata_ku_'.$umur.'_laki2_'.$tahun} - ${'ku_'.$umur.'_prov_laki2_'.$tahun};
                                    ${'prorata1_ku_'.$umur.'_lp_'.$tahun}[$i] = ${'prorata1_ku_'.$umur.'_lp_'.$tahun}[$i] - $selisih;
                                    ${'prorata1_ku_'.$umur.'_laki2_'.$tahun}[$i] = ${'prorata1_ku_'.$umur.'_laki2_'.$tahun}[$i] - $selisih_laki2;
                                };
                            }else{
                                for($umur=5;$umur<81;$umur=$umur+5){
                                    ${'prorata1_ku_'.$umur.'_lp_'.$tahun}[$i] = ${'prorata1_ku_'.$umur.'_lp_'.$tahun}[$i];
                                    ${'prorata1_ku_'.$umur.'_laki2_'.$tahun}[$i] = ${'prorata1_ku_'.$umur.'_laki2_'.$tahun}[$i];
                                };
                            };
                        };

                    //Cari nilai proyeksi perempuan
                        for($i=0;$i<count($id_wilayah);$i++){
                            for($umur=5;$umur<81;$umur=$umur+5){
                                ${'prorata1_ku_'.$umur.'_perempuan_'.$tahun}[$i] = ${'prorata1_ku_'.$umur.'_lp_'.$tahun}[$i] - ${'prorata1_ku_'.$umur.'_laki2_'.$tahun}[$i];
                            };
                        };

                };


                    
                //Simpan hasil proyeksi ke database.
                    //Buat nama2 kolom di tabel hasil_proyeksi_jumlah jadi 1 array
                    $kolomhasil = array();
                    $kolomhasil[0] = 'id_wilayah';
                    $kolomhasil[1] = 'tahun_proyeksi';
                    $kolomhasil[2] = 'jenis_kelamin';
                    $kolomhasil[3] = 'kup_5';
                    $kolomhasil[4] = 'kup_10';
                    $kolomhasil[5] = 'kup_15';
                    $kolomhasil[6] = 'kup_20';
                    $kolomhasil[7] = 'kup_25';
                    $kolomhasil[8] = 'kup_30';
                    $kolomhasil[9] = 'kup_35';
                    $kolomhasil[10] = 'kup_40';
                    $kolomhasil[11] = 'kup_45';
                    $kolomhasil[12] = 'kup_50';
                    $kolomhasil[13] = 'kup_55';
                    $kolomhasil[14] = 'kup_60';
                    $kolomhasil[15] = 'kup_65';
                    $kolomhasil[16] = 'kup_70';
                    $kolomhasil[17] = 'kup_75';
                    $kolomhasil[18] = 'kup_80';

                                    
                    //Buat isi2 tabel hasil proyeksi jadi 1 array
                    $datahasil = array();
                    $p = 0;
                    for($i=0;$i<count($id_wilayah);$i++){ //Iterasi untuk semua kabkota
                        for($tahun=$tahun_target;$tahun<=$periode_proyeksi;$tahun++){ //iterasi untuk semua tahun proyeksi
                            $datahasil[$p] =
                            [
                                $id_wilayah[$i],
                                $tahun,
                                'l+p',
                                ${'prorata1_ku_5_lp_'.$tahun}[$i],
                                ${'prorata1_ku_10_lp_'.$tahun}[$i],
                                ${'prorata1_ku_15_lp_'.$tahun}[$i],
                                ${'prorata1_ku_20_lp_'.$tahun}[$i],
                                ${'prorata1_ku_25_lp_'.$tahun}[$i],
                                ${'prorata1_ku_30_lp_'.$tahun}[$i],
                                ${'prorata1_ku_35_lp_'.$tahun}[$i],
                                ${'prorata1_ku_40_lp_'.$tahun}[$i],
                                ${'prorata1_ku_45_lp_'.$tahun}[$i],
                                ${'prorata1_ku_50_lp_'.$tahun}[$i],
                                ${'prorata1_ku_55_lp_'.$tahun}[$i],
                                ${'prorata1_ku_60_lp_'.$tahun}[$i],
                                ${'prorata1_ku_65_lp_'.$tahun}[$i],
                                ${'prorata1_ku_70_lp_'.$tahun}[$i],
                                ${'prorata1_ku_75_lp_'.$tahun}[$i],
                                ${'prorata1_ku_80_lp_'.$tahun}[$i],
                            ];
                            $p++;
                        };
                        for($tahun=$tahun_target;$tahun<=$periode_proyeksi;$tahun++){ //iterasi untuk semua tahun proyeksi
                            $datahasil[$p] =
                            [
                                $id_wilayah[$i],
                                $tahun,
                                'l',
                                ${'prorata1_ku_5_laki2_'.$tahun}[$i],
                                ${'prorata1_ku_10_laki2_'.$tahun}[$i],
                                ${'prorata1_ku_15_laki2_'.$tahun}[$i],
                                ${'prorata1_ku_20_laki2_'.$tahun}[$i],
                                ${'prorata1_ku_25_laki2_'.$tahun}[$i],
                                ${'prorata1_ku_30_laki2_'.$tahun}[$i],
                                ${'prorata1_ku_35_laki2_'.$tahun}[$i],
                                ${'prorata1_ku_40_laki2_'.$tahun}[$i],
                                ${'prorata1_ku_45_laki2_'.$tahun}[$i],
                                ${'prorata1_ku_50_laki2_'.$tahun}[$i],
                                ${'prorata1_ku_55_laki2_'.$tahun}[$i],
                                ${'prorata1_ku_60_laki2_'.$tahun}[$i],
                                ${'prorata1_ku_65_laki2_'.$tahun}[$i],
                                ${'prorata1_ku_70_laki2_'.$tahun}[$i],
                                ${'prorata1_ku_75_laki2_'.$tahun}[$i],
                                ${'prorata1_ku_80_laki2_'.$tahun}[$i],
                            ];
                            $p++;
                        };
                        for($tahun=$tahun_target;$tahun<=$periode_proyeksi;$tahun++){ //iterasi untuk semua tahun proyeksi
                            $datahasil[$p] =
                            [
                                $id_wilayah[$i],
                                $tahun,
                                'p',
                                ${'prorata1_ku_5_perempuan_'.$tahun}[$i],
                                ${'prorata1_ku_10_perempuan_'.$tahun}[$i],
                                ${'prorata1_ku_15_perempuan_'.$tahun}[$i],
                                ${'prorata1_ku_20_perempuan_'.$tahun}[$i],
                                ${'prorata1_ku_25_perempuan_'.$tahun}[$i],
                                ${'prorata1_ku_30_perempuan_'.$tahun}[$i],
                                ${'prorata1_ku_35_perempuan_'.$tahun}[$i],
                                ${'prorata1_ku_40_perempuan_'.$tahun}[$i],
                                ${'prorata1_ku_45_perempuan_'.$tahun}[$i],
                                ${'prorata1_ku_50_perempuan_'.$tahun}[$i],
                                ${'prorata1_ku_55_perempuan_'.$tahun}[$i],
                                ${'prorata1_ku_60_perempuan_'.$tahun}[$i],
                                ${'prorata1_ku_65_perempuan_'.$tahun}[$i],
                                ${'prorata1_ku_70_perempuan_'.$tahun}[$i],
                                ${'prorata1_ku_75_perempuan_'.$tahun}[$i],
                                ${'prorata1_ku_80_perempuan_'.$tahun}[$i],
                            ];
                            $p++;
                        };
                    };                            
                        
                    //simpan ke database
                    Yii::$app->db->createCommand()->batchInsert('hasil_proyeksi_lp', $kolomhasil, $datahasil)->execute();
                



            //Simpan hasil proyeksi tahunan ke database.
                //Buat nama2 kolom di tabel hasil_proyeksi_jumlah jadi 1 array
                    $kolomhasil = array();
                    $kolomhasil[0] = 'id_wilayah';
                    $kolomhasil[1] = 'tahun_proyeksi';
                    $kolomhasil[2] = 'jenis_kelamin';
                    $kolomhasil[3] = 'jumlah_proyeksi';
                //Buat isi2 tabel hasil proyeksi jadi 1 array
                    $datahasil = array();
                    $p = 0;
                    for($k=0;$k<$i;$k++){
                        //masukan data laki+perempuan
                        for($thn=$tahun_target;$thn<=$periode_proyeksi;$thn++){
                            $datahasil[$p] =
                            [
                                $id_wilayah[$k],
                                $thn,
                                'l+p',
                                ${'prorata_'.$thn}[$k],
                            ];
                            $p++;
                        };
                        //masukan data laki
                        for($thn=$tahun_target;$thn<=$periode_proyeksi;$thn++){
                            $datahasil[$p] =
                            [
                                $id_wilayah[$k],
                                $thn,
                                'l',
                                ${'prorata_laki2_'.$thn}[$k],
                            ];
                            $p++;
                        };
                        //masukan data perempuan
                        for($thn=$tahun_target;$thn<=$periode_proyeksi;$thn++){
                            $datahasil[$p] =
                            [
                                $id_wilayah[$k],
                                $thn,
                                'p',
                                ${'prorata_perempuan_'.$thn}[$k],
                            ];
                            $p++;
                        };
                        
                    };                             
                //disimpan ke database
                    Yii::$app->db->createCommand()->batchInsert('hasil_proyeksi_jumlah', $kolomhasil, $datahasil)->execute();

            //Simpan hasil proyeksi bulanan ke database.
                //Buat nama2 kolom di tabel hasil_proyeksi_jumlah jadi 1 array
                    $kolomhasil = array();
                    $kolomhasil[0] = 'id_wilayah';
                    $kolomhasil[1] = 'tahun_proyeksi';
                    $kolomhasil[2] = 'jenis_kelamin';
                    $kolomhasil[3] = 'jumlah_proyeksi';     
                //Buat isi2 tabel hasil proyeksi jadi 1 array
                    $datahasil = array();
                    $p = 0;
                    for($k=0;$k<$i;$k++){
                        for($thn=0;$thn<$periode_bulan;$thn++){
                            $datahasil[$p] =
                            [
                                $id_wilayah[$k],
                                $thn,
                                'l+p',
                                ${'proykab_bulan_'.$thn}[$k],
                            ];
                            $p++;
                        };
                        for($thn=0;$thn<$periode_bulan;$thn++){
                            $datahasil[$p] =
                            [
                                $id_wilayah[$k],
                                $thn,
                                'l',
                                ${'proykab_laki2_bulan_'.$thn}[$k],
                            ];
                            $p++;
                        };
                        for($thn=0;$thn<$periode_bulan;$thn++){
                            $datahasil[$p] =
                            [
                                $id_wilayah[$k],
                                $thn,
                                'p',
                                ${'proykab_perempuan_bulan_'.$thn}[$k],
                            ];
                            $p++;
                        };
                        
                    };                            
                //disimpan ke database
                    Yii::$app->db->createCommand()->batchInsert('hasil_proyeksi_jumlah_bulan', $kolomhasil, $datahasil)->execute();

            //Simpan data di monitoring
                $string = "Membuat proyeksi tahun {$tahun_target} - {$panjang_tahun}";
                include "koneksi.php";
                $sql_tambah = "INSERT INTO monitoring (nama_wilayah, kegiatan, tanggal, waktu) 
                               VALUE('".Yii::$app->user->identity->username."', 
                                     '".$string."',
                                     '".date('Y-m-d')."', 
                                     '".date('H:i:s')."')";
                $query_tambah = mysqli_query($host,$sql_tambah) or die(mysqli_error());


                //Langsung loncat ke halaman hasil proyeksi
                    return Yii::$app->response->redirect(Url::to(['/hasil-proyeksi-jumlah/index']));

        ?>


    <?php elseif ($status == 'hapus'): ?>
        <?php
            //Hapus hasil proyeksi yang sudah pernah dibuat-->
            $id_provinsi = substr(Yii::$app->user->identity->wilayah_id, 0, 2);
            //Buat koneksi ke DB
                include "koneksi.php";
                //Hapus hasil proyeksi tahunan
                $sql_hapusHasil = "DELETE hasil_proyeksi_jumlah
                                   FROM hasil_proyeksi_jumlah, master_wilayah
                                   WHERE hasil_proyeksi_jumlah.id_wilayah = master_wilayah.id_wilayah
                                   AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'";
                $query_hapusHasil = mysqli_query($host,$sql_hapusHasil) or die(mysqli_error());
                //Hapus hasil proyeksi bulanan
                $sql_hapusHasil3 = "DELETE hasil_proyeksi_jumlah_bulan
                                   FROM hasil_proyeksi_jumlah_bulan, master_wilayah
                                   WHERE hasil_proyeksi_jumlah_bulan.id_wilayah = master_wilayah.id_wilayah
                                   AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'";
                $query_hapusHasil3 = mysqli_query($host,$sql_hapusHasil3) or die(mysqli_error());
                //Hapus hasil proyeksi tahunan menurut JK dan KU
                $sql_hapusHasil2 = "DELETE hasil_proyeksi_lp
                                   FROM hasil_proyeksi_lp, master_wilayah
                                   WHERE hasil_proyeksi_lp.id_wilayah = master_wilayah.id_wilayah
                                   AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'";
                $query_hapusHasil2 = mysqli_query($host,$sql_hapusHasil2) or die(mysqli_error());
                //Hapus laporan
                $sql_hapusPengiriman = "DELETE FROM laporan WHERE perihal = 'Cek hasil proyeksi' AND asal = '" . Yii::$app->user->identity->username . "'";
                $query_hapusPengiriman = mysqli_query($host,$sql_hapusPengiriman) or die(mysqli_error());

            //Simpan proses hapus di monitoring
                include "koneksi.php";
                $sql_tambah = "INSERT INTO monitoring (nama_wilayah, kegiatan, tanggal, waktu) 
                               VALUE('".Yii::$app->user->identity->username."', 
                                     'Menghapus hasil proyeksi',
                                     '".date('Y-m-d')."', 
                                     '".date('H:i:s')."')";
                $query_tambah = mysqli_query($host,$sql_tambah) or die(mysqli_error());

            //Langsung loncat ke halaman hasil proyeksi
                return Yii::$app->response->redirect(Url::to(['/site/proykabkota']));
        ?>

    <?php endif; ?>
    	

</div>


