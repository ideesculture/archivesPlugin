# archivesPlugin
Plugin for CollectiveAccess providence allowing tree view both in ajax &amp; separated screen



## Installing

### Pre-requisites

You need a valid phantomjs on the server. To install it under Debian use :

```bash
apt install phantomjs pdftk
```

If you don't have the directory `/usr/lib/x86_64-linux-gnu/fonts` on your server, create a symlink to it :
```bash
ln -s /usr/share/fonts /usr/lib/x86_64-linux-gnu/fonts
```

Remember to allow the apache sys user to be able to write inside archives/temp :

```bash
chown www-data:www-data archives/temp
```

