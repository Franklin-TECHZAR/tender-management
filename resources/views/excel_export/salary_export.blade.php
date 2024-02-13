<!DOCTYPE html>
<table>
    <thead>
        <tr>
            <th colspan="9" style="font-size: 15px;font-weight: 500;color: red;text-align: center;">
                {{ env('APP_NAME') }}</th>
        </tr>
        <tr>
            <th colspan="9" style="font-weight: 500;text-align: center;color:#0b027e;">
                Salaries Report <!-- Change 'Expenses Report' to 'Salaries Report' -->
            </th>
        </tr>
        <tr>
            <th></th>
            <th colspan="2" style="font-weight: 500;"> Job Order : {{ $data['export_data'][0]['Job Order'] }}</th>

            <th></th>
            <th></th>
            <th colspan="2" style="font-weight: 500; text-align: right;">Date Time : {{ date('d-m-Y - h:i A') }}</th>
        </tr>
        <tr>
            <th style="width:50px;background: #0b027e;color:white;font-weight: 600;text-align: left">S.NO</th>
            <th style="width:200px;background: #0b027e;color:white;font-weight: 600;text-align: left">Payment To</th>
            <th style="width:100px;background: #0b027e;color:white;font-weight: 600;text-align: left">Date</th>
            <!-- Remove 'Type' column -->
            <th style="width:300px;background: #0b027e;color:white;font-weight: 600;text-align: left">Description</th>
            <th style="width:100px;background: #0b027e;color:white;font-weight: 600;text-align: left">Payment Mode</th>
            <th style="width:200px;background: #0b027e;color:white;font-weight: 600;text-align: left">Payment Details</th>
            <th style="width:100px;background: #0b027e;color:white;font-weight: 600;text-align: right">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['export_data'] as $da)
            @if ($da['S.No'] == '')
                <tr>
                    <td style="text-align: left;font-weight: 600;"><b>{{ $da['S.No'] }}</b></td>
                    <td style="text-align: left;font-weight: 600;">{{ $da['Labour'] }}</td>
                    <td style="text-align: left;font-weight: 600;">{{ $da['Date'] }}</td>
                    <!-- Remove 'Type' column -->
                    <td style="text-align: left;font-weight: 600;">{{ $da['Description'] }}</td>
                    <td style="text-align: left;font-weight: 600;">{{ $da['Payment Mode'] }}</td>
                    <td style="text-align: left;font-weight: 600;">{{ $da['Payment Details'] }}</td>
                    <td style="text-align: right;font-weight: 600;">{{ $da['Amount'] }}</td>
                </tr>
            @else
                <tr>
                    <td style="text-align: left;">{{ $da['S.No'] }}</td>
                    <td style="text-align: left;">{{ $da['Labour'] }}</td>
                    <td style="text-align: left;">{{ $da['Date'] }}</td>
                    <!-- Remove 'Type' column -->
                    <td style="text-align: left;">{{ $da['Description'] }}</td>
                    <td style="text-align: left;">{{ $da['Payment Mode'] }}</td>
                    <td style="text-align: left;">{{ $da['Payment Details'] }}</td>
                    <td style="text-align: right;">{{ $da['Amount'] }}</td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>