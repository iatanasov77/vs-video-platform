<?php namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Oneup\UploaderBundle\Controller\AbstractController as UploaderController;
use Oneup\UploaderBundle\Uploader\Response\EmptyResponse;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;

use Vankosoft\UsersBundle\Component\UserNotifications;
use Vankosoft\ApplicationBundle\Component\Status;

//class CoconutStorageController extends AbstractController
class CoconutStorageController extends UploaderController
{
    /** @var RequestStack */
    private $requestStack;
    
    /** @var UserNotifications */
    private $notifications;
    
    public function __construct(
        RequestStack $requestStack,
        UserNotifications $notifications
    ) {
        $this->requestStack     = $requestStack;
        $this->notifications    = $notifications;
    }
    
    public function index( Request $request ): JsonResponse
    {
        $this->_sendNotification();
        
        $response   = [
            'status'    => Status::STATUS_OK,
            'data'      => [
                'test_property' => 'Test Property',
            ]
        ];
        
        return new JsonResponse( $response );
    }
    
    public function upload(): JsonResponse
    {
        // get some basic stuff together
        $request    = $this->requestStack->getMainRequest();
        $response   = new EmptyResponse();
        
        $this->_sendNotification();
        return new JsonResponse( $response->assemble() );
        
        /*
        // get file from request (your own logic)
        $file = ...;
        
        try {
            $uploaded   = $this->handleUpload( $file );
        } catch( UploadException $e ) {
            // return nothing
            return new JsonResponse( array() );
        }
        
        // return assembled response
        return new JsonResponse( $response->assemble() );
        */
    }
    
    private function _sendNotification()
    {
        $notificationData   = \json_encode( $_POST ) . "\n\n" . \json_encode( $_FILES );
        $this->notifications->sentNotificationByRole( 'role-super-admin', 'Coconut Storage', 'coconut-storage-debug', $notificationData );
    }
}