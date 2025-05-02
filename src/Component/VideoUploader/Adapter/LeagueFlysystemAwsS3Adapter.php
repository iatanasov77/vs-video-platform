<?php namespace App\Component\VideoUploader\Adapter;

use League\Flysystem\Filesystem;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use Aws\S3\S3ClientInterface;
use M2MTech\FlysystemStreamWrapper\FlysystemStreamWrapper;
use App\Component\Cloud\DigitalOcean\DigitalOceanInterface;

class LeagueFlysystemAwsS3Adapter extends AwsS3V3Adapter implements AwsS3AdapterInterface
{
    /** @var DigitalOceanInterface */
    protected $do;
    
    public function __construct( DigitalOceanInterface $do )
    {
        //parent::__construct( $do->getS3Client(), $do->getBucket(), ['create' => true] );
        parent::__construct( $do->getS3Client(), $do->getBucket() );
        
        $this->do   = $do;
        $filesystem = new Filesystem( $this );
        
        $this->do->getS3Client()->registerStreamWrapper();
        FlysystemStreamWrapper::register( 'fly', $filesystem );
    }
    
    public function getBucket(): string
    {
        return $this->do->getBucket();
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