<?php
	session_start();
	echo($_SESSION['filename']);
	session_write_close();
?>