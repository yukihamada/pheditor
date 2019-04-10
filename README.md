Pheditor
=======

Pheditor is a single-file editor and file manager written in PHP.



### Features
1. Editor with syntax highlighting
2. File Manager (create, rename and delete files and directories)
3. Password protected area
4. Keeping the history of edited files and changes

### Install & Usage

Install using composer:
`composer require hamidsamak/pheditor dev-master`

or just upload `pheditor.php` to your web host (and/or rename it as you wish).

**NOTES**:
1. The default password is `admin`. Please change the password after install or first login.
2. As the script gives permission to edit files, it is recommended to keep the address secret or protected depending on the web-server you are using.

**Optional settings:**

The settings would be editable in the main PHP file (pheditor.php by default).
The settings are as below:
1. Editable formats (list of allowed file extensions to edit)
2. Log file path
3. Show/Hide hidden files
4. Limit access to the page only for an IP address (empty means access for all)
5. Show/Hide main pheditor file (pheditor.php) in files list to edit 
6. History files path
7. Word wrap
