<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en-US" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Error 404</title>
        <link href='https://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>
        <style type="text/css">
        body,html{
            font-family: 'Lato', 'sans-serif';
            }
        div{
            position: absolute;
            width: 500px;
            height: 200px;
            top: 0;
            bottom: 0;
            margin-bottom: auto;
            margin-top: auto;
            left: 0;
            right: 0;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
            color: #555;
            }
        h2{
            font-size: 50px;
            margin: 0;
            }
        span{
            font-size: 16px;
            }
        </style>
    </head>

    <body>
             <div>
                 <h2>{{ trans('site.error-404') }}</h2>
                 <span>{{ trans('site.error-404-description') }}</span>
            </div>  

    </body>	

</html>