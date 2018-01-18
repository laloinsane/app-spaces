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

    //IMPLEMENTAR LA CLASE S3 Y S3Request que se encuentra en la carpeta components
    // utilizando la libreria //https://github.com/ericnorris/amazon-s3-php
    //primero crear cliente para usar las librerias => $client = new Yii::$app->S3($access_key, $secret_key ,$endpoint);
    //las funciones se llaman de la sgte manera => echo Yii::$app->S3->welcome();die();
    

   /*     endpoint_url='https://nyc3.digitaloceanspaces.com',
                        aws_access_key_id='R3IT7XCRBGXUXGEOSDKD',
                        aws_secret_access_key='k5nrioot9Kz79XlAlld6eGPw4FK7QiWffEaShnn8isI'*/
            return $this->render('view', [
            'model' => $taller,

        ]);
    }

    public function actionUpload($id)
    {
        $taller=$this->findModel($id);
        
        /*funcion que sube un archivo*/

        return "comenzar a subir al taller=".json_encode($taller->nombre); 
        /*return $this->render('view', [
            'model' => $taller,
        ]);*/
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
}
