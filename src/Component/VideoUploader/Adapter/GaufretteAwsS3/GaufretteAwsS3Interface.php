<?php namespace App\Component\VideoUploader\Adapter\GaufretteAwsS3;

use App\Component\Cloud\DigitalOceanInterface;
use Aws\S3\S3ClientInterface;

interface GaufretteAwsS3Interface
{
    public function getBucket(): string;
    public function getDigitalOcean(): DigitalOceanInterface;
    public function getS3Client(): S3ClientInterface;
}