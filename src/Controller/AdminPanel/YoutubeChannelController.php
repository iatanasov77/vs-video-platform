<?php namespace App\Controller\AdminPanel;

use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\YoutubeChannel;

class YoutubeChannelController extends AbstractCrudController
{
    protected function customData( Request $request, $entity = NULL ): array
    {
        return [
            'dirPhotos' => $this->getParameter( 'vs_vvp.youtube_photos_directory' ),
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $photo      = $form['photo']->getData();
        if ( $photo ) {
            $this->createPhoto( $entity, $photo );
        }
    }
    
    private function createPhoto( YoutubeChannel &$channel, File $file ): void
    {
        $channelPhoto  = $channel->getPhoto() ?: $this->get( 'vs_vvp.factory.youtube_channel_photo' )->createNew();
        $channelPhoto->setOriginalName( $file->getClientOriginalName() );
        $channelPhoto->setYoutubeChannel( $channel );
        
        $uploadedFile   = new UploadedFile( $file->getRealPath(), $file->getBasename() );
        $channelPhoto->setFile( $uploadedFile );
        $this->get( 'vs_vvp.youtube_photo_uploader' )->upload( $channelPhoto );
        $channelPhoto->setFile( null ); // reset File Because: Serialization of 'Symfony\Component\HttpFoundation\File\UploadedFile' is not allowed
        
        if ( ! $channel->getPhoto() ) {
            $channel->setPhoto( $channelPhoto );
        }
    }
}