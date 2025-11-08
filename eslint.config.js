import js from '@eslint/js';
import pluginVue from 'eslint-plugin-vue';
import stylistic from '@stylistic/eslint-plugin';
import prettierConfig from 'eslint-config-prettier';
import globals from 'globals';

export default [
    // Apply recommended rules
    js.configs.recommended,

    // Vue 3 recommended rules
    ...pluginVue.configs['flat/recommended'],

    // Prettier config to disable conflicting rules
    prettierConfig,

    // Global configuration
    {
        plugins: {
            '@stylistic': stylistic,
        },

        languageOptions: {
            globals: {
                ...globals.browser,
                ...globals.node,
                route: 'readonly',
            },
        },

        rules: {
            // Vue rules
            'vue/require-default-prop': 'off',
            'vue/multi-word-component-names': 'off',
            'vue/attributes-order': 'off',
            'vue/max-attributes-per-line': 'off',
            'vue/html-indent': ['error', 4],
            'vue/singleline-html-element-content-newline': ['off'],
            'vue/multiline-html-element-content-newline': ['error', {
                allowEmptyLines: true,
            }],

            // Stylistic rules
            '@stylistic/no-multiple-empty-lines': ['error', {
                max: 1,
            }],
        },
    },

    // Files to lint
    {
        files: ['resources/js/**/*.{js,vue}'],
    },

    // Ignore patterns (from .gitignore and build artifacts)
    {
        ignores: [
            'node_modules/',
            'vendor/',
            'public/build/',
            'public/hot/',
            'public/storage/',
            'storage/',
            '.phpunit.cache/',
            '*.log',
        ],
    },
];
