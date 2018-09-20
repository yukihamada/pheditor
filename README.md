Pheditor
=======

Pheditor is a single-file editor for PHP with syntax highlighting feature.



### Install & Usage

Install using composer:
`composer require hamidsamak/pheditor dev-master`

or just upload `pheditor.php` to your web host (and/or rename it as you wish).

**NOTES**:
1. The default password is `admin`. Please change the password after login.
2. As the script gives permission to edit files, it is recommended to keep the address private or protect that with `.htpasswd` in Apache web-server.

**Optional settings:**

The settings would be editable in the main PHP file (pheditor.php by default) and the settings are as below:
1. Editable formats (list of allowed files extensions to edit)
2. Log file path
3. Show/Hide hidden files
4. Limit access to the page only for an IP address (empty means access for all)
5. Show/Hide main pheditor file (pheditor.php) in files list to edit 
