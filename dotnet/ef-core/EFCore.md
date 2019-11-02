## EFCore

### Database Migration

- model => databse

  ```shell
  ## notice
  ## 1. set parent project as startup
  ## 2. open package manage console
  ## 3. choose the project contains DbContext as Default Project
  ## 4. do migration and update database

  # get-help entityframeworkcore
  # 1. requirement: Microsoft.EntityFrameworkCore.Tools, Microsoft.EntityFrameworkCore.Design
  dotnet restore # after add to .csproj
  # 2. DbContext register
  # 3. Add-Migration MIGRATIONNAME[UNIQUE]
  # 4. Update-Database -Verbose
  ```

---

## Tools

1. package manage ef-core

|        VSCode Comand         |     VS Comand      |                      Description                       |
| :--------------------------: | :----------------: | :----------------------------------------------------: |
|                              |   Add-Migration    |                 Adds a new migration.                  |
|                              |   Drop-Database    |                  Drops the database.                   |
|                              |   Get-DbContext    |        Gets information about a DbContext type.        |
|                              |  Remove-Migration  |              Removes the last migration.               |
| dotnet ef dbcontext scaffold | Scaffold-DbContext | Scaffolds a DbContext and entity types for a database. |
|                              |  Script-Migration  |        Generates a SQL script from migrations.         |
|                              |  Update-Database   |     Updates the database to a specified migration.     |
