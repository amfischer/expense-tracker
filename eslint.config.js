import globals from 'globals';
import js from '@eslint/js';
import vue from 'eslint-plugin-vue';
import prettier from 'eslint-config-prettier/flat';

import { defineConfig } from 'eslint/config';

export default defineConfig([
    js.configs.recommended,
    vue.configs['flat/essential'],
    {
        files: ['resources/js/**/*.{js,vue}'],
    },
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
    {
        languageOptions: {
            globals: {
                ...globals.browser,
                ...globals.node,
                route: 'readonly',
            },
        },

        rules: {
            'vue/require-default-prop': 'off',
            'vue/multi-word-component-names': 'off',
            'vue/attributes-order': 'off',
            'vue/max-attributes-per-line': 'off',
            'vue/html-indent': ['error', 4],
            'vue/singleline-html-element-content-newline': ['off'],
            'vue/multiline-html-element-content-newline': [
                'error',
                {
                    allowEmptyLines: true,
                },
            ],
        },
    },
    prettier,
]);
