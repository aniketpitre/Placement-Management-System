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
	$roll = $c_name = $field = $c_url = $c_id = $c_date = $c_venue = $s_cgpa = $p_year = $c_url = "";
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
						//header('Location: student.php');
						$sql = "SELECT s_id,p_year,cgpa  FROM student where e_id = ?";
						$stmt = $conn->prepare($sql);
						$stmt->bind_param("s",$_SESSION['username']);
						$stmt->execute();
						$stmt->bind_result($roll,$p_year,$s_cgpa);
						$stmt->fetch();
						$stmt->close();

						$sql = "SELECT s_name  FROM student where e_id = ?";
						$stmt = $conn->prepare($sql);
						$stmt->bind_param("s",$_SESSION['username']);
						$stmt->execute();
						$stmt->bind_result($login_name);
						$stmt->fetch();
						$stmt->close();

					}
					else
					{
						$stmt->close();
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
?>
<!doctype html>
<html>
<head>
	<title>New Registration</title>
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
			<li><a href="student_o.php">Offers</a></li>
			<li><a href="student_r.php">Registered</a></li>
			<li><a class="active" href="#">New Registration</a></li>
			<li><a href="history.php" target="blank">History</a></li>
			<li style="float:right"><a href="logout.php">Logout</a></li>
			<li style="float:right" title="Edit Details"><a href="change_details.php" target="blank">Welcome <?php echo $login_name;?></a></li>
		</ul>
	</header>
	
	</br>
	<?php
		if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register']))
		{
/*
			if(!empty($_POST['com_id'])) 
			{
				foreach($_POST['com_id'] as $selected) 
				{
					$sql = "DELETE FROM schedule WHERE s_id=? and c_id=?";
					$stmt = $conn->prepare($sql);
					$stmt->bind_param("si",$roll,$selected);
					$stmt->execute();
					$stmt->close();
				}
			}
*/
//this method for multiple dates,here name in form is like company_id."[]" and value is date;
			$sql = "select c_id from company where floor(c_id/1000)=?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("i",$p_year);
			$stmt->execute();
			$stmt->bind_result($company_id);
			while($stmt->fetch())
			{
				if(!empty($_POST[$company_id])) 
				{
//					$checked_count = count($_POST[$company_id]);
//					echo "You have selected following ".$checked_count." option(s): <br/>";
					// Loop to store and display values of individual checked checkbox.
					foreach($_POST[$company_id] as $selected) 
					{
						$query = "insert into schedule values(?,?,?)";
						$conn1 =new mysqli($servername, $username, $password, $dbname);
						if ($conn1->connect_error) {
							session_unset(); 
							session_destroy();
							die("Connection failed: " . $conn->connect_error);
						}
						$stmt1 = $conn1->prepare($query);
						$stmt1->bind_param("sis",$roll,$company_id,$selected);
						if(!$stmt1->execute())
						{
							echo "<script type='text/javascript'> alert('Only one registration per company!!') </script>";
						}
						$stmt1->close();
						$conn1->close();
					}
//					echo "<br/><b>Note :</b> <span>Similarily, You Can Also Perform CRUD Operations using These Selected Values.</span>";
				}

			}
			$stmt->close();
		}
	?>
	<?php
		$sql = "select count(*) from results where s_id = ? and offer_acc=1";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("s",$roll);
		$stmt->execute();
		$stmt->bind_result($count);
		$stmt->fetch();
		$stmt->close();
	?>
	<?php
		if($count==1)
		{
			$sql = "select c.c_id, c.c_name, t.p_offered, c.field, c.c_url from (select c_id, p_offered from results where s_id = ? and offer_acc=1) as t,company as c where c.c_id = t.c_id";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("s",$roll);
			$stmt->execute();
			$stmt->bind_result($c_id,$c_name,$p_offered,$field,$c_url);
			$stmt->fetch();
			$stmt->close();
	?>
			<div id="wrapper">
				<h1> You have already made your choice</h1>
				<h1>Company Name: <?php echo $c_name;?></h1>
				<h1>Package : <?php echo $p_offered;?></h1>
				<h1>Field : <?php echo $field;?></h1>
				<h1><a href="<?php echo $p_offered;?>">Company website</a></h1>
			</div>
	<?php
		}
		else
		{
			$sql = "select count(*) from comp_reg where floor(c_id/1000)=? and min_cgpa=? and c_id not in(select c_id from schedule where s_id=?)";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("ids",$p_year,$s_cgpa,$roll);
			$stmt->execute();
			$stmt->bind_result($count);
			$stmt->close();
			if(!$count==0)
			{
					//no company in wich he can register
	?>
				<div id="wrapper">
					<h1>No compinies right now for you. Come back later</h1>
				</div>
	<?php

			}
			else
			{
		
		
	?>
				<div id="wrapper">
					<form action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method = "post">	
						<h1>Placement Offers</h1>
						<table id="keywords" class="for_table" cellspacing="0" cellpadding="0">
							<thead>
								<tr>
									<th><span>Select</span></th>
									<th><span>Company Name</span></th>
									<th><span>Field</span></th>
									<th><span>Min C.G.P.A</span></th>
									<th><span>Interview Date</span></th>
									<th><span>Interview Venue</span></th>
								</tr>
							</thead>
							<tbody>
								<?php
									
									$sql = "select t.c_id,c.c_name,c.field,c.c_url,t.min_cgpa,t.c_date,t.venue from ( select c_id, c_date, venue,min_cgpa from comp_reg where floor(c_id/1000)=? and min_cgpa<=? ) as t,company as c where t.c_id=c.c_id and t.c_id not in(select c_id from schedule where s_id=?)";
									$stmt = $conn->prepare($sql);
									$stmt->bind_param("ids",$p_year,$s_cgpa,$roll);
									$stmt->execute();
									$stmt->bind_result($c_id,$c_name,$field,$c_url,$min_cgpa,$c_date,$c_venue);
								?>
<!--								<tr>
									<td><input type = "checkbox" name="com_id[]" value = "<?php echo 1; ?>"/></td>
									<td class="lalign">popular web series</td>
									<td>8,700</td>
									<td>350</td>
									<td>350</td>
									<td>350</td>
								</tr>
								<tr>
									<td><input type = "checkbox" name="com_id[]" value = "<?php echo 2; ?>"/></td>
									<td class="lalign">2013 webapps</td>
									<td>9,900</td>
									<td>460</td>
									<td>350</td>
									<td>350</td>
								</tr>
-->								<?php
								while($stmt->fetch())
								{
								?>
									<tr>
										<td><input type = "checkbox" name="<?php echo $c_id.'[]'; ?>" value = "<?php echo $c_date; ?>"/></td>
										<td class="lalign"><a href="<?php echo "http://www.".$c_url;?>" target="blank"><?php echo $c_name;?></a></td>
										<td><?php echo $field;?></td>
										<td><?php echo $min_cgpa;?></td>
										<td><?php echo $c_date;?></td>
										<td><?php echo $c_venue;?></td>
									</tr>
								<?php
								}
								$stmt->close();
								$conn->close();
								?>
							</tbody>
						</table>
						<button name ="register"> REGISTER </button>
					</form>
				</div> 
		<?php
			}
		?>
	<?php
		}
	?>
</body>

<script src="//production-assets.codepen.io/assets/common/stopExecutionOnTimeout-58d22c749295bca52f487966e382a94a495ac103faca9206cbd160bdf8aedf2a.js"></script>
<script src='//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src='http://tablesorter.com/__jquery.tablesorter.min.js'></script>
<script>
	$(
		function ()
		{
			$('#keywords').tablesorter();
		}
	);
</script>
</html>