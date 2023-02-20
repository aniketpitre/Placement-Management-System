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
	$year=date('y');
	if(date('m')>6)
	{
		$year=$year+1;
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
						$stmt->close();
						$conn->close();
						header('Location: student_o.php');
					}
					else
					{
						//he can stay in page
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
					$conn->close();
					header('Location: company.php');
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
//INSERT INTO `company` (`C_ID`, `C_NAME`, `FIELD`, `C_URL`) VALUES ('3', 'xyz', 'abc', 'xyz.com');	
?>
<!doctype html>
<html>
<head>
	<title>New Company Registration</title>
	<link rel="stylesheet" href="../CSS/history.css">
	<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.1.1.min.js"></script>
	<script>
		window.console = window.console || function(t) {};
	</script>
	<script>
		if(document.location.search.match(/type=embed/gi)) 
		{
			window.parent.postMessage("resize", "*");
		}
	</script>
</head>
<body>

	<header>
		<ul>
			<li><a class="active" href="#">Add new Company</a></li>
			<li><a href="admin_assig_dates.php">Assign dates to company</a></li>
			<li><a href="history.php" target="blank">History</a></li>
			<li style="float:right"><a href="logout.php">Logout</a></li>
			<li style="float:right" title="Open Database"><a href="../../phpmyadmin/db_structure.php?server=1&db=dbms_project&token=4d602d96c3bcb1d42faa6d3efafd63eb" target="blank">Logined as Administrator</a></li>
		</ul>
	</header>
	
	</br>
	<?php
		$c_id=$c_name=$field=$c_url=$c_pass1=$c_pass2="";
		$error_message="";
		$error_class="error";
		if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create']))
		{
			$c_id = test_input($_POST["c_id"]);
			$c_name = test_input($_POST["c_name"]);
			$field = test_input($_POST["field"]);
			$c_url = test_input($_POST["c_url"]);
			$c_pass1 = test_input($_POST["c_pass1"]);
			$c_pass2 = test_input($_POST["c_pass2"]);
			if(!empty($_POST["c_id"]) && !empty($_POST["c_name"]) && !empty($_POST["field"]) && !empty($_POST["c_pass1"]) && !empty($_POST["c_pass2"]))
			{
				$error=False;
				$error = $error || !(preg_match("/^[a-zA-Z ]*$/",$c_name) && strlen($c_name)<=30);//check if name only contains letters and whitespace 
				$error = $error || !preg_match('/^[0-9]{5}$/',$c_id);
				$error = $error || !(preg_match("/^[a-zA-Z ]*$/",$field) && strlen($field)<=30);
			//	$error = $error || !filter_var($c_url, FILTER_VALIDATE_URL);//to check valid url or not
				$error = $error || !strlen($c_url<=100);
				if($error)
				{
		//			echo "<script type='text/javascript'> alert('wrong input') </script>";
					$error_message='wrong input';
				}
				else if(!preg_match("/^[a-zA-Z0-9 @#_$%]*$/",$c_pass1) || !preg_match("/^[a-zA-Z0-9 @#_$%]*$/",$c_pass2))//to see password can only have these special charecters: @#_$%& 
				{
		//			echo "<script type='text/javascript'> alert('Password can contain only a-z, A-Z, 0-9, @, #, _, $, %') </script>";
					$error_message='Password can contain only a-z, A-Z,</br>0-9, @, #, _, $, %';
				}
				else if($c_pass1 !== $c_pass2)//to see if password and confirm password matches
				{
		//			echo "<script type='text/javascript'> alert('Password did not match!') </script>";
					$error_message='Password did not match!';
				}
				else if (floor($c_id/1000)!=$year)
				{
					$error_message='Company id should start with '.$year.' for 20'.$year.' bach';
				}
				else
				{
					//all conditions are satisfied
					$sql = "INSERT INTO C_LOGIN(username,password) VALUES (?,?)";
					$stmt = $conn->prepare($sql);
					if ($stmt) 
					{
						$stmt->bind_param("is",$c_id,$c_pass1);
						if(!$stmt->execute())
						{	$stmt->close();
							$error_message='c_id already exists!';
							$c_id="";
						}
						else
						{
							$stmt->close();
							$sql = "INSERT INTO company VALUES (?,?,?,?)";
							$stmt = $conn->prepare($sql);
							if($stmt)
							{
								$stmt->bind_param("isss",$c_id,$c_name,$field,$c_url);
								$stmt->execute();
								$stmt->close();
								$error_class="noerror";
								$error_message="company with ID= ".$c_id." NAME = ".$c_name." sucessfully added";
								$c_id=$c_name=$field=$c_url=$c_pass1=$c_pass2="";
							}
							
						}
					}
				}
			}
			else
			{
		//		echo "<script type='text/javascript'> alert('All fields should be filled') </script>";
				$error_message='All fields should be filled';
			}
		}

		function test_input($data) 
		{
			$data = trim($data);
			$data = stripslashes($data);
			$data = htmlspecialchars($data);
			return $data;
		}

	?>
	<div id="wrapper" style="width: 500px;">
		<center>
			<h1>Add new company</h1>
			<p style="font-size: 0.4cm;"><span class=<?php echo $error_class;?>><?php echo $error_message;?></span></p>
			<form class="form" action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method = "post">	
				<p>	<input type="number" placeholder="Company ID" min="0" max="99999" maxlength="5" name="c_id" value="<?php echo $c_id;?>" required/>	</p>
				<p>	<input type="text" placeholder="Company Name" maxlength="30" name="c_name" value="<?php echo $c_name;?>" required/>	</p>
				<p>	<input type="text" placeholder="Field" maxlength="30" name = "field" value="<?php echo $field;?>" required/>	</p>
				<p>	<input type="text" placeholder="URL" name="c_url" maxlength="100" value="<?php echo $c_url;?>"/>	</p>
				<p>	<input type="password" placeholder="Password (case-sensitive)" maxlength="30" name="c_pass1" required/>	</p>
				<p>	<input type="password" placeholder="Re-enter password" maxlength="30" name="c_pass2" required/>	</p>
				<p>	<button name ="create" >CREATE</button>	</p>	
			</form>
		</center>
	</div> 
</body>

</html>