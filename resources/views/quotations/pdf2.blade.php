<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Commercial Offer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .container {
            width: 100%;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .header img {
            height: 50px;
        }
        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }
        .gst {
            text-align: right;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
        }
        .footer div {
            margin-bottom: 5px;
        }
        .bank-details {
            margin-top: 10px;
        }
        @media print{
            @page {
                size: A3 landscape;
                margin: 15mm;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <img src="{{ public_path('logo.png') }}" alt="Company Logo">
            <div class="title">Commercial Offer</div>
            <div class="gst">GST No: 29AABCM8922D1ZG</div>
        </div>

        <!-- Table -->
        <table>
            <tr>
                <th>Sl. No</th>
                <th>Part No</th>
                <th>Description of Goods</th>
                <th>HSN/SAC Code</th>
                <th>Unit</th>
                <th>Quantity</th>
                <th>List Price (INR)</th>
                <th>Rate Unit (INR)</th>
                <th>Total Rate (INR)</th>
                <th>CGST Rate</th>
                <th>CGST Amt</th>
                <th>SGST Rate</th>
                <th>SGST Amt</th>
                <th>IGST Rate</th>
                <th>IGST Amt</th>
                <th>Total + Tax (INR)</th>
            </tr>
            <tr>
                <td>1</td>
                <td>50012639</td>
                <td>MHE,SP-36 STD,FL-1220MM,FW-685MM,SH-90MM,LH-220MM</td>
                <td>84271000</td>
                <td>Nos</td>
                <td>1.00</td>
                <td>551,971.00</td>
                <td>551,971.00</td>
                <td>551,971.00</td>
                <td>18.0%</td>
                <td>99,354.78</td>
                <td>18.0%</td>
                <td>99,354.78</td>
                <td>0%</td>
                <td>0</td>
                <td>651,325.78</td>
            </tr>
            <tr>
                <td>2</td>
                <td>500272011</td>
                <td>CHARGER 24V, 40 A</td>
                <td>8504030-18%</td>
                <td>Nos</td>
                <td>1.00</td>
                <td>40,000.00</td>
                <td>40,000.00</td>
                <td>40,000.00</td>
                <td>18.0%</td>
                <td>7,200.00</td>
                <td>18.0%</td>
                <td>7,200.00</td>
                <td>0%</td>
                <td>0</td>
                <td>47,200.00</td>
            </tr>
        </table>

        <!-- Total Value -->
        <div class="footer">
            <div><strong>Total Quotation Value (In Words):</strong> Rupees Six Lakhs Ninety Eight Thousand Five Hundred Twenty Five And Paise Seventy Eight Only</div>
            <div><strong>Total:</strong> 591,971.00 | Tax: 106,554.78 | Grand Total: 698,525.78</div>
        </div>

        <!-- Payment & Warranty Terms -->
        <div class="footer">
            <div><strong>Payment Terms:</strong> 100% Advance</div>
            <div><strong>Price:</strong> Ex Works Maini Materials Movement - Bengaluru</div>
            <div><strong>Dispatch:</strong> 6 weeks from receipt of PO and advance payment</div>
            <div><strong>Unloading Scope:</strong> Customer</div>
            <div><strong>Standard Warranty:</strong> 12 Months or 2000 Hrs from commissioning date</div>
            <div><strong>Essential Documents:</strong> PO, Advance Payment, E-Way Bill, Declaration Form</div>
        </div>

        <!-- Bank Details -->
        <div class="bank-details">
            <strong>Our Bankers:</strong> Kotak Mahindra Bank,
            <strong>Account Number:</strong> 9411724790,
            <strong>IFSC Code:</strong> KKBK0000422,
            <strong>Swift Code:</strong> KKBKINBB095
        </div>

        <!-- Company Info -->
        <div class="footer">
            <div><strong>Maini Materials Movement Pvt. Ltd.</strong> Correspondence Address: 9, 3C Chandapura, Hosur Road, Bangalore-560 099, INDIA</div>
            <div><strong>Email:</strong> mmm@maini.com | <strong>Website:</strong> www.mainimaterials.com</div>
            <div><strong>PAN:</strong> AABCM8922D</div>
        </div>
    </div>

</body>
</html>
