<?php

require __DIR__ . '/../vendor/autoload.php';

runShellScript(['vendor/bin/phinx', 'rollback', '-e', 'testing', '-t', '0']);
runShellScript(['vendor/bin/phinx', 'migrate', '-e', 'testing']);
