<?php



namespace app\controllers;

use Yii;
use app\models\DataProvinsiLp;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;
use app\models\MasterWilayah;

ini_set('max_execution_time', 300);

/**
 * DataProvinsiLpController implements the CRUD actions for DataProvinsiLp model.
 */
class DataProvinsiLpController extends Controller
{
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
          'status' => 'belum ada data',
        ]);
      }

      elseif (Yii::$app->user->identity->role == 'provinsi' || Yii::$app->user->identity->role == 'kabkota'){
        //Perhitungan jumlah kolom SP10 dan SUPAS 2015
          include "koneksi.php";

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

        //ambil nama wilayah dari id yang ada
          $id_wilayah = substr(Yii::$app->user->identity->wilayah_id, 0, 2);
          $sql_wil = "SELECT nama_wilayah, id_wilayah
                      FROM master_wilayah
                      WHERE id_wilayah = '" . $id_wilayah . "00'";
          $query_wil = mysqli_query($host,$sql_wil) or die(mysqli_error());
          $tahun='';
          while($row = mysqli_fetch_array($query_wil)){ 
            $nama_provinsi = $row['nama_wilayah'];
            $id_provinsi = $row['id_wilayah'];
          };

        //Cek Apakah sudah ada data provinsi terkait atau belum.
          $sql_dataCek = "SELECT *
                          FROM data_provinsi_lp
                          WHERE id_wilayah = '" . $id_provinsi . "'"; 
          $query_dataCek = mysqli_query($host,$sql_dataCek) or die(mysqli_error());
          $cek = '';
          while($row = mysqli_fetch_array($query_dataCek)){ 
            $cek++;
          };

          if($cek){ //Jika ada data
            //Mulai ambil data
              for($t=0;$t<count($tahun_data);$t++){
                //Ambil jumlah laki2 per provinsi per tahun
                  $sql_dataLaki2 = "SELECT *
                                    FROM data_provinsi_lp
                                    WHERE id_wilayah = '" . $id_provinsi . "'
                                    AND jenis_kelamin = 'l'
                                    AND tahun_data = '" . $tahun_data[$t] . "'"; 
                  $query_dataLaki2 = mysqli_query($host,$sql_dataLaki2) or die(mysqli_error());

                  unset($id_wilayah);
                  while($row = mysqli_fetch_array($query_dataLaki2)){ 
                    $id_wilayah[] = $row['id_wilayah'];
                    for($umur=5;$umur<81;$umur=$umur+5){
                      ${'jumlah_laki2_'.$tahun_data[$t]}[] = $row['ku_'.$umur];
                    }; //Ini variabel jumlah penduduk laki-laki per tahun per provinsi
                  };

                //Ambil jumlah perempuan per kabupaten/kota per tahun
                  $sql_dataPerempuan = "SELECT *
                                        FROM data_provinsi_lp
                                        WHERE id_wilayah = '" . $id_provinsi . "'
                                        AND jenis_kelamin = 'p'
                                        AND tahun_data = '" . $tahun_data[$t] . "'";  
                  $query_dataPerempuan = mysqli_query($host,$sql_dataPerempuan) or die(mysqli_error());

                  while($row = mysqli_fetch_array($query_dataPerempuan)){ 
                    for($umur=5;$umur<81;$umur=$umur+5){
                      ${'jumlah_perempuan_'.$tahun_data[$t]}[] = $row['ku_'.$umur];
                    }; //Ini variabel jumlah penduduk perempuan per tahun per provinsi
                  };
                      
                //Hitung jumlah penduduk tiap tahunnya
                  $jumlah_provinsi[] = array_sum(${'jumlah_laki2_'.$tahun_data[$t]}) + array_sum(${'jumlah_perempuan_'.$tahun_data[$t]});

              };

              //Jadiin dataProvider
                $data_prov['tahun'] = $tahun_data;
                //$data_prov['nama_wilayah'] = $nama_wilayah;
                $data_prov['jumlah_provinsi'] = $jumlah_provinsi;

                $data_prov_transpose = transpose($data_prov);

                $provider = new ArrayDataProvider([ //menjadikan multidimensional array ke bentuk provider
                  'key' => '0',
                  'allModels' => $data_prov_transpose,
                  'pagination' => [
                    'pageSize' => count($tahun_data),
                  ],
                  'sort' => [
                    'attributes' => ['0', '1'],
                  ],
                ]);

              return $this->render('index', [
                  'dataProvider' => $provider,
                  'nama_provinsi' => $nama_provinsi,
                  'id_provinsi' => $id_provinsi,
                  'status' => 'sudah ada data',
              ]);
          } 

          elseif(!$cek){ //Jika tidak ada
            return $this->render('index', [
                'status' => 'belum ada data',
            ]);
          };
      }
    }

    public function actionLihat($provinsi_terpilih)
    {
      //fungsi transpose array
        function transpose($array) {
            array_unshift($array, null);
            return call_user_func_array('array_map', $array);
        }
        //Perhitungan jumlah kolom SP10 dan SUPAS 2015
          include "koneksi.php";

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
                                FROM data_provinsi_lp, master_wilayah
                                WHERE data_provinsi_lp.id_wilayah = '" . $provinsi_terpilih . "'
                                AND master_wilayah.id_wilayah = data_provinsi_lp.id_wilayah
                                AND data_provinsi_lp.jenis_kelamin = 'l'
                                AND data_provinsi_lp.tahun_data = '" . $tahun_data[$t] . "'"; 
              $query_dataLaki2 = mysqli_query($host,$sql_dataLaki2) or die(mysqli_error());

              unset($id_wilayah);
              while($row = mysqli_fetch_array($query_dataLaki2)){ 
                $id_wilayah[] = $row['id_wilayah'];
                $nama_wilayah[] = $row['nama_wilayah'];
                  for($umur=5;$umur<81;$umur=$umur+5){
                    ${'jumlah_laki2_'.$tahun_data[$t]}[] = $row['ku_'.$umur];
                }; //Ini variabel jumlah penduduk laki-laki per tahun per provinsi
              };

            //Ambil jumlah perempuan per kabupaten/kota per tahun
              $sql_dataPerempuan = "SELECT *
                                    FROM data_provinsi_lp
                                    WHERE id_wilayah = '" . $provinsi_terpilih . "'
                                    AND jenis_kelamin = 'p'
                                    AND tahun_data = '" . $tahun_data[$t] . "'";  
              $query_dataPerempuan = mysqli_query($host,$sql_dataPerempuan) or die(mysqli_error());

              while($row = mysqli_fetch_array($query_dataPerempuan)){ 
                for($umur=5;$umur<81;$umur=$umur+5){
                  ${'jumlah_perempuan_'.$tahun_data[$t]}[] = $row['ku_'.$umur];
                }; //Ini variabel jumlah penduduk perempuan per tahun per provinsi
              };
                    
            //Hitung jumlah penduduk tiap tahunnya
              $jumlah_provinsi[] = array_sum(${'jumlah_laki2_'.$tahun_data[$t]}) + array_sum(${'jumlah_perempuan_'.$tahun_data[$t]});

          };

          //Jadiin dataProvider
            $data_prov['tahun'] = $tahun_data;
            //$data_prov['nama_wilayah'] = $nama_wilayah;
            $data_prov['jumlah_provinsi'] = $jumlah_provinsi;

            $data_prov_transpose = transpose($data_prov);

            $provider = new ArrayDataProvider([ //menjadikan multidimensional array ke bentuk provider
              'key' => '0',
              'allModels' => $data_prov_transpose,
              'pagination' => [
                'pageSize' => count($tahun_data),
              ],
              'sort' => [
                'attributes' => ['0', '1'],
              ],
            ]);

          return $this->render('index', [
            'status_data' => 'pilih_provinsi',
            'status' => 'sudah ada data',
            'nama_provinsi' => $provinsi_terpilih,
            'dataProvider' => $provider,
            'id_wilayah' => $id_wilayah,
            'nama_wilayah' => $nama_wilayah,
            'tahun_data' => $tahun_data,
            'jumlah_provinsi' => $jumlah_provinsi,
          ]);
    }

    public function actionLihatdetail($id_wilayah, $nama_wilayah, $tahun_terpilih)
    {
      //fungsi transpose array
        function transpose($array) {
            array_unshift($array, null);
            return call_user_func_array('array_map', $array);
        }
      //buat tabel untuk nampilin data.
        //Buat koneksi
          include "koneksi.php";

              //Buat kolom kelompok umur
                $ku[] = '0-4'; $ku[] = '5-9'; $ku[] = '10-14'; $ku[] = '15-19'; $ku[] = '20-24';
                $ku[] = '25-29'; $ku[] = '30-34'; $ku[] = '35-39'; $ku[] = '40-44'; $ku[] = '45-49';
                $ku[] = '50-54'; $ku[] = '55-59'; $ku[] = '60-64'; $ku[] = '65-69'; $ku[] = '70-74';
                $ku[] = '75+'; 

              //Ambil data laki2 pada tahun terpilih.
                $sql_dataLaki2 = "SELECT *
                                  FROM data_provinsi_lp
                                  WHERE jenis_kelamin = 'l'
                                  AND tahun_data = '" . $tahun_terpilih . "'
                                  AND id_wilayah = '" . $id_wilayah . "'";
                $query_dataLaki2 = mysqli_query($host,$sql_dataLaki2) or die(mysqli_error());
                $i=0;
                while($row = mysqli_fetch_array($query_dataLaki2)){ 
                  $i++;
                  for($umur=5;$umur<81;$umur=$umur+5){
                    $data_laki2[] = $row['ku_'.$umur];
                  };
                };

              //Ambil data perempuan 2010.
                $sql_dataPerempuan = "SELECT *
                                      FROM data_provinsi_lp
                                      WHERE jenis_kelamin = 'p'
                                      AND tahun_data = '" . $tahun_terpilih . "'
                                      AND id_wilayah = '" . $id_wilayah  . "'"; 
                $query_dataPerempuan = mysqli_query($host,$sql_dataPerempuan) or die(mysqli_error());

                while($row = mysqli_fetch_array($query_dataPerempuan)){ 
                  for($umur=5;$umur<81;$umur=$umur+5){
                    $data_perempuan[] = $row['ku_'.$umur];
                  };
                };

              //Buat data jumlah perkelompok umur tahun 2010 dan 2015
                for($j=0;$j<count($data_laki2);$j++){
                  for($umur=5;$umur<101;$umur=$umur+5){
                    ${'tot_'.$umur.'2010'} = 
                      $data_jumlah[$j] =  $data_laki2[$j] + $data_perempuan[$j];
                      $data_laki2_pp[$j] = $data_laki2[$j] * -1;
                      $data_perempuan_pp[$j] = $data_perempuan[$j] * 1;
                    };
                };

        //Jadiin dataProvider
            $data_ku[] = $ku;
            $data_ku[] = $data_laki2;
            $data_ku[] = $data_perempuan;
            $data_ku[] = $data_jumlah;

            $data_ku_transpose = transpose($data_ku);

            $provider = new ArrayDataProvider([ //menjadikan multidimensional array ke bentuk provider
              'key' => '0',
              'allModels' => $data_ku_transpose,
              'pagination' => [
                'pageSize' => count($ku),
              ],
              'sort' => [
                'attributes' => ['0', '1', '2', '3'],
              ],
            ]);

        return $this->render('view', [
            'tahun_terpilih' => $tahun_terpilih,
            'dataProvider_ku' => $provider,
            'ku' => $ku,
            'nama_wilayah' => $nama_wilayah,
            'id_wilayah' => $id_wilayah,
            'total_jumlah' => array_sum($data_jumlah),
            'total_laki2' => array_sum($data_laki2),
            'total_perempuan' => array_sum($data_perempuan),
            'data_jumlah' => $data_jumlah,
            'data_laki2' => $data_laki2,
            'data_laki2_pp' => $data_laki2_pp,
            'data_perempuan' => $data_perempuan,
            'data_perempuan_pp' => $data_perempuan_pp,
        ]);
    }

    public function actionView($id)
    {
      //fungsi transpose array
        function transpose($array) {
            array_unshift($array, null);
            return call_user_func_array('array_map', $array);
        }
      $tahun_terpilih = $id;
      //buat tabel untuk nampilin data.
        //Buat koneksi
          include "koneksi.php";

        //ambil nama wilayah dari id yang ada
          $id_wilayah = substr(Yii::$app->user->identity->wilayah_id, 0, 2);
          $sql_wil = "SELECT nama_wilayah, id_wilayah
                      FROM master_wilayah
                      WHERE id_wilayah = '" . $id_wilayah . "00'";
          $query_wil = mysqli_query($host,$sql_wil) or die(mysqli_error());
          $tahun='';
          while($row = mysqli_fetch_array($query_wil)){ 
              $nama_provinsi = $row['nama_wilayah'];
              $id_provinsi = $row['id_wilayah'];
          };

        //Buat kolom kelompok umur
          $ku[] = '0-4'; $ku[] = '5-9'; $ku[] = '10-14'; $ku[] = '15-19'; $ku[] = '20-24';
          $ku[] = '25-29'; $ku[] = '30-34'; $ku[] = '35-39'; $ku[] = '40-44'; $ku[] = '45-49';
          $ku[] = '50-54'; $ku[] = '55-59'; $ku[] = '60-64'; $ku[] = '65-69'; $ku[] = '70-74';
          $ku[] = '75+'; 

        //Ambil data laki2 pada tahun terpilih.
          $sql_dataLaki2 = "SELECT *
                            FROM data_provinsi_lp
                            WHERE jenis_kelamin = 'l'
                            AND tahun_data = '" . $tahun_terpilih . "'
                            AND id_wilayah = '" . $id_provinsi . "'";
          $query_dataLaki2 = mysqli_query($host,$sql_dataLaki2) or die(mysqli_error());
          $i=0;
          while($row = mysqli_fetch_array($query_dataLaki2)){ 
            $i++;
            for($umur=5;$umur<81;$umur=$umur+5){
              $data_laki2[] = $row['ku_'.$umur];
            };
          };

        //Ambil data perempuan pada tahun terpilih.
          $sql_dataPerempuan = "SELECT *
                                FROM data_provinsi_lp
                                WHERE jenis_kelamin = 'p'
                                AND tahun_data = '" . $tahun_terpilih . "'
                                AND id_wilayah = '" . $id_provinsi  . "'"; 
          $query_dataPerempuan = mysqli_query($host,$sql_dataPerempuan) or die(mysqli_error());

          while($row = mysqli_fetch_array($query_dataPerempuan)){ 
            for($umur=5;$umur<81;$umur=$umur+5){
              $data_perempuan[] = $row['ku_'.$umur];
            };
          };

        //Buat data jumlah perkelompok umur tahun 2010 dan 2015
          for($j=0;$j<count($data_laki2);$j++){
            for($umur=5;$umur<101;$umur=$umur+5){
              ${'tot_'.$umur.'2010'} = 
                $data_jumlah[$j] =  $data_laki2[$j] + $data_perempuan[$j];
                $data_laki2_pp[$j] = $data_laki2[$j] * -1;
                $data_perempuan_pp[$j] = $data_perempuan[$j] * 1;
              };
          };

        //Jadiin dataProvider
          $data_ku[] = $ku;
          $data_ku[] = $data_laki2;
          $data_ku[] = $data_perempuan;
          $data_ku[] = $data_jumlah;

          $data_ku_transpose = transpose($data_ku);

          $provider = new ArrayDataProvider([ //menjadikan multidimensional array ke bentuk provider
            'key' => '0',
            'allModels' => $data_ku_transpose,
            'pagination' => [
              'pageSize' => count($ku),
            ],
            'sort' => [
              'attributes' => ['0', '1', '2', '3'],
            ],
          ]);

        return $this->render('view', [
            'tahun_terpilih' => $id,
            'dataProvider_ku' => $provider,
            'ku' => $ku,
            'nama_wilayah' => $nama_provinsi,
            'id_wilayah' => $id_provinsi,
            'total_jumlah' => array_sum($data_jumlah),
            'total_laki2' => array_sum($data_laki2),
            'total_perempuan' => array_sum($data_perempuan),
            'data_jumlah' => $data_jumlah,
            'data_laki2' => $data_laki2,
            'data_laki2_pp' => $data_laki2_pp,
            'data_perempuan' => $data_perempuan,
            'data_perempuan_pp' => $data_perempuan_pp,
        ]);
    }

    public function actionCreate()
    {
        $model = new DataProvinsiLp();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_nomor]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($nama_wilayah, $id_wilayah, $tahun_terpilih, $jenis_kelamin)
    {
        //buat model dengan id yang terkait
          //Buat Koneksi
            include "koneksi.php";

        //Ambil no_data
            $sql_noData = "SELECT id_nomor
                          FROM data_provinsi_lp
                          WHERE id_wilayah = '". $id_wilayah ."'
                          AND tahun_data = '". $tahun_terpilih."'
                          AND jenis_kelamin = '". $jenis_kelamin."'";
            $query_noData = mysqli_query($host,$sql_noData) or die(mysqli_error());
            $tahun='';
            while($row = mysqli_fetch_array($query_noData)){ 
                $id = $row['id_nomor'];
            };      


        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/site/index']);
        }

        //

        return $this->render('update', [
            'model' => $model,
            'nama_wilayah' => $nama_wilayah, 
            'id_wilayah' => $id_wilayah, 
            'tahun_terpilih' => $tahun_terpilih, 
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
                              FROM data_provinsi_lp
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
                    $model = new DataProvinsiLp();
                    $model->id_wilayah = (string)$sheetData[$baseRow]['B'];
                    $model->tahun_data = (string)$sheetData[$baseRow]['C'];
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
        if (($model = DataProvinsiLp::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
