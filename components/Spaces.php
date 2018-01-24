<?php
namespace app\components;
use yii\helpers\Html;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use yii\base\Component;
use yii\helpers\ArrayHelper;


class Spaces extends Component{
    public $content;
    public $client;
    
    public function __construct() {
        $this->client = new S3Client([
            'region' => 'nyc3',
            'version' => 'latest',
            'endpoint' => 'https://nyc3.digitaloceanspaces.com',
            'credentials' => [
                'key'    => '',
                'secret' => ''
            ]
        ]);
    }
	
	public function init(){
        parent::init();

    }

    public function listBuckets(){
        $buckets = $this->client->listBuckets();
        $bucket_names=[];
        foreach ($buckets['Buckets'] as $bucket){
            $bucket_names[]= $bucket['Name'];
        } 
       return  $bucket_names;
    }
    
    public function putObjectBucket($bucket_name,$ruta,$name){
        try{
            $resultado = $this->client->putObject([
                'Bucket'     => $bucket_name,
                'Key'        => $name,
                'SourceFile' => $ruta,
                'ACL' => 'public-read'
            ]);

            $url = $this->client->getObjectUrl($bucket_name, $name);
            
            echo $url;

        } catch (S3Exception $e) {
            echo ($e->getMessage());
        }
	}

    public function getFolderBucket($bucket_name, $carpeta){
        $objetos=[];

        try {
            $objects = $this->client->getIterator('ListObjects', array(
                'Bucket' => $bucket_name,
                'Prefix' => $carpeta
            ));

            foreach ($objects as $object) {
                if ($object['Key'] != $carpeta) {
                    $data=substr($object['Key'],strlen($carpeta));
                    $objetos[]= $data;
                }
            }

            return  $objetos;

        } catch (S3Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }

	
	public function display($content=null){
		  
		echo "hello from spacescomponent";
	}
	
}