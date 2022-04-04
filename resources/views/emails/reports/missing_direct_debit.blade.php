<!DOCTYPE html>
<html lang="nl">
<head>
    <title>Ontbrekende direct debit bank accounts</title>
    <style>
        body, h1, h2 {
            font-family: Verdana, sans-serif;
            font-size: 13px;
            color: #333;
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
        tr.row td {
            border-bottom: 1px solid #ddd;
        }
        tr.subrow td {
            color: #666;
            border-bottom: 1px solid #eee;
            background-color: #f8f8f8;
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
<h1>Ontbrekende direct debit bank accounts</h1>
<p>
    Uitgevoerd op {{ $timestamp }}
</p>

@foreach($list as $section)

    <h2>{{ $section->title }}</h2>

    @if (empty($section->list))
        <p>
            Geen relevante relations gevonden.
        </p>
    @else

        <p>
            Totaal aantal: {{ count($section->list) }}
        </p>
        <table>
            <thead>
            <tr>
                <th width="100">Rel. ID</th>
                <th width="100">Cust. nr</th>
                <th width="125">Tenant</th>
                <th width="100">Sub. ID</th>
                <th width="125">Start date</th>
                <th width="250">IBAN</th>
                <th width="125">Facturen</th>
                <th width="150">Totaal</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($section->list as $row)
                <tr class="row">
                    <td>{{ $row->relation_id }}</td>
                    <td>
                        <a href="{{config('app.front_url')}}/#/relations/{{ $row->relation_id }}/details">
                            {{ $row->customer_number }}
                        </a>
                    </td>
                    <td>{{ $row->tenant }}</td>
                    <td>{{ $row->subscr_id }}</td>
                    <td>{{ date('d-m-Y', strtotime($row->subscr_start)) }}</td>
                    <td>{{ $row->iban }}</td>
                    <td>{{ $row->invoiceCount }}</td>
                    <td style="text-align: right;">{{ number_format($row->total, 2, ',', '.') }}</td>
                </tr>
                @if (!empty($row->invoices))
                <tr>
                    <td colspan="9">
                        <table style="border: 1px solid #eee; border-top: none; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Invoice No.</th>
                                    <th>Description</th>
                                    <th>Totaal</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($row->invoices as $invoice)
                                <tr class="subrow">
                                    <td width="150">{{ $invoice->date }}</td>
                                    <td width="150">
                                        <a href="{{config('app.front_url')}}/#/relations/{{ $row->relation_id }}/{{ $invoice->id }}/invoices">
                                            {{ $invoice->invoice_no }}
                                        </a>
                                    </td>
                                    <td width="500">{{ $invoice->description }}</td>
                                    <td width="100" style="text-align: right;">{{ number_format($invoice->price_total, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    @endif
@endforeach
</body>
</html>
