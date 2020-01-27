PHP Group Chat App - Members Only - made with <3 by Sabrina Markon :3 Have fun!
------------------------------------------------------------------------------------

Stories:

- Members must register to the website.

- Members must verify their email address on the website.

- As members of the website, users can login and chat with one another either privately or in the group channel.

- The chat uses websockets.

- The websocket can be set up as a persistent service using /etc/init and chatSocket.conf

- Cron to check if service is down and start it if it is (I've seen so many issues on Stack, GitHub, etc. saying that the service dies unpredictably with few solutions.)

- The admin may login to an admin area.

- The admin can change the basic website settings from the admin area (site name, admin user/pass, domain url, etc.)

- The admin can email all members.

- The admin area allow create/edit/delete of members.

- The admin area allows the admin to edit the basic HTML content on the website pages (between the header and footer).

- The site scales well for mobile/desktop.

- The site is extremely user friendly and intuitive for a mainly non-technical community.

- The site looks and IS fun!

Example: http://collectorscave.phpsitescripts.com

------------------------------------------------------------------------------------

Installation:

1) Create a new database in your cPanel.

2) Create a new database user and assign it to your new database. WRITE DOWN your database name, user, and pass for step 8!

3) Add privileges to the new database user.

4) Go to phpMyAdmin (or however you prefer to access the database command line), and import the /DB/DB.php file.

5) Click on your new database, and then click on the table "adminsettings".

6) You can edit each of the options in there, but they can also be edited in the admin area afterwards. Change the domain field
at least to be your URL starting with an https:// and no slash at the end. You could use http:// but it is not recommended.

Fields in the "adminsettings" table: adminuser, adminpass, adminname, adminemail, domainemail, sitename, domain)

Again, you can also edit these within the admin area of the site once you are done installing it.

*** IMPORTANT: the "domainemail" field should be an email address from @yourowndomain.com, so it sends from the server properly. If you use
something else, like gmail etc., you would need to open your server to relaying, which makes it vulnerable to spam attacks! For your "adminemail", 
you can use whatever you like and that is where members will contact you or the account they will reply to if you message them from your site. ***

7) Upload all the files into your public_html (or www) directory in File Manager or with FTP.

8) Go into /config/Database.php and replace the below top lines in that file with your own information:

	private static $dbhost = "localhost";
	private static $dbname = "YOUR_DATABASE_NAME";
	private static $dbuser = "YOUR_DATABASE_USER";
	private static $dbpass = "YOUR_DATABASE_PASS";

Save the Database.php file.

9) Delete the DB directory - you already used that to create your database, which is its only purpose.

10) Set up a scheduled CRON job on your server (in cPanel, Plesk, etc.) to run SendEmails.php. This file sends to all your members at once,
so a cronjob allows that to happen in the background so you don't have to wait after submitting your message in the admin area.

11) Set up another scheduled CRON job for CheckWebSocketsService.php that will make sure your chat server is running (just in case the service stalls). 
If it is not, it will restart it.

12) Visit your website!

13) You can login to the admin area at yourdomain.com/admin with username adminuser and password adminpass (or if you changed these values in step 6,
use the ones you chose instead).

14) You can edit the CONTENT that appears on pages, or create new pages in the "PAGES" admin area (go figure).

15) In your File Manager or with FTP, you can upload images to the /images directory, or edit the following files to change the layout of your site:

/header.php
/footer.php
/admin/header.php
/admin/footer.php
/css/custom.css
/images

Back them up before editing to be on the safe side, so if things go south fast, you can simply re-upload the backups!

------------------------------------------------------------------------------------

How to set up a WebSockets daemon on the server (need root access to your VPS or server):

1) Move /cron/chatSocket.conf to your /etc/init directory.

2) SSH to your server and in the command line, run: service chatSocket start.

3) If you want the service to run automatically when your server starts, ......

NEED TO EDIT MORE IN THIS FILE.