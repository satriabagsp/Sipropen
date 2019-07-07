<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\base\DynamicModel;
use yii\data\ArrayDataProvider;

//fungsi transpose array
function transpose($array) {
    array_unshift($array, null);
    return call_user_func_array('array_map', $array);
}

class SiteController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest){
            return $this->render('about');
        }else{
        return $this->render('index');
        }
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionProynasional()
    {
        return $this->render('proynasional');
    }

    public function actionProyprovinsi()
    {
        return $this->render('proyprovinsi');
    }

    public function actionProykabkota()
    {
      if (Yii::$app->user->identity->role == 'pusat' || Yii::$app->user->identity->role == 'kabkota'){
        return $this->render('proykabkota');
      }

      elseif (Yii::$app->user->identity->role == 'provinsi'){

        $model_td = new \yii\base\DynamicModel(['tahun_dasar', 'tahun_target','panjang_tahun']);
        $model_td->addRule(['tahun_dasar', 'tahun_target','panjang_tahun'], 'required')->addRule(['tahun_dasar','tahun_target','panjang_tahun'], 'string', ['max' => 128]);

        if($model_td->load(Yii::$app->request->post()) && $model_td->validate()){
          //Buat dataprovider berisi data dasar proyeksi penduduk
          //Buat koneksi ke DB
            include "koneksi.php";

          //Ambil tahun yang ada
            $tahun_dasar = $model_td->tahun_dasar;
            $tahun_target = $model_td->tahun_target;
            $panjang_tahun = $model_td->panjang_tahun;
            $tahun_terpilih[] = $tahun_dasar;
            $tahun_terpilih[] = $tahun_target;
            
          //Mulai ambil data
            $id_provinsi = substr(Yii::$app->user->identity->wilayah_id, 0, 2);
            for($t=0;$t<count($tahun_terpilih);$t++){
              //Ambil jumlah laki2 per kabupaten/kota per tahun
                $sql_dataLaki2 = "SELECT *
                                  FROM data_kabkota_lp, master_wilayah
                                  WHERE data_kabkota_lp.id_wilayah = master_wilayah.id_wilayah
                                  AND data_kabkota_lp.jenis_kelamin = 'l'
                                  AND data_kabkota_lp.tahun_dasar = '" . $tahun_terpilih[$t] . "'
                                  AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'";
                $query_dataLaki2 = mysqli_query($host,$sql_dataLaki2) or die(mysqli_error());

                $i='';
                unset($id_wilayah);
                unset($nama_wilayah);
                while($row = mysqli_fetch_array($query_dataLaki2)){ 
                  $i++;
                  $id_wilayah[] = $row['id_wilayah'];
                  $nama_wilayah[] = $row['nama_wilayah'];
                  for($umur=5;$umur<81;$umur=$umur+5){
                    ${'ku_'.$umur.'_laki2_'.$tahun_terpilih[$t]}[] = $row['ku_'.$umur];
                  }; //Ini variabel jumlah penduduk laki-laki per kelompok umur per kabupaten tahun dasar dan tahun target
                };
                        
                for($j=0;$j<count($id_wilayah);$j++){
                  $b=0;
                  for($umur=5;$umur<81;$umur=$umur+5){
                    $b = ${'ku_'.$umur.'_laki2_'.$tahun_terpilih[$t]}[$j] + $b;
                    ${'jumlah_laki2_'.$tahun_terpilih[$t]}[$j] = $b;
                  };
                }; //Ini variabel jumlah penduduk laki-laki per kabupaten tahun dasar dan tahun target

              //Ambil jumlah perempuan per kabupaten/kota per tahun
                $sql_dataPerempuan = "SELECT *
                                  FROM data_kabkota_lp, master_wilayah
                                  WHERE data_kabkota_lp.id_wilayah = master_wilayah.id_wilayah
                                  AND data_kabkota_lp.jenis_kelamin = 'p'
                                  AND data_kabkota_lp.tahun_dasar = '" . $tahun_terpilih[$t] . "'
                                  AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'";
                $query_dataPerempuan = mysqli_query($host,$sql_dataPerempuan) or die(mysqli_error());

                $i='';
                while($row = mysqli_fetch_array($query_dataPerempuan)){ 
                  $i++;
                  for($umur=5;$umur<81;$umur=$umur+5){
                    ${'ku_'.$umur.'_perempuan_'.$tahun_terpilih[$t]}[] = $row['ku_'.$umur];
                  }; //Ini variabel jumlah penduduk perempuan per kelompok umur per kabupaten tahun dasar dan tahun target
                };
                      
                for($j=0;$j<count($id_wilayah);$j++){
                  $b=0;
                  for($umur=5;$umur<81;$umur=$umur+5){
                    $b = ${'ku_'.$umur.'_perempuan_'.$tahun_terpilih[$t]}[$j] + $b;
                    ${'jumlah_perempuan_'.$tahun_terpilih[$t]}[$j] = $b;
                  }; //Ini variabel jumlah penduduk perempuan per kabupaten tahun dasar dan tahun target
                };

              //Hitung jumlah penduduk per kabkota tahun dasar dan tahun target
                for($k=0;$k<count($id_wilayah);$k++){
                  ${'jumlah_penduduk_'.$tahun_terpilih[$t]}[$k] = ${'jumlah_laki2_'.$tahun_terpilih[$t]}[$k] + ${'jumlah_perempuan_'.$tahun_terpilih[$t]}[$k];
                };
                  
            };

              //Maka data jumlah penduduk per kabupaten/kota tahun 2010 ada di variabel array $jumlah_penduduk_2010, dan
              //data jumlah penduduk per kabupaten/kota tahun 2015 ada di variabel array $jumlah_penduduk_2015.
              //data kabupaten/kota di provinsi tersebut ada di array $kabkota.
                                              
              //Menghitung Laju Pertumbuhan Penduduk (LPP) dan sex ratio per kabupaten/kota
                for($hit=0;$hit<count($id_wilayah);$hit++){
                  $lpp_tahunan[] = round(pow(${'jumlah_penduduk_'.$tahun_target}[$hit] / ${'jumlah_penduduk_'.$tahun_dasar}[$hit] , 12/60) - 1 , 4); //Perhitungan lpp tahunan
                  $lpp_bulanan[] = round(pow(${'jumlah_penduduk_'.$tahun_target}[$hit] / ${'jumlah_penduduk_'.$tahun_dasar}[$hit] , 1/60) - 1 , 4); //Perhitungan lpp tahunan
                  $sr[] = round(${'jumlah_laki2_'.$tahun_target}[$hit] / ${'jumlah_perempuan_'.$tahun_target}[$hit] , 4); //Perhitungan lpp
                };


              //Jadiin dataProvider
                $data_kabkota['id_wilayah'] = $id_wilayah;
                $data_kabkota['nama_wilayah'] = $nama_wilayah;
                for($t=0;$t<count($tahun_terpilih);$t++){
                  $data_kabkota[$tahun_terpilih[$t]] = ${'jumlah_penduduk_'.$tahun_terpilih[$t]};
                };
                $data_kabkota['lpp_tahunan'] = $lpp_tahunan;
                $data_kabkota['lpp_bulanan'] = $lpp_bulanan;
                $data_kabkota['sr'] = $sr;

                $data_kabkota_transpose = transpose($data_kabkota);

                $provider = new ArrayDataProvider([ //menjadikan multidimensional array ke bentuk provider
                  'key' => '0',
                  'allModels' => $data_kabkota_transpose,
                  'pagination' => [
                    'pageSize' => 20,
                  ],
                  'sort' => [
                    'attributes' => ['0', '1', '2', '3', '4', '5', '6', '7'],
                  ],
                ]);

                return $this->render('proykabkota', [
                  'cek' => 'tampil_tahun',
                  'dataProvider1' => $provider,
                  'kabkota' => $nama_wilayah,
                  'lpp_tahunan' => $lpp_tahunan,
                  'lpp_bulanan' => $lpp_bulanan,
                  'tahun_terpilih' => $tahun_terpilih,
                  'tahun_dasar' => $tahun_dasar,
                  'tahun_target' => $tahun_target,
                  'panjang_tahun' => $panjang_tahun,
                  'total_tahun_dasar' => array_sum(${'jumlah_penduduk_'.$tahun_dasar}),
                  'total_tahun_target' => array_sum(${'jumlah_penduduk_'.$tahun_target}),
                ]);
        }

        return $this->render('proykabkota', [
          'cek' => 'pilih_tahun',
          'model_td' => $model_td,
        ]);
      };
    }


    public function actionSimpan($id, $id_wilayah, $tahun_dasar, $jenis_kelamin, $status)
    {
        return $this->render('simpan', [
            'asal' => $id,
            'id_wilayah' => $id_wilayah,
            'stat' => $status,
            'jenis_kelamin' => $jenis_kelamin,
            'tahun_dasar' => $tahun_dasar,
        ]);
    }

    /**
     * Displays proyeksikan page.
     *
     * @return string
     */
    public function actionProyeksikan($status)
    {
        return $this->render('proyeksikan', [
            'status' => $status,
        ]);
    }

    public function actionProyeksikan2($status, $tahun_dasar, $tahun_target, $panjang_tahun)
    {
        return $this->render('proyeksikan2', [
            'status' => $status,
            'tahun_dasar' => $tahun_dasar,
            'tahun_target' => $tahun_target,
            'panjang_tahun' => $panjang_tahun,
        ]);
    }
}
