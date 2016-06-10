<?php

$base_dir = dirname(__FILE__) . '/../../../';
require($base_dir . 'wp-load.php');

$wordpress_gallery = $_POST["wordpress_gallery"];
$orderby = $_POST["orderby"];

update_option("owl_carousel_wordpress_gallery", $wordpress_gallery);
update_option("owl_carousel_orderby", $orderby);