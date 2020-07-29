#!/bin/sh

set -xeu

: ${TRAVIS_PHP_VERSION=7.4}
: ${TIDEWAYS_VERSION=4.1.4}
: ${TIDEWAYS_XHPROF_VERSION=5.0.2}
: ${PHP_VERSION=${TRAVIS_PHP_VERSION}}

die() {
	echo >&2 "ERROR: $*"
	exit 1
}

install_xhprof() {
    local version="${1:-stable}"

    pecl install xhprof-$version
}

install_mongo() {
    echo no | pecl install mongo
}

install_mongodb() {
    php -m | grep -q mongodb || pecl install mongodb
    composer require --dev alcaeus/mongo-php-adapter
}

install_tideways_xhprof() {
	local version=$TIDEWAYS_XHPROF_VERSION
	local arch=$(uname -m)
	local url="https://github.com/tideways/php-xhprof-extension/releases/download/v$version/tideways-xhprof-$version-$arch.tar.gz"
	local extension="tideways_xhprof"
	local tar="$extension.tgz"
	local config library
	local zts

	curl -fL -o "$tar" "$url"
	tar -xvf "$tar"

	zts=$(php --version | grep -q ZTS && echo -zts || :)
	library="$PWD/tideways_xhprof-$version/tideways_xhprof-$PHP_VERSION$zts.so"
	config="$HOME/.phpenv/versions/$PHP_VERSION/etc/conf.d/tideways_xhprof.ini"
	test -f "$library" || die "Extension not available: $library"
	echo "extension=$library" > "$config"
	php -m | grep -F "$extension"
}

case "$(uname -s):$PHP_VERSION" in
*:5.*)
	install_xhprof 0.9.4
	install_mongo
	;;
Linux:7.*)
	install_xhprof
	install_mongodb
	install_tideways_xhprof
	;;
*:7.*)
	install_xhprof
	install_mongodb
	;;
esac
