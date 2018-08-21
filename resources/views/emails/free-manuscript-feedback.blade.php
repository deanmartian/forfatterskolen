<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <style type="text/css" rel="stylesheet" media="all">
        body {
            margin-left: 50px;
            font-family: Arial;
        }

        li {
            padding: 5px;
        }

        /* Media Queries */
        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>
</head>

<body>

<div>
    <img src="http://www.forfatterskolen.no/images/logo.png" style="height: 80px; margin-bottom: 36px;">
</div>

<p style="margin-top: 30px; font-family: Arial">
    <?php echo $email_content?>
</p>

</body>
</html>
