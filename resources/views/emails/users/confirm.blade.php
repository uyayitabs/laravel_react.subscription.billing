@extends('layouts.email')

@section('content')
<table class="page-wrapper" style="font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; background: #FFFFFF; margin: 0 auto !important; padding: 0px 32px !important; width: 600px !important; ">
    <tbody style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box;">
        <tr style="padding: 0; margin: 0; font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; color: #292D2F;">
            <td class="content" style="font-family: 'myriadreg', 'Open Sans', Arial, sans-serif !important; color: #292D2F; box-sizing: border-box; margin: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; padding: 16px 0;">

                <p>
                    Beste {{ $user_fullname }},
                </p>
                <p style="font-size: 11.25pt;">
                    Je kunt inloggen op <a href="{{Config::get('app.front_url')}}" style="text-decoration: none">{{Config::get('app.front_url')}}</a> met de username {{$username}}.
                </p>
                <p style="font-size: 11.25pt;">
                    Klik op deze link om je password in te stellen:
                </p>
                <p style="font-size: 11.25pt;">
                    <a style="text-decoration: none" href="{{Config::get('app.front_url')}}/#/auth/verify/{{ $code }}">{{Config::get('app.front_url')}}/#/auth/verify/{{ $code }}</a>
                </p>
                <p style="font-size: 11.25pt;">Met vriendelijke groet,</div>
                <p style="font-size: 11.25pt;">Het GRID team</div>
            </td>
        </tr>
    </tbody>
</table>
@endsection