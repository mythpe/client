<?php
/**
 * Copyright MyTh
 * Website: https://4MyTh.com
 * Email: mythpe@gmail.com
 * Copyright © 2006-2020 MyTh All rights reserved.
 */

namespace Myth\Api\Client\Traits;

use Illuminate\Http\Request;
use Myth\Api\Client\Http\Controllers\BaseController as Controller;
use Myth\Api\Client\Http\Resources\ApiValidation;

/**
 * Trait MythApiClientAbstractTrait
 * @package Myth\Api\Client\Traits
 */
trait MythApiClientAbstractTrait{

    /**
     * Can override
     * @param Request $request
     * @return array
     */
    abstract function getMythApiFillable(Request $request): array;

    /**
     * Can override
     * @return array
     */
    abstract function toMythApiArray(): array;

    /**
     * Can override
     * Function validate
     * return true if success
     * return string message if 'false|fail'
     * @param Request $request
     * @param Controller $controller
     * @return bool|null|mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    abstract function mythApiValidation(Request $request, Controller $controller): ApiValidation;

    /**
     * Event before saving
     * @param Request $request
     */
    abstract function mythApiClientSaving(Request $request): void;

    /**
     * Event after saving
     * @param Request $request
     */
    abstract function mythApiClientSaved(Request $request): void;

    /**
     * update application model on api update client
     * @param Request $request
     * @param array $data
     * @return bool
     */
    abstract function mythApiUpdateModelClientData(Request $request, array $data = []): bool;

    /**
     * bind route uri for model on api
     * @return string
     */
    abstract function getMythApiRouteName(): string;

}