<?php namespace App\Entity\Api;

use Doctrine\ORM\Mapping as ORM;
use Vankosoft\ApiBundle\Model\RefreshToken as BaseRefreshToken;

/**
 * @Doctrine\Common\Annotations\Annotation\IgnoreAnnotation( "ORM\MappedSuperclass" )
 * @Doctrine\Common\Annotations\Annotation\IgnoreAnnotation("ORM\Column")
 */
#[ORM\Entity]
#[ORM\Table(name: "VSAPI_RefreshTokens")]
class RefreshToken extends BaseRefreshToken
{
    /** @var array */
    #[ORM\Column(name: "jobs_finished", type: "json", nullable: false)]
    private $jobsFinished;
    
    public function __construct()
    {
        $this->jobsFinished = [];
    }
    
    public function getJobsFinished(): array
    {
        return $this->jobsFinished;
    }
    
    public function setJobsFinished($jobsFinished): self
    {
        $this->jobsFinished  = $jobsFinished;
        
        return $this;
    }
    
    public function addJobFinished($jobFinished): self
    {
        $this->jobsFinished[]  = $jobFinished;
        
        return $this;
    }
}
