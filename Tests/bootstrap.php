<?php

if (!empty($_ENV['DB_FLAVOR'])) {
	$_ENV['DB_DSN'] = $_ENV['DB_DSN_' . strtoupper($_ENV['DB_FLAVOR'])];
}
