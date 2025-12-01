<!DOCTYPE html>
<html>
<head>
	<title>SPARE RETURN | {{ $transaction_id }}</title>
</head>
<body>
    <div>
        @foreach ($data as $item)
        <div style="margin: 0 auto 8px; width: 205px; ">
            <img style="width: 100%;"  alt="Barcoded value {{$item->barcode_no}}"
src="https://bwipjs-api.metafloor.com/?bcid=code128&text={{$item->barcode_no}}&height=6&textsize=14&scale=6&includetext">
            <span style="text-align: center; display: block; margin-top: 5px; font-size: 13px; font-weight: bold; ">{{$item->products->title}}</span>
        </div>
        @endforeach
    </div> 
</body>
</html>