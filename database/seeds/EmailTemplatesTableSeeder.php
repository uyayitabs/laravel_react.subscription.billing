<?php

use Illuminate\Database\Seeder;

class EmailTemplatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('email_templates')->delete();
        
        \DB::table('email_templates')->insert(array (
            0 => 
            array (
                'id' => 1,
                'tenant_id' => 7,
                'type' => 'm7.create_my_account',
                'from_name' => 'Fiber Nederland',
                'from_email' => 'noreply@fiber.nl',
                'bcc_email' => 'cdmigrations@xsprovider.nl',
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
<div style="box-sizing: border-box; width: 600px !important; margin: 0 auto; padding: 5px 32px; font-family: \'myriadbold\', \'Open Sans\', Arial, sans-serif; color: #74D1F6;"> <img src="{{ $asset_url }}/{{ $slug }}/logo.72.png?v={{Str::random(6)}}" alt="" style="max-width: 100%; width: 150px;"> </div>
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
<br /> <img src="{{ $asset_url }}/{{ $slug }}/phone.72.png?v={{Str::random(6)}}" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; max-width: 100%; margin-left: 16px; margin-right: 5px; height: 18px; vertical-align: top;"><span style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; line-height: 14pt; padding-top: 3px; padding-bottom: 3px; vertical-align: top;">020-760 50 40</Span> <img src="{{ $asset_url }}/{{ $slug }}/mail.72.png?v={{Str::random(6)}}" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; max-width: 100%; margin-left: 16px; margin-right: 5px; height: 18px; vertical-align: top;"><span style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; line-height: 14pt; padding-top: 3px; padding-bottom: 3px; vertical-align: top;">info@fiber.nl</Span> <img src="{{ $asset_url }}/{{ $slug }}/bookmark.72.png?v={{Str::random(6)}}" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; max-width: 100%; margin-left: 16px; margin-right: 5px; height: 18px; vertical-align: top;"><span style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; line-height: 14pt; padding-top: 3px; padding-bottom: 3px; vertical-align: top;">www.fiber.nl</Span>
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
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'tenant_id' => 8,
                'type' => 'm7.create_my_account',
                'from_name' => 'Stipte',
                'from_email' => 'noreply@stipte.nl',
                'bcc_email' => 'cdmigrations@xsprovider.nl',
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
border-top: 1px solid #e73028;
}
</Style>
</head>

<body style="padding: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; background-color: #f4f1ea; color: #74787e; height: 100%; hyphens: auto; line-height: 1.4; margin: 0; -moz-hyphens: auto; -ms-word-break: break-all; width: 100% !important; -webkit-hyphens: auto; -webkit-text-size-adjust: none; word-break: break-word; font-size: 11.25pt;">
<div style="border-bottom: 1px solid #e73028; background-color: #fff">
<div style="box-sizing: border-box; width: 600px !important; margin: 0 auto; padding: 30px 32px; font-family: \'myriadbold\', \'Open Sans\', Arial, sans-serif; color: #74D1F6;"> <img src="{{ $asset_url }}/{{ $slug }}/logo.72.png?v={{Str::random(6)}}" alt="" style="max-width: 100%; width: 200px;"> </div>
</div>
<table class="page-wrapper" style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; background: #FFFFFF; margin: 0 auto !important; padding: 0px 32px !important; width: 600px !important; ">
<tbody style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box;">
<tr style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F;">
<td class="content" style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; margin: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; padding: 16px 0;">
<h1 style="font-size: 28px; color: #424242; padding-bottom: 32px;">Uw TV pakket is klaar voor gebruik</h1>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;"> Beste {{ $user_fullname }},</P>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;"> Stipte levert TV van Canal Digitaal over internet. Deze dienst is zojuist voor u geactiveerd.</P>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;"> Let even op de volgende zaken, dan kunt u optimaal hiervan gebruik maken.</P>
<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">             Aansluiting van uw TV op de settop box           </h3>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;"> De settop box moet met een netwerkkabel (UTP) aangesloten zijn op Stipte wifi router, en met een HDMI kabel op uw TV. U kunt maximaal 5 settop boxen in uw woning aansluiten. </P>
<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">             Canal Digitaal TV App voor smartphone en tablet           </h3>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;"> U kunt binnen en buitenshuis in de hele EU gebruikmaken van de Canal Digitaal TV App op uw smartphone of tablet. Hierbij heeft u toegang tot een groot deel van de zenders. U kunt ook live TV kijken, uw opnames plannen en bekijken, of streamen naar een Google Chromecast. Handig toch? </P>
<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">             Installeer de app in een handomdraai           </h3>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;"> Ga naar de <a href="https://itunes.apple.com/nl/app/online.nl/id1008027388?mt=8">App Store</a> (voor iOS) of <a href="https://play.google.com/store/apps/details?id=nl.streamgroup.canaldigital&hl=nl">Google Play</a> (voor Android), en installeer de Canal Digitaal TV App op uw telefoon of tablet.
<br /> Open de Canal Digitaal TV App, en log hiermee in:
<br /> </P>
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
<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">             TV kijken via PC           </h3>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;"> U kunt ook via uw PC of laptop TV kijken binnen de hele EU, door in te loggen met dezelfde inloggegevens als hierboven. Ga hiervoor naar <a href="https://login.canaldigitaal.nl/authenticate?redirect_uri=https%3A%2F%2Flivetv.canaldigitaal.nl%2Fauth.aspx&response_type=code&scope=TVE&client_id=StreamGroup">https://livetv.canaldigitaal.nl/</a>. </P>
<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">             TV kijken op uw Smart TV           </h3>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;"> Ook sommige Smart TV’s zijn geschikt om zonder settop box TV te kijken via een app op de Smart TV. Ook hiervoor kunt u bovenstaande inloggevens gebruiken. Weten of uw TV hiervoor geschikt is? <a href="https://www.canaldigitaal.nl/klantenservice/alles-over/canal-digitaal-tv-app/canal-digitaal-smart-tv-app/geschikte-smart-tvs/">Bekijk dan deze lijst</a>. </P>
<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">             Heeft u nog vragen, of lukt het inloggen niet?           </h3>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;"> We helpen u graag verder. U kunt ons bellen op 0320 29 44 44 van maandag tot en met zaterdag van 09:00 tot 17:30. U kunt ook een email sturen aan <a href="mailto:administratie@stipte.nl">administratie@stipte.nl</a>. </P>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;"> Met vriendelijk groet,
<br />
<br /> Stipte </P>
</TD>
</TR>
</Tbody>
<tfoot style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box;">
<tr class="footer-row" style="text-align: center;">
<td style="padding: 0; margin: 0; color: #292D2F; box-sizing: border-box; font-family: \'myriadbold\', \'Open Sans\', Arial, sans-serif !important; font-size: 9.25pt; vertical-align: bottom; text-align: right;"></td>
</tr>
<tr class="footer-row" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; font-size: 9pt !important; text-align: center;">
<td class="footer-column" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; padding-top: 10pt; padding-bottom: 10pt; color: #5C5E5E !important; border-top: 1px solid #e73028; line-height: 22px;">
<br /> <img src="{{ $asset_url }}/{{ $slug }}/phone.72.png?v={{Str::random(6)}}" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; max-width: 100%; margin-left: 16px; margin-right: 5px; height: 18px; vertical-align: top;"> <span style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; line-height: 14pt; padding-top: 3px; padding-bottom: 3px; vertical-align: top;">0320 29 44 44</Span> <img src="{{ $asset_url }}/{{ $slug }}/mail.72.png?v={{Str::random(6)}}" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; max-width: 100%; margin-left: 16px; margin-right: 5px; height: 18px; vertical-align: top;"> <span style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; line-height: 14pt; padding-top: 3px; padding-bottom: 3px; vertical-align: top;">administratie@stipte.nl</Span> <img src="{{ $asset_url }}/{{ $slug }}/bookmark.72.png?v={{Str::random(6)}}" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; max-width: 100%; margin-left: 16px; margin-right: 5px; height: 18px; vertical-align: top;"> <span style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; line-height: 14pt; padding-top: 3px; padding-bottom: 3px; vertical-align: top;">www.stipte.nl</Span>
<br />
<br /> Postadres: Postbus 158, 8200AD, Lelystad | KvK-nummer: 73786950
<br />
<br /> </TD>
</TR>
</Tfoot>
</Table>
</body>

</html>',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'tenant_id' => 7,
                'type' => 'invoice',
                'from_name' => 'Fiber Nederland',
                'from_email' => 'noreply@fiber.nl',
                'bcc_email' => 'mark@f2x.nl,cdmigrations@xsprovider.nl',
            'subject' => '@if ($totalWithVAT < 0) Creditfactuur @else Factuur @endif bij {{$tenantName}} voor de maand {{$date}}',
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
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'tenant_id' => 8,
                'type' => 'invoice',
                'from_name' => 'Stipte',
                'from_email' => 'noreply@stipte.nl',
                'bcc_email' => 'mark@f2x.nl,cdmigrations@xsprovider.nl',
            'subject' => '@if ($totalWithVAT < 0) Creditfactuur @else Factuur @endif bij {{$tenantName}} voor de maand {{$date}}',
                'body_html' => '<!DOCTYPE html>
<html>

<head>
<title>Stipte factuur</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!--[if mso]>   <style type=”text/css”>   .fallback-text {   font-family: Arial, sans-serif;   }   </style>   <![endif]-->
<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans" />
<style>
@font-face {
font-family: "gothamroundedbold";
src: url("{{config(\'app.asset_url\')}}/fonts/GothamRounded-Bold.ttf") format("truetype"), url("{{config(\'app.asset_url\')}}/fonts/GothamRounded-Bold.otf") format("opentype");
font-display: auto;
font-style: normal;
font-weight: normal;
}

@font-face {
font-family: "gothamroundedreg";
src:url("{{config(\'app.asset_url\')}}/fonts/GothamRounded-Book.ttf") format("truetype"), url("{{config(\'app.asset_url\')}}/fonts/GothamRounded-Book.otf") format("opentype");
font-display: auto;
font-style: normal;
font-weight: normal;
}
</style>
<style>
* {
padding: 0;
margin: 0;
font-family: \'gothamroundedreg\', \'Open Sans\', Arial, sans-serif !important;
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
font-family: \'gothamroundedbold\', \'Open Sans\', Arial, sans-serif !important;
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
border-bottom: 1px dashed #F9261F;
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
border-top: 1px dashed #F9261F;
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
<td style="text-align: left; vertical-align: bottom;"> <img src="{{config(\'app.asset_url\')}}/stipte/logo.72.png?v={{Str::random(6)}}" alt="" style="width: 115px;"> </td>
<td class="header-info"> <img src="{{config(\'app.asset_url\')}}/stipte/phone.72.png?v={{Str::random(6)}}"><span>0320-29 44 44</span> <img src="{{config(\'app.asset_url\')}}/stipte/mail.72.png?v={{Str::random(6)}}"><span>administratie@stipte.nl</span> <img src="{{config(\'app.asset_url\')}}/stipte/bookmark.72.png?v={{Str::random(6)}}"><span>stipte.nl</span> </td>
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
<p>Bij deze ontvangt u de factuur voor uw diensten bij Stipte voor de maand <strong>{{isoFormatCarbonDate($invoice_due_date, \'MMMM Y\', false)}}</strong>.</p>
<p>In het bijgesloten PDF document kunt u de details vinden, met specificatie en toelichting.</p>
<p>Heeft u nog vragen over uw factuur? Mail ons dan op <a href="mailto:administratie@stipte.nl">administratie@stipte.nl</a> of bel naar 0320-29 44 44.</p>
<p>Met vriendelijke groet,</p>
<p>De klantenservice van Stipte</p>
<p class="note">PDF documenten kunt u lezen met de gratis Acrobat Reader, die u kunt downloaden op <a href="https://get.adobe.com/nl/reader" _target="blank">adobe.nl</a>.</p>
</td>
</tr>
</tbody>
<tfoot>
<tr class="footer-row">
<td class="footer-column">Postadres: Postbus 158, 8200AD Lelystad | KvK-nummer: 73786950 | BTW-nummer: NL859664016B01</td>
</tr>
</tfoot>
</table>
</body>

</html>',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'tenant_id' => 9,
                'type' => 'invoice',
                'from_name' => 'Holland Glas',
                'from_email' => 'noreply@hollandsglas.nl',
                'bcc_email' => 'mark@f2x.nl,cdmigrations@xsprovider.nl',
            'subject' => '@if ($totalWithVAT < 0) Creditfactuur @else Factuur @endif bij {{$tenantName}} voor de maand {{$date}}',
                'body_html' => '<!DOCTYPE html>
<html>

<head>
<title>Holland Glas factuur</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!--[if mso]>   <style type=”text/css”>   .fallback-text {   font-family: Arial, sans-serif;   }   </style>   <![endif]-->
<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans" />
<style>
@font-face {
font-family: "sourcesansprobold";
src: url("{{config(\'app.asset_url\')}}/fonts/SourceSansPro-Bold.ttf") format("truetype");
font-display: auto;
font-style: normal;
font-weight: normal;
}

@font-face {
font-family: "sourcesansproreg";
src: url("{{config(\'app.asset_url\')}}/fonts/SourceSansPro-Regular.ttf") format("truetype")
font-display: auto;
font-style: normal;
font-weight: normal;
}
</style>
<style>
* {
padding: 0;
margin: 0;
font-family: \'sourcesansproreg\', \'Open Sans\', Arial, sans-serif !important;
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
font-family: \'sourcesansprobold\', \'Open Sans\', Arial, sans-serif !important;
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
<td style="text-align: left; vertical-align: bottom;"> <img src="{{config(\'app.asset_url\')}}/holland-glas/logo.72.png?v={{Str::random(6)}}" alt="" style="width: 160px;"> </td>
<td class="header-info"> <img src="{{config(\'app.asset_url\')}}/holland-glas/phone.72.png?v={{Str::random(6)}}"><span>010 760 33 60</span> <img src="{{config(\'app.asset_url\')}}/holland-glas/mail.72.png?v={{Str::random(6)}}"><span>hallo@hollandsglas.nl</span> <img src="{{config(\'app.asset_url\')}}/holland-glas/bookmark.72.png?v={{Str::random(6)}}"><span>hollandglas.nl</span> </td>
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
<p>Bij deze ontvangt u de factuur voor uw diensten bij Holland Glas voor de maand <strong>{{isoFormatCarbonDate($invoice_due_date, \'MMMM Y\', false)}}</strong>.</p>
<p>In het bijgesloten PDF document kunt u de details vinden, met specificatie en toelichting.</p>
<p>Heeft u nog vragen over uw factuur? Mail ons dan op <a href="mailto:hallo@hollandsglas.nl">hallo@hollandsglas.nl</a> of bel naar 010 760 33 60.</p>
<p>Met vriendelijke groet,</p>
<p>De klantenservice van Holland Glas</p>
<p class="note">PDF documenten kunt u lezen met de gratis Acrobat Reader, die u kunt downloaden op <a href="https://get.adobe.com/nl/reader" _target="blank">adobe.nl</a>.</p>
</td>
</tr>
</tbody>
<tfoot>
<tr class="footer-row">
<td class="footer-column">Postadres: Postbus 158, 8200AD Lelystad | KvK-nummer: 73786950 | BTW-nummer: NL859664016B01</td>
</tr>
</tfoot>
</table>
</body>

</html>',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'tenant_id' => 7,
                'type' => 'brightblue.create_account',
                'from_name' => 'Fiber Nederland',
                'from_email' => 'noreply@fiber.nl',
                'bcc_email' => 'mark@f2x.nl,cdmigrations@xsprovider.nl',
                'subject' => 'Uw login gegevens voor Fiber Start TV',
                'body_html' => '<!doctype html>
<html style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F;">
<head style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F;">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F;">
Mail van BrightBlue</Title>
<meta name="viewport" content="width=device-width, initial-scale=1" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F;">
<!--[if mso]>
<style type=&rdquo;text/css&rdquo;>
.fallback-text {
font-family: Arial, sans-serif;
}
</style>
<![endif]-->
<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
<style>
@font-face {
font-family: "myriadbold";
src: url("{{$asset_url}}/fonts/MyriadBold.woff2") format("woff2"), url("{{$asset_url}}/fonts/MyriadBold.woff") format("woff"), url("{{$asset_url}}/fonts/MyriadBold.otf") format("opentype");
font-display: auto;
font-style: normal;
font-weight: normal;
}

@font-face {
font-family: "myriadreg";
src: url("https://static.f2x.nl/fonts/MyriadReg.woff2") format("woff2"), url("https://static.f2x.nl/fonts/MyriadReg.woff") format("woff"), url("https://static.f2x.nl/fonts/MyriadReg.otf") format("opentype");
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

h1, h2, h3, h4, h5, h6,
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
<body style="padding: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; background-color: #F7F7F7; color: #74787e; height: 100%; hyphens: auto; line-height: 1.4; margin: 0; -moz-hyphens: auto; -ms-word-break: break-all; width: 100% !important; -webkit-hyphens: auto; -webkit-text-size-adjust: none; word-break: break-word; font-size: 11.25pt;">
<div style="border-bottom: 4px dotted #74D1F6; background-color: #fff">
<div style="box-sizing: border-box; width: 600px !important; margin: 0 auto; padding: 5px 32px; font-family: \'myriadbold\', \'Open Sans\', Arial, sans-serif; color: #74D1F6;">
<img src="{{ $asset_url }}/fiber/logo.72.png?v={{Str::random(6)}}" alt="" style="max-width: 100%; width: 150px;">
</div>
</div>
<table class="page-wrapper"
style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; background: #FFFFFF; margin: 0 auto !important; padding: 0px 32px !important; width: 600px !important; ">
<tbody style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box;">
<tr style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F;">
<td class="content"
style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; margin: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; padding: 16px 0;">

<h1 style="font-size: 28px; color: #74D1F6; padding-bottom: 32px;">Uw Fiber Start TV pakket is actief</h1>

<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
Beste {{ $userFullname }},</p>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
</p>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
U heeft een Start pakket bij Fiber NL. Dit hebben we zojuist voor u geactiveerd. Hieronder leest u hoe u het verder werkt.
</p>

<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">
Installatie van de settop box
</h3>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
U ontvangt binnenkort een pakketje van ons, met daarin de settop box, en een handige stap-voor-stap installatie handleiding.
</p>

<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">
Activatie code voor de settop box
</h3>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
Als u straks de settop box installeert, moet u een activatie code invullen. Dat is de volgende code:
</p>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
{{trim(chunk_split($activationCode, 4, " "))}}
</p>

<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">
PIN code voor toegangscontrole
</h3>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
Op de settop box kunt u toegangscontrole instellen. Daarvoor heeft u de PIN code nodig van de hoofdgebruiker. Dat is de volgende PIN code:
</p>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
{{$temporaryPin}}
</p>

<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">
Installeer alvast de Fiber TV app
</h3>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
U kunt al TV kijken via de Fiber TV app.
Ga naar de App Store (voor iOS) en zoek op "Fiber TV" (gemaakt door Teleplaza Services) of gebruik deze link voor <a href="https://play.google.com/store/apps/details?id=nl.fiber.nivel&hl=nl">Google Play</a> (voor Android), en installeer de Fiber TV App op uw telefoon of tablet. <br />
Open de Fiber TV App, en gebruik weer dezelfde activatie code: <br />
</p>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
{{trim(chunk_split($activationCode, 4, " "))}}
</p>

<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
Bewaar deze gegevens goed.
</p>

<!--
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
<b>1.</b> Ga naar de App Store of Google Play, en installeer de Canal Digitaal TV App op uw telefoon of tablet.
</p>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
<b>2.</b> Open de Canal Digitaal TV App, en login met uw gebruikersnaam en wachtwoord. Bewaar deze gegevens goed!
</p>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
<b>3.</b> Kunt u TV kunt kijken met de Canal Digitaal TV app? Dan kunt u de Fiber TV App verwijderen.
</p>
-->

<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">
Heeft u nog vragen, of lukt iets niet?
</h3>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
We helpen u graag verder. U kunt ons bellen op 020 – 760 50 40 van maandag tot en met zaterdag van 09:00 tot 17:30.
U kunt ook een email sturen aan <a href="mailto:info@fiber.nl">info@fiber.nl</a>.
</p>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
Met vriendelijk groet, <br />
<br />
Fiber Nederland
</p>
</td>
</tr>
</tbody>
<tfoot style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box;">
<tr class="footer-row" style="text-align: center;">
<td style="padding: 0; margin: 0; color: #292D2F; box-sizing: border-box; font-family: \'myriadbold\', \'Open Sans\', Arial, sans-serif !important; font-size: 9.25pt; vertical-align: bottom; text-align: right;">
</td>
</tr>
<tr class="footer-row" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; font-size: 9pt !important; text-align: center;">
<td class="footer-column" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; padding-top: 10pt; padding-bottom: 10pt; color: #5C5E5E !important; border-top: 1px dashed #74D1F6; line-height: 22px;">
<br />
<img src="{{ $asset_url }}/fiber/phone.png?v={{Str::random(6)}}" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; max-width: 100%; margin-left: 16px; margin-right: 5px; height: 18px; vertical-align: top;">
<span style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; line-height: 14pt; padding-top: 3px; padding-bottom: 3px; vertical-align: top;">020-760 50 40</span>
<img src="{{ $asset_url }}/fiber/mail.png?v={{Str::random(6)}}" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; max-width: 100%; margin-left: 16px; margin-right: 5px; height: 18px; vertical-align: top;">
<span style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; line-height: 14pt; padding-top: 3px; padding-bottom: 3px; vertical-align: top;">info@fiber.nl</span>
<img src="{{ $asset_url }}/fiber/bookmark.png?v={{Str::random(6)}}" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; max-width: 100%; margin-left: 16px; margin-right: 5px; height: 18px; vertical-align: top;">
<span style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; line-height: 14pt; padding-top: 3px; padding-bottom: 3px; vertical-align: top;">www.fiber.nl</span>
<br /><br />
Postadres: Postbus 60228, 1320AG Almere | Bezoekadres: Transistorstraat 7, 1322CJ Almere <br /> KvK-nummer: 73786950 | BTW-nummer: NL859664016B01
<br /><br />
</td>
</tr>
</tfoot>
</table>
</body>
</html>',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'tenant_id' => 7,
                'type' => 'brightblue.new_activation_code',
                'from_name' => 'Fiber Nederland',
                'from_email' => 'noreply@fiber.nl',
                'bcc_email' => 'mark@f2x.nl,cdmigrations@xsprovider.nl',
                'subject' => 'Uw login gegevens voor Fiber Start TV',
                'body_html' => '<!doctype html>
<html style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F;">
<head style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F;">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F;">
Mail van BrightBlue</Title>
<meta name="viewport" content="width=device-width, initial-scale=1" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F;">
<!--[if mso]>
<style type=&rdquo;text/css&rdquo;>
.fallback-text {
font-family: Arial, sans-serif;
}
</style>
<![endif]-->
<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
<style>
@font-face {
font-family: "myriadbold";
src: url("{{$asset_url}}/fonts/MyriadBold.woff2") format("woff2"), url("{{$asset_url}}/fonts/MyriadBold.woff") format("woff"), url("{{$asset_url}}/fonts/MyriadBold.otf") format("opentype");
font-display: auto;
font-style: normal;
font-weight: normal;
}

@font-face {
font-family: "myriadreg";
src: url("https://static.f2x.nl/fonts/MyriadReg.woff2") format("woff2"), url("https://static.f2x.nl/fonts/MyriadReg.woff") format("woff"), url("https://static.f2x.nl/fonts/MyriadReg.otf") format("opentype");
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

h1, h2, h3, h4, h5, h6,
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
<body style="padding: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; background-color: #F7F7F7; color: #74787e; height: 100%; hyphens: auto; line-height: 1.4; margin: 0; -moz-hyphens: auto; -ms-word-break: break-all; width: 100% !important; -webkit-hyphens: auto; -webkit-text-size-adjust: none; word-break: break-word; font-size: 11.25pt;">
<div style="border-bottom: 4px dotted #74D1F6; background-color: #fff">
<div style="box-sizing: border-box; width: 600px !important; margin: 0 auto; padding: 5px 32px; font-family: \'myriadbold\', \'Open Sans\', Arial, sans-serif; color: #74D1F6;">
<img src="{{ $asset_url }}/fiber/logo.72.png?v={{Str::random(6)}}" alt="" style="max-width: 100%; width: 150px;">
</div>
</div>
<table class="page-wrapper"
style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; background: #FFFFFF; margin: 0 auto !important; padding: 0px 32px !important; width: 600px !important; ">
<tbody style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box;">
<tr style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F;">
<td class="content"
style="font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; margin: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; padding: 16px 0;">

<h1 style="font-size: 28px; color: #74D1F6; padding-bottom: 32px;">Uw Fiber Start TV pakket is actief</h1>

<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
Beste {{ $userFullname }},</p>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
</p>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
U heeft een Start pakket bij Fiber NL. Dit hebben we zojuist voor u geactiveerd. Hieronder leest u hoe u het verder werkt.
</p>

<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">
Installatie van de settop box
</h3>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
U ontvangt binnenkort een pakketje van ons, met daarin de settop box, en een handige stap-voor-stap installatie handleiding.
</p>

<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">
Activatie code voor de settop box
</h3>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
Als u straks de settop box installeert, moet u een activatie code invullen. Dat is de volgende code:
</p>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
{{trim(chunk_split($activationCode, 4, " "))}}
</p>

<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">
PIN code voor toegangscontrole
</h3>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
Op de settop box kunt u toegangscontrole instellen. Daarvoor heeft u de PIN code nodig van de hoofdgebruiker. Dat is de volgende PIN code:
</p>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
{{$temporaryPin}}
</p>

<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">
Installeer alvast de Fiber TV app
</h3>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
U kunt al TV kijken via de Fiber TV app.
Ga naar de App Store (voor iOS) en zoek op "Fiber TV" (gemaakt door Teleplaza Services) of gebruik deze link voor <a href="https://play.google.com/store/apps/details?id=nl.fiber.nivel&hl=nl">Google Play</a> (voor Android), en installeer de Fiber TV App op uw telefoon of tablet. <br />
Open de Fiber TV App, en gebruik weer dezelfde activatie code: <br />
</p>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
{{trim(chunk_split($activationCode, 4, " "))}}
</p>

<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
Bewaar deze gegevens goed.
</p>

<!--
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
<b>1.</b> Ga naar de App Store of Google Play, en installeer de Canal Digitaal TV App op uw telefoon of tablet.
</p>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
<b>2.</b> Open de Canal Digitaal TV App, en login met uw gebruikersnaam en wachtwoord. Bewaar deze gegevens goed!
</p>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
<b>3.</b> Kunt u TV kunt kijken met de Canal Digitaal TV app? Dan kunt u de Fiber TV App verwijderen.
</p>
-->

<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">
Heeft u nog vragen, of lukt iets niet?
</h3>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
We helpen u graag verder. U kunt ons bellen op 020 – 760 50 40 van maandag tot en met zaterdag van 09:00 tot 17:30.
U kunt ook een email sturen aan <a href="mailto:info@fiber.nl">info@fiber.nl</a>.
</p>
<p style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
Met vriendelijk groet, <br />
<br />
Fiber Nederland
</p>
</td>
</tr>
</tbody>
<tfoot style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box;">
<tr class="footer-row" style="text-align: center;">
<td style="padding: 0; margin: 0; color: #292D2F; box-sizing: border-box; font-family: \'myriadbold\', \'Open Sans\', Arial, sans-serif !important; font-size: 9.25pt; vertical-align: bottom; text-align: right;">
</td>
</tr>
<tr class="footer-row" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; font-size: 9pt !important; text-align: center;">
<td class="footer-column" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; padding-top: 10pt; padding-bottom: 10pt; color: #5C5E5E !important; border-top: 1px dashed #74D1F6; line-height: 22px;">
<br />
<img src="{{ $asset_url }}/fiber/phone.png?v={{Str::random(6)}}" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; max-width: 100%; margin-left: 16px; margin-right: 5px; height: 18px; vertical-align: top;">
<span style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; line-height: 14pt; padding-top: 3px; padding-bottom: 3px; vertical-align: top;">020-760 50 40</span>
<img src="{{ $asset_url }}/fiber/mail.png?v={{Str::random(6)}}" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; max-width: 100%; margin-left: 16px; margin-right: 5px; height: 18px; vertical-align: top;">
<span style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; line-height: 14pt; padding-top: 3px; padding-bottom: 3px; vertical-align: top;">info@fiber.nl</span>
<img src="{{ $asset_url }}/fiber/bookmark.png?v={{Str::random(6)}}" style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; max-width: 100%; margin-left: 16px; margin-right: 5px; height: 18px; vertical-align: top;">
<span style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; line-height: 14pt; padding-top: 3px; padding-bottom: 3px; vertical-align: top;">www.fiber.nl</span>
<br /><br />
Postadres: Postbus 60228, 1320AG Almere | Bezoekadres: Transistorstraat 7, 1322CJ Almere <br /> KvK-nummer: 73786950 | BTW-nummer: NL859664016B01
<br /><br />
</td>
</tr>
</tfoot>
</table>
</body>
</html>',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}