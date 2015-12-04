EXA2fa
======================================
EXA2fa handles different authentification plugins with A2FA and LDAP.

A2FA is multi-factor authentication plugin that uses time-based tokens generated every 60 seconds in Google Authenticator app.
A2FA Stands for Another Two-Factor Authentication

EXA2fa gives user decission if he wants to use A2FA with LDAP, with email or with manuell authentification. Of if he wants authentification completely without A2FA.


##Installation:

* install this plugin as a moodle block

move the directories "exa2fa/auth/a2fa_*" to "yourmoodle/auth/a2fa_*"

Now go to ***Site Administration > Plugins > Authentication > Manage authentication*** and enable the a2fa plugins


##How to login: alternateloginurl

you have to change the Alternate login URL in the moodle settings (***Site Administration > Plugins > Authentication > Manage authentication***) to
yourmoodlesite.com/blocks/exa2fa/login/
else the a2fa users can't login.


TODO: add copyright and sources