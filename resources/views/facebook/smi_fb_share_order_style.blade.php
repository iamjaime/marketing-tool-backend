<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Social Media Income | SMI Platform">
    <meta property="og:url" content="{{ $order->url }}" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ $order->title }}" />
    <meta property="og:description" content="{{ $order->description }}"
    />
    <meta property="og:image" content="{{ $order->image_url }}" />

</head>
<body>

<script type="text/javascript">
    @isset($instagram_username)
        @if($agent->is('iPhone'))
            window.location('instagram://user?username=' + '{{ $instagram_username }}');
        @endif
    @endisset

    @unless($instagram_username)
        window.location('{{ $order->url }}');
    @endunless
</script>


</body>
</html>