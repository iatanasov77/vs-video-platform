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

use App\Component\VideoPlatform;
use App\Entity\Video;
use App\Entity\VideoThumbnail;
use App\Entity\VideoFile;
use App\Entity\VideoCategory;

class VideoController extends AbstractCrudController
{
    use GlobalFormsTrait;
    use FilterFormTrait;
    
    public function showAction( Request $request ): Response
    {
        $er = $this->getDoctrine()->getRepository( 'App\Entity\Video' );
        $id = $request->attributes->get( 'id' );
        if ( is_numeric( $id ) ) {
            $oVideo     = $er->find( $id );
        } else {
            $oVideo     = $er->findOneBy( ['slug' => $id] );
        }
        
        if ( ! $this->checkHasAccess( $oVideo ) ) {
            return $this->redirectToRoute( 'vvp_access_denied' );
        }

        return $this->render( 'Pages/Videos/show.html.twig', [
            'tabForm'                       => $this->getTabForm()->createView(),
            'tabCategoryForm'               => $this->getTabCategoryForm()->createView(),
            'item'                          => $oVideo,
            'error'                         => false,
            'tabCategoriesTaxonomyId'       => $this->getTabCategoriesTaxonomy()->getId(),
            'locales'                       => $this->getDoctrine()->getRepository( 'App\Entity\Application\Locale' )->findAll(),
            'paidVideoStoreServices'        => $this->get( 'vs_users_subscriptions.repository.payed_service_subscription_period' )->findAll(),
            
            'baseUrl'                       => $this->getParameter( 'vankosoft_host' ),
        ]);
    }
    
    public function deleteAction( Request $request ): Response
    {
        $configuration = $this->requestConfigurationFactory->create( $this->metadata, $request );
        $this->isGrantedOr403( $configuration, ResourceActions::DELETE );
        
        $resource       = $this->findOr404( $configuration );
        $em             = $this->get( 'doctrine' )->getManager();
        
        $videoId        = $resource->getId();
        $videoThumbnail = $resource->getVideoThumbnail();
        $videoFile      = $resource->getVideoFile();
        
        $this->removeThumbnailFile( $videoThumbnail, $videoId );
        $this->removeVideoFile( $videoFile, $videoId );
        
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
        $taxonomy   = $this->get( 'vs_application.repository.taxonomy' )->findByCode(
            $this->getParameter( 'vs_vvp.video-categories.taxonomy_code' )
        );
        
        $filterCategory = $request->attributes->get( 'filterCategory' );
        $filterForm     = $this->getFilterForm( VideoCategory::class, $filterCategory, $request );
        
        //echo "<pre>"; var_dump($this->classInfo['action']); die;
        $translations   = $this->classInfo['action'] == 'indexAction' ? $this->getTranslations() : [];
        $allVideos      = $this->classInfo['action'] == 'indexAction' ? $this->getRepository()->findAll() : [];
        
        $videoTags      = $this->get( 'vs_vvp.repository.video_tag' )->getTags();
        
        return [
            'categories'            => $this->get( 'vs_vvp.repository.video_category' )->findAll(),
            'taxonomyId'            => $taxonomy->getId(),
            'filterForm'            => $filterForm->createView(),
            'filterCategory'        => $filterCategory,
            'translations'          => $translations,
            'availableFormats'      => $this->_getVideoFormats( $allVideos ),
            'oneupVideoUploader'    => $this->_oneupUploaderId(),
            'videoTags'             => $videoTags,
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $formPost   = $request->request->all( 'video_form' );
        $formLocale = $formPost['locale'];
        
        if ( $formLocale ) {
            $entity->setTranslatableLocale( $formLocale );
        }
        
        $this->easyuiPost( $entity, $formPost );
        
        $entity->setUser( $this->getAppUser() );
        
        $thumbnailFile    = $form['thumbnail']->getData();
        if ( $thumbnailFile ) {
            $this->createThumbnail( $entity, $thumbnailFile );
        }
        
        $videoFile    = $form['video']->getData();
        if ( $videoFile ) {
            $this->createVideo( $entity, $videoFile );
        }
    }
    
    protected function getFilterRepository()
    {
        return $this->get( 'vs_vvp.repository.video_category' );
    }
    
    private function easyuiPost( Video &$entity, $formPost )
    {
        $categories = new ArrayCollection();
        $repo       = $this->get( 'vs_vvp.repository.video_category' );
        
        if ( isset( $formPost['category_taxon'] ) ) {
            if ( is_array( $formPost['category_taxon'] ) ) {
                foreach ( $formPost['category_taxon'] as $taxonId ) {
                    //$category   = $repo->findOneBy( ['taxon' => $taxonId] );
                    $category   = $repo->find( $taxonId );
                    
                    if ( $category ) {
                        $categories[]   = $category;
                        $entity->addCategory( $category );
                    }
                }
                
                foreach ( $entity->getCategories() as $cat ) {
                    if ( ! $categories->contains( $cat ) ) {
                        $entity->removeCategory( $cat );
                    }
                }
            } else {
               // For Now Not Multiple Categories
                $category   = $repo->find( $formPost['category_taxon'] );
                if ( $category ) {
                    $entity->addCategory( $category );
                }
            }
        }
    }
    
    private function createThumbnail( Video &$video, File $file ): void
    {
        $videoThumbnail = $video->getVideoThumbnail() ?: $this->get( 'vs_vvp.factory.video_thumbnail' )->createNew();
        $videoThumbnail->setOriginalName( $file->getClientOriginalName() );
        $videoThumbnail->setVideo( $video );
        
        $uploadedFile   = new UploadedFile( $file->getRealPath(), $file->getBasename() );
        $videoThumbnail->setFile( $uploadedFile );
        $this->get( 'vs_vvp.video_uploader' )->upload( $videoThumbnail );
        $videoThumbnail->setFile( null ); // reset File Because: Serialization of 'Symfony\Component\HttpFoundation\File\UploadedFile' is not allowed
        
        if ( ! $video->getVideoFile() ) {
            $video->setVideoThumbnail( $videoThumbnail );
        }
    }
    
    private function createVideo( Video &$video, File $file ): void
    {
        $videoFile  = $video->getVideoFile() ?: $this->get( 'vs_vvp.factory.video_file' )->createNew();
        $videoFile->setOriginalName( $file->getClientOriginalName() );
        $videoFile->setVideo( $video );
        
        $uploadedFile   = new UploadedFile( $file->getRealPath(), $file->getBasename() );
        $videoFile->setFile( $uploadedFile );
        $this->get( 'vs_vvp.video_uploader.local_uploader' )->upload( $videoFile );
        $videoFile->setFile( null ); // reset File Because: Serialization of 'Symfony\Component\HttpFoundation\File\UploadedFile' is not allowed
        
        $storageType    = $this->get( 'app_video_platform' )->getOriginalVideosStorage()->getStorageType();
        $videoFile->setStorageType( $storageType );
        
        if ( ! $video->getVideoFile() ) {
            $video->setVideoFile( $videoFile );
        }
    }
    
    private function getTabCategoriesTaxonomy()
    {
        $taxonomy   = $this->get( 'vs_application.repository.taxonomy' )->findByCode(
            $this->getParameter( 'vs_vvp.video-categories.taxonomy_code' )
        );
        
        return $taxonomy;
    }
    
    private function removeThumbnailFile( VideoThumbnail $videoThumbnail, int $videoId )
    {
        $thumbnailPath  = $this->getParameter( 'vs_vvp.videos_directory' ) . '/' . $videoThumbnail->getPath();
        
        $filesystem = new Filesystem();
        $filesystem->remove( $thumbnailPath );
        
        $em = $this->get( 'doctrine' )->getManager();
        $em->remove( $videoThumbnail );
        $em->flush();
        
    }
    
    private function removeVideoFile( VideoFile $videoFile, int $videoId )
    {
        $this->get( 'app_video_platform' )->removeVideoFiles( $videoFile, $videoId );
        
        $em = $this->get( 'doctrine' )->getManager();
        $em->remove( $videoFile );
        $em->flush();
    }
    
    private function getTranslations()
    {
        $translations   = [];
        $transRepo      = $this->get( 'vs_application.repository.translation' );
        
        foreach ( $this->getRepository()->findAll() as $video ) {
            $translations[$video->getId()] = array_keys( $transRepo->findTranslations( $video ) );
        }
        //echo "<pre>"; var_dump($translations); die;
        return $translations;
    }
    
    private function _getVideoFormats( array $videos ): array
    {
        $formats        = [];
        $videoPlatform  = $this->get( 'app_video_platform' );
        
        foreach ( $videos as $videoEntity ) {
            $formats[$videoEntity->getSlug()]   = $videoPlatform->getVideoFormats( $videoEntity );
        }
        
        return $formats;
    }
    
    private function _oneupUploaderId(): string
    {
        $storageType    = $this->get( 'app_video_platform' )->getOriginalVideosStorage()->getStorageType();
        switch ( $storageType ) {
            case VideoPlatform::STORAGE_TYPE_S3:
            case VideoPlatform::STORAGE_TYPE_DO:
                return 'videos_digitalocean';
                break;
            case VideoPlatform::STORAGE_TYPE_COCONUT:
            case VideoPlatform::STORAGE_TYPE_LOCAL:
            default:
                return 'videos_local';
                break;
        }
    }
}
