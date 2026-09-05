<?php

require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: index.php");
    exit;

}


$companyName =
    trim($_POST["company_name"] ?? "");

$jobRole =
    trim($_POST["job_role"] ?? "");

$package =
    $_POST["package"] ?? "";

$branches =
    $_POST["eligible_branches"] ?? [];

$minCgpa =
    $_POST["min_cgpa"] ?? "";

$maxBacklogs =
    $_POST["max_blacklogs"] ?? "";

$driveDate =
    $_POST["drive_date"] ?? "";

$venue =
    trim($_POST["venue"] ?? "");

$description =
    trim($_POST["description"] ?? "");

$remark =
    trim($_POST["remark"] ?? "");


$errors = [];


/* Company */

if ($companyName === "") {

    $errors[] =
        "Company name cannot be empty.";

}


/* Role */

if ($jobRole === "") {

    $errors[] =
        "Job role cannot be empty.";

}


/* Package */

if (
    !is_numeric($package) ||
    (float)$package <= 0
) {

    $errors[] =
        "Package must be a positive number.";

}


/* Branches */

if (
    !is_array($branches) ||
    count($branches) === 0
) {

    $errors[] =
        "Select at least one eligible branch.";

}


/* CGPA */

if (
    !is_numeric($minCgpa) ||
    (float)$minCgpa < 0 ||
    (float)$minCgpa > 10
) {

    $errors[] =
        "Minimum CGPA must be between 0 and 10.";

}


/* Backlogs */

if (
    filter_var(
        $maxBacklogs,
        FILTER_VALIDATE_INT
    ) === false ||
    (int)$maxBacklogs < 0
) {

    $errors[] =
        "Maximum backlogs must be 0 or a positive number.";

}


/* Date */

if ($driveDate === "") {

    $errors[] =
        "Drive date is required.";

}


/* Venue */

if ($venue === "") {

    $errors[] =
        "Venue cannot be empty.";

}


/* Description */

if ($description === "") {

    $errors[] =
        "Description cannot be empty.";

}


/* Remark */

if ($remark === "") {

    $errors[] =
        "Remark cannot be empty.";

}


if (count($errors) > 0) {

    header(
        "Location: index.php?type=error&message="
        . urlencode(
            implode(" ", $errors)
        )
    );

    exit;

}


$allowedBranches = [
    "CSE",
    "IT",
    "E&TC",
    "Mechanical",
    "Civil"
];


$validBranches = [];

foreach ($branches as $branch) {

    if (
        in_array(
            $branch,
            $allowedBranches,
            true
        )
    ) {

        $validBranches[] = $branch;

    }

}


if (count($validBranches) === 0) {

    header(
        "Location: index.php?type=error&message="
        . urlencode(
            "Invalid branch selection."
        )
    );

    exit;

}


$branchString =
    implode(", ", $validBranches);


try {

    $sql = "
        INSERT INTO drives
        (
            company_name,
            job_role,
            package,
            eligible_branches,
            min_cgpa,
            max_blacklogs,
            drive_date,
            venue,
            description,
            remark
        )
        VALUES
        (
            :company_name,
            :job_role,
            :package,
            :eligible_branches,
            :min_cgpa,
            :max_blacklogs,
            :drive_date,
            :venue,
            :description,
            :remark
        )
    ";


    $stmt =
        $pdo->prepare($sql);


    $stmt->execute([
        ":company_name" =>
            $companyName,

        ":job_role" =>
            $jobRole,

        ":package" =>
            $package,

        ":eligible_branches" =>
            $branchString,

        ":min_cgpa" =>
            $minCgpa,

        ":max_blacklogs" =>
            $maxBacklogs,

        ":drive_date" =>
            $driveDate,

        ":venue" =>
            $venue,

        ":description" =>
            $description,

        ":remark" =>
            $remark
    ]);


    header(
        "Location: index.php?type=success&message="
        . urlencode(
            "Placement drive added successfully."
        )
    );

    exit;


} catch (PDOException $e) {

    header(
        "Location: index.php?type=error&message="
        . urlencode(
            "Unable to add the placement drive. Please try again."
        )
    );

    exit;

}

?>