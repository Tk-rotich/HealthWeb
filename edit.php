l<?php
ob_start();
session_start();
error_reporting(0);
$username=$_SESSION['username'];
$cusername=$_SESSION['cusername'];

require 'headwithsearch.php';
require 'varfilter.php';
require 'database.php';
require 'functions/sam_img_lib.php';
$c=mysql_query("select COUNT(client_name) from mydocs where doctor_name='$cusername'")or die(mysql_error());
$counter=mysql_fetch_array($c);
$count=$counter[0]+1;




$p=$_REQUEST['page'];
$caty=$_REQUEST['cat'];
$page=unhack($p);

$open=mysql_query("select * from mydocs where docs_id='$page' AND cat='$caty' ")or die(mysql_error(). "Error :p");
$edit=mysql_fetch_array($open);
$docs_id=$edit['docs_id'];
	$img=$edit['img'];
	$file_name=$edit['img_name'];
	$details=$edit['details'];
	$date=$edit['doc_date'];
	$client_phone=$edit['client_phone'];
	echo '<br/>';

	//echo '<form method="post" enctype="multipart/form-data">




?>




                 <div class="modal" id="myModal" area-hidden="true">
	<div class="modal-header">
    	<h3>Edit content <?php echo '<a style="float:right" href="index.php" />
		<img src="img/backbtn.png" title="Go Back">Go Back</a>';?>	</h3>
    </div>
    <div class="modal-body">

    	<form method="post" enctype="multipart/form-data">

        	<label>File Name</label>
           <?php echo '<input class="span4" type="text" name="fname" value="'.$file_name.'">';?><br />
        	<label>Email</label>
            <?php echo  '<input class="span4" type="text" value="'.$details.'" name="details1">';?><br />
        	<label>Date</label>
           <?php echo '<input class="span4" type="date" value="'.$date.'" name="date1">';?><br />
        	<label>Phone No</label>
           <?php echo '<input class="span4" type="text" value="'.$client_phone.'" name="client_phone1">';?><br />
                
                <?php
                $ryp=mysql_query("SELECT * 
FROM  `linked_data` 
WHERE  `user_id` =  '$username' AND  cat='$caty' ")or die(mysql_error()."img");
                while($sdd=mysql_fetch_array($ryp))
                {
                ?>
                <a href="<?php echo $sdd['location']; ?>"><img src="<?php echo $sdd['location']."_resized"; ?>" title="edit image"></a><hr>
                <input type="checkbox" name="chk" value="<?php echo $sdd[0]; ?>" class="checkbox " />

                 <input type="file"  id="myfile" name="myfile[]"  class="span3"/><hr>

				 <?php 
				 }
				 ?>
        </div>

    <div class="modal-footer">
    		 <input type="submit" name="update" value="update" class="btn btn-success">

	    </div>
</form>
</div>

<?php
	if (isset($_POST['update'])){


	if (!empty($_POST['chk'])){
	$fname=unhack($_POST['fname']);
	$details1=unhack($_POST['details1']);
	$date1=unhack($_POST['date1']);
	$client_phone1=unhack($_POST['client_phone1']);
         $chk_val=$_POST['chk'];
//remove old file
	$get=mysql_query("select location from linked_data where id='$chk_val'");
	$fimg=mysql_fetch_array($get);
	$rm=$fimg[0];
	unlink($rm);

$saved=0;
        foreach($_FILES['myfile']['name'] as $opt=>$var)
{
if(!empty($name=$_FILES['myfile']['name'][$opt]))
{
	//Properties of the uploaded file
 	$name=$_FILES['myfile']['name'][$opt];
 	$type=$_FILES['myfile']['type'][$opt];
	$size=$_FILES['myfile']['size'][$opt];
	$tmp_name=$_FILES['myfile']['tmp_name'][$opt];
	$error=$_FILES['myfile']['error'][$opt];
	$destination="mydocs/$username/$page";

	move_uploaded_file($tmp_name,"$destination/".$name);
$saved= "$destination/".$name;
//echo $saved;
mysql_query("update mydocs set img='$saved' , img_name='$fname', details='$details1' , doc_date='$date1', client_phone='$client_phone1' where docs_id='$page' ")or die(mysql_error());
mysql_query("update linked_data set location='$saved' where id='$chk_val' ");
}
}

mysql_query("update client_login set no_of_upload='$count' where username='$details1' ");


$target_file = "$saved";
$resized_file = $saved."_resized";
$wmax = 300;
$hmax = 300;
ak_img_resize($target_file, $resized_file, $wmax, $hmax, $fileExt);

$target_file = $saved."_resized";
$thumbnail = $saved."_thumb";
$wthumb = 150;
$hthumb = 150;
ak_img_thumb($target_file, $thumbnail, $wthumb, $hthumb, $fileExt);

header('location: index.php');

}
	else
	{
	$fname=$_POST['fname'];
	$details1=$_POST['details1'];
	$date1=$_POST['date1'];
		$client_phone1=unhack($_POST['client_phone1']);
	$ffname=unhack($fname);
	$ddetails1=unhack($details1);
	$ddate1=unhack($date1);


mysql_query("update mydocs set  img_name='$ffname', details='$ddetails1' , doc_date='$ddate1',client_phone='$client_phone1' where docs_id='$page' ")or die(mysql_error());
mysql_query("update client_login set no_of_upload='$count' where username='$details1' ");
header('location: index.php');
	}

}

	?>
	<!--</div>
    </div>-->

