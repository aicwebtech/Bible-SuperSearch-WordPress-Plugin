# Tests for `com_test/php`

PHPUnit coverage for the platform-agnostic PHP library shared by the Bible
SuperSearch clients (`OptionsAbstract`, `QueryStringParser`). Nothing here loads
WordPress, and nothing here touches the network or a database.

## Running

```sh
cd com_test/php
./run-tests.sh                          # whole suite
./run-tests.sh --filter ParseDomain     # arguments are passed through to phpunit
./run-tests.sh --testdox                # readable list of what is covered
./run-tests.sh --display-skipped        # why tests were skipped
PHP_BIN=php7.3 ./run-tests.sh           # run against another PHP build
./run-tests-all.sh                      # every supported PHP that is installed
```

The project has no Composer, so PHPUnit is used as a standalone PHAR. The first
run downloads it into `tests/bin/` (git-ignored); after that the suite runs
offline.

### PHP and PHPUnit versions

The suite runs on every PHP the plugin supports, **7.3 – 8.5**. No single
PHPUnit covers that range, so `run-tests.sh` picks one from the PHP it runs:

| PHP | PHPUnit | Config |
| --- | --- | --- |
| 8.2 – 8.5 | 11.5 | `phpunit.xml` |
| 7.3 – 8.1 | 9.6 | `phpunit-9.xml` (pre-10 schema) |

Keep the two configs in step. `run-tests-all.sh` runs the suite on each of
`php7.3` … `php8.5` that is installed, prints a summary, and fails if any
installed version fails.

Every test has to run under both PHPUnit versions. In practice that means
declaring data providers twice — PHPUnit 11 reads the attribute and ignores the
doc-comment, PHPUnit 9 reads the doc-comment (an attribute is just a comment on
PHP 7):

```php
/**
 * @dataProvider hostProvider
 */
#[DataProvider('hostProvider')]
public function testParseDomain($host, $expected)
```

and sticking to PHP 7.3 syntax and assertions that exist in PHPUnit 9.6.

`PHPUNIT_VERSION` overrides the PHPUnit release (a version before 10 selects
`phpunit-9.xml`), and each version is cached under its own name in `tests/bin/`.

If the machine has no outbound network, download the PHAR from
<https://phar.phpunit.de/> by hand and save it as
`tests/bin/phpunit-<version>.phar`.

## Layout

| Path | Contents |
| --- | --- |
| `phpunit.xml` (in `com_test/php`) | Suite configuration |
| `tests/bootstrap.php` | Loads `init.php` plus a PSR-4 autoloader for the test namespace |
| `tests/Support/TestOptions.php` | In-memory concrete `OptionsAbstract` |
| `tests/Support/Fixtures.php` | Canned API statics and landing pages |
| `tests/Unit/` | The tests |

## Writing a test

`OptionsAbstract` is abstract and platform-bound, so tests drive it through
`TestOptions`, which stores options in an array and returns an injected statics
fixture instead of calling the API:

```php
$options = new TestOptions([
    'options'       => ['enableAllBibles' => false, 'enabledBibles' => ['kjv']],
    'statics'       => Fixtures::statics(),
    'landing_pages' => Fixtures::landingPages(),
]);

$this->assertSame(['kjv'], array_keys($options->getEnabledBibles()));
```

Add new test classes under `tests/Unit` with the namespace
`BibleSuperSearch\Common\Tests\Unit` and a `Test.php` suffix; the autoloader and
the suite pick them up with no further registration.

The suite is strict: stray output, PHP notices and PHP warnings all fail the
run, so a test that provokes one has to assert on it deliberately.

Keep the suite offline.

**A test for broken code should fail, not skip.** If the code fatals or returns
the wrong answer, write the test that asserts the right answer and leave it red —
that is the signal. `markTestSkipped()` is for tests that cannot run here at all
(a missing extension, a platform-specific path), never for a known bug: a green
suite over a broken route hides exactly what the tests exist to catch.

Skip reasons are not printed by default; run `./run-tests.sh --display-skipped`
to see them.
