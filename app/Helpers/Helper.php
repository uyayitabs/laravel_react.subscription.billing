<?php

use App\Models\Entry;
use App\Models\Journal;
use App\Models\NumberRange;
use App\Models\Relation;
use App\Models\SalesInvoice;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\TenantProduct;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use libphonenumber\PhoneNumberUtil;
use DmitryMamontov\PhoneNormalizer\PhoneNormalizer;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

if (!function_exists('randomNumber')) {
    function randomNumber($from, $to, array $excluded = []): int
    {
        $func = function_exists('random_int') ? 'random_int' : 'mt_rand';

        do {
            $number = $func($from, $to);
        } while (in_array($number, $excluded));

        return $number;
    }
}

if (!function_exists('generateNumberFromNumberRange')) {
    function generateNumberFromNumberRange($tenantId, $type, $saveLastRecord = true): ?string
    {
        // Get NumberRange for specific tenant and type
        $numberRange = NumberRange::where([
            ['tenant_id', '=', $tenantId],
            ['type', '=', $type],
        ])->first();

        if (!is_null($numberRange)) {
            // Get prefix & suffix
            $prefixSuffixData = getNumberRangePrefixSuffix($numberRange->format);
            $prefix = $prefixSuffixData['prefix'];
            $suffix = $prefixSuffixData['suffix'];

            $leadingZeroCount = getNumberRangeZeroPad($numberRange->format);

            // Get last saved number via current field
            $lastSavedNumber = 0;
            if (intval($numberRange->current) > 0) {
                $lastSavedNumber = $numberRange->current;
            }

            // Get previously generated numbers
            $existingNumbers = [];
            switch ($type) {
                case "invoice_no":
                    $existingNumbers = SalesInvoice::getGeneratedInvoiceNos($tenantId);
                    break;

                case "customer_number":
                    $existingNumbers = Relation::getGeneratedCustomerNumbers($tenantId);
                    break;

                case "journal_no":
                    $existingNumbers = Journal::getGeneratedJournalNos($tenantId);
                    break;

                case "entry_no":
                    $journalIds = Journal::getJournalIds($tenantId);
                    $existingNumbers = Entry::getGeneratedEntryNos($journalIds);
                    break;

                case "subscription_no":
                    $existingNumbers = Subscription::getGeneratedSubscriptionNos($tenantId);
                    break;
            }

            // Number generation whether random or increment by 1
            // If not random
            if ($lastSavedNumber > 0 && !$numberRange->randomized) {
                $lastSavedNumber = ($lastSavedNumber += 1);
            }

            // Start with numberRange->start if lastSavedNumber is 0
            if ($lastSavedNumber === 0 && !$numberRange->randomized) {
                $lastSavedNumber = $numberRange->start;
            }


            // If random (generate new number based on previously generated numbers)
            if ($numberRange->randomized) {
                $lastSavedNumber = randomNumber($numberRange->start, $numberRange->end, $existingNumbers);
            }

            //Update NumberRange's 'current' data in DB (last saved number)
            if ($saveLastRecord) {
                $numberRange->update([
                    'current' => $lastSavedNumber
                ]);
            }

            // Generate the number using prefix + lastSavedNumber (optional: with leading zeros) + suffix
            $generatedNumber = $prefix;
            $generatedNumber .= str_pad($lastSavedNumber, $leadingZeroCount, "0", STR_PAD_LEFT);
            $generatedNumber .= $suffix;
            return $generatedNumber;
        }

        return null;
    }
}

if (!function_exists('m7GenerateRequest')) {
    function bankingInformation($params): string
    {
        $accountName = $params['BankingInformation']['AccountName'];
        $bic = $params['BankingInformation']['BIC'];
        $iban = $params['BankingInformation']['IBAN'];

        $bankingInfo = "<m7s:BankingInformation>";
        $bankingInfo .= "<m7s:AccountName>{$accountName}</m7s:AccountName>";
        $bankingInfo .= "<m7s:BIC>{$bic}</m7s:BIC>";
        $bankingInfo .= "<m7s:IBAN>{$iban}</m7s:IBAN>";
        $bankingInfo .= "</m7s:BankingInformation>";
        return $bankingInfo;
    }

    function billingaddress($params): string
    {
        $city = $params['Billingaddress']['City'];
        $houseNumber = $params['Billingaddress']['HouseNumber'];
        $houseNumberExt = $params['Billingaddress']['HouseNumberExtension'];
        $municipality = $params['Billingaddress']['Municipality'];
        $postalCode = $params['Billingaddress']['PostalCode'];
        $state = $params['Billingaddress']['State'];
        $street = $params['Billingaddress']['Street'];

        return "<m7s:BillingAddress>" .
            "<m7s:City>{$city}</m7s:City>" .
            "<m7s:Country>NETHERLANDS</m7s:Country>" .
            "<m7s:HouseNumber>{$houseNumber}</m7s:HouseNumber>" .
            "<m7s:HouseNumberExtension>{$houseNumberExt}</m7s:HouseNumberExtension>" .
            "<m7s:Municipality>{$municipality}</m7s:Municipality>" .
            "<m7s:PostalCode>{$postalCode}</m7s:PostalCode>" .
            "<m7s:State>{$state}</m7s:State>" .
            "<m7s:Street>{$street}</m7s:Street>" .
            "</m7s:BillingAddress>";
    }

    function billingCustomerDetails($params): string
    {
        $dateOfBirth = "";
        if (isset($params['BillingCustomerDetails']['DateOfBirth'])) {
            $dateOfBirth = Carbon::parse($params['BillingCustomerDetails']['DateOfBirth'])
                ->format('Y-m-d');
        }
        $email = $params['BillingCustomerDetails']['Email'];
        $firstName = $params['BillingCustomerDetails']['Firstname'];
        $gender = $params['BillingCustomerDetails']['Gender'];
        $initials = $params['BillingCustomerDetails']['Initials'];
        $middleName = $params['BillingCustomerDetails']['Middlename'];
        $lastName = $params['BillingCustomerDetails']['Surname'];
        $mobile = $params['BillingCustomerDetails']['Mobile'];
        $phone = $params['BillingCustomerDetails']['Phone'];
        $title = $params['BillingCustomerDetails']['Title'];

        $billingCustomerDetails = "<m7s:BillingCustomerDetails>";
        $billingCustomerDetails .= "<m7s:DateOfBirth>{$dateOfBirth}</m7s:DateOfBirth>";
        $billingCustomerDetails .= "<m7s:Email>{$email}</m7s:Email>";
        $billingCustomerDetails .= "<m7s:FirstName>{$firstName}</m7s:FirstName>";
        $billingCustomerDetails .= "<m7s:Gender>{$gender}</m7s:Gender>";
        $billingCustomerDetails .= "<m7s:Initials>{$initials}</m7s:Initials>";
        $billingCustomerDetails .= "<m7s:MiddleName>{$middleName}</m7s:MiddleName>";
        $billingCustomerDetails .= "<m7s:Mobile>{$mobile}</m7s:Mobile>";
        $billingCustomerDetails .= "<m7s:Phone>{$phone}</m7s:Phone>";
        $billingCustomerDetails .= "<m7s:SurName>{$lastName}</m7s:SurName>";
        $billingCustomerDetails .= "<m7s:Title>{$title}</m7s:Title>";
        $billingCustomerDetails .= "</m7s:BillingCustomerDetails>";
        return $billingCustomerDetails;
    }

    function account($params): string
    {
        $customerNumber = $params['CustomerNumber'];
        $email = $params['Email'];
        $newPassword = $params['NewPassword'];
        $confirmPassword = $params['ConfirmPassword'];
        $oldPassword = $params['OldPassword'];

        $account = "<m7s:Company>" . config('m7.company') . "</m7s:Company>";
        $account .= "<m7s:CustomerNumber>{$customerNumber}</m7s:CustomerNumber>";
        $account .= "<m7s:DealerNumber>" . config('m7.dealer_code') . "</m7s:DealerNumber>";
        $account .= "<m7s:Email>{$email}</m7s:Email>";
        $account .= "<m7s:NewPassword>{$newPassword}</m7s:NewPassword>";
        $account .= "<m7s:ConfirmPassword>{$confirmPassword}</m7s:ConfirmPassword>";
        $account .= "<m7s:OldPassword>{$oldPassword}</m7s:OldPassword>";
        return $account;
    }

    function resellerProperties($params): array
    {
        return [
            'ResellerCustomerNr' => $params['ResellerCustomerNr'],
            'ResellerSolocooID' => $params['ResellerSolocooID'],
            'LineType' => $params['LineType'],
            'LineProfile' => $params['LineProfile'],
            'LineMinDownload' => $params['LineMinDownload'],
            'KpnPackageID' => $params['KpnPackageID'],
            'ContractNumber' => $params['ContractNumber'],
            'ContractPeriod' => $params['ContractPeriod'],
            'ContractStartDate' => $params['ContractStartDate'],
            'ContractEndDate' => $params['ContractEndDate'],
            'CustomerNumber' => $params['CustomerNumber'],
            'DealerNumber' => $params['DealerNumber']
        ];
    }

    function smartcardPackages($params): string
    {
        $mainSmartcard = '';
        if (isset($params['SmartcardPackagesDTO']['MainSmartcard'])) {
            $mainSmartcard = $params['SmartcardPackagesDTO']['MainSmartcard'];
        }

        if (isset($params['MainSmartcard'])) {
            $mainSmartcard = $params['MainSmartcard'];
        }

        $decoderNumber = $params['SmartcardPackagesDTO']['Decodernumber'];
        $campaignCode = $params['Productinfo']['Campaigncode'];
        $isAddOn = $params['Productinfo']['IsAddon'];
        $keywords = $params['Productinfo']['Keywords'];
        $productId = $params['Productinfo']['ProductID'];
        $smartCardNumber = $params['SmartcardPackagesDTO']['Smartcardnumber'];

        $smartcardPackages = "<m7s:SmartcardPackages>";
        $smartcardPackages .= "<m7s:SmartcardPackagesDTO>";
        $smartcardPackages .= "<m7s:DecoderNumber>{$decoderNumber}</m7s:DecoderNumber>";
        $smartcardPackages .= "<m7s:MainSmartcard>" . $mainSmartcard . "</m7s:MainSmartcard>";
        $smartcardPackages .= "<m7s:ProductInfo>";
        $smartcardPackages .= "<m7s:ProductInfo>";
        $smartcardPackages .= "<m7s:CampaignCode>{$campaignCode}</m7s:CampaignCode>";
        $smartcardPackages .= "<m7s:IsAddOn>{$isAddOn}</m7s:IsAddOn>";
        $smartcardPackages .= "<m7s:Keywords>{$keywords}</m7s:Keywords>";
        $smartcardPackages .= "<m7s:ProductID>{$productId}</m7s:ProductID>";
        $smartcardPackages .= "</m7s:ProductInfo>";
        $smartcardPackages .= "</m7s:ProductInfo>";
        $smartcardPackages .= "<m7s:SmartcardNumber>{$smartCardNumber}</m7s:SmartcardNumber>";
        $smartcardPackages .= "</m7s:SmartcardPackagesDTO>";
        $smartcardPackages .= "</m7s:SmartcardPackages>";
        return $smartcardPackages;
    }

    function authorization(): string
    {
        $authorization = "<m7s:Authorization>";
        $authorization .= "<m7s1:Internal>false</m7s1:Internal>";
        $authorization .= "<m7s1:Password>" . config('m7.password') . "</m7s1:Password>";
        $authorization .= "<m7s1:UserName>" . config('m7.username') . "</m7s1:UserName>";
        $authorization .= "</m7s:Authorization>";

        return $authorization;
    }

    function defaultRequest($params): string
    {
        $defaultRequest =
            "<m7s:Company>" . config('m7.company') . "</m7s:Company>" .
            "<m7s:ContractNumber>" . $params['ContractNumber'] . "</m7s:ContractNumber>" .
            "<m7s:ContractPeriod>" . $params['ContractPeriod'] . "</m7s:ContractPeriod>" .
            "<m7s:ContractStartDate>" . $params['ContractStartDate'] . "</m7s:ContractStartDate>" .
            "<m7s:CustomerNumber>" . $params['CustomerNumber'] . "</m7s:CustomerNumber>" .
            "<m7s:DealerNumber>" . config('m7.dealer_code') . "</m7s:DealerNumber>" .
            "<m7s:OptedForNewsletter>" . $params['OptedForNewsletter'] . "</m7s:OptedForNewsletter>";

        return $defaultRequest;
    }

    function defaultRequestEnd($params): string
    {
        $wishDate = isset($params['WishDate']) ? Carbon::parse($params['WishDate'])->format('Y-m-d') : '';
        $defaultRequestEnd = "<m7s:TransactionType>" . $params['TransactionType'] . "</m7s:TransactionType>";
        $defaultRequestEnd .= "<m7s:WishDate>" . $wishDate . "</m7s:WishDate>";

        return $defaultRequestEnd;
    }

    function m7GenerateRequest($method, $params): string
    {
        $rs = "<tem:{$method} xmlns:soap=\"http://www.w3.org/2003/05/soap-envelope\"";
        $rs .= "              xmlns:tem=\"http://tempuri.org/\"";
        $rs .= "              xmlns:m7s=\"http://schemas.datacontract.org/2004/07/M7Service.Models\"";
        $rs .= "              xmlns:m7s1=\"http://schemas.datacontract.org/2004/07/M7Service.Security\">";

        $authorization = authorization();

        switch ($method) {
            case 'CaptureSubscriber':
                $rs .= "<tem:customer>";
                $rs .= $authorization;
                $rs .= bankingInformation($params);
                $rs .= billingAddress($params);
                $rs .= billingCustomerDetails($params);
                $rs .= defaultRequest($params);
                $rs .= smartcardPackages($params);
                $rs .= defaultRequestEnd($params);
                $rs .= "</tem:customer>";
                break;

            case 'ChangePackage':
                $rs .= "<tem:customer>";
                $rs .= $authorization;
                $rs .= bankingInformation($params);
                $rs .= billingAddress($params);
                $rs .= billingCustomerDetails($params);
                $rs .= defaultRequest($params);
                $rs .= smartcardPackages($params);
                $rs .= defaultRequestEnd($params);
                $rs .= "</tem:customer>";
                break;

            case 'ChangeAddress':
                $rs .= "<tem:customer>";
                $rs .= $authorization;
                $rs .= bankingInformation($params);
                $rs .= billingAddress($params);
                $rs .= billingCustomerDetails($params);
                $rs .= defaultRequest($params);
                $rs .= smartcardPackages($params);
                $rs .= defaultRequestEnd($params);
                $rs .= "</tem:customer>";
                break;

            case 'SwopSmartcard':
                $rs .= "<tem:transaction>";
                $rs .= $authorization;
                $rs .= "<m7s:Company>" . config('m7.company') . "</m7s:Company>";
                $rs .= "<m7s:CustomerNumber>" . $params['CustomerNumber'] . "</m7s:CustomerNumber>";
                $rs .= "<m7s:DealerNumber>" . config('m7.dealer_code') . "</m7s:DealerNumber>";
                $rs .= "<m7s:Decodernumber>" . $params['Decodernumber'] . "</m7s:Decodernumber>";
                $rs .= "<m7s:Smartcardnumber>" . $params['Smartcardnumber'] . "</m7s:Smartcardnumber>";
                $rs .= "<m7s:OldDecodernumber>" . $params['OldDecodernumber'] . "</m7s:OldDecodernumber>";
                $rs .= "<m7s:OldSmartcardnumber>" . $params['OldSmartcardnumber'] . "</m7s:OldSmartcardnumber>";
                $rs .= defaultRequestEnd($params);
                $rs .= "</tem:transaction>";
                break;

            case 'ReAuthSmartcard':
                $rs .= "<tem:transaction>";
                $rs .= $authorization;
                $rs .= "<m7s:Company>" . config('m7.company') . "</m7s:Company>";
                $rs .= "<m7s:CustomerNumber>" . $params['CustomerNumber'] . "</m7s:CustomerNumber>";
                $rs .= "<m7s:DealerNumber>" . config('m7.dealer_code') . "</m7s:DealerNumber>";
                $rs .= "<m7s:Decodernumber>" . $params['Decodernumber'] . "</m7s:Decodernumber>";
                $rs .= "<m7s:Smartcardnumber>" . $params['Smartcardnumber'] . "</m7s:Smartcardnumber>";
                $rs .= "<m7s:OldDecodernumber>" . $params['OldDecodernumber'] . "</m7s:OldDecodernumber>";
                $rs .= "<m7s:OldSmartcardnumber>" . $params['OldSmartcardnumber'] . "</m7s:OldSmartcardnumber>";
                $rs .= defaultRequestEnd($params);
                $rs .= "</tem:transaction>";
                break;

            case 'CreateMyAccount':
                $rs .= "<tem:userInfo>";
                $rs .= $authorization;
                $rs .= account($params);
                $rs .= "</tem:userInfo>";
                break;

            case 'ChangeMyAccount':
                $rs .= "<tem:userInfo>";
                $rs .= $authorization;
                $rs .= "<m7s:ChangeMyAccount>";
                $rs .= account($params);
                $rs .= "</m7s:ChangeMyAccount>";
                $rs .= "</tem:userInfo>";
                break;

            case 'RemoveMyAccount':
                $rs .= "<tem:userInfo>";
                $rs .= $authorization;
                $rs .= account($params);
                $rs .= "</tem:userInfo>";
                break;

            case 'Disconnect':
                $contractNumber = $params['Disconnect']['ContractNumber'];
                $customerNumber = $params['Disconnect']['CustomerNumber'];
                $decoderNumber = $params['Disconnect']['Decodernumber'];
                $smartCardNumber = $params['Disconnect']['Smartcardnumber'];
                $oldDecoderNumber = $params['Disconnect']['OldDecodernumber'];
                $oldSmartCardNumber = $params['Disconnect']['OldSmartcardnumber'];

                $rs .= "<tem:transaction>";
                $rs .= $authorization;
                $rs .= "<m7s:Disconnect>";
                $rs .= "<m7s:Company>" . config('m7.company') . "</m7s:Company>";
                $rs .= "<m7s:ContractNumber>{$contractNumber}</m7s:ContractNumber>";
                $rs .= "<m7s:CustomerNumber>{$customerNumber}</m7s:CustomerNumber>";
                $rs .= "<m7s:DealerNumber>" . config('m7.dealer_code') . "</m7s:DealerNumber>";
                $rs .= "<m7s:Decodernumber>{$decoderNumber}</m7s:Decodernumber>";
                $rs .= "<m7s:Smartcardnumber>{$smartCardNumber}</m7s:Smartcardnumber>";
                $rs .= "<m7s:OldDecodernumber>{$oldDecoderNumber}</m7s:OldDecodernumber>";
                $rs .= "<m7s:OldSmartcardnumber>{$oldSmartCardNumber}</m7s:OldSmartcardnumber>";
                $rs .= "</m7s:Disconnect>";
                $rs .= defaultRequestEnd($params);
                $rs .= "</tem:transaction>";
                break;

            case 'Reconnect':
                $contractNumber = $params['Reconnect']['ContractNumber'];
                $customerNumber = $params['Reconnect']['CustomerNumber'];
                $decoderNumber = $params['Reconnect']['Decodernumber'];
                $smartCardNumber = $params['Reconnect']['Smartcardnumber'];
                $oldDecoderNumber = $params['Reconnect']['OldDecodernumber'];
                $oldSmartCardNumber = $params['Reconnect']['OldSmartcardnumber'];

                $rs .= "<tem:transaction>";
                $rs .= $authorization;
                $rs .= "<m7s:Disconnect>";
                $rs .= "<m7s:Company>" . config('m7.company') . "</m7s:Company>";
                $rs .= "<m7s:ContractNumber>{$contractNumber}</m7s:ContractNumber>";
                $rs .= "<m7s:CustomerNumber>{$customerNumber}</m7s:CustomerNumber>";
                $rs .= "<m7s:DealerNumber>" . config('m7.dealer_code') . "</m7s:DealerNumber>";
                $rs .= "<m7s:Decodernumber>{$decoderNumber}</m7s:Decodernumber>";
                $rs .= "<m7s:Smartcardnumber>{$smartCardNumber}</m7s:Smartcardnumber>";
                $rs .= "<m7s:OldDecodernumber>{$oldDecoderNumber}</m7s:OldDecodernumber>";
                $rs .= "<m7s:OldSmartcardnumber>{$oldSmartCardNumber}</m7s:OldSmartcardnumber>";
                $rs .= "</m7s:Disconnect>";
                $rs .= defaultRequestEnd($params);
                $rs .= "</tem:transaction>";
                break;

            case 'CloseAccount':
                $contractNumber = $params['CloseAccount']['ContractNumber'];
                $customerNumber = $params['CloseAccount']['CustomerNumber'];
                $decoderNumber = $params['CloseAccount']['Decodernumber'];
                $smartCardNumber = $params['CloseAccount']['Smartcardnumber'];
                $oldDecoderNumber = $params['CloseAccount']['OldDecodernumber'];
                $oldSmartCardNumber = $params['CloseAccount']['OldSmartcardnumber'];

                $rs .= "<tem:transaction>";
                $rs .= $authorization;
                $rs .= "<m7s:CloseAccount>";
                $rs .= "<m7s:Company>" . config('m7.company') . "</m7s:Company>";
                $rs .= "<m7s:ContractNumber>{$contractNumber}</m7s:ContractNumber>";
                $rs .= "<m7s:CustomerNumber>{$customerNumber}</m7s:CustomerNumber>";
                $rs .= "<m7s:DealerNumber>" . config('m7.dealer_code') . "</m7s:DealerNumber>";
                $rs .= "<m7s:Decodernumber>{$decoderNumber}</m7s:Decodernumber>";
                $rs .= "<m7s:Smartcardnumber>{$smartCardNumber}</m7s:Smartcardnumber>";
                $rs .= "<m7s:OldDecodernumber>{$oldDecoderNumber}</m7s:OldDecodernumber>";
                $rs .= "<m7s:OldSmartcardnumber>{$oldSmartCardNumber}</m7s:OldSmartcardnumber>";
                $rs .= "</m7s:CloseAccount>";
                $rs .= defaultRequestEnd($params);
                $rs .= "</tem:transaction>";
                break;

            case 'ResetPin':
                $contractNumber = $params['ResetPin']['ContractNumber'];
                $customerNumber = $params['ResetPin']['CustomerNumber'];
                $decoderNumber = $params['ResetPin']['Decodernumber'];
                $smartCardNumber = $params['ResetPin']['Smartcardnumber'];
                $oldDecoderNumber = $params['ResetPin']['OldDecodernumber'];
                $oldSmartCardNumber = $params['ResetPin']['OldSmartcardnumber'];

                $rs .= "<tem:transaction>";
                $rs .= $authorization;
                $rs .= "<m7s:CloseAccount>";
                $rs .= "<m7s:Company>" . config('m7.company') . "</m7s:Company>";
                $rs .= "<m7s:ContractNumber>{$contractNumber}</m7s:ContractNumber>";
                $rs .= "<m7s:CustomerNumber>{$customerNumber}</m7s:CustomerNumber>";
                $rs .= "<m7s:DealerNumber>" . config('m7.dealer_code') . "</m7s:DealerNumber>";
                $rs .= "<m7s:Decodernumber>{$decoderNumber}</m7s:Decodernumber>";
                $rs .= "<m7s:Smartcardnumber>{$smartCardNumber}</m7s:Smartcardnumber>";
                $rs .= "<m7s:OldDecodernumber>{$oldDecoderNumber}</m7s:OldDecodernumber>";
                $rs .= "<m7s:OldSmartcardnumber>{$oldSmartCardNumber}</m7s:OldSmartcardnumber>";
                $rs .= "</m7s:CloseAccount>";
                $rs .= defaultRequestEnd($params);
                $rs .= "</tem:transaction>";
                break;

            case 'UpdateTransactionStatus':
                $rs .= "<tem:transaction>";
                $rs .= $authorization;
                $rs .= "<m7s:Company>" . config('m7.company') . "</m7s:Company>";
                $rs .= "<m7s:CustomerNumber>" . $params['CustomerNumber'] . "</m7s:CustomerNumber>";
                $rs .= "<m7s:ReferenceNumber>" . $params['ReferenceNumber'] . "</m7s:ReferenceNumber>";
                $rs .= "<m7s:Result>" . $params['Result'] . "</m7s:Result>";
                $rs .= "<m7s:ResultDescription>" . $params['ResultDescription'] . "</m7s:ResultDescription>";
                $rs .= "<m7s:Soapmethod>" . $params['Soapmethod'] . "</m7s:Soapmethod>";
                $rs .= "<m7s:TransactionID>" . $params['TransactionID'] . "</m7s:TransactionID>";
                $rs .= "<m7s:XMLInfo>" . $params['XMLInfo'] . "</m7s:XMLInfo>";
                $rs .= "</tem:transaction>";
                break;

            case 'SetLineProperties':
                $rs .= "<tem:setLineProperties>";
                $rs .= $authorization;
                $rs .= "<m7s:ChannelListType>" . $params['ChannelListType'] . "</m7s:ChannelListType>";
                $rs .= "<m7s:CustomerNumber>" . $params['CustomerNumber'] . "</m7s:CustomerNumber>";
                $rs .= "<m7s:KpnPackageID>" . $params['KpnPackageID'] . "</m7s:KpnPackageID>";
                $rs .= "<m7s:LineMinDownload>" . $params['LineMinDownload'] . "</m7s:LineMinDownload>";
                $rs .= "<m7s:LineProfile>" . $params['LineProfile'] . "</m7s:LineProfile>";
                $rs .= "<m7s:LineType>" . $params['LineType'] . "</m7s:LineType>";
                $rs .= "</tem:setLineProperties>";
                break;

            case 'GetCustomerInfo':
                $rs .= "<tem:customer>";
                $rs .= $authorization;
                $rs .= "<m7s:Company>" . config('m7.company') . "</m7s:Company>";
                $rs .= "<m7s:CustomerNumber>" . $params['CustomerNumber'] . "</m7s:CustomerNumber>";
                $rs .= "</tem:customer>";
                break;
        }

        $rs .= "</tem:" . $method . ">";

        return $rs;
    }
}

if (!function_exists('currentTenant')) {
    /**
     * Gets the current tenant of the authorized user
     */
    function currentTenant($field = null): ?Tenant
    {
        $tenant_id = request()->header('tenant');
        if ($tenant_id) {
            $tenant = Tenant::find($tenant_id);
        } else {
            if (!Auth::check()) {
                return null;
            }

            $tenant = Tenant::find(Auth::user()->last_tenant_id);
        }

        if ($field) {
            return isset($tenant->{$field}) ? $tenant->{$field} : 0;
        }

        return $tenant;
    }
}

if (!function_exists('getDateDiff')) {

    /**
     * Get date diff using SQL DATEDIFF
     *
     * @param string $startDate
     * @param string $endDate
     * @return float
     *
     * return round(Carbon::parse("{$startDate} 00:00:00")->floatDiffInRealMonths("{$endDate} 23:59:59"), 1);
     */

    function getDateDiff($startDate, $endDate)
    {
        $sql = "SELECT GetDateDiff('{$startDate}', '{$endDate}') `dateDiff`";
        return floatval(DB::select($sql)[0]->dateDiff);
    }
}

if (!function_exists('getDateIntervals')) {
    function getDateIntervals($startDate, $endDate)
    {
        $sql = "SELECT TIMESTAMPDIFF(DAY, '{$startDate}', '{$endDate}') `interval`";
        return DB::select($sql)[0]->interval;
    }
}

if (!function_exists('getNumberRangeZeroPad')) {
    function getNumberRangeZeroPad($format): int
    {
        $withNumberFormatResult = [];
        $withNumberFormatMatch = preg_match("/\{\:\d{1,}number\}/", $format, $withNumberFormatResult);

        if ($withNumberFormatMatch) {
            $digitResults = [];
            $withNumberMatch = preg_match("/\d{1,}/", $withNumberFormatResult[0], $digitResults);
            return $withNumberMatch ? intval($digitResults[0]) : 0;
        }
        return 0;
    }
}

if (!function_exists('getNumberRangePrefixSuffix')) {
    function getNumberRangePrefixSuffix($format): array
    {
        $data = [
            'prefix' => null,
            'suffix' => null,
        ];

        $prefixResults = [];
        $prefixMatch = preg_match_all("/(.*)\{/", $format, $prefixResults);

        $suffixResults = [];
        $suffixMatch = preg_match_all("/\}(.*)/", $format, $suffixResults);

        $data['prefix'] = $prefixMatch ? $prefixResults[1][0] : null;
        $data['suffix'] = $suffixMatch ? $suffixResults[1][0] : null;

        return $data;
    }
}

if (!function_exists('getStringBladeView')) {
    function getStringBladeView($bladeString, $data): string
    {
        $data['__env'] = app(\Illuminate\View\Factory::class);
        $php = Blade::compileString($bladeString);

        $obLevel = ob_get_level();
        ob_start();
        extract($data, EXTR_SKIP);

        try {
            eval('?>' . $php);
        } catch (Exception $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }
            Logging::exception(
                $e,
                1,
                0
            );
            throw $e;
        } catch (Throwable $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }
            $newException = new Exception($e->getMessage());
            Logging::exception(
                $newException,
                17,
                0
            );
            throw $newException;
        }
        return ob_get_clean();
    }
}

if (!function_exists('getPriceIncVat')) {
    function getPriceIncVat($price, $tenantId, $productId): float
    {
        if ($price === null) {
            return (float) 0;
        }
        $vatCode = TenantProduct::getVatCode($tenantId, $productId);
        if ($vatCode) {
            $vatPercentage = 1 + $vatCode->vat_percentage;
        } else {
            $vatPercentage = 1 + Config::get('constants.options.default_vat_percentage');
        }
        return $price * $vatPercentage;
    }
}

if (!function_exists('jsonRecode')) {
    function jsonRecode($data): array
    {
        return json_decode(json_encode($data), true);
    }
}

if (!function_exists('filterArrayByKeys')) {
    /**
     *
     * @param array $inputArray
     * @param array $arrayKeyFilters
     * @return array
     */
    function filterArrayByKeys($inputArray, $arrayKeyFilters): array
    {
        $newArray = [];
        foreach ($arrayKeyFilters as $key) {
            if (array_key_exists($key, $inputArray)) {
                $newArray[$key] = $inputArray[$key];
            }
        }
        return $newArray;
    }
}

if (!function_exists('getE164PhoneNumber')) {
    function getE164PhoneNumber($phoneNumber, $countryCode = 'NL'): string
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        if ($phoneNumber[0] !== '+' && $phoneNumber[0] !== '0') {
            $phoneNumber = '+' . $phoneNumber;
            $numberProto = $phoneUtil->parse($phoneNumber);
            return $phoneUtil->format($numberProto, \libphonenumber\PhoneNumberFormat::E164);
        }

        $numberProto = $phoneUtil->parse($phoneNumber, $countryCode);
        return $phoneUtil->format($numberProto, \libphonenumber\PhoneNumberFormat::E164);
    }
}

if (!function_exists('getPhoneNumberCountryCode')) {
    function getPhoneNumberCountryCode($phoneNumber): string
    {
        $countryCode = null;
        try {
            $n = new PhoneNormalizer();
            $n->loadCodes('vendor/dmamontov/phone-normalizer/codes/codes.json');
            $countryName = $n->normalize($phoneNumber)->getCountryName();
            $countriesCodes = Config::get('constants.country_codes');
            $countryCode = $countriesCodes[$countryName];
        } catch (Exception $e) {
            $countryCode = null;
            Logging::exception(
                $e,
                1,
                0
            );
        }
        return $countryCode;
    }
}

if (!function_exists('getNormalizedPhoneNumber')) {
    function getNormalizedPhoneNumber($phoneNumber): ?string
    {
        try {
            $processingNumber = ltrim(rtrim($phoneNumber, "0"), "0");
            $len = strlen($processingNumber);
            if ($len < 12) {
                for ($i = 11; $i >= $len; $i--) {
                    $processingNumber = str_pad($processingNumber, $i, '0', STR_PAD_RIGHT);
                    $countryCode = getPhoneNumberCountryCode($processingNumber);
                    if (!is_null($countryCode)) {
                        break;
                    }
                }
            } else {
                $countryCode = getPhoneNumberCountryCode($processingNumber);
            }

            if (!is_null($countryCode)) {
                $phoneNumber = getE164PhoneNumber($processingNumber, $countryCode);
            }
            return $phoneNumber;
        } catch (Exception $e) {
            Logging::exceptionWithMessage(
                $e,
                "E164 Phone number EXCEPTION ",
                17,
                0
            );
            return $phoneNumber;
        }
    }
}

if (!function_exists('parseFloatGuess')) {
    function parseFloatGuess($rawValue): float
    {
        if (!$rawValue) {
            return 0;
        }

        $lastCommaPosition = strrpos($rawValue, ',');
        $lastDotPosition = strrpos($rawValue, '.');

        if ($lastDotPosition === false && $lastCommaPosition === false) {
            return floatval($rawValue);
        }

        if ($lastCommaPosition === 0 && $lastDotPosition === false) {
            $rawValue = str_replace(',', '.', $rawValue);
            return floatval($rawValue);
        }

        $decimalChar = ($lastCommaPosition > $lastDotPosition ? ',' : '.');
        $thousandsChar = ($decimalChar === '.' ? ',' : '.');

        if (substr_count($rawValue, ',') > 1) {
            $decimalChar = '.';
            $thousandsChar = ',';
        } elseif (substr_count($rawValue, '.') > 1) {
            $decimalChar = ',';
            $thousandsChar = '.';
        }

        $rawValue = str_replace($thousandsChar, '', $rawValue);
        $rawValue = str_replace($decimalChar, '.', $rawValue);

        return floatval($rawValue);
    }
}

if (!function_exists('dateFormat')) {
    function dateFormat($date, $format = 'Y-m-d'): ?string
    {
        return $date ? Carbon::parse($date)->format($format) : null;
    }
}

if (!function_exists('generateCSV')) {
    function generateCSV($fileName, $arrayColumnNames, $arrayContentData): string
    {
        $file = fopen($fileName, 'w');
        fputcsv($file, $arrayColumnNames);

        foreach ($arrayContentData as $data) {
            fputcsv($file, $data);
        }
        fclose($file);

        return $fileName;
    }
}

if (!function_exists('getMonthsByLocal')) {
    function getMonthsByLocal($month, $local = 'nl'): string
    {
        $list = [
            'nl' => [
                '01' => 'januari',
                '02' => 'februari',
                '03' => 'maart',
                '04' => 'april',
                '05' => 'mei',
                '06' => 'juni',
                '07' => 'juli',
                '08' => 'augustus',
                '09' => 'september',
                '10' => 'oktober',
                '11' => 'november',
                '12' => 'december'
            ]
        ];

        return $list[$local][$month];
    }
}

if (!function_exists('addToLaravelLog')) {
    function addToLaravelLog($tag = "", $data = []): void
    {
        \Illuminate\Support\Facades\Log::info($tag, $data);
    }
}

if (!function_exists('carbonNow')) {
    function carbonNow(): Carbon
    {
        return Carbon::now();
    }
}

if (!function_exists('carbonParse')) {
    function carbonParse($date): Carbon
    {
        return Carbon::parse($date);
    }
}

if (!function_exists('carbonAdd')) {
    function carbonAdd($date, $add, $type = 'day'): Carbon
    {
        return Carbon::parse($date)->add($add, $type);
    }
}

if (!function_exists('isExtTokenAuthorized')) {
    /**
     * Validate if bearerToken is == config('app.ext_token')
     *
     * @return bool
     */
    function isExtTokenAuthorized(): bool
    {
        $headerToken = request()->bearerToken();
        return ($headerToken == config('app.ext_token'));
    }
}

if (!function_exists('setEmailAddress')) {
    /**
     * Set email address based on app.env
     *
     * @param mixed $email
     * @return mixed
     */
    function setEmailAddress($email): string
    {
        if (config('app.env') != "production" && !preg_match("/@teleplaza.nl$/", $email)) {
            return preg_replace("/\@(.*)/", "@yopmail.com", $email);
        }

        return $email;
    }
}


if (!function_exists('isPortalRelationIdAuthorized')) {
    /**
     * Validate if requesting user is the same with session user_id
     *
     * @return bool
     */
    function isPortalRelationIdAuthorized($relationId): bool
    {
        $user = request()->user();
        return (!blank($user) && $user->person->relationsPerson->relation_id == $relationId);
    }
}

if (!function_exists("tofloat")) {
    /**
     * Creates a float from given number
     *
     * @param $num
     * @return float
     */
    function tofloat($num): float
    {
        $dotPos = strrpos($num, '.');
        $commaPos = strrpos($num, ',');
        $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
            ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);

        if (!$sep) {
            return floatval(preg_replace("/[^0-9]/", "", $num));
        }

        return floatval(
            preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
            preg_replace("/[^0-9]/", "", substr($num, $sep + 1, strlen($num)))
        );
    }
}

if (!function_exists("generateInvoiceDate")) {
    /**
     * @param int $salesInvoiceId
     * @param string $locale
     * @return string|null
     */
    function generateInvoiceDate(int $salesInvoiceId, $locale = 'nl'): ?string
    {
        $salesInvoice = SalesInvoice::find($salesInvoiceId);
        if (!isset($salesInvoice)) {
            return null;
        }
        $startDate = array_values(array_filter([
            $salesInvoice->periodicCostLines->min('invoice_start'),
            $salesInvoice->oneOffCostLines->min('invoice_start'),
            $salesInvoice->date
        ]))[0];
        if (!isset($startDate)) {
            return null;
        }

        $endDate = array_values(array_filter([
            $salesInvoice->periodicCostLines->max('invoice_stop'),
            $salesInvoice->oneOffCostLines->max('invoice_stop'),
            $salesInvoice->date
        ]))[0];

        $invoiceType = $salesInvoice->salesInvoiceLines()->whereIn('sales_invoice_line_type', [3, 4, 5])->max('sales_invoice_line_type');

        if (!isset($invoiceType)) {
            $invoiceType = 3;
        }
        switch ($invoiceType) {
            case 5: //YRC
                if (!isset($endDate)) {
                    return $startDate->format('Y');
                }
                if ($startDate->year !== $endDate->year) {
                    return $startDate->format('Y') . ' - ' . $endDate->format('Y');
                }
                return $startDate->format('Y');
            case 4: //QRC
                $QStart = 'Q' . intval(ceil($startDate->month / 3));
                if (!isset($endDate)) {
                    return $QStart . ' ' . $startDate->format('Y');
                }
                $QEnd = 'Q' . intval(ceil($endDate->month / 3));
                if ($startDate->year !== $endDate->year) {
                    return $QStart . ' ' . $startDate->format('Y') . ' - ' . $QEnd . ' ' . $endDate->format('Y');
                }
                if ($QStart !== $QEnd) {
                    return $QStart . ' - ' . $QEnd . ' ' . $endDate->format('Y');
                }
                return $QStart . ' ' . $startDate->format('Y');
            case 3: //MRC
            default:
                $startDate->locale($locale);
                if (!isset($endDate)) {
                    return $startDate->isoFormat('MMMM YYYY');
                }
                $endDate->locale($locale);
                if ($startDate->year !== $endDate->year) {
                    return $startDate->isoFormat('MMMM YYYY') . ' - ' . $endDate->isoFormat('MMMM YYYY');
                }
                if ($startDate->month !== $endDate->month) {
                    return $startDate->isoFormat('MMMM') . ' - ' . $endDate->isoFormat('MMMM YYYY');
                }
                return $endDate->isoFormat('MMMM YYYY');
        }
    }
}


function checkPalindrome($string)
{
    // $reversedString = "";
    // for ($i = strlen($string); $i > 0; $i--) {
    //     $reversedString .= $string[$i-1];
    // }
    // return $string === $reversedString;
    return $string === strrev($string);
}
