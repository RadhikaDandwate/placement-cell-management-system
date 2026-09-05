/* =========================================================
   COMMON FUNCTIONS
   ========================================================= */

function setError(id, message)
{
    const element = document.getElementById(id);

    if (element)
    {
        element.textContent = message;
    }
}


function clearError(id)
{
    const element = document.getElementById(id);

    if (element)
    {
        element.textContent = "";
    }
}


/* =========================================================
   ADD DRIVE VALIDATION
   ========================================================= */

const addDriveForm =
    document.getElementById("addDriveForm");

if (addDriveForm)
{
    const fields =
        addDriveForm.querySelectorAll(
            "input, textarea"
        );

    fields.forEach(function(field)
    {
        field.addEventListener(
            "input",
            validateAddDrive
        );
    });


    const branchCheckboxes =
        addDriveForm.querySelectorAll(
            'input[name="eligible_branches[]"]'
        );

    branchCheckboxes.forEach(function(box)
    {
        box.addEventListener(
            "change",
            validateAddDrive
        );
    });


    validateAddDrive();
}


function validateAddDrive()
{
    let valid = true;


    const company =
        document.getElementById("company_name").value.trim();

    if (company === "")
    {
        setError(
            "company_name_error",
            "Company name cannot be empty."
        );

        valid = false;
    }
    else
    {
        clearError("company_name_error");
    }


    const role =
        document.getElementById("job_role").value.trim();

    if (role === "")
    {
        setError(
            "job_role_error",
            "Job role cannot be empty."
        );

        valid = false;
    }
    else
    {
        clearError("job_role_error");
    }


    const packageValue =
        parseFloat(
            document.getElementById("package").value
        );

    if (
        isNaN(packageValue) ||
        packageValue <= 0
    )
    {
        setError(
            "package_error",
            "Package must be a positive number."
        );

        valid = false;
    }
    else
    {
        clearError("package_error");
    }


    const cgpa =
        parseFloat(
            document.getElementById("min_cgpa").value
        );

    if (
        isNaN(cgpa) ||
        cgpa < 0 ||
        cgpa > 10
    )
    {
        setError(
            "min_cgpa_error",
            "CGPA must be between 0 and 10."
        );

        valid = false;
    }
    else
    {
        clearError("min_cgpa_error");
    }


    const backlogs =
        parseInt(
            document.getElementById("max_blacklogs").value
        );

    if (
        isNaN(backlogs) ||
        backlogs < 0
    )
    {
        setError(
            "max_blacklogs_error",
            "Backlogs must be 0 or a positive number."
        );

        valid = false;
    }
    else
    {
        clearError("max_blacklogs_error");
    }


    const date =
        document.getElementById("drive_date").value;

    if (date === "")
    {
        setError(
            "drive_date_error",
            "Drive date is required."
        );

        valid = false;
    }
    else
    {
        clearError("drive_date_error");
    }


    const venue =
        document.getElementById("venue").value.trim();

    if (venue === "")
    {
        setError(
            "venue_error",
            "Venue cannot be empty."
        );

        valid = false;
    }
    else
    {
        clearError("venue_error");
    }


    const branches =
        addDriveForm.querySelectorAll(
            'input[name="eligible_branches[]"]:checked'
        );

    if (branches.length === 0)
    {
        setError(
            "eligible_branches_error",
            "Select at least one branch."
        );

        valid = false;
    }
    else
    {
        clearError("eligible_branches_error");
    }


    const description =
        document.getElementById("description").value.trim();

    if (description === "")
    {
        setError(
            "description_error",
            "Description cannot be empty."
        );

        valid = false;
    }
    else
    {
        clearError("description_error");
    }


    const remark =
        document.getElementById("remark").value.trim();

    if (remark === "")
    {
        setError(
            "remark_error",
            "Remark cannot be empty."
        );

        valid = false;
    }
    else
    {
        clearError("remark_error");
    }


    document.getElementById(
        "addDriveButton"
    ).disabled = !valid;

    return valid;
}


/* =========================================================
   EDIT POPUP
   ========================================================= */

function openEditPopup(drive)
{
    document.getElementById(
        "edit_id"
    ).value = drive.id;

    document.getElementById(
        "edit_company_name"
    ).value = drive.company_name;

    document.getElementById(
        "edit_job_role"
    ).value = drive.job_role;

    document.getElementById(
        "edit_package"
    ).value = drive.package;

    document.getElementById(
        "edit_min_cgpa"
    ).value = drive.min_cgpa;

    document.getElementById(
        "edit_max_blacklogs"
    ).value = drive.max_blacklogs;

    document.getElementById(
        "edit_drive_date"
    ).value = drive.drive_date;

    document.getElementById(
        "edit_venue"
    ).value = drive.venue;

    document.getElementById(
        "edit_description"
    ).value = drive.description;

    document.getElementById(
        "edit_remark"
    ).value = drive.remark;


    const branches =
        drive.eligible_branches
        .split(",")
        .map(function(branch)
        {
            return branch.trim();
        });


    const branchBoxes =
        document.querySelectorAll(
            ".edit-branch"
        );


    branchBoxes.forEach(function(box)
    {
        box.checked =
            branches.includes(box.value);
    });


    document.getElementById(
        "editPopup"
    ).style.display = "flex";
}


function closeEditPopup()
{
    document.getElementById(
        "editPopup"
    ).style.display = "none";
}


/* =========================================================
   DELETE POPUP
   ========================================================= */

function openDeletePopup(id, company)
{
    document.getElementById(
        "delete_id"
    ).value = id;

    document.getElementById(
        "deleteDriveName"
    ).textContent =
        company + " placement drive";


    document.getElementById(
        "deletePopup"
    ).style.display = "flex";
}


function closeDeletePopup()
{
    document.getElementById(
        "deletePopup"
    ).style.display = "none";
}


/* =========================================================
   CLOSE POPUPS WHEN CLICKING OUTSIDE
   ========================================================= */

window.addEventListener(
    "click",
    function(event)
    {
        const editPopup =
            document.getElementById("editPopup");

        const deletePopup =
            document.getElementById("deletePopup");


        if (event.target === editPopup)
        {
            closeEditPopup();
        }


        if (event.target === deletePopup)
        {
            closeDeletePopup();
        }
    }
);


/* =========================================================
   AUTO SHORTLIST
   ========================================================= */

function highlightStrongApplicants()
{
    const rows =
        document.querySelectorAll(
            "#applicants tbody tr.strong-applicant"
        );


    rows.forEach(function(row)
    {
        row.classList.add(
            "auto-highlight"
        );
    });


    if (rows.length === 0)
    {
        return;
    }
}


/* =========================================================
   STUDENT APPLICATION VALIDATION
   ========================================================= */

const applyForm =
    document.getElementById("applyForm");

if (applyForm)
{
    const fields =
        applyForm.querySelectorAll(
            "input, select"
        );


    fields.forEach(function(field)
    {
        field.addEventListener(
            "input",
            validateApplication
        );

        field.addEventListener(
            "change",
            validateApplication
        );
    });


    validateApplication();
}


function validateApplication()
{
    let valid = true;

    let messages = [];


    /* Drive */

    const driveSelect =
        document.getElementById(
            "apply_drive_id"
        );

    if (driveSelect.value === "")
    {
        setError(
            "apply_drive_id_error",
            "Please select a drive."
        );

        valid = false;
    }
    else
    {
        clearError(
            "apply_drive_id_error"
        );
    }


    /* Student name */

    const name =
        document.getElementById(
            "student_name"
        ).value.trim();

    if (name === "")
    {
        setError(
            "student_name_error",
            "Student name cannot be empty."
        );

        valid = false;
    }
    else
    {
        clearError(
            "student_name_error"
        );
    }


    /* Roll number */

    const roll =
        document.getElementById(
            "roll_no"
        ).value.trim();

    if (roll === "")
    {
        setError(
            "roll_no_error",
            "Roll number cannot be empty."
        );

        valid = false;
    }
    else
    {
        clearError(
            "roll_no_error"
        );
    }


    /* Branch */

    const branch =
        document.getElementById(
            "student_branch"
        ).value;

    if (branch === "")
    {
        setError(
            "student_branch_error",
            "Please select your branch."
        );

        valid = false;
    }
    else
    {
        clearError(
            "student_branch_error"
        );
    }


    /* CGPA */

    const cgpa =
        parseFloat(
            document.getElementById(
                "student_cgpa"
            ).value
        );


    if (
        isNaN(cgpa) ||
        cgpa < 0 ||
        cgpa > 10
    )
    {
        setError(
            "student_cgpa_error",
            "CGPA must be between 0 and 10."
        );

        valid = false;
    }
    else
    {
        clearError(
            "student_cgpa_error"
        );
    }


    /* Backlogs */

    const backlogs =
        parseInt(
            document.getElementById(
                "student_backlogs"
            ).value
        );


    if (
        isNaN(backlogs) ||
        backlogs < 0
    )
    {
        setError(
            "student_backlogs_error",
            "Backlogs must be 0 or a positive number."
        );

        valid = false;
    }
    else
    {
        clearError(
            "student_backlogs_error"
        );
    }


    /* Resume */

    const resume =
        document.getElementById(
            "resume"
        );


    if (resume.files.length === 0)
    {
        setError(
            "resume_error",
            "Please upload your resume."
        );

        valid = false;
    }
    else
    {
        const file =
            resume.files[0];

        const fileName =
            file.name.toLowerCase();

        const isPdf =
            file.type === "application/pdf" ||
            fileName.endsWith(".pdf");


        if (!isPdf)
        {
            setError(
                "resume_error",
                "Resume must be a PDF file."
            );

            valid = false;
        }
        else
        {
            clearError("resume_error");
        }
    }


    /* =====================================================
       ELIGIBILITY CHECK
       ===================================================== */

    const selectedOption =
        driveSelect.options[
            driveSelect.selectedIndex
        ];


    const eligibilityMessage =
        document.getElementById(
            "eligibilityMessage"
        );


    const finalMessage =
        document.getElementById(
            "applyValidationMessage"
        );


    eligibilityMessage.textContent = "";

    finalMessage.textContent = "";


    if (driveSelect.value !== "")
    {
        const minCgpa =
            parseFloat(
                selectedOption.dataset.minCgpa
            );


        const maxBacklogs =
            parseInt(
                selectedOption.dataset.maxBacklogs
            );


        const eligibleBranches =
            selectedOption.dataset.branches
                .split(",")
                .map(function(item)
                {
                    return item.trim();
                });


        if (!isNaN(cgpa))
        {
            if (cgpa < minCgpa)
            {
                messages.push(
                    "Your CGPA is below the required minimum of "
                    + minCgpa
                    + "."
                );

                valid = false;
            }
        }


        if (!isNaN(backlogs))
        {
            if (backlogs > maxBacklogs)
            {
                messages.push(
                    "Maximum allowed backlogs are "
                    + maxBacklogs
                    + "."
                );

                valid = false;
            }
        }


        if (
            branch !== "" &&
            !eligibleBranches.includes(branch)
        )
        {
            messages.push(
                "Your branch is not eligible for this drive."
            );

            valid = false;
        }


        if (messages.length > 0)
        {
            eligibilityMessage.className =
                "eligibility-message error";

            eligibilityMessage.innerHTML =
                "<strong>You cannot apply because:</strong><br>"
                + messages.join("<br>");
        }
        else if (
            name !== "" &&
            roll !== "" &&
            branch !== "" &&
            !isNaN(cgpa) &&
            !isNaN(backlogs)
        )
        {
            eligibilityMessage.className =
                "eligibility-message success";

            eligibilityMessage.textContent =
                "You meet the eligibility criteria for this drive.";
        }
    }


    /* Final button */

    document.getElementById(
        "applyButton"
    ).disabled = !valid;


    return valid;
}


/* =========================================================
   PREVENT FRONTEND SUBMISSION IF INVALID
   ========================================================= */

if (addDriveForm)
{
    addDriveForm.addEventListener(
        "submit",
        function(event)
        {
            if (!validateAddDrive())
            {
                event.preventDefault();
            }
        }
    );
}


if (applyForm)
{
    applyForm.addEventListener(
        "submit",
        function(event)
        {
            if (!validateApplication())
            {
                event.preventDefault();
            }
        }
    );
}