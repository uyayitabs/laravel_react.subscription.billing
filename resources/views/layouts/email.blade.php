<!doctype html>
<html style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; color: #292D2F;">
<head style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; color: #292D2F;">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; color: #292D2F;">
		Mail van {{ $tenant }}</Title>
	<meta name="viewport" content="width=device-width, initial-scale=1"
		  style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; color: #292D2F;">
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
			src: url("{{config('app.asset_url')}}/fonts/MyriadBold.woff2") format("woff2"), url("{{config('app.asset_url')}}/fonts/MyriadBold.woff") format("woff"), url("{{config('app.asset_url')}}/fonts/MyriadBold.otf") format("opentype");
			font-display: auto;
			font-style: normal;
			font-weight: normal;
		}

		@font-face {
			font-family: "myriadreg";
			src: url("{{config('app.asset_url')}}/fonts/MyriadReg.woff2") format("woff2"), url("{{config('app.asset_url')}}/fonts/MyriadReg.woff") format("woff"), url("{{config('app.asset_url')}}/fonts/MyriadReg.otf") format("opentype");
			font-display: auto;
			font-style: normal;
			font-weight: normal;
		}
	</Style>
	<style>

		* {
			padding: 0;
			margin: 0;
			font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important;
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
			font-family: 'myriadbold', 'Open Sans', Arial, sans-serif !important;
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

		@yield('style');
	</Style>
</head>
<body style="@yield('bodystyle')">
		@yield('header')
    @yield('content')
</body>
</html>