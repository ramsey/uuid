# Run tests on Linux

``` bash
cd /path/to/uuid/resources/vagrant/linux
vagrant up
vagrant ssh
```

Once inside the VM:

``` bash
cd uuid/
composer install
composer run-script --timeout=0 test
```
