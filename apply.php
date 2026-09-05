<?php

require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: index.php");
    exit;

}


$driveId =
    filter_input(
        INPUT_POST,
        "drive_id",
        FILTER_VALIDATE_INT
    );

$studentName =
    trim($_POST["student_name"] ?? "");

$rollNo =
    trim($_POST["roll_no"] ?? "");

$branch =
    trim($_POST["branch"] ?? "");

$cgpa =
    $_POST["cgpa"] ?? "";

$backlogs =
    $_POST["backlogs"] ?? "";


$errors = [];


/* Basic validation */

if (!$driveId) {

    $errors[] =
        "Please select a valid placement drive.";

}


if ($studentName === "") {

    $errors[] =
        "Student name cannot be empty.";

}


if ($rollNo === "") {

    $errors[] =
        "Roll number cannot be empty.";

}


$allowedBranches = [
    "CSE",
    "IT",
    "E&TC",
    "Mechanical",
    "Civil"
];


if (
    !in_array(
        $branch,
        $allowedBranches,
        true
    )
) {

    $errors[] =
        "Please select a valid branch.";

}


if (
    !is_numeric($cgpa) ||
    (float)$cgpa < 0 ||
    (float)$cgpa > 10
) {

    $errors[] =
        "CGPA must be between 0 and 10.";

}


if (
    filter_var(
        $backlogs,
        FILTER_VALIDATE_INT
    ) === false ||
    (int)$backlogs < 0
) {

    $errors[] =
        "Backlogs must be 0 or positive.";

}


if (
    !isset($_FILES["resume"]) ||
    $_FILES["resume"]["error"] !== UPLOAD_ERR_OK
) {

    $errors[] =
        "Please upload a resume PDF.";

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


try {

    /* =====================================================
       GET ACTUAL DRIVE FROM DATABASE
       ===================================================== */

    $stmt =
        $pdo->prepare(
            "SELECT *
             FROM drives
             WHERE id = ?"
        );

    $stmt->execute([$driveId]);

    $drive =
        $stmt->fetch();


    if (!$drive) {

        header(
            "Location: index.php?type=error&message="
            . urlencode(
                "The selected placement drive does not exist."
            )
        );

        exit;

    }


    /* =====================================================
       SERVER-SIDE ELIGIBILITY CHECK
       ===================================================== */

    $eligibleBranches =
        array_map(
            "trim",
            explode(
                ",",
                $drive["eligible_branches"]
            )
        );


    if (
        !in_array(
            $branch,
            $eligibleBranches,
            true
        )
    ) {

        header(
            "Location: index.php?type=error&message="
            . urlencode(
                "Your branch is not eligible for this drive."
            )
        );

        exit;

    }


    if (
        (float)$cgpa <
        (float)$drive["min_cgpa"]
    ) {

        header(
            "Location: index.php?type=error&message="
            . urlencode(
                "Your CGPA is below the minimum required CGPA."
            )
        );

        exit;

    }


    if (
        (int)$backlogs >
        (int)$drive["max_blacklogs"]
    ) {

        header(
            "Location: index.php?type=error&message="
            . urlencode(
                "Your number of backlogs exceeds the allowed limit."
            )
        );

        exit;

    }


    /* =====================================================
       DUPLICATE APPLICATION CHECK
       ===================================================== */

    $stmt =
        $pdo->prepare(
            "SELECT id
             FROM applications
             WHERE drive_id = ?
             AND roll_no = ?"
        );

    $stmt->execute([
        $driveId,
        $rollNo
    ]);


    if ($stmt->fetch()) {

        header(
            "Location: index.php?type=error&message="
            . urlencode(
                "You have already applied for this drive."
            )
        );

        exit;

    }


    /* =====================================================
       RESUME VALIDATION
       ===================================================== */

    $resume =
        $_FILES["resume"];


    $extension =
        strtolower(
            pathinfo(
                $resume["name"],
                PATHINFO_EXTENSION
            )
        );


    if ($extension !== "pdf") {

        header(
            "Location: index.php?type=error&message="
            . urlencode(
                "Resume must be a PDF file."
            )
        );

        exit;

    }


    /* Check MIME type */

    $fileInfo =
        finfo_open(
            FILEINFO_MIME_TYPE
        );


    $mimeType =
        finfo_file(
            $fileInfo,
            $resume["tmp_name"]
        );


    finfo_close($fileInfo);


    if ($mimeType !== "application/pdf") {

        header(
            "Location: index.php?type=error&message="
            . urlencode(
                "Uploaded resume is not a valid PDF."
            )
        );

        exit;

    }


    /* =====================================================
       SAVE RESUME WITH UNIQUE NAME
       ===================================================== */

    $uploadDirectory =
        __DIR__ . "/uploads/";


    if (!is_dir($uploadDirectory)) {

        mkdir(
            $uploadDirectory,
            0755,
            true
        );

    }


    $uniqueName =
        "resume_"
        . bin2hex(
            random_bytes(16)
        )
        . ".pdf";


    $destination =
        $uploadDirectory . $uniqueName;


    if (
        !move_uploaded_file(
            $resume["tmp_name"],
            $destination
        )
    ) {

        header(
            "Location: index.php?type=error&message="
            . urlencode(
                "Unable to save the uploaded resume."
            )
        );

        exit;

    }


    $resumePath =
        "uploads/" . $uniqueName;


    /* =====================================================
       INSERT APPLICATION
       ===================================================== */

    $sql = "
        INSERT INTO applications
        (
            drive_id,
            student_name,
            roll_no,
            branch,
            cgpa,
            backlogs,
            resume_path,
            status
        )
        VALUES
        (
            :drive_id,
            :student_name,
            :roll_no,
            :branch,
            :cgpa,
            :backlogs,
            :resume_path,
            'applied'
        )
    ";


    $stmt =
        $pdo->prepare($sql);


    $stmt->execute([

        ":drive_id" =>
            $driveId,

        ":student_name" =>
            $studentName,

        ":roll_no" =>
            $rollNo,

        ":branch" =>
            $branch,

        ":cgpa" =>
            $cgpa,

        ":backlogs" =>
            $backlogs,

        ":resume_path" =>
            $resumePath

    ]);


    header(
        "Location: index.php?type=success&message="
        . urlencode(
            "Application submitted successfully."
        )
    );

    exit;


} catch (PDOException $e) {

    header(
        "Location: index.php?type=error&message="
        . urlencode(
            "Unable to submit the application. Please try again."
        )
    );

    exit;

}

?>