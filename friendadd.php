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
    <style>
        a.disabled {
            /* Make the disabled links grayish*/
            color: gray;
            /* And disable the pointer events */
            pointer-events: none;
        }
    </style>
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
            require_once("settings.php");
            require("Database.php");

            session_start(); // start the session
            if (!isset($_SESSION["isLogInSuccessful"])){
                $_SESSION["isLogInSuccessful"] = false;
            }

            $email = $_SESSION["email"];
            $isLogInSuccessful = $_SESSION["isLogInSuccessful"];

            if ($isLogInSuccessful){
                $pdo = new Database();
                $friendTableName = "friends";
                $myfriendsTableName = "myfriends";

                if ($_POST['add']){
                    $friend_id = $_POST['friend_id'];
                    $add_friend_id = $_POST['add_friend_id'];
                    $num_of_friends_update = $_POST['update_friend_number']+1;
                    $queryAddFriend = "INSERT INTO myfriends (friend_id1, friend_id2) VALUE (:friend_id1,:friend_id2);";
                    $pdo->query($queryAddFriend);
                    $pdo->bind(':friend_id1', $friend_id);
                    $pdo->bind(':friend_id2', $add_friend_id);
                    $pdo->execute();

                    // update number of friends
                    $queryUpdateFriendsNum = "UPDATE friends SET num_of_friends = '$num_of_friends_update' WHERE friend_id = :friend_id";
                    $pdo->query($queryUpdateFriendsNum);
                    $pdo->bind(':friend_id', $friend_id);
                    $pdo->execute();
                }

                if ($pdo->tableExists($friendTableName) && $pdo->tableExists($myfriendsTableName)){
                    $queryFriendsTableSelect = "SELECT friend_id, profile_name, num_of_friends FROM friends WHERE friend_email=:friend_email";
                    $pdo->query($queryFriendsTableSelect);
                    $pdo->bind(':friend_email', $email);
                    $rows = $pdo->resultset();
                    if (count($rows) != 0){
                        $id = $rows[0]['friend_id'];
                        $name = $rows[0]['profile_name'];
                        $num_of_friends = $rows[0]['num_of_friends'];

                        $queryStrangerSelect = "SELECT friend_id, profile_name FROM friends
                    WHERE friend_id NOT IN (SELECT friend_id2 FROM myfriends WHERE friend_id1=:friend_id1)
                    AND friend_id <> :friend_id;";
                        $pdo->query($queryStrangerSelect);
                        $pdo->bind(':friend_id1', $id);
                        $pdo->bind(':friend_id', $id);
                        $strangerRows =  $pdo->resultset(); // the purpose is get the total number of strangers for pagination

                        $resultPerPage = 5;
                        $numberOfResult = count($strangerRows);
                        $numberOfPages = ceil($numberOfResult/$resultPerPage);
                        // determine which page number visitor is currently on
                        if (!isset($_GET['page'])){
                            $page = 1;
                        } else {
                            $page = $_GET['page'];
                        }

                        // determine  the SQL LIMIT starting number for the results on the displaying page
                        $this_page_first_result = ($page-1)*$resultPerPage;

                        // retrieve selected results from database and display them on page
                        $sqlQuery = "SELECT friend_id, profile_name FROM friends WHERE friend_id NOT IN (SELECT friend_id2 FROM myfriends WHERE friend_id1=:friend_id1) AND friend_id <> :friend_id LIMIT :this_page_first_result, :resultPerPage";
                        $pdo->query($sqlQuery);
                        $pdo->bind(':this_page_first_result', $this_page_first_result);
                        $pdo->bind(':resultPerPage', $resultPerPage);
                        $pdo->bind(':friend_id1', $id);
                        $pdo->bind(':friend_id', $id);
                        $strangerPaginationRows = $pdo->resultset();

//                        echo "First Result: ";
//                        print_r($strangerPaginationRows);
//                        echo "<br>".count($strangerPaginationRows);


                        // retrieve selected results from database and display them on page
                        $sqlQuery = "SELECT r1.friend_id1 AS user1, r2.friend_id1 as user2, f.profile_name, COUNT(r1.friend_id2) as relation_count FROM myfriends r1 INNER JOIN myfriends r2 ON r1.friend_id2 = r2.friend_id2 AND r1.friend_id1 <> r2.friend_id1 AND r1.friend_id1=:friend_id1 INNER JOIN friends f ON f.friend_id = r2.friend_id1 GROUP BY r1.friend_id1, r2.friend_id1";
                        $pdo->query($sqlQuery);
                        $pdo->bind(':friend_id1', $id);
                        $resultCountMutualRows = $pdo->resultset();
//                        echo "<br><br>Count Result: ";
//                        print_r($resultCountMutualRows);
//                        echo "<br>".count($resultCountMutualRows);
                        for ($i=0;$i<count($strangerPaginationRows);$i++){
                            $haveMutualFriends= false;
                            for ($j=0;$j<count($resultCountMutualRows);$j++){
                                if ($strangerPaginationRows[$i]["friend_id"] == $resultCountMutualRows[$j]["user2"]){
                                    $strangerPaginationRows[$i]["relation_count"] = $resultCountMutualRows[$j]["relation_count"];
                                    $haveMutualFriends= true;
                                }
                                if ($haveMutualFriends== false){
                                    $strangerPaginationRows[$i]["relation_count"] = 0;
                                }
                            }
                        }
//                        echo "<br><br>Final Result: ";
//                        print_r($strangerPaginationRows);
//                        echo "<br>".count($strangerPaginationRows);

                    } else {
                        echo "<div class='row'><div class='col-12 text-danger text-center'><p>Email $email is not existed!!!</p></div></div>";
                    }
                } else {
                    echo "<div class='row'><div class='col-12 text-danger text-center'><p>Friends Table is not exist!!!</p></div></div>";
                }
            }
            ?>

            <? if ($isLogInSuccessful): ?>
                <div class="row">
                    <div class="col-12 text-center">
                        <h3><?php echo $name ?>'s Friend List Page</h3>
                        <h3>Total number of friends is <?php echo $num_of_friends ?></h3>
                    </div>
                </div>

                <div class="table-responsive-sm">
                    <table class="table table-bordered">
                        <tbody>
                        <?php foreach($strangerPaginationRows as $strangerPaginationRow) : ?>
                            <tr>
                                <td class="text-center"><?php echo $strangerPaginationRow['profile_name']?></td>
                                <td class="text-center"><?php echo $strangerPaginationRow['relation_count']?> mutual friends</td>
                                <td class="text-center">
                                    <form method="post" action="<?php echo $_SERVER["PHP_SELF"];?>">
                                        <input type="hidden" name="friend_id" value="<?php echo $id;?>">
                                        <input type="hidden" name="add_friend_id" value="<?php echo $strangerPaginationRow['friend_id'];?>">
                                        <input type="hidden" name="update_friend_number" value="<?php echo $num_of_friends;?>">
                                        <input type="submit" name="add" value="Add as friend">
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-6 text-center">
                        <?php
                        if ($page == 1){
                            echo '<a class="disabled" href="friendadd.php?page='.($page-1).'">'.Previous.'</a>';
                        } else{
                            echo '<a href="friendadd.php?page='.($page-1).'">'.Previous.'</a>';
                        }
                        ?>
                    </div>

                    <div class="col-6 text-center">
                        <?php
                        if ($page == $numberOfPages){
                            echo '<a class="disabled" href="friendadd.php?page='.($page+1).'">'.Next.'</a>';
                        } else{
                            echo '<a href="friendadd.php?page='.($page+1).'">'.Next.'</a>';
                        }
                        ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6 text-right">
                        <a href="friendlist.php">Friend Lists</a>
                    </div>
                    <div class="col-6">
                        <a href="logout.php">Log out</a>
                    </div>
                </div>

            <? else: ?>
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
            <? endif; ?>
        </div>
    </div>
</body>
</html>