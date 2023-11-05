<?php namespace App\Component\Cloud;

use Aws\S3\S3ClientInterface;

interface DigitalOceanInterface
{
    public function getS3Client(): S3ClientInterface;
    public function getBucket(): string;
}