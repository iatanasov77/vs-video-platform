<?php namespace App\Controller\VideoPlatform;

use Vankosoft\UsersBundle\Controller\ForgotPasswordController as BaseForgotPasswordController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ExpiredResetPasswordTokenException;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

use Vankosoft\UsersBundle\Repository\ResetPasswordRequestRepository;
use Vankosoft\UsersBundle\Security\UserManager;

class ForgotPasswordController extends BaseForgotPasswordController
{
    use GlobalFormsTrait;
    
    public function __construct(
        ManagerRegistry $doctrine,
        ResetPasswordRequestRepository $repository,
        RepositoryInterface $usersRepository,
        MailerInterface $mailer,
        Factory $resetPasswordRequestFactory,
        UserManager $userManager,
        array $parameters
    ) {
        parent::__construct( $doctrine, $repository, $usersRepository, $mailer, $resetPasswordRequestFactory, $userManager, $parameters );
    }
    
    public function indexAction( Request $request, MailerInterface $mailer ) : Response
    {
        $error  = null;
        $form   = $this->getForgotPasswordForm();
        $form->handleRequest( $request );
        if (  $form->isSubmitted() && $form->isValid() ) {
            $email  = $form->get( 'email' )->getData();
            $user   = $this->usersRepository->findOneBy( ['email' => $email] );
            if ( $user ) {
                return parent::indexAction( $request, $mailer );
            } else {
                $error  = 'vs_vvp.form.forgot_password.email_not_found';
            }
        }
        
        return $this->render( '@VSUsers/Resetting/forgot_password.html.twig', [
            'form'          => $form->createView(),
            'formError'     => $error,
            'formErrors'    => $form->getErrors( true ),
            
            //'shoppingCart'  => $this->getShoppingCart( $request ),
        ]);
    }
    
    public function resetAction( string $token, Request $request ) : Response
    {
        $tokenExpired   = false;
        try {
            $oUser   = $this->resetPasswordHelper->validateTokenAndFetchUser( $token );
        } catch ( ExpiredResetPasswordTokenException $e ) {
            $tokenExpired   = true;
        }
        
        $form   = $this->getChangePasswordForm( $token );
        $form->handleRequest( $request );
        if ( $form->isSubmitted() && ! $tokenExpired ) {
            return parent::resetAction( $token, $request );
        }
        
        $params = [
            'user'          => $oUser,
            'token'         => $token,
            'form'          => $form->createView(),
            
            //'shoppingCart'  => $this->getShoppingCart( $request ),
        ];
        return $this->render( '@VSUsers/Resetting/change_password.html.twig', $params );
    }
}
