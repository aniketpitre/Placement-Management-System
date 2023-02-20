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
	$s_id=$s_name=$branch= $pno= $e_id= $p_offered=$offer_acc="";
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
					//he can stay in page
					$this_c_id=$_SESSION['username'];
					$sql = "SELECT c_name  FROM company where c_id = ?";
					$stmt = $conn->prepare($sql);
					$stmt->bind_param("i",$_SESSION['username']);
					$stmt->execute();
					$stmt->bind_result($this_c_name);
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
//INSERT INTO `company` (`C_ID`, `C_NAME`, `FIELD`, `C_URL`) VALUES ('3', 'xyz', 'abc', 'xyz.com');	
$error_msg="";
$color = "error";
?>
<!doctype html>
<html>
<head>
	<title>Offer-Placement</title>
	<link rel="stylesheet" href="../css/history.css">

	<style>
	.error {color: #FF0000;}
	</style>
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
			<li><a href="company.php" >Upcoming Interviews</a></li>
			<li><a class="active" href="#">Offer Placement</a></li>
			<li><a href="history.php" target="blank">History</a></li>
			<li style="float:right"><a href="logout.php">Logout</a></li>
			<li style="float:right" title="<?php echo $this_c_id;?>"><a href="#">Welcome <?php echo $this_c_name;?></a></li>
		</ul>
	</header>
	
	</br>
	<?php
	if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm']))
	{
		if($_POST['confirm']==1)
		{
			if(!empty($_POST['c_id'])&&!empty($_POST['s_id'])&&!empty($_POST['pk_o']) && $_POST['c_id']==$_SESSION['username'])
			{
				if($this_c_id==$_POST['c_id'])
				{
					$s_id = $_POST['s_id'];
					$c_id = $_POST['c_id'];
					$pk_o = $_POST['pk_o'];
					$sql = 'insert into results(s_id,c_id,p_offered,r_date) values (?,?,?,date(sysdate()))';
					$stmt = $conn->prepare($sql);
					$stmt->bind_param('sii',$s_id,$c_id,$pk_o);
					if($stmt->execute())
					{
						$error_msg='Successfully offered package to the student';
						$color="";
					}
					else
					{
						$error_msg="Can't offer that student now";
					}
				}
				else
				{
					$error_msg="Company id didn't match Retry";
				}
			}
			else
			{
				$error_msg='Something went wrong. Retry!!';
			}
		}
		$_POST['conform']="";
	}
	
	if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel']))
	{
		if($_POST['cancel']==1)
		{
			$error_msg = "Aborted offer";
		}
		$_POST['cancel']="";
	}
	?>
	<div id="wrapper">
		<center>
			<h1>Available Students</h1>
			<p style="font-size: 0.5cm;"><span class="<?php echo $color;?>"><?php echo $error_msg;?></span></p>
			<form class="form" action = "check.php" method = "post">
				<?php
				
					$sql="select s.s_id, s.s_name from(select s_id,c_id from schedule where c_id = ? and s_date<=date(sysdate())) as t , student as s where s.s_id = t.s_id and t.s_id not in (select s_id from results where c_id=?) ";
					$stmt = $conn->prepare($sql);
					$stmt->bind_param('ii',$_SESSION['username'],$_SESSION['username']);
					$stmt->execute();
					$stmt->bind_result($s_id,$s_name);
				?>
				<p>
					<select name="s_id" onchange="firstStep(this)" class='mystyle' required>
						<option value="">SELECT STUDENT</option>
						<?php
							while($stmt->fetch())
							{
						?>
								<option value=<?php echo $s_id;?>><?php echo $s_id." - ".$s_name;?></option>
						<?php
							}
							$stmt->close();
						?>
					</select>
				</p>
				<p>	<input type="number"  placeholder="CTC Amount" min="500000" max="99999999" name="pk_o" required/></p>
				<p>	<button name ="offer" title="You can't cancel once offered." >Offer</button>	</p>	
			</form>
		</center>
	</div> 
	</br>
	<div id="wrapper">
		<center>
			<h1>Accepted Students</h1>
			<table id="keywords" cellspacing="0" cellpadding="0">
				<thead>
				  <tr>
					<th><span>ROLL</span></th>
					<th><span>NAME</span></th>
					<th><span>BRANCH</span></th>
					<th><span>PHONE NO</span></th>
					<th><span>EMAIL-ID</span></th>
					<th><span>OFFER</span></th>
				  </tr>
				</thead>
				<tbody>
				<?php
				$sql = "select s.s_id,s.s_name,s.branch, s.pno, s.e_id, t.p_offered from (select s_id,p_offered from results where c_id = ? and offer_acc=1) as t, (select * from student where p_year = floor(?/1000)) as s where t.s_id = s.s_id";
				$stmt = $conn->prepare($sql);
				$stmt->bind_param('ii',$this_c_id,$this_c_id);
				$stmt->execute();
				$stmt->bind_result($s_id,$s_name,$branch, $pno, $e_id, $p_offered);
				// output data of each row
				 ?>
				  <?php
				while($stmt->fetch()) 
							{
							?>
								<tr>
									<td><?php echo $s_id;?></td>
									<td class="lalign"><?php echo $s_name;?></td>
									<td><?php echo $branch;?></td>
									<td><?php echo $pno;?></td>
									<td><?php echo $e_id;?></td>
									<td><?php echo $p_offered;?></td>
								</tr>
							<?php 
							}
							$stmt->close();
							?>
				</tbody>
			 </table>
		</center>
	</div>
</br>
<div id="wrapper" >
	<center>
			<h1>Offers in Progress</h1>
			<table id="keywords1" cellspacing="0" cellpadding="0">
				<thead>
				  <tr>
					<th><span>ROLL</span></th>
					<th><span>NAME</span></th>
					<th><span>BRANCH</span></th>
					<th><span>PHONE NO</span></th>
					<th><span>EMAIL-ID</span></th>
					<th><span>OFFER</span></th>
				  </tr>
				</thead>
				<tbody>
				<?php
				$sql = "select s.s_id,s.s_name,s.branch, s.pno, s.e_id, t.p_offered from (select s_id,p_offered from results where c_id = ? and offer_acc=0) as t, (select * from student where p_year = floor(?/1000)) as s where t.s_id = s.s_id";
				$stmt = $conn->prepare($sql);
				$stmt->bind_param('ii',$this_c_id,$this_c_id);
				$stmt->execute();
				$stmt->bind_result($s_id,$s_name,$branch, $pno, $e_id, $p_offered);
				// output data of each row
				 ?>
				  <?php
				while($stmt->fetch()) 
							{
							?>
								<tr>
									<td><?php echo $s_id;?></td>
									<td class="lalign"><?php echo $s_name;?></td>
									<td><?php echo $branch;?></td>
									<td><?php echo $pno;?></td>
									<td><?php echo $e_id;?></td>
									<td><?php echo $p_offered;?></td>
								</tr>
							<?php 
							}
							$stmt->close();
							$conn->close();
							?>
				</tbody>
			 </table>
		</center>
</div>
	
</body>
<script src="//production-assets.codepen.io/assets/common/stopExecutionOnTimeout-58d22c749295bca52f487966e382a94a495ac103faca9206cbd160bdf8aedf2a.js"></script>

    <script src='//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src='http://tablesorter.com/__jquery.tablesorter.min.js'></script>

<script>
      $(function () {
    $('#keywords').tablesorter();
});

      $(function () {
    $('#keywords1').tablesorter();
});

    </script>

</html>


