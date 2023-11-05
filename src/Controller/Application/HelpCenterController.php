<?php namespace App\Controller\Application;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class HelpCenterController extends AbstractController
{
    /** @var RepositoryInterface */
    private $questionsRepository;
    
    public function __construct(
        RepositoryInterface $questionsRepository
    ) {
        $this->questionsRepository  = $questionsRepository;
    }
    
    public function index( Request $request ): Response
    {
        $questions      = $this->questionsRepository->findAll();
        $countFirstCol  = floor( count( $questions ) / 2 );
        
        return $this->render( 'Pages/help_center_questions.html.twig', [
            'questions'     => $questions,
            'countFirstCol' => $countFirstCol,
        ]);
    }
}