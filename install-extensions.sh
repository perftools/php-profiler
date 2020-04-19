#!/bin/sh

set -xeu

: ${TRAVIS_PHP_VERSION=7.4}
: ${TIDEWAYS_VERSION=4.1.4}
: ${TIDEWAYS_XHPROF_VERSION=5.0.2}
: ${PHP_VERSION=${TRAVIS_PHP_VERSION}}

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
	test -f "$library"
	echo "extension=$library" > "$config"
	php -m | grep -F "$extension"
}

case "$(uname -s):$PHP_VERSION" in
Linux:7.*)
	install_tideways_xhprof
	;;
esac
