<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbms_project";
$conn =new mysqli($servername, $username, $password, $dbname);
if( $conn->connect_error ) {
	session_unset(); 
	session_destroy();
	die("Connection failed: " . $conn->connect_error);
}
$cgpa=$c_name=$field=$c_date=$venue=$min_cgpa=$c_url=$roll=$a_year="";//declare variables
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
					//get roll number
					$sql = "SELECT s_name,s_id,cgpa,p_year  FROM student where e_id = ?";
					$stmt = $conn->prepare($sql);
					$stmt->bind_param("s",$_SESSION['username']);
					$stmt->execute();
					$stmt->bind_result($login_name,$roll,$cgpa,$a_year);
					$stmt->fetch();
					$stmt->close();
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
}
else
{
	header('Location: logout.php');
}
//here it ends
$acpt = "";
$sql = "SELECT count(*)  FROM results where s_id = ? and offer_acc = 1";
$stmt= $conn->prepare($sql);
$stmt->bind_param("s",$roll);
$stmt->execute();
$stmt->bind_result($acpt);
$stmt->fetch();
$stmt->close();
if($acpt==0)
	{
//to get the cgpa of the student
	//register here
		if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reg_save']))
		{
			if(!empty($_POST['reg']))
			{
				$check = $_POST['reg'];
				$size = count($check);
				$query = 'insert into schedule values(?,?,?)';
				$i=0;
				for($i=0; $i < $size; $i++)
				{
					$val = $check[$i];
					$pc_id = substr($val,0,5);
					$pc_date = substr($val,6,-1);
					//echo "c_id= ".$pc_id."   date= ".$pc_date;
					$stmt = $conn->prepare($query);
					$stmt->bind_param('sis',$roll,$pc_id,$pc_date);
					if(!$stmt->execute())
					{
//						echo $roll.$pc_id.$pc_date;
						echo "<script type='text/javascript'> alert('Multiple registrations for one company not allowed') </script>";
					}
					$stmt->close();
				}
			}
		}
	//registration ends here
	}
?> 
<!doctype html>
<html>
	<head>
		<title>Register</title>
		<link rel="stylesheet" href="../CSS/history.css">
		<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.1.1.min.js"></script>
		<script>
			window.console = window.console || function(t) {};
		</script>
		<script>
		  if (document.location.search.match(/type=embed/gi)) {
			window.parent.postMessage("resize", "*");
		  }
		</script>
	</head>

	<body>
		<header>
			<ul>
			  <li><a  href="student_o.php">Offers</a></li>
			  <li><a href="student_r.php">Registered</a></li>
			  <li><a class ="active" href="#">New Registration</a></li>
			  <li><a href="history.php" target="blank">History</a></li>
			  <li style="float:right"><a href="logout.php">Logout</a></li>
			<li style="float:right" title="Edit Details"><a href="change_details.php" target="blank">Welcome <?php echo $login_name;?></a></li>
			</ul>
		</header>
		</br>
		<?php 
		if($acpt==0)
		{
		?>
		<div id="wrapper">
				<?php
				$sql = "select count(*) from comp_reg where min_cgpa <=? and floor(c_id/1000)=? and c_date>=date(sysdate()) and c_id not in(select c_id from schedule where s_id=?)";
				$stmt = $conn->prepare($sql);
				$stmt->bind_param('dis',$cgpa,$a_year,$roll);
				$stmt->execute();
				$stmt->bind_result($c2);
				$stmt->fetch();
				$stmt->close();
				if($c2>0)
				{
				?>
				<h1>Available Companies</h1>
				 <form action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method = "post" class='form'>
					<table id="keywords" class="for_table" cellspacing="0" cellpadding="0">
						<thead>
							 <tr>
								<th><span>Select</span></th>
								<th><span>Company Name</span></th>
								<th><span>Field</span></th>
								<th><span>Min CGPA</span></th>
								<th><span>Date</span></th>
								<th><span>Venue</span></th>
							 </tr>
						</thead>
						<tbody>
							<?php
								$sql = "select c.c_id,c.c_name,c.field,t.c_date,t.venue,t.min_cgpa,c.c_url
								from (select c_id, c_date, venue, min_cgpa from comp_reg where min_cgpa <=? and floor(c_id/1000)=? and c_date>=date(sysdate()) and c_id not in(select c_id from schedule where s_id=?))as t, company as c
								where c.c_id = t.c_id";
								$stmt = $conn->prepare($sql);
								$stmt->bind_param('dis',$cgpa,$a_year,$roll);
								$stmt->execute();
								$stmt->bind_result($c_id,$c_name,$field,$c_date,$venue,$min_cgpa,$c_url);
								
								
								
							?> 
<!--							<tr>
								<td><input type = "checkbox" name="reg[]" value = "asd"/></td>
								<td class="lalign">popular web series</td>
								<td class="lalign">popular web series</td>
								<td>8,700</td>
								<td>350</td>
								<td>350</td>
							</tr>
							<tr>
								<td><input type = "checkbox" name="reg[]" value = "sdf"/></td>
								<td class="lalign">popular web series</td>
								<td class="lalign">2013 webapps</td>
								<td>9,900</td>
								<td>460</td>
								<td>350</td>
							</tr>
-->							<?php
							
							while($stmt->fetch()) 
							{
							?>
								<tr>
									<td><input type = "checkbox" name="reg[]" value = "<?php echo $c_id."_".$c_date."_"; ?>"/></td>
									<td class="lalign"><a href="<?php echo 'http://www.'.$c_url;?>" target='blank'><?php echo $c_name;?></a></td>
									<td><?php echo $field;?></td>
									<td><?php echo $min_cgpa;?></td>
									<td><?php echo $c_date;?></td>
									<td><?php echo $venue;?></td>
								</tr>
							<?php 
							}
							$stmt->close();
							$conn->close();
							?>
						</tbody>
					</table>
					<center><button name ="reg_save"> Register </button></center>
				</form>
			<?php
			}
			else
			{?>
				<h1>No Companies right now for you. Check back later.</h1>
			<?php
			}
			?>
		</div> 
		<?php
		}
		else
		{
//			header('Location: student_o.php');
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
				<h1><a href="<?php echo 'http://www.'.$c_url;?>" target='blank'>Company website</a></h1>
			</div>
		<?php
		}
		?>
	</body>
	<script src="//production-assets.codepen.io/assets/common/stopExecutionOnTimeout-58d22c749295bca52f487966e382a94a495ac103faca9206cbd160bdf8aedf2a.js"></script>
	<script src='//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
	<script src='http://tablesorter.com/__jquery.tablesorter.min.js'></script>
	<script>
		  $(function () {
		$('#keywords').tablesorter();
	});
	</script>
</html>