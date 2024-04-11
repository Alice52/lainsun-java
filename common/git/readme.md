## line

1. [git introduce](./1.introduce.md)
2. [git install](./2.install.md)
3. [git command](./3.command.md)
4. [git rebase](./4.rebase.md)
5. [git submodule](./5.submodule.md)
6. [git theory](./6.theroy.md)
7. [github](./7.github.md)

---

## [commit msg](https://github.com/conventional-changelog/commitlint?tab=readme-ov-file#what-is-commitlint)

1. format: `type(scope?): subject`
2. type: angular style

   - **perf**: 改进性能
   - **modify**: 修改功能
   - **delete**: 删除代码
   - build: 构建流水线
   - refactor
   - style: 不影响代码含义的修改(如 formart)
   - revert
   -
   - test
   - docs
   - fix
   - chore
   - feat: 心功能
   -
   - ci: 自动化流程

3. release with github action

   ```yaml
   name: Release

   permissions:
     contents: write

   on:
     push:
       tags:
         - 'v*'

   jobs:
     release:
       runs-on: ubuntu-latest
       steps:
         - uses: actions/checkout@v4
           with:
             fetch-depth: 0
         - name: Set node
           uses: actions/setup-node@v4
           with:
             node-version: 20
         - name: Release
           run: npx changelogithub
           env:
             GITHUB_TOKEN: ${{secrets.GITHUB_TOKEN}}
   ```

---

## reference

1. [offical website](https://git-scm.com/docs)
1. [practice website](https://learngitbranching.js.org/)
1. [bili](BV1TA411q75f)
