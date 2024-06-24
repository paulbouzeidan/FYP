<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }
        .header h3 {
            margin: 0;
        }
        .content {
            padding: 20px;
        }
        .content p {
            font-size: 16px;
            line-height: 1.6;
            color: #333333;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #777777;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h3>Welcome To Our App</h3>
        </div>
        <div class="content">
            <p>Welcome {{$mailData}},</p>

            <p>We're excited to have you with us. YallaDone is here to make your life easier with a wide range of on-demand services, from car maintenance to personal errands.</p>
            <p>For any questions, our support team is here to help at support@yalladone.com</p>
            <p>Thanks for joining us!</p>
        </div>
        <div class="footer">
            <h5>Best,</h5>
            <h4><b>The YallaDone Team</b></h4>
        </div>
    </div>
</body>
</html>
