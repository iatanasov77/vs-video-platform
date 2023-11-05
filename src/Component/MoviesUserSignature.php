<?php namespace App\Component;

use League\Flysystem\Filesystem as LeagueFilesystem;
use FFMpeg\FFMpeg;
use Vankosoft\UsersBundle\Model\UserInterface;
use App\Entity\VideoFile;

final class MoviesUserSignature
{
    /** @var FFMpeg */
    private $ffMpeg;
    
    /** @var VideoPlatform */
    private $videoPlatform;
    
    /** @var UserInterface|null */
    private $user;
    
    /** @var LeagueFilesystem */
    private $filesystem;
    
    /** @var string */
    private $userSignaturesDirectory;
    
    public function __construct(
        FFMpeg $ffMpeg,
        VideoPlatform $videoPlatform,
        ?UserInterface $user,
        LeagueFilesystem $filesystem,
        string $userSignaturesDirectory
    ) {
        $this->ffMpeg                   = $ffMpeg;
        $this->videoPlatform            = $videoPlatform;
        $this->user                     = $user;
        
        $this->filesystem               = $filesystem;
        $this->userSignaturesDirectory  = $userSignaturesDirectory;
    }
    
    public function getUserSignedVideo( VideoFile $videoFile, string $bucket ): string
    {
        if ( $this->user ) {
            $watermarkText      = $this->user->getInfo()->getFullName();
            //$watermarkImageFile = $this->userSignaturesDirectory . '/' . $this->user->getUsername(). '/watermark.png';
            $watermarkDirectory = $this->user->getUsername();
            $watermarkImageFile = $this->user->getUsername() . '/watermark.png';
        } else {
            $watermarkText      = 'Video Platform';
            //$watermarkImageFile = $this->userSignaturesDirectory . '/anonymous/watermark.png';
            $watermarkDirectory = 'anonymous';
            $watermarkImageFile = 'anonymous/watermark.png';
        }
        
        if ( ! $this->filesystem->fileExists( $watermarkDirectory ) ) {
            $this->filesystem->createDirectory( $watermarkDirectory );
        }
        
        if ( ! $this->filesystem->fileExists( $watermarkImageFile ) ) {
            $this->_createWatermarkImage( $watermarkImageFile, $watermarkText );
        }
        
        //$signedVideoLocation    = $this->userSignaturesDirectory . '/' . $watermarkDirectory . '/' . $videoFile->getPath();
        $signedVideoLocation    = $this->userSignaturesDirectory . '/' . $watermarkDirectory . '/' . $videoFile->getPath();
        $this->_addMovieWatermark(
            $this->videoPlatform->getVideoUri( $videoFile, $bucket ),
            //$this->filesystem->read( $watermarkImageFile ),
            $this->userSignaturesDirectory . '/' . $watermarkImageFile,
            $signedVideoLocation
        );
        
        return $signedVideoLocation;
    }
    
    private function _createWatermarkImage( string $watermarkImageFile, string $watermarkText ): void
    {
        $im = @\imagecreatetruecolor( 200, 20 );
        if ( $im ) {
            $text_color = \imagecolorallocate( $im, 233, 14, 91 );
            \imagestring( $im, 6, 5, 5, $watermarkText, $text_color );
            
            \imagepng( $im );
            $data = \ob_get_clean();
            
            //\file_put_contents( $watermarkImageFile, $data );
            $this->filesystem->write( $watermarkImageFile, $data );
            
            \imagedestroy( $im );
        }
    }
    
    private function _addMovieWatermark( string $movieUri, string $watermarkPath, string $videoSaveLocation )
    {
        $video  = $this->ffMpeg->open( $movieUri );
        $video
            ->filters()
            ->watermark( $watermarkPath, [
                'position'  => 'relative',
                'bottom'    => 50,
                'right'     => 50,
            ]);
            
        //var_dump( $videoSaveLocation ); die;
        $format = new \FFMpeg\Format\Video\X264( 'libmp3lame', 'libx264' );
        $video->save( $format, $videoSaveLocation );
    }
}