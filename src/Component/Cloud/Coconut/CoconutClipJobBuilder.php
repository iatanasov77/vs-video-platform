<?php namespace App\Component\Cloud\Coconut;

use App\Entity\Video;
use App\Component\Cloud\Exception\VideoPlatformStorageException;

final class CoconutClipJobBuilder extends AbstractCoconutJobBuilder
{
    public function createJob( Video $video, string $apiToken, ?string $watermark = null ): void
    {
        $videoClipSettings  = $this->getClipSettings();
        
        $this->client->storage = [
            'url'   => $videoClipSettings['outputUrl'],
        ];
        
        $this->_setupNotificationWebhook( $apiToken );
        
        $this->jobOutputs[$videoClipSettings['format']]  = [
            'path'      => "/{$video->getVideoFile()->getPath()}",
            'offset'    => $videoClipSettings['offset'],
            'duration'  => $videoClipSettings['duration'],
        ];
        
        $job    = $this->_createCoconutJob( $video );
        if ( ! $job ) {
            return;
        }
        
        $this->_createCoconutJobEntity( $video, $job );
    }
    
    private function getClipSettings(): array
    {
        $coconutSettings    = $this->videoPlatformSettings->getCoconutSettings();
        $videoClipSettings  = [
            'format'    => $coconutSettings->getCoconutClipFormat(),
            'offset'    => $coconutSettings->getCoconutClipOffset(),
            'duration'  => $coconutSettings->getCoconutClipDuration(),
            'outputUrl' => $coconutSettings->getCoconutClipOutputUrl(),
        ];
        
        if (
            ! isset( $videoClipSettings['format'] ) ||
            ! isset( $videoClipSettings['offset'] ) ||
            ! isset( $videoClipSettings['duration'] ) ||
            ! isset( $videoClipSettings['outputUrl'] )
        ) {
            throw new VideoPlatformStorageException( 'Video Clip Settings are Not Configured !!!' );
        }
        
        return $videoClipSettings;
    }
    
    protected function _setupNotificationWebhook( string $apiToken ): void
    {
        $webhookUrl = \sprintf( '%s/api/coconut/clip-job-webhook/%s', $this->apiHost, $apiToken );
        
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
        
        if ( $video->getCoconutClipJob() ) {
            $em->remove( $video->getCoconutClipJob() );
            $em->flush();
            
            $video->setCoconutClipJob( null );
        }
        
        $jobEntity->setVideo( $video );
        $jobEntity->setJobId( $job->id );
        $jobEntity->setJobData( \json_encode( (array) $job ) );
        $jobEntity->setStatus( Coconut::STATUS_JOB_STARTING );
        
        $em->persist( $jobEntity );
        $em->flush();
    }
}
