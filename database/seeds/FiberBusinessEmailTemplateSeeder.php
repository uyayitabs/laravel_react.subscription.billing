<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FiberBusinessEmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('email_templates')
            ->updateOrInsert(
                [
                    'tenant_id' => 7,
                    'type' => 'invoice.bus',
                ],
                [
                    'bcc_email' => 'cdmigrations@xsprovider.nl,n.wegen@f2x.nl,mark@f2x.nl',
                    'body_html' => '<!DOCTYPE html>
<html>
<head>
<title>Fiber Zakelijk factuur</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!--[if mso]>
<style type=”text/css”>
.fallback-text {
font-family: Arial, sans-serif;
}
</style>
<![endif]-->
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
<td style="text-align: left; vertical-align: bottom;"> <img src="{{config(\'app.asset_url\')}}/fiber/fiber.bus.72.png?v={{Str::random(6)}}" alt="" style="width: 115px;"> </td>
<td class="header-info"> <img src="{{config(\'app.asset_url\')}}/images/phone.72.png?v={{Str::random(6)}}"><span>085 211 00 00</span> <img src="{{config(\'app.asset_url\')}}/images/mail.72.png?v={{Str::random(6)}}"><span>info@fiberzakelijk.nl</span> <img src="{{config(\'app.asset_url\')}}/images/bookmark.72.png?v={{Str::random(6)}}"><span>fiberzakelijk.nl</span> </td>
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
<p>Bij deze ontvangt u de factuur voor uw diensten bij Fiber Zakelijk voor de maand <strong>{{$invoice_due_date}}</strong>. In het bijgesloten PDF document kunt u het volgende vinden:</p>
<table>
<tr>
<td width="150">Voorpagina</td>
<td>Uw totale factuurbedrag</td>
</tr>
<tr>
<td width="150">Vervolgpagina\'s</td>
<td>De factuurspecificatie</td>
</tr>
<tr>
<td width="150">Laatste pagina</td>
<td>Toelichting op uw factuur</td>
</tr>
</table>
<br />
<p>Heeft u nog vragen over uw factuur? Mail ons dan op <a href="mailto:info@fiberzakelijk.nl">info@fiberzakelijk.nl</a> of bel naar 085 211 00 00. Wij zitten voor u klaar van maandag t/m zaterdag van 9.00 tot 17.30.</p>
<p>Met vriendelijke groet,</p>
<p>De klantenservice van Fiber Zakelijk</p>
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
                    'created_at' => now(),
                    'from_email' => 'noreply@fiberzakelijk.nl',
                    'from_name' => 'Fiber Zakelijk',
                    'product_id' => NULL,
                    'subject' => '{{ $totalWithVAT < 0 ? "Creditfactuur " : "Factuur " }}van {{$tenantName}} voor de maand {{$date}}',
                    'tenant_id' => 7,
                    'type' => 'invoice.bus',
                    'updated_at' => NULL,
                ]
            );
    }
}
