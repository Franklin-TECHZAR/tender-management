<!DOCTYPE html>
<table>
    <thead>
        <tr>
            <th colspan="9" style="font-size: 15px;font-weight: 500;color: red;text-align: center;">
                {{ env('APP_NAME') }}</th>
        </tr>
        <tr>
            <th colspan="9" style="font-weight: 500;text-align: center;color:#0b027e;">
                Expenses Report
            </th>
        </tr>
        <tr>
            <th></th>
            <th colspan="2" style="font-weight: 500;"> Job Order : {{ $data['export_data'][0]['Job Order'] }}</th>
            <th></th>
            <th></th>
            <th colspan="3" style="font-weight: 500; text-align: right;">Date Time : {{ date('d-m-Y - h:i A') }}</th>
        </tr>
        <tr>
            <th></th>
            <th colspan="2" style="font-weight: 500;"> Start Date : {{ explode(' - ', $data['date_range'])[0] }}</th>
        </tr>
        <tr>
            <th></th>
            <th colspan="2" style="font-weight: 500;"> End Date  : {{ explode(' - ', $data['date_range'])[1] }}</th>
        </tr>
        <tr>
            <th style="width:50px;background: #0b027e;color:white;font-weight: 600;text-align: left">S.NO</th>
            <th style="width:200px;background: #0b027e;color:white;font-weight: 600;text-align: left">Payment To</th>
            <th style="width:100px;background: #0b027e;color:white;font-weight: 600;text-align: left">Date</th>
            <th style="width:150px;background: #0b027e;color:white;font-weight: 600;text-align: left">Type</th>
            <th style="width:300px;background: #0b027e;color:white;font-weight: 600;text-align: left">Description</th>
            <th style="width:100px;background: #0b027e;color:white;font-weight: 600;text-align: left">Payment Mode</th>
            <th style="width:120px;background: #0b027e;color:white;font-weight: 600;text-align: left">Payment Details</th>
            <th style="width:100px;background: #0b027e;color:white;font-weight: 600;text-align: right">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['export_data'] as $da)
            @if ($da['S.No'] == '')
                <tr>
                    <td style="text-align: left;font-weight: 600;"><b>{{ $da['S.No'] }}</b></td>
                    <td style="text-align: left;font-weight: 600;">{{ $da['Payment To'] }}</td>
                    <td style="text-align: left;font-weight: 600;">{{ $da['Date'] }}</td>
                    <td style="text-align: left;font-weight: 600;">{{ $da['Type'] }}</td>
                    <td style="text-align: left;font-weight: 600;">{{ $da['Description'] }}</td>
                    <td style="text-align: left;font-weight: 600;">{{ $da['Payment Mode'] }}</td>
                    <td style="text-align: left;font-weight: 600;">{{ $da['Payment Details'] }}</td>
                    <td style="text-align: right;font-weight: 600;">{{ $da['Amount'] }}</td>
                </tr>
            @else
                <tr>
                    <td style="text-align: left;">{{ $da['S.No'] }}</td>
                    <td style="text-align: left;">{{ $da['Payment To'] }}</td>
                    <td style="text-align: left;">{{ $da['Date'] }}</td>
                    <td style="text-align: left;">{{ $da['Type'] }}</td>
                    <td style="text-align: left;">{{ $da['Description'] }}</td>
                    <td style="text-align: left;">{{ $da['Payment Mode'] }}</td>
                    <td style="text-align: left;">{{ $da['Payment Details'] }}</td>
                    <td style="text-align: right;">{{ $da['Amount'] }}</td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
