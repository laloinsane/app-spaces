<?php
namespace app\components;
use yii\helpers\Html;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use yii\base\Component;

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
    
    public function putObjectBucket($bucket_name, $type, $ruta, $name){
        try{
            $resultado = $this->client->putObject([
                'Bucket'     => $bucket_name,
                'Key'        => $name,
                'SourceFile' => $ruta,
                'ACL' => 'public-read',
                'ContentType' => $type,
            ]);

            $url = $this->client->getObjectUrl($bucket_name, $name);
            
            return $url;

        } catch (S3Exception $e) {
            echo ($e->getMessage());
        }
    }
    
    public function putFolderBucket($bucket_name, $name){
        try{
            $resultado = $this->client->putObject([
                'Bucket'     => $bucket_name,
                'Key'        => $name.'/',
            ]);

            $url = $this->client->getObjectUrl($bucket_name, $name.'/');
            
            return $url;

        } catch (S3Exception $e) {
            echo ($e->getMessage());
        }
	}

    public function getFolderBucket($bucket_name, $carpeta){
        $objetos=array();

        try {
            $objects = $this->client->getIterator('ListObjects', array(
                'Bucket' => $bucket_name,
                'Prefix' => $carpeta
            ));

            foreach ($objects as $object) {
                if ($object['Key'] != $carpeta) {
                    $nombre=substr($object['Key'],strlen($carpeta));
                    $data = array('nombre' => $nombre, 'size' => $object['Size'], 'last' => $object['LastModified']);
                    array_push($objetos, $data);
                }   
            }
            return  $objetos;

        } catch (S3Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }

    public function deleteObjectBucket($bucket_name, $name){
        try{
            $resultado = $this->client->deleteObject([
                'Bucket'     => $bucket_name,
                'Key'        => $name
            ]);

        } catch (S3Exception $e) {
            echo ($e->getMessage());
        }
    }

    public function size($size){
        $mod = 1024;
        $units = explode(' ','B KB MB GB TB PB');
        for ($i = 0; $size > $mod; $i++) {
            $size /= $mod;
        }
        return round($size, 2) . ' ' . $units[$i];
    }
	
}