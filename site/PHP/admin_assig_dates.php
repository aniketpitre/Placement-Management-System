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
	date_default_timezone_set('Asia/Kolkata');
	$year=date('y');
	if(date('m')>6)
	{
		$year=$year+1;
	}
	$last_year=$year-1;
	$max_date="20".$year."-06-30";
	$min_date="20".$last_year."-07-01";
	if(strtotime(date("Y-m-d")) > strtotime($min_date))
	{
		$min_date=date("Y-m-d");
	}
		
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
	<title>Select Dates</title>
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
			<li><a href="admin.php" >Add new Company</a></li>
			<li><a class="active" href="#">Assign dates to company</a></li>
			<li><a href="history.php" target="blank">History</a></li>
			<li style="float:right"><a href="logout.php">Logout</a></li>
			<li style="float:right" title="Open Database"><a href="../../phpmyadmin/db_structure.php?server=1&db=dbms_project&token=4d602d96c3bcb1d42faa6d3efafd63eb" target="blank">Logined as Administrator</a></li>
		</ul>
	</header>
	
	</br>
	<?php
		$c_id=$c_venue=$min_cg="";
		$error_message="";
		$error_class="error";
		if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['to_add_date']))
		{
			$c_id = test_input($_POST["c_id"]);
			$c_venue = test_input($_POST["c_venue"]);
			$c_date = $_POST['date_sel'];
			$min_cg = test_input($_POST["min_cg"]);
			if(!empty($_POST["c_id"]) && !empty($_POST["date_sel"]) && !empty($_POST["c_venue"]) && !empty($_POST["c_venue"]))
			{
//				echo strtotime(str_replace('-', '/', $c_date));
				$error=False;
				$error = $error || !preg_match('/^[0-9]{5}$/',$c_id);
				$error = $error || !(preg_match("/^[a-zA-Z ]*$/",$c_venue) && strlen($c_venue)<=30);
				$error = $error || !(strtotime(str_replace('-', '/', $c_date))!==FALSE);
				$error = $error || !(is_numeric($min_cg) && (preg_match('/^[0-9]+\.[0-9]{2}$/', $min_cg) || (float)$min_cg==10));//to check cg is number with 2 decimals
//				echo $c_id;
				if($error)
				{
		//			echo "<script type='text/javascript'> alert('wrong input') </script>";
					$error_message='wrong input';
				}
				else
				{
					if(strtotime($c_date) >= strtotime($min_date) && strtotime($c_date) <= strtotime($max_date))
					{
						//all conditions are satisfied
						$date = date('Y-m-d', strtotime(str_replace('-', '/', $c_date)));
						$sql = "INSERT INTO comp_reg VALUES (?,?,?,?)";
						$stmt = $conn->prepare($sql);
						if ($stmt) 
						{
							$stmt->bind_param("issd",$c_id,$c_date,$c_venue,$min_cg);
							if(!$stmt->execute())
							{	$stmt->close();
								$error_message="Can't Register this company for the selected dated";	
							}
							else
							{
								$stmt->close();
								$error_class="noerror";
								$error_message="company with ID= ".$c_id." sucessfully registered on ".$c_date;
							}
						}
					}
					else
					{
						$error_message="Select a date between valid placement season</br>start date: 20".$last_year."-07-01 end date: ".$max_date;
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

		function validateDate($date)
		{
			$d = DateTime::createFromFormat('Y-m-d', $date);
			return $d && $d->format('Y-m-d') === $date;
		}
	?>
	<div id="wrapper" style="width: 500px;">
		<center>
			<h1>Select Date For Companies</h1>
			<p style="font-size: 0.4cm;"><span class=<?php echo $error_class;?>><?php echo $error_message;?></span></p>
			<form class="form" action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method = "post">	
				<?php
					/*wrong query for now: */
/*					$sql="select c_id,c_name from company";
					$stmt = $conn->prepare($sql);
*/					
					
					
					
					
					//actual query:
					$sql = "select c_id,c_name from company where floor(c_id/1000)=?";
					$stmt = $conn->prepare($sql);
					$stmt->bind_param("i",$year);
					
					$stmt->execute();
					$stmt->bind_result($c_id,$c_name);
				?>
				<p>
					<select name="c_id" onchange="firstStep(this)" class='mystyle' required>
						<option value="">SELECT COMPANY</option>
						<?php
							while($stmt->fetch())
							{
						?>
								<option value=<?php echo $c_id;?>><?php echo $c_id." - ".$c_name;?></option>
						<?php
							}
							$stmt->close();
							$conn->close();
						?>
					</select>
				</p>
				<p> <input type="date" name="date_sel" class='mystyle' max=<?php echo $max_date; ?> min=<?php  echo $min_date; ?> required></p>
				<p>	<input type="text" placeholder="Venue" maxlength="30" name="c_venue" value="<?php echo $c_venue;?>"/ required>	</p>
				<p>	<input type="number" step='0.01' placeholder="Min C.G.P.A.(with 2 decimals e.g: 9.00)" min="0.01" max="10.00" name="min_cg" value="<?php echo $min_cg;?>"/ required>	</p>
				<p>	<button name ="to_add_date" >Add date</button>	</p>	
			</form>
		</center>
	</div> 
</body>

</html>