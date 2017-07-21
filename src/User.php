<?php
namespace Simplein;
require_once('Exceptions.php');
require_once('Jwt.php');
use Luracast\Restler\iIdentifyUser;
/**
 * Information gathered about the api user is kept here using static methods
 * and properties for other classes to make use of them.
 * Typically Authentication classes populate them
 *
 * @category   Framework
 * @package    restler
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc6
 */
class User implements iIdentifyUser
{
    private static $secret_key = '#w3ASDa3R@#Edad12#w';
    private static $initialized = false;
    private static $current_user = [];

    public static $id = null;
    public static $cacheId = null;
    public static $ip;
    public static $browser = '';
    public static $platform = '';

    public static $access_token = '';
    

    public static function init()
    {
        $auth_header = false;
        static::$initialized = true;
        static::$ip = static::getIpAddress();
        static::$access_token = '';
        if (isset($_SERVER['Authorization'])) {
            $auth_header = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $auth_header = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $auth_header = trim($requestHeaders['Authorization']);
            }
        }
        // HEADER: Get the access token from the header
        if (!empty($auth_header)) {
            if (preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
                static::$access_token = $matches[1];
                static::$current_user = JWT::decode($matches[1], static::$secret_key);
                static::setUniqueIdentifier(static::$current_user->id);
            }
        }

        static::setUniqueIdentifier( static::$current_user->id ?? 0  );
        
    }

    public static function getIpAddress($ignoreProxies = false)
    {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR',
                     'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP',
                     'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED',
                     'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe

                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4
                        | FILTER_FLAG_NO_PRIV_RANGE
                        | FILTER_FLAG_NO_RES_RANGE) !== false
                    ) {
                        return $ip;
                    }
                }
            }
        }
    }

    public static function makeTokenFromUserData( $data ){
      $token = JWT::encode($data, static::$secret_key);
      return $token;
    }

    /**
     * Authentication classes should call this method
     *
     *
     * @return string|int
     */
    public static function getCurrentUser($param=null)
    {
        if (!static::$initialized) static::init();
        return $param ? static::$current_user->$param?? false : static::$current_user;
    }
    
    
    /**
     * Authentication classes should call this method
     *
     *
     * @return string|int
     */
    public static function getUniqueIdentifier($includePlatform = false)
    {
        if (!static::$initialized) static::init();
        return static::$id ? : static::getCacheIdentifier();
    }

    /**
     * Authentication classes should call this method
     *
     * @param string $id user id as identified by the authentication classes
     *
     * @return void
     */
    public static function setUniqueIdentifier($id)
    {
        static::$id = $id;
        static::setCacheIdentifier($id);
    }

    /**
     * User identity to be used for caching purpose
     *
     * When the dynamic cache service places an object in the cache, it needs to
     * label it with a unique identifying string known as a cache ID. This
     * method gives that identifier
     *
     * @return string
     */
    public static function getCacheIdentifier()
    {
        return static::$cacheId ?: static::$id;
    }

    public static function setCacheIdentifier($id){
      static::$cacheId = base64_encode('ip:'.static::getIpAddress().',id:'.$id);
    }
}
