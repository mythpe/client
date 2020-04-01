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
 * Class GetSecretException
 * @package Myth\Api\Client\Exceptions
 */
class GetSecretException extends Exception{

    /**
     * GetSecretException constructor.
     * @param string $message
     */
    public function __construct($message = 'Can not get application secret'){
        parent::__construct($message);
    }
}
