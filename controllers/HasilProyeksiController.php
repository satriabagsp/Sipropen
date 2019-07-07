<?php

namespace app\controllers;

use Yii;
use app\models\HasilProyeksi;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\controllers\HasilProyeksiController; 

/**
 * HasilProyeksiController implements the CRUD actions for HasilProyeksi model.
 */
class HasilProyeksiController extends Controller
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
     * Lists all HasilProyeksi models.
     * @return mixed
     */
    public function actionIndex()
    {
        //Buat ngefilter data yang ditampilin di tabel, harus sesuai dengan daerahnya.
        if (Yii::$app->user->identity->role == 'provinsi'){
            $dataProvider = new ActiveDataProvider([
                'query' => HasilProyeksi::find()->where(['provinsi' => Yii::$app->user->identity->username]),
                'pagination' => [
                    'pageSize' => 10,
                ],
                'sort' => [
                    'defaultOrder' => [
                    'provinsi' => SORT_ASC
                    ]
                ],
            ]);
        } else if (Yii::$app->user->identity->role == 'kab_kota'){
            $dataProvider = new ActiveDataProvider([
                'query' => HasilProyeksi::find()->where(['kab_kota' => Yii::$app->user->identity->username]),
                'pagination' => [
                    'pageSize' => 10,
                ],
                'sort' => [
                    'defaultOrder' => [
                    'provinsi' => SORT_ASC
                    ]
                ],
            ]);
        } else { 
            $dataProvider = ''; //pusat sengaja dikosongin biar bisa dibedain apakah negliat dari laporan atau langsung.
        };

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single HasilProyeksi model.
     * @param string $id
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
     * Displays a single HasilProyeksi model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionTambah($tambah1)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => HasilProyeksi::find()->where(['provinsi' => $tambah1])
            ,'sort' => [
                'defaultOrder' => [
                'provinsi' => SORT_ASC
                ]
            ]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'prov' => $tambah1,
        ]);
    }


    /**
     * Creates a new HasilProyeksi model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new HasilProyeksi();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->kab_kota]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing HasilProyeksi model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->kab_kota]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing HasilProyeksi model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the HasilProyeksi model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return HasilProyeksi the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = HasilProyeksi::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
