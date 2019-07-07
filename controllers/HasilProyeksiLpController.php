<?php

namespace app\controllers;

use Yii;
use app\models\HasilProyeksiLp;
use app\models\HasilProyeksiJumlah;
use app\models\MasterWilayah;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;

/**
 * HasilProyeksiLpController implements the CRUD actions for HasilProyeksiLp model.
 */
class HasilProyeksiLpController extends Controller
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

    /**
     * Lists all HasilProyeksiLp models.
     * @return mixed
     */
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
              'stat' => 'pusat',
            ]);
        }

        elseif (Yii::$app->user->identity->role == 'provinsi'){

          //Koneksi
              include "koneksi.php";
          
          //Periksa apakah sudah ada data proyeksi yang dibuat.
            $id_provinsi = substr(Yii::$app->user->identity->wilayah_id,0,2);
            $sql_dataCek = "SELECT *
                            FROM hasil_proyeksi_lp, master_wilayah
                            WHERE hasil_proyeksi_lp.id_wilayah = master_wilayah.id_wilayah
                            AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'"; 
            $query_dataCek = mysqli_query($host,$sql_dataCek) or die(mysqli_error());
            $hasilCek='';
            while($row = mysqli_fetch_array($query_dataCek)){ 
              $hasilCek++;
            };

          if ($hasilCek){
            //Menampilkan kabkota di wilayahnya saja (kode depannya sama)
              $sql = "SELECT *
                      FROM master_wilayah
                      WHERE SUBSTRING(id_wilayah,1,2) = '".substr(Yii::$app->user->identity->wilayah_id, 0,2)."'
                      AND SUBSTRING(id_wilayah,3,2) <> '00'";
              $dataProvider = new ActiveDataProvider([
                'query' => MasterWilayah::findBySql($sql),
              ]);

              return $this->render('index', [
                'dataProvider' => $dataProvider,
                'status' => 'belum ada data',
                'status_proyeksi' => 'Sudah ada proyeksi',
                'status_data' => 'kosong',
              ]);
          } elseif (!$hasilCek){
            return $this->render('index', [
                'status' => 'belum ada data',
                'status_proyeksi' => 'Belum ada data',
                'status_data' => 'kosong',
            ]);
          };
        
        }

        elseif (Yii::$app->user->identity->role == 'kabkota'){
          $id_kabkota_terpilih = Yii::$app->user->identity->wilayah_id;
          $id_provinsi = substr($id_kabkota_terpilih,0,2);

          //Koneksi
              include "koneksi.php";

          //ambil nama wilayah dari id yang ada
              $sql_wil = "SELECT nama_wilayah, id_wilayah
                          FROM master_wilayah
                          WHERE id_wilayah = '" . $id_kabkota_terpilih . "'";
              $query_wil = mysqli_query($host,$sql_wil) or die(mysqli_error());
              $tahun='';
              while($row = mysqli_fetch_array($query_wil)){ 
                  $nama_kabkota_terpilih = $row['nama_wilayah'];
              };

          //Periksa apakah sudah ada data proyeksi yang dibuat.
              $sql_dataCek = "SELECT *
                              FROM hasil_proyeksi_lp, master_wilayah
                              WHERE hasil_proyeksi_lp.id_wilayah = master_wilayah.id_wilayah
                              AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'"; 
              $query_dataCek = mysqli_query($host,$sql_dataCek) or die(mysqli_error());
              $hasilCek='';
              while($row = mysqli_fetch_array($query_dataCek)){ 
                $hasilCek++;
              };

          if ($hasilCek){
            //ambil data jumlah proyeksi tahunan dan tahun proyeksi yangada
                $sql_dataProyeksi = "SELECT *
                                     FROM hasil_proyeksi_jumlah
                                     WHERE id_wilayah = '" . $id_kabkota_terpilih . "'
                                     AND jenis_kelamin = 'l+p'";  
                $dataProvider1 = new ActiveDataProvider([
                  'query' => HasilProyeksiJumlah::findBySql($sql_dataProyeksi),
                ]);


            return $this->render('index', [
                'dataProvider1' => $dataProvider1,
                'nama_kabkota_terpilih' => $nama_kabkota_terpilih,
                'id_kabkota_terpilih' => $id_kabkota_terpilih,
                'status_proyeksi' => 'Sudah ada proyeksi',
            ]);
          } 
          elseif (!$hasilCek){
            return $this->render('index', [
              'status_proyeksi' => 'Belum ada data',
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

        //Buat koneksi
            include "koneksi.php";

        //cari data dengan no_proyeksi_jumlah terkait
            $sql_cari = "SELECT *
                        FROM hasil_proyeksi_jumlah
                        WHERE no_proyeksi_jumlah = '" . $id . "'";
            $query_cari = mysqli_query($host,$sql_cari) or die(mysqli_error());
            $tahun='';
            while($row = mysqli_fetch_array($query_cari)){ 
                $id_wilayah = $row['id_wilayah'];
                $tahun_proyeksi = $row['tahun_proyeksi'];
            };

        //cari nama_wilayah dengan id_wilayah terkait
            $sql_wil = "SELECT *
                        FROM master_wilayah
                        WHERE id_wilayah = '" . $id_wilayah . "'";
            $query_wil = mysqli_query($host,$sql_wil) or die(mysqli_error());
            while($row = mysqli_fetch_array($query_wil)){ 
                $nama_wilayah = $row['nama_wilayah'];
            };

        //Buat kolom kelompok umur
            $ku[] = '0-4'; $ku[] = '5-9'; $ku[] = '10-14'; $ku[] = '15-19'; $ku[] = '20-24';
            $ku[] = '25-29'; $ku[] = '30-34'; $ku[] = '35-39'; $ku[] = '40-44'; $ku[] = '45-49';
            $ku[] = '50-54'; $ku[] = '55-59'; $ku[] = '60-64'; $ku[] = '65-69'; $ku[] = '70-74';
            $ku[] = '75+'; 

        //Ambil data laki2 pada tahun proyeksi terpilih.
            $sql_dataLaki2 = "SELECT *
                                FROM hasil_proyeksi_lp
                                WHERE jenis_kelamin = 'l'
                                AND tahun_proyeksi = '" . $tahun_proyeksi . "'
                                AND id_wilayah = '" . $id_wilayah . "'";
            $query_dataLaki2 = mysqli_query($host,$sql_dataLaki2) or die(mysqli_error());
            $i=0;
            while($row = mysqli_fetch_array($query_dataLaki2)){ 
                $i++;
                for($umur=5;$umur<81;$umur=$umur+5){
                    $data_laki2[] = $row['kup_'.$umur];
                };
            };

        //Ambil data perempuan 2010.
            $sql_dataPerempuan = "SELECT *
                                FROM hasil_proyeksi_lp
                                WHERE jenis_kelamin = 'p'
                                AND tahun_proyeksi = '" . $tahun_proyeksi . "'
                                AND id_wilayah = '" . $id_wilayah  . "'"; 
            $query_dataPerempuan = mysqli_query($host,$sql_dataPerempuan) or die(mysqli_error());

            while($row = mysqli_fetch_array($query_dataPerempuan)){ 
                for($umur=5;$umur<81;$umur=$umur+5){
                    $data_perempuan[] = $row['kup_'.$umur];
                };
            };

        //Buat data jumlah perkelompok umur di tahun proyeksi
            for($j=0;$j<count($data_laki2);$j++){
                $data_jumlah[$j] =  $data_laki2[$j] + $data_perempuan[$j];
                $data_laki2_pp[$j] = $data_laki2[$j] * -1;
                $data_perempuan_pp[$j] = $data_perempuan[$j] * 1;
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
            'tahun_terpilih' => $tahun_proyeksi,
            'dataProvider_ku' => $provider,
            'ku' => $ku,
            'nama_wilayah' => $nama_wilayah,
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

    public function actionLihat($provinsi_terpilih, $id_provinsi_terpilih)
    {
      //fungsi transpose array
        function transpose($array) {
            array_unshift($array, null);
            return call_user_func_array('array_map', $array);
        }

      //Perhitungan jumlah kolom SP10 dan SUPAS 2015
        include "koneksi.php";

      //Mengambil id_wilayah dari tabel master_wilayah dari nama_wilayah provinsi_terpilih
        $sql_wil = "SELECT id_wilayah
                    FROM master_wilayah
                    WHERE nama_wilayah = '".$provinsi_terpilih."'";
        $query_wil = mysqli_query($host,$sql_wil) or die(mysqli_error());
        $tahun='';
        while($row = mysqli_fetch_array($query_wil)){ 
          $id_provinsi_terpilih = $row['id_wilayah'];
        };

      //Periksa apakah provinsi terpilih sudah ada data proyeksi yang dibuat.
        $id_provinsi = substr($id_provinsi_terpilih, 0, 2);
        $sql_dataCek = "SELECT *
                        FROM hasil_proyeksi_jumlah, master_wilayah
                        WHERE hasil_proyeksi_jumlah.id_wilayah = master_wilayah.id_wilayah
                        AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'"; 
        $query_dataCek = mysqli_query($host,$sql_dataCek) or die(mysqli_error());
        $hasilCek='';
        while($row = mysqli_fetch_array($query_dataCek)){ 
          $hasilCek++;
        };

      if ($hasilCek){  

        //Menampilkan kabkota di wilayahnya saja (kode depannya sama)
              $sql = "SELECT *
                      FROM master_wilayah
                      WHERE SUBSTRING(id_wilayah,1,2) = '".$id_provinsi."'
                      AND SUBSTRING(id_wilayah,3,2) <> '00'";
              $dataProvider = new ActiveDataProvider([
                'query' => MasterWilayah::findBySql($sql),
              ]);

              return $this->render('index', [
                'dataProvider' => $dataProvider,
                'status_proyeksi' => 'Sudah ada proyeksi',
                'status_data' => 'pilih_provinsi',
              ]);

      } elseif (!$hasilCek){
          return $this->render('index', [
              'status_proyeksi' => 'Belum ada data',
              'stat' => 'pusat',
              'status_data' => 'pilih_provinsi',
              'provinsi_terpilih' => $provinsi_terpilih,
          ]);
        };
    }

    public function actionLihatkabkota($id_kabkota_terpilih)
    {
        $id_provinsi = substr($id_kabkota_terpilih,0,2);

        //fungsi transpose array
            function transpose($array) {
                array_unshift($array, null);
                return call_user_func_array('array_map', $array);
            }

        //Koneksi
            include "koneksi.php";

        //ambil nama wilayah dari id yang ada
            $sql_wil = "SELECT nama_wilayah, id_wilayah
                        FROM master_wilayah
                        WHERE id_wilayah = '" . $id_kabkota_terpilih . "'";
            $query_wil = mysqli_query($host,$sql_wil) or die(mysqli_error());
            $tahun='';
            while($row = mysqli_fetch_array($query_wil)){ 
                $nama_kabkota_terpilih = $row['nama_wilayah'];
            };

        //ambil data jumlah proyeksi tahunan dan tahun proyeksi yangada
            $sql_dataProyeksi = "SELECT *
                                 FROM hasil_proyeksi_jumlah
                                 WHERE id_wilayah = '" . $id_kabkota_terpilih . "'
                                 AND jenis_kelamin = 'l+p'";  
            $dataProvider1 = new ActiveDataProvider([
              'query' => HasilProyeksiJumlah::findBySql($sql_dataProyeksi),
            ]);


        return $this->render('index', [
            'dataProvider1' => $dataProvider1,
            'nama_kabkota_terpilih' => $nama_kabkota_terpilih,
            'id_kabkota_terpilih' => $id_kabkota_terpilih,
            'status_data' => 'pilih kabkota',
        ]);
    }

    /**
     * Creates a new HasilProyeksiLp model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new HasilProyeksiLp();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->no_proyeksi]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing HasilProyeksiLp model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->no_proyeksi]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing HasilProyeksiLp model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the HasilProyeksiLp model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return HasilProyeksiLp the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = HasilProyeksiLp::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
