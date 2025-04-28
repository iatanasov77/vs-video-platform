<?php namespace App\Component\VideoUploader;

use League\Flysystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Webmozart\Assert\Assert;

use Vankosoft\CmsBundle\Component\Uploader\AbstractFileUploader;
use Vankosoft\CmsBundle\Component\Generator\FilePathGeneratorInterface;
use Vankosoft\CmsBundle\Model\Interfaces\FileInterface;
use App\Component\VideoPlatform;

class LocalUploader extends AbstractFileUploader
{
    /** @var VideoPlatform */
    protected $videoPlatform;
    
    public function __construct(
        Filesystem $filesystem,
        FilePathGeneratorInterface $filePathGenerator,
        VideoPlatform $videoPlatform
    ) {
        parent::__construct( $filesystem, $filePathGenerator );
        
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
}
