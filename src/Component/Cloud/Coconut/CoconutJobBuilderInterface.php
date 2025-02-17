<?php namespace App\Component\Cloud\Coconut;

use App\Entity\Video;

interface CoconutJobBuilderInterface
{
    public function createJob( Video $video, string $apiToken, ?string $watermark = null ): void;
}
