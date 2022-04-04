<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateOrderSuccessSeeder extends Seeder
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
                    'type' => 'order.success',
                    'tenant_id' => 7,
                ],
                [
                    'tenant_id' => 7,
                    'product_id' => NULL,
                    'type' => 'order.success',
                    'from_name' => 'Fiber Nederland',
                    'from_email' => 'noreply@fiber.nl',
                    'bcc_email' => 'mark@f2x.nl,marisa.vanvelzen@xsprovider.nl',
                    'subject' => 'Bevestiging aanvraag Fiber NL',
                    'body_html' => '<!doctype html>
                    <html>
                       <head>
                          <meta name="viewport" content="width=device-width">
                          <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                          <title>Fiber email</title>
                          <style>
                             /* -------------------------------------
                             RESPONSIVE AND MOBILE FRIENDLY STYLES
                             ------------------------------------- */
                             @media only screen and (max-width: 620px) {
                             table[class=body] h1 {
                             font-size: 28px !important;
                             margin-bottom: 10px !important;
                             }
                             table[class=body] p,
                             table[class=body] ul,
                             table[class=body] ol,
                             table[class=body] td,
                             table[class=body] span,
                             table[class=body] a {
                             font-size: 16px !important;
                             }
                             table[class=body] .wrapper,
                             table[class=body] .article {
                             padding: 10px !important;
                             }
                             table[class=body] .content {
                             padding: 0 !important;
                             }
                             table[class=body] .container {
                             padding: 0 !important;
                             width: 100% !important;
                             }
                             table[class=body] .main {
                             border-left-width: 0 !important;
                             border-radius: 0 !important;
                             border-right-width: 0 !important;
                             }
                             table[class=body] .btn table {
                             width: 100% !important;
                             }
                             table[class=body] .btn a {
                             width: 100% !important;
                             }
                             table[class=body] .img-responsive {
                             height: auto !important;
                             max-width: 100% !important;
                             width: auto !important;
                             }
                             }
                             /* -------------------------------------
                             PRESERVE THESE STYLES IN THE HEAD
                             ------------------------------------- */
                             @media all {
                             .ExternalClass {
                             width: 100%;
                             }
                             .ExternalClass,
                             .ExternalClass p,
                             .ExternalClass span,
                             .ExternalClass font,
                             .ExternalClass td,
                             .ExternalClass div {
                             line-height: 100%;
                             }
                             .apple-link a {
                             color: inherit !important;
                             font-family: inherit !important;
                             font-size: inherit !important;
                             font-weight: inherit !important;
                             line-height: inherit !important;
                             text-decoration: none !important;
                             }
                             .btn-primary table td:hover {
                             background-color: #34495e !important;
                             }
                             .btn-primary a:hover {
                             background-color: #34495e !important;
                             border-color: #34495e !important;
                             }
                             }
                          </style>
                       </head>
                       <body class=""
                          style="background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
                          <table border="0" cellpadding="0" cellspacing="0" class="body"
                             style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background-color: #f6f6f6;">
                             <tr>
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"> </td>
                                <td class="container"
                                   style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; Margin: 0 auto; max-width: 640px; padding: 10px; width: 640px;">
                                   <div class="content"
                                      style="box-sizing: border-box; display: block; Margin: 0 auto; max-width: 640px; padding: 10px;">
                                      <!-- START CENTERED WHITE CONTAINER -->
                                      <span class="preheader"
                                         style="color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;">Bedankt dat u voor Fiber heeft gekozen. Dit is de bevestiging van uw bestelling.</span>
                                      <table class="main"
                                         style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background: #ffffff; border-radius: 3px;">
                                         <!-- START MAIN CONTENT AREA -->
                                         <tr>
                                            <td class="wrapper"
                                               style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;">
                                               <table border="0" cellpadding="0" cellspacing="0"
                                                  style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                                                  <tr>
                                                     <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">
                                                        <img src="https://bestel-fiber.nl/wp-content/uploads/2019/02/bestelling-gelukt.jpg"
                                                           width="100%">
                                                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
                                                           <br>Beste {{ $order["customer"]["name"]["first"] }} {{ $order["customer"]["name"]["middle"] }} {{ $order["customer"]["name"]["last"] }},
                                                        </p>
                                                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
                                                           Bedankt dat u voor Fiber Nederland heeft gekozen. Voor de duidelijkheid
                                                           hebben wij hieronder een overzicht gemaakt van de diensten en gegevens die
                                                           u heeft aangevraagd.
                                                        </p>
                                                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
                                                           Uw aanmelding zullen wij verwerken en zodra wij weten dat de
                                                           glasvezelaansluiting in uw huis geactiveerd kan worden zullen we u
                                                           berichten.
                                                        </p>
                                                        <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary"
                                                           style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; box-sizing: border-box;">
                                                           <tbody>
                                                              <tr>
                                                                 <td align="left"
                                                                    style="font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;">
                                                                    <p style="font-family: sans-serif; font-size: 18px; font-weight: bold; margin: 0; Margin-bottom: 15px;">
                                                                       Controleer uw gegevens
                                                                    </p>
                                                                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
                                                                       Hieronder treft u de overeenkomst die we hebben afgesloten.
                                                                       Mocht er iets niet kloppen, stuur dan svp een mail naar <a
                                                                          href="mailto:info@fiber.nl" target="_top">info@fiber.nl</a>.
                                                                    </p>
                                                                    <h3>Uw gegevens</h3>
                                                                    <table border="0" cellpadding="0" cellspacing="0"
                                                                       style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%">
                                                                       <tbody>
                                                                          <tr>
                                                                             <td width="25%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                Naam
                                                                             </td>
                                                                             <td width="75%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                {{ $order["customer"]["name"]["title"] }} {{ $order["customer"]["name"]["first"] }} {{ $order["customer"]["name"]["middle"] }} {{ $order["customer"]["name"]["last"] }}
                                                                             </td>
                                                                          </tr>
                                                                          <tr>
                                                                             <td width="25%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                Geboortedatum
                                                                             </td>
                                                                             <td width="75%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                {{ date("Y-m-d", strtotime($order["customer"]["birth_date"])) }}
                                                                             </td>
                                                                          </tr>
                                                                          <tr>
                                                                             <td width="25%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                Adres
                                                                             </td>
                                                                             <td width="75%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                {{ $order["address"]["street"] }} {{ $order["address"]["house_no"] }} {{ $order["address"]["house_no_suffix"] }} {{ $order["address"]["room"] }}<br/>
                                                                                {{ $order["address"]["postal_code"] }} {{ $order["address"]["city"] }}
                                                                             </td>
                                                                          </tr>
                                                                          <tr>
                                                                             <td width="25%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                Email adres
                                                                             </td>
                                                                             <td width="75%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                {{ $order["contact"]["email"] }}
                                                                             </td>
                                                                          </tr>
                                                                          <tr>
                                                                             <td width="25%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                Telefoon
                                                                             </td>
                                                                             <td width="75%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                {{ $order["contact"]["phone"] }} / {{ $order["contact"]["mobile"] }}
                                                                             </td>
                                                                          </tr>
                                                                       </tbody>
                                                                    </table>
                                                                    <h3>Uw abonnement</h3>
                                                                    <table border="0" cellpadding="0" cellspacing="0"
                                                                       style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%">
                                                                       <tbody>
                                                                          <tr>
                                                                             <td width="25%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                Pakket
                                                                             </td>
                                                                             <td width="75%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                {{ $order["product"]["package"] }}
                                                                             </td>
                                                                          </tr>
                                                                          <tr>
                                                                             <td width="25%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                Snelheid
                                                                             </td>
                                                                             <td width="75%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                Tot 1.000 Mbps
                                                                             </td>
                                                                          </tr>
                                                                          <tr>
                                                                             <td width="25%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                Wifi router
                                                                             </td>
                                                                             <td width="75%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                Inclusief gratis wifi router (bruikleen)
                                                                             </td>
                                                                          </tr>
                                                                          <tr>
                                                                             <td width="25%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                Telefonie
                                                                             </td>
                                                                             <td width="75%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                @if ($order["product"]["phone"]["use_phone"] == "no")
                                                                                Geen vaste telefonie
                                                                                @elseif (count($order["product"]["phone"]["use_phone"]) == "yes")
                                                                                Nummerbehoud voor nummer: {{ $order["product"]["phone"]["phone_1"] }} <br/>
                                                                                @endif
                                                                                @if ($order["product"]["phone"]["phone_2"] == "")
                                                                                Extra 2e telefoonnummer (nieuw nummer) <br/>
                                                                                @endif
                                                                                @if ($order["product"]["phone"]["phone_2"] != "")
                                                                                Nummerbehoud voor 2e telefoonnummer: {{ $order["product"]["phone"]["phone_2"] }} <br/>
                                                                                @endif
                                                                                {{ $order["product"]["phone"]["phone_plan"] }}
                                                                             </td>
                                                                          </tr>
                                                                          <tr>
                                                                             <td width="25%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                Televisie
                                                                             </td>
                                                                             <td width="75%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                Aantal settop boxen: {{ $order["product"]["tv"]["settop_boxes"] }}<br/>
                                                                                @if ($extras != "")
                                                                                Extra opties: {{ $extras }}
                                                                                @endif
                                                                             </td>
                                                                          </tr>
                                                                          <tr>
                                                                             <td width="25%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                Contractsperiode
                                                                             </td>
                                                                             <td width="75%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                {{ rtrim($order["contract"]["period"], "y") }} jaar
                                                                             </td>
                                                                          </tr>
                                                                       </tbody>
                                                                    </table>
                                                                    <h3>Maandelijkse kosten</h3>
                                                                    @if ($order["product"]["tv"]["type"] == "Canal Digitaal")
                                                                    <p>
                                                                       Hierin is € {{ localizedNumber($order["product"]["tv"]["package_price"]) }} inbegrepen voor het {{ $order["product"]["tv"]["package_name"] }} televisiepakket
                                                                       van Canal Digitaal.
                                                                    </p>
                                                                    @endif
                                                                    <table border="0" cellpadding="0" cellspacing="0"
                                                                       style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%">
                                                                       <tbody>
                                                                          <tr>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                {{ $order["product"]["tv"]["package_name"] }}
                                                                             </td>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 2em 0.5em 0; text-align: right">
                                                                                € {{ localizedNumber($order["product"]["tv"]["package_price"]) }}
                                                                             </td>
                                                                          </tr>
                                                                          <tr>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                Wifi router
                                                                             </td>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 2em 0.5em 0; text-align: right">
                                                                                gratis
                                                                             </td>
                                                                          </tr>
                                                                          <tr>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                Eerste settop box
                                                                             </td>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 2em 0.5em 0; text-align: right">
                                                                                gratis
                                                                             </td>
                                                                          </tr>
                                                                          @if ($order["product"]["tv"]["settop_boxes"] > 0)
                                                                          <tr>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                @if ($order["product"]["tv"]["settop_boxes"] == 1)
                                                                                Extra settop box
                                                                                @else
                                                                                Extra settop boxen
                                                                                @endif
                                                                             </td>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 2em 0.5em 0; text-align: right">
                                                                                € {{ localizedNumber(floatval($order["product"]["tv"]["settop_boxes"]) * 5) }}
                                                                             </td>
                                                                          </tr>
                                                                          @endif
                                                                          @if (count($order["product"]["tv"]["extra_packages"]) > 0)
                                                                          @php
                                                                          $total_extra = 0;
                                                                          @endphp
                                                                          @foreach ($order["product"]["tv"]["extra_packages"] as $extra_package)
                                                                          <tr>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                {{ $extra_package["name"] }}
                                                                             </td>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 2em 0.5em 0; text-align: right">
                                                                                € {{ localizedNumber($extra_package["price"]) }}
                                                                             </td>
                                                                          </tr>
                                                                          @php
                                                                          $total_extra += floatval($extra_package["price"]);
                                                                          @endphp
                                                                          @endforeach
                                                                          @endif
                                                                          <tr>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                <b>Totaal maandelijkse kosten</b>
                                                                             </td>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 2em 0.5em 0; text-align: right">
                                                                                <b>€ {{ localizedNumber(floatval($order["product"]["tv"]["package_price"]) + $total_extra) }}</b>
                                                                             </td>
                                                                          </tr>
                                                                       </tbody>
                                                                    </table>
                                                                    <h3>Eénmalige kosten</h3>
                                                                    <p>
                                                                       Deze kosten zullen in rekening gebracht worden op uw eerste
                                                                       factuur.
                                                                    </p>
                                                                    <table border="0" cellpadding="0" cellspacing="0"
                                                                       style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%">
                                                                       <tbody>
                                                                          <tr>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                Borg wifi router
                                                                             </td>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 2em 0.5em 0; text-align: right">
                                                                                € {{ localizedNumber($order["product"]["router_deposit"]) }}
                                                                             </td>
                                                                          </tr>
                                                                          @if (!empty($order["product"]["activation_fee"]))
                                                                          <tr>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                Activatiekosten internet
                                                                             </td>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 2em 0.5em 0; text-align: right">
                                                                                € 24,95
                                                                             </td>
                                                                          </tr>
                                                                          @endif
                                                                          @if (!empty($order["product"]["activation_fee_tv"]))
                                                                          <tr>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                Activatiekosten TV
                                                                             </td>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 2em 0.5em 0; text-align: right">
                                                                                € 24,95
                                                                             </td>
                                                                          </tr>
                                                                          @endif
                                                                          <tr>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                <b>Totaal eenmalige kosten</b>
                                                                             </td>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 2em 0.5em 0; text-align: right">
                                                                                <b>€ {{ localizedNumber((floatval($order["product"]["tv"]["package_price"]) + $total_extra) +
                                                                                (!empty($order["product"]["activation_fee"]) ? floatval($order["product"]["activation_fee"]) : 0) +
                                                                                (!empty($order["product"]["activation_fee_tv"]) ? floatval($order["product"]["activation_fee_tv"]) : 0) +
                                                                                floatval($order["product"]["router_deposit"])) }}</b>
                                                                             </td>
                                                                          </tr>
                                                                       </tbody>
                                                                    </table>
                                                                    <h3>Betaling</h3>
                                                                    <p>
                                                                       Wij zullen de bovengenoemde kosten incasseren van het volgende
                                                                       bankrekeningnummer.
                                                                    </p>
                                                                    <table border="0" cellpadding="0" cellspacing="0"
                                                                       style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%">
                                                                       <tbody>
                                                                          <tr>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                Rekeningnummer (IBAN)
                                                                             </td>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                {{ $order["bank"]["iban"] }}
                                                                             </td>
                                                                          </tr>
                                                                          <tr>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                Tenaamstelling
                                                                             </td>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                {{ $order["bank"]["holder"] }}
                                                                             </td>
                                                                          </tr>
                                                                       </tbody>
                                                                    </table>
                                                                    <h3>Akkoord</h3>
                                                                    <p>
                                                                       U bent met de volgende zaken akkoord gegaan.
                                                                    </p>
                                                                    <table border="0" cellpadding="0" cellspacing="0"
                                                                       style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%">
                                                                       <tbody>
                                                                          @foreach ($order["contract"]["agreements"] as $agreement)
                                                                          <tr>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                {{ $agreement["name"] }}
                                                                             </td>
                                                                             <td width="50%"
                                                                                style="border-bottom: 1px solid #ddd; vertical-align: top; padding: 0.5em 0;">
                                                                                {{ $agreement["text"] }}
                                                                             </td>
                                                                          </tr>
                                                                          @endforeach
                                                                       </tbody>
                                                                    </table>
                                                                 </td>
                                                              </tr>
                                                           </tbody>
                                                        </table>
                                                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
                                                           Mocht u nog vragen hebben, neem dan contact met ons op via <a
                                                              href="mailto:info@fiber.nl"
                                                              target="_top">info@fiber.nl</a>. Mocht u zich bedenken dan heeft
                                                           u 14 kalenderdagen de tijd om uw aanvraag te annuleren. Bewaar dit bewijs
                                                           van aanvraag goed. Wij willen u bedanken voor uw vertrouwen en zullen zodra
                                                           wij bericht van de netbeheerder krijgen u berichten over de levering.
                                                        </p>
                                                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
                                                           Met vriendelijke groet,
                                                        </p>
                                                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">
                                                           Team Fiber Nederland
                                                        </p>
                                                     </td>
                                                  </tr>
                                               </table>
                                            </td>
                                         </tr>
                                         <!-- END MAIN CONTENT AREA -->
                                      </table>
                                      <!-- START FOOTER -->
                                      <div class="footer" style="clear: both; Margin-top: 0px; text-align: center; width: 100%;">
                                         <table border="0" cellpadding="0" cellspacing="0"
                                            style="border-collapse: separate; background-color: #89d2f6; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                                            <tr>
                                               <td class="content-block"
                                                  style="font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #999999; text-align: center;">
                                                  <img src="https://bestel-fiber.nl/wp-content/uploads/2019/02/Logo-Fiber-Diap-685px-e1549637198635.png">
                                               </td>
                                            </tr>
                                            <tr>
                                               <td class="content-block powered-by"
                                                  style="font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #ffffff; text-align: center;">
                                                  Fiber Nederland
                                               </td>
                                            </tr>
                                         </table>
                                      </div>
                                      <!-- END FOOTER -->
                                      <!-- END CENTERED WHITE CONTAINER -->
                                   </div>
                                </td>
                                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;"> </td>
                             </tr>
                          </table>
                       </body>
                    </html>',
                    'created_at' => now(),
                    'updated_at' => NULL
                ]
            );
    }
}
