<?php namespace App\Controller\Application;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class ErrorController extends AbstractController
{
    use GlobalFormsTrait;
    
    /** @var ManagerRegistry */
    private ManagerRegistry $doctrine;
    
    /** @var TaxonomyInterface */
    private $videoCategoriesTaxonomy;
    
    public function __construct(
        ManagerRegistry $doctrine,
        EntityRepository $taxonomyRepository,
        string $videoCategoriesTaxonomyCosde
    ) {
        $this->doctrine                 = $doctrine;
        $this->videoCategoriesTaxonomy  = $taxonomyRepository->findByCode( $videoCategoriesTaxonomyCosde );
    }
    
    public function accessDenied( Request $request ): Response
    {
        return $this->render( 'admin-panel/pages/Error/access-denied.html.twig', [
            
        ]);
    }
}
