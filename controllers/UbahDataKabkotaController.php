<?php

namespace app\controllers;

use Yii;
use app\models\UbahDataKabkota;
use app\models\DataKabkotaLp;
use app\models\Laporan;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UbahDataKabkotaController implements the CRUD actions for UbahDataKabkota model.
 */
class UbahDataKabkotaController extends Controller
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
     * Lists all UbahDataKabkota models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => UbahDataKabkota::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UbahDataKabkota model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($nama_wilayah, $desk)
    {   
        $tahun_dasar = substr($desk, 2, 4);
        $jenis_kelamin = substr($desk, 0, 1);

        //cari id dari tabel data kabkota untuk wilayah,jk,tahun terkait
            //Buat Koneksi
            include "koneksi.php";

            //ambil id_wilayah dari master_wilayah
            $sql_wil = "SELECT id_wilayah
                        FROM master_wilayah
                        WHERE nama_wilayah = '". $nama_wilayah ."'";
            $query_wil = mysqli_query($host,$sql_wil) or die(mysqli_error());
            while($row = mysqli_fetch_array($query_wil)){ 
                $id_wilayah = $row['id_wilayah'];
            };

            //Ambil no_data dari data kabkota
            $sql_noData = "SELECT no_data
                          FROM data_kabkota_lp
                          WHERE id_wilayah = '" . $id_wilayah . "'
                          AND tahun_dasar = '". $tahun_dasar."'
                          AND jenis_kelamin = '". $jenis_kelamin ."'";
            $query_noData = mysqli_query($host,$sql_noData) or die(mysqli_error());
            while($row = mysqli_fetch_array($query_noData)){ 
                $id_data = $row['no_data'];
            };

            //Ambil no_data dari data ubahan data kabkota
            $sql_noData = "SELECT no_ubah_data
                          FROM ubah_data_kabkota
                          WHERE id_wilayah = '" . $id_wilayah . "'
                          AND tahun_data = '". $tahun_dasar."'
                          AND jenis_kelamin = '". $jenis_kelamin ."'";
            $query_noData = mysqli_query($host,$sql_noData) or die(mysqli_error());
            while($row = mysqli_fetch_array($query_noData)){ 
                $id_data_ubahan = $row['no_ubah_data'];
            };

        return $this->render('view', [
            'model_ubahan' => $this->findModel($id_data_ubahan),
            'model_data_kabkota' => DataKabkotaLp::findOne($id_data),
            'nama_wilayah' => $nama_wilayah,
            'id_wilayah' =>$id_wilayah,
            'desk' => $desk,
        ]);
    }

    /**
     * Creates a new UbahDataKabkota model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($kabkota, $id_kabkota, $tahun_dasar, $jenis_kelamin)
    {
        $model1 = new UbahDataKabkota();

        //Inisiasi variabel untuk menampilkan form untuk input ubah data bagi role kabupaten/kota
            $model2 = new Laporan();

        //Cari nomor data terkait di tabel data_kabkota_lp, untuk ditampilin pas ubahdata
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
                $id_data = $row['no_data'];
            };

        if ($model1->load(Yii::$app->request->post()) && $model2->load(Yii::$app->request->post()) && $model1->save() && $model2->save()) {
            return $this->redirect(['/data-kabkota-lp/view', 'id' => $id_kabkota]);
        }

        return $this->render('create', [
            'model1' => $model1,
            'model2' => $model2,
            'id_data' => $id_data,
            'kabkota' => $kabkota, 
            'id_kabkota' => $id_kabkota,
            'tahun_dasar' => $tahun_dasar, 
            'jenis_kelamin' => $jenis_kelamin,
        ]);
    }

    /**
     * Updates an existing UbahDataKabkota model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $nama_wilayah)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/site/index']);
        }

        return $this->render('update', [
            'model' => $model,
            'nama_wilayah' => $nama_wilayah,
        ]);
    }

    /**
     * Deletes an existing UbahDataKabkota model.
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
     * Finds the UbahDataKabkota model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UbahDataKabkota the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UbahDataKabkota::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
