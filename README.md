DB Connection Watcher ![Release](https://img.shields.io/badge/release-v0.1--rc2-blue.svg)
=====================

![Build status](https://img.shields.io/jenkins/s/http/julenpardo.com/jenkins/DB-Connection-Watcher.svg)
![Tests](https://img.shields.io/jenkins/t/http/julenpardo.com/jenkins/DB-Connection-Watcher.svg)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/julenpardo/DB-Connection-Watcher/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/julenpardo/DB-Connection-Watcher/?branch=master)
![License](https://img.shields.io/badge/license-Apache-blue.svg)

This tool will watch the connection number of a database, generating alerts when the defined threshold is exceeded.
This tool will watch the connection number of a database, generating alerts when the defined threshold is exceeded.

## Prerequisites
A SMTP client installed and configured is required in the host where this tool is going to be used.

## Compatibility

Tested for:

 - PostgreSQL (9.5).

## Installation
 - Download the last release: [v0.1-rc2](https://github.com/julenpardo/DB-Connection-Watcher/releases/tag/v0.1-rc2).
 - Unzip.
 - Give execution permissions to `install.sh`.
 - Execute it as superuser: `sudo ./install.sh`.
 - Make sure that the `/var/dbconnectionwatcher/` directory has write permissions for the user that is going to execute this tool.
 
## Usage
 - Configure `/etc/dbconnectionwatcher/dbconnectionwatcher.ini` to watch the databases you want (an example is shown below). Remember that the executing user has to have write permissions in `/var/dbconnectionwatcher/`. 
 - Configure a cron task with the frequency you want, to execute `/usr/local/bin/dbcw`.
 
## Configuration
Let's suppose that we want to watch the following databases:

 - `database1` and `database2`.
 - With `username1` and `username2` as users, respectively.
 - With `password1` and `password2` as passwords, respectively.
 - Both databases are running in `localhost` and listening to port `5432`.
 - The notifications for the `database1` will be sent to `admin1@example.com`; and for `database2`, `admin2@example.com`.
 - The connection threshold that the databases have to exceed to send the notifications is `5` for both.
 - Both databases run in PostgreSQL DBMS.
 
The `/etc/dbconnectionwatcher/dbconnectionwatcher.ini` would be:

```
[Database 1]

database             = database1
username             = username1
password             = password1
host                 = localhost
port                 = 5432
email                = admin1@example.com
connection_threshold = 5
dbms                 = postgresql

[Database 2]

database             = database2
username             = username2
password             = password2
host                 = localhos2
port                 = 5432
email                = admin2@example.com
connection_threshold = 5
dbms                 = postgresql
```

**Considerations**

 - Each section name (the name between square brackets `[ ]`) **must be unique to each database configuration**, but it doesn't have to follow any specific format.
 - **Don't indent the file**. PHP .ini file parser doesn't like indentations.

## Unexpected behaviors

When an exception occurs, the tools catches it and **writes the error message in the PHP error log**. So, if the tool doesn't behave as expected, take a look to that log.
