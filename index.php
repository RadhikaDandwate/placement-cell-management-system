<?php

require_once "db.php";

$message = $_GET["message"] ?? "";
$type = $_GET["type"] ?? "";


// Get all drives

try {

    $stmt = $pdo->prepare(
        "SELECT * FROM drives ORDER BY drive_date ASC"
    );

    $stmt->execute();

    $drives = $stmt->fetchAll();

} catch (PDOException $e) {

    $drives = [];

    $message = "Unable to load placement drives.";
    $type = "error";

}


// Selected drive for applicants

$selectedDrive = null;
$applications = [];

if (isset($_GET["view"])) {

    $driveId = filter_input(
        INPUT_GET,
        "view",
        FILTER_VALIDATE_INT
    );

    if ($driveId) {

        try {

            $stmt = $pdo->prepare(
                "SELECT * FROM drives WHERE id = ?"
            );

            $stmt->execute([$driveId]);

            $selectedDrive = $stmt->fetch();

            if ($selectedDrive) {

                $stmt = $pdo->prepare(
                    "SELECT *
                     FROM applications
                     WHERE drive_id = ?
                     ORDER BY applied_at DESC"
                );

                $stmt->execute([$driveId]);

                $applications = $stmt->fetchAll();

            }

        } catch (PDOException $e) {

            $message = "Unable to load applications.";
            $type = "error";

        }

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Training & Placement Cell
    </title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>

<body>


<!-- ================= TOP BAR ================= -->

<header class="top-bar">

    <div>

        <h2>
            Sanjivani College of Engineering, Kopargaon
        </h2>

        <p>
            Training & Placement Cell
        </p>

    </div>

    <div class="officer-badge">
        Placement Portal
    </div>

</header>


<!-- ================= MAIN ================= -->

<main class="container">


<?php if ($message !== ""): ?>

    <div
        class="<?= $type === "success"
            ? "success-message"
            : "error-message" ?>"
    >

        <?= htmlspecialchars($message) ?>

    </div>

<?php endif; ?>


<!-- ================= ADD DRIVE ================= -->

<section class="section">

    <div class="section-heading">

        <h2>
            Placement Officer
        </h2>

        <p>
            Manage placement drives and monitor applications.
        </p>

    </div>


    <div class="card">

        <h3>
            Add Placement Drive
        </h3>

        <br>


        <form
            id="addDriveForm"
            action="add_drive.php"
            method="POST"
        >

            <div class="form-grid">


                <!-- Company -->

                <div class="form-group">

                    <label for="company_name">
                        Company Name *
                    </label>

                    <input
                        type="text"
                        id="company_name"
                        name="company_name"
                        placeholder="Enter company name"
                    >

                    <small
                        class="field-error"
                        id="company_name_error"
                    ></small>

                </div>


                <!-- Role -->

                <div class="form-group">

                    <label for="job_role">
                        Job Role *
                    </label>

                    <input
                        type="text"
                        id="job_role"
                        name="job_role"
                        placeholder="Enter job role"
                    >

                    <small
                        class="field-error"
                        id="job_role_error"
                    ></small>

                </div>


                <!-- Package -->

                <div class="form-group">

                    <label for="package">
                        Package (LPA) *
                    </label>

                    <input
                        type="number"
                        id="package"
                        name="package"
                        step="0.01"
                        min="0.01"
                        placeholder="Example: 7.5"
                    >

                    <small
                        class="field-error"
                        id="package_error"
                    ></small>

                </div>


                <!-- Minimum CGPA -->

                <div class="form-group">

                    <label for="min_cgpa">
                        Minimum CGPA *
                    </label>

                    <input
                        type="number"
                        id="min_cgpa"
                        name="min_cgpa"
                        step="0.01"
                        min="0"
                        max="10"
                        placeholder="0 - 10"
                    >

                    <small
                        class="field-error"
                        id="min_cgpa_error"
                    ></small>

                </div>


                <!-- Maximum Backlogs -->

                <div class="form-group">

                    <label for="max_blacklogs">
                        Maximum Backlogs *
                    </label>

                    <input
                        type="number"
                        id="max_blacklogs"
                        name="max_blacklogs"
                        min="0"
                        step="1"
                        placeholder="Example: 2"
                    >

                    <small
                        class="field-error"
                        id="max_blacklogs_error"
                    ></small>

                </div>


                <!-- Drive Date -->

                <div class="form-group">

                    <label for="drive_date">
                        Drive Date *
                    </label>

                    <input
                        type="date"
                        id="drive_date"
                        name="drive_date"
                    >

                    <small
                        class="field-error"
                        id="drive_date_error"
                    ></small>

                </div>


                <!-- Venue -->

                <div class="form-group">

                    <label for="venue">
                        Venue *
                    </label>

                    <input
                        type="text"
                        id="venue"
                        name="venue"
                        placeholder="Example: Seminar Hall"
                    >

                    <small
                        class="field-error"
                        id="venue_error"
                    ></small>

                </div>


                <!-- Eligible Branches -->

                <div class="form-group">

                    <label>
                        Eligible Branches *
                    </label>

                    <div class="branch-options">

                        <label class="check-option">
                            <input
                                type="checkbox"
                                name="eligible_branches[]"
                                value="CSE"
                            >
                            CSE
                        </label>

                        <label class="check-option">
                            <input
                                type="checkbox"
                                name="eligible_branches[]"
                                value="IT"
                            >
                            IT
                        </label>

                        <label class="check-option">
                            <input
                                type="checkbox"
                                name="eligible_branches[]"
                                value="E&TC"
                            >
                            E&TC
                        </label>

                        <label class="check-option">
                            <input
                                type="checkbox"
                                name="eligible_branches[]"
                                value="Mechanical"
                            >
                            Mechanical
                        </label>

                        <label class="check-option">
                            <input
                                type="checkbox"
                                name="eligible_branches[]"
                                value="Civil"
                            >
                            Civil
                        </label>

                    </div>

                    <small
                        class="field-error"
                        id="eligible_branches_error"
                    ></small>

                </div>


                <!-- Description -->

                <div class="form-group full-width">

                    <label for="description">
                        Description *
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        placeholder="Enter drive description"
                    ></textarea>

                    <small
                        class="field-error"
                        id="description_error"
                    ></small>

                </div>


                <!-- Remark -->

                <div class="form-group full-width">

                    <label for="remark">
                        Remark *
                    </label>

                    <textarea
                        id="remark"
                        name="remark"
                        rows="2"
                        placeholder="Enter important note about this drive"
                    ></textarea>

                    <small
                        class="field-error"
                        id="remark_error"
                    ></small>

                </div>

            </div>


            <button
                type="submit"
                id="addDriveButton"
                class="primary-btn"
                disabled
            >
                Add Drive
            </button>

        </form>

    </div>

</section>


<!-- ================= DRIVE TABLE ================= -->

<section class="section">

    <div class="section-heading">

        <h2>
            Placement Drives
        </h2>

        <p>
            All drives currently stored in the database.
        </p>

    </div>


    <div class="card">

        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>Company</th>
                        <th>Role</th>
                        <th>Package</th>
                        <th>Branches</th>
                        <th>Min CGPA</th>
                        <th>Max Backlogs</th>
                        <th>Drive Date</th>
                        <th>Venue</th>
                        <th>Remark</th>
                        <th>Actions</th>

                    </tr>

                </thead>


                <tbody>

                <?php if (count($drives) > 0): ?>

                    <?php foreach ($drives as $drive): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars(
                                    $drive["company_name"]
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $drive["job_role"]
                                ) ?>
                            </td>

                            <td>
                                ₹<?= htmlspecialchars(
                                    $drive["package"]
                                ) ?> LPA
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $drive["eligible_branches"]
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $drive["min_cgpa"]
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $drive["max_blacklogs"]
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $drive["drive_date"]
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $drive["venue"]
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $drive["remark"]
                                ) ?>
                            </td>

                            <td>

                                <div class="actions">

                                    <button
                                        type="button"
                                        class="edit-btn"
                                        onclick='openEditPopup(
                                            <?= json_encode(
                                                $drive
                                            ) ?>
                                        )'
                                    >
                                        Edit
                                    </button>


                                    <button
                                        type="button"
                                        class="delete-btn"
                                        onclick='openDeletePopup(
                                            <?= (int)$drive["id"] ?>,
                                            <?= json_encode(
                                                $drive["company_name"]
                                            ) ?>
                                        )'
                                    >
                                        Delete
                                    </button>


                                    <a
                                        href="index.php?view=<?= (int)$drive["id"] ?>"
                                        class="view-btn"
                                    >
                                        View Applications
                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="10"
                            class="no-data"
                        >
                            No placement drives available.
                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</section>


<!-- ================= APPLICANTS ================= -->

<?php if ($selectedDrive): ?>

<section
    class="section"
    id="applicants"
>

    <div class="section-heading">

        <h2>
            Applicants
        </h2>

        <p>
            <?= htmlspecialchars(
                $selectedDrive["company_name"]
            ) ?>

            -
            <?= htmlspecialchars(
                $selectedDrive["job_role"]
            ) ?>
        </p>

    </div>


    <div class="card">

        <div class="card-heading">

            <div>

                <h3>
                    Applicant List
                </h3>

                <p>

                    Minimum CGPA:
                    <strong>
                        <?= htmlspecialchars(
                            $selectedDrive["min_cgpa"]
                        ) ?>
                    </strong>

                </p>

            </div>


            <div class="shortlist-info">

                <span class="shortlist-rule">

                    Auto-shortlist CGPA ≥

                    <?= number_format(
                        (float)$selectedDrive["min_cgpa"] + 0.5,
                        2
                    ) ?>

                </span>


                <button
                    type="button"
                    class="auto-shortlist-btn"
                    onclick="highlightStrongApplicants()"
                >
                    Auto Shortlist
                </button>

            </div>

        </div>


        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>Name</th>
                        <th>Roll No.</th>
                        <th>Branch</th>
                        <th>CGPA</th>
                        <th>Backlogs</th>
                        <th>Resume</th>
                        <th>Applied At</th>
                        <th>Status</th>

                    </tr>

                </thead>


                <tbody>

                <?php if (count($applications) > 0): ?>

                    <?php foreach ($applications as $application): ?>

                        <?php

                        $threshold =
                            (float)$selectedDrive["min_cgpa"] + 0.5;

                        $isStrong =
                            (float)$application["cgpa"] >= $threshold;

                        ?>


                        <tr
                            class="<?= $isStrong
                                ? "strong-applicant"
                                : "" ?>"
                        >

                            <td>
                                <?= htmlspecialchars(
                                    $application["student_name"]
                                ) ?>
                            </td>


                            <td>
                                <?= htmlspecialchars(
                                    $application["roll_no"]
                                ) ?>
                            </td>


                            <td>
                                <?= htmlspecialchars(
                                    $application["branch"]
                                ) ?>
                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $application["cgpa"]
                                ) ?>


                                <?php if ($isStrong): ?>

                                    <span class="strong-badge">
                                        Strong Match
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td>
                                <?= htmlspecialchars(
                                    $application["backlogs"]
                                ) ?>
                            </td>


                            <td>

                                <a
                                    href="<?= htmlspecialchars(
                                        $application["resume_path"]
                                    ) ?>"
                                    target="_blank"
                                    class="resume-btn"
                                >
                                    View Resume
                                </a>

                            </td>


                            <td>
                                <?= htmlspecialchars(
                                    $application["applied_at"]
                                ) ?>
                            </td>


                            <td>

                                <form
                                    action="update_status.php"
                                    method="POST"
                                >

                                    <input
                                        type="hidden"
                                        name="application_id"
                                        value="<?= (int)$application["id"] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="drive_id"
                                        value="<?= (int)$selectedDrive["id"] ?>"
                                    >


                                    <select
                                        name="status"
                                        onchange="this.form.submit()"
                                    >

                                        <option
                                            value="applied"
                                            <?= $application["status"] === "applied"
                                                ? "selected"
                                                : "" ?>
                                        >
                                            Applied
                                        </option>

                                        <option
                                            value="shortlisted"
                                            <?= $application["status"] === "shortlisted"
                                                ? "selected"
                                                : "" ?>
                                        >
                                            Shortlisted
                                        </option>

                                        <option
                                            value="selected"
                                            <?= $application["status"] === "selected"
                                                ? "selected"
                                                : "" ?>
                                        >
                                            Selected
                                        </option>

                                        <option
                                            value="rejected"
                                            <?= $application["status"] === "rejected"
                                                ? "selected"
                                                : "" ?>
                                        >
                                            Rejected
                                        </option>

                                    </select>

                                </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="8"
                            class="no-data"
                        >
                            No students have applied for this drive yet.
                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</section>

<?php endif; ?>


<!-- ================= STUDENT APPLICATION ================= -->

<section class="section student-section">

    <div class="section-heading">

        <h2>
            Student Placement Portal
        </h2>

        <p>
            Check eligibility and apply for a placement drive.
        </p>

    </div>


    <div class="card">

        <h3>
            Apply for a Drive
        </h3>

        <br>


        <form
            id="applyForm"
            action="apply.php"
            method="POST"
            enctype="multipart/form-data"
        >


            <div class="form-grid">


                <!-- Drive -->

                <div class="form-group full-width">

                    <label for="apply_drive_id">
                        Select Drive *
                    </label>

                    <select
                        id="apply_drive_id"
                        name="drive_id"
                    >

                        <option value="">
                            Select a placement drive
                        </option>

                        <?php foreach ($drives as $drive): ?>

                            <option
                                value="<?= (int)$drive["id"] ?>"
                                data-min-cgpa="<?= htmlspecialchars(
                                    $drive["min_cgpa"]
                                ) ?>"
                                data-max-backlogs="<?= htmlspecialchars(
                                    $drive["max_blacklogs"]
                                ) ?>"
                                data-branches="<?= htmlspecialchars(
                                    $drive["eligible_branches"]
                                ) ?>"
                            >

                                <?= htmlspecialchars(
                                    $drive["company_name"]
                                ) ?>

                                -
                                <?= htmlspecialchars(
                                    $drive["job_role"]
                                ) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                    <small
                        class="field-error"
                        id="apply_drive_id_error"
                    ></small>

                    <div
                        id="eligibilityMessage"
                        class="eligibility-message"
                    ></div>

                </div>


                <!-- Student Name -->

                <div class="form-group">

                    <label for="student_name">
                        Student Name *
                    </label>

                    <input
                        type="text"
                        id="student_name"
                        name="student_name"
                        placeholder="Enter your name"
                    >

                    <small
                        class="field-error"
                        id="student_name_error"
                    ></small>

                </div>


                <!-- Roll Number -->

                <div class="form-group">

                    <label for="roll_no">
                        Roll Number *
                    </label>

                    <input
                        type="text"
                        id="roll_no"
                        name="roll_no"
                        placeholder="Enter roll number"
                    >

                    <small
                        class="field-error"
                        id="roll_no_error"
                    ></small>

                </div>


                <!-- Branch -->

                <div class="form-group">

                    <label for="student_branch">
                        Branch *
                    </label>

                    <select
                        id="student_branch"
                        name="branch"
                    >

                        <option value="">
                            Select branch
                        </option>

                        <option value="CSE">
                            CSE
                        </option>

                        <option value="IT">
                            IT
                        </option>

                        <option value="E&TC">
                            E&TC
                        </option>

                        <option value="Mechanical">
                            Mechanical
                        </option>

                        <option value="Civil">
                            Civil
                        </option>

                    </select>

                    <small
                        class="field-error"
                        id="student_branch_error"
                    ></small>

                </div>


                <!-- CGPA -->

                <div class="form-group">

                    <label for="student_cgpa">
                        CGPA *
                    </label>

                    <input
                        type="number"
                        id="student_cgpa"
                        name="cgpa"
                        min="0"
                        max="10"
                        step="0.01"
                        placeholder="0 - 10"
                    >

                    <small
                        class="field-error"
                        id="student_cgpa_error"
                    ></small>

                </div>


                <!-- Backlogs -->

                <div class="form-group">

                    <label for="student_backlogs">
                        Backlogs *
                    </label>

                    <input
                        type="number"
                        id="student_backlogs"
                        name="backlogs"
                        min="0"
                        step="1"
                        placeholder="0 or more"
                    >

                    <small
                        class="field-error"
                        id="student_backlogs_error"
                    ></small>

                </div>


                <!-- Resume -->

                <div class="form-group">

                    <label for="resume">
                        Resume (PDF only) *
                    </label>

                    <input
                        type="file"
                        id="resume"
                        name="resume"
                        accept=".pdf,application/pdf"
                    >

                    <small
                        class="field-error"
                        id="resume_error"
                    ></small>

                </div>

            </div>


            <div
                id="applyValidationMessage"
                class="form-validation-message"
            ></div>


            <button
                type="submit"
                id="applyButton"
                class="primary-btn"
                disabled
            >
                Apply for Drive
            </button>

        </form>

    </div>

</section>


</main>


<!-- ================= EDIT POPUP ================= -->

<div
    id="editPopup"
    class="popup"
>

    <div class="popup-box">

        <div class="popup-header">

            <div>

                <h3>
                    Edit Placement Drive
                </h3>

                <p>
                    Update the drive details.
                </p>

            </div>


            <button
                type="button"
                class="close-btn"
                onclick="closeEditPopup()"
            >
                &times;
            </button>

        </div>


        <form
            id="editDriveForm"
            action="edit_drive.php"
            method="POST"
        >

            <input
                type="hidden"
                id="edit_id"
                name="id"
            >


            <div class="form-grid">


                <div class="form-group">

                    <label>
                        Company Name *
                    </label>

                    <input
                        type="text"
                        id="edit_company_name"
                        name="company_name"
                    >

                    <small
                        class="field-error"
                        id="edit_company_name_error"
                    ></small>

                </div>


                <div class="form-group">

                    <label>
                        Job Role *
                    </label>

                    <input
                        type="text"
                        id="edit_job_role"
                        name="job_role"
                    >

                    <small
                        class="field-error"
                        id="edit_job_role_error"
                    ></small>

                </div>


                <div class="form-group">

                    <label>
                        Package *
                    </label>

                    <input
                        type="number"
                        id="edit_package"
                        name="package"
                        step="0.01"
                        min="0.01"
                    >

                    <small
                        class="field-error"
                        id="edit_package_error"
                    ></small>

                </div>


                <div class="form-group">

                    <label>
                        Minimum CGPA *
                    </label>

                    <input
                        type="number"
                        id="edit_min_cgpa"
                        name="min_cgpa"
                        step="0.01"
                        min="0"
                        max="10"
                    >

                    <small
                        class="field-error"
                        id="edit_min_cgpa_error"
                    ></small>

                </div>


                <div class="form-group">

                    <label>
                        Maximum Backlogs *
                    </label>

                    <input
                        type="number"
                        id="edit_max_blacklogs"
                        name="max_blacklogs"
                        min="0"
                        step="1"
                    >

                    <small
                        class="field-error"
                        id="edit_max_blacklogs_error"
                    ></small>

                </div>


                <div class="form-group">

                    <label>
                        Drive Date *
                    </label>

                    <input
                        type="date"
                        id="edit_drive_date"
                        name="drive_date"
                    >

                    <small
                        class="field-error"
                        id="edit_drive_date_error"
                    ></small>

                </div>


                <div class="form-group">

                    <label>
                        Venue *
                    </label>

                    <input
                        type="text"
                        id="edit_venue"
                        name="venue"
                    >

                    <small
                        class="field-error"
                        id="edit_venue_error"
                    ></small>

                </div>


                <div class="form-group">

                    <label>
                        Eligible Branches *
                    </label>

                    <div class="branch-options">

                        <label class="check-option">
                            <input
                                type="checkbox"
                                name="eligible_branches[]"
                                value="CSE"
                                class="edit-branch"
                            >
                            CSE
                        </label>

                        <label class="check-option">
                            <input
                                type="checkbox"
                                name="eligible_branches[]"
                                value="IT"
                                class="edit-branch"
                            >
                            IT
                        </label>

                        <label class="check-option">
                            <input
                                type="checkbox"
                                name="eligible_branches[]"
                                value="E&TC"
                                class="edit-branch"
                            >
                            E&TC
                        </label>

                        <label class="check-option">
                            <input
                                type="checkbox"
                                name="eligible_branches[]"
                                value="Mechanical"
                                class="edit-branch"
                            >
                            Mechanical
                        </label>

                        <label class="check-option">
                            <input
                                type="checkbox"
                                name="eligible_branches[]"
                                value="Civil"
                                class="edit-branch"
                            >
                            Civil
                        </label>

                    </div>

                    <small
                        class="field-error"
                        id="edit_eligible_branches_error"
                    ></small>

                </div>


                <div class="form-group full-width">

                    <label>
                        Description *
                    </label>

                    <textarea
                        id="edit_description"
                        name="description"
                        rows="3"
                    ></textarea>

                    <small
                        class="field-error"
                        id="edit_description_error"
                    ></small>

                </div>


                <div class="form-group full-width">

                    <label>
                        Remark *
                    </label>

                    <textarea
                        id="edit_remark"
                        name="remark"
                        rows="2"
                    ></textarea>

                    <small
                        class="field-error"
                        id="edit_remark_error"
                    ></small>

                </div>

            </div>


            <div class="popup-footer">

                <button
                    type="button"
                    class="secondary-btn"
                    onclick="closeEditPopup()"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="primary-btn"
                >
                    Save Changes
                </button>

            </div>

        </form>

    </div>

</div>


<!-- ================= DELETE POPUP ================= -->

<div
    id="deletePopup"
    class="popup"
>

    <div class="delete-box">

        <div class="warning-icon">
            !
        </div>

        <h3>
            Are you sure?
        </h3>

        <p>
            You are about to delete this placement drive.
        </p>

        <p id="deleteDriveName"></p>


        <form
            action="delete_drive.php"
            method="POST"
        >

            <input
                type="hidden"
                id="delete_id"
                name="id"
            >


            <div class="popup-footer">

                <button
                    type="button"
                    class="secondary-btn"
                    onclick="closeDeletePopup()"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="delete-confirm-btn"
                >
                    Yes, Delete
                </button>

            </div>

        </form>

    </div>

</div>


<!-- ================= FOOTER ================= -->

<footer class="footer">

    <div>

        <h3>
            Training & Placement Cell
        </h3>

        <p>
            Sanjivani College of Engineering, Kopargaon
        </p>

    </div>


    <div class="contact">

        <span>
            Placement Cell Contact
        </span>

        <span>
            Email: placement@sanjivani.edu
        </span>

        <span>
            Phone: +91 XXXXX XXXXX
        </span>

    </div>

</footer>


<script src="js/script.js"></script>

</body>

</html>