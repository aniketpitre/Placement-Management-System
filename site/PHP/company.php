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
	if(isset($_SESSION['username'])&& isset($_SESSION['password']))
	{
		$c_name=$c_id=$s_id=$s_name=$s_branch=$s_pno=$s_eid=$s_cgpa=$s_date=$s_venue="";
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
					$c_id=$_SESSION['username'];
					$sql = "SELECT c_name  FROM company where c_id = ?";
					$stmt = $conn->prepare($sql);
					$stmt->bind_param("i",$_SESSION['username']);
					$stmt->execute();
					$stmt->bind_result($c_name);
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
	<title>Upcoming Interviews</title>
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
			<li><a class="active" href="#">Upcoming Interviews</a></li>
			<li><a href="comp_select.php">Offer Placement</a></li>
			<li><a href="history.php" target="blank">History</a></li>
			<li style="float:right"><a href="logout.php">Logout</a></li>
			<li style="float:right" title="<?php echo $c_id;?>"><a href="#">Welcome <?php echo $c_name;?></a></li>
		</ul>
	</header>
	
	</br>
	<div id="wrapper" style="width: 1000px;">
		<h1>Registered Students for Upcoming Interviews</h1>
	
		<table id="keywords" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th><span>Student ID</span></th>
					<th><span>Name</span></th>
					<th><span>Branch</span></th>
					<th><span>Phone No</span></th>
					<th><span>Email-id</span></th>
					<th><span>CGPA</span></th>
					<th><span>Date</span></th>
					<th><span>Venu</span></th>
				</tr>
			</thead>
			<tbody>
				<?php
					$sql = "SELECT S.S_ID,S.S_NAME,S.Branch,S.PNO,S.E_ID,S.CGPA,T3.C_DATE,T3.VENUE
							FROM (
									SELECT T1.S_ID,T2.C_DATE,T2.VENUE
									FROM (
											SELECT S_ID,S_DATE
											FROM schedule
											WHERE C_ID=? and s_id not in(select s_id from results where c_id=?) and s_date>=date(sysdate())
										) AS T1,
										(
											SELECT C_DATE,VENUE
											FROM comp_reg 
											WHERE C_ID=?
										) AS T2
									WHERE T1.S_DATE=T2.C_DATE
								) AS T3,STUDENT AS S
							WHERE T3.S_ID=S.S_ID";
					$stmt = $conn->prepare($sql);
					$stmt->bind_param("iii",$c_id,$c_id,$c_id);
					$stmt->execute();
					$stmt->bind_result($s_id,$s_name,$s_branch,$s_pno,$s_eid,$s_cgpa,$s_date,$s_venue);
					// output data of each row
				?>
<!--				<tr>
					<td class="lalign">popular web series</td>
					<td>8,700</td>
					<td>350</td>
					<td>4%</td>
					<td>7.0</td>
					<td>7.0</td>
					<td>7.0</td>
					<td>7.0</td>
				</tr>
				<tr>
					<td class="lalign">2013 webapps</td>
					<td>9,900</td>
					<td>460</td>
					<td>4.6%</td>
					<td>11.5</td>
					<td>7.0</td>
					<td>7.0</td>
					<td>7.0</td>
				</tr>
-->				<?php
					while($stmt->fetch()) 
					{
						//echo $s_id.$s_name.$s_branch.$s_pno.$s_eid.$s_cgpa.$s_date.$s_venue;
				?>
						<tr>
							<td><?php echo $s_id;?></td>
							<td><?php echo $s_name;?></td>
							<td><?php echo $s_branch;?></td>
							<td><?php echo $s_pno?></td>
							<td><?php echo $s_eid;?></td>
							<td><?php echo $s_cgpa;?></td>
							<td><?php echo $s_date?></td>
							<td><?php echo $s_venue?></td>
						</tr>
				<?php
					}
				?>
			</tbody>
		</table>
	</div> 
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