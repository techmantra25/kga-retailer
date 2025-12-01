<!DOCTYPE html>
<html>
<head>
	<title>KGAPO | {{ $order_no }}</title>
</head>
<body>
    <div>
        @foreach ($data as $item)
        <div style="margin: 0 auto 8px; width: 205px; ">
            
            <span style="text-align: center; display: block; margin-top: 5px; font-size: 13px; font-weight: bold; ">{{$item}}</span>
        </div>
        @endforeach
    </div> 
</body>
</html>