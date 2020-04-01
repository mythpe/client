<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Client\Http\Resources;

/**
 * Class ApiValidation
 * @package Myth\Api\Client\Http\Resources
 */
class ApiValidation{

    /**
     * @var string
     */
    protected $message;

    /**
     * @var bool
     */
    protected $success;

    /**
     * @var array
     */
    protected $data;

    /**
     * ApiValidation constructor.
     * @param string $message
     * @param bool $success
     * @param array $data
     */
    public function __construct(string $message = '', bool $success = true, array $data = []){
        $this->message = (string) $message;
        $this->success = (boolean) boolval($success);
        $this->data = (array) $data;
    }

    /**
     * @return mixed
     */
    public function getMessage(){
        return $this->message;
    }

    /**
     * @param mixed $message
     * @return ApiValidation
     */
    public function setMessage($message){
        $this->message = $message;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSuccess(){
        return $this->success;
    }

    /**
     * @param mixed $success
     * @return ApiValidation
     */
    public function setSuccess($success){
        $this->success = $success;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData(){
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return ApiValidation
     */
    public function setData($data){
        $this->data = $data;
        return $this;
    }
}