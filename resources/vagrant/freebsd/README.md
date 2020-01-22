# Run tests on FreeBSD

``` bash
cd /path/to/uuid/resources/vagrant/freebsd
vagrant up
vagrant ssh
```

Once inside the VM:

``` bash
cd uuid/
composer install
composer run-script --timeout=0 test
```
