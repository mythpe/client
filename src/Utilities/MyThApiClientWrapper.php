<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Client\Utilities;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Foundation\Application;
use Myth\Api\Client\Exceptions\GetSecretException;
use Myth\Api\Client\Exceptions\ManagerNotFountException;

/**
 * Class MythApiClientWrapper
 * @package Myth\Api\Client\Utilities
 */
class MythApiClientWrapper{

    use ApiClientSecretWrapperTrait;
    use ApiClientHttpWrapperTrait;
    use ApiClientModelWrapperTrait;

    /** @var Application $app laravel application */
    protected $app;
    /** @var array $config api client config */
    protected $config;
    /**
     * @var string $managerName manager connection name
     */
    protected $managerName;

    /**
     * MythApiClientWrapper constructor.
     * @param $app
     * @param $config
     * @throws GetSecretException
     */
    public function __construct($app, $config){
        $this->app = $app;
        $this->config = $config;
        try{
            $this->setSecret($this->getSecretFromFile());
        }
        catch(FileNotFoundException $e){
            throw new GetSecretException();
        }
    }

    /**
     * @return array
     * @throws ManagerNotFountException
     */
    public function getEntities(): array{
        if(!($managerName = $this->getManagerName())){
            throw new ManagerNotFountException();
        }
        return $this->config['managers'][$managerName];
    }

    /**
     * @return string
     */
    public function getManagerName(){
        return $this->managerName;
    }

    /**
     * @param string $managerName
     */
    public function setManagerName(string $managerName): void{
        $this->managerName = $managerName;
    }

    /**
     * @param $managerName
     * @return bool
     */
    public function hasManager($managerName): bool{
        return array_key_exists($managerName, $this->config['managers']);
    }

    /**
     * @return array
     */
    public function getConfig(): array{
        return $this->config;
    }
}