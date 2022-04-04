<?php

$admins_emails = env('RABOBANK_ADMIN_EMAILS');
if ('' != $admins_emails) $admins_emails = explode(',', $admins_emails);

return [
    'admins_email' => $admins_emails,
    'enable_rabobank_cron' => env('ENABLE_CRON_RABOBANK', false),
    'enable_payments_report_email_cron' => env('ENABLE_PAYMENTS_REPORT_EMAIL_CRON', false),
    'payments_report_email_cron_to_recipients' => env('PAYMENTS_REPORT_EMAIL_CRON_TO_RECIPIENTS', 'marjolein@teleplaza.nl,brigitte@xsprovider.nl,martijn.schafstad@xsprovider.nl'),
    'payments_report_email_cron_cc_recipients' => env('PAYMENTS_REPORT_EMAIL_CRON_CC_RECIPIENTS', null),
    'payments_report_email_cron_bcc_recipients' => env('PAYMENTS_REPORT_EMAIL_CRON_BCC_RECIPIENTS', 'n.wegen@f2x.nl'),
    'enable_no_bank_account_dd_report_email_cron' => env('ENABLE_NO_BANK_ACCOUNT_DD_REPORT_EMAIL_CRON', false),
    'no_bank_account_dd_report_email_cron_to_recipients' => env('NO_BANK_ACCOUNT_DD_REPORT_EMAIL_CRON_TO_RECIPIENTS', 'n.wegen@f2x.nl'),
];
