<?php

    isset($_GET) ? var_dump($_GET) : null;
    echo isset($params['userid']) ? "is profile" : "is connecting";