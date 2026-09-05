<?php

function clean($value)
{
    return trim($value);
}


function validCgpa($cgpa)
{
    return is_numeric($cgpa)
        && $cgpa >= 0
        && $cgpa <= 10;
}


function validBacklogs($backlogs)
{
    return filter_var(
        $backlogs,
        FILTER_VALIDATE_INT,
        [
            "options" => [
                "min_range" => 0
            ]
        ]
    ) !== false;
}


function validPackage($package)
{
    return is_numeric($package) && $package > 0;
}


function validBranches($branches)
{
    return !empty($branches);
}


function redirectWithMessage($message, $type = "success")
{
    header(
        "Location: index.php?message=" .
        urlencode($message) .
        "&type=" .
        urlencode($type)
    );

    exit;
}

?>