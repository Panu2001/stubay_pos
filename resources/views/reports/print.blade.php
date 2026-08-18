<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #111; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border-bottom: 1px solid #eee; padding: 10px; text-align: left; }
        th { background: #f9f9f9; font-weight: bold; }
        .totals { text-align: right; margin-top: 20px; font-size: 14px; }
        .totals p { margin: 5px 0; }
        .footer { margin-top: 50px; text-align: center; color: #999; font-size: 10px; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="background: #f1f1f1; padding: 10px; text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #007bff; color: white; border: none; border-radius: 4px;">Print / Save as PDF</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer; background: #6c757d; color: white; border: none; border-radius: 4px; margin-left: 10px;">Close</button>
    </div>

    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Generated on: {{ now()->format('Y-m-d H:i') }}</p>
        @if($startDate || $endDate)
            <p>Period: {{ $startDate ?? 'Beginning' }} to {{ $endDate ?? 'Today' }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        @foreach($summary as $label => $value)
            <p><strong>{{ $label }}:</strong> {{ $value }}</p>
        @endforeach
    </div>

    <div class="footer">
        <p>POS System - {{ config('app.name') }}</p>
    </div>

    <script>
        // Auto print if requested via query param
        if (window.location.search.includes('autoprint=1')) {
            window.onload = () => window.print();
        }
    </script>
</body>
</html>
