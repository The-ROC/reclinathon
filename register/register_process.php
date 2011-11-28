<?php
/*
echo "First Name: " . $firstname . "<br />";
echo "Last Name: " . $lastname . "<br / >";
echo "Email: " . $email . "<br />";
echo "Username: " . $username . "<br />";
echo "Password: " . $password . "<br />";
echo "Status: " . $status . "<br />";
echo "Forward: " . $forward . "<br />";
*/
header("Location: $forward?firstname=$firstname&lastname=$lastname&email=$email&username=$username&status=$status");
