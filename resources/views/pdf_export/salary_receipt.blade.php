<!DOCTYPE html>
<html lang="en">

<head>
    <title>Payment Voucher</title>
    <!-- Add your custom CSS styles here -->
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        .container {
            border: 1px solid #000;
            padding: 0px;
            margin: 35px 35px;
        }

        .card-header {
            padding-bottom: 30px;
            /* Padding for the headline part */
        }

        .card-body {
            padding-bottom: 30px;
            /* Padding for the body part */
        }

        .receipt-info p {
            padding: 3px 0px 3px;
            text-align: right;
        }

        .underline {
            display: inline-block;
            padding: 5px;
            border-bottom: 1px solid transparent;
            width: 100%;
        }

        .underline span {
            display: block;
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        table {
            margin: 0 auto;
            /* Center the table */
            border-collapse: collapse;
            width: 85%;
            /* border: 1px solid #000; Add border to the table */
        }

        .Order {
            margin: 0 auto;
            /* Center the table */
            border-collapse: collapse;
            width: 95%;
            /* border: 1px solid #000; Add border to the table */
        }

        .footer {
            margin: 35px 110px 8px 150px;
            border-collapse: collapse;
            width: 100%;
        }

        .border {
            padding: 0px;
            border: none;
            border-bottom: 1px solid #000;
        }
        .colon{
            width: 15%;
        }
        h2{
            margin-bottom: 10px;
        }
        .light-bold {
        font-weight: 200; /* or any other style you prefer */
    }

    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card mb-3">
            <div class="card-header text-center border">
                <h1>{{ $name }}</h1>
                <p>{{ $address }}</p>
                <p><span class="light-bold">Mobile No:</span> {{ $mobile }},  <span class="light-bold">Email:</span> {{ $email }}</p>
                <h2>Salary Voucher</h2>
            </div>

            <div class="card-body border">
                <div class="row">
                    <div class="col-md-12 border">
                        <table class="Order">
                            <tr>
                                <td><strong>Job Order:</strong>
                                    {{ $salary->job_order }}</span></span></td>
                                <td>
                                    <div class="receipt-info">

                                        <p><strong>Payment Voucher No:</strong>
                                            <span><span>{{ $salary->id }}</span></span></p>
                                            <p><strong>Date:</strong> <span><span>{{ date('d-m-Y', strtotime($salary->date)) }}</span></span></p>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="receipt-amount" style="padding: 1px 0px 4px;">
                        <table>
                            <tr>
                                <td><strong>Amount</strong></td>
                                <td class="colon">:</td>
                                <td><span class="underline"><span><span style="font-family: DejaVu Sans; sans-serif;">&#8377;{{ number_format($salary->amount, 2) }}</span></span></td>
                            </tr>
                            <tr>
                                <td><strong>Payment Method</strong></td>
                                <td class="colon">:</td>
                                <td><span
                                        class="underline"><span>{{ $salary->payment_mode }}</span></span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Payment Details</strong></td>
                                <td class="colon">:</td>
                                <td><span class="underline"><span>{{ $salary->payment_details }}</span></span></td>
                            </tr>
                            <tr>
                                <td><strong>Amount Paid To</strong></td>
                                <td class="colon">:</td>
                                <td><span class="underline"><span>{{ $salary->labour }}</span></span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center">
            <table class="footer">
                <tr>
                    <td><strong>Authorized by</strong></td>
                    <td><span class="underline"><span></span></span></td>
                    <td><strong>Received by</strong></td>
                    <td><span class="underline"><span></span></span></td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
