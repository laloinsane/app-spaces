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
                'key'    => 'N5YWF77JKZ75JL5YVWAD',
                'secret' => '1L+fTZ3UaSfu0+V5Xu8JLDAcbBSmBndRnZkc3lKPy7o'
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
           // $url = $resultado['ObjectURL'];          
            $url = $this->client->getObjectUrl($bucket_name, $name);
            
            echo $url;
        } catch (S3Exception $e) {
            //return $e->getMessage();
            echo ($e->getMessage());
        }
	}
    
   
	
	public function display($content=null){
		  
		echo "hello from spacescomponent";
	}
	
}