<!DOCTYPE html>
<html lang="nl">
<head>
    <title>Storno rapport</title>
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
<h1>Storno rapport</h1>
<p>
    Storno's na {{ $since }} <br />
    Uitgevoerd op {{ $timestamp }}
</p>

@if (empty($payments))
<p>
    Geen nieuwe storno's gevonden.
</p>
@else

@foreach ($payments as $tenant)
<h2>Tenant: {{ $tenant->name }}</h2>
<p>
    Storno's: {{ $tenant->count }} <br />
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
            <th>Omschrijving</th>
            <th>Reden</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($tenant->payments as $payment)
        <tr>
            <td>{{ $payment->id }}</td>
            <td>{{ $payment->fmtd_date }}</td>
            <td>{{ $payment->account_name }}</td>
            <td>
                @if (!empty($payment->customer_number))
                    <a href="{{ $payment->relation_link }}">{{ $payment->customer_number }}</a>
                @endif
            </td>
            <td>
                @if (!empty($payment->invoice_number))
                    <a href="{{ $payment->invoice_link }}">{{ $payment->invoice_number }}</a>
                @endif
            </td>
            <td class="align-right">{{ $payment->amount }}</td>
            <td title="{{ $payment->descr }}">
                {{ substr($payment->descr, 0, 75) }}@if (strlen($payment->descr) > 75)...@endif
            </td>
            <td>{{ $payment->return_code }} / {{ $payment->return_reason }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endforeach

@endif

</body>
</html>
