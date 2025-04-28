<?php namespace App\Controller\Traits;

use App\Component\Cloud\Coconut\Coconut;
use App\Entity\Api\RefreshToken;

trait RefreshTokenTrait
{
    protected $jobsNeeded   = [
        Coconut::JOB_TYPE_VIDEO,
        //Coconut::JOB_TYPE_CLIP,
        //Coconut::JOB_TYPE_CLIP_STORE,
    ];
    
    public function invalidateRefreshToken( RefreshToken $token, string $jobType )
    {
        $token->addJobFinished( $jobType );
        $jobsFinished   = $token->getJobsFinished();
        if ( count( $jobsFinished ) === count( $this->jobsNeeded ) ) {
            $this->doctrine->getManager()->remove( $token );
        } else {
            $this->doctrine->getManager()->persist( $token );
        }
        
        $this->doctrine->getManager()->flush();
    }
}
