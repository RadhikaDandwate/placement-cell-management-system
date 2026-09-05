<?php

require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: index.php");
    exit;

}


$id =
    filter_input(
        INPUT_POST,
        "id",
        FILTER_VALIDATE_INT
    );


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


if (!$id) {

    $errors[] =
        "Invalid drive.";

}


if ($companyName === "") {

    $errors[] =
        "Company name cannot be empty.";

}


if ($jobRole === "") {

    $errors[] =
        "Job role cannot be empty.";

}


if (
    !is_numeric($package) ||
    (float)$package <= 0
) {

    $errors[] =
        "Package must be a positive number.";

}


if (
    !is_array($branches) ||
    count($branches) === 0
) {

    $errors[] =
        "Select at least one branch.";

}


if (
    !is_numeric($minCgpa) ||
    (float)$minCgpa < 0 ||
    (float)$minCgpa > 10
) {

    $errors[] =
        "CGPA must be between 0 and 10.";

}


if (
    filter_var(
        $maxBacklogs,
        FILTER_VALIDATE_INT
    ) === false ||
    (int)$maxBacklogs < 0
) {

    $errors[] =
        "Maximum backlogs must be 0 or positive.";

}


if ($driveDate === "") {

    $errors[] =
        "Drive date is required.";

}


if ($venue === "") {

    $errors[] =
        "Venue cannot be empty.";

}


if ($description === "") {

    $errors[] =
        "Description cannot be empty.";

}


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

    /* Check exact drive exists */

    $check =
        $pdo->prepare(
            "SELECT id FROM drives WHERE id = ?"
        );

    $check->execute([$id]);


    if (!$check->fetch()) {

        header(
            "Location: index.php?type=error&message="
            . urlencode(
                "The placement drive was not found."
            )
        );

        exit;

    }


    $sql = "
        UPDATE drives

        SET
            company_name = :company_name,
            job_role = :job_role,
            package = :package,
            eligible_branches = :eligible_branches,
            min_cgpa = :min_cgpa,
            max_blacklogs = :max_blacklogs,
            drive_date = :drive_date,
            venue = :venue,
            description = :description,
            remark = :remark

        WHERE id = :id
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
            $remark,

        ":id" =>
            $id

    ]);


    header(
        "Location: index.php?type=success&message="
        . urlencode(
            "Placement drive updated successfully."
        )
    );

    exit;


} catch (PDOException $e) {

    header(
        "Location: index.php?type=error&message="
        . urlencode(
            "Unable to update the placement drive."
        )
    );

    exit;

}

?>