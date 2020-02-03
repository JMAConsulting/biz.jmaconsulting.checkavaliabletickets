# biz.jmaconsulting.checkavailabletickets

This extension adds in the ability to manage a calculation of number of tickets sold and in the process of being sold in an Event. This is useful as you may have a number of concurrent event registrations and you need to ensure that the ability to register for the event is still valid. It adds checks before loading the registration page, after submitting the registration page before loading the Confirm page and then after submitting the confirm step but before actually processing the confirm page. It checks the total number of tickets sold as returned by the sum of eventFull and the number of tickets being sold (held) that is stored as a counter in a database table.

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.0+
* CiviCRM 5.19

## Installation (Web UI)

This extension has not yet been published for installation via the web UI.

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl biz.jmaconsulting.checkavaliabletickets@https://github.com/JMAConsulting/biz.jmaconsulting.checkavaliabletickets/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/FIXME/biz.jmaconsulting.checkavaliabletickets.git
cv en checkavaliabletickets
```

## Usage

To use this extension enable the extension and then set a maximum number of participants for a specific event.


## Known Issues

The counter of number of held tickets may not be accurate if people's sessions time out and the counter is not updated.
