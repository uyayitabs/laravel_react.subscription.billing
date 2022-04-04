@extends('layouts.email')

@section('style')
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
@endsection

@section('header')
<div style="border-bottom: 4px dotted #74D1F6; background-color: #fff">
	<div style="box-sizing: border-box; width: 600px !important; margin: 0 auto; padding: 5px 32px; font-family: 'myriadbold', 'Open Sans', Arial, sans-serif; color: #74D1F6;">
		<img src="{{ config('app.asset_url') }}/{{ $slug }}/logo.72.png?v={{Str::random(6)}}" alt="" style="max-width: 100%; width: 150px;">
	</div>
</div>
@endsection

@section('content')
	<table class="page-wrapper"
	   style="font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; background: #FFFFFF; margin: 0 auto !important; padding: 0px 32px !important; width: 600px !important; ">
		<tbody 
			style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box;">
			<tr 
				style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; color: #292D2F;">
				<td class="content"
					style="font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; margin: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; padding: 16px 0;">

					<h1 style="font-size: 28px; color: #74D1F6; padding-bottom: 32px;">Uw TV pakket is klaar voor gebruik</h1>

					<p style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
						Beste {{ $user_fullname }},</p>
					<p style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
						Fiber NL levert TV van Canal Digitaal over internet. Deze dienst is zojuist voor u geactiveerd.</p>
					<p style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
						Let even op de volgende zaken, dan kunt u optimaal hiervan gebruik maken.</p>

					<h3 style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">
						Aansluiting van uw TV op de Settop Box
					</h3>
					<p style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
						De Settop Box moet met een netwerkkabel (UTP) aangesloten zijn op Fiber wifi router, en met een HDMI kabel op uw TV. U kunt maximaal 5 Settop Boxen in uw woning aansluiten.
					</p>

					<h3 style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">
						Canal Digitaal TV App voor smartphone en tablet
					</h3>
					<p style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
						U kunt binnen en buitenshuis in de hele EU gebruikmaken van de Canal Digitaal TV App op uw smartphone of tablet. Hierbij heeft u toegang tot een groot deel van de zenders. U kunt ook live TV kijken, uw opnames plannen en bekijken, of streamen naar een Google Chromecast. Handig toch?
					</p>

					<h3 style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">
						Installeer de app in een handomdraai
					</h3>
					<p style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
						Ga naar de <a href="https://itunes.apple.com/nl/app/online.nl/id1008027388?mt=8">App Store</a> (voor iOS) of <a href="https://play.google.com/store/apps/details?id=nl.streamgroup.canaldigital&hl=nl">Google Play</a> (voor Android), en installeer de Canal Digitaal TV App op uw telefoon of tablet. <br />
						Open de Canal Digitaal TV App, en log hiermee in: <br />
					</p>
					<table style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
						<tr>
							<td style="width: 150px;">Gebruikersnaam</td>
							<td>{{ $email }}</td>
						</tr>
						<tr>
							<td style="width: 150px;">Wachtwoord</td>
							<td>{{ $password }}</td>
						</tr>
					</table>
					<p style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
						Bewaar deze gegevens goed.
					</p>
					<h3 style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">
						TV kijken via PC
					</h3>
					<p style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
						U kunt ook via uw PC of laptop TV kijken binnen de hele EU, door in te loggen met dezelfde inloggegevens als hierboven. Ga hiervoor naar <a href="https://login.canaldigitaal.nl/authenticate?redirect_uri=https%3A%2F%2Flivetv.canaldigitaal.nl%2Fauth.aspx&response_type=code&scope=TVE&client_id=StreamGroup">https://livetv.canaldigitaal.nl/</a>.
					</p>
					<h3 style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">
						TV kijken op uw Smart TV
					</h3>
					<p style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
						Ook sommige Smart TV’s zijn geschikt om zonder settop box TV te kijken via een app op de Smart TV. Ook hiervoor kunt u bovenstaande inloggevens gebruiken. Weten of uw TV hiervoor geschikt is? <a href="https://www.canaldigitaal.nl/klantenservice/alles-over/canal-digitaal-tv-app/canal-digitaal-smart-tv-app/geschikte-smart-tvs/">Bekijk dan deze lijst</a>.
					</p>
					<h3 style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 0.5em; font-size: 12.25pt; letter-spacing: .35px; line-height: 22px;">
						Heeft u nog vragen, of lukt het inloggen niet?
					</h3>
					<p style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
						We helpen u graag verder. U kunt ons bellen op 020 – 760 50 40 van maandag tot en met zaterdag van 09:00 tot 17:30.
						U kunt ook een email sturen aan <a href="mailto:info@fiber.nl">info@fiber.nl</a>.
					</p>
					<p style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
						Met vriendelijk groet, <br />
						<br />
						Fiber Nederland
					</p>
				</td>
			</tr>
	</tbody>
	<tfoot style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box;">
		<tr class="footer-row" style="text-align: center;">
			<td style="padding: 0; margin: 0; color: #292D2F; box-sizing: border-box; font-family: 'myriadbold', 'Open Sans', Arial, sans-serif !important; font-size: 9.25pt; vertical-align: bottom; text-align: right;">
			</td>
		</tr>
		<tr class="footer-row"
			style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; color: #292D2F; font-size: 9pt !important; text-align: center;">
			<td class="footer-column"
				style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; box-sizing: border-box; padding-top: 10pt; padding-bottom: 10pt; color: #5C5E5E !important; border-top: 1px dashed #74D1F6; line-height: 22px;">
				<br />
				<img src="{{ config('app.asset_url') }}/{{ $slug }}/phone.72.png?v={{Str::random(6)}}"
					style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; max-width: 100%; margin-left: 16px; margin-right: 5px; height: 18px; vertical-align: top;"><span
					style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; line-height: 14pt; padding-top: 3px; padding-bottom: 3px; vertical-align: top;">020-760 50 40</Span>
				<img src="{{ config('app.asset_url') }}/{{ $slug }}/mail.72.png?v={{Str::random(6)}}"
					style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; max-width: 100%; margin-left: 16px; margin-right: 5px; height: 18px; vertical-align: top;"><span
					style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; line-height: 14pt; padding-top: 3px; padding-bottom: 3px; vertical-align: top;">info@fiber.nl</Span>
				<img src="{{ config('app.asset_url') }}/{{ $slug }}/bookmark.72.png?v={{Str::random(6)}}"
					style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; max-width: 100%; margin-left: 16px; margin-right: 5px; height: 18px; vertical-align: top;"><span
					style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; line-height: 14pt; padding-top: 3px; padding-bottom: 3px; vertical-align: top;">www.fiber.nl</Span>

				<br /><br />
				Postadres: Postbus 60228, 1320AG Almere | Bezoekadres: Transistorstraat 7, 1322CJ Almere <br /> KvK-nummer: 73786950 | BTW-nummer: NL859664016B01
				<br /><br />
			</td>
		</tr>
	</tfoot>
</table>
@endsection
