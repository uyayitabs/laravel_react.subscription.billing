<!DOCTYPE html>
<html lang="nl">
<head>
    <title>Deprovisioning rapport</title>
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
<h1>Deprovisioning rapport</h1>
<p>
    Uitgevoerd op {{ $timestamp }}
</p>

@if (empty($subLines))
<p>
    Geen relevante subscription lines gevonden.
</p>
@else

<p>
    Totaal aantal: {{ count($subLines) }}
</p>
<table>
    <thead>
        <tr>
            <th width="100">Sub.</th>
            <th width="100">Line</th>
            <th width="100">Tenant</th>
            <th width="250">Description</th>
            <th width="125">Start date</th>
            <th width="125">End date</th>
            <th width="350">Comment</th>
            <th width="350">Customer Number</th>
            <th width="125">CD cust. nr</th>
            <th width="150">GRID</th>
            <th width="150">Solocoo</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($subLines as $subLine)
        <tr>
            <td>{{ $subLine->subscription_id }}</td>
            <td>{{ $subLine->subscription_line_id }}</td>
            <td>{{ $subLine->tenant }}</td>
            <td>{{ $subLine->descr }}</td>
            <td>{{ $subLine->subscr_line_start }}</td>
            <td>{{ $subLine->subscr_line_end }}</td>
            <td>{{ $subLine->result }}</td>
            <td>{{ $subLine->customer_number }}</td>
            <td>{{ $subLine->m7CustNumber }}</td>
            <td>
                <a href="{{config('app.front_url')}}/#/relations/{{ $subLine->relation_id }}/{{ $subLine->subscription_id }}/subscriptions">
                    GRID &raquo;
                </a>
            </td>
            <td>
                @if (!empty($subLine->m7CustNumber))
                <a href="https://m7be2.solocoo.tv/m7be2saportal/customercare/users/?Search={{ $subLine->m7CustNumber }}&p=1">
                    Solocoo &raquo;
                </a>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endif

@if (!empty($subLinesNotOk))

    <h2>Nog af te handelen</h2>
    <p>Deze subscription lines zijn eerder gemarkeerd als "niet goed gedeprovisioned" en moeten nog afgehandeld worden met M7/Canal Digitaal.</p>

    <p>
        Totaal aantal: {{ count($subLinesNotOk) }}
    </p>
    <table>
        <thead>
        <tr>
            <th width="100">Sub.</th>
            <th width="100">Line</th>
            <th width="100">Tenant</th>
            <th width="250">Description</th>
            <th width="125">Start date</th>
            <th width="125">End date</th>
            <th width="350">Customer Number</th>
            <th width="125">CD cust. nr</th>
            <th width="150">GRID</th>
            <th width="150">Solocoo</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($subLinesNotOk as $subLine)
            <tr>
                <td>{{ $subLine->subscription_id }}</td>
                <td>{{ $subLine->subscription_line_id }}</td>
                <td>{{ $subLine->tenant }}</td>
                <td>{{ $subLine->descr }}</td>
                <td>{{ $subLine->subscr_line_start }}</td>
                <td>{{ $subLine->subscr_line_end }}</td>
                <td>{{ $subLine->customer_number }}</td>
                <td>{{ $subLine->m7CustNumber }}</td>
                <td>
                    <a href="{{config('app.front_url')}}/#/relations/{{ $subLine->relation_id }}/{{ $subLine->subscription_id }}/subscriptions">
                        GRID &raquo;
                    </a>
                </td>
                <td>
                    @if (!empty($subLine->m7CustNumber))
                        <a href="https://m7be2.solocoo.tv/m7be2saportal/customercare/users/?Search={{ $subLine->m7CustNumber }}&p=1">
                            Solocoo &raquo;
                        </a>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endif

</body>
</html>
