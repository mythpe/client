<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Client\Utilities;

use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Trait ApiClientSecretWrapperTrait
 * @package Myth\Api\Client\Utilities
 */
trait ApiClientSecretWrapperTrait{

    /** @var string */
    protected $secret = '';

    /** @var string Secret File name */
    protected $secretFileName = "myth_api_client.key";

    /**
     * Make New Application secret
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function makeSecret(): string{
        $time = Carbon::now();
        $key = $this->config['secret'];
        $name = $this->secretFileName;
        $h = urlencode(Crypt::encrypt(base64_encode(Str::random(40).$time.$key)));
        try{
            $this->disk()->put($name, $h);
            $secret = $this->disk()->get($name);
            $this->setSecret($secret);
        }
        catch(\Exception $exception){
        }
        return $this->getSecret();
    }

    /**
     * @return string
     */
    public function getSecret(): string{
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret(string $secret): void{
        $this->secret = $secret;
    }

    /**
     * @return \Illuminate\Contracts\Filesystem\Filesystem|\Illuminate\Filesystem\FilesystemAdapter
     */
    protected function disk(){
        return Storage::disk($this->config['file_system_disk']);
    }

    /**
     * Get Secret From Secret File
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function getSecretFromFile(): string{
        return (string) !$this->disk()->exists($this->secretFileName) ? "" : $this->disk()->get($this->secretFileName);
    }
}