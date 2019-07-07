<?php

namespace app\controllers;

use Yii;
use app\models\HasilProyeksiJumlah;
use app\models\MasterWilayah;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;



/**
 * HasilProyeksiJumlahController implements the CRUD actions for HasilProyeksiJumlah model.
 */
class HasilProyeksiJumlahController extends Controller
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
     * Lists all HasilProyeksiJumlah models.
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

      elseif (Yii::$app->user->identity->role == 'provinsi' || Yii::$app->user->identity->role == 'kabkota'){
        //Perhitungan jumlah kolom SP10 dan SUPAS 2015
          include "koneksi.php";

        $id_provinsi = substr(Yii::$app->user->identity->wilayah_id, 0, 2);

        //Periksa apakah sudah ada data proyeksi yang dibuat.
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
          //Ambil tahun yang ada
            $sql_tahun = "SELECT tahun_proyeksi
                          FROM hasil_proyeksi_jumlah
                          WHERE SUBSTRING(id_wilayah,1,2) = '" . $id_provinsi . "'
                          GROUP BY tahun_proyeksi
                          having count(*)>1 ";
            $query_tahun = mysqli_query($host,$sql_tahun) or die(mysqli_error());
            $tahun='';
            while($row = mysqli_fetch_array($query_tahun)){ 
                $tahun++; //jumlah tahun yang ada.
                $tahun_proyeksi[] = $row['tahun_proyeksi'];
            };

          //Mulai ambil data
            for($t=0;$t<count($tahun_proyeksi);$t++){
                //Ambil l+p per kabupaten/kota per tahun
                if(Yii::$app->user->identity->role == 'provinsi'){
                  $sql_dataProyeksi = "SELECT *
                                FROM hasil_proyeksi_jumlah, master_wilayah
                                WHERE hasil_proyeksi_jumlah.id_wilayah = master_wilayah.id_wilayah
                                AND hasil_proyeksi_jumlah.tahun_proyeksi = '" . $tahun_proyeksi[$t] . "'
                                AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'
                                AND jenis_kelamin = 'l+p'";  
                } elseif(Yii::$app->user->identity->role == 'kabkota'){
                  $sql_dataProyeksi = "SELECT *
                                FROM hasil_proyeksi_jumlah, master_wilayah
                                WHERE hasil_proyeksi_jumlah.id_wilayah = master_wilayah.id_wilayah
                                AND hasil_proyeksi_jumlah.tahun_proyeksi = '" . $tahun_proyeksi[$t] . "'
                                AND master_wilayah.id_wilayah = '" . Yii::$app->user->identity->wilayah_id . "'
                                AND jenis_kelamin = 'l+p'";   
                };
                $query_dataProyeksi = mysqli_query($host,$sql_dataProyeksi) or die(mysqli_error());
                unset($id_wilayah);
                unset($nama_wilayah);
                while($row = mysqli_fetch_array($query_dataProyeksi)){ 
                    $id_wilayah[] = $row['id_wilayah'];
                    $nama_wilayah[] = $row['nama_wilayah'];
                    ${'proyeksi_'.$tahun_proyeksi[$t]}[] = $row['jumlah_proyeksi'];
                };

                //Ambil l per kabupaten/kota per tahun
                if(Yii::$app->user->identity->role == 'provinsi'){
                  $sql_dataProyeksi = "SELECT *
                                FROM hasil_proyeksi_jumlah, master_wilayah
                                WHERE hasil_proyeksi_jumlah.id_wilayah = master_wilayah.id_wilayah
                                AND hasil_proyeksi_jumlah.tahun_proyeksi = '" . $tahun_proyeksi[$t] . "'
                                AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'
                                AND jenis_kelamin = 'l'";  
                } elseif(Yii::$app->user->identity->role == 'kabkota'){
                  $sql_dataProyeksi = "SELECT *
                                FROM hasil_proyeksi_jumlah, master_wilayah
                                WHERE hasil_proyeksi_jumlah.id_wilayah = master_wilayah.id_wilayah
                                AND hasil_proyeksi_jumlah.tahun_proyeksi = '" . $tahun_proyeksi[$t] . "'
                                AND master_wilayah.id_wilayah = '" . Yii::$app->user->identity->wilayah_id . "'
                                AND jenis_kelamin = 'l'";   
                };
                $query_dataProyeksi = mysqli_query($host,$sql_dataProyeksi) or die(mysqli_error());
                unset($id_wilayah);
                unset($nama_wilayah);
                while($row = mysqli_fetch_array($query_dataProyeksi)){ 
                    $id_wilayah[] = $row['id_wilayah'];
                    $nama_wilayah[] = $row['nama_wilayah'];
                    ${'proyeksi_laki2_'.$tahun_proyeksi[$t]}[] = $row['jumlah_proyeksi'];
                };

                //Ambil p per kabupaten/kota per tahun
                if(Yii::$app->user->identity->role == 'provinsi'){
                  $sql_dataProyeksi = "SELECT *
                                FROM hasil_proyeksi_jumlah, master_wilayah
                                WHERE hasil_proyeksi_jumlah.id_wilayah = master_wilayah.id_wilayah
                                AND hasil_proyeksi_jumlah.tahun_proyeksi = '" . $tahun_proyeksi[$t] . "'
                                AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'
                                AND jenis_kelamin = 'p'";  
                } elseif(Yii::$app->user->identity->role == 'kabkota'){
                  $sql_dataProyeksi = "SELECT *
                                FROM hasil_proyeksi_jumlah, master_wilayah
                                WHERE hasil_proyeksi_jumlah.id_wilayah = master_wilayah.id_wilayah
                                AND hasil_proyeksi_jumlah.tahun_proyeksi = '" . $tahun_proyeksi[$t] . "'
                                AND master_wilayah.id_wilayah = '" . Yii::$app->user->identity->wilayah_id . "'
                                AND jenis_kelamin = 'p'";   
                };
                $query_dataProyeksi = mysqli_query($host,$sql_dataProyeksi) or die(mysqli_error());
                unset($id_wilayah);
                unset($nama_wilayah);
                while($row = mysqli_fetch_array($query_dataProyeksi)){ 
                    $id_wilayah[] = $row['id_wilayah'];
                    $nama_wilayah[] = $row['nama_wilayah'];
                    ${'proyeksi_perempuan_'.$tahun_proyeksi[$t]}[] = $row['jumlah_proyeksi'];
                };
            };

          
          //Jadiin dataProvider l+p
            $data_proyeksi[] = $id_wilayah;
            $data_proyeksi[] = $nama_wilayah;
            for($t=0;$t<count($tahun_proyeksi);$t++){
              $data_proyeksi[] = ${'proyeksi_'.$tahun_proyeksi[$t]};
              $data_grafik[] = (int)${'proyeksi_'.$tahun_proyeksi[$t]}[0];
              $total_jumlah[] = array_sum(${'proyeksi_'.$tahun_proyeksi[$t]});
            };
            $attributes = []; 
            for($coba=0;$coba<count($data_proyeksi);$coba++) {
              $attributes[] = $coba;
            };
            $data_proyeksi_transpose = transpose($data_proyeksi);

            $provider = new ArrayDataProvider([ //menjadikan multidimensional array ke bentuk provider
              'key' => '0',
              'allModels' => $data_proyeksi_transpose,
              'pagination' => [
                'pageSize' => count($id_wilayah),
              ],
              'sort' => [
                'attributes' => $attributes,
              ],
            ]);

          //Jadiin dataProvider l
            $data_proyeksi_laki2[] = $id_wilayah;
            $data_proyeksi_laki2[] = $nama_wilayah;
            for($t=0;$t<count($tahun_proyeksi);$t++){
              $data_proyeksi_laki2[] = ${'proyeksi_laki2_'.$tahun_proyeksi[$t]};
              $data_grafik_laki2[] = (int)${'proyeksi_laki2_'.$tahun_proyeksi[$t]}[0];
              $total_jumlah_laki2[] = array_sum(${'proyeksi_laki2_'.$tahun_proyeksi[$t]});
            };
            $attributes = []; 
            for($coba=0;$coba<count($data_proyeksi_laki2);$coba++) {
              $attributes[] = $coba;
            };
            $data_proyeksi_laki2_transpose = transpose($data_proyeksi_laki2);

            $provider_laki2 = new ArrayDataProvider([ //menjadikan multidimensional array ke bentuk provider
              'key' => '0',
              'allModels' => $data_proyeksi_laki2_transpose,
              'pagination' => [
                'pageSize' => count($id_wilayah),
              ],
              'sort' => [
                'attributes' => $attributes,
              ],
            ]);

          //Jadiin dataProvider l+p
            $data_proyeksi_perempuan[] = $id_wilayah;
            $data_proyeksi_perempuan[] = $nama_wilayah;
            for($t=0;$t<count($tahun_proyeksi);$t++){
              $data_proyeksi_perempuan[] = ${'proyeksi_perempuan_'.$tahun_proyeksi[$t]};
              $data_grafik_perempuan[] = (int)${'proyeksi_perempuan_'.$tahun_proyeksi[$t]}[0];
              $total_jumlah_perempuan[] = array_sum(${'proyeksi_perempuan_'.$tahun_proyeksi[$t]});
            };
            $attributes = []; 
            for($coba=0;$coba<count($data_proyeksi_perempuan);$coba++) {
              $attributes[] = $coba;
            };
            $data_proyeksi_perempuan_transpose = transpose($data_proyeksi_perempuan);

            $provider_perempuan = new ArrayDataProvider([ //menjadikan multidimensional array ke bentuk provider
              'key' => '0',
              'allModels' => $data_proyeksi_perempuan_transpose,
              'pagination' => [
                'pageSize' => count($id_wilayah),
              ],
              'sort' => [
                'attributes' => $attributes,
              ],
            ]);

          return $this->render('index', [
              'dataProvider' => $provider,
              'dataProvider_laki2' => $provider_laki2,
              'dataProvider_perempuan' => $provider_perempuan,
              'tahun_proyeksi' => $tahun_proyeksi,
              'id_wilayah' => $id_wilayah,
              'status_proyeksi' => 'Sudah ada proyeksi',
              'total_jumlah' => $total_jumlah,
              'data_grafik' => $data_grafik,
              'nama' => $nama_wilayah,
              'cekkk' => $attributes,
          ]);

        } elseif (!$hasilCek){
          return $this->render('index', [
              'status_proyeksi' => 'Belum ada data',
          ]);
        };
      };
    }

    /**
     * Displays a single HasilProyeksiJumlah model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        //fungsi transpose array
            function transpose($array) {
                array_unshift($array, null);
                return call_user_func_array('array_map', $array);
            }

        //Buat koneksi
            include "koneksi.php";

        $id_kabkota = $id;

        //cari nama_wilayah dengan id_wilayah terkait
            $sql_wil = "SELECT *
                        FROM master_wilayah
                        WHERE id_wilayah = '" . $id_kabkota . "'";
            $query_wil = mysqli_query($host,$sql_wil) or die(mysqli_error());
            while($row = mysqli_fetch_array($query_wil)){ 
                $nama_kabkota = $row['nama_wilayah'];
            };

        //Ambil tahun yang ada
            $id_provinsi = substr($id_kabkota,0,2);
            $sql_tahun = "SELECT tahun_proyeksi
                          FROM hasil_proyeksi_jumlah
                          WHERE SUBSTRING(id_wilayah,1,2) = '" . $id_provinsi . "'
                          GROUP BY tahun_proyeksi
                          having count(*)>1 ";
            $query_tahun = mysqli_query($host,$sql_tahun) or die(mysqli_error());
            $tahun='';
            while($row = mysqli_fetch_array($query_tahun)){ 
                $tahun++; //jumlah tahun yang ada.
                $tahun_proyeksi[] = $row['tahun_proyeksi'];
            };

          //Mulai ambil data
            for($t=0;$t<count($tahun_proyeksi);$t++){
                //Ambil l+p per kabupaten/kota per tahun
                  $sql_dataProyeksi = "SELECT *
                                        FROM hasil_proyeksi_jumlah, master_wilayah
                                        WHERE hasil_proyeksi_jumlah.id_wilayah = master_wilayah.id_wilayah
                                        AND hasil_proyeksi_jumlah.tahun_proyeksi = '" . $tahun_proyeksi[$t] . "'
                                        AND master_wilayah.id_wilayah = '" . $id_kabkota . "'
                                        AND jenis_kelamin = 'l+p'";   
                $query_dataProyeksi = mysqli_query($host,$sql_dataProyeksi) or die(mysqli_error());
                unset($id_wilayah);
                unset($nama_wilayah);
                while($row = mysqli_fetch_array($query_dataProyeksi)){ 
                    $id_wilayah[] = $row['id_wilayah'];
                    $nama_wilayah[] = $row['nama_wilayah'];
                    ${'proyeksi_'.$tahun_proyeksi[$t]}[] = $row['jumlah_proyeksi'];
                };

                //Ambil l per kabupaten/kota per tahun
                  $sql_dataProyeksi = "SELECT *
                                      FROM hasil_proyeksi_jumlah, master_wilayah
                                      WHERE hasil_proyeksi_jumlah.id_wilayah = master_wilayah.id_wilayah
                                      AND hasil_proyeksi_jumlah.tahun_proyeksi = '" . $tahun_proyeksi[$t] . "'
                                      AND master_wilayah.id_wilayah = '" . $id_kabkota . "'
                                      AND jenis_kelamin = 'l'";   
                $query_dataProyeksi = mysqli_query($host,$sql_dataProyeksi) or die(mysqli_error());
                unset($id_wilayah);
                unset($nama_wilayah);
                while($row = mysqli_fetch_array($query_dataProyeksi)){ 
                    $id_wilayah[] = $row['id_wilayah'];
                    $nama_wilayah[] = $row['nama_wilayah'];
                    ${'proyeksi_laki2_'.$tahun_proyeksi[$t]}[] = $row['jumlah_proyeksi'];
                };

                //Ambil p per kabupaten/kota per tahun
                  $sql_dataProyeksi = "SELECT *
                                FROM hasil_proyeksi_jumlah, master_wilayah
                                WHERE hasil_proyeksi_jumlah.id_wilayah = master_wilayah.id_wilayah
                                AND hasil_proyeksi_jumlah.tahun_proyeksi = '" . $tahun_proyeksi[$t] . "'
                                AND master_wilayah.id_wilayah = '" . $id_kabkota . "'
                                AND jenis_kelamin = 'p'";   
                $query_dataProyeksi = mysqli_query($host,$sql_dataProyeksi) or die(mysqli_error());
                unset($id_wilayah);
                unset($nama_wilayah);
                while($row = mysqli_fetch_array($query_dataProyeksi)){ 
                    $id_wilayah[] = $row['id_wilayah'];
                    $nama_wilayah[] = $row['nama_wilayah'];
                    ${'proyeksi_perempuan_'.$tahun_proyeksi[$t]}[] = $row['jumlah_proyeksi'];
                };
            };

          
          //Jadiin dataProvider l+p
            $data_proyeksi[] = $id_wilayah;
            $data_proyeksi[] = $nama_wilayah;
            for($t=0;$t<count($tahun_proyeksi);$t++){
              $data_proyeksi[] = ${'proyeksi_'.$tahun_proyeksi[$t]};
              $data_grafik[] = (int)${'proyeksi_'.$tahun_proyeksi[$t]}[0];
              $total_jumlah[] = array_sum(${'proyeksi_'.$tahun_proyeksi[$t]});
            };
            $attributes = []; 
            for($coba=0;$coba<count($data_proyeksi);$coba++) {
              $attributes[] = $coba;
            };
            $data_proyeksi_transpose = transpose($data_proyeksi);

            $provider = new ArrayDataProvider([ //menjadikan multidimensional array ke bentuk provider
              'key' => '0',
              'allModels' => $data_proyeksi_transpose,
              'pagination' => [
                'pageSize' => count($id_wilayah),
              ],
              'sort' => [
                'attributes' => $attributes,
              ],
            ]);

          //Jadiin dataProvider l
            $data_proyeksi_laki2[] = $id_wilayah;
            $data_proyeksi_laki2[] = $nama_wilayah;
            for($t=0;$t<count($tahun_proyeksi);$t++){
              $data_proyeksi_laki2[] = ${'proyeksi_laki2_'.$tahun_proyeksi[$t]};
              $data_grafik_laki2[] = (int)${'proyeksi_laki2_'.$tahun_proyeksi[$t]}[0];
              $total_jumlah_laki2[] = array_sum(${'proyeksi_laki2_'.$tahun_proyeksi[$t]});
            };
            $attributes = []; 
            for($coba=0;$coba<count($data_proyeksi_laki2);$coba++) {
              $attributes[] = $coba;
            };
            $data_proyeksi_laki2_transpose = transpose($data_proyeksi_laki2);

            $provider_laki2 = new ArrayDataProvider([ //menjadikan multidimensional array ke bentuk provider
              'key' => '0',
              'allModels' => $data_proyeksi_laki2_transpose,
              'pagination' => [
                'pageSize' => count($id_wilayah),
              ],
              'sort' => [
                'attributes' => $attributes,
              ],
            ]);

          //Jadiin dataProvider l+p
            $data_proyeksi_perempuan[] = $id_wilayah;
            $data_proyeksi_perempuan[] = $nama_wilayah;
            for($t=0;$t<count($tahun_proyeksi);$t++){
              $data_proyeksi_perempuan[] = ${'proyeksi_perempuan_'.$tahun_proyeksi[$t]};
              $data_grafik_perempuan[] = (int)${'proyeksi_perempuan_'.$tahun_proyeksi[$t]}[0];
              $total_jumlah_perempuan[] = array_sum(${'proyeksi_perempuan_'.$tahun_proyeksi[$t]});
            };
            $attributes = []; 
            for($coba=0;$coba<count($data_proyeksi_perempuan);$coba++) {
              $attributes[] = $coba;
            };
            $data_proyeksi_perempuan_transpose = transpose($data_proyeksi_perempuan);

            $provider_perempuan = new ArrayDataProvider([ //menjadikan multidimensional array ke bentuk provider
              'key' => '0',
              'allModels' => $data_proyeksi_perempuan_transpose,
              'pagination' => [
                'pageSize' => count($id_wilayah),
              ],
              'sort' => [
                'attributes' => $attributes,
              ],
            ]);

          return $this->render('index', [
              'dataProvider' => $provider,
              'dataProvider_laki2' => $provider_laki2,
              'dataProvider_perempuan' => $provider_perempuan,
              'tahun_proyeksi' => $tahun_proyeksi,
              'id_wilayah' => $id_wilayah,
              'status_proyeksi' => 'Sudah ada proyeksi',
              'total_jumlah' => $total_jumlah,
              'data_grafik' => $data_grafik,
              'nama' => $nama_wilayah,
              'cekkk' => $attributes,
              'stat' => 'pilih',
          ]);

        return $this->render('view', [
            'model' => $this->findModel($id),
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

        //Ambil tahun yang ada
          $id_provinsi = substr($id_provinsi_terpilih,0,2);
          $sql_tahun = "SELECT tahun_proyeksi
                        FROM hasil_proyeksi_jumlah
                        WHERE SUBSTRING(id_wilayah,1,2) = '" . $id_provinsi . "'
                        GROUP BY tahun_proyeksi
                        having count(*)>1 ";
          $query_tahun = mysqli_query($host,$sql_tahun) or die(mysqli_error());
          $tahun='';
          while($row = mysqli_fetch_array($query_tahun)){ 
            $tahun++; //jumlah tahun yang ada.
            $tahun_proyeksi[] = $row['tahun_proyeksi'];
          };

            //Mulai ambil data
              for($t=0;$t<count($tahun_proyeksi);$t++){
                  //Ambil l+p per kabupaten/kota per tahun
                    $sql_dataProyeksi = "SELECT *
                                          FROM hasil_proyeksi_jumlah, master_wilayah
                                          WHERE hasil_proyeksi_jumlah.id_wilayah = master_wilayah.id_wilayah
                                          AND hasil_proyeksi_jumlah.tahun_proyeksi = '" . $tahun_proyeksi[$t] . "'
                                          AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'
                                          AND jenis_kelamin = 'l+p'";   
                  $query_dataProyeksi = mysqli_query($host,$sql_dataProyeksi) or die(mysqli_error());
                  unset($id_wilayah);
                  unset($nama_wilayah);
                  while($row = mysqli_fetch_array($query_dataProyeksi)){ 
                      $id_wilayah[] = $row['id_wilayah'];
                      $nama_wilayah[] = $row['nama_wilayah'];
                      ${'proyeksi_'.$tahun_proyeksi[$t]}[] = $row['jumlah_proyeksi'];
                  };

                  //Ambil l per kabupaten/kota per tahun
                    $sql_dataProyeksi = "SELECT *
                                        FROM hasil_proyeksi_jumlah, master_wilayah
                                        WHERE hasil_proyeksi_jumlah.id_wilayah = master_wilayah.id_wilayah
                                        AND hasil_proyeksi_jumlah.tahun_proyeksi = '" . $tahun_proyeksi[$t] . "'
                                        AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'
                                        AND jenis_kelamin = 'l'";   
                  $query_dataProyeksi = mysqli_query($host,$sql_dataProyeksi) or die(mysqli_error());
                  unset($id_wilayah);
                  unset($nama_wilayah);
                  while($row = mysqli_fetch_array($query_dataProyeksi)){ 
                      $id_wilayah[] = $row['id_wilayah'];
                      $nama_wilayah[] = $row['nama_wilayah'];
                      ${'proyeksi_laki2_'.$tahun_proyeksi[$t]}[] = $row['jumlah_proyeksi'];
                  };

                  //Ambil p per kabupaten/kota per tahun
                    $sql_dataProyeksi = "SELECT *
                                  FROM hasil_proyeksi_jumlah, master_wilayah
                                  WHERE hasil_proyeksi_jumlah.id_wilayah = master_wilayah.id_wilayah
                                  AND hasil_proyeksi_jumlah.tahun_proyeksi = '" . $tahun_proyeksi[$t] . "'
                                  AND SUBSTRING(master_wilayah.id_wilayah,1,2) = '" . $id_provinsi . "'
                                  AND jenis_kelamin = 'p'";   
                  $query_dataProyeksi = mysqli_query($host,$sql_dataProyeksi) or die(mysqli_error());
                  unset($id_wilayah);
                  unset($nama_wilayah);
                  while($row = mysqli_fetch_array($query_dataProyeksi)){ 
                      $id_wilayah[] = $row['id_wilayah'];
                      $nama_wilayah[] = $row['nama_wilayah'];
                      ${'proyeksi_perempuan_'.$tahun_proyeksi[$t]}[] = $row['jumlah_proyeksi'];
                  };
              };

            
            //Jadiin dataProvider l+p
              $data_proyeksi[] = $id_wilayah;
              $data_proyeksi[] = $nama_wilayah;
              for($t=0;$t<count($tahun_proyeksi);$t++){
                $data_proyeksi[] = ${'proyeksi_'.$tahun_proyeksi[$t]};
                $data_grafik[] = (int)${'proyeksi_'.$tahun_proyeksi[$t]}[0];
                $total_jumlah[] = array_sum(${'proyeksi_'.$tahun_proyeksi[$t]});
              };
              $attributes = []; 
              for($coba=0;$coba<count($data_proyeksi);$coba++) {
                $attributes[] = $coba;
              };
              $data_proyeksi_transpose = transpose($data_proyeksi);

              $provider = new ArrayDataProvider([ //menjadikan multidimensional array ke bentuk provider
                'key' => '0',
                'allModels' => $data_proyeksi_transpose,
                'pagination' => [
                  'pageSize' => count($id_wilayah),
                ],
                'sort' => [
                  'attributes' => $attributes,
                ],
              ]);

            //Jadiin dataProvider l
              $data_proyeksi_laki2[] = $id_wilayah;
              $data_proyeksi_laki2[] = $nama_wilayah;
              for($t=0;$t<count($tahun_proyeksi);$t++){
                $data_proyeksi_laki2[] = ${'proyeksi_laki2_'.$tahun_proyeksi[$t]};
                $data_grafik_laki2[] = (int)${'proyeksi_laki2_'.$tahun_proyeksi[$t]}[0];
                $total_jumlah_laki2[] = array_sum(${'proyeksi_laki2_'.$tahun_proyeksi[$t]});
              };
              $attributes = []; 
              for($coba=0;$coba<count($data_proyeksi_laki2);$coba++) {
                $attributes[] = $coba;
              };
              $data_proyeksi_laki2_transpose = transpose($data_proyeksi_laki2);

              $provider_laki2 = new ArrayDataProvider([ //menjadikan multidimensional array ke bentuk provider
                'key' => '0',
                'allModels' => $data_proyeksi_laki2_transpose,
                'pagination' => [
                  'pageSize' => count($id_wilayah),
                ],
                'sort' => [
                  'attributes' => $attributes,
                ],
              ]);

            //Jadiin dataProvider l+p
              $data_proyeksi_perempuan[] = $id_wilayah;
              $data_proyeksi_perempuan[] = $nama_wilayah;
              for($t=0;$t<count($tahun_proyeksi);$t++){
                $data_proyeksi_perempuan[] = ${'proyeksi_perempuan_'.$tahun_proyeksi[$t]};
                $data_grafik_perempuan[] = (int)${'proyeksi_perempuan_'.$tahun_proyeksi[$t]}[0];
                $total_jumlah_perempuan[] = array_sum(${'proyeksi_perempuan_'.$tahun_proyeksi[$t]});
              };
              $attributes = []; 
              for($coba=0;$coba<count($data_proyeksi_perempuan);$coba++) {
                $attributes[] = $coba;
              };
              $data_proyeksi_perempuan_transpose = transpose($data_proyeksi_perempuan);

              $provider_perempuan = new ArrayDataProvider([ //menjadikan multidimensional array ke bentuk provider
                'key' => '0',
                'allModels' => $data_proyeksi_perempuan_transpose,
                'pagination' => [
                  'pageSize' => count($id_wilayah),
                ],
                'sort' => [
                  'attributes' => $attributes,
                ],
              ]);

        return $this->render('index', [
          'dataProvider' => $provider,
          'dataProvider_laki2' => $provider_laki2,
          'dataProvider_perempuan' => $provider_perempuan,
          'provinsi_terpilih' => $provinsi_terpilih,
          'id_provinsi_terpilih' => $id_provinsi_terpilih,
          'status_data' => 'pilih_provinsi',
          'tahun_proyeksi' => $tahun_proyeksi,
          'id_wilayah' => $id_wilayah,
          'total_jumlah' => $total_jumlah,
          'nama' => $nama_wilayah,
          'cekkk' => $data_proyeksi,
          'stat' => 'pusat',
          'status_proyeksi' => 'Sudah ada proyeksi',
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

    /**
     * Creates a new HasilProyeksiJumlah model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new HasilProyeksiJumlah();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->no_proyeksi_jumlah]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing HasilProyeksiJumlah model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->no_proyeksi_jumlah]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing HasilProyeksiJumlah model.
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
     * Finds the HasilProyeksiJumlah model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return HasilProyeksiJumlah the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = HasilProyeksiJumlah::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
