<?php namespace App\Controller\AdminPanel;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use App\Form\ActorForm;

class ActorExtController extends AbstractController
{
    /** @var ManagerRegistry */
    protected ManagerRegistry $doctrine;
    
    /** @var RepositoryInterface */
    protected RepositoryInterface $repository;
    
    public function __construct(
        ManagerRegistry $doctrine,
        RepositoryInterface $repository
    ) {
        $this->doctrine     = $doctrine;
        $this->repository   = $repository;
    }
    
    public function getForm( $itemId, $locale, Request $request ): Response
    {
        $em     = $this->doctrine->getManager();
        $item   = $this->repository->find( $itemId );
        
        if ( $locale != $request->getLocale() ) {
            $item->setTranslatableLocale( $locale );
            $em->refresh( $item );
        }
        
        return $this->render( 'Pages/Actors/actor_form.html.twig', [
            'item'  => $item,
            'form'  => $this->createForm( ActorForm::class, $item )->createView(),
        ]);
    }
}