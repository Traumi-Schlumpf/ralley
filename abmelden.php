<?php
session_start();
if(session_destroy()){
	if(! headers_sent() ){
		header("location: Koepenick.php");
		}else{
			echo '<script type="text\javascript">
			window.location.href="Koepenick.php";</script>';
		}
}
session_destroy();
?>