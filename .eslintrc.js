module.exports = {
    plugins: [
        "vue"
    ],
    extends: [
        "plugin:vue/base",
        "plugin:vue/essential",
        "plugin:vue/strongly-recommended",
        "plugin:vue/recommended"
    ],
    "rules": {
        "vue/html-indent": ["error", 4, {
            "attribute": 1,
            "baseIndent": 1,
            "closeBracket": 0,
            "alignAttributesVertically": true,
            "ignores": []
        }],
        "vue/html-self-closing": ["error", {
            "html": {
                "void": "any",
                "normal": "any",
                "component": "any"
            },
            "svg": "any",
            "math": "any"
        }],
        "vue/max-attributes-per-line": ["error", {
            "singleline": {
                "max": 5
            },
            "multiline": {
                "max": 5
            }
        }]
    }
}
