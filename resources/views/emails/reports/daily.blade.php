<!DOCTYPE html>
<html>
<head>
    <title>Daily Admin Report</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6; padding: 20px;">
    <h2 style="color: #4F46E5;">Daily Admin Report - {{ $reportData['date'] }}</h2>
    
    <p>Hello Admin,</p>
    
    <p>Please find attached the daily sales, profit, and lends report for today ({{ $reportData['date'] }}).</p>
    
    <h3>Quick Summary:</h3>
    <ul>
        <li><strong>Total Orders:</strong> {{ $reportData['todayOrdersCount'] }}</li>
        <li><strong>Sales:</strong> ${{ number_format($reportData['todaySales'], 2) }}</li>
        <li><strong>Profit:</strong> ${{ number_format($reportData['todayProfit'], 2) }}</li>
    </ul>

    <p>For more detailed information, including top-selling items and outstanding lends, please review the attached PDF document.</p>
    
    <p>Best regards,<br>
    The POS System</p>
</body>
</html>
