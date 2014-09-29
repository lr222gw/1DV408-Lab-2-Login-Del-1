<?php
class HTMLView {

    public function echoHTML($body) {

        echo "
				<!DOCTYPE html>
				<html>
				<head>
				<meta charset=\"utf-8\">
				<title>Laboration 4 </title>
				<link rel='stylesheet' href='style.css' type='text/css'
				</head>
				<body>
					$body
				</body>
				</html>";
    }
}