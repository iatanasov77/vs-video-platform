<?php namespace App\Component\Cloud\Coconut;

/**
 * Using KNP Gaufrette to Store Files in Cloud Storage
 * ====================================================
 * https://florian.ec/blog/symfony2-gaufrette-s3/
 */
final class Coconut
{
    const STATUS_JOB_STARTING       = 'job.starting';
    const STATUS_JOB_NOT_FOUND      = 'job.not_found';
    
    const EVENT_JOB_COMPLETED       = 'job.completed';
    const EVENT_JOB_FAILED          = 'job.failed';
    const EVENT_INPUT_TRANSFERRED   = 'input.transferred';
    const EVENT_OUTPUT_COMPLETED    = 'output.completed';
    const EVENT_OUTPUT_FAILED       = 'output.failed';
    
    const JOB_TYPE_VIDEO            = 'video';
    const JOB_TYPE_CLIP             = 'clip';
    const JOB_TYPE_CLIP_STORE       = 'clip-store';
}