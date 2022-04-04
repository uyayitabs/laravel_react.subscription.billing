<?php

use App\EmailTemplate;
use App\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BeeSmartM7CreateAccountTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $existingDataAttr = [];
        $newDataArr = [
            'tenant_id' => 15,
            'type' => 'm7.create_my_account',
            'from_name' => 'BeeSmart Telecom B.V.',
            'from_email' => 'noreply@fiber.nl',
            'bcc_email' => 'mark@f2x.nl,cdmigrations@xsprovider.nl,marisa.vanvelzen@xsprovider.nl',
            'subject' => 'Uw login gegevens voor Canal Digitaal',
            'body_html' => '<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<!--[if gte mso 15]>
<xml>
<o:OfficeDocumentSettings> <o:AllowPNG /> <o:PixelsPerInch>96</o:PixelsPerInch> </o:OfficeDocumentSettings>
</xml>
<![endif]-->
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title></title>
<style>
@font-face {
font-family: "myriadbold";
src: url("{{config(\'app.asset_url\')}}/fonts/MyriadBold.woff2") format("woff2"), url("{{config(\'app.asset_url\')}}/fonts/MyriadBold.woff") format("woff"),
url("{{config(\'app.asset_url\')}}/fonts/MyriadBold.otf") format("opentype");
font-display: auto;
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
<style type="text/css">
body {
font-family: "myriadreg", "Open Sans", Arial, sans-serif;
}
p {
margin: 10px 0;
padding: 0;
}
table {
border-collapse: collapse;
}
h1,
h2,
h3,
h4,
h5,
h6 {
display: block;
margin: 0;
padding: 0;
}
img,
a img {
border: 0;
height: auto;
outline: none;
text-decoration: none;
}
body,
#bodyTable,
#bodyCell {
height: 100%;
margin: 0;
padding: 0;
width: 100%;
}
.mcnPreviewText {
display: none !important;
}
#outlook a {
padding: 0;
}
img {
-ms-interpolation-mode: bicubic;
}
table {
mso-table-lspace: 0;
mso-table-rspace: 0;
}
.ReadMsgBody {
width: 100%;
}
.ExternalClass {
width: 100%;
}
p,
a,
li,
td,
blockquote {
mso-line-height-rule: exactly;
}
a[href^="tel"],
a[href^="sms"] {
color: inherit;
cursor: default;
text-decoration: none;
}
p,
a,
li,
td,
body,
table,
blockquote {
-ms-text-size-adjust: 100%;
-webkit-text-size-adjust: 100%;
}
.ExternalClass,
.ExternalClass p,
.ExternalClass td,
.ExternalClass div,
.ExternalClass span,
.ExternalClass font {
line-height: 100%;
}
a[x-apple-data-detectors] {
color: inherit !important;
text-decoration: none !important;
font-size: inherit !important;
font-family: inherit !important;
font-weight: inherit !important;
line-height: inherit !important;
}
#bodyCell {
padding: 10px;
}
.templateContainer {
max-width: 640px !important;
}
a.mcnButton {
display: block;
}
.mcnImage,
.mcnRetinaImage {
vertical-align: bottom;
}
.mcnTextContent {
word-break: break-word;
}
.mcnTextContent img {
height: auto !important;
}
.mcnDividerBlock {
table-layout: fixed !important;
}
body,
#bodyTable {
background-color: #fafafa;
}
#bodyCell {
border-top: 0;
}
.templateContainer {
border: 0;
}
h1 {
color: #202020;
font-family: Helvetica;
font-size: 26px;
font-style: normal;
font-weight: bold;
line-height: 125%;
letter-spacing: normal;
text-align: left;
}
h2 {
color: #202020;
font-family: Helvetica;
font-size: 22px;
font-style: normal;
font-weight: bold;
line-height: 125%;
letter-spacing: normal;
text-align: left;
}
h3 {
color: #202020;
font-family: Helvetica;
font-size: 20px;
font-style: normal;
font-weight: bold;
line-height: 125%;
letter-spacing: normal;
text-align: left;
}
h4 {
color: #202020;
font-family: Helvetica;
font-size: 18px;
font-style: normal;
font-weight: bold;
line-height: 125%;
letter-spacing: normal;
text-align: left;
}
#templatePreheader {
background-color: #fafafa;
background-image: none;
background-repeat: no-repeat;
background-position: center;
background-size: cover;
border-top: 0;
border-bottom: 0;
padding-top: 9px;
padding-bottom: 9px;
}
#templatePreheader .mcnTextContent,
#templatePreheader .mcnTextContent p {
color: #656565;
font-family: Helvetica;
font-size: 12px;
line-height: normal;
text-align: left;
}
#templatePreheader .mcnTextContent a,
#templatePreheader .mcnTextContent p a {
color: #656565;
font-weight: normal;
text-decoration: underline;
}
#templateHeader {
background-color: #fff;
background-image: none;
background-repeat: no-repeat;
background-position: center;
background-size: cover;
border-top: 0;
border-bottom: 1px dashed #faba00;
padding-top: 9px;
padding-bottom: 0;
}
#templateHeader .mcnTextContent,
#templateHeader .mcnTextContent p {
color: #202020;
font-family: Helvetica;
font-size: 16px;
line-height: normal;
text-align: left;
}
#templateHeader .mcnTextContent a,
#templateHeader .mcnTextContent p a {
color: #007c89;
font-weight: normal;
text-decoration: underline;
}
#templateBody {
background-color: #fff;
background-image: none;
background-repeat: no-repeat;
background-position: center;
background-size: cover;
border-top: 0;
border-bottom: 0;
padding-top: px;
padding-bottom: 9px;
}
#templateBody .mcnTextContent,
#templateBody .mcnTextContent p {
color: #202020;
font-family: Helvetica;
font-size: 16px;
line-height: normal;
text-align: left;
}
#templateBody .mcnTextContent a,
#templateBody .mcnTextContent p a {
color: #007c89;
font-weight: normal;
text-decoration: underline;
}
#templateFooter {
background-color: #fff;
background-image: none;
background-repeat: no-repeat;
background-position: center;
background-size: cover;
border-top: 1px dashed #faba00;
border-bottom: 0;
padding-top: 9px;
padding-bottom: 9px;
}
#templateFooter .mcnTextContent,
#templateFooter .mcnTextContent p {
color: #656565;
font-family: Helvetica;
font-size: 12px;
line-height: normal;
text-align: center;
}
#templateFooter .mcnTextContent a,
#templateFooter .mcnTextContent p a {
color: #656565;
font-weight: normal;
text-decoration: underline;
}
@media only screen and(min-width:768px) {
.templateContainer {
width: 640px !important;
}
}
@media only screen and(max-width: 480px) {
body,
table,
td,
p,
a,
li,
blockquote {
-webkit-text-size-adjust: none !important;
}
}
@media only screen and(max-width: 480px) {
body {
width: 100% !important;
min-width: 100% !important;
}
}
@media only screen and(max-width: 480px) {
.mcnRetinaImage {
max-width: 100% !important;
}
}
@media only screen and(max-width: 480px) {
.mcnImage {
width: 100% !important;
}
}
@media only screen and(max-width: 480px) {
.mcnCartContainer,
.mcnCaptionTopContent,
.mcnRecContentContainer,
.mcnCaptionBottomContent,
.mcnTextContentContainer,
.mcnBoxedTextContentContainer,
.mcnImageGroupContentContainer,
.mcnCaptionLeftTextContentContainer,
.mcnCaptionRightTextContentContainer,
.mcnCaptionLeftImageContentContainer,
.mcnCaptionRightImageContentContainer,
.mcnImageCardLeftTextContentContainer,
.mcnImageCardRightTextContentContainer,
.mcnImageCardLeftImageContentContainer,
.mcnImageCardRightImageContentContainer {
max-width: 100% !important;
width: 100% !important;
}
}
@media only screen and(max-width: 480px) {
.mcnBoxedTextContentContainer {
min-width: 100% !important;
}
}
@media only screen and(max-width: 480px) {
.mcnImageGroupContent {
padding: 9px !important;
}
}
@media only screen and(max-width: 480px) {
.mcnCaptionLeftContentOuter .mcnTextContent,
.mcnCaptionRightContentOuter .mcnTextContent {
padding-top: 9px !important;
}
}
@media only screen and(max-width: 480px) {
.mcnImageCardTopImageContent,
.mcnCaptionBottomContent:last-child .mcnCaptionBottomImageContent,
.mcnCaptionBlockInner .mcnCaptionTopContent:last-child .mcnTextContent {
padding-top: 18px !important;
}
}
@media only screen and(max-width: 480px) {
.mcnImageCardBottomImageContent {
padding-bottom: 9px !important;
}
}
@media only screen and(max-width: 480px) {
.mcnImageGroupBlockInner {
padding-top: 0 !important;
padding-bottom: 0 !important;
}
}
@media only screen and(max-width: 480px) {
.mcnImageGroupBlockOuter {
padding-top: 9px !important;
padding-bottom: 9px !important;
}
}
@media only screen and(max-width: 480px) {
.mcnTextContent,
.mcnBoxedTextContentColumn {
padding-right: 18px !important;
padding-left: 18px !important;
}
}
@media only screen and(max-width: 480px) {
.mcnImageCardLeftImageContent,
.mcnImageCardRightImageContent {
padding-right: 18px !important;
padding-bottom: 0 !important;
padding-left: 18px !important;
}
}
@media only screen and(max-width: 480px) {
.mcpreview-image-uploader {
display: none !important;
width: 100% !important;
}
}
@media only screen and(max-width: 480px) {
h1 {
font-size: 22px !important;
line-height: 125% !important;
}
}
@media only screen and(max-width: 480px) {
h2 {
font-size: 20px !important;
line-height: 125% !important;
}
}
@media only screen and(max-width: 480px) {
h3 {
font-size: 18px !important;
line-height: 125% !important;
}
}
@media only screen and(max-width: 480px) {
h4 {
font-size: 16px !important;
line-height: normal !important;
}
}
@media only screen and(max-width: 480px) {
.mcnBoxedTextContentContainer .mcnTextContent,
.mcnBoxedTextContentContainer .mcnTextContent p {
font-size: 14px !important;
line-height: normal !important;
}
}
@media only screen and(max-width: 480px) {
#templatePreheader {
display: block !important;
}
}
@media only screen and(max-width: 480px) {
#templatePreheader .mcnTextContent,
#templatePreheader .mcnTextContent p {
font-size: 14px !important;
line-height: normal !important;
}
}
@media only screen and(max-width: 480px) {
#templateHeader .mcnTextContent,
#templateHeader .mcnTextContent p {
font-size: 16px !important;
line-height: normal !important;
}
}
@media only screen and(max-width: 480px) {
#templateBody .mcnTextContent,
#templateBody .mcnTextContent p {
font-size: 16px !important;
line-height: normal !important;
}
}
@media only screen and(max-width: 480px) {
#templateFooter .mcnTextContent,
#templateFooter .mcnTextContent p {
font-size: 14px !important;
line-height: normal !important;
}
}
table.transaction th {
border-bottom: 1px solid #000;
text-align: left;
}
</style>
</head>
<body>
<center>
<table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
<tr>
<td align="center" valign="top" id="bodyCell">
<!--[if (gte mso 9)|(IE)]><table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;"><tr><td align="center" valign="top" width="600" style="width:600px;"> <![endif]-->
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer">
<tr>
<td valign="top" id="templateHeader">
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width: 100%;">
<tbody class="mcnTextBlockOuter">
<tr>
<td valign="top" class="mcnTextBlockInner" style="padding-top: 9px;">
<!--[if mso]><table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;"><tr> <![endif]-->
<!--[if mso]><td valign="top" width="210" style="width:210px;"> <![endif]-->
<table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width: 210px;" width="100%" class="mcnTextContentContainer">
<tbody>
<tr>
<td valign="top" class="mcnTextContent" style="padding-top: 0; padding-left: 18px; padding-bottom: 9px; padding-right: 18px;">
<img
data-file-id="1875424"
height="auto"
src="{{config(\'app.asset_url\')}}/beesmart/logo.72.png?v={{Str::random(6)}}"
style="border: 0px; width: 150px; height: auto; margin: 0px;"
width="150"
/>
</td>
</tr>
</tbody>
</table>
<!--[if mso]></td> <![endif]-->
<!--[if mso]><td valign="top" width="420" style="width:420px;"> <![endif]-->
<table align="right" border="0" cellpadding="0" cellspacing="0" style="max-width: 420px;" width="100%" class="mcnTextContentContainer">
<tbody>
<tr>
<td valign="top" class="mcnTextContent" style="padding-top: 0; padding-left: 0; padding-bottom: 9px; padding-right: 0; padding-top: 20px;">
<div style="text-align: right;">
<table align="right" border="0" cellpadding="0" cellspacing="0" style="max-width: 420px;" width="100%" class="mcnTextContentContainer">
<tbody>
<tr>
<td valign="top" style="padding-top: 0;" class="mcnTextContent">
<img
align="none"
data-file-id="1875408"
height="18"
src="{{config(\'app.asset_url\')}}/beesmart/phone.72.png?v={{Str::random(6)}}"
style="border: 0px; width: 19px; height: 18px !important;"
width="19"
/>
</td>
<td valign="top" style="padding-top: 0;" class="mcnTextContent">
<span style="font-size: 9pt; padding: 0 8px 0 5px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">088 1 105 105</span>
</td>
<td valign="top" style="padding-top: 0;" class="mcnTextContent">
<img
data-file-id="1875416"
height="18"
src="{{config(\'app.asset_url\')}}/beesmart/mail.72.png?v={{Str::random(6)}}"
style="border: 0px; width: 22px; height: 18px !important;"
width="19"
/>
</td>
<td valign="top" style="padding-top: 0;" class="mcnTextContent">
<span style="font-size: 9pt; padding: 0 8px 0 5px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">info@beesmarttelecom.nl</span>
</td>
<td valign="top" style="padding-top: 0;" class="mcnTextContent">
<img
data-file-id="1875420"
height="18"
src="{{config(\'app.asset_url\')}}/beesmart/bookmark.72.png?v={{Str::random(6)}}"
style="border: 0px; width: 19px; height: 18px !important;"
width="19"
/>
</td>
<td valign="top" style="padding-top: 0;" class="mcnTextContent">
<span style="font-size: 9pt; padding: 0 8px 0 5px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">beesmarttelecom.nl</span>
</td>
</tr>
</tbody>
</table>
</div>
</td>
</tr>
</tbody>
</table>
<!--[if mso]></td> <![endif]-->
<!--[if mso]></tr></table> <![endif]-->
</td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td valign="top" id="templateBody">
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width: 100%;">
<tbody class="mcnTextBlockOuter">
<tr>
<td valign="top" class="mcnTextBlockInner" style="padding-top: 9px;">
<!--[if mso]><table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;"><tr> <![endif]-->
<!--[if mso]><td valign="top" width="600" style="width:600px;"> <![endif]-->
<table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width: 100%; min-width: 100%;" width="100%" class="mcnTextContentContainer">
<tbody>
<tr>
<td valign="top" class="mcnTextContent" style="padding: 25px 18px 9px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; font-size: 11.25pt; line-height: normal;">
<!--- START -->
<h1 style="font-size: 28px; color: #74D1F6; padding-bottom: 32px;">Uw TV pakket is klaar voor gebruik</h1>
<p style="margin-bottom: 30px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; line-height: normal;"> Beste {{ $user_fullname }},</p>
<p style="margin-bottom: 30px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; line-height: normal;"> BeeSmart Telecom B.V. levert TV van Canal Digitaal over internet. Deze dienst is zojuist voor u geactiveerd.</p>
<p style="margin-bottom: 30px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; line-height: normal;"> Let even op de volgende zaken, dan kunt u optimaal hiervan gebruik maken.</p>
<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">Aansluiting van uw TV op de Settop Box</h3>
<p style="margin-bottom: 30px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; line-height: normal;"> De Settop Box moet met een netwerkkabel (UTP) aangesloten zijn op Fiber wifi router, en met een HDMI kabel op uw TV. U kunt maximaal 5 Settop Boxen in uw woning aansluiten. </p>
<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">Canal Digitaal TV App voor smartphone en tablet</h3>
<p style="margin-bottom: 30px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; line-height: normal;"> U kunt binnen en buitenshuis in de hele EU gebruikmaken van de Canal Digitaal TV App op uw smartphone of tablet. Hierbij heeft u toegang tot een groot deel van de zenders. U kunt ook live TV kijken, uw opnames plannen en bekijken, of streamen naar een Google Chromecast. Handig toch? </p>
<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">Installeer de app in een handomdraai</h3>
<p style="margin-bottom: 30px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; line-height: normal;"> Ga naar de <a href="https://itunes.apple.com/nl/app/online.nl/id1008027388?mt=8">App Store</a> (voor iOS) of <a href="https://play.google.com/store/apps/details?id=nl.streamgroup.canaldigital&hl=nl">Google Play</a> (voor Android), en installeer de Canal Digitaal TV App op uw telefoon of tablet.
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
<p style="margin-bottom: 30px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; line-height: normal;"> Bewaar deze gegevens goed. </p>
<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">TV kijken via PC</h3>
<p style="margin-bottom: 30px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; line-height: normal;"> U kunt ook via uw PC of laptop TV kijken binnen de hele EU, door in te loggen met dezelfde inloggegevens als hierboven. Ga hiervoor naar <a href="https://login.canaldigitaal.nl/authenticate?redirect_uri=https%3A%2F%2Flivetv.canaldigitaal.nl%2Fauth.aspx&response_type=code&scope=TVE&client_id=StreamGroup">https://livetv.canaldigitaal.nl/</a>. </p>
<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">TV kijken op uw Smart TV</h3>
<p style="margin-bottom: 30px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; line-height: normal;"> Ook sommige Smart TV’s zijn geschikt om zonder settop box TV te kijken via een app op de Smart TV. Ook hiervoor kunt u bovenstaande inloggevens gebruiken. Weten of uw TV hiervoor geschikt is? <a href="https://www.canaldigitaal.nl/klantenservice/alles-over/canal-digitaal-tv-app/canal-digitaal-smart-tv-app/geschikte-smart-tvs/">Bekijk dan deze lijst</a>. </p>
<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">Heeft u nog vragen, of lukt het inloggen niet?</h3>
<p style="margin-bottom: 30px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; line-height: normal;"> We helpen u graag verder. U kunt ons bellen op 020 – 760 50 40 van maandag tot en met zaterdag van 09:00 tot 17:30. U kunt ook een email sturen aan <a href="mailto:info@beesmarttelecom.nl">info@beesmarttelecom.nl</a>. </p>
<!--- END -->
<p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">Met vriendelijke groet,</p>

<p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">De klantenservice van BeeSmart Telecom B.V.
</p>
</td>
</tr>
</tbody>
</table>
<!--[if mso]></td> <![endif]-->
<!--[if mso]></tr></table> <![endif]-->
</td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td valign="top" id="templateFooter">
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width: 100%;">
<tbody class="mcnTextBlockOuter">
<tr>
<td valign="top" class="mcnTextBlockInner" style="padding-top: 9px;">
<!--[if mso]><table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;"><tr> <![endif]-->
<!--[if mso]><td valign="top" width="600" style="width:600px;"> <![endif]-->
<table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width: 100%; min-width: 100%;" width="100%" class="mcnTextContentContainer">
<tbody>
<tr>
<td valign="top" class="mcnTextContent" style="padding-top: 0; padding-right: 18px; padding-bottom: 9px; padding-left: 18px;">
<div style="text-align: center;">
<span style="font-size: 9pt; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">
BeeSmart Telecom B.V. | Energiestraat 2 | 7442DA Nijverdal | KvK-nummer: 57125805
</span>
</div>
</td>
</tr>
</tbody>
</table>
<!--[if mso]></td> <![endif]-->
<!--[if mso]></tr></table> <![endif]-->
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</table>
<!--[if (gte mso 9)|(IE)]></td></tr></table> <![endif]-->
</td>
</tr>
</table>
</center>
</body>
</html>',
            'created_at' => '2020-10-07 08:54:40',
            'updated_at' => NULL,
        ];

        $emailTemplate = EmailTemplate::where('type', 'm7.create_my_account')->where('tenant_id', 15)->first();
        if ($emailTemplate) {
            $existingDataAttr['id'] = $emailTemplate->id;
        } else {
            $newDataArr['id'] = (EmailTemplate::orderBy('id', 'DESC')->first()->id) + 1;
        }

        DB::table('email_templates')
            ->updateOrInsert(
                $existingDataAttr,
                $newDataArr
            );
    }
}
