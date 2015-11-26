A2FA or Etouffee as I like to call it!
======================================
A2FA is multi-factor authentication plugin that uses time-based tokens generated every 60 seconds in Google Authenticator app.
A2FA Stands for Another Two-Factor Authentication

The field is to add a QR code for the user to be able to sync Google Authenticator with the a2fa system.

##Installation:

move the directories
exa2fa -> /blocks/exafa
a2fa_* -> /auth/a2fa_*

Now go to ***Site Administration > Plugins > Authentication > Manage authentication*** and enable ***A2FA***

##How to login:
you have to change the altternative login url in the moodle settings to
yourmoodlesite.com/blocks/exa2fa/login/
else the a2fa users can't login.



TODO: add copyright and sources