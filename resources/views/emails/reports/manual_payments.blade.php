<!DOCTYPE html>
<html lang="nl">
<head>
    <title>Handmatige betalingen rapport</title>
    <style>
        body, h1, h2 {
            font-family: Verdana, sans-serif;
            font-size: 13px;
        }
        h1 {
            font-size: 1.5em;
        }
        h2 {
            font-size: 1.25em;
        }
        p {
            margin-bottom: 1em;
        }
        th, td {
            padding: 0.25em 1em;
            text-align: left;
        }
        th {
            border-bottom: 2px solid #ddd;
        }
        td.align-right {
            text-align: right;
        }
        tr.row-even td {
            background-color: #f8f8f8;
        }
        tr.row-paid td {
            background-color: #edffe4;
        }
        tr:nth-child(even) {
            background-color: #f8f8f8;
        }
        tr:nth-child(odd) {
            background-color: #ffffff;
        }
    </style>
</head>
<body>
<h1>Handmatige betalingen rapport</h1>
<p>
    Betalingen sinds {{ $since }}
    <br />
    Uitgevoerd op {{ $timestamp }}
</p>

@if (empty($payments))
<p>
    Geen nieuwe handmatige betalingen gevonden.
</p>
@else

@foreach ($payments as $tenant)
<h2>Tenant: {{ $tenant->name }}</h2>
<p>
    Betalingen: {{ $tenant->count }} <br />
    Totaal: {{ number_format($tenant->total, 2, ',', '.') }}
</p>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th width="125">Datum</th>
            <th>Klant</th>
            <th>Klantnr</th>
            <th>Factuurnr</th>
            <th>Bedrag</th>
            <th>IBAN</th>
            <th>Omschrijving</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($tenant->payments as $payment)
        <tr>
            <td>{{ $payment->id }}</td>
            <td>{{ $payment->fmtd_date }}</td>
            <td>{{ $payment->account_name }}</td>
            <td>
                @if (!empty($payment->relation_id) && !empty($payment->customerNumber))
                    <a href="{{config('app.front_url')}}/#/relations/{{ $payment->relation_id }}/details">{{ $payment->customerNumber }}</a><br />
                @endif
                &nbsp;
            </td>
            <td>
                @if (!empty($payment->invoiceNumbers))
                    @foreach ($payment->invoiceNumbers as $invoiceId => $invoiceNumber)
                        @if (!empty($invoiceId) && !empty($invoiceNumber))
                        <a href="{{config('app.front_url')}}/#/relations/{{ $payment->relation_id }}/{{ $invoiceId }}/invoices">{{ $invoiceNumber }}</a><br />
                        @endif
                    @endforeach
                @endif
                &nbsp;
            </td>
            <td class="align-right">{{ $payment->amount }}</td>
            <td>{{ $payment->iban }}</td>
            <td title="{{ $payment->descr }}">
                {{ substr($payment->descr, 0, 75) }}@if (strlen($payment->descr) > 75)...@endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endforeach

@endif

</body>
</html>
