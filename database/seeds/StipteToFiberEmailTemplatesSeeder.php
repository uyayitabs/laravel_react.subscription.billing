<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StipteToFiberEmailTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = array(
            array(
                'id' => 4,
                'tenant_id' => 8,
                'product_id' => NULL,
                'type' => 'invoice',
                'from_name' => 'Fiber Nederland',
                'from_email' => 'noreply@fiber.nl',
                'bcc_email' => 'mark@f2x.nl,cdmigrations@xsprovider.nl,marisa.vanvelzen@xsprovider.nl',
                'subject' => '{{ $totalWithVAT < 0 ? "Creditfactuur " : "Factuur " }}van Fiber NL voor de maand {{$date}}',
                'body_html' => '<!DOCTYPE html>
<html>

<head>
<title>Fiber factuur</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!--[if mso]>   <style type=”text/css”>   .fallback-text {   font-family: Arial, sans-serif;   }   </style>   <![endif]-->
<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans" />
<style>
@font-face {
font-family: "myriadbold";
src: url("{{config(\'app.asset_url\')}}/fonts/MyriadBold.woff2") format("woff2"), url("{{config(\'app.asset_url\')}}/fonts/MyriadBold.woff") format("woff"), url("{{config(\'app.asset_url\')}}/fonts/MyriadBold.otf") format("opentype");
font-display: auto;d
font-style: normal;
font-weight: normal;
}

@font-face {
font-family: "myriadreg";
src: url("{{config(\'app.asset_url\')}}/fonts/MyriadReg.woff2") format("woff2"), url("{{config(\'app.asset_url\')}}/fonts/MyriadReg.woff") format("woff"), url("{{config(\'app.asset_url\')}}/fonts/MyriadReg.otf") format("opentype");
font-display: auto;
font-style: normal;
font-weight: normal;
}
</style>
<style>
* {
padding: 0;
margin: 0;
font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important;
color: #292D2F;
}

body {
font-size: 11.25pt;
width: 100%;
}

h1,
h2,
h3,
h4,
h5,
h6,
table thead th,
table thead td,
strong {
font-family: \'myriadbold\', \'Open Sans\', Arial, sans-serif !important;
}

h1 {
font-size: 15.25pt;
}

h2 {
font-size: 14.25pt;
}

h3 {
font-size: 13.25pt;
margin: 10pt 0;
}

h4 {
font-size: 12.25pt;
}

h5 {
font-size: 11.25pt;
}

table {
width: 100%;
}

p {
margin-bottom: 16px;
font-size: 11.25pt;
letter-spacing: .35px;
line-height: 1;
}

.page-wrapper {
background: #FFFFFF;
margin: 0 !important;
padding: 0 32px !important;
width: 960px !important;
}

.header {
margin: 16px 0;
padding-bottom: 16px;
border-bottom: 1px dashed #74D1F6;
}

.header-info {
font-size: 9.25pt;
vertical-align: bottom;
text-align: right;
}

.header-info img {
margin-left: 16px;
margin-right: 5px;
height: 18px;
vertical-align: top;
}

.header-info span {
line-height: 14pt;
padding-top: 3px;
padding-bottom: 3px;
vertical-align: top;
}

.content {
padding: 16px 0;
}

.content .note {
color: #5D6162 !important;
font-size: 8.25pt !important;
}

.content .note-count {
display: inline-block;
vertical-align: middle;
float: none;
padding: 5px 10px;
line-height: 1;
color: #FFFFFF;
text-align: center;
background: #939598;
}

.footer-row {
font-size: 9pt !important;
text-align: center;
}

.footer-column {
padding-top: 10pt;
padding-bottom: 10pt;
color: #8E9192 !important;
border-top: 1px dashed #74D1F6;
}
</style>
</head>

<body>
<table class="page-wrapper">
<thead>
<tr>
<td>
<table class="header">
<thead>
<tr>
<td style="text-align: left; vertical-align: bottom;"> <img src="{{config(\'app.asset_url\')}}/images/logo.72.png?v={{Str::random(6)}}" alt="" style="width: 115px;"> </td>
<td class="header-info"> <img src="{{config(\'app.asset_url\')}}/images/phone.72.png?v={{Str::random(6)}}"><span>020-760 50 40</span> <img src="{{config(\'app.asset_url\')}}/images/mail.72.png?v={{Str::random(6)}}"><span>info@fiber.nl</span> <img src="{{config(\'app.asset_url\')}}/images/bookmark.72.png?v={{Str::random(6)}}"><span>www.fiber.nl</span> </td>
</tr>
</thead>
</table>
</td>
</tr>
</thead>
<tbody>
<tr>
<td class="content">
<p>Beste {{$user_fullname}},</p>
<p>Bij deze ontvangt u de factuur voor uw diensten bij Fiber NL voor de maand <strong>{{isoFormatCarbonDate($invoice_due_date, \'MMMM Y\', false)}}</strong>.</p>
<p>In het bijgesloten PDF document kunt u de details vinden, met specificatie en toelichting.</p>
<p>Heeft u nog vragen over uw factuur? Mail ons dan op <a href="mailto:info@fiber.nl">info@fiber.nl</a> of bel naar 020 760 50 40.</p>
<p>Met vriendelijke groet,</p>
<p>De klantenservice van Fiber Nederland</p>
<p class="note">PDF documenten kunt u lezen met de gratis Acrobat Reader, die u kunt downloaden op <a href="https://get.adobe.com/nl/reader" _target="blank">adobe.nl</a>.</p>
</td>
</tr>
</tbody>
<tfoot>
<tr class="footer-row">
<td class="footer-column">Postadres: Postbus 60228, 1320AG Almere | Bezoekadres: Transistorstraat 7, 1322CJ Almere | KvK-nummer: 73786950 | BTW-nummer: NL859664016B01</td>
</tr>
</tfoot>
</table>
</body>

</html>',
                'created_at' => NULL,
                'updated_at' => '2020-12-23 04:05:46',
            ),
            array(
                'id' => 2,
                'tenant_id' => 8,
                'product_id' => NULL,
                'type' => 'm7.create_my_account',
                'from_name' => 'Fiber Nederland',
                'from_email' => 'noreply@fiber.nl',
                'bcc_email' => 'mark@f2x.nl,cdmigrations@xsprovider.nl,marisa.vanvelzen@xsprovider.nl',
                'subject' => 'Uw login gegevens voor Canal Digitaal',
                'body_html' => '<!doctype html>
<html style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F;">

<head style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F;">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F;"> Mail van {{ $tenant }}</Title>
<meta name="viewport" content="width=device-width, initial-scale=1" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F;">
<!--[if mso]> 	<style type=&rdquo;text/css&rdquo;> 		.fallback-text { 			font-family: Arial, sans-serif; 		} 	</style> 	<![endif]-->
<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
<style>
@font-face {
font-family: "myriadbold";
src: url("{{$asset_url}}/fonts/MyriadBold.woff2") format("woff2"),
url("{{$asset_url}}/fonts/MyriadBold.woff") format("woff"),
url("{{$asset_url}}/fonts/MyriadBold.otf") format("opentype");
font-display: auto;
font-style: normal;
font-weight: normal;
}

@font-face {
font-family: "myriadreg";
src: url("{{$asset_url}}/fonts/MyriadReg.woff2") format("woff2"),
url("{{$asset_url}}/fonts/MyriadReg.woff") format("woff"),
url("{{$asset_url}}/fonts/MyriadReg.otf") format("opentype");
font-display: auto;
font-style: normal;
font-weight: normal;
}
</Style>
<style>
* {
padding: 0;
margin: 0;
font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important;
color: #292D2F;
}

body {
font-size: 11.25pt;
width: 100%;
}

h1,
h2,
h3,
h4,
h5,
h6,
table thead th,
table thead td,
strong {
font-family: \'myriadbold\', \'Open Sans\', Arial, sans-serif !important;
}

h1 {
font-size: 15.25pt;
}

h2 {
font-size: 14.25pt;
}

h3 {
font-size: 14px;
margin: 10pt 0;
}

h4 {
font-size: 12.25pt;
}

h5 {
font-size: 11.25pt;
}

table {
width: 100%;
}

p {
margin-bottom: 10px;
font-size: 11.25pt;
letter-spacing: .35px;
line-height: 18px;
}

.page-wrapper {
background: #FFFFFF;
margin: 0 !important;
padding: 0 32px !important;
width: 960px !important;
}

.header {
margin: 16px 0;
padding-bottom: 16px;
/*border-bottom: 1px dashed #74D1F6;*/
}

.header-info {
font-size: 9.25pt;
vertical-align: bottom;
text-align: right;
}

.header-info img {
margin-left: 16px;
margin-right: 5px;
height: 18px;
vertical-align: top;
}

.header-info span {
line-height: 14pt;
padding-top: 3px;
padding-bottom: 3px;
vertical-align: top;
}

.content {
padding: 16px 0;
}

.content .note {
color: #5D6162 !important;
font-size: 8.25pt !important;
}

.content .note-count {
display: inline-block;
vertical-align: middle;
float: none;
padding: 5px 10px;
line-height: 22px;
color: #FFFFFF;
text-align: center;
background: #939598;
}

.footer-row {
font-size: 10pt !important;
text-align: center;
}

.footer-column {
padding-top: 10pt;
padding-bottom: 10pt;
color: #8E9192 !important;
border-top: 1px dashed #74D1F6;
}
</Style>
</head>

<body style="">
<div style="border-bottom: 4px dotted #74D1F6; background-color: #fff">
<div style="box-sizing: border-box; width: 600px !important; margin: 0 auto; padding: 5px 32px; font-family: \'myriadbold\', \'Open Sans\', Arial, sans-serif; color: #74D1F6;"> <img src="{{ $asset_url }}/fiber/logo.72.png?v={{Str::random(6)}}" alt="" style="max-width: 100%; width: 150px;"> </div>
</div>
<table class="page-wrapper" style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; background: #FFFFFF; margin: 0 auto !important; padding: 0px 32px !important; width: 600px !important; ">
<tbody style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box;">
<tr style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F;">
<td class="content" style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; margin: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; padding: 16px 0;">
<h1 style="font-size: 28px; color: #74D1F6; padding-bottom: 32px;">Uw TV pakket is klaar voor gebruik</h1>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;"> Beste {{ $user_fullname }},</p>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;"> Fiber NL levert TV van Canal Digitaal over internet. Deze dienst is zojuist voor u geactiveerd.</p>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;"> Let even op de volgende zaken, dan kunt u optimaal hiervan gebruik maken.</p>
<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">               Aansluiting van uw TV op de Settop Box             </h3>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;"> De Settop Box moet met een netwerkkabel (UTP) aangesloten zijn op Fiber wifi router, en met een HDMI kabel op uw TV. U kunt maximaal 5 Settop Boxen in uw woning aansluiten. </p>
<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">               Canal Digitaal TV App voor smartphone en tablet             </h3>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;"> U kunt binnen en buitenshuis in de hele EU gebruikmaken van de Canal Digitaal TV App op uw smartphone of tablet. Hierbij heeft u toegang tot een groot deel van de zenders. U kunt ook live TV kijken, uw opnames plannen en bekijken, of streamen naar een Google Chromecast. Handig toch? </p>
<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">               Installeer de app in een handomdraai             </h3>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;"> Ga naar de <a href="https://itunes.apple.com/nl/app/online.nl/id1008027388?mt=8">App Store</a> (voor iOS) of <a href="https://play.google.com/store/apps/details?id=nl.streamgroup.canaldigital&hl=nl">Google Play</a> (voor Android), en installeer de Canal Digitaal TV App op uw telefoon of tablet.
<br /> Open de Canal Digitaal TV App, en log hiermee in:
<br /> </p>
<table style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
<tr>
<td style="width: 150px;">Gebruikersnaam</td>
<td>{{ $email }}</td>
</tr>
<tr>
<td style="width: 150px;">Wachtwoord</td>
<td>{{ $password }}</td>
</tr>
</table>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;"> Bewaar deze gegevens goed. </p>
<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">               TV kijken via PC             </h3>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;"> U kunt ook via uw PC of laptop TV kijken binnen de hele EU, door in te loggen met dezelfde inloggegevens als hierboven. Ga hiervoor naar <a href="https://login.canaldigitaal.nl/authenticate?redirect_uri=https%3A%2F%2Flivetv.canaldigitaal.nl%2Fauth.aspx&response_type=code&scope=TVE&client_id=StreamGroup">https://livetv.canaldigitaal.nl/</a>. </p>
<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">               TV kijken op uw Smart TV             </h3>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;"> Ook sommige Smart TV’s zijn geschikt om zonder settop box TV te kijken via een app op de Smart TV. Ook hiervoor kunt u bovenstaande inloggevens gebruiken. Weten of uw TV hiervoor geschikt is? <a href="https://www.canaldigitaal.nl/klantenservice/alles-over/canal-digitaal-tv-app/canal-digitaal-smart-tv-app/geschikte-smart-tvs/">Bekijk dan deze lijst</a>. </p>
<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">               Heeft u nog vragen, of lukt het inloggen niet?             </h3>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;"> We helpen u graag verder. U kunt ons bellen op 020 – 760 50 40 van maandag tot en met zaterdag van 09:00 tot 17:30. U kunt ook een email sturen aan <a href="mailto:info@fiber.nl">info@fiber.nl</a>. </p>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;"> Met vriendelijk groet,
<br />
<br /> Fiber Nederland </p>
</td>
</tr>
</tbody>
<tfoot style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box;">
<tr class="footer-row" style="text-align: center;">
<td style="padding: 0; margin: 0; color: #292D2F; box-sizing: border-box; font-family: \'myriadbold\', \'Open Sans\', Arial, sans-serif !important; font-size: 9.25pt; vertical-align: bottom; text-align: right;"> </td>
</tr>
<tr class="footer-row" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; font-size: 9pt !important; text-align: center;">
<td class="footer-column" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; padding-top: 10pt; padding-bottom: 10pt; color: #5C5E5E !important; border-top: 1px dashed #74D1F6; line-height: 22px;">
<br /> <img src="{{ $asset_url }}/fiber/phone.72.png?v={{Str::random(6)}}" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; max-width: 100%; margin-left: 16px; margin-right: 5px; height: 18px; vertical-align: top;"><span style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; line-height: 14pt; padding-top: 3px; padding-bottom: 3px; vertical-align: top;">020-760 50 40</Span> <img src="{{ $asset_url }}/fiber/mail.72.png?v={{Str::random(6)}}" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; max-width: 100%; margin-left: 16px; margin-right: 5px; height: 18px; vertical-align: top;"><span style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; line-height: 14pt; padding-top: 3px; padding-bottom: 3px; vertical-align: top;">info@fiber.nl</Span> <img src="{{ $asset_url }}/fiber/bookmark.72.png?v={{Str::random(6)}}" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; max-width: 100%; margin-left: 16px; margin-right: 5px; height: 18px; vertical-align: top;"><span style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; line-height: 14pt; padding-top: 3px; padding-bottom: 3px; vertical-align: top;">www.fiber.nl</Span>
<br />
<br /> Postadres: Postbus 60228, 1320AG Almere | Bezoekadres: Transistorstraat 7, 1322CJ Almere
<br /> KvK-nummer: 73786950 | BTW-nummer: NL859664016B01
<br />
<br /> </td>
</tr>
</tfoot>
</table>
</body>

</html>',
                'created_at' => NULL,
                'updated_at' => '2020-12-23 04:05:46',
            ),
            array(
                'id' => 11,
                'tenant_id' => 8,
                'product_id' => NULL,
                'type' => 'cdr_summary',
                'from_name' => 'Fiber Nederland',
                'from_email' => 'noreply@fiber.nl',
                'bcc_email' => 'mark@f2x.nl,cdmigrations@xsprovider.nl,marisa.vanvelzen@xsprovider.nl',
                'subject' => 'Gespreksspecificatie bij uw factuur van Fiber NL voor de maand {{$date}}',
                'body_html' => '<!DOCTYPE html>
<html>
<head>
<title>Fiber factuur</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!--[if mso]>   <style type=”text/css”>   .fallback-text {   font-family: Arial, sans-serif;   }   </style>   <![endif]-->
<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans" />
<style>
@font-face { font-family: "myriadbold"; src: url("{{config(\'app.asset_url\')}}/fonts/MyriadBold.woff2") format("woff2"), url("{{config(\'app.asset_url\')}}/fonts/MyriadBold.woff") format("woff"), url("{{config(\'app.asset_url\')}}/fonts/MyriadBold.otf") format("opentype"); font-display: auto;d font-style: normal; font-weight: normal; }  @font-face { font-family: "myriadreg"; src: url("{{config(\'app.asset_url\')}}/fonts/MyriadReg.woff2") format("woff2"), url("{{config(\'app.asset_url\')}}/fonts/MyriadReg.woff") format("woff"), url("{{config(\'app.asset_url\')}}/fonts/MyriadReg.otf") format("opentype"); font-display: auto; font-style: normal; font-weight: normal; }
</style>
<style>
* {
padding: 0;
margin: 0;
font-family: "myriadreg", "Open Sans", Arial, sans-serif !important;
color: #292d2f;
}
body {
font-size: 11.25pt;
width: 100%;
}
h1,
h2,
h3,
h4,
h5,
h6,
table thead th,
table thead td,
strong {
font-family: "myriadbold", "Open Sans", Arial, sans-serif !important;
}
h1 {
font-size: 15.25pt;
}
h2 {
font-size: 14.25pt;
}
h3 {
font-size: 13.25pt;
margin: 10pt 0;
}
h4 {
font-size: 12.25pt;
}
h5 {
font-size: 11.25pt;
}
table {
width: 100%;
}
p {
margin-bottom: 16px;
font-size: 11.25pt;
letter-spacing: 0.35px;
line-height: 1;
}
.page-wrapper {
background: #ffffff;
margin: 0 !important;
padding: 0 32px !important;
width: 960px !important;
}
.header {
margin: 16px 0;
padding-bottom: 16px;
border-bottom: 1px dashed #74d1f6;
}
.header-info {
font-size: 9.25pt;
vertical-align: bottom;
text-align: right;
}
.header-info img {
margin-left: 16px;
margin-right: 5px;
height: 18px;
vertical-align: top;
}
.header-info span {
line-height: 14pt;
padding-top: 3px;
padding-bottom: 3px;
vertical-align: top;
}
.content {
padding: 16px 0;
}
.content .note {
color: #5d6162 !important;
font-size: 8.25pt !important;
}
.content .note-count {
display: inline-block;
vertical-align: middle;
float: none;
padding: 5px 10px;
line-height: 1;
color: #ffffff;
text-align: center;
background: #939598;
}
.footer-row {
font-size: 9pt !important;
text-align: center;
}
.footer-column {
padding-top: 10pt;
padding-bottom: 10pt;
color: #8e9192 !important;
border-top: 1px dashed #74d1f6;
}
</style>
</head>
<body>
<table class="page-wrapper">
<thead>
<tr>
<td>
<table class="header">
<thead>
<tr>
<td style="text-align: left; vertical-align: bottom;"><img src="{{config(\'app.asset_url\')}}/images/logo.72.png?v={{Str::random(6)}}" alt="" style="width: 115px;" /></td>
<td class="header-info">
<img src="{{config(\'app.asset_url\')}}/images/phone.72.png?v={{Str::random(6)}}" /><span>020-760 50 40</span> <img src="{{config(\'app.asset_url\')}}/images/mail.72.png?v={{Str::random(6)}}" />
<span>info@fiber.nl</span> <img src="{{config(\'app.asset_url\')}}/images/bookmark.72.png?v={{Str::random(6)}}" /><span>www.fiber.nl</span>
</td>
</tr>
</thead>
</table>
</td>
</tr>
</thead>
<tbody>
<tr>
<td class="content">
<p>Beste {{$user_fullname}},</p>
<p>Bij deze ontvangt u de gespreksspecificatie bij de factuur van Fiber NL voor de maand <strong>{{$invoice_due_date}}</strong>.</p>
<p>In het bijgesloten PDF document kunt u de specificatie vinden.</p>
<p>Heeft u nog vragen hierover? Mail ons dan op <a href="mailto:info@fiber.nl">info@fiber.nl</a> of bel naar 020 760 50 40.</p>
<p>Met vriendelijke groet,</p>
<p>De klantenservice van Fiber Nederland</p>
<p class="note">PDF documenten kunt u lezen met de gratis Acrobat Reader, die u kunt downloaden op <a href="https://get.adobe.com/nl/reader" _target="blank">adobe.nl</a>.</p>
</td>
</tr>
</tbody>
<tfoot>
<tr class="footer-row">
<td class="footer-column">Postadres: Postbus 60228, 1320AG Almere | Bezoekadres: Transistorstraat 7, 1322CJ Almere | KvK-nummer: 73786950 | BTW-nummer: NL859664016B01</td>
</tr>
</tfoot>
</table>
</body>
</html>
',
                'created_at' => '2020-06-11 06:00:00',
                'updated_at' => '2020-12-23 04:05:46',
            ),
            array(
                'id' => 13,
                'tenant_id' => 8,
                'product_id' => NULL,
                'type' => 'deposit_invoice',
                'from_name' => 'Fiber Nederland',
                'from_email' => 'noreply@fiber.nl',
                'bcc_email' => 'mark@f2x.nl,cdmigrations@xsprovider.nl,marisa.vanvelzen@xsprovider.nl',
                'subject' => 'Factuur van Fiber NL voor de borg van uw apparatuur',
                'body_html' => '<!DOCTYPE html>
<html>

<head>
<title>Fiber factuur</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!--[if mso]>   <style type=”text/css”>   .fallback-text {   font-family: Arial, sans-serif;   }   </style>   <![endif]-->
<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans" />
<style>
@font-face {
font-family: "myriadbold";
src: url("{{config(\'app.asset_url\')}}/fonts/MyriadBold.woff2") format("woff2"), url("{{config(\'app.asset_url\')}}/fonts/MyriadBold.woff") format("woff"), url("{{config(\'app.asset_url\')}}/fonts/MyriadBold.otf") format("opentype");
font-display: auto;d
font-style: normal;
font-weight: normal;
}

@font-face {
font-family: "myriadreg";
src: url("{{config(\'app.asset_url\')}}/fonts/MyriadReg.woff2") format("woff2"), url("{{config(\'app.asset_url\')}}/fonts/MyriadReg.woff") format("woff"), url("{{config(\'app.asset_url\')}}/fonts/MyriadReg.otf") format("opentype");
font-display: auto;
font-style: normal;
font-weight: normal;
}
</style>
<style>
* {
padding: 0;
margin: 0;
font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important;
color: #292D2F;
}

body {
font-size: 11.25pt;
width: 100%;
}

h1,
h2,
h3,
h4,
h5,
h6,
table thead th,
table thead td,
strong {
font-family: \'myriadbold\', \'Open Sans\', Arial, sans-serif !important;
}

h1 {
font-size: 15.25pt;
}

h2 {
font-size: 14.25pt;
}

h3 {
font-size: 13.25pt;
margin: 10pt 0;
}

h4 {
font-size: 12.25pt;
}

h5 {
font-size: 11.25pt;
}

table {
width: 100%;
}

p {
margin-bottom: 16px;
font-size: 11.25pt;
letter-spacing: .35px;
line-height: 1;
}

.page-wrapper {
background: #FFFFFF;
margin: 0 !important;
padding: 0 32px !important;
width: 960px !important;
}

.header {
margin: 16px 0;
padding-bottom: 16px;
border-bottom: 1px dashed #74D1F6;
}

.header-info {
font-size: 9.25pt;
vertical-align: bottom;
text-align: right;
}

.header-info img {
margin-left: 16px;
margin-right: 5px;
height: 18px;
vertical-align: top;
}

.header-info span {
line-height: 14pt;
padding-top: 3px;
padding-bottom: 3px;
vertical-align: top;
}

.content {
padding: 16px 0;
}

.content .note {
color: #5D6162 !important;
font-size: 8.25pt !important;
}

.content .note-count {
display: inline-block;
vertical-align: middle;
float: none;
padding: 5px 10px;
line-height: 1;
color: #FFFFFF;
text-align: center;
background: #939598;
}

.footer-row {
font-size: 9pt !important;
text-align: center;
}

.footer-column {
padding-top: 10pt;
padding-bottom: 10pt;
color: #8E9192 !important;
border-top: 1px dashed #74D1F6;
}
</style>
</head>

<body>
<table class="page-wrapper">
<thead>
<tr>
<td>
<table class="header">
<thead>
<tr>
<td style="text-align: left; vertical-align: bottom;"> <img src="{{config(\'app.asset_url\')}}/images/logo.72.png?v={{Str::random(6)}}" alt="" style="width: 115px;"> </td>
<td class="header-info"> <img src="{{config(\'app.asset_url\')}}/images/phone.72.png?v={{Str::random(6)}}"><span>020-760 50 40</span> <img src="{{config(\'app.asset_url\')}}/images/mail.72.png?v={{Str::random(6)}}"><span>info@fiber.nl</span> <img src="{{config(\'app.asset_url\')}}/images/bookmark.72.png?v={{Str::random(6)}}"><span>www.fiber.nl</span> </td>
</tr>
</thead>
</table>
</td>
</tr>
</thead>
<tbody>
<tr>
<td class="content">
<p>Beste {{$user_fullname}},</p>
<p>Bij deze ontvangt u de factuur voor de borg van de apparatuur die u van Fiber NL heeft ontvangen of binnenkort zult ontvangen.</p>
<p>Bij beëindiging van uw abonnement krijgt u deze borg weer terug als u de apparatuur weer inlevert.</p>
<p>In het bijgesloten PDF document kunt u de details vinden.</p>
<p>Heeft u nog vragen over deze factuur? Mail ons dan op <a href="mailto:info@fiber.nl">info@fiber.nl</a> of bel naar 020 760 50 40.</p>
<p>Met vriendelijke groet,</p>
<p>De klantenservice van Fiber Nederland</p>
<p class="note">PDF documenten kunt u lezen met de gratis Acrobat Reader, die u kunt downloaden op <a href="https://get.adobe.com/nl/reader" _target="blank">adobe.nl</a>.</p>
</td>
</tr>
</tbody>
<tfoot>
<tr class="footer-row">
<td class="footer-column">Postadres: Postbus 60228, 1320AG Almere | Bezoekadres: Transistorstraat 7, 1322CJ Almere | KvK-nummer: 73786950 | BTW-nummer: NL859664016B01</td>
</tr>
</tfoot>
</table>
</body>

</html>',
                'created_at' => NULL,
                'updated_at' => '2020-12-23 04:05:46',
            ),
            array(
                'id' => 22,
                'tenant_id' => 8,
                'product_id' => NULL,
                'type' => 'sales_invoice.first_reminder',
                'from_name' => 'Fiber Nederland',
                'from_email' => 'noreply@fiber.nl',
                'bcc_email' => 'mark@f2x.nl,cdmigrations@xsprovider.nl,marisa.vanvelzen@xsprovider.nl',
                'subject' => 'Herinnering: Uw Fiber NL factuur {{$invoice_number}} staat nog open',
                'body_html' => '<!doctype html><html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"><head> <!--[if gte mso 15]> <xml> <o:OfficeDocumentSettings> <o:AllowPNG/> <o:PixelsPerInch>96</o:PixelsPerInch> </o:OfficeDocumentSettings> </xml> <![endif]--><meta charset="UTF-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{$subject}}</title><style>@font-face{font-family:"myriadbold";src:url("{{config(\'app.asset_url\')}}/fonts/MyriadBold.woff2") format("woff2"),url("{{config(\'app.asset_url\')}}/fonts/MyriadBold.woff") format("woff"),url("{{config(\'app.asset_url\')}}/fonts/MyriadBold.otf") format("opentype");font-display:auto;font-style:normal;font-weight:normal}@font-face{font-family:"myriadreg";src:url("{{config(\'app.asset_url\')}}/fonts/MyriadReg.woff2") format("woff2"),url("{{config(\'app.asset_url\')}}/fonts/MyriadReg.woff") format("woff"),url("{{config(\'app.asset_url\')}}/fonts/MyriadReg.otf") format("opentype");font-display:auto;font-style:normal;font-weight:normal}</style><style type="text/css">body{font-family:\'myriadreg\',\'Open Sans\',Arial,sans-serif}p{margin:10px 0;padding:0}table{border-collapse:collapse}h1,h2,h3,h4,h5,h6{display:block;margin:0;padding:0}img, a img{border:0;height:auto;outline:none;text-decoration:none}body,#bodyTable,#bodyCell{height:100%;margin:0;padding:0;width:100%}.mcnPreviewText{display:none !important}#outlook a{padding:0}img{-ms-interpolation-mode:bicubic}table{mso-table-lspace:0;mso-table-rspace:0}.ReadMsgBody{width:100%}.ExternalClass{width:100%}p,a,li,td,blockquote{mso-line-height-rule:exactly}a[href^=tel],a[href^=sms]{color:inherit;cursor:default;text-decoration:none}p,a,li,td,body,table,blockquote{-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}.ExternalClass, .ExternalClass p, .ExternalClass td, .ExternalClass div, .ExternalClass span, .ExternalClass font{line-height:100%}a[x-apple-data-detectors]{color:inherit !important;text-decoration:none !important;font-size:inherit !important;font-family:inherit !important;font-weight:inherit !important;line-height:inherit !important}#bodyCell{padding:10px}.templateContainer{max-width:640px !important}a.mcnButton{display:block}.mcnImage,.mcnRetinaImage{vertical-align:bottom}.mcnTextContent{word-break:break-word}.mcnTextContent img{height:auto !important}.mcnDividerBlock{table-layout:fixed !important}body,#bodyTable{background-color:#FAFAFA}#bodyCell{border-top:0}.templateContainer{border:0}h1{color:#202020;font-family:Helvetica;font-size:26px;font-style:normal;font-weight:bold;line-height:125%;letter-spacing:normal;text-align:left}h2{color:#202020;font-family:Helvetica;font-size:22px;font-style:normal;font-weight:bold;line-height:125%;letter-spacing:normal;text-align:left}h3{color:#202020;font-family:Helvetica;font-size:20px;font-style:normal;font-weight:bold;line-height:125%;letter-spacing:normal;text-align:left}h4{color:#202020;font-family:Helvetica;font-size:18px;font-style:normal;font-weight:bold;line-height:125%;letter-spacing:normal;text-align:left}#templatePreheader{background-color:#fafafa;background-image:none;background-repeat:no-repeat;background-position:center;background-size:cover;border-top:0;border-bottom:0;padding-top:9px;padding-bottom:9px}#templatePreheader .mcnTextContent, #templatePreheader .mcnTextContent p{color:#656565;font-family:Helvetica;font-size:12px;line-height:normal;text-align:left}#templatePreheader .mcnTextContent a, #templatePreheader .mcnTextContent p a{color:#656565;font-weight:normal;text-decoration:underline}#templateHeader{background-color:#fff;background-image:none;background-repeat:no-repeat;background-position:center;background-size:cover;border-top:0;border-bottom:1px dashed #74d1f6;padding-top:9px;padding-bottom:0}#templateHeader .mcnTextContent, #templateHeader .mcnTextContent p{color:#202020;font-family:Helvetica;font-size:16px;line-height:normal;text-align:left}#templateHeader .mcnTextContent a, #templateHeader .mcnTextContent p a{color:#007C89;font-weight:normal;text-decoration:underline}#templateBody{background-color:#fff;background-image:none;background-repeat:no-repeat;background-position:center;background-size:cover;border-top:0;border-bottom:0;padding-top:px;padding-bottom:9px}#templateBody .mcnTextContent, #templateBody .mcnTextContent p{color:#202020;font-family:Helvetica;font-size:16px;line-height:normal;text-align:left}#templateBody .mcnTextContent a, #templateBody .mcnTextContent p a{color:#007C89;font-weight:normal;text-decoration:underline}#templateFooter{background-color:#fff;background-image:none;background-repeat:no-repeat;background-position:center;background-size:cover;border-top:1px dashed #74d1f6;border-bottom:0;padding-top:9px;padding-bottom:9px}#templateFooter .mcnTextContent, #templateFooter .mcnTextContent p{color:#656565;font-family:Helvetica;font-size:12px;line-height:normal;text-align:center}#templateFooter .mcnTextContent a, #templateFooter .mcnTextContent p a{color:#656565;font-weight:normal;text-decoration:underline}@media only screen and(min-width:768px){.templateContainer{width:640px !important}}@media only screen and(max-width: 480px){body,table,td,p,a,li,blockquote{-webkit-text-size-adjust:none !important}}@media only screen and(max-width: 480px){body{width:100% !important;min-width:100% !important}}@media only screen and(max-width: 480px){.mcnRetinaImage{max-width:100% !important}}@media only screen and(max-width: 480px){.mcnImage{width:100% !important}}@media only screen and(max-width: 480px){.mcnCartContainer,.mcnCaptionTopContent,.mcnRecContentContainer,.mcnCaptionBottomContent,.mcnTextContentContainer,.mcnBoxedTextContentContainer,.mcnImageGroupContentContainer,.mcnCaptionLeftTextContentContainer,.mcnCaptionRightTextContentContainer,.mcnCaptionLeftImageContentContainer,.mcnCaptionRightImageContentContainer,.mcnImageCardLeftTextContentContainer,.mcnImageCardRightTextContentContainer,.mcnImageCardLeftImageContentContainer,.mcnImageCardRightImageContentContainer{max-width:100% !important;width:100% !important}}@media only screen and(max-width: 480px){.mcnBoxedTextContentContainer{min-width:100% !important}}@media only screen and(max-width: 480px){.mcnImageGroupContent{padding:9px !important}}@media only screen and(max-width: 480px){.mcnCaptionLeftContentOuter .mcnTextContent, .mcnCaptionRightContentOuter .mcnTextContent{padding-top:9px !important}}@media only screen and(max-width: 480px){.mcnImageCardTopImageContent, .mcnCaptionBottomContent:last-child .mcnCaptionBottomImageContent, .mcnCaptionBlockInner .mcnCaptionTopContent:last-child .mcnTextContent{padding-top:18px !important}}@media only screen and(max-width: 480px){.mcnImageCardBottomImageContent{padding-bottom:9px !important}}@media only screen and(max-width: 480px){.mcnImageGroupBlockInner{padding-top:0 !important;padding-bottom:0 !important}}@media only screen and(max-width: 480px){.mcnImageGroupBlockOuter{padding-top:9px !important;padding-bottom:9px !important}}@media only screen and(max-width: 480px){.mcnTextContent,.mcnBoxedTextContentColumn{padding-right:18px !important;padding-left:18px !important}}@media only screen and(max-width: 480px){.mcnImageCardLeftImageContent,.mcnImageCardRightImageContent{padding-right:18px !important;padding-bottom:0 !important;padding-left:18px !important}}@media only screen and(max-width: 480px){.mcpreview-image-uploader{display:none !important;width:100% !important}}@media only screen and(max-width: 480px){h1{font-size:22px !important;line-height:125% !important}}@media only screen and(max-width: 480px){h2{font-size:20px !important;line-height:125% !important}}@media only screen and(max-width: 480px){h3{font-size:18px !important;line-height:125% !important}}@media only screen and(max-width: 480px){h4{font-size:16px !important;line-height:normal !important}}@media only screen and(max-width: 480px){.mcnBoxedTextContentContainer .mcnTextContent, .mcnBoxedTextContentContainer .mcnTextContent p{font-size:14px !important;line-height:normal !important}}@media only screen and(max-width: 480px){#templatePreheader{display:block !important}}@media only screen and(max-width: 480px){#templatePreheader .mcnTextContent, #templatePreheader .mcnTextContent p{font-size:14px !important;line-height:normal !important}}@media only screen and(max-width: 480px){#templateHeader .mcnTextContent, #templateHeader .mcnTextContent p{font-size:16px !important;line-height:normal !important}}@media only screen and(max-width: 480px){#templateBody .mcnTextContent, #templateBody .mcnTextContent p{font-size:16px !important;line-height:normal !important}}@media only screen and(max-width: 480px){#templateFooter .mcnTextContent, #templateFooter .mcnTextContent p{font-size:14px !important;line-height:normal !important}}table.transaction th{border-bottom:1px solid #000;text-align:left}</style></head><body><center><table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable"><tr><td align="center" valign="top" id="bodyCell"> <!--[if (gte mso 9)|(IE)]><table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;"><tr><td align="center" valign="top" width="600" style="width:600px;"> <![endif]--><table border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer"><tr><td valign="top" id="templateHeader"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;"><tbody class="mcnTextBlockOuter"><tr><td valign="top" class="mcnTextBlockInner" style="padding-top:9px;"> <!--[if mso]><table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;"><tr> <![endif]--> <!--[if mso]><td valign="top" width="210" style="width:210px;"> <![endif]--><table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:210px;" width="100%" class="mcnTextContentContainer"><tbody><tr><td valign="top" class="mcnTextContent" style="padding-top:0; padding-left:18px; padding-bottom:9px; padding-right:18px;"> <img data-file-id="1875424" height="58" src="{{config(\'app.asset_url\')}}/images/logo.72.png?v={{Str::random(6)}}" style="border: 0px ; width: 115px; height: 58px; margin: 0px;" width="115"></td></tr></tbody></table> <!--[if mso]></td> <![endif]--> <!--[if mso]><td valign="top" width="390" style="width:390px;"> <![endif]--><table align="right" border="0" cellpadding="0" cellspacing="0" style="max-width:390px;" width="100%" class="mcnTextContentContainer"><tbody><tr><td valign="top" class="mcnTextContent" style="padding-top:0; padding-left:18px; padding-bottom:9px; padding-right:0; padding-top: 30px"><div style="text-align: right;"><table align="right" border="0" cellpadding="0" cellspacing="0" style="max-width:390px;" width="100%" class="mcnTextContentContainer"><tbody><tr><td valign="top" style="padding-top:0;" class="mcnTextContent"> <img align="none" data-file-id="1875408" height="18" src="{{config(\'app.asset_url\')}}/images/phone.72.png?v={{Str::random(6)}}" style="border: 0px ; width: 19px; height: 18px;" width="19"></td><td valign="top" style="padding-top:0;" class="mcnTextContent"> <span style="font-size:9.25pt; padding:0 10px 0 5px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">020-760 50 40</span></td><td valign="top" style="padding-top:0;" class="mcnTextContent"> <img data-file-id="1875416" height="18" src="{{config(\'app.asset_url\')}}/images/mail.72.png?v={{Str::random(6)}}" style="border: 0px ; width: 19px; height: 18px;" width="19"></td><td valign="top" style="padding-top:0;" class="mcnTextContent"> <span style="font-size:9.25pt; padding:0 10px 0 5px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">info@fiber.nl</span></td><td valign="top" style="padding-top:0;" class="mcnTextContent"> <img data-file-id="1875420" height="18" src="{{config(\'app.asset_url\')}}/images/bookmark.72.png?v={{Str::random(6)}}" style="border: 0px ; width: 19px; height: 18px;" width="19"></td><td valign="top" style="padding-top:0;" class="mcnTextContent"> <span style="font-size:9.25pt; padding-left:5px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">www.fiber.nl</span></td></tr></tbody></table></div></td></tr></tbody></table> <!--[if mso]></td> <![endif]--> <!--[if mso]></tr></table> <![endif]--></td></tr></tbody></table></td></tr><tr><td valign="top" id="templateBody"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;"><tbody class="mcnTextBlockOuter"><tr><td valign="top" class="mcnTextBlockInner" style="padding-top:9px;"> <!--[if mso]><table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;"><tr> <![endif]--> <!--[if mso]><td valign="top" width="600" style="width:600px;"> <![endif]--><table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer"><tbody><tr><td valign="top" class="mcnTextContent" style="padding: 25px 18px 9px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; font-size: 11.25pt; line-height: normal;"><p style="margin-bottom: 30px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; line-height: normal;"> {{$user_fullname}}<br> {{$street}}<br> {{$address}}</p> <span style="font-size: 24px"> <strong>Herinnering</strong> </span><p style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; font-size: 1pt;"> &nbsp;</p><table border="0" cellpadding="0" cellspacing="0" role="presentation" style="width: 50%"><tbody><tr><td>Datum</td><td style="padding-left: 30px">{{$date}}</td></tr><tr><td>Klantnummer</td><td style="padding-left: 30px"> {{$customer_number}}</td></tr></tbody></table> &nbsp;<p style="margin-top: 20px; margin-bottom: 20px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; font-size: 11.25pt;"> Beste {{$user_fullname}},</p><p style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; font-size: 11.25pt;"> Wij hebben de onderstaande factuur geïncasseerd van uw rekening nummer {{$iban}} maar deze incasso is niet gelukt. Hierdoor staat er nu een bedrag open. Het openstaande bedrag heeft betrekking op de onderstaande factuur voor de diensten die Fiber NL aan u geleverd heeft, conform uw overeenkomst met Fiber NL (zie bijlage voor details van deze factuur).</p><table border="0" cellpadding="0" cellspacing="0" class="transaction" role="presentation" style="width: 100%; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"><tbody><tr style="padding: 5px 0; border-bottom: 1px solid #000;"><th>Factuur nr.</th><th>Factuurdatum</th><th>Betreft</th><th colspan="2" style="padding-left: 5px; text-align: right"> Bedrag</th></tr><tr style="padding: 5px 0;"><td>{{$invoice_number}}</td><td>{{$invoice_date}}</td><td>{{$concern}}</td><td style="background-color: #EEF1F0; padding-left: 5px;"> €</td><td align="right" style="background-color: #EEF1F0; padding-right: 5px;"> {{$amount}}</td></tr></tbody></table><p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"> Wij verzoeken u vriendelijk doch dringend het openstaande bedrag binnen 7 dagen over te maken op rekeningnummer <strong>NL30 RABO 0337 2353 41</strong> ten name XS Provider, onder vermelding van uw klantnummer en het factuurnummer.</p><p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"> Mochten uw betaling en deze herinnering elkaar hebben gekruist, dan kunt u deze herinnering als niet verzonden beschouwen.</p><p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"> Met vriendelijke groet,</p> &nbsp;<p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"> Klantenservice <br/> Fiber NL</p></td></tr></tbody></table> <!--[if mso]></td> <![endif]--> <!--[if mso]></tr></table> <![endif]--></td></tr></tbody></table></td></tr><tr><td valign="top" id="templateFooter"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;"><tbody class="mcnTextBlockOuter"><tr><td valign="top" class="mcnTextBlockInner" style="padding-top:9px;"> <!--[if mso]><table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;"><tr> <![endif]--> <!--[if mso]><td valign="top" width="600" style="width:600px;"> <![endif]--><table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer"><tbody><tr><td valign="top" class="mcnTextContent" style="padding-top:0; padding-right:18px; padding-bottom:9px; padding-left:18px;"><div style="text-align: center;"> <span style="font-size:9pt; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">Postadres: Postbus 60228, 1320AG Almere | Bezoekadres:<br> Transistorstraat 7, 1322CJ Almere | KvK-nummer: 73786950 | BTW-nummer: NL859664016B01</span></div></td></tr></tbody></table> <!--[if mso]></td> <![endif]--> <!--[if mso]></tr></table> <![endif]--></td></tr></tbody></table></td></tr></table> <!--[if (gte mso 9)|(IE)]></td></tr></table> <![endif]--></td></tr></table></center></body></html>',
                'created_at' => NULL,
                'updated_at' => '2020-12-23 04:05:46',
            ),
            array(
                'id' => 23,
                'tenant_id' => 8,
                'product_id' => NULL,
                'type' => 'sales_invoice.second_reminder',
                'from_name' => 'Fiber Nederland',
                'from_email' => 'noreply@fiber.nl',
                'bcc_email' => 'mark@f2x.nl,cdmigrations@xsprovider.nl,marisa.vanvelzen@xsprovider.nl',
                'subject' => '2e Herinnering: Uw Fiber NL factuur {{$invoice_number}} staat nog open',
                'body_html' => '<!doctype html><html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"><head> <!--[if gte mso 15]> <xml> <o:OfficeDocumentSettings> <o:AllowPNG/> <o:PixelsPerInch>96</o:PixelsPerInch> </o:OfficeDocumentSettings> </xml> <![endif]--><meta charset="UTF-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{$subject}}</title><style>@font-face{font-family:"myriadbold";src:url("{{config(\'app.asset_url\')}}/fonts/MyriadBold.woff2") format("woff2"),url("{{config(\'app.asset_url\')}}/fonts/MyriadBold.woff") format("woff"),url("{{config(\'app.asset_url\')}}/fonts/MyriadBold.otf") format("opentype");font-display:auto;font-style:normal;font-weight:normal}@font-face{font-family:"myriadreg";src:url("{{config(\'app.asset_url\')}}/fonts/MyriadReg.woff2") format("woff2"),url("{{config(\'app.asset_url\')}}/fonts/MyriadReg.woff") format("woff"),url("{{config(\'app.asset_url\')}}/fonts/MyriadReg.otf") format("opentype");font-display:auto;font-style:normal;font-weight:normal}</style><style type="text/css">p{margin:10px 0;padding:0}table{border-collapse:collapse}h1,h2,h3,h4,h5,h6{display:block;margin:0;padding:0}img, a img{border:0;height:auto;outline:none;text-decoration:none}body,#bodyTable,#bodyCell{height:100%;margin:0;padding:0;width:100%}.mcnPreviewText{display:none !important}#outlook a{padding:0}img{-ms-interpolation-mode:bicubic}table{mso-table-lspace:0;mso-table-rspace:0}.ReadMsgBody{width:100%}.ExternalClass{width:100%}p,a,li,td,blockquote{mso-line-height-rule:exactly}a[href^=tel],a[href^=sms]{color:inherit;cursor:default;text-decoration:none}p,a,li,td,body,table,blockquote{-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}.ExternalClass, .ExternalClass p, .ExternalClass td, .ExternalClass div, .ExternalClass span, .ExternalClass font{line-height:100%}a[x-apple-data-detectors]{color:inherit !important;text-decoration:none !important;font-size:inherit !important;font-family:inherit !important;font-weight:inherit !important;line-height:inherit !important}#bodyCell{padding:10px}.templateContainer{max-width:640px !important}a.mcnButton{display:block}.mcnImage,.mcnRetinaImage{vertical-align:bottom}.mcnTextContent{word-break:break-word}.mcnTextContent img{height:auto !important}.mcnDividerBlock{table-layout:fixed !important}body,#bodyTable{background-color:#FAFAFA}#bodyCell{border-top:0}.templateContainer{border:0}h1{color:#202020;font-family:Helvetica;font-size:26px;font-style:normal;font-weight:bold;line-height:125%;letter-spacing:normal;text-align:left}h2{color:#202020;font-family:Helvetica;font-size:22px;font-style:normal;font-weight:bold;line-height:125%;letter-spacing:normal;text-align:left}h3{color:#202020;font-family:Helvetica;font-size:20px;font-style:normal;font-weight:bold;line-height:125%;letter-spacing:normal;text-align:left}h4{color:#202020;font-family:Helvetica;font-size:18px;font-style:normal;font-weight:bold;line-height:125%;letter-spacing:normal;text-align:left}#templatePreheader{background-color:#fafafa;background-image:none;background-repeat:no-repeat;background-position:center;background-size:cover;border-top:0;border-bottom:0;padding-top:9px;padding-bottom:9px}#templatePreheader .mcnTextContent, #templatePreheader .mcnTextContent p{color:#656565;font-family:Helvetica;font-size:12px;line-height:normal;text-align:left}#templatePreheader .mcnTextContent a, #templatePreheader .mcnTextContent p a{color:#656565;font-weight:normal;text-decoration:underline}#templateHeader{background-color:#fff;background-image:none;background-repeat:no-repeat;background-position:center;background-size:cover;border-top:0;border-bottom:1px dashed #74d1f6;padding-top:9px;padding-bottom:0}#templateHeader .mcnTextContent, #templateHeader .mcnTextContent p{color:#202020;font-family:Helvetica;font-size:16px;line-height:normal;text-align:left}#templateHeader .mcnTextContent a, #templateHeader .mcnTextContent p a{color:#007C89;font-weight:normal;text-decoration:underline}#templateBody{background-color:#fff;background-image:none;background-repeat:no-repeat;background-position:center;background-size:cover;border-top:0;border-bottom:0;padding-top:px;padding-bottom:9px}#templateBody .mcnTextContent, #templateBody .mcnTextContent p{color:#202020;font-family:Helvetica;font-size:16px;line-height:normal;text-align:left}#templateBody .mcnTextContent a, #templateBody .mcnTextContent p a{color:#007C89;font-weight:normal;text-decoration:underline}#templateFooter{background-color:#fff;background-image:none;background-repeat:no-repeat;background-position:center;background-size:cover;border-top:1px dashed #74d1f6;border-bottom:0;padding-top:9px;padding-bottom:9px}#templateFooter .mcnTextContent, #templateFooter .mcnTextContent p{color:#656565;font-family:Helvetica;font-size:12px;line-height:normal;text-align:center}#templateFooter .mcnTextContent a, #templateFooter .mcnTextContent p a{color:#656565;font-weight:normal;text-decoration:underline}@media only screen and(min-width:768px){.templateContainer{width:640px !important}}@media only screen and(max-width: 480px){body,table,td,p,a,li,blockquote{-webkit-text-size-adjust:none !important}}@media only screen and(max-width: 480px){body{width:100% !important;min-width:100% !important}}@media only screen and(max-width: 480px){.mcnRetinaImage{max-width:100% !important}}@media only screen and(max-width: 480px){.mcnImage{width:100% !important}}@media only screen and(max-width: 480px){.mcnCartContainer,.mcnCaptionTopContent,.mcnRecContentContainer,.mcnCaptionBottomContent,.mcnTextContentContainer,.mcnBoxedTextContentContainer,.mcnImageGroupContentContainer,.mcnCaptionLeftTextContentContainer,.mcnCaptionRightTextContentContainer,.mcnCaptionLeftImageContentContainer,.mcnCaptionRightImageContentContainer,.mcnImageCardLeftTextContentContainer,.mcnImageCardRightTextContentContainer,.mcnImageCardLeftImageContentContainer,.mcnImageCardRightImageContentContainer{max-width:100% !important;width:100% !important}}@media only screen and(max-width: 480px){.mcnBoxedTextContentContainer{min-width:100% !important}}@media only screen and(max-width: 480px){.mcnImageGroupContent{padding:9px !important}}@media only screen and(max-width: 480px){.mcnCaptionLeftContentOuter .mcnTextContent, .mcnCaptionRightContentOuter .mcnTextContent{padding-top:9px !important}}@media only screen and(max-width: 480px){.mcnImageCardTopImageContent, .mcnCaptionBottomContent:last-child .mcnCaptionBottomImageContent, .mcnCaptionBlockInner .mcnCaptionTopContent:last-child .mcnTextContent{padding-top:18px !important}}@media only screen and(max-width: 480px){.mcnImageCardBottomImageContent{padding-bottom:9px !important}}@media only screen and(max-width: 480px){.mcnImageGroupBlockInner{padding-top:0 !important;padding-bottom:0 !important}}@media only screen and(max-width: 480px){.mcnImageGroupBlockOuter{padding-top:9px !important;padding-bottom:9px !important}}@media only screen and(max-width: 480px){.mcnTextContent,.mcnBoxedTextContentColumn{padding-right:18px !important;padding-left:18px !important}}@media only screen and(max-width: 480px){.mcnImageCardLeftImageContent,.mcnImageCardRightImageContent{padding-right:18px !important;padding-bottom:0 !important;padding-left:18px !important}}@media only screen and(max-width: 480px){.mcpreview-image-uploader{display:none !important;width:100% !important}}@media only screen and(max-width: 480px){h1{font-size:22px !important;line-height:125% !important}}@media only screen and(max-width: 480px){h2{font-size:20px !important;line-height:125% !important}}@media only screen and(max-width: 480px){h3{font-size:18px !important;line-height:125% !important}}@media only screen and(max-width: 480px){h4{font-size:16px !important;line-height:normal !important}}@media only screen and(max-width: 480px){.mcnBoxedTextContentContainer .mcnTextContent, .mcnBoxedTextContentContainer .mcnTextContent p{font-size:14px !important;line-height:normal !important}}@media only screen and(max-width: 480px){#templatePreheader{display:block !important}}@media only screen and(max-width: 480px){#templatePreheader .mcnTextContent, #templatePreheader .mcnTextContent p{font-size:14px !important;line-height:normal !important}}@media only screen and(max-width: 480px){#templateHeader .mcnTextContent, #templateHeader .mcnTextContent p{font-size:16px !important;line-height:normal !important}}@media only screen and(max-width: 480px){#templateBody .mcnTextContent, #templateBody .mcnTextContent p{font-size:16px !important;line-height:normal !important}}@media only screen and(max-width: 480px){#templateFooter .mcnTextContent, #templateFooter .mcnTextContent p{font-size:14px !important;line-height:normal !important}}table.transaction th{border-bottom:1px solid #000;text-align:left}</style></head><body><center><table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable"><tr><td align="center" valign="top" id="bodyCell"> <!--[if (gte mso 9)|(IE)]><table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;"><tr><td align="center" valign="top" width="600" style="width:600px;"> <![endif]--><table border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer"><tr><td valign="top" id="templateHeader"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;"><tbody class="mcnTextBlockOuter"><tr><td valign="top" class="mcnTextBlockInner" style="padding-top:9px;"> <!--[if mso]><table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;"><tr> <![endif]--> <!--[if mso]><td valign="top" width="210" style="width:210px;"> <![endif]--><table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:210px;" width="100%" class="mcnTextContentContainer"><tbody><tr><td valign="top" class="mcnTextContent" style="padding-top:0; padding-left:18px; padding-bottom:9px; padding-right:18px;"><img data-file-id="1875424" height="58" src="{{config(\'app.asset_url\')}}/images/logo.72.png?v={{Str::random(6)}}" style="border: 0px ; width: 115px; height: 58px; margin: 0px;" width="115"></td></tr></tbody></table> <!--[if mso]></td> <![endif]--><!--[if mso]><td valign="top" width="390" style="width:390px;"> <![endif]--><table align="right" border="0" cellpadding="0" cellspacing="0" style="max-width:390px;" width="100%" class="mcnTextContentContainer"><tbody><tr><td valign="top" class="mcnTextContent" style="padding-top:0; padding-left:18px; padding-bottom:9px; padding-right:0; padding-top: 30px"><div style="text-align: right;"><table align="right" border="0" cellpadding="0" cellspacing="0" style="max-width:390px;" width="100%" class="mcnTextContentContainer"><tbody><tr><td valign="top" style="padding-top:0;" class="mcnTextContent"> <img align="none" data-file-id="1875408" height="18" src="{{config(\'app.asset_url\')}}/images/phone.72.png?v={{Str::random(6)}}" style="border: 0px ; width: 19px; height: 18px;" width="19"></td><td valign="top" style="padding-top:0;" class="mcnTextContent"> <span style="font-size:9.25pt; padding:0 10px 0 5px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">020-760 50 40</span></td><td valign="top" style="padding-top:0;" class="mcnTextContent"> <img data-file-id="1875416" height="18" src="{{config(\'app.asset_url\')}}/images/mail.72.png?v={{Str::random(6)}}" style="border: 0px ; width: 19px; height: 18px;" width="19"></td><td valign="top" style="padding-top:0;" class="mcnTextContent"> <span style="font-size:9.25pt; padding:0 10px 0 5px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">info@fiber.nl</span></td><td valign="top" style="padding-top:0;" class="mcnTextContent"> <img data-file-id="1875420" height="18" src="{{config(\'app.asset_url\')}}/images/bookmark.72.png?v={{Str::random(6)}}" style="border: 0px ; width: 19px; height: 18px;" width="19"></td><td valign="top" style="padding-top:0;" class="mcnTextContent"> <span style="font-size:9.25pt; padding-left:5px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">www.fiber.nl</span></td></tr></tbody></table></div></td></tr></tbody></table> <!--[if mso]></td> <![endif]--><!--[if mso]></tr></table> <![endif]--></td></tr></tbody></table></td></tr><tr><td valign="top" id="templateBody"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;"><tbody class="mcnTextBlockOuter"><tr><td valign="top" class="mcnTextBlockInner" style="padding-top:9px;"> <!--[if mso]><table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;"><tr> <![endif]--> <!--[if mso]><td valign="top" width="600" style="width:600px;"> <![endif]--><table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer"><tbody><tr><td valign="top" class="mcnTextContent" style="padding: 25px 18px 9px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; font-size: 11.25pt; line-height: normal;"><p style="margin-bottom: 30px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; line-height: normal;"> {{$user_fullname}}<br> {{$street}}<br> {{$address}}</p> <span style="font-size: 24px"> <strong>Tweede herinnering</strong> </span><p style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; font-size: 1pt;"> &nbsp;</p><table border="0" cellpadding="0" cellspacing="0" role="presentation" style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; width: 50%"><tbody><tr><td>Datum</td><td style="padding-left: 30px">{{$date}}</td></tr><tr><td>Klantnummer</td><td style="padding-left: 30px"> {{$customer_number}}</td></tr></tbody></table> &nbsp;<p style="margin-top: 20px; margin-bottom: 20px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; font-size: 11.25pt;"> Beste {{$user_fullname}},</p><p style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; font-size: 11.25pt;"> Wij hebben de onderstaande factuur geïncasseerd van uw rekening nummer {{$iban}} maar deze incasso is niet gelukt. Hierdoor staat er nu een bedrag open. Het openstaande bedrag heeft betrekking op de onderstaande factuur voor de diensten die Fiber NL aan u geleverd heeft, conform uw overeenkomst met Fiber NL (zie bijlage voor details van deze factuur).</p><table border="0" cellpadding="0" cellspacing="0" class="transaction" role="presentation" style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; width: 100%"><tbody><tr style="padding: 5px 0; border-bottom: 1px solid #000;"><th>Factuur nr.</th><th>Factuurdatum</th><th>Betreft</th><th colspan="2" style="padding-left: 5px; text-align: right"> Bedrag</th></tr><tr style="padding: 5px 0;"><td>{{$invoice_number}}</td><td>{{$invoice_date}}</td><td>{{$concern}}</td><td style="background-color: #EEF1F0; padding-left: 5px;"> €</td><td align="right" style="background-color: #EEF1F0; padding-right: 5px;"> {{$amount}}</td></tr></tbody></table><p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"> Wij verzoeken u vriendelijk doch dringend het openstaande bedrag binnen 7 dagen over te maken op rekeningnummer <strong>NL30 RABO 0337 2353 41</strong> ten name XS Provider, onder vermelding van uw klantnummer en het factuurnummer.</p><p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"></p><p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"> Mochten uw betaling en deze herinnering elkaar hebben gekruist, dan kunt u deze herinnering als niet verzonden beschouwen.</p><p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"> Met vriendelijke groet,</p> &nbsp;<p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"> Klantenservice <br/> Fiber NL</p></td></tr></tbody></table> <!--[if mso]></td> <![endif]--><!--[if mso]></tr></table> <![endif]--></td></tr></tbody></table></td></tr><tr><td valign="top" id="templateFooter"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;"><tbody class="mcnTextBlockOuter"><tr><td valign="top" class="mcnTextBlockInner" style="padding-top:9px;"> <!--[if mso]><table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;"><tr> <![endif]--> <!--[if mso]><td valign="top" width="600" style="width:600px;"> <![endif]--><table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer"><tbody><tr><td valign="top" class="mcnTextContent" style="padding-top:0; padding-right:18px; padding-bottom:9px; padding-left:18px;"><div style="text-align: center;"> <span style="font-size:9pt; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">Postadres: Postbus 60228, 1320AG Almere | Bezoekadres:<br> Transistorstraat 7, 1322CJ Almere | KvK-nummer: 73786950 | BTW-nummer: NL859664016B01</span></div></td></tr></tbody></table> <!--[if mso]></td> <![endif]--><!--[if mso]></tr></table> <![endif]--></td></tr></tbody></table></td></tr></table> <!--[if (gte mso 9)|(IE)]></td></tr></table> <![endif]--></td></tr></table></center></body></html>',
                'created_at' => NULL,
                'updated_at' => '2020-12-23 04:05:46',
            ),
            array(
                'id' => 24,
                'tenant_id' => 8,
                'product_id' => NULL,
                'type' => 'sales_invoice.warning',
                'from_name' => 'Fiber Nederland',
                'from_email' => 'noreply@fiber.nl',
                'bcc_email' => 'mark@f2x.nl,cdmigrations@xsprovider.nl,marisa.vanvelzen@xsprovider.nl',
                'subject' => 'AANMANING: Uw Fiber NL factuur {{$invoice_number}} staat nog open',
                'body_html' => '<!doctype html><html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"><head> <!--[if gte mso 15]> <xml> <o:OfficeDocumentSettings> <o:AllowPNG/> <o:PixelsPerInch>96</o:PixelsPerInch> </o:OfficeDocumentSettings> </xml> <![endif]--><meta charset="UTF-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{$subject}}</title><style>@font-face{font-family:"myriadbold";src:url("{{config(\'app.asset_url\')}}/fonts/MyriadBold.woff2") format("woff2"),url("{{config(\'app.asset_url\')}}/fonts/MyriadBold.woff") format("woff"),url("{{config(\'app.asset_url\')}}/fonts/MyriadBold.otf") format("opentype");font-display:auto;font-style:normal;font-weight:normal}@font-face{font-family:"myriadreg";src:url("{{config(\'app.asset_url\')}}/fonts/MyriadReg.woff2") format("woff2"),url("{{config(\'app.asset_url\')}}/fonts/MyriadReg.woff") format("woff"),url("{{config(\'app.asset_url\')}}/fonts/MyriadReg.otf") format("opentype");font-display:auto;font-style:normal;font-weight:normal}</style><style type="text/css">p{margin:10px 0;padding:0}table{border-collapse:collapse}h1,h2,h3,h4,h5,h6{display:block;margin:0;padding:0}img, a img{border:0;height:auto;outline:none;text-decoration:none}body,#bodyTable,#bodyCell{height:100%;margin:0;padding:0;width:100%}.mcnPreviewText{display:none !important}#outlook a{padding:0}img{-ms-interpolation-mode:bicubic}table{mso-table-lspace:0;mso-table-rspace:0}.ReadMsgBody{width:100%}.ExternalClass{width:100%}p,a,li,td,blockquote{mso-line-height-rule:exactly}a[href^=tel],a[href^=sms]{color:inherit;cursor:default;text-decoration:none}p,a,li,td,body,table,blockquote{-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}.ExternalClass, .ExternalClass p, .ExternalClass td, .ExternalClass div, .ExternalClass span, .ExternalClass font{line-height:100%}a[x-apple-data-detectors]{color:inherit !important;text-decoration:none !important;font-size:inherit !important;font-family:inherit !important;font-weight:inherit !important;line-height:inherit !important}#bodyCell{padding:10px}.templateContainer{max-width:640px !important}a.mcnButton{display:block}.mcnImage,.mcnRetinaImage{vertical-align:bottom}.mcnTextContent{word-break:break-word}.mcnTextContent img{height:auto !important}.mcnDividerBlock{table-layout:fixed !important}body,#bodyTable{background-color:#FAFAFA}#bodyCell{border-top:0}.templateContainer{border:0}h1{color:#202020;font-family:Helvetica;font-size:26px;font-style:normal;font-weight:bold;line-height:125%;letter-spacing:normal;text-align:left}h2{color:#202020;font-family:Helvetica;font-size:22px;font-style:normal;font-weight:bold;line-height:125%;letter-spacing:normal;text-align:left}h3{color:#202020;font-family:Helvetica;font-size:20px;font-style:normal;font-weight:bold;line-height:125%;letter-spacing:normal;text-align:left}h4{color:#202020;font-family:Helvetica;font-size:18px;font-style:normal;font-weight:bold;line-height:125%;letter-spacing:normal;text-align:left}#templatePreheader{background-color:#fafafa;background-image:none;background-repeat:no-repeat;background-position:center;background-size:cover;border-top:0;border-bottom:0;padding-top:9px;padding-bottom:9px}#templatePreheader .mcnTextContent, #templatePreheader .mcnTextContent p{color:#656565;font-family:Helvetica;font-size:12px;line-height:normal;text-align:left}#templatePreheader .mcnTextContent a, #templatePreheader .mcnTextContent p a{color:#656565;font-weight:normal;text-decoration:underline}#templateHeader{background-color:#fff;background-image:none;background-repeat:no-repeat;background-position:center;background-size:cover;border-top:0;border-bottom:1px dashed #74d1f6;padding-top:9px;padding-bottom:0}#templateHeader .mcnTextContent, #templateHeader .mcnTextContent p{color:#202020;font-family:Helvetica;font-size:16px;line-height:normal;text-align:left}#templateHeader .mcnTextContent a, #templateHeader .mcnTextContent p a{color:#007C89;font-weight:normal;text-decoration:underline}#templateBody{background-color:#fff;background-image:none;background-repeat:no-repeat;background-position:center;background-size:cover;border-top:0;border-bottom:0;padding-top:px;padding-bottom:9px}#templateBody .mcnTextContent, #templateBody .mcnTextContent p{color:#202020;font-family:Helvetica;font-size:16px;line-height:normal;text-align:left}#templateBody .mcnTextContent a, #templateBody .mcnTextContent p a{color:#007C89;font-weight:normal;text-decoration:underline}#templateFooter{background-color:#fff;background-image:none;background-repeat:no-repeat;background-position:center;background-size:cover;border-top:1px dashed #74d1f6;border-bottom:0;padding-top:9px;padding-bottom:9px}#templateFooter .mcnTextContent, #templateFooter .mcnTextContent p{color:#656565;font-family:Helvetica;font-size:12px;line-height:normal;text-align:center}#templateFooter .mcnTextContent a, #templateFooter .mcnTextContent p a{color:#656565;font-weight:normal;text-decoration:underline}@media only screen and(min-width:768px){.templateContainer{width:640px !important}}@media only screen and(max-width: 480px){body,table,td,p,a,li,blockquote{-webkit-text-size-adjust:none !important}}@media only screen and(max-width: 480px){body{width:100% !important;min-width:100% !important}}@media only screen and(max-width: 480px){.mcnRetinaImage{max-width:100% !important}}@media only screen and(max-width: 480px){.mcnImage{width:100% !important}}@media only screen and(max-width: 480px){.mcnCartContainer,.mcnCaptionTopContent,.mcnRecContentContainer,.mcnCaptionBottomContent,.mcnTextContentContainer,.mcnBoxedTextContentContainer,.mcnImageGroupContentContainer,.mcnCaptionLeftTextContentContainer,.mcnCaptionRightTextContentContainer,.mcnCaptionLeftImageContentContainer,.mcnCaptionRightImageContentContainer,.mcnImageCardLeftTextContentContainer,.mcnImageCardRightTextContentContainer,.mcnImageCardLeftImageContentContainer,.mcnImageCardRightImageContentContainer{max-width:100% !important;width:100% !important}}@media only screen and(max-width: 480px){.mcnBoxedTextContentContainer{min-width:100% !important}}@media only screen and(max-width: 480px){.mcnImageGroupContent{padding:9px !important}}@media only screen and(max-width: 480px){.mcnCaptionLeftContentOuter .mcnTextContent, .mcnCaptionRightContentOuter .mcnTextContent{padding-top:9px !important}}@media only screen and(max-width: 480px){.mcnImageCardTopImageContent, .mcnCaptionBottomContent:last-child .mcnCaptionBottomImageContent, .mcnCaptionBlockInner .mcnCaptionTopContent:last-child .mcnTextContent{padding-top:18px !important}}@media only screen and(max-width: 480px){.mcnImageCardBottomImageContent{padding-bottom:9px !important}}@media only screen and(max-width: 480px){.mcnImageGroupBlockInner{padding-top:0 !important;padding-bottom:0 !important}}@media only screen and(max-width: 480px){.mcnImageGroupBlockOuter{padding-top:9px !important;padding-bottom:9px !important}}@media only screen and(max-width: 480px){.mcnTextContent,.mcnBoxedTextContentColumn{padding-right:18px !important;padding-left:18px !important}}@media only screen and(max-width: 480px){.mcnImageCardLeftImageContent,.mcnImageCardRightImageContent{padding-right:18px !important;padding-bottom:0 !important;padding-left:18px !important}}@media only screen and(max-width: 480px){.mcpreview-image-uploader{display:none !important;width:100% !important}}@media only screen and(max-width: 480px){h1{font-size:22px !important;line-height:125% !important}}@media only screen and(max-width: 480px){h2{font-size:20px !important;line-height:125% !important}}@media only screen and(max-width: 480px){h3{font-size:18px !important;line-height:125% !important}}@media only screen and(max-width: 480px){h4{font-size:16px !important;line-height:normal !important}}@media only screen and(max-width: 480px){.mcnBoxedTextContentContainer .mcnTextContent, .mcnBoxedTextContentContainer .mcnTextContent p{font-size:14px !important;line-height:normal !important}}@media only screen and(max-width: 480px){#templatePreheader{display:block !important}}@media only screen and(max-width: 480px){#templatePreheader .mcnTextContent, #templatePreheader .mcnTextContent p{font-size:14px !important;line-height:normal !important}}@media only screen and(max-width: 480px){#templateHeader .mcnTextContent, #templateHeader .mcnTextContent p{font-size:16px !important;line-height:normal !important}}@media only screen and(max-width: 480px){#templateBody .mcnTextContent, #templateBody .mcnTextContent p{font-size:16px !important;line-height:normal !important}}@media only screen and(max-width: 480px){#templateFooter .mcnTextContent, #templateFooter .mcnTextContent p{font-size:14px !important;line-height:normal !important}}table.transaction th{border-bottom:1px solid #000;text-align:left}</style></head><body><center><table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable"><tr><td align="center" valign="top" id="bodyCell"> <!--[if (gte mso 9)|(IE)]><table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;"><tr><td align="center" valign="top" width="600" style="width:600px;"> <![endif]--><table border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer"><tr><td valign="top" id="templateHeader"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;"><tbody class="mcnTextBlockOuter"><tr><td valign="top" class="mcnTextBlockInner" style="padding-top:9px;"> <!--[if mso]><table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;"><tr> <![endif]--> <!--[if mso]><td valign="top" width="210" style="width:210px;"> <![endif]--><table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:210px;" width="100%" class="mcnTextContentContainer"><tbody><tr><td valign="top" class="mcnTextContent" style="padding-top:0; padding-left:18px; padding-bottom:9px; padding-right:18px;"> <img data-file-id="1875424" height="58" src="{{config(\'app.asset_url\')}}/images/logo.72.png?v={{Str::random(6)}}" style="border: 0px ; width: 115px; height: 58px; margin: 0px;" width="115"></td></tr></tbody></table> <!--[if mso]></td> <![endif]--> <!--[if mso]><td valign="top" width="390" style="width:390px;"> <![endif]--><table align="right" border="0" cellpadding="0" cellspacing="0" style="max-width:390px;" width="100%" class="mcnTextContentContainer"><tbody><tr><td valign="top" class="mcnTextContent" style="padding-top:0; padding-left:18px; padding-bottom:9px; padding-right:0; padding-top: 30px"><div style="text-align: right;"><table align="right" border="0" cellpadding="0" cellspacing="0" style="max-width:390px;" width="100%" class="mcnTextContentContainer"><tbody><tr><td valign="top" style="padding-top:0;" class="mcnTextContent"> <img align="none" data-file-id="1875408" height="18" src="{{config(\'app.asset_url\')}}/images/phone.72.png?v={{Str::random(6)}}" style="border: 0px ; width: 19px; height: 18px;" width="19"></td><td valign="top" style="padding-top:0;" class="mcnTextContent"> <span style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; font-size:9.25pt; padding:0 10px 0 5px">020-760 50 40</span></td><td valign="top" style="padding-top:0;" class="mcnTextContent"> <img data-file-id="1875416" height="18" src="{{config(\'app.asset_url\')}}/images/mail.72.png?v={{Str::random(6)}}" style="border: 0px ; width: 19px; height: 18px;" width="19"></td><td valign="top" style="padding-top:0;" class="mcnTextContent"> <span style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; font-size:9.25pt; padding:0 10px 0 5px">info@fiber.nl</span></td><td valign="top" style="padding-top:0;" class="mcnTextContent"> <img data-file-id="1875420" height="18" src="{{config(\'app.asset_url\')}}/images/bookmark.72.png?v={{Str::random(6)}}" style="border: 0px ; width: 19px; height: 18px;" width="19"></td><td valign="top" style="padding-top:0;" class="mcnTextContent"> <span style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; font-size:9.25pt; padding-left:5px">www.fiber.nl</span></td></tr></tbody></table></div></td></tr></tbody></table> <!--[if mso]></td> <![endif]--> <!--[if mso]></tr></table> <![endif]--></td></tr></tbody></table></td></tr><tr><td valign="top" id="templateBody"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;"><tbody class="mcnTextBlockOuter"><tr><td valign="top" class="mcnTextBlockInner" style="padding-top:9px;"> <!--[if mso]><table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;"><tr> <![endif]--> <!--[if mso]><td valign="top" width="600" style="width:600px;"> <![endif]--><table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer"><tbody><tr><td valign="top" class="mcnTextContent" style="padding: 25px 18px 9px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; font-size: 11.25pt; line-height: normal;"><p style="margin-bottom: 30px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; line-height: normal;"> {{$user_fullname}}<br> {{$street}}<br> {{$address}}</p> <span style="font-size: 24px"> <strong>AANMANING</strong> </span><p style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; font-size: 1pt;"> &nbsp;</p><table border="0" cellpadding="0" cellspacing="0" role="presentation" style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; width: 50%"><tbody><tr><td>Datum</td><td style="padding-left: 30px">{{$date}}</td></tr><tr><td>Klantnummer</td><td style="padding-left: 30px"> {{$customer_number}}</td></tr></tbody></table> &nbsp;<p style="margin-top: 20px; margin-bottom: 20px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; font-size: 11.25pt;"> Beste {{$user_fullname}},</p><p style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; font-size: 11.25pt;"> Wij hebben u eerder geïnformeerd over een openstaande factuur, waarvoor wij nog geen betaling hebben ontvangen. Het openstaande bedrag heeft betrekking op de onderstaande factuur inzake door Fiber NL aan u geleverde diensten (zie bijlage voor details van deze factuur).</p><table border="0" cellpadding="0" cellspacing="0" class="transaction" role="presentation" style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; width: 100%"><tbody><tr style="padding: 5px 0; border-bottom: 1px solid #000;"><th>Factuur nr.</th><th>Factuurdatum</th><th>Betreft</th><th colspan="2" style="padding-left: 5px; text-align: right"> Bedrag</th></tr><tr style="padding: 5px 0;"><td>{{$invoice_number}}</td><td>{{$invoice_date}}</td><td>{{$concern}}</td><td style="background-color: #EEF1F0; padding-left: 5px;"> €</td><td align="right" style="background-color: #EEF1F0; padding-right: 5px;"> {{$amount}}</td></tr></tbody></table><p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"> Wij verzoeken u vriendelijk doch dringend het openstaande bedrag binnen 7 dagen over te maken op rekeningnummer <strong>NL30 RABO 0337 2353 41</strong> ten name XS Provider, onder vermelding van uw klantnummer en het factuurnummer.</p><p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"> <strong>Indien wij uw betaling niet ontvangen binnen 7 dagen sluiten wij uw diensten tijdelijk af totdat de betaling ontvangen is.</strong> U ontvangt dan de officiële ingebrekestelling. Daarna dragen wij de vordering over aan een incassobureau. De incassokosten van € 40,- worden dan aan u in rekening gebracht.</p><p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"> Laat het niet zover komen en betaal vandaag nog.</p><p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"> Mochten uw betaling en deze herinnering elkaar hebben gekruist, dan kunt u deze herinnering als niet verzonden beschouwen.</p><p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"> Met vriendelijke groet,</p> &nbsp;<p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"> Klantenservice <br/> Fiber NL</p></td></tr></tbody></table> <!--[if mso]></td> <![endif]--> <!--[if mso]></tr></table> <![endif]--></td></tr></tbody></table></td></tr><tr><td valign="top" id="templateFooter"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;"><tbody class="mcnTextBlockOuter"><tr><td valign="top" class="mcnTextBlockInner" style="padding-top:9px;"> <!--[if mso]><table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;"><tr> <![endif]--> <!--[if mso]><td valign="top" width="600" style="width:600px;"> <![endif]--><table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer"><tbody><tr><td valign="top" class="mcnTextContent" style="padding-top:0; padding-right:18px; padding-bottom:9px; padding-left:18px;"><div style="text-align: center;"> <span style="font-size:9pt; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"> Postadres: Postbus 60228, 1320AG Almere | Bezoekadres:<br> Transistorstraat 7, 1322CJ Almere | KvK-nummer: 73786950 | BTW-nummer: NL859664016B01</span></div></td></tr></tbody></table> <!--[if mso]></td> <![endif]--> <!--[if mso]></tr></table> <![endif]--></td></tr></tbody></table></td></tr></table> <!--[if (gte mso 9)|(IE)]></td></tr></table> <![endif]--></td></tr></table></center></body></html>',
                'created_at' => NULL,
                'updated_at' => '2020-12-23 04:05:46',
            ),
            array(
                'id' => 25,
                'tenant_id' => 8,
                'product_id' => NULL,
                'type' => 'sales_invoice.final_notice',
                'from_name' => 'Fiber Nederland',
                'from_email' => 'noreply@fiber.nl',
                'bcc_email' => 'mark@f2x.nl,cdmigrations@xsprovider.nl,marisa.vanvelzen@xsprovider.nl',
                'subject' => 'INGEBREKESTELLING: Uw Fiber NL factuur {{$invoice_number}} staat nog open',
                'body_html' => '<!doctype html><html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"><head> <!--[if gte mso 15]> <xml> <o:OfficeDocumentSettings> <o:AllowPNG/> <o:PixelsPerInch>96</o:PixelsPerInch> </o:OfficeDocumentSettings> </xml> <![endif]--><meta charset="UTF-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{$subject}}</title><style>@font-face{font-family:"myriadbold";src:url("{{config(\'app.asset_url\')}}/fonts/MyriadBold.woff2") format("woff2"),url("{{config(\'app.asset_url\')}}/fonts/MyriadBold.woff") format("woff"),url("{{config(\'app.asset_url\')}}/fonts/MyriadBold.otf") format("opentype");font-display:auto;font-style:normal;font-weight:normal}@font-face{font-family:"myriadreg";src:url("{{config(\'app.asset_url\')}}/fonts/MyriadReg.woff2") format("woff2"),url("{{config(\'app.asset_url\')}}/fonts/MyriadReg.woff") format("woff"),url("{{config(\'app.asset_url\')}}/fonts/MyriadReg.otf") format("opentype");font-display:auto;font-style:normal;font-weight:normal}</style><style type="text/css">p{margin:10px 0;padding:0}table{border-collapse:collapse}h1,h2,h3,h4,h5,h6{display:block;margin:0;padding:0}img, a img{border:0;height:auto;outline:none;text-decoration:none}body,#bodyTable,#bodyCell{height:100%;margin:0;padding:0;width:100%}.mcnPreviewText{display:none !important}#outlook a{padding:0}img{-ms-interpolation-mode:bicubic}table{mso-table-lspace:0;mso-table-rspace:0}.ReadMsgBody{width:100%}.ExternalClass{width:100%}p,a,li,td,blockquote{mso-line-height-rule:exactly}a[href^=tel],a[href^=sms]{color:inherit;cursor:default;text-decoration:none}p,a,li,td,body,table,blockquote{-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}.ExternalClass, .ExternalClass p, .ExternalClass td, .ExternalClass div, .ExternalClass span, .ExternalClass font{line-height:100%}a[x-apple-data-detectors]{color:inherit !important;text-decoration:none !important;font-size:inherit !important;font-family:inherit !important;font-weight:inherit !important;line-height:inherit !important}#bodyCell{padding:10px}.templateContainer{max-width:640px !important}a.mcnButton{display:block}.mcnImage,.mcnRetinaImage{vertical-align:bottom}.mcnTextContent{word-break:break-word}.mcnTextContent img{height:auto !important}.mcnDividerBlock{table-layout:fixed !important}body,#bodyTable{background-color:#FAFAFA}#bodyCell{border-top:0}.templateContainer{border:0}h1{color:#202020;font-family:Helvetica;font-size:26px;font-style:normal;font-weight:bold;line-height:125%;letter-spacing:normal;text-align:left}h2{color:#202020;font-family:Helvetica;font-size:22px;font-style:normal;font-weight:bold;line-height:125%;letter-spacing:normal;text-align:left}h3{color:#202020;font-family:Helvetica;font-size:20px;font-style:normal;font-weight:bold;line-height:125%;letter-spacing:normal;text-align:left}h4{color:#202020;font-family:Helvetica;font-size:18px;font-style:normal;font-weight:bold;line-height:125%;letter-spacing:normal;text-align:left}#templatePreheader{background-color:#fafafa;background-image:none;background-repeat:no-repeat;background-position:center;background-size:cover;border-top:0;border-bottom:0;padding-top:9px;padding-bottom:9px}#templatePreheader .mcnTextContent, #templatePreheader .mcnTextContent p{color:#656565;font-family:Helvetica;font-size:12px;line-height:normal;text-align:left}#templatePreheader .mcnTextContent a, #templatePreheader .mcnTextContent p a{color:#656565;font-weight:normal;text-decoration:underline}#templateHeader{background-color:#fff;background-image:none;background-repeat:no-repeat;background-position:center;background-size:cover;border-top:0;border-bottom:1px dashed #74d1f6;padding-top:9px;padding-bottom:0}#templateHeader .mcnTextContent, #templateHeader .mcnTextContent p{color:#202020;font-family:Helvetica;font-size:16px;line-height:normal;text-align:left}#templateHeader .mcnTextContent a, #templateHeader .mcnTextContent p a{color:#007C89;font-weight:normal;text-decoration:underline}#templateBody{background-color:#fff;background-image:none;background-repeat:no-repeat;background-position:center;background-size:cover;border-top:0;border-bottom:0;padding-top:px;padding-bottom:9px}#templateBody .mcnTextContent, #templateBody .mcnTextContent p{color:#202020;font-family:Helvetica;font-size:16px;line-height:normal;text-align:left}#templateBody .mcnTextContent a, #templateBody .mcnTextContent p a{color:#007C89;font-weight:normal;text-decoration:underline}#templateFooter{background-color:#fff;background-image:none;background-repeat:no-repeat;background-position:center;background-size:cover;border-top:1px dashed #74d1f6;border-bottom:0;padding-top:9px;padding-bottom:9px}#templateFooter .mcnTextContent, #templateFooter .mcnTextContent p{color:#656565;font-family:Helvetica;font-size:12px;line-height:normal;text-align:center}#templateFooter .mcnTextContent a, #templateFooter .mcnTextContent p a{color:#656565;font-weight:normal;text-decoration:underline}@media only screen and(min-width:768px){.templateContainer{width:640px !important}}@media only screen and(max-width: 480px){body,table,td,p,a,li,blockquote{-webkit-text-size-adjust:none !important}}@media only screen and(max-width: 480px){body{width:100% !important;min-width:100% !important}}@media only screen and(max-width: 480px){.mcnRetinaImage{max-width:100% !important}}@media only screen and(max-width: 480px){.mcnImage{width:100% !important}}@media only screen and(max-width: 480px){.mcnCartContainer,.mcnCaptionTopContent,.mcnRecContentContainer,.mcnCaptionBottomContent,.mcnTextContentContainer,.mcnBoxedTextContentContainer,.mcnImageGroupContentContainer,.mcnCaptionLeftTextContentContainer,.mcnCaptionRightTextContentContainer,.mcnCaptionLeftImageContentContainer,.mcnCaptionRightImageContentContainer,.mcnImageCardLeftTextContentContainer,.mcnImageCardRightTextContentContainer,.mcnImageCardLeftImageContentContainer,.mcnImageCardRightImageContentContainer{max-width:100% !important;width:100% !important}}@media only screen and(max-width: 480px){.mcnBoxedTextContentContainer{min-width:100% !important}}@media only screen and(max-width: 480px){.mcnImageGroupContent{padding:9px !important}}@media only screen and(max-width: 480px){.mcnCaptionLeftContentOuter .mcnTextContent, .mcnCaptionRightContentOuter .mcnTextContent{padding-top:9px !important}}@media only screen and(max-width: 480px){.mcnImageCardTopImageContent, .mcnCaptionBottomContent:last-child .mcnCaptionBottomImageContent, .mcnCaptionBlockInner .mcnCaptionTopContent:last-child .mcnTextContent{padding-top:18px !important}}@media only screen and(max-width: 480px){.mcnImageCardBottomImageContent{padding-bottom:9px !important}}@media only screen and(max-width: 480px){.mcnImageGroupBlockInner{padding-top:0 !important;padding-bottom:0 !important}}@media only screen and(max-width: 480px){.mcnImageGroupBlockOuter{padding-top:9px !important;padding-bottom:9px !important}}@media only screen and(max-width: 480px){.mcnTextContent,.mcnBoxedTextContentColumn{padding-right:18px !important;padding-left:18px !important}}@media only screen and(max-width: 480px){.mcnImageCardLeftImageContent,.mcnImageCardRightImageContent{padding-right:18px !important;padding-bottom:0 !important;padding-left:18px !important}}@media only screen and(max-width: 480px){.mcpreview-image-uploader{display:none !important;width:100% !important}}@media only screen and(max-width: 480px){h1{font-size:22px !important;line-height:125% !important}}@media only screen and(max-width: 480px){h2{font-size:20px !important;line-height:125% !important}}@media only screen and(max-width: 480px){h3{font-size:18px !important;line-height:125% !important}}@media only screen and(max-width: 480px){h4{font-size:16px !important;line-height:normal !important}}@media only screen and(max-width: 480px){.mcnBoxedTextContentContainer .mcnTextContent, .mcnBoxedTextContentContainer .mcnTextContent p{font-size:14px !important;line-height:normal !important}}@media only screen and(max-width: 480px){#templatePreheader{display:block !important}}@media only screen and(max-width: 480px){#templatePreheader .mcnTextContent, #templatePreheader .mcnTextContent p{font-size:14px !important;line-height:normal !important}}@media only screen and(max-width: 480px){#templateHeader .mcnTextContent, #templateHeader .mcnTextContent p{font-size:16px !important;line-height:normal !important}}@media only screen and(max-width: 480px){#templateBody .mcnTextContent, #templateBody .mcnTextContent p{font-size:16px !important;line-height:normal !important}}@media only screen and(max-width: 480px){#templateFooter .mcnTextContent, #templateFooter .mcnTextContent p{font-size:14px !important;line-height:normal !important}}table.transaction th{border-bottom:1px solid #000;text-align:left}</style></head><body><center><table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable"><tr><td align="center" valign="top" id="bodyCell"> <!--[if (gte mso 9)|(IE)]><table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;"><tr><td align="center" valign="top" width="600" style="width:600px;"> <![endif]--><table border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer"><tr><td valign="top" id="templateHeader"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;"><tbody class="mcnTextBlockOuter"><tr><td valign="top" class="mcnTextBlockInner" style="padding-top:9px;"> <!--[if mso]><table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;"><tr> <![endif]--> <!--[if mso]><td valign="top" width="210" style="width:210px;"> <![endif]--><table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:210px;" width="100%" class="mcnTextContentContainer"><tbody><tr><td valign="top" class="mcnTextContent" style="padding-top:0; padding-left:18px; padding-bottom:9px; padding-right:18px;"> <img data-file-id="1875424" height="58" src="{{config(\'app.asset_url\')}}/images/logo.72.png?v={{Str::random(6)}}" style="border: 0px ; width: 115px; height: 58px; margin: 0px;" width="115"></td></tr></tbody></table> <!--[if mso]></td> <![endif]--> <!--[if mso]><td valign="top" width="390" style="width:390px;"> <![endif]--><table align="right" border="0" cellpadding="0" cellspacing="0" style="max-width:390px;" width="100%" class="mcnTextContentContainer"><tbody><tr><td valign="top" class="mcnTextContent" style="padding-top:0; padding-left:18px; padding-bottom:9px; padding-right:0; padding-top: 30px"><div style="text-align: right;"><table align="right" border="0" cellpadding="0" cellspacing="0" style="max-width:390px;" width="100%" class="mcnTextContentContainer"><tbody><tr><td valign="top" style="padding-top:0;" class="mcnTextContent"> <img align="none" data-file-id="1875408" height="18" src="{{config(\'app.asset_url\')}}/images/phone.72.png?v={{Str::random(6)}}" style="border: 0px ; width: 19px; height: 18px;" width="19"></td><td valign="top" style="padding-top:0;" class="mcnTextContent"> <span style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; font-size:9.25pt; padding:0 10px 0 5px">020-760 50 40</span></td><td valign="top" style="padding-top:0;" class="mcnTextContent"> <img data-file-id="1875416" height="18" src="{{config(\'app.asset_url\')}}/images/mail.72.png?v={{Str::random(6)}}" style="border: 0px ; width: 19px; height: 18px;" width="19"></td><td valign="top" style="padding-top:0;" class="mcnTextContent"> <span style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; font-size:9.25pt; padding:0 10px 0 5px">info@fiber.nl</span></td><td valign="top" style="padding-top:0;" class="mcnTextContent"> <img data-file-id="1875420" height="18" src="{{config(\'app.asset_url\')}}/images/bookmark.72.png?v={{Str::random(6)}}" style="border: 0px ; width: 19px; height: 18px;" width="19"></td><td valign="top" style="padding-top:0;" class="mcnTextContent"> <span style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; font-size:9.25pt; padding-left:5px">www.fiber.nl</span></td></tr></tbody></table></div></td></tr></tbody></table> <!--[if mso]></td> <![endif]--> <!--[if mso]></tr></table> <![endif]--></td></tr></tbody></table></td></tr><tr><td valign="top" id="templateBody"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;"><tbody class="mcnTextBlockOuter"><tr><td valign="top" class="mcnTextBlockInner" style="padding-top:9px;"> <!--[if mso]><table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;"><tr> <![endif]--> <!--[if mso]><td valign="top" width="600" style="width:600px;"> <![endif]--><table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer"><tbody><tr><td valign="top" class="mcnTextContent" style="padding: 25px 18px 9px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; font-size: 11.25pt; line-height: normal;"><p style="margin-bottom: 30px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; line-height: normal;"> {{$user_fullname}}<br> {{$street}}<br> {{$address}}</p> <span style="font-size: 24px"> <strong>INGEBREKESTELLING</strong> </span><p style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; font-size: 1pt;"> &nbsp;</p><table border="0" cellpadding="0" cellspacing="0" role="presentation" style="width: 50%; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"><tbody><tr><td>Datum</td><td style="padding-left: 30px">{{$date}}</td></tr><tr><td>Klantnummer</td><td style="padding-left: 30px"> {{$customer_number}}</td></tr></tbody></table> &nbsp;<p style="margin-top: 20px; margin-bottom: 20px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; font-size: 11.25pt;"> Beste {{$user_fullname}},</p><p style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; font-size: 11.25pt;"> Wij hebben u eerder 2 herinneringen en een aanmaning gestuurd voor een openstaande factuur, waarvoor wij nog geen betaling hebben ontvangen. Het openstaande bedrag heeft betrekking op de onderstaande factuur inzake door Fiber NL aan u geleverde diensten (zie bijlage voor details van deze factuur).</p><table border="0" cellpadding="0" cellspacing="0" class="transaction" role="presentation" style="width: 100%; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"><tbody><tr style="padding: 5px 0; border-bottom: 1px solid #000;"><th>Factuur nr.</th><th>Factuurdatum</th><th>Betreft</th><th colspan="2" style="padding-left: 5px; text-align: right"> Bedrag</th></tr><tr style="padding: 5px 0;"><td>{{$invoice_number}}</td><td>{{$invoice_date}}</td><td>{{$concern}}</td><td style="background-color: #EEF1F0; padding-left: 5px;"> €</td><td align="right" style="background-color: #EEF1F0; padding-right: 5px;"> {{$amount}}</td></tr></tbody></table><p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"> Wij verzoeken u vriendelijk doch dringend het openstaande bedrag binnen 7 dagen over te maken op rekeningnummer <strong>NL30 RABO 0337 2353 41</strong> ten name XS Provider, onder vermelding van uw klantnummer en het factuurnummer.</p><p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"> <strong>Indien wij uw betaling niet binnen 7 dagen ontvangen sluiten wij uw diensten af en dragen wij de vordering over aan een incassobureau.</strong> Het incassobureau hanteert een minimumbedrag van € 40 voor incassokosten die dan extra in rekening wordt gebracht. Bij heraansluiting zullen ook kosten in rekening gebracht worden.</p><p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"> Laat het niet zover komen en betaal vandaag nog.</p><p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"> Mochten uw betaling en deze herinnering elkaar hebben gekruist, dan kunt u deze herinnering als niet verzonden beschouwen.</p><p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"> Met vriendelijke groet,</p> &nbsp;<p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;"> Klantenservice <br/> Fiber NL</p></td></tr></tbody></table> <!--[if mso]></td> <![endif]--> <!--[if mso]></tr></table> <![endif]--></td></tr></tbody></table></td></tr><tr><td valign="top" id="templateFooter"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;"><tbody class="mcnTextBlockOuter"><tr><td valign="top" class="mcnTextBlockInner" style="padding-top:9px;"> <!--[if mso]><table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;"><tr> <![endif]--> <!--[if mso]><td valign="top" width="600" style="width:600px;"> <![endif]--><table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer"><tbody><tr><td valign="top" class="mcnTextContent" style="padding-top:0; padding-right:18px; padding-bottom:9px; padding-left:18px;"><div style="text-align: center;"> <span style="font-size:9pt; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">Postadres: Postbus 60228, 1320AG Almere | Bezoekadres:<br> Transistorstraat 7, 1322CJ Almere | KvK-nummer: 73786950 | BTW-nummer: NL859664016B01</span></div></td></tr></tbody></table> <!--[if mso]></td> <![endif]--> <!--[if mso]></tr></table> <![endif]--></td></tr></tbody></table></td></tr></table> <!--[if (gte mso 9)|(IE)]></td></tr></table> <![endif]--></td></tr></table></center></body></html>',
                'created_at' => NULL,
                'updated_at' => '2020-12-23 04:05:46',
            ),
        );


        foreach ($items as $item) {
            DB::table('email_templates')->updateOrInsert([
                'id' => $item['id'],
                'tenant_id' => $item['tenant_id'],
                'type' => $item['type'],
            ], $item);
        }
    }
}
