<html>

<head>

    <meta charset="UTF-8">
    <style>
        *{margin:0;padding:0}

        table {
            position: absolute;
            bottom:150px;
            width: 85%;
        }

        body {
            font-family: Denk One, sans-serif;
            background-image: url('https://www.forfatterskolen.no/images-new/certificate/template.png');
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .certificate-content {
            padding: 50px;
        }

        .name-underlined {
            font-size:30px;
            text-align:center;
            text-decoration: underline;
        }

        .light-text {
            color: #b1b0b0;
        }

    </style>
</head>

<body>
<center>
    <div class="certificate-content">
        <div>
            <img src="https://www.forfatterskolen.no/images-new/logo-tagline.png" alt="Forfatterskolen" title="Logo" style="height: 6rem;">
        </div>
        <span style="font-size:50px; font-weight:bold">10 weeks novelcourse</span>
        <br><br>
        <span style="font-size:25px"><i>on Forfatterskolen</i></span>
        <br><br>
        <span style="font-size:25px" class="light-text"><i>has been completed by</i></span>
        <br><br>
        <div><h1 class="name-underlined">{LEARNERNAME}</h1></div>
        <br /><br />
        <span style="font-size:20px" class="light-text">10 course modules</span>
        <br />
        <span style="font-size:20px" class="light-text">2 assignments</span>
        <br />

        <table>
            <tr>
                <!-- Date -->
                <td style="width: 40%; text-align: center;">
                    <div style="padding-top: 12px" class="light-text"><b>{ISSUEDDATE}</b></div>
                    <div style="border-bottom: 1px solid #000; height: 19px;"></div>
                    <div><b>Dato</b></div>
                </td>
        
                <!-- Signature -->
                <td style="width: 40%; text-align: center;">
                    <div>
                        <img src="https://www.forfatterskolen.no/images-new/certificate/signature.png" style="height: 30px">
                    </div>
                    <div style="border-bottom: 1px solid #000; height: 20px;"></div>
                    <div><b>Signatur</b></div>
                </td>
            </tr>
        </table>

    </div>
</center>
</body>

</html>