<?php namespace App\Component\VideoUploader;

use League\Flysystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Webmozart\Assert\Assert;

use Vankosoft\CmsBundle\Component\Uploader\FileUploaderInterface;
use Vankosoft\CmsBundle\Component\Generator\FilePathGeneratorInterface;
use Vankosoft\CmsBundle\Model\Interfaces\FileInterface;
use App\Component\VideoPlatform;

class LocalUploader implements FileUploaderInterface
{
    /** @var Filesystem */
    protected $filesystem;
    
    /** @var FilePathGeneratorInterface */
    protected $filePathGenerator;
    
    /** @var VideoPlatform */
    protected $videoPlatform;
    
    public function __construct(
        Filesystem $filesystem,
        FilePathGeneratorInterface $filePathGenerator,
        VideoPlatform $videoPlatform
    ) {
            $this->filesystem           = $filesystem;
            
            if ( $filePathGenerator === null ) {
                @trigger_error( sprintf(
                    'Not passing an $filePathGenerator to %s constructor is deprecated since Sylius 1.6 and will be not possible in Sylius 2.0.',
                    self::class
                ), \E_USER_DEPRECATED );
            }
            
            $this->filePathGenerator    = $filePathGenerator ?? new FilePathGenerator();
            $this->videoPlatform        = $videoPlatform;
    }
    
    public function upload( FileInterface $videoFile ): void
    {
        if ( ! $videoFile->hasFile() ) {
            return;
        }
        
        $file = $videoFile->getFile();
        
        /** @var File $file */
        Assert::isInstanceOf( $file, File::class );
        
        if ( null !== $videoFile->getPath() && $this->has( $videoFile->getPath() ) ) {
            $this->remove( $videoFile->getPath() );
        }
        
        do {
            $path = $this->filePathGenerator->generate( $videoFile );
        } while ( $this->isAdBlockingProne( $path ) || $this->filesystem->has( $path ) );
        
        $videoFile->setPath( $path );
        
        $storageType    = $this->videoPlatform->getOriginalVideosStorage()->getStorageType();
        $videoFile->setStorageType( $storageType );
        
        $this->filesystem->write(
            $videoFile->getPath(),
            file_get_contents( $videoFile->getFile()->getPathname() )
        );
        
        $videoFile->setType( $this->filesystem->mimeType( $videoFile->getPath() ) );
    }
    
    public function remove( string $path ): bool
    {
        if ( $this->filesystem->has( $path ) ) {
            return $this->filesystem->delete( $path );
        }
        
        return false;
    }
    
    private function has( string $path ): bool
    {
        return $this->filesystem->has( $path );
    }
    
    /**
     * Will return true if the path is prone to be blocked by ad blockers
     */
    private function isAdBlockingProne( string $path ): bool
    {
        return strpos( $path, 'ad' ) !== false;
    }
}
