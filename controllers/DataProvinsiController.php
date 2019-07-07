<?php

namespace app\controllers;

use Yii;
use app\models\DataProvinsi;
use app\models\Wilayah;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DataProvinsiController implements the CRUD actions for DataProvinsi model.
 */
class DataProvinsiController extends Controller
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
     * Lists all DataProvinsi models.
     * @return mixed
     */
    public function actionIndex()
    {
        //Buat ngefilter data yang ditampilin di tabel, harus sesuai dengan daerahnya.
        if (Yii::$app->user->identity->role == 'provinsi'){
            //Ambil tahun yang ada
                include "koneksi.php";
                //Ambil tahun yang ada
                $sql_tahun = "SELECT tahun_proy_prov
                            FROM data_provinsi
                            GROUP BY tahun_proy_prov
                            having count(*)>1 ";
                $query_tahun = mysqli_query($host,$sql_tahun) or die(mysqli_error());
                while($row = mysqli_fetch_array($query_tahun)){ 
                    $tahun_proy_prov[] = $row['tahun_proy_prov'];
                };

                //Ambil id provinsi
                $sql_idProv = "SELECT *
                               FROM wilayah
                               WHERE provinsi = '" . Yii::$app->user->identity->username . "'
                               GROUP BY provinsi
                               having count(*)>1 ";
                $query_idProv = mysqli_query($host,$sql_idProv) or die(mysqli_error());
                while($row = mysqli_fetch_array($query_idProv)){ 
                    $id_provinsi = $row['id_provinsi'];
                };

            $dataProvider = new ActiveDataProvider([
                'query' =>  DataProvinsi::find()->where(['id_provinsi' => $id_provinsi]),
                'pagination' => [
                    'pageSize' => count($tahun_proy_prov),
                ],
                'sort' => [
                    'defaultOrder' => [
                    'tahun_proy_prov' => SORT_ASC
                    ]
                ]
            ]);

            return $this->render('index', [
                'dataProvider' => $dataProvider,
            ]);

        } else {
            $sql = "SELECT *
                FROM wilayah
                GROUP BY id_provinsi
                having count(*)>1";
            $dataProvider = new ActiveDataProvider([
                'query' => Wilayah::findBySql($sql),
                'pagination' => [
                    'pageSize' => 34,
                ],
            ]);

            return $this->render('index', [
              'status_data' => 'kosong',
              'dataProvider' => $dataProvider,
            ]);
        };
    }

    /**
     * Displays a single DataKabkotaLp model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionLihat($id_provinsi_terpilih, $provinsi_terpilih)
    {
        
        $dataProvider = new ActiveDataProvider([
            'query' =>  DataProvinsi::find()->where(['id_provinsi' => $id_provinsi_terpilih]),
            'sort' => [
                'defaultOrder' => [
                'tahun_proy_prov' => SORT_ASC
                ]
            ]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'provinsi_terpilih' => $provinsi_terpilih,
            'status_data' => 'pilih_provinsi',
        ]);
    }

    /**
     * Displays a single DataProvinsi model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new DataProvinsi model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DataProvinsi();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->no_data_prov]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DataProvinsi model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->no_data_prov]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DataProvinsi model.
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
     * Finds the DataProvinsi model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DataProvinsi the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DataProvinsi::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
