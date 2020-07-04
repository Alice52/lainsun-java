## link: this is for run latest code in docker

- from: `/home/t4856/workspace/portal/modules/nikedream`: commit code in this folder
- to: `/home/t4856/workspace/portal/backend/src/backend/modules/nikedream`
- how:

## add new module

1. enable module in monogo

   ```sql
   db.getCollection('account').find({})
   ```

2. run code in docker

   ```shell
   cd ~/workspace/portal
   # enter docker
   ./build.sh ssh -p 8081:8081

   # add menus and mods in admin UI
   cd omnisocials-backend/src/
   ./yii management/accounts/add-menus-and-mods

   # build front code
   cd ../../omnisocials-frontend/src/
   grunt
   ```

3. route guidline

   - `/home/t4856/workspace/portal/backend/src/backend/config/main.php`

## init jingmai

1. portal repository checkout to `add-jingmai-module`
2. `./build.sh init`
3. `./build.sh up`
4. enable module in monogo
