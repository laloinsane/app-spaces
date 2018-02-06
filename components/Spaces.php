<?php
namespace app\components;
use Yii;
use yii\helpers\Html;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;
use yii\base\Component;

class Spaces extends Component{
    public $content;
    public $client;
    private $key, $secret, $region, $version, $endpoint, $bucket_name;
    
    public function __construct() {
        $this->key = $this->getKey();
        $this->secret = $this->getSecret();
        $this->region = $this->getRegion();
        $this->version = $this->getVersion();
        $this->endpoint = $this->getEndpoint();
        $this->bucket_name = $this->getBucketname();

        $this->client = new S3Client([
            'region' => $this->region,
            'version' => $this->version,
            'endpoint' => $this->endpoint,
            'credentials' => [
                'key' => $this->key,
                'secret' => $this->secret
            ]
        ]); 
    }
	
	public function init(){
        parent::init();
    }

    private function getKey(){
        return $this->decode(Yii::$app->db->createCommand('select apikey from config;')->queryScalar());
    }

    private function getSecret(){
        return $this->decode(Yii::$app->db->createCommand('select secret from config;')->queryScalar());
    }

    private function getRegion(){
        return Yii::$app->db->createCommand('select region from config;')->queryScalar();
    }

    private function getVersion(){
        return Yii::$app->db->createCommand('select version from config;')->queryScalar();
    }

    private function getEndpoint(){
        return Yii::$app->db->createCommand('select endpoint from config;')->queryScalar();
    }

    private function getBucketname(){
        return Yii::$app->db->createCommand('select bucket_name from config;')->queryScalar();
    }

    private function decode($pass_encode){
        return base64_decode($pass_encode);
    }

    public function listBuckets(){
        $buckets = $this->client->listBuckets();
        $bucket_names=[];
        foreach ($buckets['Buckets'] as $bucket){
            $bucket_names[]= $bucket['Name'];
        } 
       return  $bucket_names;
    }
    
    public function putFolderBucket($name){
        try{
            $resultado = $this->client->putObject([
                'Bucket'     => $this->bucket_name,
                'Key'        => $this->prepareName($name).'/',
            ]);

            $url = $this->client->getObjectUrl($this->bucket_name, $this->prepareName($name).'/');
            
            $result = array('status' => 200, 'result' => $url, 'message' => 'Carpeta de almacenamiento creada con éxito!');
            return $result;

        } catch (S3Exception $e) {
            $result = array('status' => 400, 'result' => $e->getMessage(), 'message' => 'No se ha creado ninguna carpeta de almacenamiento!');
            return $result;
        }
	}

    public function putObjectBucket($type, $ruta, $name){
        try{
            $resultado = $this->client->putObject([
                'Bucket'     => $this->bucket_name,
                'Key'        => $this->prepareName($name),
                'SourceFile' => $ruta,
                'ACL' => 'public-read',
                'ContentType' => $type,
            ]);

            $url = $this->client->getObjectUrl($this->bucket_name, $this->prepareName($name));
            
            $result = array('status' => 200, 'result' => $url);
            return $result;

        } catch (S3Exception $e) {
            $result = array('status' => 400, 'result' => $e->getMessage());
            return $result;
        }
    }

    public function changeACLpublic($name){
        try{
            $resultado = $this->client->putObjectAcl([
                'Bucket'     => $this->bucket_name,
                'Key'        => $name,
                'ACL' => 'public-read',
            ]);
            
            $result = array('status' => 200, 'result' => 'Change OK', 'message' => 'ACL cambiado con éxito!');
            return $result;

        } catch (S3Exception $e) {
            $result = array('status' => 400, 'result' => $e->getMessage(), 'message' => 'No se ha cambiado ACL!');
            return $result;
        }
    }

    public function changeACLprivate($name){
        try{
            $resultado = $this->client->putObjectAcl([
                'Bucket'     => $this->bucket_name,
                'Key'        => $name,
                'ACL' => 'private',
            ]);
            
            $result = array('status' => 200, 'result' => 'Change OK', 'message' => 'ACL cambiado con éxito!');
            return $result;

        } catch (S3Exception $e) {
            $result = array('status' => 400, 'result' => $e->getMessage(), 'message' => 'No se ha cambiado ACL!');
            return $result;
        }
    }


    /*Obtiene el listado de objetos de una carpeta
    *Ordena y devuelve un array con los objetos y sus propiedades: nombre(key), permiso, peso, fecha modif.
    */
    public function getFolderBucket($carpeta){
        $objetos=array();

        try {
            $objects = $this->client->getIterator('ListObjects', array(
                'Bucket' => $this->bucket_name,
                'Prefix' => $carpeta
            ));

            foreach ($objects as $object) {
                if ($object['Key'] != $carpeta) {
                    $key=substr($object['Key'], strlen($carpeta));     
                    $permiso = $this->getACLObject($carpeta.$key);
                    $data =  ['nombre' => $key, 
                                'size' => $this->size( $object['Size']), 
                                'last' => $object['LastModified']->format('Y-m-d H:i')  , 
                                'permiso' => $permiso['result']
                            ];
                    
                    array_push($objetos, $data);
                }
            }

           return ['status' => 200, 'result' => $objetos, 'message' => 'Elementos obtenidos con éxito'];
          

        } catch (S3Exception $e) {
            $result = array('status' => 400, 'result' => $e->getMessage(), 'message' => 'No se ha obtenido los elementos correspondientes a la carpeta');
            return $result;
        }
    }

    public function deleteObjectBucket($name){
        try{
            $resultado = $this->client->deleteObject([
                'Bucket'     => $this->bucket_name,
                'Key'        => $name
            ]);

            $result = array('status' => 200, 'result' => 'Delete OK', 'message' => 'Elemento eliminado con éxito!');
            return $result;

        } catch (S3Exception $e) {
            $result = array('status' => 400, 'result' => $e->getMessage(), 'message' => 'No se ha eliminado el elemento');
            return $result;
        }
    }

    public function getACLObject($name){
        try{
            $resultado = $this->client->getObjectAcl([
                'Bucket'     => $this->bucket_name,
                'Key'        => $name
            ]);
           

           return  ['status' => 200, 'result' =>  $resultado->get('Grants')[0]["Permission"].'', 
                        'message' => 'El ACL se ha recuperado con éxito!'];
           

        } catch (S3Exception $e) {
            $result = array('status' => 400, 'result' => $e->getMessage(), 'message' => 'No se ha recuperado el ACL');
            return $result;
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

    public function prepareName($name){          
        $unwanted_array  = array('Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', ' ' => '-');
        return strtr(strtolower($name),$unwanted_array);
    }
	
}