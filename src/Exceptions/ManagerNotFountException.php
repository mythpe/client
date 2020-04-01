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
 * Class ManagerNotFountException
 * @package Myth\Api\Client\Exceptions
 */
class ManagerNotFountException extends Exception{

    /**
     * ManagerNotFountException constructor.
     * @param string $message
     */
    public function __construct($message = 'Manager not found'){
        parent::__construct($message);
    }
}
