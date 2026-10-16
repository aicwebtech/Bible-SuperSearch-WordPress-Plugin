#!/usr/bin/env bash
#
# Runs the PHPUnit suite against every PHP version the plugin supports, using
# whichever of php7.3 ... php8.5 are installed. See run-tests.sh for details.
#
# Usage:
#   ./run-tests-all.sh                      # whole suite on every version
#   ./run-tests-all.sh --filter ApiProxy    # arguments are passed through to phpunit
#
# Exits non-zero if the suite fails on any installed version.  Versions that are
# not installed are reported, but do not fail the run.
#

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PHP_VERSIONS="7.3 7.4 8.0 8.1 8.2 8.3 8.4 8.5"

passed=()
failed=()
missing=()

for v in $PHP_VERSIONS; do
    if ! command -v "php$v" > /dev/null 2>&1; then
        missing+=("$v")
        continue
    fi

    echo "=== PHP $v ==="

    if PHP_BIN="php$v" "$DIR/run-tests.sh" "$@"; then
        passed+=("$v")
    else
        failed+=("$v")
    fi

    echo
done

echo "=== Summary ==="
echo "Passed:        ${passed[*]:-(none)}"
echo "Failed:        ${failed[*]:-(none)}"
echo "Not installed: ${missing[*]:-(none)}"

[ ${#failed[@]} -eq 0 ] && [ ${#passed[@]} -gt 0 ]
