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
#   PHP_BIN=php7.3 ./run-tests.sh           # run against another PHP build
#   ./run-tests-all.sh                      # every supported PHP version installed
#
# Versions: the suite runs on every PHP the plugin supports (7.3 - 8.5).
# PHPUnit 11.5 needs PHP 8.2+, so older PHP gets PHPUnit 9.6 (the last series
# running on PHP 7.3) and its pre-10 config, phpunit-9.xml. The version is picked
# from the PHP being run; PHPUNIT_VERSION overrides it. Each PHPUnit version is
# cached under its own name in tests/bin/.
#
# Tests must therefore run on both: declare data providers with both the
# #[DataProvider] attribute (PHPUnit 11) and a @dataProvider doc-comment (PHPUnit 9).
#
set -e

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PHP_BIN="${PHP_BIN:-php}"
PHP_MINIMUM="70300"

if ! command -v "$PHP_BIN" > /dev/null 2>&1; then
    echo "PHP binary '$PHP_BIN' not found. Set PHP_BIN to a PHP 7.3+ executable." >&2
    exit 1
fi

PHP_VERSION_ID="$("$PHP_BIN" -r 'echo PHP_VERSION_ID;')"

if [ "$PHP_VERSION_ID" -lt "$PHP_MINIMUM" ]; then
    echo "The suite needs PHP 7.3 or newer; '$PHP_BIN' is $("$PHP_BIN" -r 'echo PHP_VERSION;')." >&2
    exit 1
fi

if [ -z "$PHPUNIT_VERSION" ]; then
    if [ "$PHP_VERSION_ID" -lt 80200 ]; then
        PHPUNIT_VERSION="9.6.36"
    else
        PHPUNIT_VERSION="11.5.56"
    fi
fi

# PHPUnit before 10 needs the old config schema
if [ "${PHPUNIT_VERSION%%.*}" -lt 10 ]; then
    PHPUNIT_CONFIG="$DIR/phpunit-9.xml"
else
    PHPUNIT_CONFIG="$DIR/phpunit.xml"
fi

PHPUNIT_PHAR="$DIR/tests/bin/phpunit-${PHPUNIT_VERSION}.phar"
PHPUNIT_URL="https://phar.phpunit.de/phpunit-${PHPUNIT_VERSION}.phar"

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

exec "$PHP_BIN" "$PHPUNIT_PHAR" -c "$PHPUNIT_CONFIG" "$@"
