## setting 配置

```json
{
  "files.associations": {
    "*.cjson": "jsonc",
    "*.wxss": "css",
    "*.wxs": "javascript",
    "*.vue": "vue"
  },
  "emmet.includeLanguages": {
    "wxml": "html",
    "vue-html": "html",
    "vue": "html"
  },
  "minapp-vscode.disableAutoConfig": true,
  "editor.fontSize": 18,
  "editor.renderControlCharacters": true,
  "editor.renderWhitespace": "all",
  // 启用后，保存文件时在文件末尾插入一个最终新行
  "files.insertFinalNewline": true,
  // 启用后，将在保存文件时剪裁尾随空格
  "files.trimTrailingWhitespace": true,
  // 加载和侧边栏显示时,忽略的文件/文件夹
  "files.exclude": {
    "**/.svn": true,
    "**/.hg": true,
    "**/.DS_Store": true,
    // "**/_posts":true,
    "**/.sass-cache": true,
    "**/.vscode": true,
    "**/node_modules": true,
    "**/.idea": true
  },
  // 改变 powershell 为 git 相关
  "terminal.integrated.shell.windows": "E:\\Git\\bin\\bash.exe",
  // 启用后，将使用的参数和方法名称的类型进行提示。
  "docthis.inferTypesFromNames": true,

  "eslint.validate": [
    "javascript",
    "javascriptreact",
    "html",
    {
      "language": "vue",
      "autoFix": true
    }
  ],
  "eslint.options": {
    "plugins": ["html"]
  },
  // 控制编辑器是否应在键入后自动设置行的格式
  "editor.formatOnType": true,
  "editor.formatOnSave": true,
  "window.zoomLevel": 0,
  "breadcrumbs.enabled": true,

  // #让prettier使用eslint的代码格式进行校验
  "prettier.eslintIntegration": true,
  //  #使用带引号替代双引号
  "prettier.singleQuote": true,
  //  #让函数(名)和后面的括号之间加个空格
  "javascript.format.insertSpaceBeforeFunctionParenthesis": true
}
```

## keyboard shutcuts

```json
// Place your key bindings in this file to override the defaults
[
  {
    "key": "ctrl+u",
    "command": "editor.action.transformToUppercase",
    "when": "editorTextFocus"
  },
  {
    "key": "ctrl+l",
    "command": "editor.action.transformToLowercase",
    "when": "editorTextFocus"
  },
  {
    "key": "alt+q",
    "command": "workbench.action.editor.changeLanguageMode"
  },
  {
    "key": "ctrl+k m",
    "command": "-workbench.action.editor.changeLanguageMode"
  },
  {
    "key": "alt+e alt+d",
    "command": "workbench.action.editor.changeEncoding"
  }
]
```

## plugins

|       plugin-name       | function |
| :---------------------: | :------: |
|    Path Intellisence    |   PATH   |
|         GitLens         |   GIT    |
|     TODO Highlight      |   TOOL   |
|     open in browser     |   TOOL   |
|   VSCode Great Icons    |   TOOL   |
|      vscode-faker       |   TOOL   |
|    Markdown Preview     |    MD    |
|        CSS Peek         |   CSS    |
|      Document This      |    --    |
|         ESLint          |    JS    |
|    HTML Boilerpalte     |    H5    |
|         MDTools         |    MD    |
| Prettier-Code formatter |  FORMAT  |
|         TSLint          |  FORMAT  |
|     Vue 2 Snippets      |   VUE    |
|          Vetur          |   VUE    |
|         minapp          |  WECHAT  |
|     TODO Highlight      |  WECHAT  |
