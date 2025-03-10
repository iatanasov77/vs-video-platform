<?php namespace App\Component;

use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Factory\FactoryInterface;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use FFMpeg\Coordinate\TimeCode;
use App\Entity\Video;

final class VideoClipMaker
{
    const CLIP_MAKER_NONE       = 'none';
    const CLIP_MAKER_COCONUT    = 'coconut';
    const CLIP_MAKER_FFMPEG     = 'ffmpeg';
    
    const VIDEO_CLIP_MAKERS     = [
        self::CLIP_MAKER_NONE       => 'vs_vvp.form.video_platform_settings.video_clip_maker_none',
        self::CLIP_MAKER_COCONUT    => 'vs_vvp.form.video_platform_settings.video_clip_maker_coconut',
        self::CLIP_MAKER_FFMPEG     => 'vs_vvp.form.video_platform_settings.video_clip_maker_ffmpeg',
    ];
    
    /** @var FFMpeg */
    private $ffMpeg;
    
    /** @var ManagerRegistry */
    private $doctrine;
    
    /** @var FactoryInterface */
    private $videoClipFactory;
    
    /** @var string */
    private $videosDirectory;
    
    public function __construct(
        FFMpeg $ffMpeg,
        ManagerRegistry $doctrine,
        FactoryInterface $videoClipFactory,
        string $videosDirectory
    ) {
        $this->ffMpeg           = $ffMpeg;
        $this->doctrine         = $doctrine;
        $this->videoClipFactory = $videoClipFactory;
        $this->videosDirectory  = $videosDirectory;
    }
    
    public function createVideoClip( Video $videoEntity, string $videoUri )
    {
        $video = $this->ffMpeg->open( $videoUri );
        
        $video->filters()->clip( TimeCode::fromSeconds( 0 ), TimeCode::fromSeconds( 60 ) );
        
        $format = new X264( 'libmp3lame', 'libx264' );
        $clipLocation    = $this->videosDirectory . '/' . $videoEntity->getVideoFile()->getPath();
        $video->save( $format, $clipLocation );
        
        $videoClip  = $this->videoClipFactory->createNew();
        $videoClip->setOriginalName( $videoEntity->getVideoFile()->getOriginalName() );
        $videoClip->setVideo( $videoEntity );
        
        //$this->doctrine->getManager()->persist( $videoEntity );
        $this->doctrine->getManager()->persist( $videoClip );
        $this->doctrine->getManager()->flush();
    }
}
