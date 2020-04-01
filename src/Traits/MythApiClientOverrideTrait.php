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
 * Trait MythApiClientOverrideTrait
 * @package Myth\Api\Client\Traits
 */
trait MythApiClientOverrideTrait{

    /**
     * Can override
     * Get store client fillable
     * @param Request $request
     * @return array
     */
    public function getMythApiFillable(Request $request): array{
        return $request->only($this->getFillable());
    }

    /**
     * Can override
     * transform client api data
     * @return array
     */
    public function toMythApiArray(): array{
        return (array) $this->toArray();
    }

    /**
     * Can override
     * Function validate before store new client data
     * @param Request $request
     * @param Controller $controller
     * @return bool|null|mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function mythApiValidation(Request $request, Controller $controller): ApiValidation{
        return new ApiValidation("", true, []);
    }

    /**
     * Can override
     * Event before saving new client data
     * @param Request $request
     */
    public function mythApiClientSaving(Request $request): void{ }

    /**
     * Can override
     * Event after saving new client data
     * @param Request $request
     */
    public function mythApiClientSaved(Request $request): void{ }

    /**
     * Can override
     * Method update client data by api
     * @param Request $request
     * @param array $data
     * @return bool
     */
    public function mythApiUpdateModelClientData(Request $request, array $data = []): bool{
        return true;
    }
}