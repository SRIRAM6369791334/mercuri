<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Product Price & Details Updated</title>
</head>
<body style="font-family: Arial, sans-serif; font-size: 14px; color: #333; line-height: 1.5;">

    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #e1e8ed; border-radius: 6px; overflow: hidden;">
        <div style="background-color: #0b5fa5; color: #ffffff; padding: 16px 20px;">
            <h3 style="margin: 0; font-size: 18px;">Product Master &mdash; Price &amp; Details Updated</h3>
            <p style="margin: 4px 0 0; font-size: 13px; opacity: 0.9;">Notification for Sales Team</p>
        </div>

        <div style="padding: 20px;">
            <p>Hello Team,</p>
            <p>
                The <strong>Design &amp; Costing</strong> details for the following product have been updated in the Product Master. Sales executives can now proceed with generating customer quotations.
            </p>

            <table cellpadding="6" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-top: 15px; background: #f9fbfd; border: 1px solid #eef2f5;">
                <tr style="border-bottom: 1px solid #eef2f5;">
                    <td style="width: 35%; color: #555;"><strong>Product Name</strong></td>
                    <td><strong>{{ $product_name }}</strong></td>
                </tr>
                @if(!empty($product_model))
                <tr style="border-bottom: 1px solid #eef2f5;">
                    <td style="color: #555;"><strong>Model</strong></td>
                    <td>{{ $product_model }}</td>
                </tr>
                @endif
                @if(!empty($part_no))
                <tr style="border-bottom: 1px solid #eef2f5;">
                    <td style="color: #555;"><strong>Part Number</strong></td>
                    <td>{{ $part_no }}</td>
                </tr>
                @endif
                <tr style="border-bottom: 1px solid #eef2f5;">
                    <td style="color: #555;"><strong>Category / Group</strong></td>
                    <td>{{ $category_name }} @if(!empty($sub_category_name)) &rsaquo; {{ $sub_category_name }} @endif</td>
                </tr>
                <tr style="border-bottom: 1px solid #eef2f5;">
                    <td style="color: #555;"><strong>Updated Basic Price (₹)</strong></td>
                    <td style="font-size: 16px; color: #2e7d32; font-weight: bold;">
                        ₹ {{ number_format((float)$new_rate, 2) }}
                        @if(!empty($old_rate) && (float)$old_rate != (float)$new_rate)
                            <span style="font-size: 12px; color: #888; font-weight: normal; text-decoration: line-through; margin-left: 8px;">
                                (Old: ₹ {{ number_format((float)$old_rate, 2) }})
                            </span>
                        @endif
                    </td>
                </tr>
                @if(!empty($gst_percent))
                <tr style="border-bottom: 1px solid #eef2f5;">
                    <td style="color: #555;"><strong>GST Rate</strong></td>
                    <td>{{ $gst_percent }}%</td>
                </tr>
                @endif
                @if(!empty($updated_by))
                <tr style="border-bottom: 1px solid #eef2f5;">
                    <td style="color: #555;"><strong>Updated By</strong></td>
                    <td>{{ $updated_by }} ({{ $updated_at }})</td>
                </tr>
                @endif
            </table>

            @if(!empty($related_leads) && count($related_leads) > 0)
                <div style="margin-top: 20px;">
                    <h4 style="margin: 0 0 8px; color: #0b5fa5; font-size: 14px;">Associated Enquiries / Leads Waiting for Quotation:</h4>
                    <table cellpadding="6" cellspacing="0" style="width: 100%; border-collapse: collapse; font-size: 13px; border: 1px solid #ddd;">
                        <thead>
                            <tr style="background: #eef4f8; text-align: left;">
                                <th style="padding: 6px; border: 1px solid #ddd;">Lead ID</th>
                                <th style="padding: 6px; border: 1px solid #ddd;">Customer</th>
                                <th style="padding: 6px; border: 1px solid #ddd;">Assigned Sales Rep</th>
                                <th style="padding: 6px; border: 1px solid #ddd; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($related_leads as $rl)
                                <tr>
                                    <td style="padding: 6px; border: 1px solid #ddd;"><strong>{{ $rl['lead_id'] }}</strong></td>
                                    <td style="padding: 6px; border: 1px solid #ddd;">{{ $rl['customer_name'] }}</td>
                                    <td style="padding: 6px; border: 1px solid #ddd;">{{ $rl['sales_rep'] }}</td>
                                    <td style="padding: 6px; border: 1px solid #ddd; text-align: center;">
                                        <a href="{{ $rl['quota_url'] }}" style="display: inline-block; padding: 4px 10px; background: #28a745; color: #fff; text-decoration: none; border-radius: 3px; font-size: 12px;">Create Quotation</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div style="margin-top: 25px; text-align: center;">
                <a href="{{ url('/products') }}" style="display: inline-block; padding: 10px 20px; background: #0b5fa5; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: bold;">
                    View in Product Master
                </a>
            </div>

            <p style="margin-top: 20px; font-size: 12px; color: #777;">
                This is an automated notification triggered upon updating product details in Mercuriis CRM.
            </p>
        </div>
    </div>

</body>
</html>
