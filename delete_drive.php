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


if (!$id) {

    header(
        "Location: index.php?type=error&message="
        . urlencode(
            "Invalid drive."
        )
    );

    exit;

}


try {

    $stmt =
        $pdo->prepare(
            "DELETE FROM drives WHERE id = ?"
        );


    $stmt->execute([$id]);


    if ($stmt->rowCount() === 0) {

        header(
            "Location: index.php?type=error&message="
            . urlencode(
                "The placement drive was not found."
            )
        );

        exit;

    }


    header(
        "Location: index.php?type=success&message="
        . urlencode(
            "Placement drive deleted successfully."
        )
    );

    exit;


} catch (PDOException $e) {

    header(
        "Location: index.php?type=error&message="
        . urlencode(
            "Unable to delete the placement drive."
        )
    );

    exit;

}

?>