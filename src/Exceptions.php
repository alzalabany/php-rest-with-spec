<?php
namespace Simplein;
use Luracast\Restler\RestException;

/**
 * Special Exception for raising API errors
 * that can be used in API methods
 *
 * @category   Framework
 * @package    Restler
 * @subpackage exception
 * @author     R.Arul Kumaran <arul@luracast.com>
 * @copyright  2010 Luracast
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://luracast.com/products/restler/
 * @version    3.0.0rc6
 */

class DomainException extends RestException{
    public function __construct($errorMessage = 'Hack attempt recorded')
    {
        parent::__construct(401, $errorMessage);
    }
}
class UnexpectedValueException extends RestException
{

    /**
     * @param string      $httpStatusCode http status code
     * @param string|null $errorMessage   error message
     * @param array       $details        any extra detail about the exception
     * @param Exception   $previous       previous exception if any
     */
    public function __construct($errorMessage = 'Hack attempt recorded')
    {
        parent::__construct(401, $errorMessage);
    }

}

