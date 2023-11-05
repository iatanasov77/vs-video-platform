<?php namespace App\Component\VideoUploader\Adapter\GaufretteAwsS3;

use Gaufrette\Adapter\AwsS3;
use App\Component\Cloud\DigitalOceanInterface;
use Aws\S3\S3ClientInterface;

class GaufretteAwsS3Adapter extends AwsS3 implements GaufretteAwsS3Interface
{
    /** @var DigitalOceanInterface */
    protected $do;
    
    public function __construct( DigitalOceanInterface $do )
    {
        $this->do   = $do;
        
        $options    = ['create' => true];
        $client     = $this->do->getS3Client();
        
        $client->registerStreamWrapper();
        
        parent::__construct( $client, $this->do->getBucket(), $options );
    }
    
    public function getBucket(): string
    {
        return $this->bucket;
    }
    
    public function getDigitalOcean(): DigitalOceanInterface
    {
        return $this->do;
    }
    
    public function getS3Client(): S3ClientInterface
    {
        return $this->do->getS3Client();
    }
}