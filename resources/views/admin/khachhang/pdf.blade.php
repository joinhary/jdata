<!DOCTYPE html>
<html lang="vi">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Ảnh thật cho đương sự</title>
</head>
<style>
	img {
		width:360px;
		height:270px;
	}
	body{
		text-align: center;
		margin-top: 10px;
		font-size: 20px;
		font-weight: bold;
		font-family: arial;
	}

	#qr-code {
		width:160px;
		height:auto;
	};
	td {
		width:50%;
	}
</style>
<body>
	<table>
		<tr>
			<td>
				<div>
					<h2>Thong tin khach hang</h2>
					<h5>ID khach hang: {{ $khach_hang_id }}</h5>
					<h5>ID ho so     : {{ $ho_so_id }}</h5>
					<div style="text-align: center">
						<img id="qr-code" src="{{ $qr_code }}" ><br>
							<span>QR CODE</span>
					<div>
				</div>

			</td>
			<td>
				<div>
			  		<p>Anh 1</p>
				  	<img src="{{ $img2 }}"><br>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div>
			  		<p>Anh 2</p>
				  	<img src="{{ $img1 }}"><br>
				</div>
			</td>
			<td>
				<div>
			  		<p>Anh 3</p>
				  	<img src="{{ $img3 }}"><br>
				</div>
			</td>
		</tr>
	</table>
</body>
</html>
