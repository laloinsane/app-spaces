<?php
use yii\helpers\Html;
use yii\widgets\DetailView;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

/* @var $this yii\web\View */
/* @var $model app\models\Taller */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tallers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="taller-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>

    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nombre',
            'url_bucket:url',
        ],
    ]) ?>

    <button id="subir-archivo" class ="btn btn-primary" data-taller-id = "<?=$model->id ?>">Subir Archivo</button>

    <?php //Yii::$app->message->display('I am Yii2.0 Programmer'); 
        echo "<br>";
        echo "Lista de Buckets: ";
          foreach ($buckets['Buckets'] as $bucket){
                echo $bucket['Name']."\n";
            }
    ?>

    <?php 

$BUCKET_NAME='joe';
//Create a S3Client
$s3Client = new S3Client([
                'region' => 'nyc3',
                'version' => 'latest',
                'endpoint' => 'https://nyc3.digitaloceanspaces.com',
                    'credentials' => [
                        'key'    => 'R3IT7XCRBGXUXGEOSDKD',
                        'secret' => 'k5nrioot9Kz79XlAlld6eGPw4FK7QiWffEaShnn8isI'
                    ]
]);
//Creating S3 Bucket
try {
    $s3Client->createBucket(['Bucket' => 'my-bucketmo']);
} catch (S3Exception $e) {
    // Catch an S3 specific exception.
    echo $e->getMessage();
} catch (AwsException $e) {
    // This catches the more generic AwsException. You can grab information
    // from the exception using methods of the exception object.
    echo $e->getAwsRequestId() . "\n";
    echo $e->getAwsErrorType() . "\n";
    echo $e->getAwsErrorCode() . "\n";
}

    ?>

    
</div>

<?php 
    $this->registerJsFile(
        '@web/js/upload.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>
