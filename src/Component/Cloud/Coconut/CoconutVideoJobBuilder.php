<?php namespace App\Component\Cloud\Coconut;

use Symfony\Component\Routing\RouterInterface;
use App\Entity\Video;
use App\Component\Cloud\Exception\VideoPlatformStorageException;
use App\Component\VideoPlatform;

/**
 * Using KNP Gaufrette to Store Files in Cloud Storage
 * ====================================================
 * https://florian.ec/blog/symfony2-gaufrette-s3/
 */
final class CoconutVideoJobBuilder extends AbstractCoconutJobBuilder
{
    public function storage(): string
    {
        return $this->videoPlatform->getCoconutStorage()->getStorageType();
    }
    
    public function createJob( Video $video, string $apiToken, ?string $watermark = null ): void
    {
        $this->_setupStorage();
        $this->_setupNotificationWebhook( $apiToken );
        
        if ( $this->videoPlatformSettings->getCoconutSettings()->getCoconutWatermark() ) {
            /*
            $watermark  = $this->router->generate( 'vs_cms_images_get_file',
                ['file' => 'public/shared_media/gaufrette/videos_user_signatured/anonymous/watermark.png' ],
                RouterInterface::ABSOLUTE_URL
            );
            */
            
            $watermark  = $this->router->generate( 'vs_cms_images_get_file',
                ['file' => 'anonymous/watermark.png' ],
                RouterInterface::ABSOLUTE_URL
            ) . '?filter=video_watermark';
                
            //$watermark  = 'http://admin.video-platform.vankosoft.org/images/public/shared_media/gaufrette/videos_user_signatured/anonymous/watermark.png';
            
            $this->_setupCoconutJobOutputs( $video, $watermark );
        } else {
            $this->_setupCoconutJobOutputs( $video );
        }
        
        $job    = $this->_createCoconutJob( $video );
        if ( ! $job ) {
            return;
        }
        
        $this->_createCoconutJobEntity( $video, $job );
    }
    
    protected function _setupNotificationWebhook( string $apiToken ): void
    {
        $webhookUrl = \sprintf( '%s/api/coconut/video-job-webhook/%s', $this->apiHost, $apiToken );
        
        $this->client->notification = [
            'type'      => 'http',
            'url'       => $webhookUrl,
            "events"    => true,
            "metadata"  => true
        ];
    }
    
    protected function _createCoconutJobEntity( Video $video, \stdClass $job ): void
    {
        $em         = $this->doctrine->getManager();
        $jobEntity  = $this->jobsFactory->createNew();
        
        if ( $video->getCoconutVideoJob() ) {
            $em->remove( $video->getCoconutVideoJob() );
            $em->flush();
            
            $video->setCoconutVideoJob( null );
        }
        
        $jobEntity->setVideo( $video );
        $jobEntity->setJobId( $job->id );
        $jobEntity->setJobData( \json_encode( (array) $job ) );
        $jobEntity->setStatus( Coconut::STATUS_JOB_STARTING );
        
        $em->persist( $jobEntity );
        $em->flush();
    }
    
    private function _setupStorage()
    {
        switch ( $this->videoPlatform->getCoconutStorage()->getStorageType() ) {
            case VideoPlatform::STORAGE_TYPE_LOCAL:
                $this->_setupLocalStorage();
                break;
            case VideoPlatform::STORAGE_TYPE_S3:
                $this->_setupS3Storage();
                break;
                
            case VideoPlatform::STORAGE_TYPE_DO:
                $this->_setupDigitalOceanStorage();
                break;
                
            case VideoPlatform::STORAGE_TYPE_COCONUT:
            default:
                $this->_setupCoconutStorage();
        }
    }
    
    private function _setupCoconutStorage()
    {
        $this->client->storage = [
            'service' => 'coconut',
        ];
    }
    
    private function _setupLocalStorage()
    {
        //$storageUrl = 'http://admin.video-platform.vankosoft.org/api/coconut/storage-oneup';
        $settings   = $this->videoPlatform->getCoconutStorage()->getSettings();
        if ( ! isset( $settings['local_url'] ) ) {
            throw new VideoPlatformStorageException( 'Local Storage is Not Configured Properly !!!' );
        }
        
        $this->client->storage = [
            'url'   => $settings['local_url'],
        ];
    }
    
    private function _setupS3Storage()
    {
        $credentials    = $this->videoPlatform->getCoconutStorage()->getSettings();
        if ( ! isset( $credentials['access_key'] ) || ! isset( $credentials['secret_key'] ) ) {
            throw new VideoPlatformStorageException( 'AWS Storage is Not Configured Properly !!!' );
        }
        
        /** Created on johnny2000@abv.bg Account */
        $this->client->storage = [
            'service'       => 's3',
            'bucket'        => 'my-video-platform',
            'region'        => 'eu-central-1',
            'credentials'   => [
                'access_key_id'     => $credentials['access_key'],
                'secret_access_key' => $credentials['secret_key']
            ]
        ];
    }
    
    private function _setupDigitalOceanStorage()
    {
        $storageSettings    = $this->videoPlatform->getCoconutStorage()->getSettings();
        //echo '<pre>'; var_dump( $credentials ); die;
        
        if (
            ! isset( $storageSettings['spaces_access_id'] ) ||
            ! isset( $storageSettings['spaces_secret_key'] ) ||
            ! isset( $storageSettings['endpoint'] ) ||
            ! isset( $storageSettings['region'] ) ||
            ! isset( $storageSettings['bucket'] )
        ) {
            throw new VideoPlatformStorageException( 'DigitalOcean Storage is Not Configured Properly !!!' );
        }
            
        /**
         * Any S3 compatible services: https://docs.coconut.co/jobs/storage#any-s3-compatible-services
         * ===============================================================================================
         * https://docs.digitalocean.com/products/spaces/reference/s3-sdk-examples/
         */
        $this->client->storage = [
            //'service'           => 's3other',
            //'force_path_style'  => false, // Configures to use subdomain/virtual calling format.
            //'endpoint'          => $credentials['endpoint'],
            
            'service'           => 'dospaces',
            //'bucket'            => $credentials['coconut_output_bucket'],
            'bucket'            => $storageSettings['bucket'],
            'region'            => $storageSettings['region'],
            'acl'               => isset( $storageSettings['acl'] ) ? $storageSettings['acl'] : 'private',
            
            'credentials'       => [
                'access_key_id'     => $storageSettings['spaces_access_id'],
                'secret_access_key' => $storageSettings['spaces_secret_key']
            ],
        ];
    }
    
    private function _setupCoconutJobOutputs( Video $video, ?string $watermark = null )
    {
        $outputFormats  = $this->videoPlatformSettings->getCoconutSettings()->getCoconutOutputFormats();
        foreach( $outputFormats as $format ) {
            switch ( $format ) {
                case 'httpstrem':
                    // https://developer.apple.com/streaming/
                    $this->jobOutputs['httpstrem']  = [
                        'hls'   => [
                            'path' => "/video-{$video->getId()}-hls"
                        ],
                        'dash' => [
                            'path'     => "/video-{$video->getId()}-dash",
                            'hlsfmp4'  => true
                        ]
                    ];
                    break;
                default:
                    $formatParts    = \explode( ':', $format );
                    
                    $this->jobOutputs[$format]  = [
                        'path' => "/video-{$video->getId()}-{$formatParts[1]}.{$formatParts[0]}"
                    ];
                    
                    if ( $watermark ) {
                        $this->jobOutputs[$format]['watermark'] = [
                            'url'       => $watermark,
                            'position'  => "bottomright"
                        ];
                    }
            }
        }
    }
}
