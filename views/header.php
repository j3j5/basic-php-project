<!DOCTYPE html>
<html lang="en">
	<head>

		<link href='http://fonts.googleapis.com/css?family=PT+Sans+Narrow:400,700&subset=latin,cyrillic-ext,cyrillic,latin-ext' rel='stylesheet' type='text/css'>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="author" content="@julioelpoeta">
		<meta http-equiv="Content-Language" content="en">
		<meta name="description" content="<?php if(isset($description)) echo $description; ?>">

		<meta charset="utf-8">

		<title>
			<?php if(isset($title)) echo $title; ?>
		</title>


		<!-- CSS AND JS-->
		<?php
			foreach($js_files['header'] AS $file) {
				echo '<script  src="' . $file . '></script>'.PHP_EOL;
			}

			foreach($css_files['header'] AS $file) {
				echo '<link href="' . $file . '" rel="stylesheet">'.PHP_EOL;
			}
		?>

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script type="text/javascript" src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script type="text/javascript" src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<div class="site-wrapper">
			<div class="inner cover">
				<div class="page-header">
					<h1 class="cover-heading">PROJECT_NAME</h1>
				</div>
				<?php include('alerts.php'); ?>
