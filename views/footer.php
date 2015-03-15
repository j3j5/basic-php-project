		</div>
		<!-- JS at the footer
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<?php
		foreach($css_files['footer'] AS $file) {
			echo '<link href="' . $file . '" rel="stylesheet">'.PHP_EOL;
		}

		foreach($js_files['footer'] AS $file) {
			echo '<script  src="' . $file . '></script>'.PHP_EOL;
		}
		?>

	</body>
</html>
