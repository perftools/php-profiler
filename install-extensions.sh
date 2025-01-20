#!/usr/bin/env bash

set -xeu

: "${TIDEWAYS_VERSION:=4.1.4}"
: "${TIDEWAYS_XHPROF_VERSION:=5.0.4}"
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
    local ext="xhprof" version="${1:-stable}"

    has_extension "$ext" && return 0
    pecl install "$ext-$version"
}

install_mongo() {
    local ext="mongo" version="${1:-stable}"

    has_extension "$ext" && return 0
    echo no | pecl install "$ext-$version"
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
	local workdir="vendor/tideways_xhprof"
	local library
	local config
	local zts

	zts=$(php --version | grep -q ZTS && echo -zts || :)
	library="$PWD/$workdir/tideways_xhprof-$version/tideways_xhprof-$PHP_VERSION$zts.so"

	if [ ! -f "$library" ]; then
		curl -fL -o "$tar" "$url"
		mkdir -p "$workdir"
		tar -xvf "$tar" -C "$workdir"
	fi

	test -f "$library" || die "Extension not available: $library"
	config="/etc/php/$PHP_VERSION/cli/conf.d/10-tideways_xhprof.ini"
	echo "extension=$library" | sudo tee "$config"
	has_extension "$extension"
}

pecl version
php -m

case "$(uname -s):$PHP_VERSION" in
*:5.*)
	install_xhprof 0.9.4
	install_mongo
	;;
Linux:7.*|Linux:8.*)
	install_xhprof
	install_mongodb
	install_tideways_xhprof
	;;
*:7.*|*:8.*)
	install_xhprof
	install_mongodb
	;;
esac
