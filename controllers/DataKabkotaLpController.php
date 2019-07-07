<?php

namespace app\controllers;

use Yii;
use app\models\DataKabkotaLp;
use app\models\Wilayah;
use app\models\Masuk;
use app\models\MasterWilayah;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;

ini_set('max_execution_time', 120);


/**
 * DataKabkotaLpController implements the CRUD actions for DataKabkotaLp model.
 */
class DataKabkotaLpController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
      //fungsi transpose array
        function transpose($array) {
            array_unshift($array, null);
            return call_user_func_array('array_map', $array);
        }
      if (Yii::$app->user->identity->role == 'pusat'){
        //Menampilkan wilayah provinsi saja (kode belakangnya 00)
            $sql = "SELECT *
                    FROM master_wilayah
                    WHERE SUBSTRING(id_wilayah,3,2) = '00' " ;
            $dataProvider = new ActiveDataProvider([
                'query' => MasterWilayah::findBySql($sql),
            ]);

            return $this->render('index', [
              'status_data' => 'kosong',
              'dataProvider' => $dataProvider,
            ]);
      }

      elseif (Yii::$app->user->identity->role == 'provinsi' || Yii::$app->user->identity->role == 'kabkota'){
        //Perhitungan jumlah kolom SP10 dan SUPAS 2015
          include "koneksi.php";

        //Ambil tahun yang ada
          $sql_tahun = "SELECT tahun_dasar
                        FROM data_kabkota_lp
                        GROUP BY tahun_dasar
                        having count(*)>1 ";
          $query_tahun = mysqli_query($host,$sql_tahun) or die(mysqli_error());
          $tahun='';
          while($row = mysqli_fetch_array($query_tahun)){ 
              $tahun++; //jumlah tahun yang ada.
              $tahun_data[] = $row['tahun_dasar'];
          };

        //Cek Apakah sudah ada data provinsi terkait atau belum.
          $id_provinsi = substr(Yii::$app->user->identity->wilayah_id, 0, 2);
          $sql_dataCek = "SELECT *
                          FROM data_kabkota_lp
                          WHERE SUBSTRING(data_kabkota_lp.id_wilayah,1,2) = '" . $id_provinsi . "'"; 
          $query_dataCek = mysqli_query($host,$sql_dataCek) or die(mysqli_error());
          $cek = '';
          while($row = mysqli_fetch_array($query_dataCek)){ 
            $cek++;
          };

        if($cek){ //Jika ada data
          //Mulai ambil data
          for($t=0;$t<count($tahun_data);$t++){
            //Ambil jumlah laki2 per kabupaten/kota per tahun
              if(Yii::$app->user->identity->role == 'provinsi'){
                $id_provinsi = substr(Yii::$app->user->identity->wilayah_id, 0, 2);
                $sql_dataLaki2 = "SELECT *
                                  FROM data_kabkota_lp, master_wilayah
                                  WHERE data_kabkota_lp.id_wilayah = master_wilayah.id_wilayah
                                  AND data_kabkota_lp.jenis_kelamin = 'l'
                                  AND data_kabkota_lp.tahun_dasar = '" . $tahun_data[$t] . "'
                                  AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'";
                $query_dataLaki2 = mysqli_query($host,$sql_dataLaki2) or die(mysqli_error());
              } elseif(Yii::$app->user->identity->role == 'kabkota'){
                $sql_dataLaki2 = "SELECT *
                                  FROM data_kabkota_lp, master_wilayah
                                  WHERE master_wilayah.id_wilayah = '" . Yii::$app->user->identity->wilayah_id . "'
                                  AND data_kabkota_lp.id_wilayah = master_wilayah.id_wilayah
                                  AND data_kabkota_lp.jenis_kelamin = 'l'
                                  AND data_kabkota_lp.tahun_dasar = '" . $tahun_data[$t] . "'"; 
                $query_dataLaki2 = mysqli_query($host,$sql_dataLaki2) or die(mysqli_error());
              };

              $i='';
              unset($nama_wilayah);
              unset($id_wilayah);
              while($row = mysqli_fetch_array($query_dataLaki2)){ 
                $i++;
                $nama_wilayah[] = $row['nama_wilayah'];
                $id_wilayah[] = $row['id_wilayah'];
                for($umur=5;$umur<81;$umur=$umur+5){
                  ${'ku_'.$umur.'_laki2_'.$tahun_data[$t]}[] = $row['ku_'.$umur];
                };
              };
                  
              for($j=0;$j<count($id_wilayah);$j++){
                $b=0;
                for($umur=5;$umur<81;$umur=$umur+5){
                  $b = ${'ku_'.$umur.'_laki2_'.$tahun_data[$t]}[$j] + $b;
                  ${'jumlah_laki2_'.$tahun_data[$t]}[$j] = $b;
                  ${'jumlah_ku_'.$umur.'_laki2_'.$tahun_data[$t]} = array_sum(${'ku_'.$umur.'_laki2_'.$tahun_data[$t]}); 
                };
              };

            //Ambil jumlah perempuan per kabupaten/kota per tahun
              if(Yii::$app->user->identity->role == 'provinsi'){
                $id_provinsi = substr(Yii::$app->user->identity->wilayah_id, 0, 2);
                $sql_dataPerempuan = "SELECT *
                                  FROM data_kabkota_lp, master_wilayah
                                  WHERE data_kabkota_lp.id_wilayah = master_wilayah.id_wilayah
                                  AND data_kabkota_lp.jenis_kelamin = 'p'
                                  AND data_kabkota_lp.tahun_dasar = '" . $tahun_data[$t] . "'
                                  AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'"; 
                $query_dataPerempuan = mysqli_query($host,$sql_dataPerempuan) or die(mysqli_error());
              } elseif(Yii::$app->user->identity->role == 'kabkota'){
                $sql_dataPerempuan = "SELECT *
                                  FROM data_kabkota_lp, master_wilayah
                                  WHERE master_wilayah.id_wilayah = '" . Yii::$app->user->identity->wilayah_id . "'
                                  AND data_kabkota_lp.id_wilayah = master_wilayah.id_wilayah
                                  AND data_kabkota_lp.jenis_kelamin = 'p'
                                  AND data_kabkota_lp.tahun_dasar = '" . $tahun_data[$t] . "'"; 
                $query_dataPerempuan = mysqli_query($host,$sql_dataPerempuan) or die(mysqli_error());
              };

              $i='';
              while($row = mysqli_fetch_array($query_dataPerempuan)){ 
                $i++;
                for($umur=5;$umur<81;$umur=$umur+5){
                  ${'ku_'.$umur.'_perempuan_'.$tahun_data[$t]}[] = $row['ku_'.$umur];
                };
              };
                  
              for($j=0;$j<count($nama_wilayah);$j++){
                $b=0;
                for($umur=5;$umur<81;$umur=$umur+5){
                  $b = ${'ku_'.$umur.'_perempuan_'.$tahun_data[$t]}[$j] + $b;
                  ${'jumlah_perempuan_'.$tahun_data[$t]}[$j] = $b;
                };
              };

            //Hitung jumlah penduduk untuk semua tahun data
              for($k=0;$k<$i;$k++){
                ${'jumlah_penduduk_'.$tahun_data[$t]}[$k] = ${'jumlah_laki2_'.$tahun_data[$t]}[$k] + ${'jumlah_perempuan_'.$tahun_data[$t]}[$k];
              }

            //Hitung total penduduk semua kabupaten/kota pertahun
              $total_tahun[$t] = array_sum(${'jumlah_penduduk_'.$tahun_data[$t]});

          };

          //Jadiin dataProvider
            $data_kabkota['id_wilayah'] = $id_wilayah;
            $data_kabkota['nama_wilayah'] = $nama_wilayah;
            for($t=0;$t<count($tahun_data);$t++){
              $data_kabkota[$tahun_data[$t]] = ${'jumlah_penduduk_'.$tahun_data[$t]};
            };

            $attributes = []; 
              for($coba=0;$coba<count($data_kabkota);$coba++) {
                $attributes[] = $coba;
              };

            $data_kabkota_transpose = transpose($data_kabkota);

            $provider = new ArrayDataProvider([ //menjadikan multidimensional array ke bentuk provider
              'key' => '0',
              'allModels' => $data_kabkota_transpose,
              'pagination' => [
                'pageSize' => count($id_wilayah),
              ],
              'sort' => [
                'attributes' => $data_kabkota,
              ],
            ]);

          return $this->render('index', [
              'dataProvider' => $provider,
              'tahun_data' => $tahun_data,
              'attributes' => $attributes,
              'total_tahun' => $total_tahun,
              'status_proyeksi' => 'sudah ada data',
              'jumlah_ku_5_laki2_2010' => $jumlah_ku_5_laki2_2010,
          ]);

        } 
        elseif(!$cek){ //Jika tidak ada
          return $this->render('index', [
            'status_proyeksi' => 'belum ada data',
          ]);
        };
      };
    }

    public function actionView($id)
    {
      //fungsi transpose array
        function transpose($array) {
          array_unshift($array, null);
          return call_user_func_array('array_map', $array);
        }

      $wilayah_terpilih = $id;

      //buat tabel untuk nampilin data.
        //Buat koneksi
          include "koneksi.php";

        //Ambil nama wilayah dari tabel master_wilayah untuk role provinsi
          $sql_dataWilayah = "SELECT *
                              FROM master_wilayah
                              WHERE id_wilayah = '".$wilayah_terpilih."'"; 
          $query_dataWilayah = mysqli_query($host,$sql_dataWilayah) or die(mysqli_error());

          while($row = mysqli_fetch_array($query_dataWilayah)){ 
            $nama_wilayah[] = $row['nama_wilayah'];
          };
          $nama_wilayah = $nama_wilayah[0];
          $id_wilayah = $wilayah_terpilih;

        //Buat kolom kelompok umur
          $ku[] = '0-4'; $ku[] = '5-9'; $ku[] = '10-14'; $ku[] = '15-19'; $ku[] = '20-24';
          $ku[] = '25-29'; $ku[] = '30-34'; $ku[] = '35-39'; $ku[] = '40-44'; $ku[] = '45-49';
          $ku[] = '50-54'; $ku[] = '55-59'; $ku[] = '60-64'; $ku[] = '65-69'; $ku[] = '70-74';
          $ku[] = '75+'; 

        //Ambil tahun yang ada
          $sql_tahun = "SELECT tahun_dasar
                        FROM data_kabkota_lp
                        GROUP BY tahun_dasar
                        having count(*)>1 ";
          $query_tahun = mysqli_query($host,$sql_tahun) or die(mysqli_error());
          $tahun='';
          while($row = mysqli_fetch_array($query_tahun)){ 
              $tahun++; //jumlah tahun yang ada.
              $tahun_data[] = $row['tahun_dasar'];
          };

        //Mulai ambil data
          for($t=0;$t<count($tahun_data);$t++){
            //Ambil jumlah laki2 per kabupaten/kota per tahun
            $sql_dataLaki2 = "SELECT *
                              FROM data_kabkota_lp
                              WHERE jenis_kelamin = 'l'
                              AND tahun_dasar = '" . $tahun_data[$t] . "'
                              AND id_wilayah = '". $id_wilayah ."'";
            $query_dataLaki2 = mysqli_query($host,$sql_dataLaki2) or die(mysqli_error());
            $i=0;
            while($row = mysqli_fetch_array($query_dataLaki2)){ 
              $i++;
              for($umur=5;$umur<81;$umur=$umur+5){
                ${'data_laki2_t'.$t}[] = $row['ku_'.$umur];
              };
            };

          //Ambil data perempuan 2010.
            $sql_dataPerempuan = "SELECT *
                                  FROM data_kabkota_lp
                                  WHERE jenis_kelamin = 'p'
                                  AND tahun_dasar = '" . $tahun_data[$t] . "'
                                  AND id_wilayah = '". $id_wilayah ."'"; 
            $query_dataPerempuan = mysqli_query($host,$sql_dataPerempuan) or die(mysqli_error());

            while($row = mysqli_fetch_array($query_dataPerempuan)){ 
              for($umur=5;$umur<81;$umur=$umur+5){
                ${'data_perempuan_t'.$t}[] = $row['ku_'.$umur];
              };
            };
        
          //Buat data jumlah perkelompok umur tahun 2010 dan 2015
            for($j=0;$j<count($data_laki2_t0);$j++){
              ${'data_jumlah_t'.$t}[$j] =  ${'data_laki2_t'.$t}[$j] + ${'data_perempuan_t'.$t}[$j];
              ${'data_laki2_t'.$t.'_pp'}[$j] = ${'data_laki2_t'.$t}[$j] * -1;
              ${'data_perempuan_t'.$t.'_pp'}[$j] = ${'data_perempuan_t'.$t}[$j] * 1;
            };

          //Jadiin dataProvider untuk 2010
            ${'data_ku_t'.$t}[] = $ku;
            ${'data_ku_t'.$t}[] = ${'data_laki2_t'.$t};
            ${'data_ku_t'.$t}[] = ${'data_perempuan_t'.$t};
            ${'data_ku_t'.$t}[] = ${'data_jumlah_t'.$t};
            ${'data_ku_t'.$t.'_transpose'} = transpose(${'data_ku_t'.$t});
            ${'provider_t'.$t} = new ArrayDataProvider([ //menjadikan multidimensional array ke bentuk provider
              'key' => '0',
              'allModels' => ${'data_ku_t'.$t.'_transpose'},
              'pagination' => [
                'pageSize' => count($ku),
              ],
              'sort' => [
                'attributes' => ['0', '1', '2', '3'],
              ],
            ]);
        };

        //Buat variabel array untuk dikirimkan ke halam view
          for($t=0;$t<count($tahun_data);$t++){
            $provider[$t] = ${'provider_t'.$t};
            $total_jumlah[$t] = array_sum(${'data_jumlah_t'.$t});
            $total_laki2[$t] = array_sum(${'data_laki2_t'.$t});
            $total_perempuan[$t] = array_sum(${'data_perempuan_t'.$t});
            $data_laki2[$t] = ${'data_laki2_t'.$t};
            $data_perempuan[$t] = ${'data_perempuan_t'.$t};
            $data_laki2_pp[$t] = ${'data_laki2_t'.$t.'_pp'};
            $data_perempuan_pp[$t] = ${'data_perempuan_t'.$t.'_pp'};
          };

        return $this->render('view', [
          'dataProvider_ku_2010' => $provider_t0,
          'dataProvider_ku_2015' => $provider_t1,
          'ku' => $ku,
          'nama_wilayah' => $nama_wilayah,
          'id_wilayah' => $id_wilayah,
          'total_jumlah_2015' => array_sum($data_jumlah_t1),
          'total_laki2_2015' => array_sum($data_laki2_t1),
          'total_perempuan_2015' => array_sum($data_perempuan_t1),
          'total_jumlah_2010' => array_sum($data_jumlah_t0),
          'total_laki2_2010' => array_sum($data_laki2_t0),
          'total_perempuan_2010' => array_sum($data_perempuan_t0),
          'data_laki2_2010_pp' => $data_laki2_t0_pp,
          'data_perempuan_2010_pp' => $data_perempuan_t0_pp,
          'data_laki2_2015_pp' => $data_laki2_t1_pp,
          'data_perempuan_2015_pp' => $data_perempuan_t1_pp,
          'tahun_data' => $tahun_data,
          'provider' => $provider,
          'total_jumlah' => $total_jumlah,
          'total_laki2' => $total_laki2,
          'total_perempuan' => $total_perempuan,
          'data_laki2' => $data_laki2,
          'data_perempuan' => $data_perempuan,
          'data_laki2_pp' => $data_laki2_pp,
          'data_perempuan_pp' => $data_perempuan_pp,


        ]);
    }

    public function actionLihat($provinsi_terpilih, $id_provinsi_terpilih)
    {
      $id_provinsi = substr($id_provinsi_terpilih,0,2);

      //fungsi transpose array
        function transpose($array) {
            array_unshift($array, null);
            return call_user_func_array('array_map', $array);
        }

      //Perhitungan jumlah kolom SP10 dan SUPAS 2015
          include "koneksi.php";

      //Cek Apakah sudah ada data provinsi terkait atau belum.
          $sql_dataCek = "SELECT *
                          FROM data_kabkota_lp
                          WHERE SUBSTRING(data_kabkota_lp.id_wilayah,1,2) = '" . $id_provinsi . "'"; 
          $query_dataCek = mysqli_query($host,$sql_dataCek) or die(mysqli_error());
          $cek = '';
          while($row = mysqli_fetch_array($query_dataCek)){ 
            $cek++;
          };

      if($cek){
        //Ambil tahun yang ada
          $sql_tahun = "SELECT tahun_dasar
                        FROM data_kabkota_lp
                        GROUP BY tahun_dasar
                        having count(*)>1 ";
          $query_tahun = mysqli_query($host,$sql_tahun) or die(mysqli_error());
          $tahun='';
          while($row = mysqli_fetch_array($query_tahun)){ 
            $tahun++; //jumlah tahun yang ada.
            $tahun_data[] = $row['tahun_dasar'];
          };


        //Mulai ambil data
          for($t=0;$t<count($tahun_data);$t++){
            //Ambil jumlah laki2 per kabupaten/kota per tahun
            $sql_dataLaki2 = "SELECT *
                              FROM data_kabkota_lp, master_wilayah
                              WHERE data_kabkota_lp.id_wilayah = master_wilayah.id_wilayah
                              AND data_kabkota_lp.jenis_kelamin = 'l'
                              AND data_kabkota_lp.tahun_dasar = '" . $tahun_data[$t] . "'
                              AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'";
            $query_dataLaki2 = mysqli_query($host,$sql_dataLaki2) or die(mysqli_error());

            $i='';
            unset($nama_wilayah);
            unset($id_wilayah);
            while($row = mysqli_fetch_array($query_dataLaki2)){ 
              $i++;
              $nama_wilayah[] = $row['nama_wilayah'];
              $id_wilayah[] = $row['id_wilayah'];
              for($umur=5;$umur<81;$umur=$umur+5){
                ${'ku_'.$umur.'_laki2_'.$tahun_data[$t]}[] = $row['ku_'.$umur];
              };
            };
                  
            for($j=0;$j<count($nama_wilayah);$j++){
              $b=0;
              for($umur=5;$umur<81;$umur=$umur+5){
                $b = ${'ku_'.$umur.'_laki2_'.$tahun_data[$t]}[$j] + $b;
                ${'jumlah_laki2_'.$tahun_data[$t]}[$j] = $b;
              };
            };

            //Ambil jumlah perempuan per kabupaten/kota per tahun
            $sql_dataPerempuan = "SELECT *
                                  FROM data_kabkota_lp, master_wilayah
                                  WHERE data_kabkota_lp.id_wilayah = master_wilayah.id_wilayah
                                  AND data_kabkota_lp.jenis_kelamin = 'p'
                                  AND data_kabkota_lp.tahun_dasar = '" . $tahun_data[$t] . "'
                                  AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'"; 
            $query_dataPerempuan = mysqli_query($host,$sql_dataPerempuan) or die(mysqli_error());

            $i='';
            while($row = mysqli_fetch_array($query_dataPerempuan)){ 
              $i++;
              for($umur=5;$umur<81;$umur=$umur+5){
                ${'ku_'.$umur.'_perempuan_'.$tahun_data[$t]}[] = $row['ku_'.$umur];
              };
            };
                  
            for($j=0;$j<count($nama_wilayah);$j++){
              $b=0;
              for($umur=5;$umur<81;$umur=$umur+5){
                $b = ${'ku_'.$umur.'_perempuan_'.$tahun_data[$t]}[$j] + $b;
                ${'jumlah_perempuan_'.$tahun_data[$t]}[$j] = $b;
              };
            };

          //Hitung jumlah penduduk untuk semua tahun data
            for($k=0;$k<$i;$k++){
              ${'jumlah_penduduk_'.$tahun_data[$t]}[$k] = ${'jumlah_laki2_'.$tahun_data[$t]}[$k] + ${'jumlah_perempuan_'.$tahun_data[$t]}[$k];
            }

          //Hitung total penduduk semua kabupaten/kota pertahun
            $total_tahun[$t] = array_sum(${'jumlah_penduduk_'.$tahun_data[$t]});
          };

        //Jadiin dataProvider
          $data_kabkota['id_wilayah'] = $id_wilayah;
          $data_kabkota['nama_wilayah'] = $nama_wilayah;
          for($t=0;$t<count($tahun_data);$t++){
            $data_kabkota[$tahun_data[$t]] = ${'jumlah_penduduk_'.$tahun_data[$t]};
          };

          $attributes = []; 
          for($coba=0;$coba<count($data_kabkota);$coba++) {
            $attributes[] = $coba;
          };

          $data_kabkota_transpose = transpose($data_kabkota);

          $provider = new ArrayDataProvider([ //menjadikan multidimensional array ke bentuk provider
            'key' => '0',
            'allModels' => $data_kabkota_transpose,
            'pagination' => [
              'pageSize' => count($id_wilayah),
            ],
            'sort' => [
              'attributes' => $data_kabkota,
            ],
          ]);

        return $this->render('index', [
          'dataProvider' => $provider,
          'provinsi_terpilih' => $provinsi_terpilih,
          'status_data' => 'pilih_provinsi',
          'status_proyeksi' => 'sudah ada data',
          'tahun_data' => $tahun_data,
          'attributes' => $attributes,
          'total_tahun' => $total_tahun,
        ]);

      } 
      elseif(!$cek){ //Jika tidak ada
        return $this->render('index', [
          'status_proyeksi' => 'belum ada data',
          'status_data' => 'pilih_provinsi',
          'provinsi_terpilih' => $provinsi_terpilih,
        ]);
      };
    }

    public function actionCreate()
    {
        $model = new DataKabkotaLp();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/site/index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }




    public function actionUpdate($kabkota, $id_kabkota, $tahun_dasar, $jenis_kelamin)
    {
        //buat model dengan id yang terkait
          //Buat Koneksi
            include "koneksi.php";

        //Ambil no_data
            $sql_noData = "SELECT no_data
                          FROM data_kabkota_lp
                          WHERE id_wilayah = '". $id_kabkota ."'
                          AND tahun_dasar = '". $tahun_dasar."'
                          AND jenis_kelamin = '". $jenis_kelamin ."'";
            $query_noData = mysqli_query($host,$sql_noData) or die(mysqli_error());
            $tahun='';
            while($row = mysqli_fetch_array($query_noData)){ 
                $id = $row['no_data'];
            };      


        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        //

        return $this->render('update', [
            'model' => $model,
            'kabkota' => $kabkota, 
            'id_kabkota' => $id_kabkota,
            'tahun_dasar' => $tahun_dasar, 
            'jenis_kelamin' => $jenis_kelamin,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionHapus()
    {
        //Hapus hasil proyeksi yang sudah pernah dibuat-->
          $id_provinsi = substr(Yii::$app->user->identity->wilayah_id, 0, 2);
        //Buat koneksi ke DB
          include "koneksi.php";
            $sql_hapusData = "DELETE 
                              FROM data_kabkota_lp
                              WHERE SUBSTRING(id_wilayah,1,2) = '" . $id_provinsi . "'";
            $query_hapusData = mysqli_query($host,$sql_hapusData) or die(mysqli_error());

        return $this->redirect(['index']);
          
    }

    public function actionImport()
    {
        $modelImport = new \yii\base\DynamicModel([
                    'fileImport'=>'File Import',
                ]);
        $modelImport->addRule(['fileImport'],'required');
        $modelImport->addRule(['fileImport'],'file',['extensions'=>'ods,xls,xlsx'],['maxSize'=>1024*1024]);

        if(Yii::$app->request->post()){
            $modelImport->fileImport = \yii\web\UploadedFile::getInstance($modelImport,'fileImport');
            if($modelImport->fileImport && $modelImport->validate()){
                $inputFileType = \PHPExcel_IOFactory::identify($modelImport->fileImport->tempName);
                $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($modelImport->fileImport->tempName);
                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
                $baseRow = 2;
                while(!empty($sheetData[$baseRow]['B'])){
                    $model = new DataKabkotaLp();
                    $model->id_wilayah = (string)$sheetData[$baseRow]['B'];
                    $model->tahun_dasar = (string)$sheetData[$baseRow]['C'];
                    $model->jenis_kelamin = (string)$sheetData[$baseRow]['D'];
                    $model->ku_5 = (string)$sheetData[$baseRow]['E'];
                    $model->ku_10 = (string)$sheetData[$baseRow]['F'];
                    $model->ku_15 = (string)$sheetData[$baseRow]['G'];
                    $model->ku_20 = (string)$sheetData[$baseRow]['H'];
                    $model->ku_25 = (string)$sheetData[$baseRow]['I'];
                    $model->ku_30 = (string)$sheetData[$baseRow]['J'];
                    $model->ku_35 = (string)$sheetData[$baseRow]['K'];
                    $model->ku_40 = (string)$sheetData[$baseRow]['L'];
                    $model->ku_45 = (string)$sheetData[$baseRow]['M'];
                    $model->ku_50 = (string)$sheetData[$baseRow]['N'];
                    $model->ku_55 = (string)$sheetData[$baseRow]['O'];
                    $model->ku_60 = (string)$sheetData[$baseRow]['P'];
                    $model->ku_65 = (string)$sheetData[$baseRow]['Q'];
                    $model->ku_70 = (string)$sheetData[$baseRow]['R'];
                    $model->ku_75 = (string)$sheetData[$baseRow]['S'];
                    $model->ku_80 = (string)$sheetData[$baseRow]['T'];
                    $model->save();
                    $baseRow++;
                }
                Yii::$app->getSession()->setFlash('success','Success');
                return $this->redirect(['index']);
            }else{
                Yii::$app->getSession()->setFlash('error','Error');
            }
        }

        return $this->render('import',[
                'modelImport' => $modelImport,
            ]);
    }

    protected function findModel($id)
    {
        if (($model = DataKabkotaLp::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
