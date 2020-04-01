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
 * Class HeaderManagerKeyException
 * @package Myth\Api\Client\Exceptions
 */
class HeaderManagerKeyException extends Exception{

    /**
     * HeaderManagerKeyException constructor.
     * @param string $message
     */
    public function __construct($message = 'Manager name not provided'){
        parent::__construct($message);
    }
}
