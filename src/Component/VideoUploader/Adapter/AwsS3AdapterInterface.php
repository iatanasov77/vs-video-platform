<?php namespace App\Component\VideoUploader\Adapter;

use App\Component\Cloud\DigitalOcean\DigitalOceanInterface;
use Aws\S3\S3ClientInterface;

interface AwsS3AdapterInterface
{
    public function getBucket(): string;
    public function getDigitalOcean(): DigitalOceanInterface;
    public function getS3Client(): S3ClientInterface;
}