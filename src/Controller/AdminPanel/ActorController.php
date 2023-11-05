<?php namespace App\Controller\AdminPanel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Doctrine\Common\Collections\ArrayCollection;

use Sylius\Component\Resource\ResourceActions;
use Vankosoft\ApplicationBundle\Component\Status;

use App\Entity\Actor;
use App\Entity\ActorPhoto;

class ActorController extends AbstractCrudController
{
    public function deleteAction( Request $request ): Response
    {
        $configuration = $this->requestConfigurationFactory->create( $this->metadata, $request );
        $this->isGrantedOr403( $configuration, ResourceActions::DELETE );
        
        $resource   = $this->findOr404( $configuration );
        $em         = $this->get( 'doctrine' )->getManager();
        
        $this->removeActorPhotos( $resource );
        $em->remove( $resource );
        $em->flush();
        
        $redirectUrl    = $request->request->get( 'redirectUrl' );
        if ( $redirectUrl ) {
            return $this->redirect( $redirectUrl );
        }
        
        return new JsonResponse([
            'status'   => Status::STATUS_OK
        ]);
    }
    
    protected function customData( Request $request, $entity = NULL ): array
    {
        $translations   = $this->classInfo['action'] == 'indexAction' ? $this->getTranslations() : [];
        
        return [
            'translations'  => $translations,
            'dirPhotos'     => $this->getParameter( 'vs_vvp.actor_photos_directory' ),
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $formPost   = $request->request->all( 'actor_form' );
        $formLocale = $formPost['locale'];
        
        if ( $formLocale ) {
            $entity->setTranslatableLocale( $formLocale );
        }
        
        $this->videosPost( $entity, $formPost );
        
        $formFiles  = $request->files->get( 'actor_form' );
        $photos = $form['photos']->getData();
        if ( ! empty( $formFiles['photos'] ) ) {
            foreach ( $formFiles['photos'] as $photoId => $photo ) {
                if ( ! $photo['photo'] ) {
                    continue;
                }
                $this->createPhoto( $entity, $photos[$photoId], $photo['photo'] );
            }
        }
    }
    
    private function getTranslations()
    {
        $translations   = [];
        $transRepo      = $this->get( 'vs_application.repository.translation' );
        
        foreach ( $this->getRepository()->findAll() as $actor ) {
            $translations[$actor->getId()] = \array_reverse( \array_keys( $transRepo->findTranslations( $actor ) ) );
        }
        
        return $translations;
    }
    
    private function createPhoto( Actor &$actor, ActorPhoto &$actorPhoto, File $file ): void
    {
        $actorPhoto->setOriginalName( $file->getClientOriginalName() );
        $actorPhoto->setActor( $actor );
        
        $uploadedFile   = new UploadedFile( $file->getRealPath(), $file->getBasename() );
        $actorPhoto->setFile( $uploadedFile );
        $this->get( 'vs_vvp.actor_photo_uploader' )->upload( $actorPhoto );
        $actorPhoto->setFile( null ); // reset File Because: Serialization of 'Symfony\Component\HttpFoundation\File\UploadedFile' is not allowed
    }
    
    private function removeActorPhotos( Actor $actor )
    {
        $em         = $this->get( 'doctrine' )->getManager();
        $filesystem = new Filesystem();
        $photosDir  = $this->getParameter( 'vs_vvp.actor_photos_directory' );
        
        foreach ( $actor->getPhotos() as $photo ) {
            $photoFile  = $photosDir . '/' . $photo->getPath();
            
            $em->remove( $photo );
            $em->flush();
            
            $filesystem->remove( $photoFile );
        }
    }
    
    private function videosPost( Actor &$entity, $formPost )
    {
        $videos = new ArrayCollection();
        $repo   = $this->get( 'vs_vvp.repository.video' );
        
        if ( isset( $formPost['videos'] ) ) {
            if ( is_array( $formPost['videos'] ) ) {
                foreach ( $formPost['videos'] as $videoId ) {
                    $video  = $repo->find( $videoId );
                    if ( $video ) {
                        $videos[]   = $video;
                        $entity->addVideo( $video );
                        $video->addActor( $entity );
                    }
                }
                
                foreach ( $entity->getVideos() as $video ) {
                    if ( ! $videos->contains( $video ) ) {
                        $entity->removeVideo( $video );
                        $video->removeActor( $entity );
                    }
                }
            } else {
                // For Now Not Multiple Categories
                $video   = $repo->find( $formPost['videos'] );
                if ( $video ) {
                    $entity->addVideo( $video );
                    $video->addActor( $entity );
                }
            }
        }
    }
}