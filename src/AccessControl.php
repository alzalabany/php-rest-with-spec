<?php
use \Luracast\Restler\iAuthenticate;
use \Luracast\Restler\Resources;
use \Luracast\Restler\Defaults;
class AccessControl implements iAuthenticate
{
    public static $role = 'n/a';

    public function __isAllowed()
    {
        $roles = array('12345' => 'user', '67890' => 'admin');
        $userClass = Defaults::$userIdentifierClass;
        $role = $roles[$_GET['api_key']??''] ?? $userClass::getCurrentUser('role');
        
        return $role === 'admin' ? true : static::$role === $role;
    }
    public function __getWWWAuthenticateString()
    {
        return 'Bearer realm="simpleinformatics"';
    }
    /**
     * This function is used by ResetAPI Explorer to show/hide function in 
     * swagger documentations
     * 
     * it has no effect on runtime.
     * 
     * @param $m 
     * @access private
     */
    public static function verifyAccess(array $m)
    {
        print_r($m);
        $requires =
            isset($m['class']['AccessControl']['properties']['requires'])
                ? $m['class']['AccessControl']['properties']['requires']
                : false;
        return $requires
            ? static::$role == 'admin' || static::$role == $requires
            : true;
    }
}