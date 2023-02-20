<?php
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

?>
<!doctype html>
<html>
<head>
<title>Placement History</title>
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
 <div id="wrapper">
  <h1>Placement History</h1>
  
  <table id="keywords" class="for_table" cellspacing="0" cellpadding="0">
    <thead>
      <tr>
        <th><span>Year</span></th>
        <th><span>Company</span></th>
        <th><span>Field</span></th>
        <th><span>Avg package</span></th>
        <th><span>Total Sel</span></th>
		<th><span>PPO Sel</span></th>
      </tr>
    </thead>
    <tbody>
	<?php
	$sql = "select t1.p_year,t1.c_name,t1.field,t1.avg_pkj,t1.Tot_sel,IFNULL(t2.ppo_sel,0) as ppo_sel,t1.c_url
			from (
					select c.c_name,c.c_id,IFNULL(c.c_url,'#') as C_URL ,C.field,t.avg_pkj,t.Tot_sel, floor(c.c_id/1000) as p_year
					from (
							select c_id,avg(p_offered) as avg_pkj,count(*) as Tot_sel
							from results
							group by c_id
						) t,company c
					where c.c_id=t.c_id
				) as t1 LEFT JOIN 
				(
					select c_id,count(*) as ppo_sel
					from results
					where r_date is null
					group by c_id
				) as t2
			ON t1.c_id=t2.c_id
			order by t1.p_year";
	$result = $conn->query($sql);
	// output data of each row
     ?><!-- <tr>
        <td class="lalign">popular web series</td>
        <td>8,700</td>
        <td>350</td>
        <td>4%</td>
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
      </tr>-->
	  <?php
	while($row = $result->fetch_assoc()) 
	{
	?>
		<tr>
			<td><?php echo $row['p_year'];?></td>
			<td class="lalign"><a href="<?php echo 'http://www.'.$row['C_URL'];?>" target="balnk" ><?php echo $row['c_name'];?></a></td>
			<td class="lalign"><?php echo $row['field'];?></td>
			<td><?php echo $row['avg_pkj'];?></td>
			<td><?php echo $row['Tot_sel'];?></td>
			<td><?php echo $row['ppo_sel'];?></td>
		</tr>
	<?php }
     
?>
    </tbody>
  </table>
 </div> 
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