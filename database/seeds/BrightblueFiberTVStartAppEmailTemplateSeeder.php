<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrightblueFiberTVStartAppEmailTemplateSeeder extends Seeder
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
                'tenant_id' => 7,
                'product_id' => 196,
                'type' => 'brightblue.create_account',
                'from_name' => 'Fiber Nederland',
                'from_email' => 'noreply@fiber.nl',
                'bcc_email' => 'mark@f2x.nl,marisa.vanvelzen@xsprovider.nl',
                'subject' => 'Uw login gegevens voor Fiber Start TV App',
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
<title>Uw login gegevens voor Fiber Start TV</title>
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
border-bottom: 1px dashed #74d1f6;
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
border-top: 1px dashed #74d1f6;
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
height="58"
src="{{config(\'app.asset_url\')}}/images/logo.72.png?v={{Str::random(6)}}"
style="border: 0px; width: 115px; height: 58px; margin: 0px;"
width="115"
/>
</td>
</tr>
</tbody>
</table>
<!--[if mso]></td> <![endif]-->
<!--[if mso]><td valign="top" width="390" style="width:390px;"> <![endif]-->
<table align="right" border="0" cellpadding="0" cellspacing="0" style="max-width: 390px;" width="100%" class="mcnTextContentContainer">
<tbody>
<tr>
<td valign="top" class="mcnTextContent" style="padding-top: 0; padding-left: 18px; padding-bottom: 9px; padding-right: 0; padding-top: 30px;">
<div style="text-align: right;">
<table align="right" border="0" cellpadding="0" cellspacing="0" style="max-width: 390px;" width="100%" class="mcnTextContentContainer">
<tbody>
<tr>
<td valign="top" style="padding-top: 0;" class="mcnTextContent">
<img
align="none"
data-file-id="1875408"
height="18"
src="{{config(\'app.asset_url\')}}/images/phone.72.png?v={{Str::random(6)}}"
style="border: 0px; width: 19px; height: 18px;"
width="19"
/>
</td>
<td valign="top" style="padding-top: 0;" class="mcnTextContent">
<span style="font-size: 9.25pt; padding: 0 10px 0 5px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">020-760 50 40</span>
</td>
<td valign="top" style="padding-top: 0;" class="mcnTextContent">
<img
data-file-id="1875416"
height="18"
src="{{config(\'app.asset_url\')}}/images/mail.72.png?v={{Str::random(6)}}"
style="border: 0px; width: 19px; height: 18px;"
width="19"
/>
</td>
<td valign="top" style="padding-top: 0;" class="mcnTextContent">
<span style="font-size: 9.25pt; padding: 0 10px 0 5px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">info@fiber.nl</span>
</td>
<td valign="top" style="padding-top: 0;" class="mcnTextContent">
<img
data-file-id="1875420"
height="18"
src="{{config(\'app.asset_url\')}}/images/bookmark.72.png?v={{Str::random(6)}}"
style="border: 0px; width: 19px; height: 18px;"
width="19"
/>
</td>
<td valign="top" style="padding-top: 0;" class="mcnTextContent">
<span style="font-size: 9.25pt; padding-left: 5px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">www.fiber.nl</span>
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


<h1 style="font-size: 28px; color: #74D1F6; padding-bottom: 32px;">Uw Fiber Start TV pakket is actief</h1>

<p style="margin-bottom: 30px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif; line-height: normal;">
Beste {{ $userFullname }},
</p>

<p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">
U heeft een Start pakket bij Fiber NL. Dit hebben we zojuist voor u geactiveerd. Hieronder leest u hoe u het verder werkt.
</p>

<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">Installatie van de settop box
</h3>

<p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">
U ontvangt binnenkort een pakketje van ons, met daarin de settop box, en een handige stap-voor-stap installatie handleiding.
</p>

<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">Activatie code voor de settop box</h3>

<p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">
Als u straks de settop box installeert, moet u een activatie code invullen. Dat is de volgende code:
</p>

<p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">
{{trim(chunk_split($activationCode, 4, " "))}}
</p>

<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">PIN code voor toegangscontrole</h3>

<p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">
Op de settop box kunt u toegangscontrole instellen. Daarvoor heeft u de PIN code nodig van de hoofdgebruiker. Dat is de volgende PIN code:
</p>

<p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">
{{$temporaryPin}}
</p>

<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">Installeer alvast de Fiber TV app</h3>

<p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">
U kunt al TV kijken via de Fiber TV app.
Ga naar de App Store (voor iOS) en zoek op "Fiber TV" (gemaakt door Teleplaza Services) of gebruik deze link voor <a href="https://play.google.com/store/apps/details?id=nl.fiber.nivel&hl=nl">Google Play</a> (voor Android), en installeer de Fiber TV App op uw telefoon of tablet. <br />
Open de Fiber TV App, en gebruik weer dezelfde activatie code: <br />
</p>

<p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">
{{trim(chunk_split($activationCode, 4, " "))}}
</p>

<h3 style="padding: 0; margin: 0; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">Heeft u nog vragen, of lukt iets niet?</h3>

<p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">
We helpen u graag verder. U kunt ons bellen op 020 – 760 50 40 van maandag tot en met zaterdag van 09:00 tot 17:30.<br>
U kunt ook een email sturen aan <a href="mailto:info@fiber.nl">info@fiber.nl</a>.
</p>

<p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">Met vriendelijke groet,</p>
&nbsp;
<p style="margin-bottom: 16px; font-size: 11.25pt; letter-spacing: 0.35px; font-family: \'myriadreg\', \'Open Sans\', Arial, sans-serif;">
Klantenservice <br />
Fiber NL
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
Postadres: Postbus 60228, 1320AG Almere | Bezoekadres:<br />
Transistorstraat 7, 1322CJ Almere | KvK-nummer: 73786950 | BTW-nummer: NL859664016B01
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
                'created_at' => '2020-10-01 00:00:00',
                'updated_at' => NULL,
            )
        );

        DB::table('email_templates')->insert($items);
    }
}
