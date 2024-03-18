<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>View Invoice</title>
    <style>
        body {
            width: 100% !important;
            font-size: 12px;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif !important;
        }

        * {
            font-family: DejaVu Sans, sans-serif;
        }

        .container {
            width: 700px;
            margin-top: 50px;
            position: relative;
        }

        .outer_border {
            border: 1px solid #999999 !important;
            padding: 4% !important;
            margin-bottom: 2% !important;
        }

        .top_box1 {
            width: 52%;
            padding: 0%;
            width: calc(48% - 1%);
            padding: 0%;
            display: inline-block;
            vertical-align: top;
        }

        .top_box2 {
            width: 47%;
            padding: 0%;
            display: inline-block;
            vertical-align: top;
            /* margin-left: 20%; */
        }

        .table_pad {
            padding: 0% 2%;
        }

        .border {
            border: 1px solid #CCCCCC !important;
        }

        .small_text {
            font-size: 10px !important;
        }

        .bg_color1 {
            background: #3a5082;
            color: #fff;
        }

        .text_color1 {
            color: #3a5082;
        }

        td {
            padding: 4px;
        }

        .invoice-heading {
            text-align: center;
            margin-bottom: 20px;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .col-sm-6 {
            width: 100%;
        }
    </style>
</head>

<body>
    {{-- <div class="invoice-heading">
        <h2>INVOICE</h2>
    </div> --}}
    <div class="container">
        <div class="outer_border">
            <div class="row">
                <div class="top_box1">
                    <h2 class="text_color1" style="font-size: 30px;">{{ config('app.name') }}</h2>
                    {{ $address }}<br>
                    Phone: {{ $mobile }}<br>
                    Email: {{ $email }}<br>
                    Website: {{ config('app.url') }}
                </div>
                <div class="top_box2">
                    <h2 style="color:#687cbf;font-weight: bold;font-size:30px; text-align:right; padding-right: 30px;"
                        id="invoice">INVOICE</h2>
                    <table width="100%" height="70" class="table_pad">
                        <tr>
                            <td>Date</td>
                            <td>{{ date('d-m-Y', strtotime($purchase['date'])) }}</td>
                        </tr>
                        <tr>
                            <td>Invoice #</td>
                            <td>{{ $purchase['invoice_no'] }}</td>
                        </tr>
                        <tr>
                            <td>Customer ID</td>
                            <td>{{ $purchase['id'] }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row">
                <table width="100%" border="0">
                    <tr>
                        <td colspan="2">
                            <div class="bg_color1"
                                style="text-indent:10px;font-size: 14px;width: 50%;height: 26px;">
                                BILL TO</div>
                            <table width="100%" border="0">
                                <tr>
                                    <td width="18%">Name</td>
                                    <td width="82%">{{ $company_settings->name }}</td>
                                </tr>
                                <tr>
                                    <td>Phone</td>
                                    <td>{{ $company_settings->mobile }}</td>
                                </tr>
                                <tr>
                                    <td>Address</td>
                                    <td>{{ $company_settings->address }}</td>
                                </tr>
                                {{-- <tr>
                                    <td>City</td>
                                    <td>{{ $purchase['vendor']['city'] }}</td>
                                </tr> --}}
                                <tr>
                                    <td>Gst Number</td>
                                    <td>{{ $company_settings->gst_number }}</td>
                                {{-- </tr>
                                <tr>
                                    <td>ZIP</td>
                                    <td>{{ $order->billing_zip }}</td>
                                </tr> --}}
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                    </tr>
                </table>
            </div>
            <dd style="clear: both;"></dd>
            <div class="row">
                <table height="82" class="" style="width: 100%;">
                    <tr class="bg_color1">
                        <td width="30%" height="12" style="padding-left: 10px;">DESCRIPTION</td>
                        <td width="10%" height="12" style="padding-left: 5px;">QTY</td>
                        <td width="20%" height="12" style="padding-left: 5px;">UNIT PRICE</td>
                        <td width="13%" style="padding-right: 10px;" width="15%" align="right">AMOUNT</td>
                        <td width="13%" style="padding-right: 10px;" width="15%" align="right">GST</td>
                        <td style="padding-right: 10px;" width="15%" align="right">SUB TOTAL</td>
                    </tr>
                    @if ($purchase['invoiceProduct'] != null)
                        @php
                            $total_gst = 0;
                            $total_amount = 0;
                        @endphp
                        @foreach ($purchase['invoiceProduct'] as $item)
                        <tr class="">
                            @if ($job_orders->isNotEmpty())
                            @foreach ($job_orders as $jobOrder)
                            <td>{{ $jobOrder->description }} &#8377;{{ $item->unit }} X {{ $item->quantity }}</td>
                            @endforeach
                            @endif
                            <td align="center">{{ number_format($item->quantity) }}</td>
                            <td align="right">&#8377;{{ number_format($item->unit) }}</td>
                            <td align="right">&#8377;{{ number_format($item->unit * $item->quantity, 2) }}</td>
                            <td align="right">{{ $item->gst }}</td>
                            <td align="right">&#8377;{{ $item->total }}</td>
                            @php
                                $total_gst += $item->gst;
                                $total_amount += ($item->unit * $item->quantity);
                            @endphp
                        </tr>
                        @endforeach
                        <tr><td colspan="6"><hr></td></tr>
                        <tr class="">
                            <td></td>
                            <td></td>
                            <td><strong>Final Total</strong></td>
                            <td align="right">&#8377;{{ number_format($total_amount, 2) }}</td>
                            <td align="right">&#8377;{{ number_format($total_gst, 2) }}</td>
                            <td align="right">&#8377;{{ $final_total }}</td>
                        </tr>
                    @endif
                </table>
            </div>
            <div class="row">
                <div style="text-align: center">
                    If you have any question about this invoice, please contact<br>
                    {{ config('app.name') }}, {{ $mobile }}, {{ $email }}<br>
                    <b>Thank You For Your Business!</b>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
