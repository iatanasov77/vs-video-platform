<?php namespace App\Controller\AdminPanel;

use Symfony\Component\Security\Core\User\UserInterface;

use App\Entity\Video;
use App\Entity\VideoCategory;

trait GlobalFormsTrait
{
    protected function checkHasAccess( Video $video ): bool
    {
        return true;    // Work-Around
        
        $hasAccess  = $video->isPublic();
        if ( $hasAccess ) {
            return  true;
        }
        
        return false;
    }
    
    protected function getAppUser():? UserInterface
    {
        if ( $this->container->has( 'vs_users.security_bridge' ) ) {
            return $this->container->get( 'vs_users.security_bridge' )->getUser();
        }

        return $this->getUser();
    }
    
    protected function _getDoctrine()
    {
        if ( \method_exists( $this, 'getDoctrine' ) ) {
            return $this->getDoctrine();
        } else {
            return $this->doctrine;
        }
    }
}
