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

        if (empty($taller->url_bucket)) {
            return $this->render('view', [
                'model' => $taller,
            ]);
        } else {
            $carpeta=substr(parse_url($taller->url_bucket, PHP_URL_PATH),1);

            $arr_result = Yii::$app->spaces->getFolderBucket($carpeta);

            if ($arr_result['status']==200) {
                /*Yii::$app->session->setFlash('msg', '
                    <div class="alert alert-success alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <strong> '.$arr_result['message'].' </strong></div>'
                );*/

                return $this->render('view', [
                    'model' => $taller,
                    'error' => false,
                    'lista_objetos' => $arr_result,
                ]);
            } else {
                if ($arr_result['status']==400) {
                    Yii::$app->session->setFlash('msg', '
                        <div class="alert alert-danger alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <strong> '.$arr_result['message'].' </strong><small> ('.$arr_result['result'].') </small></div>'
                    );

                    return $this->render('view', [
                        'model' => $taller,
                        'error' => true,
                    ]);
                }
            }
        }
    }

    public function actionSubir($id)
    {
        $taller=$this->findModel($id);
        echo $id;die();
    }

    public function actionUpload()
    {
        if (isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST") {

            $name = isset($_POST['name']) ? $_POST['name']:NULL;
            $id = isset($_POST['id']) ? $_POST['id']:NULL;
            $type = isset($_POST['type']) ? $_POST['type']:NULL;

            $ruta_tmp = $_FILES['file']['tmp_name'];


            $taller=$this->findModel($id);
            $carpeta=substr(parse_url($taller->url_bucket, PHP_URL_PATH),1);

            $arr_result = Yii::$app->spaces->putObjectBucket($type, $ruta_tmp, $carpeta.$name);

        if ($arr_result['status']==200) {
            echo "wena";
            //var_dump($arr_result['result']); die();
            return $arr_result['result'];
        } else {
            if ($arr_result['status']==400) {
                echo('charcha'); //die();
                return $this->redirect(['index']);
            }
        }

            exit;
        }


    }
/*
    public function actionUpload()
    {
        $imgfile = isset($_FILES['myfile']) ? $_FILES['myfile']:NULL;   //La imagen
        $filename = isset($_POST['filename']) ? $_POST['filename']:NULL;//nombre de la imagen
        $id = isset($_POST['id']) ? $_POST['id']:NULL; // El id de la entidad/tabla que actualiza RutaImg
          $type = isset($_POST['type']) ? $_POST['type']:NULL;
        $ruta_tmp = $_FILES['myfile']['tmp_name']; // Nombre para identificar y mover el archivo
        $nose = 'wena';

        $taller=$this->findModel($id);

        $carpeta=substr(parse_url($taller->url_bucket, PHP_URL_PATH),1);

        //$url = Yii::$app->spaces->putObjectBucket($type, $ruta_tmp, $carpeta.$filename);

        //return $url;

        $arr_result = Yii::$app->spaces->putObjectBucket($type, $ruta_tmp, $carpeta.$filename);

        if ($arr_result['status']==200) {
            return $arr_result['result'];
        } else {
            if ($arr_result['status']==400) {
                echo($arr_result['result']); die();
                return $this->redirect(['index']);
            }
        }
    }
    */

    /**
     * Creates a new Taller model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Taller();

        if ($model->load(Yii::$app->request->post())) {

            /*Si el taller es creado correctamente en la bd, entonces se crea una nueva carpeta
            en el spaces de talleres, el nombre de la carpeta es el nombre del taller + fecha de creación.
            La url de la carpeta creada en spaces es almacenada en el registro del taller creado.
            */
            $arr_result = Yii::$app->spaces->putFolderBucket($model->nombre.date('d-m-Y'));

            if ($arr_result['status']==200) {
                $model->url_bucket = $arr_result['result'];
                $model->save();
                 Yii::$app->session->setFlash('msg', '
                    <div class="alert alert-success alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <strong> '.$arr_result['message'].' </strong> </br> Desde ahora en adelante puedes almacenar y administrar contenido multimedia referente a esté taller.</div>'
                );
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                if ($arr_result['status']==400) {
                    $model->save();
                     Yii::$app->session->setFlash('msg', '
                        <div class="alert alert-danger alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <strong> '.$arr_result['message'].' </strong> </br> Necesitas crear una carpeta de almacenamiento para poder agregar contenido multimedia.</div>'
                    );
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
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
        $arr_result = Yii::$app->spaces->deleteObjectBucket($carpeta.$objeto);

        if ($arr_result['status']==200) {
            Yii::$app->session->setFlash('msg', '
                <div class="alert alert-success alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <strong> '.$arr_result['message'].' </strong></div>'
            );
            return $this->redirect(['view?id='.$id]);
        } else {
            if ($arr_result['status']==400) {
                Yii::$app->session->setFlash('msg', '
                    <div class="alert alert-danger alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <strong> '.$arr_result['message'].' </strong> <small> ('.$arr_result['result'].') </small></div>'
                );
                return $this->redirect(['view?id='.$id]);
            }
        }
    }

    public function actionPrivate($id, $objeto)
    {
        $taller=$this->findModel($id);
        $carpeta=substr(parse_url($taller->url_bucket, PHP_URL_PATH),1);
        $arr_result = Yii::$app->spaces->changeACLprivate($carpeta.$objeto);

        if ($arr_result['status']==200) {
            Yii::$app->session->setFlash('msg', '
                <div class="alert alert-success alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <strong> '.$arr_result['message'].' </strong></div>'
            );
            return $this->redirect(['view?id='.$id]);
        } else {
            if ($arr_result['status']==400) {
                Yii::$app->session->setFlash('msg', '
                    <div class="alert alert-danger alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <strong> '.$arr_result['message'].' </strong> <small> ('.$arr_result['result'].') </small></div>'
                );
                return $this->redirect(['view?id='.$id]);
            }
        }
    }

    public function actionPublic($id, $objeto)
    {
        $taller=$this->findModel($id);
        $carpeta=substr(parse_url($taller->url_bucket, PHP_URL_PATH),1);
       // $arr_result = Yii::$app->spaces->getACLObject($carpeta.$objeto);
        $arr_result = Yii::$app->spaces->changeACLpublic($carpeta.$objeto);

        if ($arr_result['status']==200) {
            Yii::$app->session->setFlash('msg', '
                <div class="alert alert-success alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <strong> '.$arr_result['message'].' </strong></div>'
            );
            return $this->redirect(['view?id='.$id]);
        } else {
            if ($arr_result['status']==400) {
                Yii::$app->session->setFlash('msg', '
                    <div class="alert alert-danger alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <strong> '.$arr_result['message'].' </strong> <small> ('.$arr_result['result'].') </small></div>'
                );
                return $this->redirect(['view?id='.$id]);
            }
        }
    }

}
