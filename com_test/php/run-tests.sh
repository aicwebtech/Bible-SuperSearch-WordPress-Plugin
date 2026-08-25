#!/usr/bin/env bash
#
# Runs the PHPUnit suite for the platform-agnostic Bible SuperSearch PHP library.
#
# There is no Composer in this project, so PHPUnit is used as a standalone PHAR.
# It is downloaded on first run into tests/bin/ (which is git-ignored).
#
# Usage:
#   ./run-tests.sh                          # whole suite
#   ./run-tests.sh --filter ParseDomain     # any phpunit argument is passed through
#   ./run-tests.sh --testdox                # readable list of what is covered
#   ./run-tests.sh --display-skipped        # why tests were skipped
#
#   PHP_BIN=php8.4 ./run-tests.sh           # run against another PHP build
#   PHPUNIT_VERSION=9.6.36 ./run-tests.sh   # older PHPUnit (see NOTE)
#
# NOTE on versions: the test tooling targets PHP 8.2+, which is not the same as
# the PHP the *plugin* supports (7.3+, per readme.txt). Tests only ever run on a
# developer's machine, so they track a current PHPUnit rather than the oldest
# runtime. To check the library still parses on the plugin's minimum, lint it:
#
#   php7.3 -l src/OptionsAbstract.php
#
# PHPUnit 9.6 (the last series running on PHP 7.3) needs the pre-10 config
# schema, so PHPUNIT_VERSION=9.6.36 also requires -c pointing at a 9.x
# phpunit.xml. Each version is cached under its own name in tests/bin/.
#
set -e

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PHPUNIT_VERSION="${PHPUNIT_VERSION:-11.5.56}"
PHPUNIT_PHAR="$DIR/tests/bin/phpunit-${PHPUNIT_VERSION}.phar"
PHPUNIT_URL="https://phar.phpunit.de/phpunit-${PHPUNIT_VERSION}.phar"
PHP_BIN="${PHP_BIN:-php}"
PHP_MINIMUM="80200"

if ! command -v "$PHP_BIN" > /dev/null 2>&1; then
    echo "PHP binary '$PHP_BIN' not found. Set PHP_BIN to a PHP 8.2+ executable." >&2
    exit 1
fi

PHP_VERSION_ID="$("$PHP_BIN" -r 'echo PHP_VERSION_ID;')"

if [ "$PHP_VERSION_ID" -lt "$PHP_MINIMUM" ]; then
    echo "PHPUnit ${PHPUNIT_VERSION} needs PHP 8.2 or newer; '$PHP_BIN' is $("$PHP_BIN" -r 'echo PHP_VERSION;')." >&2
    echo "Set PHP_BIN to a newer PHP, e.g. PHP_BIN=php8.2 $0" >&2
    exit 1
fi

if [ ! -f "$PHPUNIT_PHAR" ]; then
    echo "Downloading PHPUnit ${PHPUNIT_VERSION}..."
    mkdir -p "$DIR/tests/bin"

    if command -v curl > /dev/null 2>&1; then
        curl -sSLf -o "$PHPUNIT_PHAR" "$PHPUNIT_URL"
    elif command -v wget > /dev/null 2>&1; then
        wget -q -O "$PHPUNIT_PHAR" "$PHPUNIT_URL"
    else
        echo "Need curl or wget to download PHPUnit. Alternatively, download" >&2
        echo "$PHPUNIT_URL manually and save it as $PHPUNIT_PHAR" >&2
        exit 1
    fi

    chmod +x "$PHPUNIT_PHAR"
fi

exec "$PHP_BIN" "$PHPUNIT_PHAR" -c "$DIR/phpunit.xml" "$@"
