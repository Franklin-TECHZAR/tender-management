<!DOCTYPE html>
<table>
    <thead>
        <tr>
            <th colspan="6" style="font-size: 15px;font-weight: 500;color: red;text-align: center;">
                {{ env('APP_NAME') }}</th>
        </tr>
        <tr>
            <th colspan="6" style="font-weight: 500;text-align: center;color:#0b027e;">
                Tender Payments
            </th>
        </tr>
        <tr>
            <th></th>
            <th colspan="2" style="font-weight: 500;"> Tender Name : {{ $data['tender_name'] }}</th>
            <th style="font-weight: 500;">Date Time : {{ date('d-m-Y - h:i A') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['export_data'] as $index => $payment_for)
            <tr>
                <th colspan="6"></th>
            </tr>
            <tr>
                <th style="font-weight: 700;text-align: center" colspan="6">{{ $payment_for['payment_for'] }}</th>
            </tr>

            <tr>
                <th style="width:50px;background: #0b027e;color:white;font-weight: 600;text-align: left">S.NO</th>
                <th style="width:100px;background: #0b027e;color:white;font-weight: 600;text-align: left">Date</th>
                <th style="width:300px;background: #0b027e;color:white;font-weight: 600;text-align: left">Description
                </th>
                <th style="width:100px;background: #0b027e;color:white;font-weight: 600;text-align: right">Credit</th>
                <th style="width:100px;background: #0b027e;color:white;font-weight: 600;text-align: right">Debit</th>
                <th style="width:100px;background: #0b027e;color:white;font-weight: 600;text-align: right">Balance</th>
            </tr>

            @foreach ($payment_for['data'] as $da)
                @if ($da['sno'] == '')
                    <tr>
                        <td style="text-align: left;font-weight: 600;"><b>{{ $da['sno'] }}</b></td>
                        <td style="text-align: left;font-weight: 600;">{{ $da['date'] }}</td>
                        <td style="text-align: left;font-weight: 600;">{{ $da['description'] }}</td>
                        <td style="text-align: right;font-weight: 600;">{{ $da['credit'] }}</td>
                        <td style="text-align: right;font-weight: 600;">{{ $da['debit'] }}</td>
                        <td style="text-align: right;font-weight: 600;">{{ $da['balance'] }}</td>
                    </tr>
                @else
                    <tr>
                        <td style="text-align: left;">{{ $da['sno'] }}</td>
                        <td style="text-align: left;">{{ $da['date'] }}</td>
                        <td style="text-align: left;">{{ $da['description'] }}</td>
                        <td style="text-align: right;">{{ $da['credit'] }}</td>
                        <td style="text-align: right;">{{ $da['debit'] }}</td>
                        <td style="text-align: right;">{{ $da['balance'] }}</td>
                    </tr>
                @endif
            @endforeach
        @endforeach
    </tbody>
</table>
