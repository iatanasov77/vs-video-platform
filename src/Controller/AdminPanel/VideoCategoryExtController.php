<?php namespace App\Controller\AdminPanel;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Vankosoft\ApplicationBundle\Repository\TaxonomyRepository;
use Vankosoft\ApplicationBundle\Controller\Traits\TaxonomyTreeDataTrait;
use Vankosoft\ApplicationBundle\Component\Status;

class VideoCategoryExtController extends AbstractController
{
    use TaxonomyTreeDataTrait;
    
    protected $taxonomyId;
    
    public function __construct(
        TaxonomyRepository $taxonomyRepository,
        $taxonomyCode
    ) {
        $this->taxonomyRepository   = $taxonomyRepository;
        $this->taxonomyId           = $this->taxonomyRepository->findByCode( $taxonomyCode )->getId();
    }
    
    public function easyuiComboTree( Request $request ): Response
    {
        return new JsonResponse( $this->easyuiComboTreeData( $this->taxonomyId ) );
    }
}
