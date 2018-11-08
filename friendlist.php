<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="description" content="Web application development" />
    <meta name="keywords" content="Assignment2" />
    <meta name="author" content="Dai Trung Duong Luong" />
    <title>Assignment 2</title>
    <link href="library/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="library/bootstrap/css/bootstrap-grid.min.css" rel="stylesheet" />
    <link href="css/custom.css" rel="stylesheet" />
</head>
<body>
    <div class="container">
        <div class="bg-white text-dark rounded mt-5 p-3">
            <div class="row">
                <div class="col-12 text-center">
                    <h1>My Friend System - Friend Add Page</h1>
                </div>
            </div>

            <?php
            /**
             * Created by IntelliJ IDEA.
             * User: JoelLuong
             * Date: 7/10/2018
             * Time: 7:05 PM
             */
            require("Database.php");

            session_start(); // start the session
            if (!isset($_SESSION["isLogInSuccessful"])){ // check if session variable exists
                $_SESSION["isLogInSuccessful"] = false; // create the session variable with default value
            }
			$isLogInSuccessful = $_SESSION["isLogInSuccessful"];
			
            if ($isLogInSuccessful){
				$email = $_SESSION["email"]; // get the session variable
                $pdo = new Database(); // call php data object
                $friendTableName = "friends";
                $myfriendsTableName = "myfriends";

                // delete friends, if user want to delete
                if (isset($_POST['delete'])){
                    $friend_id = $_POST['friend_id'];   // retrieve my id
                    $delete_friend_id = $_POST['delete_friend_id']; // retrieve my friend's id
                    $num_of_friends_update = $_POST['update_friend_number']-1;  // retrieve the number of friends and minus 1

                    // delete friend
                    $queryDeleteFriend = "DELETE FROM myfriends WHERE friend_id2= :friend_id2 AND friend_id1=:friend_id1;";
                    $pdo->query($queryDeleteFriend);
                    $pdo->bind(':friend_id2', $delete_friend_id);
                    $pdo->bind(':friend_id1', $friend_id);
                    $pdo->execute();

                    // update number of friends after delete
                    $queryUpdateFriendsNum = "UPDATE friends SET num_of_friends = '$num_of_friends_update' WHERE friend_id = :friend_id";
                    $pdo->query($queryUpdateFriendsNum);
                    $pdo->bind(':friend_id', $friend_id);
                    $pdo->execute();
                }

                // check if two table exist
                if ($pdo->tableExists($friendTableName) && $pdo->tableExists($myfriendsTableName)){
                    // find yourself based on your email
                    $queryFriendsTableSelect = "SELECT friend_id, profile_name, num_of_friends FROM friends WHERE friend_email= :friend_email";
                    $pdo->query($queryFriendsTableSelect);
                    $pdo->bind(':friend_email', $email);
                    $rows = $pdo->resultset();

                    if (count($rows) != 0){ // check if input email already existed in the table or not (should be equal to 1)
                        $id = $rows[0]['friend_id'];
                        $name = $rows[0]['profile_name'];
                        $num_of_friends = $rows[0]['num_of_friends'];
                        $queryMyFriendTableSelect = "SELECT person.friend_id as my_id, person.profile_name as my_name, personf.friend_id as my_friend_id, personf.profile_name as my_friend_name
                                          FROM friends person
                                        JOIN myfriends friend
                                        ON person.friend_id = :friend_id AND friend.friend_id1=:friend_id
                                        JOIN friends personf
                                        ON personf.friend_id=friend.friend_id2";
                        $pdo->query($queryMyFriendTableSelect);
                        $pdo->bind(':friend_id', $id);
                        $myFriendRows =  $pdo->resultset();
                    } else {
                        echo "<div class='row'><div class='col-12 text-danger text-center'><p>Email $email is not existed!!!</p></div></div>";
                    }
                } else {
                    echo "<div class='row'><div class='col-12 text-danger text-center'><p>Friends Table is not exist!!!</p></div></div>";
                }

            }

            ?>

			<?php if ($isLogInSuccessful) { ?>
                <div class="row">
                    <div class="col-12 text-center">
                        <h3><?php echo $name ?>'s Friend List Page</h3>
                        <h3>Total number of friends is <?php echo $num_of_friends ?></h3>
                    </div>
                </div>

                <div class="table-responsive-sm">
                    <table class="table table-bordered">
                        <tbody>
                        <?php foreach($myFriendRows as $myFriendRow) : ?>
                            <tr>
                                <td class="text-center"><?php echo $myFriendRow['my_friend_name']?></td>
                                <td class="text-center">
                                    <form method="post" action="<?php echo $_SERVER["PHP_SELF"];?>">
                                        <input type="hidden" name="friend_id" value="<?php echo $myFriendRow['my_id'];?>">
                                        <input type="hidden" name="delete_friend_id" value="<?php echo $myFriendRow['my_friend_id'];?>">
                                        <input type="hidden" name="update_friend_number" value="<?php echo $num_of_friends;?>">
                                        <input type="submit" name="delete" value="Unfriend">
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-6 text-right">
                        <a href="friendadd.php">Add friends</a>
                    </div>
                    <div class="col-6">
                        <a href="logout.php">Log out</a>
                    </div>
                </div>
            <?php } else { ?>
                <div class='row'><div class='col-12 text-danger text-center'><p> MESSAGE: Please Log In or Sign Up to access My Friend System</p></div></div>
                <div class="row">
                    <div class="col-4 text-right">
                        <a href="signup.php">Sign Up</a>
                    </div>
                    <div class="col-4 text-center">
                        <a href="login.php">Log In</a>
                    </div>

                    <div class="col-4 text-left">
                        <a href="index.php">Home</a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>