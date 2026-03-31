<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tracking History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        .doc-info {
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
        }
    </style>
</head>
<body>

    <h2>Tracking History</h2>

    <div class="doc-info">
        <strong>Document No:</strong> {{ $document->document_number }} <br>
        <strong>Document Name:</strong> {{ $document->document_name }}
    </div>

    <table>
        <thead>
            <tr>
                <th>From</th>
                <th>Action</th>
                <th>To</th>
                <th>Date</th>
                <th>Time</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tracks as $track)
                @php
                    $from = $track->user->full_name ?? 'N/A';
                    $action = ucfirst(strtolower($track->action_type));
                    $to = $track->section->section_name ?? 'N/A';

                    $date = \Carbon\Carbon::parse($track->action_datetime)->format('M d, Y');
                    $time = \Carbon\Carbon::parse($track->action_datetime)->format('h:i A');
                @endphp
                <tr>
                    <td>{{ $from }}</td>
                    <td>{{ $action }}</td>
                    <td>{{ $action === 'Created' ? 'N/A' : $to }}</td>
                    <td>{{ $date }}</td>
                    <td>{{ $time }}</td>
                    <td>{{ $track->remarks ?? '--' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
