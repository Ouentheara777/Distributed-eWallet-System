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
    padding: 20px;
}
body {
    background-color: #f9f9f9;
}

.buttonHolder {
	text-align:center;
}
</style>


<body>

	<h1 align="center"> Upload Image </h1>
	<div class="buttonHolder">               
		<form method="POST" action="" enctype="multipart/form-data">
  			<input type="file" name="picture" accept="image/*">
			<br> <br> <br>
  			<input type="submit" value="Submit" name="submit">
		</form>
	</div>

    <?php 
        if($_SERVER["REQUEST_METHOD"] == "POST"){
          $app_info = array();
  	      $the_image = $_FILES['picture'];
  	      $the_name = $the_image['name'];

  	      $base64 = base64_encode(file_get_contents($_FILES['picture']['tmp_name']));
          $app_info = array("nama_gambar" => $the_name , "base64" => $base64); 

          $ch = curl_init("http://152.118.33.76/tugas4/server/postImage");
          curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Content-Type : application/json; charset=utf-8'),
            CURLOPT_POSTFIELDS => json_encode($app_info)
          ));
          
          $response = curl_exec($ch);
          if($response == FALSE){
           $result = "Upload gambar gagal!";
          } else {
            $result = "Gambar Sukses disimpan!";
          }
	      echo $result ;
	     }
      ?>
	
            
</body>

</html>
