## Git

1. [git introduce](./1.introduce.md)
2. [git install](./2.install.md)
3. [git command](./3.command.md)
4. [git rebase](./4.rebase.md)
5. [git submodule](./5.submodule.md)

---

## tricks

1. [push code to gitee and github[origin]](https://www.jianshu.com/p/747e2bb71775)

   ```shell
    # config
    git remote add gitee  GITEE_REPO_URL

    # should push separately
    # push to github
    git push origin BRANCH_NAME

    # push to gitee
    git push gitee BRANCH_NAME
   ```

2. worktree: 切分支工作

   ```shell
   # 比如需要进入 branch_a 工作
   git worktree add DIR_NAME branch_a
   cd DIR_NAME
   # 此时这个工作空间是 branch_a 全新的且没有任何改动的

   # 在 branch_a 的工作结束后回到原分支
   cd REPO_NAME

   # 删除 worktree
   git worktree remove DIR_NAME
   ```

## reference

1. [offical website](https://git-scm.com/docs)
1. [practice website](https://learngitbranching.js.org/)
1. [bili](BV1TA411q75f)
