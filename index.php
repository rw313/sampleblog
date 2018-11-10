<?php 
session_start(); 

define ('ROOT_PATH', realpath(dirname(__FILE__)));
define('BASE_URL', 'http://rachelwu.org/blog/');

//connects to db; has 2 simple tables; posts and comments are pretty the same, with comments being by just post it
//create table {.... foreign key post_id ... on delete cascade ... } 

require('dbconn.php');
if (!$conn) { 	die("Error connecting to database: " . mysqli_connect_error()); } 

// These functions could be put into separate files, so that Ajax calls can be made without refreshing the entire page. 
if (isset($_POST['editid'])) { 
    $title = $_POST['newtitle'];
    $body = $_POST['newbody'];
    $postid = $_POST['editid'];
    $updateSql = "update `posts` SET title = '$title', body = '$body' WHERE id=$postid";
    $result = mysqli_query($conn, $updateSql);
}
else if (isset($_POST['deleteid'])) {  
    $postid = $_POST['deleteid'];
    $updateSql = "delete from `posts` WHERE id=$postid";
    $result = mysqli_query($conn, $updateSql);
}
else if (isset($_POST['addtitle'])) {  
    $title = $_POST['addtitle'];
    $body = $_POST['addbody'];
    $insertSql = "INSERT INTO `posts` (`user_id`, `title`, `views`, `body`) VALUES (1, '$title', 0, '$body')";
    $result = mysqli_query($conn, $insertSql);
}
else if (isset($_POST['caddtitle'])) {  
    $title = $_POST['caddtitle'];
    $body = $_POST['caddbody'];
    $postid = $_POST['postid'];
    $insertSql = "INSERT INTO `comments` (`user_id`,`post_id`, `title`,`body`) VALUES (1, $postid, '$title', '$body')";
    $result = mysqli_query($conn, $insertSql);
}
else if (isset($_POST['ceditid'])) { 
    $title = $_POST['cnewtitle'];
    $body = $_POST['cnewbody'];
    $commentid = $_POST['ceditid'];
    $updateSql = "update `comments` SET title = '$title', body = '$body' WHERE comment_id=$commentid";
    $result = mysqli_query($conn, $updateSql);
}
else if (isset($_POST['cdeleteid'])) {
    $commentid = $_POST['cdeleteid'];
    $deleteSql = "delete from `comments` WHERE comment_id=$commentid";
    $result = mysqli_query($conn, $deleteSql);
}

?>
<!DOCTYPE html>
<html>
<head>
	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Averia+Serif+Libre|Noto+Serif|Tangerine" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

	<!-- Styling for public area -->
	<style>
	    
	    /****************
        *** DEFAULTS
        *****************/
        * { margin: 0px; padding: 0px; font-size: 12px; color:#999; }
        
        html { height: 100%; box-sizing: border-box; }
        body {
          position: relative;
          margin: 0;
          padding-bottom: 6rem;
          min-height: 100%;
        }
        /* HEADINGS DEFAULT */
        h1, h2, h3, h4, h5, h6 {
            color: #444;
            font-family: helvetica;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-size: 12px;
            line-height: 1.5em; 
        }
        a { text-decoration: none; font-size: 11px; color: #2796b3;}
        ul, ol { margin-left: 40px; }
        hr { margin: 10px 0px; opacity: .25; }
        p {
            margin-top: 0;
            font-size: 12px;
            font-family: helvetica;
            font-weight: lighter;
            line-height: 1.5em;
            color:#444;
        }
        /* FORM DEFAULTS */
        form h2 {
        	margin: 25px auto;
        	text-align: center;
        	font-family: 'Averia Serif Libre', cursive;
        }
        form input, form textarea {
        	width: 100%;
        	display: block;
        	padding: 13px 13px;
        	font-size: 1em;
        	margin: 5px auto 10px;
        	border-radius: 0px;
        	box-sizing : border-box;
        	background: transparent;
        	border: 1px solid #3E606F;
        } 
        form input:focus, form textarea:focus {
        	outline: none;
        }
        /* BUTTON DEFAULT */
        .btn {
        	color: #2796b3;
            background: none;
            text-align: center;
            border: 1px solid #2796b3;
            border-radius: 0;
            display: block;
            letter-spacing: .1em;
            margin: 10px 0px;
            padding: 13px 20px;
            text-decoration: none;
        }
        .btn:hover {
            color: #fff;
            background: #2796b3;
        }
        /* NAVBAR */
        .navbar {
        	background-color: #3E606F;
        	height: 3px;
        	padding: 0px;
        }
        
        /* FOOTER */
        .footer {
          position: absolute;
          right: 0;
          bottom: 0;
          left: 0;
          color: white;
          background-color: #f5f5f5;
          text-align: center;
          margin: 20px auto 0px;
        }
        
        .comment {
            background: #f5f5f5f5;
            border-radius: 3px;
            padding: 10px;
            padding-bottom: 5px;
            font-size: 11px;
            margin-bottom: 4px;
        }
        .comment .modifiers {
            float: right;
        }
        .post-modifiers {
            margin-bottom: 10px;
        }
        .post-body {
            margin-top: 20px;
        }
        .post-wrapper {
            margin-bottom: 70px;
        }
	    
	</style>
	<meta charset="UTF-8">
	<title>LifeBlog | Home </title>
</head>
<body> 
	<div class="container-fluid">
		<div class="row navbar"></div>
		
		<!-- Page content -->
		<div class="container">
		    <div class="row" style="padding-top:50px;">
    		    <div class="col-sm-8">
    		        <h2 class="content-title" style="color: #2796b3">Recent Articles</h2>
        			<hr>
        			<?php 
                	$getPostsSql = "SELECT * FROM posts";
                	$result = mysqli_query($conn, $getPostsSql);  
                    while($row = mysqli_fetch_assoc($result)) {  ?>
                    
                        <div id="post<?php echo $row["id"];?>" class="post-wrapper">
                            <h4 class='title' id='title<?php echo $row["id"];?>'><?php echo $row["title"] ?> </h4>
                            <span class="post-modifiers">
                                <span class="date"><?php echo $row["created_at"]?></span>
                                &nbsp; | &nbsp; 
                                <a href="#" class="edit" onclick="editPost(<?php echo $row["id"]?>);return false;">Edit</a>
                                &nbsp; | &nbsp; 
                                <a href="#" class="delete" onclick="deletePost(<?php echo $row["id"]?>);return false;">Delete</a>
                            </span>
                            
                            
                            <p class='post-body' id='body<?php echo $row["id"];?>'><?php echo $row["body"] ?></p>
                            <div class="row">
                                <div class="col-sm-1"></div>
                                <div class="col-sm-10">
                                    <div class="comments">
                                        <p>Comments &nbsp; &nbsp; <span class="add"><a href="#" class="add" onclick="addComment(<?php echo $row["id"]?>);return false;">Add A Comment</a></span></p>
                                        
                                        <div id="comments-for-post-<?php echo $row["id"]?>">
                                            <?php 
                                            $getCommentsSql = "SELECT * FROM comments where post_id=".$row['id'].";";
                        	                $cresult = mysqli_query($conn, $getCommentsSql);   
                                            while($crow = mysqli_fetch_assoc($cresult)) {  ?>
                                                <div class="comment" id="comment<?php echo $crow['comment_id'];?>">
                                                    <span class="modifiers">
                                                        <span class="date"><?php echo $crow["created_at"]?></span>
                                                        &nbsp; | &nbsp; 
                                                        <a href="#" class="edit" onclick="editComment(<?php echo $crow["comment_id"]?>);return false;"> Edit</a>
                                                        &nbsp; | &nbsp; 
                                                        <a href="#" class="delete" onclick="deleteComment(<?php echo $crow["comment_id"]?>);return false;">Delete</a>
                                                    </span>
                                                    <h6 class='title' id='ctitle<?php echo $crow["comment_id"];?>'><?php echo $crow["title"] ?> </h6>
                                                    <p class='comment-body' id='cbody<?php echo $crow["comment_id"];?>'><?php echo $crow["body"] ?></p>
                                                    
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                    <?php  } ?> 
    		    </div>
    		    
    		    <div class="col-sm-4">
    		        <h2 class="content-title">Create a Post</h2>
    		        <form action="" method="POST" class="form" id="add-form" >
        			    <input id="addtitle" name="addtitle" placeholder="Title">
        			    <textarea style='height:200px;width:100%;' id='addbody' name='addbody' type='text'></textarea>
        			    <input class="btn-primary btn" type='submit' value="Create Post">
        			</form> 
    		    </div>
			</div>
		</div>
		<!-- // Page content -->

		<!-- footer -->
		<div class="footer"> 
		    <p>&nbsp;</p>
		</div>
		<!-- // footer -->

	</div>
	<!-- // container -->
	
	
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    
    <script>
        //Some flaws: Inserting html from javascript isn't optimal and could instead rely on a css visibility toggle. 
        //There is no form validation or security checks. 
        //Editing, deleting, and adding are similar functions for posts and comments. Therefore the methods could be combined by using boolean flags. 
        //Quotes should be more consistent (single vs double quotes). 
        //These functions would be less verbose if using jquery instead of plain javascript. 
        //Items get duplicated if buttons are clicked twice, such as Add Comment
        
        function editPost(postid) {
            var title = document.getElementById("title"+postid).innerText;
            var body = document.getElementById("body"+postid).innerText;
            var post = document.getElementById("post"+postid);
            var edithtml = "<form action='' method='POST' id='edit-form' class='form'>";
            edithtml += "<input id='editid' name='editid' type='hidden' value='"+postid+"'>";
            edithtml += "<input id='newtitle' name='newtitle' type='text' value='"+title+"'>";
            edithtml += "<textarea style='height:200px;width:100%;' id='newbody' name='newbody' type='text'>"+body+"</textarea>";
            edithtml += "<input class='btn-success btn' type='submit' value='Edit'></form>";
            post.insertAdjacentHTML('beforebegin', edithtml);
        }
        
        function deletePost(postid) {
            var post = document.getElementById("post"+postid);
            var deletehtml = "<form action='' method='POST' id='delete-form' class='form'>";
            deletehtml += "<input id='deleteid' name='deleteid' type='hidden' value='"+postid+"'>";
            deletehtml += "<p>Are you sure?</p><input class='btn-success btn' type='submit' value='Yes'></form>";
            post.insertAdjacentHTML('beforebegin', deletehtml);
        }
        
        function addComment(postid) {
            var postcomments = document.getElementById("comments-for-post-"+postid);
            var addhtml = '<form action="" method="POST" class="form" id="add-form" style="">';
            addhtml += "<input type='hidden' value='"+postid+"' id='postid' name='postid'>";
			addhtml += '<input id="caddtitle" name="caddtitle" placeholder="Title">'
			addhtml += "<textarea style='height:50px;width:100%;' id='caddbody' name='caddbody' type='text'></textarea>";
			addhtml += '<input class="btn-primary btn" type="submit" value="Create Comment">';
			addhtml += "</form>";
            postcomments.insertAdjacentHTML('beforebegin', addhtml);
        }
        
        function editComment(commentid) { 
            var title = document.getElementById("ctitle"+commentid).innerText;
            var body = document.getElementById("cbody"+commentid).innerText;
            var comment = document.getElementById("comment"+commentid);
            var edithtml = "<form action='' method='POST' id='edit-form' class='form'>";
            edithtml += "<input id='ceditid' name='ceditid' type='hidden' value='"+commentid+"'>";
            edithtml += "<input id='cnewtitle' name='cnewtitle' type='text' value='"+title+"'>";
            edithtml += "<textarea style='height:50px;width:100%;' id='cnewbody' name='cnewbody' type='text'>"+body+"</textarea>";
            edithtml += "<input class='btn-success btn' type='submit' value='Edit'></form>";
            comment.insertAdjacentHTML('beforeend', edithtml);
        }
        
        function deleteComment(commentid) { 
            var comment = document.getElementById("comment"+commentid);
            var deletehtml = "<form action='' method='POST' id='delete-form' class='form'>";
            deletehtml += "<input id='cdeleteid' name='cdeleteid' type='hidden' value='"+commentid+"'>";
            deletehtml += "<p>Are you sure?</p><input class='btn-success btn' type='submit' value='Yes'></form>";
            comment.insertAdjacentHTML('beforeend', deletehtml);
        }
    </script>

</body>
</html>
