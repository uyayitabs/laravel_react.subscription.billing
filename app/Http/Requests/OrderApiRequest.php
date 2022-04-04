<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class OrderApiRequest extends BaseApiRequest
{
    public function authorize()
    {
        return isExtTokenAuthorized();
    }

    public function rules(): array
    {
        return [
            // source
            'source' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],

            // customer
            'customer' => [
                'array',
                $this->requiredOrNullable,
                'min:1',
            ],
            'customer.name' => [
                'array',
                $this->requiredOrNullable,
                'min:1',
            ],
            'customer.name.title' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],
            'customer.name.first' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],
            'customer.name.middle' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],
            'customer.name.last' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],

            'customer.birth_date' => [
                'string',
                $this->requiredOrNullable,
                'date_format:Y-m-d',
            ],


            // address
            'address' => [
                'array',
                $this->requiredOrNullable,
                'min:1',
            ],
            'address.street' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],
            'address.house_no' => [
                'string',
                'nullable',
            ],
            'address.house_no_suffix' => [
                'string',
                'nullable',
            ],
            'address.room' => [
                'string',
                'nullable',
            ],
            'address.postal_code' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],
            'address.city' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],
            'address.country' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],
            'address.status' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],

            // contact
            'contact' => [
                'array',
                $this->requiredOrNullable,
                'min:1',
            ],
            'contact.phone' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],
            'contact.mobile' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],
            'contact.email' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],

            // bank
            'bank' => [
                'array',
                $this->requiredOrNullable,
                'min:1',
            ],
            'bank.iban' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],
            'bank.holder' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],

            // contract
            'contract' => [
                'array',
                $this->requiredOrNullable,
                'min:1',
            ],
            'contract.period' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],
            'contract.agreements.*.name' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],
            'contract.agreements.*.text' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],

            // delivery
            'delivery' => [
                'array',
                $this->requiredOrNullable,
                'min:1',
            ],
            'delivery.wish_date' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],

            // product
            'product' => [
                'array',
                $this->requiredOrNullable,
                'min:1',
            ],
            'product.package' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],
            'product.package_price' => [
                $this->requiredOrNullable,
                'between:0,99999.99999',
            ],
            'product.activation_fee' => [
                $this->requiredOrNullable,
                'between:0,99999.99999',
            ],
            'product.activation_fee_tv' => [
                $this->requiredOrNullable,
                'between:0,99999.99999',
            ],
            'product.router_deposit' => [
                $this->requiredOrNullable,
                'between:0,99999.99999',
            ],
            'product.tv' => [
                'array',
                $this->requiredOrNullable,
                'min:1',
            ],
            'product.tv.package_name' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],
            'product.tv.package_price' => [
                $this->requiredOrNullable,
                'between:0,99999.99999',
            ],
            'product.tv.settop_boxes' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],
            'product.extra_packages.*.name' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],
            'product.extra_packages.*.price' => [
                $this->requiredOrNullable,
                'between:0,99999.99999',
            ],
            'product.phone' => [
                'array',
                $this->requiredOrNullable,
                'min:1',
            ],
            'product.phone.use_phone' => [
                'string',
                $this->requiredOrNullable,
                Rule::in(['yes', 'no']),
            ],
            'product.phone.phone_plan' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],
            'product.phone.phone_1' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],
            'product.phone.phone_2' => [
                'string',
                $this->requiredOrNullable,
                'min:1',
            ],
            'product.phone.phone_2_type' => [
                'string',
                $this->requiredOrNullable,
                Rule::in(['new', 'migrate']),
            ],

        ];
    }

    public function attributes()
    {
        return [
            'source' => 'source',
            'customer' => 'customer',
            'customer.name' => "name",
            'customer.name.title' => "title",
            'customer.name.first' => "first name",
            'customer.name.middle' => "middle name",
            'customer.name.last' => "last name",
            'customer.birth_date' => "birth date",
            'address' => 'address',
            'address.street' => 'street',
            'address.house_no' => 'house no.',
            'address.house_no_suffix' => 'house no. suffix',
            'address.room' => 'room',
            'address.postal_code' => 'postal code',
            'address.city' => 'city',
            'address.country' => 'country',
            'address.status' => 'status',
            'contact' => 'contact',
            'contact.phone' => 'phone number',
            'contact.mobile' => 'mobile number',
            'contact.email' => 'email',
            'bank' => 'bank',
            'bank.iban' => 'iban',
            'bank.holder' => 'holder',
            'contract' =>  'contract',
            'contract.period' => 'contract period',
            'contract.agreements.*.name' => "agreement's name",
            'contract.agreements.*.text' => "agreement's text",
            'delivery' => 'delivery',
            'delivery.wish_date' => 'wish date',
            'product' => 'product',
            'product.package' => "product's package",
            'product.package_price' => "product's package price",
            'product.activation_fee' => "product's activation fee",
            'product.activation_fee_tv' => "product's activation fee tv",
            'product.router_deposit' => "product's router deposit",
            'product.tv' => 'tv',
            'product.tv.package_name' => "tv's package name",
            'product.tv.package_price' => "tv's package price",
            'product.tv.settop_boxes' => "tv's setup boxes",
            'product.extra_packages.*.name' => "extra package's name",
            'product.extra_packages.*.price' => "extra package's price",
            'product.phone' => 'phone',
            'product.phone.use_phone' => 'use phone',
            'product.phone.phone_plan' => 'phone plan',
            'product.phone.phone_1' => 'phone 1',
            'product.phone.phone_2' => 'phone 2',
            'product.phone.phone_2_type' => 'phone 2 type',
        ];
    }
}
