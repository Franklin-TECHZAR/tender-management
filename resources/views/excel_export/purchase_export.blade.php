<!DOCTYPE html>
<table>
    <thead>
        <tr>
            <th colspan="9" style="font-size: 15px;font-weight: 500;color: red;text-align: center;">
                {{ env('APP_NAME') }}</th>
        </tr>
        <tr>
            <th colspan="9" style="font-weight: 500;text-align: center;color:#0b027e;">
                Purchase Report
            </th>
        </tr>
        <tr>
            <th></th>
            <th colspan="2" style="font-weight: 500;"> Job Order : {{ $data['export_data'][0]['Job Order'] }}</th>

            <th></th>
            <th></th>
            <th colspan="4" style="font-weight: 500; text-align: right;">Date Time : {{ date('d-m-Y - h:i A') }}</th>
        </tr>
        <tr>
            <th style="width:50px;background: #0b027e;color:white;font-weight: 600;text-align: left">S.NO</th>
            <th style="width:200px;background: #0b027e;color:white;font-weight: 600;text-align: left">Type</th>
            <th style="width:100px;background: #0b027e;color:white;font-weight: 600;text-align: left">Date</th>
            <th style="width:100px;background: #0b027e;color:white;font-weight: 600;text-align: left">Invoice No</th>
            <th style="width:200px;background: #0b027e;color:white;font-weight: 600;text-align: left">Vendor</th>
            <th style="width:200px;background: #0b027e;color:white;font-weight: 600;text-align: left">Product/Material</th>
            <th style="width:100px;background: #0b027e;color:white;font-weight: 600;text-align: left">Quantity</th>
            <th style="width:100px;background: #0b027e;color:white;font-weight: 600;text-align: right">Amount</th>
            <th style="width:100px;background: #0b027e;color:white;font-weight: 600;text-align: right">GST</th>
            <th style="width:100px;background: #0b027e;color:white;font-weight: 600;text-align: right">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['export_data'] as $da)
            @if ($da['S.No'] == '')
                <tr>
                    <td style="text-align: left;font-weight: 600;"><b>{{ $da['S.No'] }}</b></td>
                    <td style="text-align: left;font-weight: 600;">{{ $da['Type'] }}</td>
                    <td style="text-align: left;font-weight: 600;">{{ $da['Date'] }}</td>
                    <td style="text-align: left;font-weight: 600;">{{ $da['Invoice No'] }}</td>
                    <td style="text-align: left;font-weight: 600;">{{ $da['Vendor'] }}</td>
                    <td style="text-align: left;font-weight: 600;">{{ $da['Product/Material'] }}</td>
                    <td style="text-align: left;font-weight: 600;">{{ $da['Quantity'] }}</td>
                    <td style="text-align: right;font-weight: 600;">{{ $da['Amount'] }}</td>
                    <td style="text-align: right;font-weight: 600;">{{ $da['GST'] }}</td>
                    <td style="text-align: right;font-weight: 600;">{{ $da['Total'] }}</td>
                </tr>
            @else
                <tr>
                    <td style="text-align: left;">{{ $da['S.No'] }}</td>
                    <td style="text-align: left;">{{ $da['Type'] }}</td>
                    <td style="text-align: left;">{{ $da['Date'] }}</td>
                    <td style="text-align: left;">{{ $da['Invoice No'] }}</td>
                    <td style="text-align: left;">{{ $da['Vendor'] }}</td>
                    <td style="text-align: left;">{{ $da['Product/Material'] }}</td>
                    <td style="text-align: left;">{{ $da['Quantity'] }}</td>
                    <td style="text-align: right;">{{ $da['Amount'] }}</td>
                    <td style="text-align: right;">{{ $da['GST'] }}</td>
                    <td style="text-align: right;">{{ $da['Total'] }}</td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
