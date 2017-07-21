<?php
use Luracast\Restler\Defaults;

class Home
{
    /**
     * Undocumented function
     * @access protected
     * @class  AccessControl {@role admin}
     *
     * @return void
     */
    protected function index()
    {
        $userClass = Defaults::$userIdentifierClass;
        return array(
            'success' => array(
                'code' => 200,
                'id' => $userClass::getUniqueIdentifier(),
                'cacheid' => $userClass::getCacheIdentifier(),
                'data' => $userClass::getCurrentUser(),
                'access_token' => $userClass::$access_token,
                'token' => $userClass::makeTokenFromUserData(['id'=>2,'role'=>'admin','x'=>['id'=>2,'role'=>'admin','x'=>['id'=>2,'role'=>'admin','x'=>['id'=>2,'role'=>'admin','x'=>['id'=>2,'role'=>'admin']]]]]),
                'message' => 'Restler is up and running!'.$userClass,
            ),
        );
    }
}
