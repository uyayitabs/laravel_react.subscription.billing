<?php

$admins_emails = env('M7_ADMIN_EMAILS');
if ('' != $admins_emails) $admins_emails = explode(',', $admins_emails);

return [
    'url' => env('M7CLIP_URL', 'http://tst-services.m7group.eu/M7.CLIPService/M7Service.svc?wsdl'),
    'company' => env('M7CLIP_COMPANY', 'TELEPLAZA'),
    'username' => env('M7CLIP_USERNAME', 'TELEPLAZA'),
    'password' => env('M7CLIP_PASSWORD', 'e0b15861-b6c1-44aa-9d1e-885d168df6d9'),
    'dealer_code' => env('M7CLIP_DEALER_CODE', 'TELEPLA'),
    'market_segment' => env('M7CLIP_MARKET_SEGMENT', 'TELEPLA'),
    'account_type' => env('M7CLIP_ACCOUNT_TYPE', 'P'),
    'solocoo_reseller_code' => env('M7CLIP_SOLOCOO_RESELLER_CODE', 'cds-tpl'),
    'admins_email' => $admins_emails,
    'enable_deprovisioning_report_email_cron' => env('ENABLE_M7_DEPROVISIONING_EMAIL_CRON', false),
    'deprovisioning_email_cron_to_recipients' => env('M7_DEPROVISIONING_EMAIL_CRON_TO_RECIPIENTS', 'info@fiber.nl'),
    'deprovisioning_email_cron_cc_recipients' => env('M7_DEPROVISIONING_EMAIL_CRON_CC_RECIPIENTS', 'brigitte@xsprovider.nl,dennis.peters@xsprovider.nl, joost.hink@xsprovider.nl,marjolein@teleplaza.nl'),
    'deprovisioning_email_cron_bcc_recipients' => env('M7_DEPROVISIONING_EMAIL_CRON_BCC_RECIPIENTS', 'n.wegen@f2x.nl'),
];
