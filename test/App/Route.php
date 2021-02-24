<?php

use Yjtec\Linphe\Router;

Router::cli("/cli/u", "Test\\App\\User", 'start');
Router::any("//u", "Test\\App\\User", 'start');
