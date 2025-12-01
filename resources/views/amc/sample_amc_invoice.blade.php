<!DOCTYPE html>
<html>
<head>
    <title>AMC Invoice</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; }
    </style>
</head>
<body>
    <h1>AMC Invoice</h1>
    <p>Customer: {{ $data['customer_name'] }}</p>
    <p>Invoice #: {{ $data['invoice_number'] }}</p>
    <p>Date: {{ $data['date'] }}</p>

    <h3>Items</h3>
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['items'] as $item)
                <tr>
                    <td>{{ $item['description'] }}</td>
                    <td>{{ $item['amount'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
