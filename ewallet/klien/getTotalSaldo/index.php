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

p {
    font-family:'Segoe UI';
    font-size: 22pt;
}

input[type=submit] {
    font-family:'Segoe UI';
    width: 100%;
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
    padding: 0 250px 0 250px;
}
body {
    background-color: #f9f9f9;
    margin:0;
}

ul {
    font-family:'Segoe UI';
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
    border: 1px solid #e7e7e7;
    background-color: #f3f3f3;
}

li {
    font-family:'Segoe UI';
    float: left;
}

li a {
    font-family:'Segoe UI';
    display: block;
    color: #666;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
}

li a:hover:not(.active) {
    background-color: #ddd;
}


.active {
    background-color: #1A89AD;
    color: #ffffff;
}
</style>


<body>

	<ul>
        <li><a href="../register">Register</a></li>
        <li><a href="../getSaldo">Get Saldo</a></li>
        <li><a href="../transfer">Transfer</a></li>
        <li><a class="active" href="#">Get Total Saldo</a></li>
    </ul>


	<p align="center"> Get Total Saldo Page </p>
	<div>
        <form role="form" method="POST" action="">
    		<label for="user_id">User ID: </label>
    		<input type="text" id="user_id" name="user_id" value="<?php echo isset($_POST['user_id']) ? $_POST['user_id'] : '' ?>">
   			
    		<input type="submit" value="Submit" name="submit">
 		</form>                
		<?php
        if(isset($_POST['submit'])) {
            $id = $_POST["user_id"];
            $app_info = array();

            $app_info = array("user_id" => $id); 

            $ch = curl_init("https://raditya.sisdis.ui.ac.id/ewallet/getTotalSaldo");
            curl_setopt_array($ch, array(
                CURLOPT_POST => TRUE,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_HTTPHEADER => array(
                'Content-Type : application/json; charset=utf-8'),
                CURLOPT_POSTFIELDS => json_encode($app_info)
            ));
          
            $response = curl_exec($ch);
            $json = json_decode($response);

            $nilai_saldo = $json->nilai_saldo;

            if($nilai_saldo == -1){
                $result = "User tidak terdaftar!";
            } else {
                $result = "Nilai saldo: " . $nilai_saldo ;
            }

                    
            echo ("<h3>" . $result . "</h3>");
        }
		?>

	</div>
            
</body>

</html>
