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
        <li><a href="../getStatusLampu">Get Status Lampu</a></li>
        <li><a class="active" href="#">Set Status Lampu</a></li>
    </ul>


	<p align="center"> Set Lampu Page </p>
	<div>
        <form role="form" method="POST" action="">
    		<input type="radio" name="lampu" value="1"> 1 <br>
            <input type="radio" name="lampu" value="2"> 2 <br>
            <input type="radio" name="lampu" value="3"> 3 <br>
            <input type="radio" name="lampu" value="4"> 4 <br>
            <input type="radio" name="lampu" value="5"> 5 <br>
            <input type="radio" name="lampu" value="6"> 6 

            <input type="submit" value="TurnOff" name="submit_mati">
            <input type="submit" value="TurnOn" name="submit_nyala">
            <input type="submit" value="Redup" name="submit_redup">
 		</form>                
		<?php
        if(isset($_POST['submit_mati'])) {
            $id = $_POST["lampu"];
            
            $ch = curl_init("https://raditya.sisdis.ui.ac.id/ubiq/lampu/index.php?fungsi=set_lampu&id_device=" . $id . "&jml_arg=1&arg1=mati");
            curl_setopt_array($ch, array(
                CURLOPT_POST => TRUE,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_HTTPHEADER => array(
                'Content-Type : application/json; charset=utf-8')
            ));
          
            $response = curl_exec($ch);
            $json = json_decode($response);

            $current_data = $json->data;
            $current_status = $current_data->current_status;
            $result = "Set Lampu " . $id . " Sukses: " . $current_status ;
                    
            echo ("<h3>" . $result . "</h3>");
        }
        else if(isset($_POST['submit_nyala'])) {
            $id = $_POST["lampu"];
            
            $ch = curl_init("https://raditya.sisdis.ui.ac.id/ubiq/lampu/index.php?fungsi=set_lampu&id_device=" . $id . "&jml_arg=1&arg1=hidup");
            curl_setopt_array($ch, array(
                CURLOPT_POST => TRUE,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_HTTPHEADER => array(
                'Content-Type : application/json; charset=utf-8')
            ));
          
            $response = curl_exec($ch);
            $json = json_decode($response);

            $current_data = $json->data;
            $current_status = $current_data->current_status;
            $result = "Set Lampu " . $id . " Sukses: " . $current_status ;
                    
            echo ("<h3>" . $result . "</h3>");
        }
        else if(isset($_POST['submit_redup'])) {
            $id = $_POST["lampu"];
            
            $ch = curl_init("https://raditya.sisdis.ui.ac.id/ubiq/lampu/index.php?fungsi=set_lampu&id_device=" . $id . "&jml_arg=1&arg1=redup");
            curl_setopt_array($ch, array(
                CURLOPT_POST => TRUE,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_HTTPHEADER => array(
                'Content-Type : application/json; charset=utf-8')
            ));
          
            $response = curl_exec($ch);
            $json = json_decode($response);

            $current_data = $json->data;
            $current_status = $current_data->current_status;
            $result = "Set Lampu " . $id . " Sukses: " . $current_status ;
                    
            echo ("<h3>" . $result . "</h3>");
        }
        else {

        }
		?>

	</div>
            
</body>

</html>
