<?php namespace App\Entity\Api;

use Doctrine\ORM\Mapping as ORM;
use Vankosoft\ApiBundle\Model\RefreshToken as BaseRefreshToken;

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
    
    public function getJobsFinished()
    {
        return $this->jobData;
    }
    
    public function setJobsFinished($jobsFinished)
    {
        $this->jobsFinished  = $jobsFinished;
        
        return $this;
    }
    
    public function addJobFinished($jobFinished)
    {
        $this->jobsFinished[]  = $jobFinished;
        
        return $this;
    }
}
