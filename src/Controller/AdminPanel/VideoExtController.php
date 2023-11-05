<?php namespace App\Controller\AdminPanel;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use League\Flysystem\Filesystem;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Factory\FactoryInterface;

use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\ApplicationBundle\Repository\TaxonomyRepository;
use Vankosoft\ApplicationBundle\Repository\TaxonRepository;
use Vankosoft\ApplicationBundle\Controller\TaxonomyTreeDataTrait;
use Vankosoft\UsersBundle\Security\SecurityBridge;
use App\Component\VideoPlatform;
use App\Form\VideoForm;

class VideoExtController extends AbstractController
{
    use GlobalFormsTrait;
    use TaxonomyTreeDataTrait;
    
    /** @var ManagerRegistry */
    private ManagerRegistry $doctrine;
    
    /** @var SecurityBridge */
    private SecurityBridge $securityBridge;
    
    /** @var EntityRepository */
    private EntityRepository $videosRepository;
    
    /** @var FactoryInterface */
    private FactoryInterface $videosFactory;
    
    /** @var EntityRepository */
    private EntityRepository $videosCategoriesRepository;
    
    /** @var string */
    private string $videosDirectory;
    
    /** @var EventDispatcherInterface */
    private EventDispatcherInterface $eventDispatcher;
    
    /** @var EntityRepository */
    private EntityRepository $videoThumbnailRepository;
    
    /** @var EntityRepository */
    private EntityRepository $videoFileRepository;
    
    /** @var Filesystem */
    private $filesystem;
    
    /** @var Filesystem */
    private $filesystemCoconut;
    
    /** @var EntityRepository */
    private EntityRepository $actorsRepository;
    
    /** @var VideoPlatform */
    private VideoPlatform $videoPlatform;
    
    /** @var EntityRepository */
    private EntityRepository $videoTagsRepository;
    
    //EntityRepository $taxonRepository,
    public function __construct(
        ManagerRegistry $doctrine,
        TaxonomyRepository $taxonomyRepository,
        TaxonRepository $taxonRepository,
        SecurityBridge $securityBridge,
        EntityRepository $videosRepository,
        FactoryInterface $videosFactory,
        EntityRepository $videosCategoriesRepository,
        string $videosDirectory,
        EventDispatcherInterface $eventDispatcher,
        EntityRepository $videoThumbnailRepository,
        EntityRepository $videoFileRepository,
        Filesystem $filesystem,
        Filesystem $filesystemCoconut,
        EntityRepository $actorsRepository,
        VideoPlatform $videoPlatform,
        EntityRepository $videoTagsRepository
    ) {
        $this->doctrine                     = $doctrine;
        $this->taxonomyRepository           = $taxonomyRepository;
        $this->taxonRepository              = $taxonRepository;
        $this->securityBridge               = $securityBridge;
        $this->videosRepository             = $videosRepository;
        $this->videosFactory                = $videosFactory;
        $this->videosCategoriesRepository   = $videosCategoriesRepository;
        $this->videosDirectory              = $videosDirectory;
        $this->eventDispatcher              = $eventDispatcher;
        $this->videoThumbnailRepository     = $videoThumbnailRepository;
        $this->videoFileRepository          = $videoFileRepository;
        $this->filesystem                   = $filesystem;
        $this->filesystemCoconut            = $filesystemCoconut;
        $this->actorsRepository             = $actorsRepository;
        $this->videoPlatform                = $videoPlatform;
        $this->videoTagsRepository          = $videoTagsRepository;
    }
    
    public function getForm( $itemId, $locale, Request $request ): Response
    {
        $em     = $this->doctrine->getManager();
        $item   = $this->videosRepository->find( $itemId );
        
        if ( $locale != $request->getLocale() ) {
            $item->setTranslatableLocale( $locale );
            $em->refresh( $item );
        }
        
        $taxonomy   = $this->taxonomyRepository->findByCode(
            $this->getParameter( 'vs_vvp.video-categories.taxonomy_code' )
        );
        
        return $this->render( 'admin-panel/pages/Videos/partial/video_form.html.twig', [
            'item'                  => $item,
            'form'                  => $this->createForm( VideoForm::class, $item )->createView(),
            'taxonomyId'            => $taxonomy->getId(),
            'videoTags'             => $this->videoTagsRepository->getTags(),
            'oneupVideoUploader'    => $this->_oneupUploaderId(),
        ]);
    }
    
    public function saveVideoAction( Request $request ): Response
    {
        if ( $request->isMethod( 'POST' ) || $request->isMethod( 'PUT' ) )
        {
            $formValues = $this->getFormValues( $request );
            $em         = $this->doctrine->getManager();
            
            $entity     = $formValues['id'] ? $this->videosRepository->find( $formValues['id'] ) : $this->videosFactory->createNew();
            
            foreach ( $formValues['category_taxon'] as $cat ) {
                //$category   = $this->videosCategoriesRepository->findByTaxonId( \intval( $cat ) );
                $category   = $this->videosCategoriesRepository->find( \intval( $cat ) );
                if ( $category ) {
                    $entity->addCategory( $category );
                }
            }
            
            $this->videoTagsRepository->updateTags( explode( ',', $formValues['tags'] ) );
            
            $entity->setCurrentLocale( $formValues['currentLocale'] );
            $entity->setUser( $this->getUser() );
            $entity->setTitle( $formValues['title'] );
            $entity->setTags( $formValues['tags'] );
            $entity->setDescription( $formValues['description'] );
            
            $em->persist( $entity );
            $em->flush();
            
            if ( $this->saveFiles( $entity, $formValues ) ) {
                $em->persist( $entity );
                $em->flush();
            }
            
            if ( isset( $formValues['video_file'] ) && $formValues['video_file'] ) {
                // Dispach a Sylius Resource Post Event
                $this->eventDispatcher->dispatch( new GenericEvent( $entity ), 'vs_vvp.video.post_create' );
            }
            
            return new JsonResponse([
                'status'   => Status::STATUS_OK
            ]);
        }
        
        return new JsonResponse([
            'status'   => Status::STATUS_ERROR
        ]);
    }
    
    /**
     * Read a video file from storage dir
     *
     * examples: https://ourcodeworld.com/articles/read/329/how-to-send-a-file-as-response-from-a-controller-in-symfony-3
     */
    public function read( $id, Request $request ): Response
    {
        $oVideo = $this->videosRepository->find( $request->attributes->get( 'id' ) );
        
        if ( ! $this->checkHasAccess( $oVideo ) ) {
            return $this->redirectToRoute( 'vvp_access_denied' );
        }
        
        $oFile      = $oVideo->getVideoFile();
        $response = new StreamedResponse( function() use ( $oFile )
        {
            $outputStream   = \fopen( 'php://output', 'wb' );
            $fileStream     = $this->videoPlatform->getVideoStream( $oFile );
            
            while ( ! feof( $fileStream ) ) \fwrite( $outputStream, \fread( $fileStream, 1000000 ) );
        });
        
        $transliterator = \Transliterator::create( 'Any-Latin' );
        $transliteratorToASCII = \Transliterator::create( 'Latin-ASCII' );
        $originalName   = $transliteratorToASCII->transliterate( $transliterator->transliterate( $oFile->getOriginalName() ) );
        //var_dump( $originalName ); die;
        
        $disposition    = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $originalName
        );
        
        $response->headers->set( 'Content-Type', $oFile->getType() );
        $response->headers->set( 'Content-Disposition', $disposition );
        
        return $response;
    }
    
    public function readTranscoded( $id, $format, Request $request ): Response
    {
        $oVideo = $this->videosRepository->find( $request->attributes->get( 'id' ) );
        
        if ( ! $this->checkHasAccess( $oVideo ) ) {
            return $this->redirectToRoute( 'app_video_player_access_denied' );
        }
        
        $oFile      = $oVideo->getVideoFile();
        //$fileStream = $this->filesystemCoconut->readStream( \sprintf( 'video-%s-%s.mp4', $id, $format ) );
        $fileStream = $this->videoPlatform->getCoconutOutputStream( $id, $format );
        
        $response   = new StreamedResponse( function() use ( $fileStream )
        {
            $outputStream   = \fopen( 'php://output', 'wb' );
            
            while ( ! feof( $fileStream ) ) \fwrite( $outputStream, \fread( $fileStream, 1000000 ) );
        });
        
        $disposition    = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $oFile->getOriginalName()
        );
        
        $response->headers->set( 'Content-Type', $oFile->getType() );
        $response->headers->set( 'Content-Disposition', $disposition );
        
        return $response;
    }
    
    public function easyuiComboTreeWithSelectedSource( $taxonomyId, $videoId, Request $request ): Response
    {
        return new JsonResponse( $this->easyuiComboTreeData( $taxonomyId, $this->getSelectedCategoryTaxons( $videoId ) ) );
    }
    
    public function easyuiComboBoxActors( $videoId, Request $request ): Response
    {
        $actors = $this->actorsRepository->findAll();
        
        $data   = [];
        foreach ( $actors as $actor ) {
            $data[]   = [
                'id'        => $actor->getId(),
                'text'      => $actor->getName(),
                'checked'   => true,
            ];
        }
        
        return new JsonResponse( $data );
    }
    
    public function easyuiComboBoxVideos( $actorId, Request $request ): Response
    {
        $videos = $this->videosRepository->findAll();
        
        $data   = [];
        foreach ( $videos as $video ) {
            $data[]   = [
                'id'        => $video->getId(),
                'text'      => $video->getTitle(),
                'checked'   => true,
            ];
        }
        
        return new JsonResponse( $data );
    }
    
    protected function getSelectedCategoryTaxons( $videoId ): array
    {
        $selected   = [];
        $video       = $this->videosRepository->find( $videoId );
        if ( $video ) {
            foreach( $video->getCategories() as $cat ) {
                $selected[] = $cat->getTaxon()->getId();
            }
        }
        
        return $selected;
    }
    
    private function getFormValues( Request $request ): array
    {
        $formValues = [];
        
        switch ( true ) {
            case $request->isMethod( 'POST' ):
                $formValues = [
                    'id'                => \intval( $request->request->get( 'id' ) ),
                    'category_taxon'    => \explode( ',', $request->request->get( 'category_taxon' ) ),
                    'tags'              => $request->request->get( 'tags' ),
                    'currentLocale'     => $request->request->get( 'currentLocale' ),
                    'title'             => $request->request->get( 'title' ),
                    'description'       => $request->request->get( 'description' ),
                    'video_thumbnail'   => \intval( $request->request->get( 'video_thumbnail' ) ),
                    'video_file'        => \intval( $request->request->get( 'video_file' ) ),
                ];
                
                break;
            case $request->isMethod( 'PUT' ):
                $formValues = $request->request->all( 'video_form' );
                
                break;
            default:
                throw new \Exception( 'Runtime Error !!!' );
        }
        
        return $formValues;
    }
    
    private function saveFiles( &$entity, $formValues ): bool
    {
        $saveProcessed  = false;
        $em             = $this->doctrine->getManager();
        
        if ( isset( $formValues['video_thumbnail'] ) && $formValues['video_thumbnail'] ) {
            $thumbnail  = $this->videoThumbnailRepository->find( $formValues['video_thumbnail'] );
            $thumbnail->setOwner( $entity );
            
            $em->persist( $thumbnail );
            $em->flush();
            $entity->setVideoThumbnail( $thumbnail );
            
            $saveProcessed  = true;
        }
        
        if ( isset( $formValues['video_file'] ) && $formValues['video_file'] ) {
            $video  = $this->videoFileRepository->find( $formValues['video_file'] );
            $video->setOwner( $entity );
            
            $storageType    = $this->videoPlatform->getOriginalVideosStorage()->getStorageType();
            $video->setStorageType( $storageType );
            
            $em->persist( $video );
            $em->flush();
            $entity->setVideoFile( $video );
            
            $saveProcessed  = true;
        }
        
        return $saveProcessed;
    }
    
    private function _oneupUploaderId(): string
    {
        $storageType    = $this->videoPlatform->getOriginalVideosStorage()->getStorageType();
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
