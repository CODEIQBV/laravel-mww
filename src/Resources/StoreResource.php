<?php

namespace YourNamespace\MyOnlineStore\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'primary_domain' => $this->primary_domain,
            'description' => $this->description,
            'open' => $this->open,
            'version' => $this->version,
            'administration_language' => $this->administration_language,
            'currency' => $this->currency,
            'prices_include_tax' => $this->prices_include_tax,
            'default_language' => $this->default_language,
            'active_languages' => $this->active_languages,
            'currency_format_locale' => $this->currency_format_locale,
            'available_business_models' => $this->available_business_models,
        ];
    }
} 