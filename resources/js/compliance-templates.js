document.addEventListener("DOMContentLoaded", function () {
    const addButton = document.getElementById("add-field");
    const container = document.getElementById("fields-container");
    let fieldIndex = document.querySelectorAll(".field-row").length;

    if (addButton) {
        addButton.addEventListener("click", function () {
            fetch(`/compliance-templates/field-row?index=${fieldIndex}`)
                .then((response) => {
                    if (!response.ok)
                        throw new Error("Failed to fetch field row");
                    return response.text();
                })
                .then((html) => {
                    container.insertAdjacentHTML("beforeend", html);
                    initField(container.lastElementChild);
                    fieldIndex++;
                })
                .catch((error) => {
                    console.error("Error adding field:", error);
                });
        });
    }

    // Initialize any existing field rows
    document.querySelectorAll(".field-row").forEach(initField);
});

function initField(fieldElement) {
    if (!fieldElement) return;

    const typeSelect = fieldElement.querySelector(".field-type-select");
    const removeBtn = fieldElement.querySelector(".remove-field");

    if (typeSelect) {
        typeSelect.addEventListener("change", handleFieldTypeChange);
        handleFieldTypeChange({ target: typeSelect }); // Trigger once
    }

    if (removeBtn) {
        removeBtn.addEventListener("click", function (e) {
            e.preventDefault();
            fieldElement.remove();
        });
    }
}

function handleFieldTypeChange(event) {
    const select = event.target;
    const row = select.closest(".field-row");

    const optionsContainer = row.querySelector(".field-options-container");
    const validationContainer = row.querySelector(
        ".field-validation-container"
    );

    if (optionsContainer) {
        optionsContainer.style.display =
            select.value === "select" ? "block" : "none";
    }

    if (validationContainer) {
        validationContainer.style.display =
            select.value === "number" || select.value === "date"
                ? "block"
                : "none";
    }
}
