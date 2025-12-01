<!DOCTYPE html>
<html>
<head>
    <title>AMC Subscription - {{ $subscription->amc_unique_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .section { margin-bottom: 25px; }
        .bold { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .footer { margin-top: 30px; font-size: 0.8em; }
    </style>
</head>
<body>
    <div class="header">
        <h2>AMC Subscription Details</h2>
        <p>AMC Number: {{ $subscription->amc_unique_number }}</p>
    </div>

    <div class="section">
        <h3>Customer Information</h3>
        <table>
            <tr>
                <th>Name</th>
                <td>{{ $subscription->SalesData->customer_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Contact</th>
                <td>
                    {{ $subscription->SalesData->mobile ?? '' }} / 
                    {{ $subscription->SalesData->phone ?? '' }}
                </td>
            </tr>
            <tr>
                <th>Address</th>
                <td>
                    {{ $subscription->SalesData->address ?? '' }}<br>
                    {{ $subscription->SalesData->near_location ?? '' }}<br>
                    PIN: {{ $subscription->SalesData->pincode ?? '' }}
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Product Details</h3>
        <table>
            <tr>
                <th>Product Name</th>
                <td>{{ $subscription->SalesData->item ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Serial Number</th>
                <td>{{ $subscription->serial }}</td>
            </tr>
            <tr>
                <th>Bill Details</th>
                <td>
                    {{ $subscription->SalesData->bill_no ?? '' }} / 
                    {{ $subscription->SalesData->bill_date ?? '' }}
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>AMC Details</h3>
        <table>
            <tr>
                <th>Plan Name</th>
                <td>
					@php
						$plan_data = optional($subscription->AmcData)?->plan_id 
							? App\Models\AmcPlanType::find($subscription->AmcData->plan_id) 
							: null;
					@endphp
					{{optional($plan_data)->name}}
				</td>
            </tr>
            <tr>
                <th>Duration</th>
                <td>{{ optional($subscription->AmcData)->duration ?? 'N/A' }} days</td>
            </tr>
            <tr>
                <th>Validity</th>
                <td>
                    {{ $subscription->amc_start_date }} to 
                    {{ $subscription->amc_end_date }}
                </td>
            </tr>
            <tr>
                <th>Purchase Amount</th>
                <td>{{ number_format($subscription->purchase_amount, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Generated on: {{ date('Y-m-d H:i:s') }}
    </div>
</body>
</html>