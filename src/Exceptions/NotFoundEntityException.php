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
 * Class NotFoundEntityException
 * @package Myth\Api\Client\Exceptions
 */
class NotFoundEntityException extends Exception{

    /**
     * NotFoundEntityException constructor.
     * @param string $message
     */
    public function __construct($message = 'Entity Not Found'){
        parent::__construct($message);
    }
}
