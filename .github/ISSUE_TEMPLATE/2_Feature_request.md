---
name: "Feature request"
description: "Suggest an idea for root"
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
        label: "Is your feature request related to a problem?"
        description: "Please describe"
    validations:
        required: true
-
    type: "markdown"
    attributes:
        label: "Describe the solution you'd like"
    validations:
        required: true
-
    type: "markdown"
    attributes:
        label: "Why do you think this feature is something we should consider for Root?"
    validations:
        required: true
-
    type: "markdown"
    attributes:
        label: "Additional context"
