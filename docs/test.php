<?php

require_once '../vendor/autoload.php';

use Ramsey\Uuid\Uuid;

const MY_NAMESPACE = '9a494836-ef67-4c63-a27b-15bc5a17e0ed';

$uuid = Uuid::uuid3(MY_NAMESPACE, 'widget/1234567890');

printf("UUID: %s\n", $uuid->toString());
