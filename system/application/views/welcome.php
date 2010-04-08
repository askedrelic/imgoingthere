<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
   
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Form Generation Library Demo</title>
	<style type="text/css">
	body {
		font-family: "Lucida Grande", "Lucida Sans Unicode", Lucida, Tahoma, Arial, sans-serif;
		font-size: 14px;
		color: #666;
		margin: 0;
		padding: 0;
	}
	
	#wrapper {
		width: 800px;
		margin: auto;
		text-align: left;		
	}
	
	#content {
		padding: 40px;
	}
	
	#content h1 {
		font-size: 20px;
		margin-top: 0px;
	}
	
	.clear {
		clear: both;
	}
	
	a {
		color: #c00;
		text-decoration: none;
	}
	
	a:hover {
		text-decoration: underline;
	}	
	</style>
	<link rel="stylesheet" href="<?=base_url()?>styles/form.css" type="text/css" media="screen" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="content-language" content="de" />   
</head>

<body>
<div id="wrapper">	
	<div id="content">
	<a href="http://frankmichel.de/formgenlib/user_guide/">User Guide</a> | <a href="http://codeigniter.com/forums/viewthread/107861/">CodeIgniter Forums Thread</a><br /><br />
	
	<h1>Form Generation Library Demo</h1>	
	This site will show you an example implementations of the form generation library:<br /><br />

	<?=$errors?>
	<?=$form?>
	
	<br /><br />
	
	<a href="http://validator.w3.org/check?uri=referer"><img src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Strict" height="31" width="88" style="border: none" /></a>
	</div>
</div>
</body>
</html>