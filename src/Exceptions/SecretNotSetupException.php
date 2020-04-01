<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Client\Exceptions;

use Exception;

/**
 * Class SecretNotSetupException
 * @package Myth\Api\Client\Exceptions
 */
class SecretNotSetupException extends Exception{

    /**
     * SecretNotSetupException constructor.
     * @param string $message
     */
    public function __construct($message = 'Application secret not setup'){
        parent::__construct($message);
    }
}
