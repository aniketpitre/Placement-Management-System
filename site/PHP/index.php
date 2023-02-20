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
	else
	{
		session_unset(); 
		session_destroy();
		$stmt->close();
		$conn->close();
		header('Location: index.php');
	}
}
//make student_o.php and admin.php
?>
<!doctype html>
<html>
<head>
<title>Login</title>
<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.1.1.min.js"></script>
<link rel="stylesheet" href="../CSS/login.css">
<style>
.error {color: #FF0000;}
</style>

</head>
<body>
		<!--php in here-->
<?php
	$email=$pass="";
	$error_message="";
	//$error_message="invalid e-mail id/password";
	if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login']))
	{
		$email = test_input($_POST["email"]);
		$email = strtolower($email);//make email lower case
		$pass = test_input($_POST["pass"]);
		if(!empty($_POST['email']) && !empty($_POST['pass']))
		{
			$error=False;
			$error = $error || !(filter_var($email, FILTER_VALIDATE_EMAIL) ||((preg_match('/^[0-9]{5}$/',$email))));//to check valid email or not
			$error = $error || !((preg_match("/^[a-z0-9]*@gmail.com$/",$email) && strlen($email)<=30)||(preg_match('/^[0-9]{5}$/',$email)));//to check email ends with @iitbbs.ac.in
			$error = $error || !preg_match("/^[a-zA-Z0-9 @#_$%]*$/",$pass);
			if($error)
			{
				$error_message="invalid e-mail id/password";
			}
			else
			{
//				echo "all conditions are satisfied";
				//all conditions are satisfied;
				$sql = "SELECT count(*)  FROM login where username = ? and password = ?";
				$stmt = $conn->prepare($sql);
				if ($stmt)
				{
					$stmt->bind_param("ss",$email,$pass);
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
							//echo "<script type='text/javascript'> alert('before bind_parm') </script>";
							$stmt->bind_param("ss",$email,$pass);
							$stmt->execute();
							$stmt->bind_result($ar);
							$stmt->fetch();
							$stmt->close();
							$conn->close();
							$_SESSION['username']=$email;
							$_SESSION['password']=$pass;
							if($ar == 0)
							{
							//	echo "<script type='text/javascript'> alert('Session Expired') </script>";
								header('Location: student_o.php');
							}
							else
							{
								header('Location: admin.php');
							}
						}
						else
						{
							$error_message="error!!</br>reload page";
						}
					}
					else
					{
						$sql = "SELECT count(*)  FROM c_login where username = ? and password = ?";
						$stmt = $conn->prepare($sql);
						$stmt->bind_param("is",$email,$pass);
						$stmt->execute();
						$stmt->bind_result($count);
						$stmt->fetch();
						$stmt->close();
						if($count==1)
						{
							$_SESSION['username']=$email;
							$_SESSION['password']=$pass;
							$conn->close();
							header('Location: company.php');
						}
						else
						{
							$error_message="invalid e-mail id/password</br>Please sign-up if you don't have an account";
						}
					}
				}
				else
				{
					$error_message="error!!</br>reload page";
				}
			}
		}
		else
		{
			$error_message="enter e-mail id/password";
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
		<form id="login" class="login-form" action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method = "post">
			<input type="text" placeholder="E-mail ID or Comapany ID" name="email" value="<?php echo $email;?>" required/>
			<input type="password" placeholder="Password" name="pass" required/>
			<button name ='login'>login</button>	
			<p class="message">Not registered? <a href="create.php">Create an account</a></p>
		</form>
	</div>
</div>
</body>
</html>