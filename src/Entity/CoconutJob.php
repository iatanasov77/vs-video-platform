<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity]
#[ORM\Table(name: "VVP_CoconutJobs")]
class CoconutJob implements ResourceInterface
{
    use TimestampableEntity;
    
    const STATUS_JOB_STARTING       = 'job.starting';
    const STATUS_JOB_NOT_FOUND      = 'job.not_found';
    
    const EVENT_JOB_COMPLETED       = 'job.completed';
    const EVENT_JOB_FAILED          = 'job.failed';
    const EVENT_INPUT_TRANSFERRED   = 'input.transferred';
    const EVENT_OUTPUT_COMPLETED    = 'output.completed';
    const EVENT_OUTPUT_FAILED       = 'output.failed';
    
    /** @var int */
    #[ORM\Id, ORM\Column(type: "integer"), ORM\GeneratedValue(strategy: "IDENTITY")]
    private $id;
    
    /** @var Video */
    #[ORM\OneToOne(targetEntity: "Video", inversedBy: "coconutJob")]
    #[ORM\JoinColumn(name: "video_id", referencedColumnName: "id")]
    private $video;
    
    /** @var string */
    #[ORM\Column(name: "job_id", type: "string", length: 32)]
    private $jobId;
    
    /** @var array */
    #[ORM\Column(name: "job_data", type: "json", nullable: true)]
    private $jobData;
    
    /** @var string */
    #[ORM\Column(type: "string", length: 32)]
    private $status;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getVideo()
    {
        return $this->video;
    }
    
    public function setVideo($video)
    {
        $this->video = $video;
        
        return $this;
    }
    
    public function getJobId()
    {
        return $this->jobId;
    }
    
    public function setJobId($jobId)
    {
        $this->jobId = $jobId;
        
        return $this;
    }
    
    public function getJobData()
    {
        return $this->jobData;
    }
    
    public function setJobData($jobData)
    {
        $this->jobData  = $jobData;
        
        return $this;
    }
    
    public function getStatus()
    {
        return $this->status;
    }
    
    public function setStatus($status)
    {
        $this->status = $status;
        
        return $this;
    }
}