<?php

use Yjtec\Linphe\Router;

Router::cli("/cli/u", "Test\\App\\User", 'start');
Router::cli("//u", "Test\\App\\User", 'start');
