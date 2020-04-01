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
 * Class HeaderSecretKeyException
 * @package Myth\Api\Client\Exceptions
 */
class HeaderSecretKeyException extends Exception{

    /**
     * HeaderSecretKeyException constructor.
     * @param string $message
     */
    public function __construct($message = 'secret not provided'){
        parent::__construct($message);
    }
}
