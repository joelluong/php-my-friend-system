<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="description" content="Web application development" />
        <meta name="keywords" content="Assignment1" />
        <meta name="author" content="Dai Trung Duong Luong" />
        <link href="library/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
        <link href="library/bootstrap/css/bootstrap-grid.min.css" rel="stylesheet" />
        <link href="css/custom.css" rel="stylesheet" />
        <title>Assignment 2</title>
    </head>
    <body>
        <div class="container">
            <div class="bg-white text-dark rounded mt-5 p-3">
                <div class="row">
                    <div class="col-12 text-center">
                        <h1>My Friend System</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-center">
                        <h1>Assignment Home Page</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <p>Name: Dai Trung Duong Luong</p>
                    </div>
                    <div class="col-12 col-md-6">
                        <p>Student ID: 101051766</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <p>Email: <a href="101051766@student.swin.edu.au">101051766@student.swin.edu.au</a></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <p>I declare that this assignment is my individual work. I have not worked collaboratively nor have I
                            copied from any other student’s work or from any other source</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <p>Tables successfully created and populated</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-md-4 text-right">
                        <p><a href="signup.php">Sign-Up</a></p>
                    </div>
                    <div class="col-12 col-md-4 text-center">
                        <p><a href="login.php">Log-In</a></p>
                    </div>
                    <div class="col-12 col-md-4 text-left">
                        <p><a href="about.php">About</a></p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

<?php
require("Database.php");

$pdo = new Database();
$friendTableName = "friends";
$myfriendsTableName = "myfriends";

//## 2. set up SQL string and execute
// echo tableExists($friendTableName);
//if ($pdo->tableExists($friendTableName)){
//    echo "Friends Exist";
//} else {
//    echo "Friends Not Exist";
//}

//if ($pdo->tableExists($myfriendsTableName)){
//    echo "My Friends Exist";
//} else {
//    echo "My Friends Not Exist";
//}

$queryCreateFriendsTable = "
CREATE TABLE friends(
friend_id int NOT NULL AUTO_INCREMENT,
friend_email varchar(50) NOT NULL,
password varchar(20) NOT NULL,
profile_name varchar(30) NOT NULL,
date_started date NOT NULL,
num_of_friends int UNSIGNED,
PRIMARY KEY (friend_id),
UNIQUE (friend_email)
);
";

$queryFriendsTableInsert = "INSERT INTO friends (friend_email, password, profile_name, date_started, num_of_friends) VALUES
('duong@aic.com', 'admin', 'Duong', '2012-9-22', 3),
('luong@aic.com', 'admin', 'Luong', '2012-8-19', 3),
('dai@aic.com', 'admin', 'Dai', '1995-9-20', 3),
('trung@aic.com', 'admin', 'Trung', '1995-9-23', 2),
('duong@gmail.com', 'admin', 'Cucumber', '1988-9-23', 3),
('luong@gmail.com', 'admin', 'Tomato', '1990-10-6', 1),
('dai@gmail.com', 'admin', 'Orange', '2005-10-31', 2),
('trung@gmail.com', 'admin', 'Strawbery', '2016-3-11', 0),
('cindy@gmail.com', 'admin', 'Cindy', '2015-9-23', 1),
('wijaya@aic.com', 'admin', 'Wijaya', '1994-11-14', 2)
";

if (!$pdo->tableExists($friendTableName)){
    $pdo->query($queryCreateFriendsTable);
    $pdo->execute();
    $pdo->query($queryFriendsTableInsert);
    $pdo->execute();
}

$queryCreateMyFriendsTable = "
CREATE TABLE myfriends (
friend_id1 int NOT NULL,
friend_id2 int NOT NULL,
UNIQUE (friend_id1, friend_id2)
);
";
$queryMyFriendsTableInsert = "INSERT INTO myfriends (friend_id1, friend_id2) VALUES (1,2), (1,10), (1,5), (2,1), (2,7), (2,10), (3,5),
(3,6), (3,7), (4,5), (4,9), (5,3), (5,4), (5,1), (6,3), (7,2), (7,3), (9,4), (10,1), (10,2);
";

if (!$pdo->tableExists($myfriendsTableName)){
    $pdo->query($queryCreateMyFriendsTable);
    $pdo->execute();
    $pdo->query($queryMyFriendsTableInsert);
    $pdo->execute();
}
?>