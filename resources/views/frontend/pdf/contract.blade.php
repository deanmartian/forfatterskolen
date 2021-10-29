<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>{{ $contract->title }}</title>
    <style>

        blockquote {
            padding: 10px 20px;
            margin: 0 0 20px;
            font-size: 17.5px;
            border-left: 5px solid #eee;
        }

        p {
            margin-bottom: 10px;
        }

        .top-image {
            width: 100%;
            height: 250px;
        }
    </style>
</head>

<body>
@if ($contract->image)
    <img src="{{ asset($contract->image) }}" alt="" class="top-image">
@endif
{!! $contract->details !!}

@if ($contract->signature)
<h3>
    {{ $contract->signature_label }}
</h3>
<img src="{{ asset($contract->signature) }}" style="height: 100px;">
@endif
</body>

</html>