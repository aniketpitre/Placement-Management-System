<?php
session_start();
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "dbms_project";
	$conn =new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) {
		session_unset(); 
		session_destroy();
		die("Connection failed: " . $conn->connect_error);
	}
//	$roll = $c_name = $field = $c_url = $c_id = $c_date = $c_venue = $s_cgpa = $p_year = $c_url = "";
	if(isset($_SESSION['username'])&& isset($_SESSION['password']))
	{
		$sql = "SELECT count(*)  FROM login where username = ? and password = ?";
		$stmt = $conn->prepare($sql);
		if ($stmt) 
		{
			$stmt->bind_param("ss",$_SESSION['username'],$_SESSION['password']);
			$stmt->execute();
			$stmt->bind_result($count);
			$stmt->fetch();
			$stmt->close();
			if($count==1)
			{
				$sql = "SELECT acc_right  FROM login where username = ? and password = ?";
				$stmt = $conn->prepare($sql);
				if ($stmt) 
				{
					$stmt->bind_param("ss",$_SESSION['username'],$_SESSION['password']);
					$stmt->execute();
					$stmt->bind_result($ar);
					$stmt->fetch();
					$stmt->close();
					
					if($ar == 0)
					{
						$conn->close();
						header('Location: student_o.php');
					}
					else
					{
						$conn->close();
						header('Location: admin.php');
					}
				}
			}
			else
			{
				$sql = "SELECT count(*)  FROM c_login where username = ? and password = ?";
				$stmt = $conn->prepare($sql);
				$stmt->bind_param("is",$_SESSION['username'],$_SESSION['password']);
				$stmt->execute();
				$stmt->bind_result($count);
				$stmt->fetch();
				$stmt->close();
				if($count==1)
				{
					$sql = "SELECT c_name  FROM company where c_id = ?";
					$stmt = $conn->prepare($sql);
					$stmt->bind_param("i",$_SESSION['username']);
					$stmt->execute();
					$stmt->bind_result($c_name);
					$c_id=$_SESSION['username'];
					$stmt->fetch();
					$stmt->close();
				}
				else
				{
					$conn->close();
					header('Location: logout.php');
				}
			}
		}
		else
		{
			$stmt->close();
			$conn->close();
			header('Location: logout.php');
		}
	}
	else
	{
		$conn->close();
		header('Location: logout.php');
	}
?>
<!doctype html>
<html>
<head>
	<title>Offer-Placement</title>
	<link rel="stylesheet" href="../css/history.css">
</head>
<body style="background: #dcd9d9">
	<?php
	
				$s_id='14CS01018';
				$pk_o= '5000000';
				if( preg_match('/\d{2}[A-Z]{2}\d{5}/',$s_id) && $pk_o>=500000 && $pk_o<=99999999)
				{
					$sql = "SELECT s_name,branch,pno,e_id,cgpa,p_year FROM student where s_id = ?";
					$stmt = $conn->prepare($sql);
					$stmt->bind_param("s",$s_id);
					$stmt->execute();
					$stmt->bind_result($s_name,$branch,$pno,$e_id,$cgpa,$p_year);
					$stmt->fetch();
					$stmt->close();
				}
			
		
	?>
	<center>
		<p style="font-size: 0.5cm;">ONCE OFFERED CAN'T BE UNDONE</p>
		<form method="post" action="comp_select.php">
			<table cellspacing="0" cellpadding="0">
				<tbody style="font-size: 0.4cm;">

					<tr>
						<td>Company-id</td>
						<td>:</td>
						<td><input type="number" value="<?php echo $c_id;?>" name="c_id" readonly="readonly"/></td>
					</tr>

					
					<tr>
						<td>Student-id</td>
						<td>:</td>
						<td><input type="text" value="<?php echo $s_id;?>" name="s_id" readonly="readonly"/></td>
					</tr>
					
					<tr>
						<td>Student-name</td>
						<td>:</td>
						<td><input type="text" value="<?php echo $s_name;?>" readonly="readonly"/></td>
					</tr>
										
					<tr>
						<td>Package-offered</td>
						<td>:</td>
						<td><input type="number" value="<?php echo $pk_o;?>" name="pk_o" readonly="readonly"/></td>
					</tr>
					
					<tr>
						<td>Student-Pno</td>
						<td>:</td>
						<td><input type="number" value="<?php echo $pno;?>" readonly="readonly"/></td>
					</tr>
					
					<tr>
						<td>Student-Email</td>
						<td>:</td>
						<td><input type="text" value="<?php echo $e_id;?>" readonly="readonly"/></br></td>
					</tr>
					
					<tr>
						<td>Student-CGPA</td>
						<td>:</td>
						<td><input type="number" value="<?php echo $cgpa?>" readonly="readonly"/></td>
					</tr>
					
					<tr>
						<td>Student-Passing Year&nbsp</td>
						<td>:</td>
						<td><input type="number" value="<?php echo $p_year?>" readonly="readonly"/></td>
					</tr>

				</tbody>
			</table>
			</br>
			<button name="confirm" value="1">CONFIRM</button>
			<button name="cancel" value="1" >CANCEL</button>
		</form>
	</center>
	<?php
	?>
</body>
</html>