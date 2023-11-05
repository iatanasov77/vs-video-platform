<?php namespace App\Component\VideoProviders;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

use App\Component\VideoPlayer\Domain\Video;
use App\Component\VideoPlayer\Domain\VideoPlayer;
use App\Component\VideoPlayer\Domain\VideoProvider;
use App\Component\VideoPlayer\Domain\VideoProviderRequest;
use App\Component\VideoPlayer\Exception\VideoProviderRequestException;
use App\Component\VideoPlayer\VideoService;
use App\Component\VideoPlayer\Html5VideoPlayer;
use App\Component\Cloud\Coconut;
use App\Entity\Video as VideoEntity;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class LocalStorageVideoProvider implements VideoProvider
{
    /** @var EntityRepository */
    private $videosRepository;
    
    /** @var array */
    private $videos;
    
    /** @var string */
    private $host;
    
    /** @var CacheManager */
    private $imagineCacheManager;
    
    /** @var Coconut */
    private $coconut;
    
    public function __construct(
        EntityRepository $videosRepository,
        string $host,
        CacheManager $imagineCacheManager,
        Coconut $coconut
    ) {
        $this->videosRepository     = $videosRepository;
        $this->host                 = $host;
        $this->imagineCacheManager  = $imagineCacheManager;
        $this->coconut              = $coconut;
    }
    
    /**
     * {@inheritdoc}
     */
    public function videoList( VideoProviderRequest $request ): array
    {
        switch ( $request->command ) {
            case VideoService::REQUEST_COMMAND_LATEST:
            case VideoService::REQUEST_COMMAND_CATEGORY:
                $searchResult   = $this->search( $request->params['category'] ?: VideoService::REQUEST_COMMAND_LATEST );
                
                break;
            case VideoService::REQUEST_COMMAND_GET_A_VIDEO:
                $searchResult   = $this->get( $request->params['video_id'] );
                
                break;
            default:
                throw new VideoProviderRequestException( 'Invalid VideoProviderRequest !!!' );
        }
        
        $videos         = $this->loadSearchVideos( $searchResult );
        $this->videos   = $videos;
        
        return $videos;
    }
    
    /**
     * {@inheritdoc}
     */
    public function first():? Video
    {
        if ( ! empty( $this->videos ) ) {
            return reset( $this->videos );
        }
        
        return null;
    }
    
    /**
     * {@inheritdoc}
     */
    public function player(): VideoPlayer
    {
        return new Html5VideoPlayer();
    }
    
    /**
     * @param $item
     * @return Video
     */
    private function createVideoFrom( VideoEntity $item ): Video
    {
        //var_dump( $item->getVideoThumbnail()->getPath() ); die;
        $videoUrl       = \sprintf( '//%s/videos/%s/read', $this->host, $item->getId() );
        
        $thumbnailPath  = $item->getVideoThumbnail()->getPath();
        $thumbnailUrl   = $this->imagineCacheManager->getBrowserPath( $thumbnailPath, 'video_thumbnail' );
        
        return ( new Video(
                $item->getId(),
                $item->getTitle(),
                $item->getDescription(),
                $videoUrl,
                $item->getSlug()
            ))
            ->createdBy( $item->getUser()->getUsername() )
            ->withThumbnail( $thumbnailUrl )
        ;
    }
    
    /**
     * @param $searchResult
     * @return array
     */
    private function loadSearchVideos( array $searchResult ): array
    {
        $videos = [];
        
        /** @var VideoEntity $item */
        foreach ( $searchResult as $item ) {
            $video      = $this->createVideoFrom( $item );
            $videos[]   = $video;
        }
        
        return $videos;
    }
    
    /**
     * @param string $searchTerm
     * @return array
     */
    private function search( $category ): array
    {
        $searchResult = $this->videosRepository->findBy( [], ['id' => 'DESC'] );
        
        return $searchResult;
    }
    
    /**
     * @param int $videoId
     * @return array
     */
    private function get( $videoId ): array
    {
        $searchResult = $this->videosRepository->findBy( ['id' => $videoId] );
        
        return $searchResult;
    }
}