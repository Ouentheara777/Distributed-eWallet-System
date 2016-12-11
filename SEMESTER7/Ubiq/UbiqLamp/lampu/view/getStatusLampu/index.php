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
        <li><a class="active" href="#" >Get Status Lampu</a></li>
        <li><a href="../setStatusLampu">Set Status Lampu</a></li>
    </ul>


	<p align="center"> Get Status Page </p>
	<div>
        <form role="form" method="POST" action="">
    		<input type="submit" value="Check Health" name="submit">
 		</form>                
		<?php
        if(isset($_POST['submit'])) {
        echo "<table> <tr>";
    
        $kata = "halo";
    
        for ($i=1; $i<7 ; $i++) {
            $ch = curl_init("https://raditya.sisdis.ui.ac.id/ubiq/lampu/index.php?fungsi=get_lampu&id_device=" . $i . "&jml_arg=0");
            curl_setopt_array($ch, array(
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_HTTPHEADER => array(
                'Content-Type : application/json; charset=utf-8')
            ));
          
            $response = curl_exec($ch);
            $json = json_decode($response);

            $current_data = $json->data;
            $current_status = $current_data->current_status;

            if ($i < 4) {
                echo '<td><center>'.$current_status.'</td>';
                
                
            } else {
                if ($i==4) {
                    echo '</tr><tr>';
                    echo '<td><center>'.$current_status.'</td>';
                   
                } else {
                    echo '<td><center>'.$current_status.'</td>';
                    
                }
            }
        }
         echo "</tr> </table>";
        }
		?>

	</div>
            
</body>

</html>
