<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $document_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        .doc-name {
            text-align: center;
            font-size: 48px;
            font-weight: bold;
        }
        .doc-number {
            text-align: center;
            font-size: 18px;
            margin-bottom: 100px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            height: 60%;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            text-align: left;
            padding: 10px;
        }
        .footer {
            margin-top: 30px;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="doc-name">{{ $document_name }}</div>
    <div class="doc-number">{{ $document_number }}</div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Section</th>
                <th>Date</th>
                <th>Signature</th>
            </tr>
        </thead>
        <tbody>
            {{-- Empty table rows for filling later --}}
            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        </tbody>
    </table>

    <div class="footer">
        Created on: {{ $created_at }} <br>
        Owner: {{ $owner_name }}
    </div>

</body>
</html>
