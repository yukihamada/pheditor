Pheditor
=======

Pheditor is a single-file editor and file manager written in PHP.

![Pheditor - PHP file Editor](https://hamidsamak.github.io/pheditor/assets/image/screenrecord-desktop.gif "Pheditor PHP file editor")

### Features
1. Editor with syntax highlighting
2. File Manager (create, rename and delete files and directories)
3. Password protected area
4. Keeping the history of edited files and changes
5. Keyboard shortcuts
6. Access levels for reading and writing and other permissions

### Install & Usage

Install using composer:
`composer create-project hamidsamak/pheditor`

or just upload `pheditor.php` to your web host (and/or rename it as you wish).

---

**NOTES**:
1. The default password is `admin`. Please change the password after install or first login.
2. As the script gives permission to edit files, it is recommended to keep the address secret or protected depending on the web-server you are using.

---

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
8. Changing main directory (`MAIN_DIR`)

---

**Hotkeys:**

1. New File `Ctrl (CMD) + Shift + N`
2. Save File `Ctrl (CMD) + Shift + S`
3. Switch between file manager and editor `Esc`
4. Double click on file name to view in browser window/tab.

---

**Using without password:**

You can empty the `PASSWORD` constant in the source code to access the script without the password. But it is highly recommended to use it and change the default password after installation.

---

**Access Levels and Permissions:**

There are eight permissions for users that is defined in `PERMISSIONS` constant. You can remove any of them as you need.

Default value: `newfile,newdir,editfile,deletefile,deletedir,renamefile,renamedir,changepassword`
