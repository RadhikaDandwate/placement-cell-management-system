<?php

require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: index.php");
    exit;

}


$applicationId =
    filter_input(
        INPUT_POST,
        "application_id",
        FILTER_VALIDATE_INT
    );

$driveId =
    filter_input(
        INPUT_POST,
        "drive_id",
        FILTER_VALIDATE_INT
    );

$status =
    $_POST["status"] ?? "";


$allowedStatuses = [
    "applied",
    "shortlisted",
    "selected",
    "rejected"
];


if (!$applicationId || !$driveId) {

    header(
        "Location: index.php?type=error&message="
        . urlencode(
            "Invalid application."
        )
    );

    exit;

}


if (
    !in_array(
        $status,
        $allowedStatuses,
        true
    )
) {

    header(
        "Location: index.php?type=error&message="
        . urlencode(
            "Invalid application status."
        )
    );

    exit;

}


try {

    $stmt =
        $pdo->prepare(
            "UPDATE applications
             SET status = ?
             WHERE id = ?
             AND drive_id = ?"
        );


    $stmt->execute([
        $status,
        $applicationId,
        $driveId
    ]);


    if ($stmt->rowCount() === 0) {

        header(
            "Location: index.php?view="
            . $driveId
            . "&type=error&message="
            . urlencode(
                "Unable to update applicant status."
            )
        );

        exit;

    }


    header(
        "Location: index.php?view="
        . $driveId
        . "&type=success&message="
        . urlencode(
            "Applicant status updated successfully."
        )
    );

    exit;


} catch (PDOException $e) {

    header(
        "Location: index.php?view="
        . $driveId
        . "&type=error&message="
        . urlencode(
            "Unable to update applicant status."
        )
    );

    exit;

}

?>