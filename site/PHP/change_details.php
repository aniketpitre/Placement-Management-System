<?php
	echo "<script type='text/javascript'> alert('Note: If you change you PASSING YEAR all your Registrations and Offers will be reset') </script>";
$c_name=$roll=$email=$branch=$old_phone=$old_cg=$old_year=$old_c_pass="";
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
	$roll = $c_name = $p_offered = $field = $c_url =$c_id="";

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
						$sql = "SELECT s_id,s_name,branch,pno,e_id,cgpa,p_year  FROM student where e_id = ?";
						$stmt = $conn->prepare($sql);
						$stmt->bind_param("s",$_SESSION['username']);
						$stmt->execute();
						$stmt->bind_result($roll,$c_name,$branch,$old_phone,$email,$old_cg,$old_year);
						$old_c_pass=$_SESSION['password'];
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
				$stmt->close();
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
<title>Edit Info</title>
<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.1.1.min.js"></script>
<link rel="stylesheet" href="../CSS/login.css">
<style>
.error {color: #FF0000;}
</style>

</head>
<body>
		<!--php in here-->
<?php
// define variables and set to empty values
$phone=$old_phone;
$cg=$old_cg;
$year=$old_year;
$c_pass_check="";
$c_pass1=$c_pass2="";
$error_message="";
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change']))
{
	$phone = test_input($_POST["phone"]);
	$cg = test_input($_POST["cg"]);
	$year = test_input($_POST["year"]);
	$c_pass_check = test_input($_POST["c_pass_check"]);
	$c_pass1 = test_input($_POST["c_pass1"]);
	$c_pass2 = test_input($_POST["c_pass2"]);
	if(!empty($_POST["phone"]) && !empty($_POST["cg"]) && !empty($_POST["year"]) && !empty($_POST["c_pass_check"]) && !empty($_POST["c_pass1"]) && !empty($_POST["c_pass2"]))
	{
		$error=False;
		$error = $error || !(preg_match('/^[1-9]{1}[0-9]{9}$/', $phone));//check if phone number is whole number
		$error = $error || !(is_numeric($cg) && (preg_match('/^[0-9]+\.[0-9]{2}$/', $cg) || (float)$cg==10));//to check cg is number with 2 decimals
		$error = $error || !(preg_match('/^[0-9]{2}$/', $year));//to check year has excatly 2 numbers
		$error = $error || !preg_match("/^[a-zA-Z0-9 @#_$%]*$/",$c_pass_check);
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
		else if($old_c_pass!==$c_pass_check)
		{
			$error_message='Wrong old password';
		}
		else
		{
			//all conditions are satisfied
			$sql = "UPDATE STUDENT SET PNO=?,CGPA=?,P_YEAR=? WHERE S_ID=?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("idis",$phone,$cg,$year,$roll);
			$stmt->execute();
			$stmt->close;
			
			$sql = "UPDATE LOGIN SET PASSWORD=? WHERE USERNAME=?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("ss",$c_pass1,$email);
			$stmt->execute();
			$stmt->close;
//			echo "<script type='text/javascript'> alert('Succesfully changed details') </script>";
			header('Location: logout.php');
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
		<!--php end here-->
		
		

<div class="login-page">
	<div class="form">
		<p><span class="error"><?php echo $error_message;?></span></p>
		<form id="reg" class="register-form" action = '<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>' method = "post">
			<input type="text" placeholder="Name" maxlength="30" name="c_name" value="<?php echo $c_name;?>" disabled required />
			<input type="text" placeholder="Institute Roll Number" maxlength="9" name="roll" value="<?php echo $roll;?>" disabled required/>
			<input type="text" placeholder="email address" maxlength="30" name = "email" value="<?php echo $email;?>" disabled required />
			<input type="text" placeholder="Branch" name="branch" maxlength="30" value="<?php echo $branch;?>" disabled required/>
			<input type="number" placeholder="Mobile Number" min="1000000000" max="9999999999" name="phone" value="<?php echo $phone;?>" required/>
			<input type="number" step='0.01' placeholder="C.G.P.A.(with 2 decimals e.g: 9.00)" min="0.01" max="10.00" name="cg" value="<?php echo $cg;?>" required/>
			<input type="number" placeholder="Passing year (e.g. 07)" min="00" max="99" name="year" value="<?php echo $year;?>" required/>
			<input type="password" placeholder="old password" maxlength="30" name="c_pass_check" required/>
			<input type="password" placeholder="new password (case-sensitive)" maxlength="30" name="c_pass1" required/>
			<input type="password" placeholder="Re-enter new password" maxlength="30" name="c_pass2" required/>
			<button name ="change" >change</button>
		</form>
   
	</div>
</div>
</body>
</html>