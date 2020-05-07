# PHP and Frontend Develop Environment Setup

In this training project, we need use Ubuntu 16.04 as our operate system. As we use Win 10 on our local host machine, we need vagrant to provide us a Ubuntu environment on virtual machine. The basic softwares used in this project training are:

- Nginx 1.x
- MySQL 5.7
- PHP 7.1
- Redis 3
- Node.js

### Knowledge preparation

- **Vagrant** is a tool for building and managing virtual machine environments in a single workflow. More detail information, please refer [Vagrant - Getting Started](https://www.vagrantup.com/intro/getting-started/index.html).
- **Nginx** [engine x] is an HTTP and reverse proxy server. More detail information, please refer [beginner's guide](http://nginx.org/en/docs/beginners_guide.html)
- **Redis** is an open source (BSD licensed), in-memory data structure store, used as a database, cache and message broker. More detail information please refer [Introduction to Redis](https://redis.io/topics/introduction)
- **Node.js** is a JavaScript runtime built on Chrome's V8 JavaScript engine. Node.js uses an event-driven, non-blocking I/O model that makes it lightweight and efficient.
- Basic linux command
    - sudo
    - ls
    - cd
    - mkdir
    - cp
    - rm
    - apt-get
    - [vi](https://www.cs.colostate.edu/helpdocs/vi.html)

### Installation & Setup


#### Step 0. Installing The Ubuntu 16.04 Vagrant Box

Get vagrant's Ubuntu 16.04 box to your local machine. And then add it.
```
vagrant box add "file:////path/to/ubuntu1604.box" --name "playground"
```


#### Step 1. Set Up Virtual Machine

**Initialize the vagrant based on added box:**
```
vagrant init playground
```

**Configuring ```Vagrantfile```:**
```
# network configuration
config.vm.network "private_network", ip: "192.168.33.10"
config.vm.network "forwarded_port", guest: 80, host: 8080
config.vm.network "forwarded_port", guest: 3306, host: 33060

# sync folder
config.vm.synced_folder "c:\\", "/home/vagrant/code"

# customize the amount of memory on the VM:
config.vm.provider "virtualbox" do |vb|
  vb.memory = "1024"
end
```

**Launching the virtual machine**
Once you edit the `Vagrantfile` to your like, run the `vagrant up` command from your vagrant directory. Vagrant will boot the virtual machine and automatically configure your shared folders. After virtual machine is started, run `vagrant ssh` to access it.

To stop the machine, you may use the `vagrant halt` command. And if your virtual machine has any broken issue, please feel free to destroy it by using `vagrant destroy`. And start from `Step 0` again.

#### Step 2. Set Up Develop Environment

**Installing Nginx**
```
sudo apt-get install nginx
```
After installing, please access `http://192.168.10.10` in your chrome

**Installing MySQL**
```
sudo apt-get install mysql-server-5.7
```
After installing, you can access MySQL through `mysql -uroot -ppassword`

**Installing PHP**
```
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
sudo apt-get install php7.1 php7.1-fpm
```
After installing, you can check it by `php -v`

**Installing Redis**
```
sudo apt-get install redis-server
```
After installing, you can access redis through its client `redis-cli`

**Installing Node.js**
```
curl -sL https://deb.nodesource.com/setup_8.x | sudo -E bash -
sudo apt-get install -y nodejs
```
After installing, you can check it by `node -v` and `npm -v`

#### Step 3. Configuring Develop Environment

**Let Nginx Serve PHP**
```
sudo vi /etc/nginx/sites-available/default
```

change the root folder and enable php part and save the file, then reload nginx

```
sudo service nginx reload
```

create `index.php` under your root folder with following code:
```
<?php

phpinfo();
```
check the result in the browser again.

**Let PHP Access DB through MySQL Module**
```
ll /etc/php/7.1/fpm/conf.d/
sudo apt-get install php7.1-mysql
```
after installing, check the result in the browser again.

**Access DB in VM from Host Machine**

If you want to access the database on your VM using MySQL Workbench or other MySQL client, you need to configure MySQL on VM:
```
sudo vi /etc/mysql/mysql.conf.d/mysqld.cnf
```
change the settings like following:
```
bind-address = 0.0.0.0
```
then save the file and restart the service with `sudo service mysql restart`. After unlimiting the bind address, we need to grant the user `root` the permission that accessing database from any host.
```
mysql -uroot -ppassword

use mysql;
select Host, User from user;
update user set Host = '%' where User = 'root';
flush privileges;
```

#### Here we are, you can start to play in your `playground` :)