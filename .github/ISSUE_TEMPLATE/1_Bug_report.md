---
name: "Bug report"
description: "Create a report to help us improve"
body:
-
    type: "input"
    attributes:
        label: "Root version"
        placeholder: "#.#.#"
    validations:
        required: true
-
    type: "input"
    attributes:
        label: "Laravel version"
        placeholder: "#.#.#"
-
    type: "input"
    attributes:
        label: "PHP version"
        placeholder: "#.#.#"
-
    type: "input"
    attributes:
        label: "Database engine & version"
        placeholder: "#.#"
-
    type: "markdown"
    attributes:
        label: "Description"
    validations:
        required: true
-
    type: "markdown"
    attributes:
        label: "Steps To Reproduce"
    validations:
        required: true
