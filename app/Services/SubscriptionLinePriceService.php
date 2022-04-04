<?php

namespace App\Services;

use Logging;
use App\Models\SubscriptionLine;
use App\Models\SubscriptionLinePrice;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class SubscriptionLinePriceService
{
    /**
     * Return a list of subscription line prices
     */
    public function list(Request $request)
    {
        return QueryBuilder::for(SubscriptionLinePrice::class, request())
            ->allowedFields(SubscriptionLinePrice::$fields)
            ->allowedIncludes(SubscriptionLinePrice::$scopes)
            ->allowedFilters(SubscriptionLinePrice::$fields)
            ->defaultSort('-id')
            ->allowedSorts(SubscriptionLinePrice::$fields);
    }

    /**
     * Store a newly created subscription line price
     *
     * @return array
     */
    public function create(SubscriptionLine $subscriptionLine, array $inputParams)
    {
        $attributes = filterArrayByKeys(
            $inputParams,
            [
                'subscription_line_id',
                'parent_plan_line_id',
                'fixed_price',
                'margin',
                'price_valid_from'
            ]
        );

        $isValid = $this->formatAndValidateValues($attributes, $subscriptionLine);
        if (!$isValid['success']) {
            return $isValid;
        }

        $subscriptionLinePrice = new SubscriptionLinePrice($attributes);
        $subscriptionLine->subscriptionLinePrices()->save($subscriptionLinePrice);
        Logging::information('Create Subscription Line Price', $subscriptionLinePrice, 1);

        return [
            'success' => true,
            'data' => SubscriptionLinePrice::find($subscriptionLinePrice->id),
            'message' => 'SubscriptionPricePlan was created successfully'
        ];
    }

    /**
     * Return a subscription line price
     *
     * @param int $id
     *
     * @return QueryBuilder
     */
    public function show($id)
    {
        return QueryBuilder::for(SubscriptionLinePrice::where('id', $id))
            ->allowedFields(SubscriptionLinePrice::$fields)
            ->allowedIncludes(SubscriptionLinePrice::$scopes);
    }

    /**
     * Update a subscription line price
     *
     * @param SubscriptionLinePrice $subscriptionLinePrice
     * @param array $inputParams
     *
     * @return array
     */
    public function update(SubscriptionLinePrice $subscriptionLinePrice, array $inputParams): array
    {
        $attributes = filterArrayByKeys(
            $inputParams,
            [
                'subscription_line_id',
                'parent_plan_line_id',
                'fixed_price',
                'margin',
                'price_valid_from'
            ]
        );

        $isValid = $this->formatAndValidateValues($attributes, $subscriptionLinePrice->subscriptionLine, $subscriptionLinePrice->id);
        if (!$isValid['success']) {
            return $isValid;
        }

        $log['old_values'] = $subscriptionLinePrice->getRawDBData();
        $subscriptionLinePrice->update($attributes);

        $log['new_values'] = $subscriptionLinePrice->getRawDBData();
        $log['changes'] = $subscriptionLinePrice->getChanges();
        Logging::information('Update Subscription Line Price', $log, 1, 1);

        return [
            'success' => true,
            'data' => SubscriptionLinePrice::find($subscriptionLinePrice->id),
            'message' => 'SubscriptionPricePlan was updated successfully'
        ];
    }

    private function formatAndValidateValues(array &$attributes, SubscriptionLine $subscriptionLine, $linePriceId = 0)
    {
        if ($linePriceId) {
            $linePrice = SubscriptionLinePrice::find($linePriceId);
        }
        $hasFixedPriceValue = (array_key_exists('fixed_price', $attributes) && $attributes['fixed_price'] !== null);
        $hasMarginValue = (array_key_exists('margin', $attributes) && $attributes['margin'] !== null);
        $hasLinePrices = $subscriptionLine->subscriptionLinePrices()->where([
                ['price_valid_from', '<=', $subscriptionLine->subscription_start],
                ['id', '!=', $linePriceId]
            ])->exists() || $attributes['price_valid_from'] <= $subscriptionLine->subscription_start;

        $isPriceChanged = (isset($linePrice) && (
                ($hasMarginValue && $attributes['margin'] != $linePrice->margin) ||
                ($hasFixedPriceValue && $attributes['fixed_price'] != $linePrice->fixed_price)));

        if (
            $isPriceChanged && $attributes['price_valid_from'] && $subscriptionLine->last_invoice_stop &&
            $attributes['price_valid_from'] < $subscriptionLine->last_invoice_stop
        ) {
            return [
            'success' => false,
            'message' => 'Cannot change a price from before invoiced period.'
            ];
        }

        if ($hasFixedPriceValue && $hasMarginValue) {
            return [
            'success' => false,
            'message' => 'Cannot enter both a fixed price and a margin.'
            ];
        }

        if (!$hasFixedPriceValue && !$hasMarginValue) {
            return [
            'success' => false,
            'message' => 'At least one field is required; fixed_price, margin.'
            ];
        }

        if ($hasMarginValue && !$subscriptionLine->planLine) {
            return [
            'success' => false,
            'message' => 'Cannot enter a margin if there is no linked plan line.'
            ];
        }

        if (!$hasLinePrices && (!$subscriptionLine->subscription_start || $attributes['price_valid_from'] > $subscriptionLine->subscription_start)) {
            return [
            'success' => false,
            'message' => 'The valid-from date of the first price has to be before subscription start.'
            ];
        }

        if (
            $hasLinePrices && (SubscriptionLinePrice::where([
                ['subscription_line_id', $subscriptionLine->id],
                ['price_valid_from', $attributes['price_valid_from']],
                ['id', '!=', $linePriceId]])->count())
        ) {
            return [
            'success' => false,
            'message' => 'Another price with the same valid-from date exists.'
                ];
        }

        if ($hasFixedPriceValue) {
            $attributes['fixed_price'] = parseFloatGuess($attributes['fixed_price']);
        }
        if ($hasMarginValue) {
            $attributes['margin'] = parseFloatGuess($attributes['margin']);
        }

                return ['success' => true];
    }
}
