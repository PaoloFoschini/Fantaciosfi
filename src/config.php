<?php
const PROD = true;
if(!PROD) {
	$_SESSION['user'] = [
	    'email' => 'pol.foschini@gmail.com',
	    'name' => 'Pol Foschini',
	];
}
