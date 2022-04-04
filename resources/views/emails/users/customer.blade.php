@extends('layouts.email')

@section('content')
<table class="page-wrapper" style="font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; background: #FFFFFF; margin: 0 auto !important; padding: 0px 32px !important; width: 600px !important; ">
    <tbody style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box;">
        <tr style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; color: #292D2F;">
            <td class="content" style="font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; margin: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; padding: 16px 0;">

                <h1 style="font-size: 28px; color: #74D1F6; padding-bottom: 32px;"></h1>

                <p style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; box-sizing: border-box; color: #3d4852; margin-top: 0; text-align: left; margin-bottom: 1em; font-size: 11.25pt; letter-spacing: .35px; line-height: 22px;">
                    Beste {{ $title }} {{ $user_fullname }},
                </p>
                <p>
                    Bij deze ontvangt u uw login gegevens voor My{{ ucfirst($identifier) }}.
                </p>
                <p>
                    Uw gebruikersnaam is: {{$username}}
                </p>
                <p>
                    Uw wachtwoord kunt u hier instellen: <a href="{{$service_url}}/auth/verify/{{ $code }}" target="_blank">Password reset</a>
                </p>
                <p>
                    Heeft u vragen over uw login? Neem dan contact op met onze klantenservice. U kunt ons bereiken op telefoonnummer {{ $service_number }} of per email op {{ $service_email }}.
                </p>
                <p>
                    Met vriendelijke groet,
                </p>
                <p>
                    {{ $tenant }}
                </p>
            </td>
        </tr>
    </tbody>
</table>
@endsection
