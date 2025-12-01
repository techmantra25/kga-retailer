<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KGA - Payment Success</title>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: 'Rubik', sans-serif;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .not-found-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            text-align: center;
        }
        .not-found-wrapper .content {
            background: #ffffff;
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
        }
        .not-found-wrapper h2 {
            font-size: 48px;
            font-family: 'Rubik', sans-serif;
            color: #00a018;
            font-weight: bold;
            margin: 0 0 10px;
        }
        .not-found-wrapper p {
            font-size: 24px;
            font-family: 'Rubik', sans-serif;
            font-weight: bold;
            margin: 0 0 20px;
            color: #333;
        }
        .not-found-wrapper button {
            font-size: 16px;
            font-family: 'Rubik', sans-serif;
            color: #ffffff;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            background-color: #000000;
            border-radius: 30px;
            border: none;
            box-shadow: none;
            outline: none;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 15px 40px;
            margin-top: 20px;
        }
        .not-found-wrapper button:hover {
            background-color: #00a018;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="not-found-wrapper">
        <div class="content">
            <h2>Thanks!</h2>
            <p>Payment has been successfully completed</p>
            <button>Back to home</button>
        </div>
    </div>
</body>
</html>
