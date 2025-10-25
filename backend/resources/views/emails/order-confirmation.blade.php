<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #42b883;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
        }
        .success-icon {
            font-size: 48px;
            color: #42b883;
            margin-bottom: 10px;
        }
        .order-details {
            margin: 20px 0;
        }
        .detail-row {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-label {
            font-weight: bold;
            color: #666;
        }
        .detail-value {
            color: #2c3e50;
        }
        .order-items {
            margin: 20px 0;
        }
        .order-items h2 {
            color: #2c3e50;
            border-bottom: 2px solid #42b883;
            padding-bottom: 10px;
        }
        .item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }
        .item-details {
            flex: 1;
        }
        .item-name {
            font-weight: bold;
            color: #2c3e50;
        }
        .item-quantity {
            color: #666;
            font-size: 0.9em;
        }
        .item-price {
            font-weight: bold;
            color: #42b883;
        }
        .total {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #2c3e50;
            text-align: right;
        }
        .total-label {
            font-size: 1.2em;
            font-weight: bold;
            color: #2c3e50;
        }
        .total-amount {
            font-size: 1.5em;
            font-weight: bold;
            color: #42b883;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="success-icon">✓</div>
            <h1>Order Confirmed!</h1>
            <p>Thank you for your order, {{ $order->customer_name }}</p>
        </div>

        <div class="order-details">
            <h2>Order Details</h2>
            <div class="detail-row">
                <span class="detail-label">Order Number:</span>
                <span class="detail-value">{{ $order->order_number }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Order Date:</span>
                <span class="detail-value">{{ $order->created_at->format('F d, Y \a\t g:i A') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Customer Email:</span>
                <span class="detail-value">{{ $order->customer_email }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Shipping Address:</span>
                <span class="detail-value">{{ nl2br($order->shipping_address) }}</span>
            </div>
        </div>

        <div class="order-items">
            <h2>Order Items</h2>
            @foreach($order->items as $item)
            <div class="item">
                <div class="item-details">
                    <div class="item-name">{{ $item->product->name }}</div>
                    <div class="item-quantity">Quantity: {{ $item->quantity }} × ${{ number_format($item->price, 2) }}</div>
                </div>
                <div class="item-price">${{ number_format($item->subtotal, 2) }}</div>
            </div>
            @endforeach
        </div>

        <div class="total">
            <div class="total-label">Total Amount:</div>
            <div class="total-amount">${{ number_format($order->total_amount, 2) }}</div>
        </div>

        <div class="footer">
            <p>This is an automated email confirmation. Please do not reply to this email.</p>
            <p>If you have any questions about your order, please contact our support team.</p>
            <p>&copy; {{ date('Y') }} E-Commerce Platform. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
