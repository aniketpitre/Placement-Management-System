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
				$conn->close();
				if($ar == 0)
				{
					header('Location: student_o.php');
				}
				else
				{
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
//make student_o.php and admin.php
?>
<!doctype html>
<html>
<head>
<title>Create</title>
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
$c_name=$roll=$email=$branch=$phone=$cg=$year=$c_pass1=$c_pass2="";
$error_message="";
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create']))
{
	$c_name = test_input($_POST["c_name"]);
	$roll = test_input($_POST["roll"]);
	$roll = strtoupper($roll);//make roll all caps
	$email = test_input($_POST["email"]);
	$email = strtolower($email);//make email lowercase
	$branch = test_input($_POST["branch"]);
	$branch = strtoupper($branch);//make branch all caps
	$phone = test_input($_POST["phone"]);
	$cg = test_input($_POST["cg"]);
	$year = test_input($_POST["year"]);
	$c_pass1 = test_input($_POST["c_pass1"]);
	$c_pass2 = test_input($_POST["c_pass2"]);
	if(!empty($_POST["c_name"]) && !empty($_POST["roll"]) && !empty($_POST["email"]) && !empty($_POST["branch"]) && !empty($_POST["phone"]) && !empty($_POST["cg"]) && !empty($_POST["year"]) && !empty($_POST["c_pass1"]) && !empty($_POST["c_pass2"]))
	{
		$error=False;
		$error = $error || !(preg_match("/^[a-zA-Z ]*$/",$c_name) && strlen($c_name)<=30);//check if name only contains letters and whitespace 
		$error = $error || !preg_match('/\d{5}/',$roll);//to check roll number of format 14cs01018
		$error = $error || !filter_var($email, FILTER_VALIDATE_EMAIL);//to check valid email or not
		$error = $error || !(preg_match("/^[a-z0-9]*@gmail.com$/",$email) && strlen($email)<=30);//to check email ends with @iitbbs.ac.in
		$error = $error || !(preg_match("/^[a-zA-Z ]*$/",$branch) && strlen($branch)<=30);//check if branch only contains letters and whitespace 
		$error = $error || !(preg_match('/^[1-9]{1}[0-9]{9}$/', $phone));//check if phone number is whole number
		$error = $error || !(is_numeric($cg) && (preg_match('/^[0-9]+\.[0-9]{2}$/', $cg) || (float)$cg==10));//to check cg is number with 2 decimals
		$error = $error || !(preg_match('/^[0-9]{2}$/', $year));//to check year has excatly 2 numbers
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
		else
		{
			//all conditions are satisfied
			$sql = "INSERT INTO LOGIN(username,password) VALUES (?,?)";
			$stmt = $conn->prepare($sql);
			if ($stmt) 
			{
				$stmt->bind_param("ss",$email,$c_pass1);
				if(!$stmt->execute())
				{	$stmt->close();
					$error_message='Email id already exists!';
					$email="";
				}
				else
				{
					$stmt->close();
					$sql = "INSERT INTO STUDENT VALUES (?,?,?,?,?,?,?)";
					$stmt = $conn->prepare($sql);
					if($stmt)
					{
						$stmt->bind_param("sssisdi",$roll,$c_name,$branch,$phone,$email,$cg,$year);
						if(!$stmt->execute())
						{
							$stmt->close();
							$error_message='Roll number already exists!';
							$query='delete from login where username =?';
							$stmt = $conn->prepare($query);
							$stmt->bind_param("s",$email);
							$stmt->execute();
							$roll="";
						}
						else
						{
							$stmt->close();
							session_unset(); 
							session_destroy();
							$conn->close();
							header('Location: index.php');
						}
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
		<!--php end here-->
		
		

<div class="login-page">
	<div class="form">
		<p><span class="error"><?php echo $error_message;?></span></p>
		<form id="reg" class="register-form" action = '<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>' method = "post">
			<input type="text" placeholder="Name" maxlength="30" name="c_name" value="<?php echo $c_name;?>" required/>
			<input type="text" placeholder="Institute Roll Number" maxlength="9" name="roll" value="<?php echo $roll;?>" required/>
			<input type="text" placeholder="email address" maxlength="30" name = "email" value="<?php echo $email;?>" required/>
			<input type="text" placeholder="Branch" name="branch" maxlength="30" value="<?php echo $branch;?>" required/>
			<input type="number" placeholder="Mobile Number" min="1000000000" max="9999999999" name="phone" value="<?php echo $phone;?>" required/>
			<input type="number" step='0.01' placeholder="C.G.P.A.(with 2 decimals e.g: 9.00)" min="0.01" max="10.00" name="cg" value="<?php echo $cg;?>" required/>
			<input type="number" placeholder="Passing year (e.g. 07)" min="00" max="99" name="year" value="<?php echo $year;?>" required/>
			<input type="password" placeholder="password (case-sensitive)" maxlength="30" name="c_pass1" required/>
			<input type="password" placeholder="Re-enter password" maxlength="30" name="c_pass2" required/>
			<button name ="create" >create</button>
			<p class="message">Already registered? <a href="index.php">Sign In</a></p>
		</form>
   
	</div>
</div>
</body>
</html>