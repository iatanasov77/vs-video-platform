<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use App\Entity\Application\Application;

#[ORM\Entity]
#[ORM\Table(name: "VVP_VideoPlatformApplications")]
class VideoPlatformApplication implements ResourceInterface
{
    /** @var int */
    #[ORM\Id, ORM\Column(type: "integer"), ORM\GeneratedValue(strategy: "IDENTITY")]
    private $id;
    
    /** @var Application */
    #[ORM\OneToOne(targetEntity: Application::class, inversedBy: "videoPlatformApplication")]
    #[ORM\JoinColumn(name: "application_id", referencedColumnName: "id")]
    private $application;
    
    /** @var VideoPlatformSettings */
    #[ORM\ManyToOne(targetEntity: "VideoPlatformSettings", inversedBy: "videoPlatformApplication")]
    #[ORM\JoinColumn(name: "settings_id", referencedColumnName: "id")]
    private $settings;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getApplication()
    {
        return $this->application;
    }
    
    public function setApplication($application)
    {
        $this->application  = $application;
        
        return $this;
    }
    
    public function getSettings()
    {
        return $this->settings;
    }
    
    public function setSettings($settings)
    {
        $this->settings  = $settings;
        
        return $this;
    }
}