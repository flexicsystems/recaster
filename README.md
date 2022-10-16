📖 Recaster
----------------

This package allows to recast php objects to object of other class.

----
### Installation

Run

```bash
composer require flexic/recaster
```

to install `flexic/recaster`.

----
### Cast to object of other class

```php
$recaster = new Flexic\Recaster\Recaster($inputObject);
$object = $recaster->toClass(TargetClass::class);
```

### Cast to array

```php
$recaster = new Flexic\Recaster\Recaster($inputObject);
$arrayOfObject = $recaster->toArray();
```

----
### License
This package is licensed using the GNU License.

Please have a look at [LICENSE.md](LICENSE.md).

---

[![Donate](https://img.shields.io/badge/Donate-PayPal-blue.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=Q98R2QXXMTUF6&source=url)