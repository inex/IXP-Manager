# Notes

* Need to use a secure connection or restore the default mysql password plugin:
```
mysql --protocol=TCP --port 33060 -u root
ALTER USER 'ixpmanager' IDENTIFIED WITH mysql_native_password BY 'ixpmanager';
```