<!DOCTYPE html>
<html>
<style>
input[type=text], select {
    width: 100%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    font-family:'Segoe UI';
}

h1 {
    font-family:'Segoe UI';
}

input[type=submit] {
    font-family:'Segoe UI';
    width: 30%;
    background-color: #1A89AD;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

input[type=submit]:hover {
    background-color: #167494;
}

div {
    font-family:'Segoe UI';
    border-radius: 5px;
}
body {
    background-color: #f9f9f9;
}

.buttonHolder {
	text-align:center;
}
</style>


<body>

	<h1 align="center"> View Image </h1>
    <img src="data:image; base64, <?php echo $image; ?>" alt="<?php echo $image_name; ?>"> <br>
    <br>
    <h4> Lokasi gambar: <?php echo $images_location; ?> </h4> <br>
    <h4> Ukuran gambar: <?php echo $image_size; ?> </h4>           
</body>

</html>
