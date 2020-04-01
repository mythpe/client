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
 * Class MakeSecretException
 * @package Myth\Api\Client\Exceptions
 */
class MakeSecretException extends Exception{

    /**
     * MakeSecretException constructor.
     * @param string $message
     */
    public function __construct($message = 'Can not make a new secret'){
        parent::__construct($message);
    }
}
