#!/bin/sh

set -xeu

: "${TIDEWAYS_VERSION:=4.1.4}"
: "${TIDEWAYS_XHPROF_VERSION:=5.0.2}"
: "${PHP_VERSION:=7.4}"

die() {
	echo >&2 "ERROR: $*"
	exit 1
}

has_extension() {
    local extension="$1"
    php -m | awk -vrc=1 -vextension="$extension" '$1 == extension { rc=0 } END { exit rc }'
}

install_xhprof() {
    local version="${1:-stable}"

    has_extension "xhprof" && return 0
    pecl install xhprof-$version
}

install_mongo() {
    echo no | pecl install mongo
}

install_mongodb() {
    has_extension "mongodb" || pecl install -f mongodb
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
	tar -xvf "$tar" -C vendor/tideways_xhprof

	zts=$(php --version | grep -q ZTS && echo -zts || :)
	library="$PWD/vendor/tideways_xhprof/tideways_xhprof-$version/tideways_xhprof-$PHP_VERSION$zts.so"
	config="/etc/php/$PHP_VERSION/cli/conf.d/10-tideways_xhprof.ini"
	test -f "$library" || die "Extension not available: $library"
	echo "extension=$library" > "$config"
	has_extension "$extension"
}

# Show php config paths
php --ini

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
