<!DOCTYPE html>
<table>
    <thead>
        <tr>
            <th colspan="9" style="font-size: 15px;font-weight: 500;color: red;text-align: center;">
                {{ env('APP_NAME') }}</th>
        </tr>
        <tr>
            <th colspan="9" style="font-weight: 500;text-align: center;color:#0b027e;">
                Balance Log Report
            </th>
        </tr>
        <tr>
            <th></th>
            <th colspan="2" style="font-weight: 500;"> Job Order : {{ $data['export_data'][0]['Job Order'] }}</th>

            <th></th>
            <th colspan="3" style="font-weight: 500; text-align: right;">Date Time : {{ date('d-m-Y - h:i A') }}</th>
        </tr>
        <tr>
            <th></th>
            <th colspan="2" style="font-weight: 500;"> Start Date :
                @if(isset($data['date_range']) && count(explode(' - ', $data['date_range'])) > 0)
                    {{ explode(' - ', $data['date_range'])[0] }}
                @else
                    ALL
                @endif
            </th>
        </tr>
        <tr>
            <th></th>
            <th colspan="2" style="font-weight: 500;"> End Date:
                @if (isset($data['date_range']) && count(explode(' - ', $data['date_range'])) > 1)
                    {{ explode(' - ', $data['date_range'])[1] }}
                @else
                    ALL
                @endif
            </th>
        </tr>

        <tr>
            <th style="width:50px;background: #0b027e;color:white;font-weight: 600;text-align: left">S.NO</th>
            <th style="width:200px;background: #0b027e;color:white;font-weight: 600;text-align: left">Job Order</th>
            <th style="width:100px;background: #0b027e;color:white;font-weight: 600;text-align: left">Date</th>
            <th style="width:300px;background: #0b027e;color:white;font-weight: 600;text-align: left">Description</th>
            <th style="width:100px;background: #0b027e;color:white;font-weight: 600;text-align: left">Credit</th>
            <th style="width:100px;background: #0b027e;color:white;font-weight: 600;text-align: left">Debit</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['export_data'] as $da)
            @if ($da['S.No'] == '')
                <tr>
                    <td style="text-align: left;font-weight: 600;"><b>{{ $da['S.No'] }}</b></td>
                    <td style="text-align: left;font-weight: 600;">{{ $da['Job Order'] }}</td>
                    <td style="text-align: left;font-weight: 600;">{{ $da['Date'] }}</td>
                    <td style="text-align: left;font-weight: 600;">{{ $da['Description'] }}</td>
                    <td style="text-align: left;font-weight: 600;">₹{{ number_format($da['Credit'], 2) }}</td>
                    <td style="text-align: right;font-weight: 600;">₹{{ number_format($da['Debit'], 2) }}</td>
                </tr>
            @else
                <tr>
                    <td style="text-align: left;">{{ $da['S.No'] }}</td>
                    <td style="text-align: left;">{{ $da['Job Order'] }}</td>
                    <td style="text-align: left;">{{ $da['Date'] }}</td>
                    <td style="text-align: left;">{{ $da['Description'] }}</td>
                    <td style="text-align: right;">₹{{ number_format($da['Credit'], 2) }}</td>
                    <td style="text-align: right;">₹{{ number_format($da['Debit'], 2) }}</td>
                </tr>
            @endif
        @endforeach
        <!-- Display Total Credit and Debit -->
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td style="font-weight: 600;">Total</td>
            <td style="text-align: right;font-weight: 600;">₹{{ number_format($data['total_credit'], 2) }}</td>
            <td style="text-align: right;font-weight: 600; font:bold">₹{{ number_format($data['total_debit'], 2) }}</td>
            <td></td>
        </tr>
    </tbody>
</table>
