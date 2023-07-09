module.exports = {
    extends: [
        'eslint:recommended',
        'plugin:vue/base',
        'plugin:vue/vue3-essential',
        'plugin:vue/vue3-strongly-recommended',
        'plugin:vue/vue3-recommended',
    ],
    rules: {
        'vue/html-indent': ['error', 4, {
            'attribute': 1,
            'baseIndent': 1,
            'closeBracket': 0,
            'alignAttributesVertically': true,
            'ignores': [],
        }],
        'vue/html-closing-bracket-spacing': ['error', {
            selfClosingTag: 'never'
        }],
    },
    'overrides': [
        {
            'files': ['*.vue'],
            'rules': {
                'vue/multi-word-component-names': 'off',
            },
        },
    ],
    'globals': {
        'route': 'readonly',
        'Ziggy': 'readonly',
    },
}
