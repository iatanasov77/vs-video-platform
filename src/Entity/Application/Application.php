<?php namespace App\Entity\Application;

use Doctrine\ORM\Mapping as ORM;
use Vankosoft\ApplicationBundle\Model\Application as BaseApplication;
use App\Entity\VideoPlatformApplication;

#[ORM\Entity]
#[ORM\Table(name: "VSAPP_Applications")]
class Application extends BaseApplication
{
    /** @var VideoPlatformApplication */
    #[ORM\OneToOne(targetEntity: VideoPlatformApplication::class, mappedBy: "application")]
    private $videoPlatformApplication;
    
    public function getVideoPlatformApplication()
    {
        return $this->videoPlatformApplication;
    }
    
    public function setVideoPlatformApplication($videoPlatformApplication)
    {
        $this->videoPlatformApplication  = $videoPlatformApplication;
        
        return $this;
    }
}
