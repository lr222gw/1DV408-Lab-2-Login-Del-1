<?php
class HTMLView {

    public function echoHTML($body) {

        echo "
				<!DOCTYPE html>
				<html>
				<head>
				<meta charset=\"utf-8\">
				<title>Laboration 2</title>
				</head>
				<body>
					$body
				</body>
				</html>";
    }
}