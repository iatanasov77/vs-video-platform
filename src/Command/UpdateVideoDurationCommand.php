<?php namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Doctrine\Persistence\ManagerRegistry;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use FFMpeg\FFProbe;
use App\Component\Cloud\Exception\VideoPlatformStorageException;
use App\Component\VideoPlatform;

#[AsCommand(
    name: 'app:update-video-duration',
    description: 'Update Video Duration on All Videos.',
    hidden: false
)]
class UpdateVideoDurationCommand extends Command
{
    /** @var ManagerRegistry */
    private $doctrine;
    
    /** @var FFProbe */
    private $ffprobe;
        
    /** @var EntityRepository */
    private $videoFileRepository;
    
    /** @var VideoPlatform */
    private $videoPlatform;
    
    public function __construct(
        ManagerRegistry $doctrine,
        FFProbe $ffprobe,
        EntityRepository $videoFileRepository,
        VideoPlatform $videoPlatform
    ) {
        parent::__construct();
        
        $this->doctrine             = $doctrine;
        $this->ffprobe              = $ffprobe;
        $this->videoFileRepository  = $videoFileRepository;
        $this->videoPlatform        = $videoPlatform;
    }
    
    protected function configure(): void
    {
        $this
            ->setHelp( 'This command allows you to Update Video Duration on All Videos.' )
        ;
    }
    
    protected function execute( InputInterface $input, OutputInterface $output ): int
    {
        $storageSettings    = $this->videoPlatform->getOriginalVideosStorage()->getSettings();
        if ( ! isset( $storageSettings['bucket'] ) ) {
            throw new VideoPlatformStorageException( 'Video Platform Storage Not Configured Properly !!!' );
        }
        
        $em         = $this->doctrine->getManager();
        $videoFiles = $this->videoFileRepository->findAll();
        
        foreach ( $videoFiles as $video ) {
            $filmDuration       = null;
            $filmDuration   = $this->ffprobe->streams(
                $this->videoPlatform->getVideoUri( $video, $storageSettings['bucket'] )
            )->videos()->first()->get( 'duration' );
            
            $video->setDuration( $filmDuration );
            
            $em->persist( $video );
            $em->flush();
        }
        
        return Command::SUCCESS;
    }
}