<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daily Report - {{ $date }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #4F46E5;
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .header p {
            margin: 0;
            color: #666;
            font-size: 16px;
        }
        .section {
            margin-bottom: 40px;
        }
        .section-title {
            font-size: 20px;
            color: #111;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .stats-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .stats-grid td {
            width: 50%;
            padding: 15px;
            vertical-align: top;
            border: 1px solid #eaeaea;
        }
        .stat-box {
            background-color: #f9fafb;
            border-radius: 8px;
            text-align: center;
        }
        .stat-label {
            display: block;
            font-size: 14px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 5px;
        }
        .stat-value {
            display: block;
            font-size: 24px;
            font-weight: bold;
            color: #111827;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.data-table th, table.data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        table.data-table th {
            background-color: #f3f4f6;
            color: #374151;
            font-size: 14px;
            font-weight: 600;
        }
        table.data-table td {
            font-size: 14px;
            color: #4b5563;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #9ca3af;
            font-size: 12px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>POS Daily Report</h1>
        <p>Date: <strong>{{ $date }}</strong></p>
    </div>

    <div class="section">
        <h2 class="section-title">Overview</h2>
        <table class="stats-grid">
            <tr>
                <td class="stat-box">
                    <span class="stat-label">Total Sales</span>
                    <span class="stat-value" style="color: #10b981;">${{ number_format($todaySales, 2) }}</span>
                </td>
                <td class="stat-box">
                    <span class="stat-label">Total Profit</span>
                    <span class="stat-value" style="color: #8b5cf6;">${{ number_format($todayProfit, 2) }}</span>
                </td>
            </tr>
            <tr>
                <td class="stat-box">
                    <span class="stat-label">Total Orders</span>
                    <span class="stat-value">{{ $todayOrdersCount }}</span>
                </td>
                <td class="stat-box">
                    <span class="stat-label">Lends Made Today</span>
                    <span class="stat-value" style="color: #ef4444;">${{ number_format($todayLends, 2) }}</span>
                </td>
            </tr>
        </table>
        
        <div style="background-color: #fef2f2; padding: 15px; border-radius: 8px; border-left: 4px solid #ef4444; margin-top: 15px;">
            <span style="display: block; font-size: 14px; color: #991b1b; font-weight: bold;">Total Outstanding Lends (Store-wide)</span>
            <span style="display: block; font-size: 20px; color: #b91c1c; font-weight: bold; margin-top: 5px;">${{ number_format($outstandingLends, 2) }}</span>
        </div>

        <div style="background-color: #ecfdf5; padding: 15px; border-radius: 8px; border-left: 4px solid #10b981; margin-top: 15px;">
            <span style="display: block; font-size: 14px; color: #065f46; font-weight: bold;">Current Drawer Cash</span>
            <span style="display: block; font-size: 20px; color: #047857; font-weight: bold; margin-top: 5px;">${{ number_format($currentDrawer, 2) }}</span>
        </div>

        <h3 style="font-size: 16px; margin-top: 25px; margin-bottom: 10px; color: #374151; border-bottom: 1px dashed #ddd; padding-bottom: 5px;">Supplier Purchases & Owed</h3>
        <table class="stats-grid">
            <tr>
                <td class="stat-box">
                    <span class="stat-label">Purchases Today</span>
                    <span class="stat-value" style="color: #3b82f6;">${{ number_format($todaySupplierPurchases, 2) }}</span>
                </td>
                <td class="stat-box">
                    <span class="stat-label">Owed from Today's Purchases</span>
                    <span class="stat-value" style="color: #f59e0b;">${{ number_format($todaySupplierOwed, 2) }}</span>
                </td>
            </tr>
        </table>
        
        <div style="background-color: #fffbeb; padding: 15px; border-radius: 8px; border-left: 4px solid #f59e0b; margin-top: 15px;">
            <span style="display: block; font-size: 14px; color: #92400e; font-weight: bold;">Total Outstanding Supplier Owed (Store-wide)</span>
            <span style="display: block; font-size: 20px; color: #d97706; font-weight: bold; margin-top: 5px;">${{ number_format($outstandingSupplierOwed, 2) }}</span>
        </div>
    </div>



    <div class="footer">
        Generated by POS System &copy; {{ date('Y') }}
    </div>

</body>
</html>
