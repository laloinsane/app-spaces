<?php

namespace app\controllers;

use Yii;
use app\models\Taller;
use app\models\TallerSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TallerController implements the CRUD actions for Taller model.
 */
class TallerController extends Controller
{
    /**
     * @inheritdoc
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
     * Lists all Taller models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TallerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Taller model.
     *Recuperar el listado de elementos almacenados en la carpeta de spaces del taller segun $taller->Url
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $taller=$this->findModel($id);

        $carpeta=substr(parse_url($taller->url_bucket, PHP_URL_PATH),1);

        return $this->render('view', [
            'model' => $taller,
            'buckets'=>  Yii::$app->spaces->listBuckets(),
            'lista_objetos' => Yii::$app->spaces->getFolderBucket('nosenose4', $carpeta),
        ]);
    }

    public function actionSubir($id)
    {
        $taller=$this->findModel($id);

        echo $id;die();


    }

    public function actionUpload()
    {
        $imgfile = isset($_FILES['myfile']) ? $_FILES['myfile']:NULL;   //La imagen
        $filename = isset($_POST['filename']) ? $_POST['filename']:NULL;//nombre de la imagen
        $id = isset($_POST['id']) ? $_POST['id']:NULL; // El id de la entidad/tabla que actualiza RutaImg
        $ruta_tmp = $_FILES['myfile']['tmp_name']; // Nombre para identificar y mover el archivo
        $nose = 'wena';

        $taller=$this->findModel($id);

        $carpeta=substr(parse_url($taller->url_bucket, PHP_URL_PATH),1);

        $url = Yii::$app->spaces->putObjectBucket('nosenose4', $ruta_tmp, $carpeta.$filename);

        return $url;
       // return $filename.', '.$ruta_tmp.', '.$url; //$url.', '.$filename.','.json_encode($imgfile).', '.$imgTmpName;

        /*SUBIR AL ARCHIVO EN LA CARPETA DEL TALLER*/

        //return $url;

    
    }

    /**
     * Creates a new Taller model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Taller();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            /*Si el taller es creado correctamente en la bd, entonces se crea una nueva carpeta
            en el spaces de talleres, el nombre de la carpeta es el nombre del taller + fecha de creación.
            La url de la carpeta creada en spaces es almacenada en el registro del taller creado.
            */

            $model->url_bucket= Yii::$app->spaces->putFolderBucket('nosenose4', $model->nombre.date('d-m-Y'));
            $model->save();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Taller model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Taller model.
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
     * Finds the Taller model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Taller the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Taller::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionEliminar($id, $objeto)
    {
        $taller=$this->findModel($id);

        $carpeta=substr(parse_url($taller->url_bucket, PHP_URL_PATH),1);

        Yii::$app->spaces->deleteObjectBucket('nosenose4', $carpeta.$objeto);

        return $this->redirect(['view?id='.$id]);
    }
}
